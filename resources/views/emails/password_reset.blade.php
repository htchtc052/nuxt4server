@component('vendor.mail.markdown.message')

@lang('emails.password_reset.greeting', ['name' => $user->name])

@lang('emails.password_reset.text')

@component('mail::button', ['url' =>  config('services.frontend.url')."/password_set/".$token."?email=".$user->email])
    @lang('emails.password_reset.button')
@endcomponent

@endcomponent