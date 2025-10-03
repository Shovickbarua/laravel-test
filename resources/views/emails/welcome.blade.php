@component('mail::message')
# Welcome, {{ $user->name }} 🎉

We’re excited to have you join us!

@component('mail::button', ['url' => url('/')])
Go to Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
