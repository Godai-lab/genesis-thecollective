@props(['thead','tbody','data','route', 'buttons', 'defaultButtons', 'createButton'])

@if ($thead && $tbody)
    <div class="relative border border-black rounded-lg w-full block overflow-x-auto">
        <table class="table-auto w-full border-collapse">
            <thead>
                <tr> 
                    <th scope="col" class="border-b border-black text-left py-3 pl-6 pr-3 bg-black text-white">#</th>
                    @foreach ($thead as $key => $th)
                        <th scope="col" class="border-b border-black text-left py-3 pl-6 pr-3 bg-black text-white">{{$th}}</th>
                    @endforeach
                    <th scope="col" class="border-b border-black text-left py-3 pl-6 pr-3 bg-black text-white">
                        @if((!isset($defaultButtons)) || (isset($defaultButtons) && $defaultButtons === true))
                            @if(Route::has($route.'.create') && Gate::check('haveaccess', $route.'.create'))
                                <x-dynamic-button-link :type="'add'" :action="route($route.'.create')" />
                            @endif
                        @endif
                        @if (isset($createButton))
                            @php
                                if (isset($createButton['params'])) {
                                    $routeParams = [];
                                    
                                    foreach ($createButton['params'] as $paramName => $fieldName) {
                                        $routeParams[$paramName] = $fieldName;
                                    }
                                    $action = route($createButton['route'], $routeParams);
                                } else {
                                    $action = route($createButton['route']);
                                }
                            @endphp
                            <x-dynamic-button-link :type="$createButton['type']" :action="$action" />
                        @endif
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $row)
                    <tr>
                        @can('haveaccess',$route.'.show')
                        <td class="text-left py-3 pl-6 pr-2 border-b border-black">{{ $row->id }}</td>
                        @else
                        <td class="text-left py-3 pl-6 pr-2 border-b border-black">{{ ($key+1)+(($data->currentPage()-1)*$data->perPage()) }}</td>
                        @endcan
                        @foreach ($tbody as $key => $td)
                            <td class="text-left py-3 pl-6 pr-2 border-b border-black">
                                @if($td == 'status' || $td == 'full_access')
                                    <x-dynamic-table-switch :id="$row->id" :status="$row[$td]" />
                                @elseif(is_object($row[$td]) && $td != 'created_at' && $td != 'updated_at')
                                    @foreach ($row[$td] as $key => $filaobj)
                                        <span class="bg-black text-white px-2 py-1 rounded-md text-xs whitespace-nowrap">{{$filaobj->name}}</span>
                                    @endforeach
                                @elseif($td == 'image')
                                    @if($row[$td])
                                        <img class="mx-auto h-12 w-auto" src="{{ asset("image/{$row[$td]}") }}" alt="{{$row[$td]}}">
                                    @else
                                        <x-icon-image />
                                    @endif
                                @else
                                    @php
                                        $rowaux = $row;
                                        foreach ($arrayrow = explode('.', $td) as $key => $elem) {
                                            $rowaux = $rowaux[$elem];
                                        }
                                        echo $rowaux;
                                    @endphp
                                @endif
                            </td>
                        @endforeach
                        <td class="text-left py-3 pl-6 pr-3 border-b border-black">
                            @if((!isset($defaultButtons)) || (isset($defaultButtons) && $defaultButtons === true))
                                @if(Route::has($route.'.edit') && Gate::check('haveaccess', $route.'.edit'))
                                    <x-dynamic-button-link :type="'edit'" :action="route($route.'.edit',$row->id)" />
                                @endif
                                @if(Route::has($route.'.destroy') && Gate::check('haveaccess', $route.'.destroy'))
                                    <x-dynamic-button-link :type="'delete'" :action="route($route.'.destroy', $row->id)" />
                                @endif
                            @endif
                            @if (isset($buttons) && count($buttons) > 0)
                                @foreach ($buttons as $button)
                                    @php
                                        // Validar si hay parámetros especificados y generar la acción apropiada
                                        if (isset($button['params'])) {
                                            $routeParams = [];
                                            
                                            foreach ($button['params'] as $paramName => $fieldName) {
                                                $routeParams[$paramName] = $row->$fieldName;
                                            }
                                            $action = route($button['route'], $routeParams);
                                        } else {
                                            $action = route($button['route']);
                                        }
                                    @endphp
                                    <x-dynamic-button-link :type="$button['type']" :action="$action" :class="(isset($button['class']))?$button['class']:''" :icon="(isset($button['icon']))?$button['icon']:''"/>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-2 "> 
            {{$data->withQueryString()->links()}}
        </div>
    </div>
@endif