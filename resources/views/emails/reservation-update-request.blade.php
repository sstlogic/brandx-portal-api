@component('mail::message')
# Reservation Modification Request

A new reservation modification request has been submitted.

# Form Details
**Booking Reference:** {{ $submitted_ref }}\
**Name:** {{ $submitted_name }}\
**Email:** {{ $submitted_email }}\
**Message:** {{ $submitted_message }}


# Details Retrieved From Booked
**Start Time:** {{ $start_date }} \
**End Time:** {{ $end_date }}

@if ($booking_exists)
@component('mail::button', ['url' => $url, 'color' => 'black'])
View In Booked
@endcomponent
@endif

---

# Authorised User
The authorised user that submitted this request is;\
**Name:** {{ $user->first_name }} {{ $user->last_name }}\
**Email:** {{ $user->email }}\
**Booked User ID:** {{ $user->external_id }}\
**Phone:** {{ $user->phone }}

@if ($booking_exists)
@component('mail::button', ['url' => $user_url, 'color' => 'black'])
View User In Booked
@endcomponent
@endif

@if ($stripe_url)
___

@component('mail::button', ['url' => $stripe_url, 'color' => 'black'])
View Invoice in Stripe
@endcomponent
@endIf

Thanks,<br>
The Brand X Team
@endcomponent
