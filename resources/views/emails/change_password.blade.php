@component('mail::message')
# Introduction

Hi {{ $user -> name }},

Please click on the link below or copy it into the address bar of your browser to start changing password for your account:
<br>

@component('mail::button', ['url' => \Config::get('services.frontend.url')."/password_set/".$token."?email=".$user->email])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
