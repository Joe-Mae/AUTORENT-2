<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #212529; max-width: 640px; margin: 0 auto;">
    <div style="border-bottom: 3px solid #198754; padding-bottom: 12px; margin-bottom: 20px;">
        <h1 style="color: #14213d; margin: 0; font-size: 22px;">{{ setting('company_name') }}</h1>
    </div>

    <h2 style="color: #198754;">✓ {{ __('site.admin.reservations.mail.confirmed_title') }}</h2>

    <p>{{ __('site.admin.reservations.mail.confirmed_hello', ['name' => $reservation->clientFullName()]) }},</p>

    <p>{{ __('site.admin.reservations.mail.confirmed_intro') }}</p>

    <p style="background: #f6f7f9; border-left: 4px solid #198754; padding: 12px 16px; font-size: 16px; font-weight: bold;">
        {{ $reservation->code }} · {{ $reservation->starts_at->format('d/m/Y H:i') }}
    </p>

    <p style="background: #fff3cd; border: 1px solid #ffe69c; border-radius: 6px; padding: 12px;">
        ⚠️ <strong>{{ __('site.booking.license_reminder') }}</strong>
    </p>
</body>
</html>
