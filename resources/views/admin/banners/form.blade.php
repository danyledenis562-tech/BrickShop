@php
    $positions = [
        'home_hero' => __('messages.banner_position_home_hero'),
        'home_middle' => __('messages.banner_position_home_middle'),
        'catalog_top' => __('messages.banner_position_catalog_top'),
        'checkout_side' => __('messages.banner_position_checkout_side'),
        'product_bottom' => __('messages.banner_position_product_bottom'),
    ];
    $locales = [
        null => __('messages.banner_locale_all'),
        'uk' => __('messages.lang_ukrainian'),
        'ru' => __('messages.lang_russian'),
        'en' => __('messages.lang_english'),
        'pl' => __('messages.lang_polish'),
    ];
@endphp

<div>
    <label class="text-sm font-semibold">{{ __('messages.title') }}</label>
    <input name="title" value="{{ old('title', $banner->title ?? '') }}" class="lego-input mt-2" required>
    @error('title') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.subtitle') }}</label>
    <input name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}" class="lego-input mt-2">
    @error('subtitle') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.image_url') }}</label>
    <input name="image" value="{{ old('image', $banner->image ?? '') }}" class="lego-input mt-2" placeholder="{{ __('messages.image_placeholder') }}">
    @error('image') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.link_url') }}</label>
    <input name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" class="lego-input mt-2" required>
    @error('link_url') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-semibold">{{ __('messages.position') }}</label>
        <select name="position" class="lego-input mt-2">
            @foreach ($positions as $value => $label)
                <option value="{{ $value }}" @selected(old('position', $banner->position ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('position') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm font-semibold">{{ __('messages.locale') }}</label>
        <select name="locale" class="lego-input mt-2">
            @foreach ($locales as $value => $label)
                <option value="{{ $value }}" @selected(old('locale', $banner->locale ?? null) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('locale') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div>
        <label class="text-sm font-semibold">{{ __('messages.sort_order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="lego-input mt-2" min="0">
        @error('sort_order') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="text-sm font-semibold">{{ __('messages.starts_at') }}</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', isset($banner->starts_at) ? $banner->starts_at?->format('Y-m-d\TH:i') : null) }}" class="lego-input mt-2">
        @error('starts_at') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
    </div>
    <div>
        <label class="text-sm font-semibold">{{ __('messages.ends_at') }}</label>
        <input type="datetime-local" name="ends_at" value="{{ old('ends_at', isset($banner->ends_at) ? $banner->ends_at?->format('Y-m-d\TH:i') : null) }}" class="lego-input mt-2">
        @error('ends_at') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true))>
    {{ __('messages.is_active') }}
</label>

<button class="lego-btn lego-btn-primary text-xs">{{ __('messages.save') }}</button>
