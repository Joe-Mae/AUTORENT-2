<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Rapports d'activité : réservations et revenus par période,
 * exportables en CSV et PDF (spécification §6.2).
 */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to, $reservations] = $this->period($request);

        return view('admin.reports.index', [
            'reservations' => $reservations,
            'from' => $from,
            'to' => $to,
            'summary' => [
                'count' => $reservations->count(),
                'revenue' => $reservations->sum('total_price'),
                'days' => $reservations->sum('days_count'),
                'by_type' => $reservations->groupBy('type')->map->count(),
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to, $reservations] = $this->period($request);

        return response()->streamDownload(function () use ($reservations): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($out, ['Code', 'Client', 'Téléphone', 'Formule', 'Véhicule', 'Début', 'Fin', 'Jours', 'Total', 'Statut'], ';');
            foreach ($reservations as $reservation) {
                fputcsv($out, [
                    $reservation->code,
                    $reservation->clientFullName(),
                    $reservation->phone,
                    $reservation->type->label(),
                    $reservation->vehicle?->fullName() ?? '—',
                    $reservation->starts_at->format('d/m/Y H:i'),
                    $reservation->ends_at->format('d/m/Y H:i'),
                    $reservation->days_count,
                    $reservation->total_price,
                    $reservation->status->label(),
                ], ';');
            }
            fclose($out);
        }, 'reservations-'.$from->format('Ymd').'-'.$to->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(Request $request)
    {
        [$from, $to, $reservations] = $this->period($request);

        return Pdf::loadView('pdf.report', [
            'reservations' => $reservations,
            'from' => $from,
            'to' => $to,
        ])->download('rapport-'.$from->format('Ymd').'-'.$to->format('Ymd').'.pdf');
    }

    /**
     * Borne la période demandée : le mois en cours par défaut.
     *
     * @return array{0: Carbon, 1: Carbon, 2: mixed}
     */
    private function period(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : now()->endOfMonth();

        $reservations = Reservation::with('vehicle')
            ->whereBetween('starts_at', [$from, $to])
            ->whereIn('status', ['confirmed', 'ongoing', 'completed'])
            ->orderBy('starts_at')
            ->get();

        return [$from, $to, $reservations];
    }
}
