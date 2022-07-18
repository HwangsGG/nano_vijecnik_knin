@component('mail::message')
  # Your new password: {{ $details['password'] }}

  Change password for your account.

  @component('mail::button', ['url' => $details['url']])
    Change password
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
