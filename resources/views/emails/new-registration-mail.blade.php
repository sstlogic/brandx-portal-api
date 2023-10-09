@component('mail::message')
    # Brand X New Registration

    Hi {{ $user->first_name }},

    Thank you for registering for an account with Brand X.

    Keep this email as a reminder that your username is: {{ $user->email }}

    Your account gives you access to 33 spaces across performing arts, music, visual art spaces in Sydney.

    To book a space at Brand X visit www.brandx.org.au.

    Look forward to seeing you in our spaces.

    Thanks,<br>
    The Brand X Team
@endcomponent
