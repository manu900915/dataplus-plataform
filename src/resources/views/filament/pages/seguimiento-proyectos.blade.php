<x-filament-panels::page>
    <style>
        .dp-kanban-root {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .dp-kanban-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .dp-kanban-tabs-group {
            display: inline-flex;
            align-items: center;
            background: #0c1424;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 4px;
            gap: 4px;
        }
        .dp-kanban-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 16px;
            font-size: 13px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            font-weight: 500;
            background: transparent;
            color: #94a3b8;
        }
        .dp-kanban-tab:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }
        .dp-kanban-tab.active {
            background: #00e5a3 !important;
            color: #02241b !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 10px rgba(0, 229, 163, 0.3) !important;
        }
        .dp-kanban-tab-counter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 800;
            border-radius: 999px;
            line-height: 1;
        }
        .dp-kanban-tab.active .dp-kanban-tab-counter {
            background: rgba(2, 36, 27, 0.22);
            color: #02241b;
        }
        .dp-kanban-tab:not(.active) .dp-kanban-tab-counter {
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }
        .dp-kanban-btn-create {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            background: linear-gradient(135deg, #00e5a3 0%, #059669 100%) !important;
            color: #022c22 !important;
            text-decoration: none !important;
            box-shadow: 0 4px 14px rgba(0, 229, 163, 0.25);
            transition: all 0.15s;
        }
        .dp-kanban-btn-create:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 229, 163, 0.35);
            color: #022c22 !important;
        }
        .dp-kanban-layout {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1.25rem;
            align-items: start;
            width: 100%;
        }
        @media (max-width: 1100px) {
            .dp-kanban-layout {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .dp-kanban-layout {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
        .dp-kanban-col {
            background: #0b1325 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 14px !important;
            padding: 1.1rem !important;
            min-height: 520px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
        }
        .dp-kanban-card {
            background: #131f37 !important;
            border: 1px solid rgba(255, 255, 255, 0.09) !important;
            border-radius: 12px !important;
            padding: 1rem !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: #f8fafc !important;
        }
        .dp-kanban-card:hover {
            border-color: rgba(0, 229, 163, 0.45) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5), 0 0 16px rgba(0, 229, 163, 0.1);
        }
    </style>

    <div class="dp-kanban-root">
        @php
            $counts = $this->getCounts();
        @endphp

        {{-- Toolbar con Selector de Filtros y Botón Nuevo Proyecto --}}
        <div class="dp-kanban-toolbar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="dp-kanban-tabs-group" style="display: inline-flex; align-items: center; background: #0c1424; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 4px; gap: 4px;">
                <button 
                    type="button"
                    wire:click="setFiltro('todos')"
                    class="dp-kanban-tab {{ $filtroTipo === 'todos' ? 'active' : '' }}"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px; font-size: 13px; font-weight: {{ $filtroTipo === 'todos' ? '700' : '500' }}; border-radius: 8px; border: none; cursor: pointer; background: {{ $filtroTipo === 'todos' ? '#00e5a3' : 'transparent' }}; color: {{ $filtroTipo === 'todos' ? '#02241b' : '#94a3b8' }}; box-shadow: {{ $filtroTipo === 'todos' ? '0 2px 10px rgba(0,229,163,0.3)' : 'none' }};"
                >
                    <span>Todos los Proyectos</span>
                    <span class="dp-kanban-tab-counter" style="display: inline-flex; align-items: center; justify-content: center; padding: 2px 8px; font-size: 11px; font-weight: 800; border-radius: 999px; background: {{ $filtroTipo === 'todos' ? 'rgba(2, 36, 27, 0.22)' : 'rgba(255, 255, 255, 0.08)' }}; color: {{ $filtroTipo === 'todos' ? '#02241b' : '#94a3b8' }};">
                        {{ $counts['todos'] }}
                    </span>
                </button>

                <button 
                    type="button"
                    wire:click="setFiltro('instalacion')"
                    class="dp-kanban-tab {{ $filtroTipo === 'instalacion' ? 'active' : '' }}"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px; font-size: 13px; font-weight: {{ $filtroTipo === 'instalacion' ? '700' : '500' }}; border-radius: 8px; border: none; cursor: pointer; background: {{ $filtroTipo === 'instalacion' ? '#00e5a3' : 'transparent' }}; color: {{ $filtroTipo === 'instalacion' ? '#02241b' : '#94a3b8' }}; box-shadow: {{ $filtroTipo === 'instalacion' ? '0 2px 10px rgba(0,229,163,0.3)' : 'none' }};"
                >
                    <span>📦 Instalaciones & Obras</span>
                    <span class="dp-kanban-tab-counter" style="display: inline-flex; align-items: center; justify-content: center; padding: 2px 8px; font-size: 11px; font-weight: 800; border-radius: 999px; background: {{ $filtroTipo === 'instalacion' ? 'rgba(2, 36, 27, 0.22)' : 'rgba(255, 255, 255, 0.08)' }}; color: {{ $filtroTipo === 'instalacion' ? '#02241b' : '#94a3b8' }};">
                        {{ $counts['instalacion'] }}
                    </span>
                </button>

                <button 
                    type="button"
                    wire:click="setFiltro('investigacion')"
                    class="dp-kanban-tab {{ $filtroTipo === 'investigacion' ? 'active' : '' }}"
                    style="display: inline-flex; align-items: center; gap: 8px; padding: 7px 16px; font-size: 13px; font-weight: {{ $filtroTipo === 'investigacion' ? '700' : '500' }}; border-radius: 8px; border: none; cursor: pointer; background: {{ $filtroTipo === 'investigacion' ? '#00e5a3' : 'transparent' }}; color: {{ $filtroTipo === 'investigacion' ? '#02241b' : '#94a3b8' }}; box-shadow: {{ $filtroTipo === 'investigacion' ? '0 2px 10px rgba(0,229,163,0.3)' : 'none' }};"
                >
                    <span>🔬 I+D Especiales</span>
                    <span class="dp-kanban-tab-counter" style="display: inline-flex; align-items: center; justify-content: center; padding: 2px 8px; font-size: 11px; font-weight: 800; border-radius: 999px; background: {{ $filtroTipo === 'investigacion' ? 'rgba(2, 36, 27, 0.22)' : 'rgba(255, 255, 255, 0.08)' }}; color: {{ $filtroTipo === 'investigacion' ? '#02241b' : '#94a3b8' }};">
                        {{ $counts['investigacion'] }}
                    </span>
                </button>
            </div>

            <a 
                href="{{ \App\Filament\Resources\ProyectoResource::getUrl('create') }}"
                class="dp-kanban-btn-create"
                style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; background: linear-gradient(135deg, #00e5a3 0%, #059669 100%); color: #022c22; text-decoration: none; box-shadow: 0 4px 14px rgba(0, 229, 163, 0.25);"
            >
                <svg style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Nuevo Proyecto</span>
            </a>
        </div>

        {{-- Tablero Kanban Grid (4 columnas horizontales) --}}
        <div class="dp-kanban-layout">
            @foreach($this->getColumns() as $columnId => $column)
                @php
                    $headerColors = [
                        'por_hacer' => ['border' => '#64748b', 'badge_bg' => 'rgba(100, 116, 139, 0.25)', 'badge_color' => '#94a3b8', 'icon' => '📋', 'title' => 'Por Hacer'],
                        'en_progreso' => ['border' => '#f59e0b', 'badge_bg' => 'rgba(245, 158, 11, 0.25)', 'badge_color' => '#fbbf24', 'icon' => '🔄', 'title' => 'En Progreso'],
                        'en_revision' => ['border' => '#38bdf8', 'badge_bg' => 'rgba(56, 189, 248, 0.25)', 'badge_color' => '#38bdf8', 'icon' => '🔍', 'title' => 'En Revisión'],
                        'completado' => ['border' => '#10b981', 'badge_bg' => 'rgba(16, 185, 129, 0.25)', 'badge_color' => '#34d399', 'icon' => '✅', 'title' => 'Completado'],
                    ];
                    $hInfo = $headerColors[$columnId] ?? ['border' => '#64748b', 'badge_bg' => 'rgba(255,255,255,0.08)', 'badge_color' => '#94a3b8', 'icon' => '📋', 'title' => $column['title']];
                @endphp

                <div class="dp-kanban-col" style="border-top: 3.5px solid {{ $hInfo['border'] }} !important;">
                    {{-- Cabecera de Columna --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: #f1f5f9;">
                            <span style="font-size: 16px;">{{ $hInfo['icon'] }}</span>
                            <span>{{ $hInfo['title'] }}</span>
                        </div>
                        <span style="display: inline-flex; align-items: center; justify-content: center; padding: 2px 9px; border-radius: 999px; font-size: 11px; font-weight: 800; font-family: monospace; background: {{ $hInfo['badge_bg'] }}; color: {{ $hInfo['badge_color'] }}; border: 1px solid rgba(255,255,255,0.06);">
                            {{ $column['proyectos']->count() }}
                        </span>
                    </div>

                    {{-- Lista de Tarjetas --}}
                    <div style="display: flex; flex-direction: column; gap: 12px; flex: 1; overflow-y: auto;">
                        @foreach($column['proyectos'] as $proyecto)
                            <div class="dp-kanban-card">
                                {{-- Fila Superior: Código y Tag --}}
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-family: monospace; font-weight: 700; font-size: 12px; color: #00e5a3; letter-spacing: 0.02em;">
                                        {{ $proyecto->codigo }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        @if($proyecto->tipo_seguimiento === 'investigacion')
                                            <span style="display: inline-block; padding: 2px 7px; border-radius: 5px; font-size: 10px; font-weight: 700; background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);">I+D</span>
                                        @else
                                            <span style="display: inline-block; padding: 2px 7px; border-radius: 5px; font-size: 10px; font-weight: 700; background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">Instalación</span>
                                        @endif

                                        <a 
                                            href="{{ \App\Filament\Resources\ProyectoResource::getUrl('edit', ['record' => $proyecto]) }}"
                                            style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 6px; background: rgba(255,255,255,0.06); color: #94a3b8; text-decoration: none;"
                                            title="Editar proyecto"
                                        >
                                            <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Nombre del Proyecto --}}
                                <div style="font-size: 13.5px; font-weight: 700; color: #ffffff; line-height: 1.35; margin-bottom: 10px;">
                                    {{ $proyecto->nombre }}
                                </div>

                                {{-- Metadatos: Cliente y Responsable --}}
                                <div style="display: flex; flex-direction: column; gap: 4px; margin-bottom: 10px;">
                                    @if($proyecto->cliente)
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #cbd5e1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <span style="opacity: 0.7;">🏢</span>
                                            <span style="overflow: hidden; text-overflow: ellipsis;">{{ $proyecto->cliente->nombre }}</span>
                                        </div>
                                    @endif

                                    @if($proyecto->responsable)
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #94a3b8; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <span style="opacity: 0.7;">👤</span>
                                            <span style="overflow: hidden; text-overflow: ellipsis;">{{ $proyecto->responsable->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Presupuesto si existe --}}
                                @if($proyecto->presupuesto_total > 0)
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.06); margin-bottom: 8px;">
                                        <span style="color: #64748b; font-weight: 600;">Presupuesto:</span>
                                        <span style="color: #00e5a3; font-weight: 700; font-family: monospace;">
                                            ${{ number_format($proyecto->presupuesto_total, 2) }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Barra de progreso --}}
                                @php
                                    $progress = ($columnId === 'completado' || $proyecto->estado === 'completado') ? 100 : ($columnId === 'en_revision' ? 85 : ($columnId === 'en_progreso' ? 50 : 15));
                                    $progressGrad = ($columnId === 'completado' || $proyecto->estado === 'completado') ? 'linear-gradient(90deg, #059669, #00e5a3)' : 'linear-gradient(90deg, #0284c7, #00e5a3)';
                                @endphp
                                <div style="width: 100%; height: 4px; border-radius: 999px; background: #090e1a; overflow: hidden; margin-bottom: 10px;">
                                    <div style="height: 100%; width: {{ $progress }}%; background: {{ $progressGrad }}; border-radius: 999px;"></div>
                                </div>

                                {{-- Formulario Selector de Fase --}}
                                <form action="{{ route('proyectos.kanban.update', $proyecto) }}" method="POST" style="padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.06);">
                                    @csrf
                                    @method('PATCH')
                                    <label style="display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 4px;">
                                        Mover a fase
                                    </label>
                                    <select 
                                        name="estado_kanban"
                                        onchange="this.form.submit()"
                                        style="width: 100%; background: #090e1a; color: #f1f5f9; border: 1px solid rgba(255,255,255,0.14); border-radius: 6px; padding: 6px 10px; font-size: 11.5px; font-weight: 500; cursor: pointer; outline: none;"
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
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 36px 16px; border: 1.5px dashed rgba(255, 255, 255, 0.08); border-radius: 10px; color: #64748b; font-size: 12px; text-align: center; background: rgba(0, 0, 0, 0.15); gap: 8px;">
                                <span style="font-size: 22px; opacity: 0.6;">📦</span>
                                <span>Sin proyectos en esta fase</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
