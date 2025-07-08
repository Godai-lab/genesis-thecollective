<button {{ $attributes->merge(['type' => 'submit', 'class' => 'border border-black hover:bg-transparent bg-black text-white hover:text-black rounded-xl px-5 text-lg text-center']) }}>
    {{ $slot }}
</button>
