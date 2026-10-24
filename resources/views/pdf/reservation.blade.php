<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $reservation->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #212529; font-size: 13px; }
        .header { border-bottom: 3px solid #d95319; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #14213d; margin: 0; font-size: 20px; }
        .code { background: #f6f7f9; border-left: 4px solid #d95319; padding: 10px 14px; font-size: 16px; font-weight: bold; color: #14213d; margin: 14px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        td.label { color: #6c757d; width: 40%; }
        .warning { background: #fff3cd; border: 1px solid #ffe69c; border-radius: 4px; padding: 10px; margin: 14px 0; }
        .footer { color: #6c757d; font-size: 11px; border-top: 1px solid #dee2e6; padding-top: 10px; margin-top: 24px; }
        h2 { color: #14213d; font-size: 15px; margin: 18px 0 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ setting('company_name') }}</h1>
        {{ setting('tagline') }}
    </div>

    <h2>{{ __('site.booking.pdf.title') }}</h2>

    <div class="code">{{ __('site.booking.your_code') }} : {{ $reservation->code }}</div>

    <table>
        <tr><td class="label">{{ __('site.booking.client') }}</td><td>{{ $reservation->clientFullName() }} · {{ $reservation->phone }} · {{ $reservation->email }}</td></tr>
        <tr><td class="label">{{ __('site.booking.type_label') }}</td><td>{{ $reservation->type->label() }}</td></tr>
        @if ($reservation->vehicle)
            <tr><td class="label">{{ __('site.nav.vehicles') }}</td><td>{{ $reservation->vehicle->fullName() }} ({{ $reservation->vehicle->registration_plate }})</td></tr>
        @endif
        <tr><td class="label">{{ __('site.home.search.pickup_date') }}</td><td>{{ $reservation->starts_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td class="label">{{ __('site.home.search.return_date') }}</td><td>{{ $reservation->ends_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td class="label">{{ __('site.booking.summary_days') }}</td><td>{{ $reservation->days_count }}</td></tr>
        @if ($reservation->pickup_location)
            <tr><td class="label">{{ __('site.booking.pickup_location') }}</td><td>{{ $reservation->pickup_location }}</td></tr>
        @endif
        <tr><td class="label">{{ __('site.booking.summary_vehicle_price') }}</td><td>{{ format_price((float) $reservation->vehicle_daily_price) }}{{ __('site.common.per_day') }}</td></tr>
        <tr><td class="label">{{ __('site.booking.summary_driver_price') }}</td><td>{{ format_price((float) $reservation->driver_daily_price) }}{{ __('site.common.per_day') }}</td></tr>
        <tr><td class="label"><strong>{{ __('site.booking.summary_total') }}</strong></td><td><strong>{{ format_price((float) $reservation->total_price) }}</strong> — {{ __('site.vehicles.payment_agency') }}</td></tr>
        <tr><td class="label">{{ __('site.booking.status') }}</td><td>{{ $reservation->status->label() }}</td></tr>
    </table>

    <div class="warning">
        <strong>{{ __('site.booking.license_reminder') }}</strong>
    </div>

    <div class="footer">
        {{ setting('company_name') }} · {{ setting('contact_address') }} · {{ setting('contact_phone') }} · {{ setting('contact_email') }}<br>
        {{ __('site.booking.pdf.generated') }} {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
