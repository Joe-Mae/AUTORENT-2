<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ __('site.admin.reports.title') }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #212529; }
        h1 { font-size: 16px; color: #14213d; margin: 0 0 4px; }
        .period { color: #6c757d; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #14213d; color: #fff; text-align: left; padding: 6px 8px; font-size: 11px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; text-align: right; background: #f6f7f9; }
        .footer { color: #6c757d; font-size: 10px; margin-top: 16px; border-top: 1px solid #dee2e6; padding-top: 8px; }
    </style>
</head>
<body>
    <h1>{{ setting('company_name') }} — {{ __('site.admin.reports.title') }}</h1>
    <p class="period">{{ $from->format('d/m/Y') }} → {{ $to->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr><th>Code</th><th>Client</th><th>Véhicule</th><th>Début</th><th>Fin</th><th style="text-align: right;">Total</th></tr>
        </thead>
        <tbody>
            @foreach ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->code }}</td>
                    <td>{{ $reservation->clientFullName() }}</td>
                    <td>{{ $reservation->vehicle?->fullName() ?? '—' }}</td>
                    <td>{{ $reservation->starts_at->format('d/m/Y') }}</td>
                    <td>{{ $reservation->ends_at->format('d/m/Y') }}</td>
                    <td style="text-align: right;">{{ format_price((float) $reservation->total_price) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="total">{{ __('site.admin.reports.revenue') }}</td>
                <td class="total">{{ format_price((float) $reservations->sum('total_price')) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="footer">{{ setting('company_name') }} · {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
