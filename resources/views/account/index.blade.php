<x-app-layout>
    <x-slot name="title">Génesis - Cuenta - Index</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="block">
                        <form method="GET" action="{{route('account.index')}}">
                            <div class="flex items-center flex-wrap justify-start gap-3">
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
                                        <x-dynamic-button-link :type="'clean'" :action="route('account.index')" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block p-3"></div>
                    <x-dynamic-table 
                        :thead="['Nombre','Categoria','Estado','Fecha creación','Fecha actualización']" 
                        :tbody="['name','category','status','created_at','updated_at']" 
                        :route="'account'"
                        :buttons="[
                            ['type' => 'config', 'route' => 'config.edit', 'params' => ['account' => 'id']],
                            ['type' => 'custom', 'icon' => 'fa-solid fa-file', 'class' => 'bg-gray-700 hover:bg-gray-900', 'route' => 'account.file.index', 'params' => ['account' => 'id']],
                            ['type' => 'custom', 'icon' => 'fa-solid fa-link', 'class' => 'bg-gray-700 hover:bg-gray-900', 'route' => 'account.site.index', 'params' => ['account' => 'id']]
                        ]" 
                        :data="$accounts" >
                    </x-dynamic-table>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>