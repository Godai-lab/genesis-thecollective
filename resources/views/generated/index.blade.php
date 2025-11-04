<x-app-layout>
    <x-slot name="title">Génesis - Generado - Index</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Generados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="block">
                        <form method="GET" action="{{route('generated.index')}}">
                            <div class="flex items-center flex-wrap justify-start gap-3">
                                <div class="w-72">
                                    <x-dynamic-select :id="'type'" :name="'type'" :selected="(isset($_GET['type']))?$_GET['type']:''" :label="'Tipo'" :list="$types" class=""  />
                                </div>
                                <div class="w-72">
                                    <x-dynamic-select :id="'account'" :name="'account'" :selected="(isset($_GET['account']))?$_GET['account']:''" :label="'Cuenta'" :list="$accounts" class=""  />
                                </div>
                                <div class="w-72">
                                    <x-dynamic-input :id="'search'" :name="'search'" :value="(isset($_GET['search']))?$_GET['search']:''" :type="'text'" :label="'Buscar'" class=""  />
                                </div>
                                <div class="w-72">
                                    <x-dynamic-input :id="'from'" :name="'from'" :value="(isset($_GET['from']))?$_GET['from']:''" :type="'date'" :label="'Desde'" class=""  />
                                </div>
                                <div class="w-72">
                                    <x-dynamic-input :id="'to'" :name="'to'" :value="(isset($_GET['to']))?$_GET['to']:''" :type="'date'" :label="'Hasta'" class=""  />
                                </div>
                                <div class="flex">
                                    <div class="flex align-middle gap-2">
                                        <x-dynamic-button-link :type="'search'"  />
                                        <x-dynamic-button-link :type="'clean'" :action="route('generated.index')" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block p-3"></div>
                    <x-dynamic-table 
                        :thead="['Tipo','Nombre','Rating','Cuenta','Fecha Inicio','Fecha Fin']" 
                        :tbody="['key','name', 'rating','account.name','created_at','updated_at']" 
                        :route="'generated'" 
                        :defaultButtons="false"
                        :createButton="['type' => 'add', 'route' => 'generated.create']"
                        :buttons="[
                            ['type' => 'edit', 'route' => 'generated.edit', 'params' => ['generated' => 'id'], 'conditions' => [['field' => 'status', 'operator' => '==', 'value' => 'completed']]],
                            ['type' => 'custom', 'icon' => 'fa-solid fa-download', 'class' => 'bg-gray-700 hover:bg-gray-900', 'route' => 'generated.download', 'params' => ['generated' => 'id'], 'conditions' => [['field' => 'status', 'operator' => '==', 'value' => 'completed']]],
                            ['type' => 'custom', 'icon' => 'fa-solid fa-play', 'class' => 'bg-gray-600 hover:bg-gray-500', 'route' => 'generated.continue', 'params' => ['generated' => 'id'], 'conditions' => [['field' => 'status', 'operator' => '!=', 'value' => 'completed'],['field' => 'metadata', 'operator' => 'not_empty']]],
                            ['type' => 'delete', 'route' => 'generated.destroy', 'params' => ['generated' => 'id']],
                        ]" 
                        :data="$generateds" >
                    </x-dynamic-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>