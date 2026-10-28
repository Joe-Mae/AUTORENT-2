@php
    use App\Enums\ReservationStatus;

    $status = $reservation->status;
    $fuelLabels = [
        'eighth' => '1/8', 'quarter' => '1/4', 'half' => '1/2',
        'three_quarters' => '3/4', 'full' => __('Plein'),
    ];
@endphp
@extends('layouts.admin')

@section('title', $reservation->code)
@section('page_title', $reservation->code.' · '.$reservation->clientFullName())

@section('content')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('site.common.close') }}"></button>
        </div>
    @endif

    <div class="row g-3">
        {{-- Colonne principale --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                        <div>
                            <h2 class="h4 fw-bold mb-1">{{ $reservation->code }}</h2>
                            <p class="text-muted mb-0">
                                {{ $reservation->type->label() }}
                                @if ($reservation->vehicle) · {{ $reservation->vehicle->fullName() }} ({{ $reservation->vehicle->registration_plate }}) @endif
                                @if ($reservation->driver) · {{ __('site.admin.reservations.driver') }} : {{ $reservation->driver->full_name }} @endif
                            </p>
                        </div>
                        <x-reservation-status :status="$status" />
                    </div>

                    <div class="row small">
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.client') }}</strong><br>{{ $reservation->clientFullName() }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.contact.phone') }}</strong><br>{{ $reservation->phone }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.contact.email') }}</strong><br>{{ $reservation->email }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.birth_date') }}</strong><br>{{ $reservation->birth_date?->format('d/m/Y') ?? '—' }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.id_card_number') }}</strong><br>{{ $reservation->id_card_number ?? '—' }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.home.search.pickup_date') }}</strong><br>{{ $reservation->starts_at->format('d/m/Y H:i') }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.home.search.return_date') }}</strong><br>{{ $reservation->ends_at->format('d/m/Y H:i') }}</div>
                        <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.summary_days') }}</strong><br>{{ $reservation->days_count }}</div>
                        @if ($reservation->pickup_location)
                            <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.pickup_location') }}</strong><br>{{ $reservation->pickup_location }}</div>
                        @endif
                        @if ($reservation->event_reason)
                            <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.event_reason') }}</strong><br>{{ $reservation->event_reason }}</div>
                        @endif
                        @if ($reservation->passenger_count)
                            <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.booking.passenger_count') }}</strong><br>{{ $reservation->passenger_count }}</div>
                        @endif
                        @if ($reservation->client_vehicle_brand)
                            <div class="col-6 col-md-3 mb-2"><strong>{{ __('site.admin.reservations.client_vehicle') }}</strong><br>{{ $reservation->client_vehicle_brand }} ({{ $reservation->client_vehicle_plate }})</div>
                        @endif
                        @if ($reservation->itinerary)
                            <div class="col-12 mb-2"><strong>{{ __('site.booking.itinerary') }}</strong><br>{{ $reservation->itinerary }}</div>
                        @endif
                        @if ($reservation->notes)
                            <div class="col-12 mb-2"><strong>{{ __('site.booking.notes') }}</strong><br>{{ $reservation->notes }}</div>
                        @endif
                        @if ($reservation->internal_notes)
                            <div class="col-12 mb-2"><strong>{{ __('site.admin.reservations.internal_notes') }}</strong><br>{{ $reservation->internal_notes }}</div>
                        @endif
                    </div>

                    <hr>

                    <div class="d-flex flex-wrap justify-content-between">
                        <span>{{ __('site.booking.summary_vehicle_price') }} : {{ format_price((float) $reservation->vehicle_daily_price) }}{{ __('site.common.per_day') }}</span>
                        <span>{{ __('site.booking.summary_driver_price') }} : {{ format_price((float) $reservation->driver_daily_price) }}{{ __('site.common.per_day') }}</span>
                        <strong class="text-primary">{{ __('site.booking.summary_total') }} : {{ format_price((float) $reservation->total_price) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Caisse : encaissements de la location --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-cash-coin me-1 text-primary"></i>{{ __('site.admin.payments.title') }}</h3>
                        <x-payment-status :reservation="$reservation" />
                    </div>

                    <div class="row small mb-3">
                        <div class="col-4"><strong>{{ __('site.booking.summary_total') }}</strong><br>{{ format_price((float) $reservation->total_price) }}</div>
                        <div class="col-4"><strong>{{ __('site.admin.payments.paid_amount') }}</strong><br>{{ format_price($reservation->paidAmount()) }}</div>
                        <div class="col-4"><strong>{{ __('site.admin.payments.balance') }}</strong><br class="d-none">{{ format_price($reservation->paymentBalance()) }}</div>
                    </div>

                    @forelse ($reservation->payments as $payment)
                        <div class="d-flex justify-content-between align-items-center border-top py-2">
                            <div class="small">
                                <i class="bi bi-{{ $payment->method->icon() }} me-1"></i><strong>{{ $payment->method->label() }}</strong>
                                @if ($payment->reference)<span class="text-muted">· {{ $payment->reference }}</span>@endif
                                <span class="text-muted d-block">{{ $payment->receiver?->name ?? '—' }} · {{ $payment->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-semibold">{{ format_price((float) $payment->amount) }}</span>
                                <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="d-inline"
                                      data-confirm="{{ __('site.admin.payments.delete_confirm') }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-1 ms-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="small text-muted mb-0">{{ __('site.admin.payments.none_yet') }}</p>
                    @endforelse

                    @unless ($reservation->isFullyPaid())
                        <form method="POST" action="{{ route('admin.payments.store', $reservation) }}" class="row g-2 mt-3 border-top pt-3">
                            @csrf
                            <div class="col-6 col-md-3">
                                <label class="form-label small mb-0 fw-semibold">{{ __('site.admin.payments.amount') }}</label>
                                <input type="number" name="amount" min="100" step="100" max="{{ $reservation->paymentBalance() }}"
                                       value="{{ $reservation->paymentBalance() }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small mb-0 fw-semibold">{{ __('site.admin.payments.method') }}</label>
                                <select name="method" class="form-select form-select-sm">
                                    @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                        <option value="{{ $method->value }}">{{ $method->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small mb-0 fw-semibold">{{ __('site.admin.payments.reference') }}</label>
                                <input type="text" name="reference" class="form-control form-control-sm" placeholder="{{ __('site.admin.payments.reference_hint') }}">
                            </div>
                            <div class="col-6 col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-circle me-1"></i>{{ __('site.admin.payments.record') }}</button>
                            </div>
                        </form>
                    @endunless
                </div>
            </div>

            {{-- Documents d'identité et permis --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-person-vcard me-1 text-primary"></i>{{ __('site.admin.reservations.documents') }}</h3>

                    <div class="row g-3">
                        @foreach ($reservation->documents as $document)
                            <div class="col-6 col-md-4">
                                <a href="{{ route('admin.reservations.documents.show', [$reservation, $document]) }}" target="_blank" rel="noopener"
                                   class="d-block border rounded-3 p-2 text-decoration-none bg-surface h-100">
                                    <i class="bi {{ $document->type === \App\Enums\DocumentType::License ? 'bi-card-checklist' : 'bi-person-vcard' }} fs-3 d-block mb-1 {{ $document->type === \App\Enums\DocumentType::License ? 'text-accent' : 'text-primary' }}"></i>
                                    <span class="small fw-semibold d-block">{{ $document->type->label() }}</span>
                                    <small class="text-muted">{{ $document->created_at->format('d/m/Y H:i') }} · {{ $document->original_name }}</small>
                                </a>
                            </div>
                        @endforeach

                        {{-- Zone d'ajout du permis (photographié/scanné à l'agence) --}}
                        @if (in_array($status, [ReservationStatus::Pending, ReservationStatus::IdentityVerified, ReservationStatus::Confirmed]) && auth()->user()->can('reservations.upload_license'))
                            <div class="col-6 col-md-4">
                                <form method="POST" action="{{ route('admin.reservations.license', $reservation) }}"
                                      enctype="multipart/form-data" class="border border-dashed rounded-3 p-2 h-100 d-flex flex-column">
                                    @csrf
                                    <i class="bi bi-camera fs-3 d-block mb-1 text-secondary"></i>
                                    <span class="small fw-semibold d-block">{{ __('site.admin.reservations.add_license') }}</span>
                                    <input type="file" name="license" accept="image/*" capture="environment" class="form-control form-control-sm mt-2" required onchange="this.form.submit()">
                                    <small class="text-muted mt-1">{{ __('site.admin.reservations.add_license_hint') }}</small>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Historique --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3"><i class="bi bi-clock-history me-1 text-primary"></i>{{ __('site.admin.reservations.timeline') }}</h3>

                    <ul class="list-unstyled mb-0">
                        @foreach ($reservation->statusHistories as $history)
                            <li class="d-flex gap-3 pb-3 border-bottom">
                                <i class="bi bi-circle-fill small text-primary mt-1"></i>
                                <div class="small">
                                    <strong>{{ \App\Enums\ReservationStatus::tryFrom($history->to_status)?->label() ?? $history->to_status }}</strong>
                                    <span class="text-muted">· {{ $history->created_at->format('d/m/Y H:i') }} · {{ $history->user?->name ?? __('site.admin.reservations.system') }}</span>
                                    @if ($history->note)<p class="mb-1 mt-1">{{ $history->note }}</p>@endif
                                    @if ($history->properties)
                                        <p class="mb-0 text-muted">
                                            @if (isset($history->properties['mileage'])){{ __('site.admin.reservations.mileage') }} : {{ number_format($history->properties['mileage'], 0, ',', ' ') }} km · @endif
                                            @if (isset($history->properties['fuel'])){{ __('site.admin.reservations.fuel') }} : {{ $fuelLabels[$history->properties['fuel']] ?? '—' }} · @endif
                                            @if (isset($history->properties['damages'])){{ __('site.admin.reservations.damages') }} : {{ $history->properties['damages'] }}@endif
                                        </p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Colonne d'actions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-3">{{ __('site.admin.reservations.actions') }}</h3>

                    <a href="{{ route('admin.reservations.pdf', $reservation) }}" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('site.admin.reservations.download_pdf') }}
                    </a>

                    @can('reservations.manage')
                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('admin.reservations.edit', $reservation) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i>{{ __('site.admin.reservations.edit') }}
                            </a>
                            @if ($reservation->type->requiresDriver())
                                <form method="POST" action="{{ route('admin.reservations.assign-driver', $reservation) }}" class="d-grid gap-1">
                                    @csrf
                                    <label class="small fw-semibold mb-0" for="driver_id">{{ __('site.admin.drivers.title') }}</label>
                                    <select name="driver_id" id="driver_id" class="form-select form-select-sm">
                                        <option value="">{{ __('site.admin.reservations.no_driver') }}</option>
                                        @foreach (\App\Models\Driver::active()->orderBy('full_name')->get() as $driver)
                                            <option value="{{ $driver->id }}" @selected($reservation->driver_id === $driver->id)>{{ $driver->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary btn-sm">{{ __('site.admin.reservations.assign_driver') }}</button>
                                </form>
                            @endif
                            @unless ($reservation->status->isTerminal())
                                <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                                      data-confirm="{{ __('site.admin.reservations.cancel_confirm') }}">
                                    @csrf
                                    <textarea name="note" rows="2" class="form-control form-control-sm mb-2" placeholder="{{ __('site.admin.reservations.reject_reason') }}"></textarea>
                                    <button class="btn btn-outline-danger w-100">
                                        <i class="bi bi-slash-circle me-1"></i>{{ __('site.admin.reservations.cancel') }}
                                    </button>
                                </form>
                            @endunless
                        </div>
                    @endcan

                    @if ($status === ReservationStatus::Pending && auth()->user()->can('reservations.verify_identity'))
                        <form method="POST" action="{{ route('admin.reservations.verify-identity', $reservation) }}">
                            @csrf
                            <button class="btn btn-info w-100 mb-2">
                                <i class="bi bi-person-check me-1"></i>{{ __('site.admin.reservations.verify_identity') }}
                            </button>
                        </form>
                        <p class="small text-muted">{{ __('site.admin.reservations.verify_identity_hint') }}</p>
                    @endif

                    @if (in_array($status, [ReservationStatus::Pending, ReservationStatus::IdentityVerified]) && auth()->user()->can('reservations.decide'))
                        <form method="POST" action="{{ route('admin.reservations.decide', $reservation) }}" class="d-grid gap-2">
                            @csrf
                            <input type="hidden" name="decision" value="confirm">
                            <button class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>{{ __('site.admin.reservations.confirm') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.reservations.decide', $reservation) }}" class="mt-2"
                              data-confirm="{{ __('site.admin.reservations.reject_confirm') }}">
                            @csrf
                            <input type="hidden" name="decision" value="reject">
                            <textarea name="note" rows="2" class="form-control form-control-sm mb-2" placeholder="{{ __('site.admin.reservations.reject_reason') }}"></textarea>
                            <button class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle me-1"></i>{{ __('site.admin.reservations.reject') }}
                            </button>
                        </form>
                    @endif

                    @if ($status === ReservationStatus::Confirmed && auth()->user()->can('reservations.update_status'))
                        <hr>
                        <h4 class="small fw-bold">{{ __('site.admin.reservations.handover_title') }}</h4>
                        <form method="POST" action="{{ route('admin.reservations.handover', $reservation) }}" class="row g-2"
                              data-confirm="{{ __('site.admin.reservations.handover_confirm') }}">
                            @csrf
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.mileage') }}</label>
                                <input type="number" name="mileage" min="0" class="form-control form-control-sm" placeholder="km">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.fuel') }}</label>
                                <select name="fuel" class="form-select form-select-sm">
                                    <option value="eighth">1/8</option>
                                    <option value="quarter">1/4</option>
                                    <option value="half">1/2</option>
                                    <option value="three_quarters">3/4</option>
                                    <option value="full">{{ __('Plein') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.damages') }}</label>
                                <input type="text" name="damages" class="form-control form-control-sm" placeholder="{{ __('site.admin.reservations.damages_none') }}">
                            </div>
                            <div class="col-12 d-grid">
                                <button class="btn btn-primary">
                                    <i class="bi bi-key me-1"></i>{{ __('site.admin.reservations.handover') }}
                                </button>
                            </div>
                        </form>
                    @endif

                    @if ($status === ReservationStatus::Ongoing && auth()->user()->can('reservations.update_status'))
                        <hr>
                        <h4 class="small fw-bold">{{ __('site.admin.reservations.return_title') }}</h4>
                        <form method="POST" action="{{ route('admin.reservations.return', $reservation) }}" class="row g-2"
                              data-confirm="{{ __('site.admin.reservations.return_confirm') }}">
                            @csrf
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.mileage') }}</label>
                                <input type="number" name="mileage" min="0" class="form-control form-control-sm" placeholder="km">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.fuel') }}</label>
                                <select name="fuel" class="form-select form-select-sm">
                                    <option value="eighth">1/8</option>
                                    <option value="quarter">1/4</option>
                                    <option value="half">1/2</option>
                                    <option value="three_quarters">3/4</option>
                                    <option value="full">{{ __('Plein') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.damages') }}</label>
                                <input type="text" name="damages" class="form-control form-control-sm" placeholder="{{ __('site.admin.reservations.damages_none') }}">
                            </div>
                            <div class="col-12 d-grid">
                                <button class="btn btn-secondary">
                                    <i class="bi bi-check2-circle me-1"></i>{{ __('site.admin.reservations.return') }}
                                </button>
                            </div>
                        </form>
                    @endif

                    @if ($status->isTerminal())
                        <p class="text-muted small mb-0">{{ __('site.admin.reservations.terminal_status') }}</p>
                    @endif

                    @can('settings.manage')
                        <hr>
                        <h4 class="small fw-bold text-danger">{{ __('site.admin.reservations.rgpd_title') }}</h4>
                        <p class="small text-muted">{{ __('site.admin.reservations.rgpd_hint') }}</p>
                        @if ($reservation->documents->isNotEmpty())
                            <form method="POST" action="{{ route('admin.reservations.purge-documents', $reservation) }}" data-confirm="{{ __('site.admin.reservations.purge_confirm') }}">
                                @csrf
                                <button class="btn btn-outline-danger btn-sm w-100 mb-2">
                                    <i class="bi bi-eraser me-1"></i>{{ __('site.admin.reservations.purge_documents') }}
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.reservations.anonymize', $reservation) }}" data-confirm="{{ __('site.admin.reservations.anonymize_confirm') }}">
                            @csrf
                            <button class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-person-x me-1"></i>{{ __('site.admin.reservations.anonymize') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
