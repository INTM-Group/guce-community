@component('mail::message')

Bonjour {{ $user->first_name }} {{ $user->last_name }},

Pour activer votre compte, veuillez cliquer sur le bouton ci-dessous pour une première connexion :

@component('mail::button', ['url' => (config('app.url') ?: "https://client.intm.tools").'/activate/'.$user->remember_token])
Activer votre compte
@endcomponent

Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent
