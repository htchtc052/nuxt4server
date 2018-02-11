@component('mail::message')
# Introduction

Hi {{ $user -> name }},

Please click on the link below or copy it into the address bar of your browser to confirm changing email to {{ $email }} for your account:
<br>

@component('mail::button', ['url' => \Config::get('services.frontend.url')."/email_set/".$token])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
