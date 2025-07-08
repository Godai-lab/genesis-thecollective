<x-app-layout>
    <x-slot name="title">Génesis - Usuarios - Index</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usuario') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="block">
                        <form method="GET" action="{{route('user.index')}}">
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
                                    <div class="flex align-middle">
                                        <x-dynamic-button-link :type="'search'"  />
                                        <x-dynamic-button-link :type="'clean'" :action="route('user.index')" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="block p-3"></div>
                    <x-dynamic-table 
                        :thead="['Nombre','Usuario','Email', 'Rol','Estado','Fecha creación','Fecha actualización']" 
                        :tbody="['name','username','email', 'roles','status','created_at','updated_at']" 
                        :route="'user'" 
                        :data="$users" >
                    </x-dynamic-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>