<x-theme-genesis>
    <x-slot name="title">Génesis - Intro</x-slot>
    <section class="w-full relative bg-withe pt-16 pb-28 flex justify-center px-8">
        <div class="relative z-10 flex w-full h-full max-w-6xl">
            <div class="h-auto w-full flex flex-col gap-40 justify-center items-center">
                <a class="" href="{{route('home')}}"><img class="w-12" src="{{ asset('images/god-ai-isologo.png') }}" alt=""></a>
                <div class="flex flex-col gap-20 justify-center items-center">
                    <div class="sm:text-8xl text-6xl text-center">Génesis</div>
                    {{-- <div class="flex flex-col gap-4">
                        <x-button-genesis href="{{route('login')}}" class="text-xl">Iniciar</x-button-genesis>
                       
                    </div> --}}
                    
                </div>
                
                <div class="flex flex-col gap-20 w-full justify-between relative">
                    {{-- <div class="sm:text-8xl text-6xl text-center">El futuro<br>aquí y ahora.</div> --}}
                    <div class="sm:text-3xl text-2xl text-center">
                        <p>Tu subscripción ha finalizado, comunicate con el administrador para adquirir una nueva ¡Muchas gracias por usar Godai!</p></div>
                    <div class="flex flex-row flex-wrap gap-4 justify-center items-end content-center">
                        <img class="scale-75" src="{{ asset('images/god-ai-logo-gemini.png') }}" alt="Gemini">
                        <img class="scale-75" src="{{ asset('images/god-ai-logo-claude.png') }}" alt="Claude">
                        <img class="scale-75" src="{{ asset('images/god-ai-logo-gpt4o.png') }}" alt="GPT4o">
                    </div>
                </div>
            </div>
        </div>
        <div class="image-wrapper h-full absolute top-0 left-0 w-full z-0">
            <img class="h-full w-full object-cover object-top absolute" src="{{ asset('images/godai-bg-intro.jpg') }}" alt="">
        </div>
    </section>
    <x-slot name="footer">
        <x-theme-genesis-footer />
    </x-slot>
</x-theme-genesis>