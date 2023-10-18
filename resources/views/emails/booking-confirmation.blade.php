@component('mail::message')
# Your Brand X booking is confirmed

Hi {{ $user->first_name }}

Your booking with Brand X is now confirmed.

Here are the details of your booking/s with Brand X;

@foreach ($reservations as $reservation)
**Date:** {{ $reservation->prettyStart()->setTimezone('Australia/Sydney')->format('d/m/Y g:ia') }} -
{{ $reservation->prettyEnd()->setTimezone('Australia/Sydney')->format('g:ia') }}\
**Room:** {{ $reservation->resource_name }}\
**Number of Guests:** {{ $reservation->attendees }}\
**Invoice Number (sent by Stripe):** {{ $reference }}\
**Reference Number:** {{ $reservation->reference_number }}

@if ($reservation->isRepeating())
Your reservation will repeat {{ $reservation->prettyIntervalType() }} every {{ $reservation->prettyInterval() }}
until {{ $reservation->end->setTimezone('Australia/Sydney')->format('d/m/Y') }}
@endif

---
@endforeach


Access:

If this is your first time booking with us, a member of the Brand X team will be in contact prior to your booking’s
commencement date with information on how to access your space.

In the meantime, please familiarise yourself with our Induction Packages:

East Sydney Community and Arts Centre (ESCAC), Darlinghurst: https://www.brandx.org.au/escac-induction-package
City of Sydney Creative Studios (CoSCS), Sydney: https://www.brandx.org.au/coscs-induction-package

Access to your space is granted from the time your booking starts.
Please remember your code (ESCAC) or to bring your FOB with you (CoSCS).
Hirers are required to have packed up and left the space by the time your booking concludes.
If the Hirer occupies the space prior to or after the confirmed booking time then Brand X shall be entitled to charge
additional fees based on the standard rate.

Cancellation Policy:

The Hirer must notify Brand X if it wishes to cancel its booking and if the cancellation is:
• from 48 hours to 24 hours prior to the Hire Date then half the Hire Fee is forfeited to Brand X and the balance ofthe
Hire Fee paid by the Hirer will be refunded to the Hirer; or
• within 24 hours prior to the Hire Date then the entire Hire Fee is forfeited to Brand X.
• for weekly hire (40+ hours) 14 days to 7 days prior to the Hire Date, half the Hire Fee is forfeited to Brand X;
within 7 days prior to the Hire Date then the entire Hire Fee is forfeited to Brand X.

Should you wish to make any changes to your booking contact [bookings@brandx.org.au](mailto:bookings@brandx.org.au) or
if you have any questions please call (02) 8029 9068 during business hours.

We look forward to seeing you soon!

Thanks,<br>
The Brand X team

**Our locations:**\
CoSCS: 119 Bathurst St, Sydney NSW 2000\
ESCAC: 34-40 Burton St, Darlinghust NSW 2010
@endcomponent

You are receiving this email because you completed a booking with Brand X.
Brand X is a non-profit organisation, making space for artists since 2005”
