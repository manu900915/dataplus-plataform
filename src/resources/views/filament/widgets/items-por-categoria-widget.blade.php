<x-filament-widgets::widget>
    <x-filament::section>
        <div x-data="{ expanded: true }">
            <!-- Header clickable -->
            <div 
                @click="expanded = !expanded"
                class="flex items-center justify-between mb-4 cursor-pointer select-none"
            >
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900">
                        <x-heroicon-o-rectangle-stack class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                            Items por Categoría
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Click en una categoría para ver sus items
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <x-filament::badge color="primary" size="lg">
                        {{ $this->getCategoriasConItems()->sum('items_count') }} items totales
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
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($this->getCategoriasConItems() as $categoria)
                        <a 
                            href="{{ $categoria['url'] }}"
                            class="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm dark:bg-gray-900 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-primary-500 dark:hover:border-primary-500 transition-all cursor-pointer group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg 
                                    @if($categoria['tipo'] === 'equipamiento') bg-blue-100 dark:bg-blue-900
                                    @elseif($categoria['tipo'] === 'material') bg-green-100 dark:bg-green-900
                                    @else bg-purple-100 dark:bg-purple-900
                                    @endif">
                                    @if($categoria['tipo'] === 'equipamiento')
                                        <x-heroicon-o-cube class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    @elseif($categoria['tipo'] === 'material')
                                        <x-heroicon-o-archive-box class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    @else
                                        <x-heroicon-o-rectangle-stack class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-950 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ $categoria['nombre'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($categoria['tipo']) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $categoria['items_count'] }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $categoria['items_count'] === 1 ? 'item' : 'items' }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>