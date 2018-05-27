@component('vendor.mail.markdown.message')

@lang('emails.activation.greeting', ['name' => $user->name])

@lang('emails.activation.text')

@component('mail::button', ['url' =>  config('services.frontend.url')."/activate_set/".$token])
    @lang('emails.activation.button')
@endcomponent

@endcomponent
