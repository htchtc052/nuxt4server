@component('mail::message')
# Introduction

Hi {{ $user -> name }},


@component('mail::button', ['url' => \Config::get('services.frontend.url')."/home"])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
