@component('mail::message')

Bonjour,

Un nouveau ticket a été créé par {{ $creator->first_name }} {{ $creator->last_name }}, le {{ $ticket->created_at }}. Veuillez cliquer sur le bouton ci-dessous pour voir le ticket

@component('mail::subcopy')
<strong>Titre:</strong> {{ $ticket->title }}

<strong>Description</strong>
<blockquote style="border: solid thin lightgrey; padding: 1em;">
{!! $ticket->description !!}
</blockquote>

<strong>Type:</strong> {{
Illuminate\Support\Facades\Lang::get('ticket.priority._' . $ticket->priority, [], 'fr')
}}

@endcomponent

@if($ticket->priority != 130)
@component('mail::table')
| Criticité | Logiciel |
|:---------:|---------:|
| {{
Illuminate\Support\Facades\Lang::get('ticket.criticality._' . floor($ticket->criticality/120), [], 'fr')
}} | {{ $ticket->tag_principal }} |
@endcomponent
@endif

@component('mail::button', ['url' => (config('app.url') ?: "https://client.intm.tools").'/ticket/'.$ticket->id])
Voir Ticket
@endcomponent

Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent
