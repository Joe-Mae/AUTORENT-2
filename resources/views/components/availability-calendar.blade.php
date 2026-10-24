@props(['unavailableDates' => [], 'months' => 2])

@php
    /** Calendrier de disponibilité rendu côté serveur : aucune dépendance JS. */
    $weekDays = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
    $today = now()->startOfDay();
@endphp

<div class="availability-calendar d-flex flex-wrap gap-4">
    @for ($m = 0; $m < $months; $m++)
        @php
            $first = now()->addMonths($m)->startOfMonth();
        @endphp
        <div>
            <p class="fw-bold text-secondary text-capitalize mb-2">{{ $first->translatedFormat('F Y') }}</p>
            <div class="d-grid cal-grid small text-center">
                @foreach ($weekDays as $day)
                    <span class="text-muted fw-semibold">{{ $day }}</span>
                @endforeach

                @for ($i = 1; $i < $first->dayOfWeekIso; $i++)
                    <span></span>
                @endfor

                @for ($day = 1; $day <= $first->daysInMonth; $day++)
                    @php
                        $date = $first->copy()->setDay($day);
                        $classes = 'cal-day rounded-circle d-inline-flex align-items-center justify-content-center';
                        if ($date->lt($today)) {
                            $classes .= ' cal-past text-muted';
                        } elseif (in_array($date->format('Y-m-d'), $unavailableDates)) {
                            $classes .= ' cal-unavailable text-danger bg-danger-subtle';
                        }
                    @endphp
                    <span class="{{ $classes }}">{{ $day }}</span>
                @endfor
            </div>
        </div>
    @endfor
</div>
