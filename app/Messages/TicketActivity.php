<?php

namespace App\Messages;

use App\Models\Activity;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class TicketActivity extends Mailable
{
    use Queueable, SerializesModels;

    public $creator = null;
    public $ticket = null;
    public $activity = null;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct($type, User $creator, Ticket $ticket, Activity $activity)
    {
        $this->creator = $creator;
        $this->ticket = $ticket;
        $this->activity = $activity;
        $this->type = $type;
        $this->subject(Lang::get('activities.subjects.' . $this->type, [
            'app' => env('APP_NAME', 'GUCE'),
            'ticketId' => $ticket->id,
            'ticketTitle' => $ticket->title,
        ], env('APP_LOCAL', 'fr')));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.activities.' . $this->type);
    }
}
