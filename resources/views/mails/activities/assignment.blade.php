@component('mail::message')

Bonjour,

Un nouveau ticket a été attribué à vous. Veuillez cliquer sur le bouton ci-dessous pour voir le ticket.

@component('mail::button', ['url' => (config('app.url') ?: "https://client.intm.tools").'/ticket/'.$ticket->id])
Voir Ticket
@endcomponent

Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent
