@props(['type','action','icon'])
@if ($type)
    @if ($type == 'search')
        <button {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-black hover:bg-white hover:text-black border border-black'])}}
            type="submit">
            <i class="fa-solid fa-magnifying-glass"></i> 
        </button>
    @elseif ($type == 'clean')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-black hover:bg-white hover:text-black border border-black'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-eraser"></i> 
        </a>
    @elseif ($type == 'add')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-black dark:bg-green-500 bg-white border border-black dark:hover:bg-green-700 whitespace-nowrap'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-plus"></i> Agregar nuevo
        </a>
    @elseif ($type == 'edit')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white dark:bg-yellow-500 bg-black dark:hover:bg-yellow-700'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-pen-to-square"></i> 
        </a>
    @elseif ($type == 'delete')
        <form id="deleteForm" class="inline-block btnEliminar btn btn-sm btn-icon" action="{{ $action }}" method="POST" onSubmit="return  confirmDelete(this)">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold text-white transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none bg-black dark:bg-red-500 dark:hover:bg-red-700">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </form>
        <script>
            function confirmDelete(form) {
                event.preventDefault();
                Swal.fire({
                    title: '¿Está seguro?',
                    text: '¡No podrás revertir esto!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    @elseif ($type == 'save')
        <button {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-black hover:bg-black'])}}
            type="submit">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
    @elseif ($type == 'cancel')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-black hover:bg-black'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
    @elseif ($type == 'download')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-indigo-500 hover:bg-indigo-600'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-download"></i>
        </a>
    @elseif ($type == 'excel')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-indigo-500 hover:bg-indigo-600'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-file-excel"></i>
        </a>
    @elseif ($type == 'next')
        <button {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-green-500 hover:bg-green-700'])}}
            type="submit">
            <i class="fa-solid fa-arrow-right"></i> Siguiente
        </button>
    @elseif ($type == 'prev')
        <button {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-gray-500 hover:bg-gray-700'])}}
            type="submit">
            <i class="fa-solid fa-arrow-left"></i> Anterior
        </button>
    @elseif ($type == 'config')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-gray-500 hover:bg-gray-700'])}}
            href="{{ $action }}">
            <i class="fa-solid fa-gear"></i> 
        </a>
    @elseif ($type == 'custom')
        <a {{$attributes->merge(['class' => 
            'inline-block middle none center rounded-lg py-3 px-6 text-xs font-bold transition-all focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none text-white bg-gray-500 hover:bg-gray-700'])}}
            href="{{ $action }}">
            @if($icon)
                <i class="{{$icon}}"></i> 
            @else
                <i class="fa-solid fa-icons"></i> 
            @endif
        </a>
    @endif

@endif