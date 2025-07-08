@props(['id','name','selected','list','label'])
@if ($id && $name && $list && $label)
    <div class="relative h-10 w-full min-w-[200px]">
        <select
            {{$attributes->merge(['class' => 'peer h-full w-full rounded-[7px] border dark:border-gray-600 border-black border-t-transparent dark:bg-gray-800 bg-white px-3 py-2.5 text-sm font-normal dark:text-gray-200 text-black outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-gray-600 placeholder-shown:border-t-gray-600 focus:border-2 dark:focus:border-sky-300 focus:border-black focus:border-t-transparent focus:outline-0 disabled:border-0 disabled:bg-gray-600'])}}
            style="box-shadow: none;"
            id="{{$id}}" 
            name="{{$name}}"
        >
            <option class="hidden" value=""></option>
            @foreach($list as $key => $option)
                <option 
                    value="{{$option->id}}" 
                    @if($selected == $option->id) selected="selected" @endif
                >
                    {{$option->name}}
                </option>
            @endforeach
        </select>
        <label 
            class="before:content[' '] after:content[' '] pointer-events-none absolute left-0 -top-1.5 flex h-full w-full select-none text-[11px] font-normal leading-tight dark:text-gray-400 text-black transition-all before:pointer-events-none before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5 before:w-2.5 before:rounded-tl-md before:border-t before:border-l before:border-gray-600 before:transition-all after:pointer-events-none after:mt-[6.5px] after:ml-1 after:box-border after:block after:h-1.5 after:w-2.5 after:flex-grow after:rounded-tr-md after:border-t after:border-r after:border-gray-600 after:transition-all peer-placeholder-shown:text-sm peer-placeholder-shown:leading-[3.75] peer-placeholder-shown:text-gray-400 peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent peer-focus:text-[11px] peer-focus:leading-tight dark:peer-focus:text-sky-300 peer-focus:text-black peer-focus:before:border-t-2 peer-focus:before:border-l-2 dark:peer-focus:before:border-sky-300 peer-focus:before:border-black peer-focus:after:border-t-2 peer-focus:after:border-r-2 dark:peer-focus:after:border-sky-300 peer-focus:after:border-black peer-disabled:text-transparent peer-disabled:before:border-transparent peer-disabled:after:border-transparent peer-disabled:peer-placeholder-shown:text-gray-600">
            {{$label}}
        </label>
    </div>
@endif