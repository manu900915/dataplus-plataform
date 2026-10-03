<x-filament-panels::page>
    <style>
        .ldap-theme {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            color: #f1f5f9;
        }

        /* Banner Hero */
        .ldap-hero-card {
            background: linear-gradient(135deg, #0b1329 0%, #111e38 50%, #034466 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            margin-bottom: 1.5rem;
        }

        .ldap-hero-content {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            position: relative;
            z-index: 10;
        }
        @media (min-width: 768px) {
            .ldap-hero-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        /* Status Badge */
        .ldap-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .ldap-status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        .ldap-status-idle {
            background: rgba(14, 165, 233, 0.15);
            color: #7dd3fc;
            border: 1px solid rgba(14, 165, 233, 0.35);
        }
        .ldap-status-error {
            background: rgba(244, 63, 94, 0.15);
            color: #fda4af;
            border: 1px solid rgba(244, 63, 94, 0.35);
        }
        .ldap-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            display: inline-block;
        }

        /* Botones */
        .ldap-btn-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
        }
        .ldap-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.1rem;
            border-radius: 0.625rem;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .ldap-btn-secondary {
            background: #1e293b;
            color: #e2e8f0;
            border: 1px solid #334155;
        }
        .ldap-btn-secondary:hover {
            background: #334155;
            color: #ffffff;
        }
        .ldap-btn-sky {
            background: #0284c7;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        .ldap-btn-sky:hover {
            background: #0369a1;
        }
        .ldap-btn-emerald {
            background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
        }
        .ldap-btn-emerald:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        /* KPI Cards Grid */
        .ldap-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 1rem !important;
            margin-top: 1.5rem !important;
            padding-top: 1.25rem !important;
            border-top: 1px solid rgba(255, 255, 255, 0.12) !important;
        }
        @media (max-width: 900px) {
            .ldap-kpi-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 500px) {
            .ldap-kpi-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .ldap-kpi-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
        }
        .ldap-kpi-title {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }
        .ldap-kpi-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            margin-top: 0.25rem;
            letter-spacing: -0.02em;
        }

        /* Barra de Pestañas */
        .ldap-tabs-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 0.5rem;
        }
        .ldap-tab-btn {
            padding: 0.55rem 1.1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #94a3b8;
            transition: all 0.2s ease;
        }
        .ldap-tab-btn:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.05);
        }
        .ldap-tab-active {
            background: #0284c7 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        /* Explorador de Cuentas: Header */
        .ldap-card-header {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        @media (min-width: 640px) {
            .ldap-card-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
        .ldap-card-header h3 {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
            color: #f9fafb;
        }
        .ldap-card-header p {
            margin: 0.25rem 0 0 0;
            font-size: 0.75rem;
            color: #9ca3af;
        }

        /* Tabla del Directorio */
        .ldap-table-wrapper {
            background: #0b1329;
            border: 1px solid #1e293b;
            border-radius: 0.75rem;
            overflow-x: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .ldap-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .ldap-table thead {
            background: #111e38;
        }
        .ldap-table th {
            padding: 0.85rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            border-bottom: 1px solid #1e293b;
        }
        .ldap-table td {
            padding: 0.85rem 1rem;
            font-size: 0.875rem;
            color: #e2e8f0;
            border-bottom: 1px solid #1e293b;
            vertical-align: middle;
        }
        .ldap-table tr:hover td {
            background: rgba(30, 41, 59, 0.4);
        }
        .ldap-table tr:last-child td {
            border-bottom: none;
        }

        /* Avatar circular */
        .ldap-avatar {
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: linear-gradient(135deg, #0284c7 0%, #4f46e5 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Badges */
        .ldap-badge-synced {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .ldap-badge-pending {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .ldap-code-pill {
            padding: 0.15rem 0.5rem;
            border-radius: 0.375rem;
            background: #1e293b;
            border: 1px solid #334155;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            color: #38bdf8;
        }

        /* CLI Box */
        .ldap-cli-box {
            background: #020617;
            border: 1px solid #1e293b;
            border-radius: 0.625rem;
            padding: 0.875rem 1rem;
            font-family: ui-monospace, monospace;
            font-size: 0.8125rem;
            color: #38bdf8;
            overflow-x: auto;
        }
    </style>

    <div class="ldap-theme space-y-6">
        {{-- Banner Principal con Estadísticas y Acciones Rápidas --}}
        <div class="ldap-hero-card">
            <div class="ldap-hero-content">
                <div style="max-width: 42rem;">
                    @if($this->connectionStatus === true)
                        <div class="ldap-status-pill ldap-status-active">
                            <span class="ldap-dot" style="background: #10b981;"></span>
                            Enlace LDAP Activo ({{ $this->connectionLatency }} ms)
                        </div>
                    @elseif($this->connectionStatus === false)
                        <div class="ldap-status-pill ldap-status-error">
                            <span class="ldap-dot" style="background: #f43f5e;"></span>
                            Conexión Fallida
                        </div>
                    @else
                        <div class="ldap-status-pill ldap-status-idle">
                            <span class="ldap-dot" style="background: #38bdf8;"></span>
                            Servicio Listo para Conectar
                        </div>
                    @endif

                    <h2 style="font-size: 1.75rem; font-weight: 800; color: #ffffff; margin: 0; letter-spacing: -0.02em;">
                        Directorio de Usuarios LDAP
                    </h2>
                    <p style="font-size: 0.875rem; color: #cbd5e1; margin-top: 0.35rem; line-height: 1.45;">
                        Sincroniza cuentas automáticamente desde tu servidor LLDAP / OpenLDAP hacia DataPlus para habilitar acceso unificado y asignación de roles.
                    </p>
                </div>

                {{-- Botones de Acción Inmediata --}}
                <div class="ldap-btn-group">
                    <button 
                        type="button"
                        wire:click="testConnection"
                        wire:loading.attr="disabled"
                        class="ldap-btn ldap-btn-secondary"
                    >
                        <x-heroicon-o-signal class="w-4 h-4 text-sky-400" wire:loading.remove wire:target="testConnection" />
                        <svg wire:loading wire:target="testConnection" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="testConnection">Probar Conexión</span>
                        <span wire:loading wire:target="testConnection">Probando...</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="loadDirectoryUsers"
                        wire:loading.attr="disabled"
                        class="ldap-btn ldap-btn-sky"
                    >
                        <x-heroicon-o-users class="w-4 h-4 text-white" wire:loading.remove wire:target="loadDirectoryUsers" />
                        <svg wire:loading wire:target="loadDirectoryUsers" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Explorar Directorio</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="syncAllUsers"
                        wire:loading.attr="disabled"
                        class="ldap-btn ldap-btn-emerald"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4 text-white" wire:loading.remove wire:target="syncAllUsers" />
                        <svg wire:loading wire:target="syncAllUsers" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="syncAllUsers">Sincronizar Todo</span>
                        <span wire:loading wire:target="syncAllUsers">Sincronizando...</span>
                    </button>
                </div>
            </div>

            {{-- Métricas Rápidas en Cuadrícula 4 Columnas --}}
            <div class="ldap-kpi-grid">
                <div class="ldap-kpi-card">
                    <div class="ldap-kpi-title">Total Usuarios en BD</div>
                    <div class="ldap-kpi-value">{{ $this->totalUsers }}</div>
                </div>

                <div class="ldap-kpi-card">
                    <div class="ldap-kpi-title" style="color: #38bdf8;">Vinculados con LDAP</div>
                    <div class="ldap-kpi-value" style="color: #7dd3fc;">{{ $this->syncedCount }}</div>
                </div>

                <div class="ldap-kpi-card">
                    <div class="ldap-kpi-title">Servidor y Puerto</div>
                    <div class="ldap-kpi-value" style="font-size: 1.05rem; margin-top: 0.45rem; color: #e2e8f0;">
                        {{ $data['host'] ?? 'lldap' }}:{{ $data['port'] ?? 3890 }}
                    </div>
                </div>

                <div class="ldap-kpi-card">
                    <div class="ldap-kpi-title">Cifrado</div>
                    <div class="ldap-kpi-value" style="font-size: 1.05rem; margin-top: 0.45rem;">
                        @if(!empty($data['ssl']))
                            <span style="color: #34d399;">LDAPS (6360)</span>
                        @elseif(!empty($data['tls']))
                            <span style="color: #38bdf8;">STARTTLS</span>
                        @else
                            <span style="color: #94a3b8;">Plano (3890)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Selector de Pestañas --}}
        <div class="ldap-tabs-bar">
            <button 
                type="button"
                wire:click="$set('activeTab', 'config')"
                class="ldap-tab-btn {{ $activeTab === 'config' ? 'ldap-tab-active' : '' }}"
            >
                <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                <span>Configuración de Servidor</span>
            </button>

            <button 
                type="button"
                wire:click="loadDirectoryUsers"
                class="ldap-tab-btn {{ $activeTab === 'directory' ? 'ldap-tab-active' : '' }}"
            >
                <x-heroicon-o-user-group class="w-4 h-4" />
                <span>Explorador de Cuentas LDAP</span>
                @if(count($directoryUsers) > 0)
                    <span style="background: rgba(14, 165, 233, 0.25); color: #7dd3fc; border-radius: 9999px; padding: 0.15rem 0.55rem; font-size: 0.75rem; font-weight: 700;">
                        {{ count($directoryUsers) }}
                    </span>
                @endif
            </button>

            <button 
                type="button"
                wire:click="$set('activeTab', 'diagnostics')"
                class="ldap-tab-btn {{ $activeTab === 'diagnostics' ? 'ldap-tab-active' : '' }}"
            >
                <x-heroicon-o-command-line class="w-4 h-4" />
                <span>Comando CLI & Cron</span>
            </button>
        </div>

        {{-- PESTAÑA 1: FORMULARIO DE CONFIGURACIÓN --}}
        @if($activeTab === 'config')
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <x-filament::button type="submit" size="lg" color="primary">
                        Guardar Parámetros LDAP
                    </x-filament::button>
                </div>
            </form>
        @endif

        {{-- PESTAÑA 2: EXPLORADOR DE USUARIOS LDAP --}}
        @if($activeTab === 'directory')
            <div class="space-y-4">
                <div class="ldap-card-header">
                    <div>
                        <h3>Cuentas detectadas en el servidor LDAP</h3>
                        <p>Puedes importar usuarios de forma individual o sincronizarlos todos a la vez.</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <button 
                            type="button" 
                            wire:click="loadDirectoryUsers" 
                            class="ldap-btn ldap-btn-secondary"
                            style="padding: 0.45rem 0.85rem; font-size: 0.75rem;"
                        >
                            Refrescar Lista
                        </button>
                        <button 
                            type="button" 
                            wire:click="syncAllUsers" 
                            class="ldap-btn ldap-btn-emerald"
                            style="padding: 0.45rem 0.85rem; font-size: 0.75rem;"
                        >
                            Sincronizar Todos
                        </button>
                    </div>
                </div>

                @if(empty($directoryUsers))
                    <div style="text-align: center; padding: 3rem 1rem; border-radius: 0.75rem; border: 1px dashed #334155; background: #0b1329;">
                        <x-heroicon-o-users class="w-12 h-12 mx-auto text-slate-500 mb-3" />
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: #f8fafc; margin: 0;">No se han consultado usuarios aún</h4>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                            Haz clic en "Explorar Directorio" para conectar con tu servidor LDAP y listar los usuarios disponibles.
                        </p>
                        <button 
                            type="button"
                            wire:click="loadDirectoryUsers"
                            class="ldap-btn ldap-btn-sky"
                            style="margin-top: 1rem;"
                        >
                            Cargar Usuarios de LDAP Ahora
                        </button>
                    </div>
                @else
                    <div class="ldap-table-wrapper">
                        <table class="ldap-table">
                            <thead>
                                <tr>
                                    <th>Usuario / Nombre</th>
                                    <th>Identificador (UID)</th>
                                    <th>Correo Electrónico</th>
                                    <th style="text-align: center;">Estado en DataPlus</th>
                                    <th style="text-align: right;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($directoryUsers as $u)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div class="ldap-avatar">
                                                    {{ strtoupper(substr($u['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #ffffff;">{{ $u['name'] }}</div>
                                                    <div style="font-size: 0.75rem; color: #94a3b8; max-width: 20rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $u['dn'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="ldap-code-pill">
                                                {{ $u['uid'] }}
                                            </span>
                                        </td>
                                        <td style="color: #cbd5e1;">
                                            {{ $u['email'] }}
                                        </td>
                                        <td style="text-align: center;">
                                            @if($u['is_synced'])
                                                <span class="ldap-badge-synced">
                                                    <span class="ldap-dot" style="background: #10b981; width: 0.375rem; height: 0.375rem;"></span>
                                                    Sincronizado
                                                </span>
                                            @else
                                                <span class="ldap-badge-pending">
                                                    <span class="ldap-dot" style="background: #f59e0b; width: 0.375rem; height: 0.375rem;"></span>
                                                    Pendiente
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            <button 
                                                type="button"
                                                wire:click="syncIndividual('{{ $u['uid'] }}', '{{ $u['email'] }}', '{{ addslashes($u['name']) }}')"
                                                class="ldap-btn {{ $u['is_synced'] ? 'ldap-btn-secondary' : 'ldap-btn-sky' }}"
                                                style="padding: 0.35rem 0.75rem; font-size: 0.75rem;"
                                            >
                                                {{ $u['is_synced'] ? 'Re-sincronizar' : 'Importar ahora' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        {{-- PESTAÑA 3: DIAGNÓSTICO Y CLI --}}
        @if($activeTab === 'diagnostics')
            <div class="space-y-6">
                <div style="background: #0b1329; border: 1px solid #1e293b; border-radius: 0.75rem; padding: 1.5rem;" class="space-y-4">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <x-heroicon-o-command-line class="w-5 h-5 text-sky-400" />
                        Sincronización Automatizada vía Consola
                    </h3>
                    <p style="font-size: 0.875rem; color: #94a3b8; line-height: 1.5; margin: 0;">
                        Puedes ejecutar la sincronización de usuarios manualmente en cualquier momento desde el servidor usando Artisan, o programarla en el Cron de Linux para que se actualice cada hora.
                    </p>

                    <div style="padding-top: 0.5rem;" class="space-y-2">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Comando Artisan</label>
                        <div class="ldap-cli-box">
                            <code>docker compose -f docker-compose.prod.yml exec -T app php artisan ldap:sync-users</code>
                        </div>
                    </div>

                    <div style="padding-top: 0.5rem;" class="space-y-2">
                        <label style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Automatización en Cron de Linux (crontab -e)</label>
                        <div class="ldap-cli-box" style="color: #34d399;">
                            <code>0 * * * * cd /home/jpaz/deploy/dataplus-platform && docker compose -f docker-compose.prod.yml exec -T app php artisan ldap:sync-users >/dev/null 2>&1</code>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
