<button {{ $attributes->merge(['type' => 'submit', 'class' => 'lego-btn lego-btn-primary']) }}>
    {{ $slot }}
</button>
