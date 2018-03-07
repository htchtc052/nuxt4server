@component('vendor.mail.markdown.message')

@lang('emails.change_password.greeting', ['name' => $user->name])

@lang('emails.change_password.text')

@component('mail::button', ['url' => \Config::get('services.frontend.url')."/password_set/".$token."?email=".$user->email])
    @lang('emails.change_password.button')
@endcomponent

@endcomponent