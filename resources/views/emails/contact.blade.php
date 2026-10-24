<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #212529;">
    <h2 style="color: #14213d;">{{ __('Nouveau message de contact') }}</h2>

    <table cellpadding="6" cellspacing="0" border="0">
        <tr><td><strong>{{ __('Nom') }} :</strong></td><td>{{ $contactMessage->name }}</td></tr>
        <tr><td><strong>{{ __('Email') }} :</strong></td><td>{{ $contactMessage->email }}</td></tr>
        @if ($contactMessage->phone)
            <tr><td><strong>{{ __('Téléphone') }} :</strong></td><td>{{ $contactMessage->phone }}</td></tr>
        @endif
        <tr><td><strong>{{ __('Date') }} :</strong></td><td>{{ $contactMessage->created_at->format('d/m/Y H:i') }}</td></tr>
    </table>

    <p style="margin-top: 16px; padding: 12px; background: #f6f7f9; border-left: 3px solid #d95319;">
        {!! nl2br(e($contactMessage->message)) !!}
    </p>
</body>
</html>
