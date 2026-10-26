<footer class="bg-secondary text-white pt-5 pb-4 mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">{{ setting('company_name') }}</h5>
                <p class="text-white-50">{{ setting('footer_text') }}</p>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="fw-bold mb-3">{{ __('site.common.navigation') }}</h6>
                <ul class="list-unstyled d-grid gap-2">
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('vehicles.index', app()->getLocale()) }}">{{ __('site.nav.vehicles') }}</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('driver-service', app()->getLocale()) }}">{{ __('site.nav.driver_service') }}</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('check-booking', app()->getLocale()) }}">{{ __('site.nav.check_booking') }}</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('faq', app()->getLocale()) }}">{{ __('site.nav.faq') }}</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('contact', app()->getLocale()) }}">{{ __('site.nav.contact') }}</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-bold mb-3">{{ __('site.nav.contact') }}</h6>
                <ul class="list-unstyled d-grid gap-2 text-white-50">
                    @if (setting('contact_address'))
                        <li><i class="bi bi-geo-alt me-2"></i>{{ setting('contact_address') }}</li>
                    @endif
                    @if (setting('contact_phone'))
                        <li><i class="bi bi-telephone me-2"></i>{{ setting('contact_phone') }}</li>
                    @endif
                    @if (setting('contact_email'))
                        <li><i class="bi bi-envelope me-2"></i>{{ setting('contact_email') }}</li>
                    @endif
                    @if (setting('contact_hours'))
                        <li><i class="bi bi-clock me-2"></i>{{ setting('contact_hours') }}</li>
                    @endif
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-bold mb-3">{{ __('site.common.follow_us') }}</h6>
                <div class="d-flex gap-2">
                    @foreach (array_filter(['facebook' => setting('social_facebook'), 'instagram' => setting('social_instagram'), 'tiktok' => setting('social_tiktok'), 'twitter-x' => setting('social_x')]) as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="text-white-50 fs-5" aria-label="{{ ucfirst($network) }}">
                            <i class="bi bi-{{ $network }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <hr class="border-secondary mt-4">

        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small text-white-50">
            <span>&copy; {{ date('Y') }} {{ setting('company_name') }}. {{ __('site.common.all_rights_reserved') }}</span>
            <nav class="d-flex flex-wrap gap-3" aria-label="{{ __('site.common.legal') }}">
                @foreach (['conditions-de-location' => __('site.common.terms'), 'confidentialite' => __('site.common.privacy'), 'mentions-legales' => __('site.common.legal_notice')] as $slug => $label)
                    <a href="{{ route('page', ['locale' => app()->getLocale(), 'page' => $slug]) }}" class="text-white-50 text-decoration-none">{{ $label }}</a>
                @endforeach
                <a href="{{ route('admin.login') }}"
                   class="btn btn-outline-light btn-sm py-0 px-2 opacity-50"
                   title="{{ __('site.admin.title') }}">
                    <i class="bi bi-lock small"></i> {{ __('site.common.admin') }}
                </a>
                @if (setting('contact_whatsapp'))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp')) }}" target="_blank" rel="noopener" class="text-white-50 text-decoration-none">
                        <i class="bi bi-whatsapp me-1"></i>{{ __('site.common.whatsapp') }}
                    </a>
                @endif
            </nav>
        </div>
    </div>
</footer>
