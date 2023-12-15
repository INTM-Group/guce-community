@component('mail::message')

Bonjour,

Vous avez été mentionné dans le ticket suivant par {{ $creator->first_name }} {{ $creator->last_name }}, le {{ $activity->created_at }}.

<details>
<summary style="cursor: pointer; bold">Détails du ticket</summary>

<strong>Titre:</strong> {{ $ticket->title }}

<strong>Description</strong>
<blockquote style="border: solid thin lightgrey; padding: 1em;">
{!! $ticket->description !!}
</blockquote>


<strong>Type:</strong> {{
Illuminate\Support\Facades\Lang::get('ticket.priority._' . $ticket->priority, [], 'fr')
}}

@if($ticket->priority != 130)
@component('mail::table')
| Criticité | Logiciel |
|:---------:|---------:|
| {{
Illuminate\Support\Facades\Lang::get('ticket.criticality._' . floor($ticket->criticality/120), [], 'fr')
}} | {{ $ticket->tag_principal }} |
@endcomponent
@endif

---

## Message

<blockquote style="border: solid thin lightgrey; padding: 1em;">
{!! $activity->message !!}
</blockquote>


</details><br>


Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent
