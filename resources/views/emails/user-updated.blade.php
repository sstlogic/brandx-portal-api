@component('mail::message')
# Your Profile Has Been Updated

This is a courtesy email to let you know that your profile has been updated. If you did not update your profile, please contact us at bookings@brandx.org.au.

@component('mail::button', ['url' => $url, 'color' => 'black'])
Login
@endcomponent

Thanks,<br>
The Brand X Team
@endcomponent
