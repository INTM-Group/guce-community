<?php

namespace App\Models;

use App\Contracts\RelationalTrait;
use Alograg\StrTools;
use App\Messages\TicketActivity;
use App\Messages\TicketCreationMail;
use App\Tools;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use SoftDeletes, RelationalTrait;

    const STATUS_DISABLED = 0; // same
    const STATUS_VALID = 1; // same
    const STATUS_OPEN = 2; // same
    const STATUS_COURS_CT = 4; // en cours CT
    const STATUS_ATTENTE_CT = 8; // en attente CT
    const STATUS_COURS_CR = 16; // en cours Cr
    const STATUS_ATTENTE_CR = 32; // en attente Cr
    const STATUS_RESOLVED = 64; // same (resolu)
    const STATUS_CLOSED = 128; // clos

    /** @var bool $timestamps */
    public $timestamps = true;

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'participants' => '[]',
        'stats' => '{"take_it":0,"in_hours":false,"crono":{"client":0,"supplier":0}}',
        'tags' => '[]',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'participants' => 'collection',
        'stats' => 'json',
        'tags' => 'collection',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'status',
        'priority',
        'criticality',
        'service_id',
        'creator_id',
        'title',
        'description',
        'participants',
        'take_by',
        'stats',
        'tag_principal',
        'tags'
    ];
    public $appends = [
        'satisfaction'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Validation rules.
     *
     * @var array<string>
     */
    public static $rules = [
        'priority' => 'required',
        //'criticality' => 'required',
        'title' => 'required',
        'description' => 'required',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        if (php_sapi_name() !== "cli") {
            static::creating(function ($ticket) {
                $user = Auth::user();
                $ticket->creator_id = $user->id;
                $ticket->participants = [$user->id];
                $ticket->service_id = $user->service_id;
                $ticket->status = $ticket->status ?: static::STATUS_OPEN;
                if (Str::startsWith(request()->header('Content-Type'), 'multipart/form-data')) {
                    $ticket->tags = json_decode(request('tags', '[]'));
                }
            });
            static::created(function ($ticket) {
                try {
                    Mail::to($ticket->creator)->send(new TicketCreationMail('creation-client', $ticket->creator, $ticket, new Activity()));
                    $clients = User::where('type', '&', User::TYPE_MANAGER)->where('type', '&', User::TYPE_CLIENT)->get();
                    Mail::to($clients)->send(new TicketCreationMail('creation-client', $ticket->creator, $ticket, new Activity()));
                    $suppliers = User::where('type', '&', User::TYPE_SUPPLIER)->get();
                    Mail::to($suppliers)->send(new TicketCreationMail('creation-supplier', $ticket->creator, $ticket, new Activity()));
                } catch (\Exception $exception) {
                    Tools::teamsAlert('Server Mail error: ' . $exception->getMessage());
		}
                Tools::teamsAlert('Server (SNCF) New ticket: ' . $ticket->id . PHP_EOL .$ticket->title);
                $files = request()->file('uploadFiles');
                $destination = storage_path('private/guce/');
                $destination .= Str::lower("ticket") . DIRECTORY_SEPARATOR;
                $destination .= $ticket->id . DIRECTORY_SEPARATOR;
                if (!file_exists($destination)) {
                    mkdir($destination, 0754, true);
                }
                if ($files) {
                    foreach ($files as $file) {
                        $fileName = Str::snake($file->getClientOriginalName());
                        if (file_exists($destination . $fileName)) {
                            $fileName = date('Ymd-his-') . $fileName;
                        }
                        $file->move($destination, $fileName);
                        $data['files'][] = $fileName;
                    }
                    $activity = new Activity([
                        'type' => Activity::TYPE_MESSAGE,
                        'message' => 'Files : ' . implode(', ', $data['files']),
                        'data' => $data
                    ]);
                    $ticket->activities()->save($activity);
                }
            });
            static::saved(function ($ticket) {
                if (request("ccTo")) {
                    $ccTo = (array) request('ccTo', []);
                    if (count($ccTo)) {
                        $ccToMails = array_reduce($ccTo, function ($carry, $item) {
                            $email = StrTools::extractEmails($item);
                            if ($email) $carry[] = ['email' => $email[0]];
                            return $carry;
                        }, []);
                        if (count($ccToMails)) {
                            $activity = new Activity([
                                'type' => Activity::TYPE_MESSAGE,
                                'message' => 'Notification : ' . implode(', ', $ccTo),
                                'data' => [
                                    'ccTo' => $ccTo
                                ]
                            ]);
                            $ticket->activities()->save($activity);
                            Mail::to($ccToMails)
                                ->send(new TicketCreationMail('creation-tier', $ticket->creator, $ticket, $activity));
                        }
                    }
                }
                if ($ticket->isDirty('take_by')) {
                    $suppliers = User::find($ticket->take_by);
                    if ($suppliers)
                        try {
                            Mail::to($suppliers)->send(new TicketActivity('assignment', $ticket->creator, $ticket, new Activity()));
                        } catch (\Exception $exception) {
                            Tools::teamsAlert('Server Mail error: ' . $exception->getMessage());
                        }
                }
            });
        }
    }

    public function getSatisfactionAttribute()
    {
        return $this->activities()
            ->whereRaw("JSON_TYPE(JSON_VALUE(data , '$.satisfaction')) = 'INTEGER' ")
            ->select(\DB::raw("CAST(JSON_VALUE(data , '$.satisfaction') as int) as satisfaction"))
            ->get()->pluck("satisfaction");
    }
}
