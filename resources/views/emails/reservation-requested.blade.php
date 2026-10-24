<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #212529; max-width: 640px; margin: 0 auto;">
    <div style="border-bottom: 3px solid #d95319; padding-bottom: 12px; margin-bottom: 20px;">
        <h1 style="color: #14213d; margin: 0; font-size: 22px;">{{ setting('company_name') }}</h1>
        <p style="color: #6c757d; margin: 4px 0 0;">{{ setting('tagline') }}</p>
    </div>

    <h2 style="color: #14213d; font-size: 18px;">{{ __('site.booking.mail.client_title') }}</h2>

    <p>{{ __('site.booking.mail.client_hello', ['name' => $reservation->clientFullName()]) }},</p>

    <p>{{ __('site.booking.mail.client_intro') }}</p>

    <p style="background: #f6f7f9; border-left: 4px solid #d95319; padding: 12px 16px; font-size: 18px; font-weight: bold; color: #14213d;">
        {{ __('site.booking.your_code') }} : {{ $reservation->code }}
    </p>

    <table cellpadding="6" cellspacing="0" border="0" style="width: 100%;">
        <tr><td style="color: #6c757d; width: 40%;">{{ __('site.booking.type_label') }}</td><td><strong>{{ $reservation->type->label() }}</strong></td></tr>
        @if ($reservation->vehicle)
            <tr><td style="color: #6c757d;">{{ __('site.nav.vehicles') }}</td><td><strong>{{ $reservation->vehicle->fullName() }}</strong></td></tr>
        @endif
        <tr><td style="color: #6c757d;">{{ __('site.home.search.pickup_date') }}</td><td>{{ $reservation->starts_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.home.search.return_date') }}</td><td>{{ $reservation->ends_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.booking.summary_days') }}</td><td>{{ $reservation->days_count }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.booking.summary_total') }}</td><td><strong>{{ format_price((float) $reservation->total_price) }}</strong> ({{ __('site.vehicles.payment_agency') }})</td></tr>
    </table>

    <p style="background: #fff3cd; border: 1px solid #ffe69c; border-radius: 6px; padding: 12px;">
        ⚠️ <strong>{{ __('site.booking.license_reminder') }}</strong>
    </p>

    <p style="color: #6c757d; font-size: 13px;">
        {{ __('site.check_booking.subtitle') }} :
        {{ route('check-booking', 'fr') }}
    </p>

    <p style="color: #6c757d; font-size: 12px; border-top: 1px solid #dee2e6; padding-top: 12px; margin-top: 24px;">
        {{ setting('company_name') }} · {{ setting('contact_phone') }} · {{ setting('contact_email') }}
    </p>
</body>
</html>
