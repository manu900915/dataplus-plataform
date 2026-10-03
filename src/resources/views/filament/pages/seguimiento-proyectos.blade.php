<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($this->getColumns() as $columnId => $column)
            <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">
                        {{ $column['title'] }}
                    </h3>
                    <x-filament::badge :color="$column['color']">
                        {{ $column['proyectos']->count() }}
                    </x-filament::badge>
                </div>

                <div class="space-y-3">
                    @foreach($column['proyectos'] as $proyecto)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-white">
                                    {{ $proyecto->nombre }}
                                </h4>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $proyecto->codigo }}
                                </span>
                            </div>

                            @if($proyecto->cliente)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    👤 {{ $proyecto->cliente->nombre }}
                                </p>
                            @endif

                            @if($proyecto->responsable)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    💻 {{ $proyecto->responsable->name }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between mt-3">
                                <x-filament::badge size="xs" :color="$column['color']">
                                    {{ ucfirst(str_replace('_', ' ', $proyecto->estado_kanban)) }}
                                </x-filament::badge>

                                <a 
                                    href="{{ \App\Filament\Resources\ProyectoResource::getUrl('edit', ['record' => $proyecto]) }}"
                                    class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                                >
                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                </a>
                            </div>

                            {{-- Select para cambiar estado --}}
                            <form action="{{ route('proyectos.kanban.update', $proyecto) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <select 
                                    name="estado_kanban"
                                    onchange="this.form.submit()"
                                    class="text-xs w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                    <option value="por_hacer" {{ $proyecto->estado_kanban === 'por_hacer' ? 'selected' : '' }}>
                                         Por Hacer
                                    </option>
                                    <option value="en_progreso" {{ $proyecto->estado_kanban === 'en_progreso' ? 'selected' : '' }}>
                                        🔄 En Progreso
                                    </option>
                                    <option value="en_revision" {{ $proyecto->estado_kanban === 'en_revision' ? 'selected' : '' }}>
                                        🔍 En Revisión
                                    </option>
                                    <option value="completado" {{ $proyecto->estado_kanban === 'completado' ? 'selected' : '' }}>
                                        ✅ Completado
                                    </option>
                                </select>
                            </form>
                        </div>
                    @endforeach

                    @if($column['proyectos']->count() === 0)
                        <div class="text-center text-gray-500 dark:text-gray-400 text-sm py-8">
                            Sin proyectos
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>