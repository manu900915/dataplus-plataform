<x-filament-panels::page>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <div x-data="{ activeTab: 'proyectos' }" class="space-y-6">
        {{-- Tabs Navigation --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2">
            <nav class="flex space-x-1 overflow-x-auto" aria-label="Tabs">
                @php
                    $tabs = [
                        'proyectos' => ['icon' => 'heroicon-o-briefcase', 'label' => 'Proyectos'],
                        'operaciones' => ['icon' => 'heroicon-o-cpu-chip', 'label' => 'Operaciones'],
                        'inventario' => ['icon' => 'heroicon-o-archive-box', 'label' => 'Inventario'],
                        'finanzas' => ['icon' => 'heroicon-o-currency-dollar', 'label' => 'Finanzas'],
                        'administracion' => ['icon' => 'heroicon-o-cog', 'label' => 'Administración'],
                    ];
                @endphp

                @foreach($tabs as $key => $tab)
                    <button 
                        @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'bg-primary-500 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm transition-all flex-shrink-0"
                    >
                        <x-dynamic-component :component="$tab['icon']" class="w-5 h-5" />
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-6">
            {{-- PROYECTOS --}}
            <div x-show="activeTab === 'proyectos'" x-cloak x-transition>
                {{-- Stats --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Proyectos Activos</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Proyecto::whereIn('estado', ['borrador', 'en_progreso'])->count() }}</p>
                            </div>
                            <x-heroicon-o-briefcase class="w-10 h-10 text-blue-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Completados este Mes</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Proyecto::where('estado', 'completado')->whereMonth('created_at', now()->month)->count() }}</p>
                            </div>
                            <x-heroicon-o-check-circle class="w-10 h-10 text-green-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Con Retraso</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Proyecto::where('estado', 'en_progreso')->where('fecha_fin', '<', now())->count() }}</p>
                            </div>
                            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Presupuesto Total</p>
                                <p class="text-2xl font-bold mt-1">${{ number_format(\App\Models\Proyecto::sum('presupuesto_total'), 2) }}</p>
                            </div>
                            <x-heroicon-o-currency-dollar class="w-10 h-10 text-amber-200" />
                        </div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\ProyectosChart::class)
                    @livewire(\App\Filament\Widgets\FinanzasChart::class)
                </div>
            </div>

            {{-- OPERACIONES --}}
            <div x-show="activeTab === 'operaciones'" x-cloak x-transition>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 mb-6">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Brigadas Activas</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Brigada::where('activa', true)->count() }}</p>
                            </div>
                            <x-heroicon-o-user-group class="w-10 h-10 text-purple-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-indigo-100 text-sm font-medium">Proyectos I+D</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Proyecto::where('tipo_seguimiento', 'investigacion')->whereIn('estado', ['borrador', 'en_progreso'])->count() }}</p>
                            </div>
                            <x-heroicon-o-queue-list class="w-10 h-10 text-indigo-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Instalaciones</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Proyecto::where('tipo_seguimiento', 'instalacion')->whereIn('estado', ['borrador', 'en_progreso'])->count() }}</p>
                            </div>
                            <x-heroicon-o-wrench-screwdriver class="w-10 h-10 text-emerald-200" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\OperacionesChart::class)
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Actividad Reciente</h3>
                        <div class="space-y-3">
                            @foreach(\App\Models\Proyecto::latest()->take(5)->get() as $proyecto)
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $proyecto->nombre }}</p>
                                        <p class="text-xs text-gray-500">{{ $proyecto->created_at->diffForHumans() }}</p>
                                    </div>
                                    <x-filament::badge size="xs" :color="match($proyecto->estado) { 'completado' => 'success', 'en_progreso' => 'info', 'cancelado' => 'danger', default => 'gray' }">
                                        {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
                                    </x-filament::badge>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- INVENTARIO --}}
            <div x-show="activeTab === 'inventario'" x-cloak x-transition>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Items</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Item::count() }}</p>
                            </div>
                            <x-heroicon-o-archive-box class="w-10 h-10 text-blue-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Stock Bajo</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Item::whereColumn('stock_actual', '<=', 'stock_minimo')->count() }}</p>
                            </div>
                            <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-red-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Equipamiento</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Item::where('es_equipamiento', true)->sum('stock_actual') }}</p>
                            </div>
                            <x-heroicon-o-cube class="w-10 h-10 text-purple-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Materiales</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Item::where('es_equipamiento', false)->sum('stock_actual') }}</p>
                            </div>
                            <x-heroicon-o-archive-box class="w-10 h-10 text-green-200" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @livewire(\App\Filament\Widgets\InventarioChart::class)
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 lg:col-span-2">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">⚠️ Alertas de Stock Bajo</h3>
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @forelse(\App\Models\Item::whereColumn('stock_actual', '<=', 'stock_minimo')->take(10)->get() as $item)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->nombre }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->codigo }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ $item->stock_actual }} {{ $item->unidad_medida }}</p>
                                        <p class="text-xs text-gray-500">Mín: {{ $item->stock_minimo }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">✅ No hay alertas de stock</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- FINANZAS --}}
            <div x-show="activeTab === 'finanzas'" x-cloak x-transition>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Presupuesto Total</p>
                                <p class="text-2xl font-bold mt-1">${{ number_format(\App\Models\Proyecto::sum('presupuesto_total'), 2) }}</p>
                            </div>
                            <x-heroicon-o-currency-dollar class="w-10 h-10 text-blue-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Este Mes</p>
                                <p class="text-2xl font-bold mt-1">${{ number_format(\App\Models\Proyecto::whereMonth('created_at', now()->month)->sum('presupuesto_total'), 2) }}</p>
                            </div>
                            <x-heroicon-o-calendar class="w-10 h-10 text-green-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Equipamiento</p>
                                <p class="text-2xl font-bold mt-1">${{ number_format(\App\Models\LineaPresupuesto::where('tipo_linea', 'equipamiento')->sum('subtotal'), 2) }}</p>
                            </div>
                            <x-heroicon-o-cube class="w-10 h-10 text-purple-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-amber-100 text-sm font-medium">Mano de Obra</p>
                                <p class="text-2xl font-bold mt-1">${{ number_format(\App\Models\LineaPresupuesto::where('tipo_linea', 'mano_obra')->sum('subtotal'), 2) }}</p>
                            </div>
                            <x-heroicon-o-user-group class="w-10 h-10 text-amber-200" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @livewire(\App\Filament\Widgets\FinanzasChart::class)
                    <div class="bg-white dark:bg-gray-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top 5 Proyectos por Presupuesto</h3>
                        <div class="space-y-3">
                            @foreach(\App\Models\Proyecto::orderByDesc('presupuesto_total')->take(5)->get() as $i => $proyecto)
                                @php $max = \App\Models\Proyecto::max('presupuesto_total') ?: 1; @endphp
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $proyecto->nombre }}</span>
                                        <span class="text-sm font-bold text-primary-600">${{ number_format($proyecto->presupuesto_total, 2) }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-primary-500 to-primary-700 h-2 rounded-full" style="width: {{ ($proyecto->presupuesto_total / $max) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ADMINISTRACIÓN --}}
            <div x-show="activeTab === 'administracion'" x-cloak x-transition>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Usuarios Activos</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\User::where('activo', true)->count() }}</p>
                            </div>
                            <x-heroicon-o-users class="w-10 h-10 text-blue-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Clientes</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Cliente::count() }}</p>
                            </div>
                            <x-heroicon-o-building-office-2 class="w-10 h-10 text-green-200" />
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Clientes Activos</p>
                                <p class="text-3xl font-bold mt-1">{{ \App\Models\Cliente::where('activo', true)->count() }}</p>
                            </div>
                            <x-heroicon-o-check-circle class="w-10 h-10 text-purple-200" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>