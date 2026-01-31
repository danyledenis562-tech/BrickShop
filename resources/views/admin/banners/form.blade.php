@php
    $positions = [
        'home_hero' => __('messages.banner_position_home_hero'),
        'home_middle' => __('messages.banner_position_home_middle'),
        'catalog_top' => __('messages.banner_position_catalog_top'),
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
    <input name="image" value="{{ old('image', $banner->image ?? '') }}" class="lego-input mt-2" placeholder="https://... або storage/banner.jpg">
    @error('image') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.link_url') }}</label>
    <input name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" class="lego-input mt-2" required>
    @error('link_url') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.position') }}</label>
    <select name="position" class="lego-input mt-2">
        @foreach ($positions as $value => $label)
            <option value="{{ $value }}" @selected(old('position', $banner->position ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('position') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true))>
    {{ __('messages.is_active') }}
</label>

<button class="lego-btn lego-btn-primary text-xs">{{ __('messages.save') }}</button>
