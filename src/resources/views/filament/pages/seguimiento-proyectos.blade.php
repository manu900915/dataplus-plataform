<x-filament-panels::page>
    {{-- Selector de filtro de tipo de seguimiento --}}
    @php
        $counts = $this->getCounts();
    @endphp
    <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-2">
            <button 
                type="button"
                wire:click="setFiltro('todos')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-2 cursor-pointer {{ $filtroTipo === 'todos' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
            >
                <span>Todos los Proyectos</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filtroTipo === 'todos' ? 'bg-primary-800 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                    {{ $counts['todos'] }}
                </span>
            </button>

            <button 
                type="button"
                wire:click="setFiltro('instalacion')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-2 cursor-pointer {{ $filtroTipo === 'instalacion' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
            >
                <span>📦 Instalaciones & Obras</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filtroTipo === 'instalacion' ? 'bg-primary-800 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                    {{ $counts['instalacion'] }}
                </span>
            </button>

            <button 
                type="button"
                wire:click="setFiltro('investigacion')"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors flex items-center gap-2 cursor-pointer {{ $filtroTipo === 'investigacion' ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}"
            >
                <span>🔬 I+D Especiales</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $filtroTipo === 'investigacion' ? 'bg-primary-800 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                    {{ $counts['investigacion'] }}
                </span>
            </button>
        </div>

        <a 
            href="{{ \App\Filament\Resources\ProyectoResource::getUrl('create') }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-colors"
        >
            <x-heroicon-m-plus class="w-4 h-4" />
            <span>Nuevo Proyecto</span>
        </a>
    </div>

    {{-- Columnas Kanban --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-2">
        @foreach($this->getColumns() as $columnId => $column)
            <div class="bg-gray-100/80 dark:bg-gray-900/80 rounded-xl p-3.5 border border-gray-200 dark:border-gray-800 flex flex-col min-h-[500px]">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-1.5">
                        {{ $column['title'] }}
                    </h3>
                    <x-filament::badge :color="$column['color']" size="sm">
                        {{ $column['proyectos']->count() }}
                    </x-filament::badge>
                </div>

                <div class="space-y-3 flex-1 overflow-y-auto">
                    @foreach($column['proyectos'] as $proyecto)
                        <div class="bg-white dark:bg-gray-800/90 rounded-lg p-3.5 shadow-sm border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 transition-all">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <span class="font-mono text-xs font-bold text-primary-600 dark:text-primary-400">
                                    {{ $proyecto->codigo }}
                                </span>
                                <div class="flex items-center gap-1">
                                    @if($proyecto->tipo_seguimiento === 'investigacion')
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                                            I+D
                                        </span>
                                    @else
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                            Instalación
                                        </span>
                                    @endif

                                    <a 
                                        href="{{ \App\Filament\Resources\ProyectoResource::getUrl('edit', ['record' => $proyecto]) }}"
                                        class="p-1 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                        title="Editar proyecto"
                                    >
                                        <x-heroicon-o-pencil-square class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>

                            <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-2 leading-snug">
                                {{ $proyecto->nombre }}
                            </h4>

                            @if($proyecto->cliente)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1 flex items-center gap-1 truncate">
                                    <span class="text-gray-400">👤</span>
                                    <span class="truncate">{{ $proyecto->cliente->nombre }}</span>
                                </p>
                            @endif

                            @if($proyecto->responsable)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1 flex items-center gap-1 truncate">
                                    <span class="text-gray-400">💻</span>
                                    <span class="truncate">{{ $proyecto->responsable->name }}</span>
                                </p>
                            @endif

                            @if($proyecto->presupuesto_total > 0)
                                <div class="text-xs font-mono font-semibold text-emerald-600 dark:text-emerald-400 mt-2 mb-2">
                                    Presupuesto: ${{ number_format($proyecto->presupuesto_total, 2) }}
                                </div>
                            @endif

                            {{-- Select para cambiar fase de seguimiento --}}
                            <form action="{{ route('proyectos.kanban.update', $proyecto) }}" method="POST" class="mt-2.5 pt-2 border-t border-gray-100 dark:border-gray-700">
                                @csrf
                                @method('PATCH')
                                <label class="block text-[10px] text-gray-500 dark:text-gray-400 font-medium mb-1">
                                    Mover a fase:
                                </label>
                                <select 
                                    name="estado_kanban"
                                    onchange="this.form.submit()"
                                    class="text-xs w-full py-1 px-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                >
                                    <option value="por_hacer" {{ ($proyecto->estado_kanban === 'por_hacer' || (empty($proyecto->estado_kanban) && $proyecto->estado === 'borrador')) ? 'selected' : '' }}>
                                        📋 Por Hacer
                                    </option>
                                    <option value="en_progreso" {{ ($proyecto->estado_kanban === 'en_progreso' || (empty($proyecto->estado_kanban) && $proyecto->estado === 'en_progreso')) ? 'selected' : '' }}>
                                        🔄 En Progreso
                                    </option>
                                    <option value="en_revision" {{ $proyecto->estado_kanban === 'en_revision' ? 'selected' : '' }}>
                                        🔍 En Revisión
                                    </option>
                                    <option value="completado" {{ ($proyecto->estado_kanban === 'completado' || $proyecto->estado === 'completado') ? 'selected' : '' }}>
                                        ✅ Completado
                                    </option>
                                </select>
                            </form>
                        </div>
                    @endforeach

                    @if($column['proyectos']->count() === 0)
                        <div class="h-32 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-lg flex items-center justify-center text-xs text-gray-400 dark:text-gray-500">
                            Sin proyectos en esta fase
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
