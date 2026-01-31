<button {{ $attributes->merge(['type' => 'button', 'class' => 'lego-btn lego-btn-secondary']) }}>
    {{ $slot }}
</button>
