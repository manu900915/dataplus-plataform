<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{ expanded: true }">
            <!-- Header clickable -->
            <div 
                @click="expanded = !expanded"
                class="flex items-center justify-between mb-4 cursor-pointer select-none"
            >
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900">
                        <x-heroicon-o-archive-box class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                            Items por Almacén
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Click en un almacén para ver sus movimientos
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <x-filament::badge color="warning" size="lg">
                        {{ $this->getAlmacenesConItems()->count() }} almacenes
                    </x-filament::badge>
                    
                    <svg 
                        x-bind:class="expanded ? 'rotate-180' : ''"
                        class="w-6 h-6 text-gray-500 transition-transform"
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke-width="2" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>

            <!-- Expanded content -->
            <div 
                x-show="expanded"
                x-collapse
                style="display: none;"
            >
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
                    @foreach($this->getAlmacenesConItems() as $almacen)
                        <a 
                            href="{{ $almacen['url'] }}"
                            class="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-orange-500 dark:hover:border-orange-500 transition-all cursor-pointer group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-orange-100 dark:bg-orange-900">
                                    <x-heroicon-o-archive-box class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400">
                                        {{ $almacen['nombre'] }}
                                    </h3>
                                    @if($almacen['ubicacion'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $almacen['ubicacion'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <span class="text-xl font-bold text-orange-600 dark:text-orange-400">
                                    {{ $almacen['items_count'] }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $almacen['items_count'] === 1 ? 'item' : 'items' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>