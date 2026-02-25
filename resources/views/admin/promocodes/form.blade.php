@php
    $promoCode = $promoCode ?? null;
@endphp

<div>
    <label class="text-sm font-semibold">{{ __('messages.promo_code') }}</label>
    <input name="code" value="{{ old('code', $promoCode?->code) }}" class="lego-input mt-2" placeholder="LEGO10" required>
    @error('code') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.promo_type') }}</label>
    <select name="type" class="lego-input mt-2">
        <option value="percent" @selected(old('type', $promoCode?->type) === 'percent')>{{ __('messages.promo_type_percent') }}</option>
        <option value="fixed" @selected(old('type', $promoCode?->type) === 'fixed')>{{ __('messages.promo_type_fixed') }}</option>
    </select>
    @error('type') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.value') }}</label>
    <input type="number" name="value" value="{{ old('value', $promoCode?->value) }}" class="lego-input mt-2" step="0.01" min="0" required>
    @error('value') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.valid_from') }}</label>
    <input type="date" name="valid_from" value="{{ old('valid_from', $promoCode?->valid_from?->format('Y-m-d')) }}" class="lego-input mt-2">
    @error('valid_from') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.valid_until') }}</label>
    <input type="date" name="valid_until" value="{{ old('valid_until', $promoCode?->valid_until?->format('Y-m-d')) }}" class="lego-input mt-2">
    @error('valid_until') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<div>
    <label class="text-sm font-semibold">{{ __('messages.usage_limit') }}</label>
    <input type="number" name="usage_limit" value="{{ old('usage_limit', $promoCode?->usage_limit) }}" class="lego-input mt-2" min="1" placeholder="{{ __('messages.unlimited') }}">
    @error('usage_limit') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
</div>

<label class="flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promoCode?->is_active ?? true))>
    {{ __('messages.is_active') }}
</label>

<button class="lego-btn lego-btn-primary text-xs">{{ __('messages.save') }}</button>
