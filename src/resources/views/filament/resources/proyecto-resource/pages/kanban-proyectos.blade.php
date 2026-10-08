<x-filament-panels::page>
    <style>
        .dp-kanban-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.25rem;
            align-items: start;
            width: 100%;
        }
        @media (min-width: 640px) {
            .dp-kanban-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .dp-kanban-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }
        .dp-kanban-col {
            background: #0b1325 !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 14px !important;
            padding: 1rem !important;
            min-height: 480px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .dp-kanban-card {
            background: #131f37 !important;
            border: 1px solid rgba(255, 255, 255, 0.09) !important;
            border-radius: 12px !important;
            padding: 1rem !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: #f8fafc !important;
        }
        .dp-kanban-card:hover {
            border-color: rgba(0, 229, 163, 0.45) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5), 0 0 16px rgba(0, 229, 163, 0.1);
        }
    </style>

    <div class="dp-kanban-wrapper">
        {{-- Toolbar con Filtros y Botón de Acción --}}
        @php
            $counts = $this->getCounts();
        @endphp
        <div class="dp-kanban-toolbar">
            <div class="dp-kanban-filters">
                <button 
                    type="button"
                    wire:click="setFiltro('todos')"
                    class="dp-kanban-filter-btn {{ $filtroTipo === 'todos' ? 'active' : '' }}"
                >
                    <span>Todos los Proyectos</span>
                    <span class="dp-kanban-pill">
                        {{ $counts['todos'] }}
                    </span>
                </button>

                <button 
                    type="button"
                    wire:click="setFiltro('instalacion')"
                    class="dp-kanban-filter-btn {{ $filtroTipo === 'instalacion' ? 'active' : '' }}"
                >
                    <span>📦 Instalaciones & Obras</span>
                    <span class="dp-kanban-pill">
                        {{ $counts['instalacion'] }}
                    </span>
                </button>

                <button 
                    type="button"
                    wire:click="setFiltro('investigacion')"
                    class="dp-kanban-filter-btn {{ $filtroTipo === 'investigacion' ? 'active' : '' }}"
                >
                    <span>🔬 I+D Especiales</span>
                    <span class="dp-kanban-pill">
                        {{ $counts['investigacion'] }}
                    </span>
                </button>
            </div>

            <a 
                href="{{ \App\Filament\Resources\ProyectoResource::getUrl('create') }}"
                class="dp-kanban-create-btn"
            >
                <x-heroicon-m-plus class="w-4 h-4" />
                <span>Nuevo Proyecto</span>
            </a>
        </div>

        {{-- Tablero Kanban Grid --}}
        <div class="dp-kanban-grid">
            @foreach($this->getColumns() as $columnId => $column)
                @php
                    $headerColors = [
                        'por_hacer' => ['border' => '#64748b', 'badge_bg' => 'rgba(100, 116, 139, 0.2)', 'badge_color' => '#94a3b8'],
                        'en_progreso' => ['border' => '#f59e0b', 'badge_bg' => 'rgba(245, 158, 11, 0.2)', 'badge_color' => '#fbbf24'],
                        'en_revision' => ['border' => '#38bdf8', 'badge_bg' => 'rgba(56, 189, 248, 0.2)', 'badge_color' => '#38bdf8'],
                        'completado' => ['border' => '#10b981', 'badge_bg' => 'rgba(16, 185, 129, 0.2)', 'badge_color' => '#34d399'],
                    ];
                    $hInfo = $headerColors[$columnId] ?? ['border' => '#64748b', 'badge_bg' => 'rgba(255,255,255,0.08)', 'badge_color' => '#94a3b8'];
                @endphp

                <div class="dp-kanban-col" style="border-top: 3px solid {{ $hInfo['border'] }} !important;">
                    <div class="dp-kanban-col-header">
                        <div class="dp-kanban-col-title">
                            <span style="font-size: 1.05rem;">
                                @if($columnId === 'por_hacer') 📋
                                @elseif($columnId === 'en_progreso') 🔄
                                @elseif($columnId === 'en_revision') 🔍
                                @elseif($columnId === 'completado') ✅
                                @endif
                            </span>
                            <span>
                                @if($columnId === 'por_hacer') Por Hacer
                                @elseif($columnId === 'en_progreso') En Progreso
                                @elseif($columnId === 'en_revision') En Revisión
                                @elseif($columnId === 'completado') Completado
                                @endif
                            </span>
                        </div>
                        <span class="dp-kanban-col-badge" style="background: {{ $hInfo['badge_bg'] }} !important; color: {{ $hInfo['badge_color'] }} !important;">
                            {{ $column['proyectos']->count() }}
                        </span>
                    </div>

                    <div class="dp-kanban-cards">
                        @foreach($column['proyectos'] as $proyecto)
                            <div class="dp-kanban-card">
                                {{-- Cabecera de la tarjeta --}}
                                <div class="dp-kanban-card-top">
                                    <span class="dp-kanban-card-code">
                                        {{ $proyecto->codigo }}
                                    </span>
                                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                                        @if($proyecto->tipo_seguimiento === 'investigacion')
                                            <span class="dp-kanban-tag investigacion">I+D</span>
                                        @else
                                            <span class="dp-kanban-tag instalacion">Instalación</span>
                                        @endif

                                        <a 
                                            href="{{ \App\Filament\Resources\ProyectoResource::getUrl('edit', ['record' => $proyecto]) }}"
                                            style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 6px; background: rgba(255,255,255,0.06); color: #94a3b8; transition: color 0.15s;"
                                            title="Editar proyecto"
                                            onmouseover="this.style.color='#00e5a3'; this.style.background='rgba(0,229,163,0.12)'"
                                            onmouseout="this.style.color='#94a3b8'; this.style.background='rgba(255,255,255,0.06)'"
                                        >
                                            <x-heroicon-m-pencil-square class="w-3.5 h-3.5" />
                                        </a>
                                    </div>
                                </div>

                                {{-- Nombre del Proyecto --}}
                                <div class="dp-kanban-card-title">
                                    {{ $proyecto->nombre }}
                                </div>

                                {{-- Metadatos: Cliente y Responsable --}}
                                <div class="dp-kanban-card-meta">
                                    @if($proyecto->cliente)
                                        <div class="dp-kanban-card-meta-row" title="Cliente: {{ $proyecto->cliente->nombre }}">
                                            <span style="opacity: 0.7;">🏢</span>
                                            <span>{{ $proyecto->cliente->nombre }}</span>
                                        </div>
                                    @endif

                                    @if($proyecto->responsable)
                                        <div class="dp-kanban-card-meta-row" title="Responsable: {{ $proyecto->responsable->name }}">
                                            <span style="opacity: 0.7;">👤</span>
                                            <span>{{ $proyecto->responsable->name }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Presupuesto si existe --}}
                                @if($proyecto->presupuesto_total > 0)
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem; margin-top: 0.35rem; padding-top: 0.35rem; border-top: 1px dashed rgba(255,255,255,0.06);">
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
                                <div class="dp-kanban-progress-track">
                                    <div class="dp-kanban-progress-bar" style="width: {{ $progress }}%; background: {{ $progressGrad }};"></div>
                                </div>

                                {{-- Selector de Fase --}}
                                <form action="{{ route('proyectos.kanban.update', $proyecto) }}" method="POST" class="dp-kanban-card-footer">
                                    @csrf
                                    @method('PATCH')
                                    <label class="dp-kanban-select-label">
                                        Mover a fase
                                    </label>
                                    <select 
                                        name="estado_kanban"
                                        onchange="this.form.submit()"
                                        class="dp-kanban-select"
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
                            <div class="dp-kanban-empty">
                                <span style="font-size: 1.25rem; opacity: 0.5; margin-bottom: 0.35rem;">📦</span>
                                <span>Sin proyectos en esta fase</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
