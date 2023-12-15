@component('mail::message')

Bonjour {{ $user->first_name }} {{ $user->last_name }},

Pour réinitializer votre mot de passe, veuillez cliquer sur le bouton ci-dessous:

@component('mail::button', ['url' => (config('app.url') ?: "https://client.intm.tools").'/activate/'.$user->remember_token])
Réinitializer votre mot de passe
@endcomponent

Cordialement,<br>
Équipe {{ strtoupper(config('app.name')) }}

@endcomponent




