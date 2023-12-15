@component('mail::message')

Bonjour,

{{ $activity->user->type & App\Models\User::TYPE_CLIENT ? 'Un client':'Un agent'}} a
@if ($activity->type & App\Models\Activity::TYPE_STATUS)
changé le statut du ticket à "{{
    Illuminate\Support\Facades\Lang::get('ticket.status._' . $ticket->status, [], 'fr')
}}" en laissant
@else
laissé
@endif
un message, le {{ $ticket->updated_at }}.

Veuillez cliquer sur le bouton ci-dessous pour voir le ticket.

## Message

<blockquote style="border: solid thin lightgrey; padding: 1em;">
{!! $activity->message !!}
</blockquote>

@component('mail::button', ['url' => (config('app.url') ?: "https://client.intm.tools").'/ticket/'.$ticket->id])
Voir Ticket
@endcomponent

Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent
