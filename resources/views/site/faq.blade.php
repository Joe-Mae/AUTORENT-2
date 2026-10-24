@extends('layouts.public')

@section('title', __('site.faq.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 860px;">
            <div class="text-center mb-5">
                <h1 class="fw-bold">{{ __('site.faq.title') }}</h1>
                <p class="text-muted">{{ __('site.faq.subtitle') }}</p>
            </div>

            @if ($faqs->isEmpty())
                <div class="alert alert-light border" role="status">{{ __('site.faq.empty') }}</div>
            @else
                <div class="accordion" id="faqAccordion">
                    @foreach ($faqs as $index => $faq)
                        <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                            <h3 class="accordion-header" id="faq-heading-{{ $faq->id }}">
                                <button class="accordion-button @unless ($index === 0) collapsed @endunless bg-white fw-semibold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq-{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h3>
                            <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse @if ($index === 0) show @endif"
                                 aria-labelledby="faq-heading-{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="text-center text-muted mt-4 mb-0">
                    {{ __('site.faq.other_question') }}
                    <a href="{{ route('contact', app()->getLocale()) }}" class="text-decoration-none fw-semibold">{{ __('site.nav.contact') }}</a>
                </p>
            @endif
        </div>
    </section>
@endsection
