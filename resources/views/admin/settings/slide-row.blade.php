@php($isNew = $slide === null)
<div class="border rounded-3 p-3 bg-surface" data-slide-row>
    <div class="row g-2 align-items-center">
        <div class="col-md-3">
            @if (! $isNew && ! empty($slide['image']))
                <img src="{{ asset(str_starts_with($slide['image'], 'http') ? '' : '').(str_starts_with($slide['image'], 'http') ? $slide['image'] : 'storage/'.$slide['image']) }}"
                     alt="" class="w-100 rounded object-fit-cover" style="height: 64px;">
                <input type="hidden" name="slides[{{ $index }}][existing_image]" value="{{ $slide['image'] }}">
            @else
                <span class="d-inline-block border rounded bg-white" style="height: 64px; width: 100%;"></span>
            @endif
            <input type="file" name="hero_images[{{ $index }}]" accept="image/*" class="form-control form-control-sm mt-1">
        </div>
        <div class="col-md-4">
            <label class="form-label small mb-0 fw-semibold">{{ __('site.admin.settings.slide_title') }}</label>
            <input type="text" name="slides[{{ $index }}][title]" value="{{ $slide['title'] ?? '' }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-5">
            <label class="form-label small mb-0 fw-semibold">{{ __('site.admin.settings.slide_text') }}</label>
            <textarea name="slides[{{ $index }}][text]" rows="2" class="form-control form-control-sm">{{ $slide['text'] ?? '' }}</textarea>
        </div>
    </div>
</div>
