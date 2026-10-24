<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #212529; max-width: 640px; margin: 0 auto;">
    <div style="border-bottom: 3px solid #dc3545; padding-bottom: 12px; margin-bottom: 20px;">
        <h1 style="color: #14213d; margin: 0; font-size: 22px;">{{ setting('company_name') }}</h1>
    </div>

    <h2 style="color: #dc3545;">{{ __('site.admin.reservations.mail.rejected_title') }}</h2>

    <p>{{ __('site.admin.reservations.mail.rejected_hello', ['name' => $reservation->clientFullName()]) }},</p>

    <p>{{ __('site.admin.reservations.mail.rejected_intro', ['code' => $reservation->code]) }}</p>

    @if ($reason)
        <p style="background: #f6f7f9; border-left: 4px solid #dc3545; padding: 12px 16px;">{{ $reason }}</p>
    @endif

    <p>{{ __('site.admin.reservations.mail.rejected_footer') }}</p>
</body>
</html>
