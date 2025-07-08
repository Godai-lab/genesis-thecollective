
<x-theme-genesis>
    <x-slot name="title">Génesis - Inicio</x-slot>
    <x-slot name="header">
        <x-theme-genesis-header />
    </x-slot>
    <section class="h-screen w-full relative bg-withe pt-16 pb-28 flex justify-center px-8">
        <div class="relative z-10 flex w-full h-full max-w-6xl">
            <div class="col-span-1 h-auto flex flex-col gap-2 self-end justify-between my-14">
                <img class="w-24" src="{{ asset('images/god-ai-logo.png') }}" alt="logo">
                <div class="text-xl">Potenciando las habilidades humanas con inteligencia artificial.</div>
            </div>
        </div>
        <div class="image-wrapper h-full absolute top-0 left-0 w-full z-0">
            <img class="h-full w-full object-cover absolute" src="{{ asset('images/godai-bg-home.jpg') }}" alt="">
        </div>
    </section>
    <section class="w-full relative bg-zinc-300 py-52 flex justify-center px-8">
        <div class="relative z-10 flex w-full h-full max-w-6xl">
            <div class="h-auto flex flex-col gap-14 self-end">
                <div class="text-xl">Te ayudamos a integrar la IA generativa en tu organización para mejorar la eficiencia, optimizar recursos humanos y obtener una ventaja competitiva en el mercado.</div>
                <div class="flex flex-col gap-2">
                    <div>Clic si eres:</div>
                    <div class="flex flex-row gap-5">
                        <x-button-genesis class="" href="https://calendly.com/el-feo/genai-agencias" >Agencia</x-button-genesis>
                        <x-button-genesis class="" href="https://calendly.com/el-feo/genai-marcas" >Marca</x-button-genesis>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="w-full relative bg-withe pt-16 pb-28 flex justify-center px-8">
        <div class="relative z-10 flex w-full h-full max-w-6xl">
            <div class="h-auto w-full flex flex-col gap-48 justify-center items-center">
                <a class="" href="{{route('home')}}"><img class="w-12" src="{{ asset('images/god-ai-isologo.png') }}" alt=""></a>
                <div class="sm:text-8xl text-6xl text-center">El futuro<br>aquí y ahora.</div>
                <div class="flex flex-row flex-wrap gap-5 w-full justify-between relative">
                    <div class="sm:basis-1/3">Nuestra metodología facilita la implementación de IA en tu organización. Además tenemos integraciones tecnológicas propias con IA.</div>
                    <div class="flex flex-col gap-2 sm:justify-end sm:basis-1/3 w-full">
                        <div class="flex justify-end">Conoce a:</div>
                        <div class="flex flex-row gap-5 justify-end">
                            <x-button-genesis class="" href="#" >Trinidad</x-button-genesis>
                            <x-button-genesis class="" href="{{route('intro')}}" >Génesis</x-button-genesis>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="image-wrapper h-full absolute top-0 left-0 w-full z-0">
            <img class="h-full w-full object-cover object-top absolute" src="{{ asset('images/godai-bg2-home.jpg') }}" alt="">
        </div>
    </section>
    <x-slot name="footer">
        <x-theme-genesis-footer />
    </x-slot>
</x-theme-genesis>
