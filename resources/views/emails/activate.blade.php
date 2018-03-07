@component('vendor.mail.markdown.message')

@lang('emails.activation.greeting', ['name' => $user->name])

@lang('emails.activation.text')

@component('mail::button', ['url' => route('activate_set', $token)])
    @lang('emails.activation.button')
@endcomponent

@endcomponent
