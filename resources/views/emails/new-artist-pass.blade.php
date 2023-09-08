@component('mail::message')
# Brand X Artist Pass Confirmation

Hi {{$user->first_name}},

Thank you for your ARTIST PASS subscription. A receipt will be emailed to you shortly.

An Artist Pass entitles subscribers to discounts on rates of hire for our studios and tickets to our Flying Nun and Artist-2-Artist programs.

To make a booking for a studio space, login and click BOOK A SPACE (in the top right corner), to view the calendar and select times to book

To book tickets to a show in The Flying Nun season or our Artist-2-Artist program, copy this code and paste into the "promo code" section when purchasing tickets to claim your concession: **artistpass-4567382**

@component('mail::button', ['url' => $login, 'color' => 'black'])
Login Now
@endcomponent

Thanks,<br>
The Brand X Team
@endcomponent
