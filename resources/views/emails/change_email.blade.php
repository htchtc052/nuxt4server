@component('vendor.mail.markdown.message')

@lang('emails.change_email.greeting', ['name' => $user->name])

@lang('emails.change_email.text')

@component('mail::button', ['url' => route('email_set', $token)])
    @lang('emails.change_email.button')
@endcomponent

@endcomponent
