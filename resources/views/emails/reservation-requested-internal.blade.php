<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #212529; max-width: 640px; margin: 0 auto;">
    <h2 style="color: #14213d;">{{ __('site.booking.mail.internal_title') }}</h2>

    <p><strong>{{ $reservation->code }}</strong> · {{ $reservation->type->label() }}</p>

    <table cellpadding="6" cellspacing="0" border="0" style="width: 100%;">
        <tr><td style="color: #6c757d; width: 40%;">{{ __('site.booking.client') }}</td><td>{{ $reservation->clientFullName() }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.contact.phone') }}</td><td>{{ $reservation->phone }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.contact.email') }}</td><td>{{ $reservation->email }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.nav.vehicles') }}</td><td>{{ $reservation->vehicle?->fullName() ?? '—' }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.booking.summary_dates') }}</td><td>{{ $reservation->starts_at->format('d/m/Y H:i') }} → {{ $reservation->ends_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td style="color: #6c757d;">{{ __('site.booking.summary_total') }}</td><td><strong>{{ format_price((float) $reservation->total_price) }}</strong></td></tr>
    </table>

    <p style="font-size: 13px; color: #6c757d;">{{ __('site.booking.mail.internal_footer') }}</p>
</body>
</html>
