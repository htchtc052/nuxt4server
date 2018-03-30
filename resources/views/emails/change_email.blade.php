@component('vendor.mail.markdown.message')

@lang('emails.change_email.greeting', ['name' => $user->name])

@lang('emails.change_email.text', ['new_email' => $new_email])

@component('mail::button', ['url' => route('email_set', $token)."?email=".$new_email])
    @lang('emails.change_email.button')
@endcomponent

@endcomponent
