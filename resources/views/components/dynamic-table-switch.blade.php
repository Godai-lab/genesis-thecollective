@props(['id','status'])
@if ($id)
    <div class="pointer-events-none inline-flex items-center">
        <div class="relative inline-block h-4 w-8 cursor-pointer rounded-full">
            <input
                id="status-{{ $id }}"
                type="checkbox"
                @if($status) checked="checked" @endif
                class="pointer-events-none peer absolute h-4 w-8 cursor-pointer appearance-none rounded-full bg-black transition-colors duration-300 checked:bg-black peer-checked:border-black peer-checked:before:bg-black !bg-none"
            />
            <label
                for="status-{{ $id }}"
                class="pointer-events-none before:content[''] absolute top-2/4 -left-1 h-5 w-5 -translate-y-2/4 cursor-pointer rounded-full border border-blue-gray-100 bg-white shadow-md transition-all duration-300 before:absolute before:top-2/4 before:left-2/4 before:block before:h-10 before:w-10 before:-translate-y-2/4 before:-translate-x-2/4 before:rounded-full before:bg-gray-50 before:opacity-0 before:transition-opacity hover:before:opacity-10 peer-checked:translate-x-full peer-checked:border-black peer-checked:before:bg-black"
            >
            <div
                class="top-2/4 left-2/4 inline-block -translate-x-2/4 -translate-y-2/4 rounded-full p-5"
                data-ripple-dark="true"
            >
            </div>
            </label>
        </div>
        <label
            for="auto-update"
            class="mt-px ml-3 mb-0 cursor-pointer select-none font-light text-black"
            >
        </label>
    </div>
@endif