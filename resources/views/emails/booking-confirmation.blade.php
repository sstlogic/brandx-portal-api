@component('mail::message')
# Your Brand X booking is confirmed



Hi {{$user->first_name}}

Your booking with Brand X is now confirmed.

Here is your confirmation for the following bookings;


@foreach($reservations as $reservation)

**Date:** {{$reservation->prettyStart()->setTimezone('Australia/Sydney')->format('d/m/Y g:ia')}} - {{$reservation->prettyEnd()->setTimezone('Australia/Sydney')->format('g:ia')}}\
**Room:** {{$reservation->resource_name}}\
**Number of Guests:** {{$reservation->attendees}}\
**Invoice Number (sent by Stripe):** {{$reference}}\
**Reference Number:** {{$reservation->reference_number}}

@if($reservation->isRepeating())
Your reservation will repeat {{$reservation->prettyIntervalType()}} every {{$reservation->prettyInterval()}} until {{$reservation->end->setTimezone('Australia/Sydney')->format('d/m/Y')}}
@endif

---

@endforeach

Should you wish to make any changes to your booking, or if you have any questions please contact [bookings@brandx.org.au](mailto:bookings@brandx.org.au)

We look forward to seeing you soon!

Thanks,<br>
The Brand X team

(You are receiving this email because you completed a booking with Brand X.)

**Our locations:**\
CoSCS: 119 Bathurst St, Sydney NSW 2000\
ESCAC: 34-40 Burton St, Darlinghust NSW 2010
@endcomponent
