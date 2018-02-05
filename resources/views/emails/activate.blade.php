@component('mail::message')
# Introduction

Hi {{ $user -> name }},
<br>
Thank you for creating an account with us. Don't forget to complete your registration!
<br>
Please click on the link below or copy it into the address bar of your browser to confirm your email address:
<br>

@component('mail::button', ['url' => \Config::get('services.frontend.url')."/activate_set/".$token])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
