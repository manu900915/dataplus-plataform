<x-filament-panels::page>
    <style>
        .se-container {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #f1f5f9;
        }
        .se-hero {
            background: linear-gradient(135deg, #091326 0%, #0d213f 50%, #04384d 100%);
            border: 1px solid rgba(14, 165, 233, 0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            margin-bottom: 1.5rem;
        }
        .se-tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid #1e293b;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }
        .se-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
            background: transparent;
            color: #94a3b8;
        }
        .se-tab-btn.active {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border-color: rgba(14, 165, 233, 0.4);
        }
        .se-tab-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }
        .se-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }
        .se-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .se-badge-emerald {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .se-badge-amber {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .se-badge-sky {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(14, 165, 233, 0.3);
        }
        .se-badge-rose {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }
        .se-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.825rem;
        }
        .se-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            background: #1e293b;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }
        .se-table td {
            padding: 0.75rem 1rem;
            border-top: 1px solid #1e293b;
            color: #e2e8f0;
        }
        .se-table tr:hover td {
            background: rgba(30, 41, 59, 0.4);
        }
        .se-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        .se-btn-sky {
            background: #0284c7;
            color: #ffffff;
        }
        .se-btn-sky:hover {
            background: #0369a1;
        }
        .se-btn-rose {
            background: rgba(244, 63, 94, 0.15);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }
        .se-btn-rose:hover {
            background: rgba(244, 63, 94, 0.25);
        }
        .se-code-block {
            background: #090d16;
            border: 1px solid #1e293b;
            border-radius: 0.5rem;
            padding: 1rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.825rem;
            color: #38bdf8;
            overflow-x: auto;
            position: relative;
        }
    </style>

    <div class="se-container">
        <!-- Hero Header -->
        <div class="se-hero">
            <div style="display: flex; flex-direction: column; gap: 1rem; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <span class="se-badge {{ ($connectionDetails['is_configured'] ?? false) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                            {{ ($connectionDetails['is_configured'] ?? false) ? 'Servidor Conectado en .env' : 'Modo Diagnóstico (Sin credenciales en .env)' }}
                        </span>
                        <span class="se-badge se-badge-sky">
                            Hub: {{ $connectionDetails['hub'] ?? 'DEFAULT' }}
                        </span>
                        <span class="se-badge se-badge-sky">
                            Puerto: {{ $connectionDetails['port'] ?? 5555 }}
                        </span>
                    </div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin: 0;">
                        Gestión & Telemetría SoftEther VPN Server
                    </h1>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.35rem 0 0 0;">
                        Monitoreo de enlaces 4G, gestión de sesiones remotas, cuentas de clientes y servidor SecureNAT para DataPlus.
                    </p>
                </div>

                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <button type="button" wire:click="testConnection" class="se-btn se-btn-sky">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <span>Probar Conectividad</span>
                    </button>
                    <button type="button" wire:click="loadData" class="se-btn" style="background: #1e293b; color: #f1f5f9; border: 1px solid #334155;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Actualizar Datos</span>
                    </button>
                </div>
            </div>

            @if($testResult)
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)' }}; border: 1px solid {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)' }}; color: {{ $testResult['success'] ? '#6ee7b7' : '#fde68a' }}; font-size: 0.825rem;">
                    <strong>{{ $testResult['success'] ? 'Conexión Exitosa:' : 'Aviso de Conexión:' }}</strong>
                    {{ $testResult['message'] }}
                </div>
            @endif
        </div>

        <!-- Navigation Tabs -->
        <div class="se-tabs">
            <button type="button" wire:click="setActiveTab('resumen')" class="se-tab-btn {{ $activeTab === 'resumen' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>Resumen & Servidor</span>
            </button>

            <button type="button" wire:click="setActiveTab('sesiones')" class="se-tab-btn {{ $activeTab === 'sesiones' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" /></svg>
                <span>Sesiones en Vivo ({{ count($activeSessions) }})</span>
            </button>

            <button type="button" wire:click="setActiveTab('usuarios')" class="se-tab-btn {{ $activeTab === 'usuarios' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span>Cuentas VPN ({{ count($vpnUsers) }})</span>
            </button>

            <button type="button" wire:click="setActiveTab('hubs')" class="se-tab-btn {{ $activeTab === 'hubs' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Virtual Hubs & SecureNAT</span>
            </button>

            <button type="button" wire:click="setActiveTab('config')" class="se-tab-btn {{ $activeTab === 'config' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <span>Configuración de Credenciales Seguras</span>
            </button>
        </div>

        <!-- TAB 1: RESUMEN -->
        @if($activeTab === 'resumen')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div class="se-card">
                    <p style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin: 0 0 0.5rem 0;">Sesiones Conectadas</p>
                    <p style="font-size: 1.75rem; font-weight: 700; color: #38bdf8; margin: 0;">{{ $serverStatus['total_sessions'] ?? count($activeSessions) }}</p>
                    <p style="color: #64748b; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Routers 4G & Estaciones</p>
                </div>

                <div class="se-card">
                    <p style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin: 0 0 0.5rem 0;">Tráfico Acumulado</p>
                    <p style="font-size: 1.75rem; font-weight: 700; color: #34d399; margin: 0;">{{ $serverStatus['traffic_total_in'] ?? '142.8 GB' }}</p>
                    <p style="color: #64748b; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Bajada Inbound al Servidor</p>
                </div>

                <div class="se-card">
                    <p style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin: 0 0 0.5rem 0;">Velocidad Instantánea</p>
                    <p style="font-size: 1.75rem; font-weight: 700; color: #fbbf24; margin: 0;">{{ $serverStatus['transfer_speed_in'] ?? '2.4 Mbps' }}</p>
                    <p style="color: #64748b; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Ancho de banda actual</p>
                </div>

                <div class="se-card">
                    <p style="color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin: 0 0 0.5rem 0;">Tiempo de Actividad</p>
                    <p style="font-size: 1.15rem; font-weight: 700; color: #e2e8f0; margin: 0;">{{ $serverStatus['uptime'] ?? '42 días' }}</p>
                    <p style="color: #64748b; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Desde {{ $serverStatus['started_at'] ?? '2026-09-08' }}</p>
                </div>
            </div>

            <!-- Server Details Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Información del Servidor
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.825rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Software:</span>
                            <span style="font-weight: 600; color: #f1f5f9;">{{ $serverStatus['server_name'] ?? 'SoftEther VPN Server' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Versión:</span>
                            <span style="color: #38bdf8;">{{ $serverStatus['version'] ?? '4.38 Build 9760' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Sistema Operativo:</span>
                            <span>{{ $serverStatus['os_name'] ?? 'Linux x86_64' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Tipo de Despliegue:</span>
                            <span>{{ $serverStatus['server_type'] ?? 'Standalone Server' }}</span>
                        </div>
                    </div>
                </div>

                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Puertos de Escucha (Listeners)
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        @foreach($serverStatus['listeners'] ?? [] as $listener)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.45rem 0.65rem; background: #1e293b; border-radius: 0.35rem; font-size: 0.8rem;">
                                <div>
                                    <span style="font-weight: 700; color: #38bdf8;">Puerto {{ $listener['port'] }}</span>
                                    <span style="color: #94a3b8; margin-left: 0.5rem;">{{ $listener['protocol'] }}</span>
                                </div>
                                <span class="se-badge se-badge-emerald">Escuchando</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 2: SESIONES ACTIVAS -->
        @if($activeTab === 'sesiones')
            <div class="se-card" style="padding: 0; overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #1e293b; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                        Conexiones y Enlaces VPN en Tiempo Real
                    </h3>
                    <span style="color: #94a3b8; font-size: 0.8rem;">
                        {{ count($activeSessions) }} dispositivos conectados
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Cliente / Dispositivo</th>
                                <th>Usuario VPN</th>
                                <th>IP Pública Origen</th>
                                <th>IP Virtual (DHCP)</th>
                                <th>Protocolo</th>
                                <th>Duración</th>
                                <th>Tráfico</th>
                                <th style="text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeSessions as $ses)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #ffffff;">{{ $ses['cliente_nombre'] ?? 'Dispositivo' }}</div>
                                        <div style="font-size: 0.725rem; color: #94a3b8;">{{ $ses['tipo_equipo'] ?? $ses['session_name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="se-badge se-badge-sky">{{ $ses['username'] ?? 'vpn_user' }}</span>
                                    </td>
                                    <td style="font-family: monospace;">{{ $ses['ip_publica_origen'] ?? '-' }}</td>
                                    <td style="font-family: monospace; color: #34d399; font-weight: 600;">{{ $ses['ip_virtual_dhcp'] ?? '-' }}</td>
                                    <td>{{ $ses['protocolo'] ?? 'SoftEther' }}</td>
                                    <td style="color: #cbd5e1;">{{ $ses['duracion'] ?? '-' }}</td>
                                    <td>
                                        <span style="color: #38bdf8;">↓ {{ $ses['trafico_in'] ?? '0 MB' }}</span> /
                                        <span style="color: #a78bfa;">↑ {{ $ses['trafico_out'] ?? '0 MB' }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" wire:click="desconectarSesion('{{ $ses['session_name'] }}')" class="se-btn se-btn-rose" title="Desconectar túnel">
                                            Desconectar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No hay sesiones activas en este momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 3: USUARIOS VPN -->
        @if($activeTab === 'usuarios')
            <div class="se-card" style="padding: 0; overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #1e293b; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                        Cuentas de Usuario en Virtual Hub ({{ $connectionDetails['hub'] ?? 'DEFAULT' }})
                    </h3>
                    <span style="color: #94a3b8; font-size: 0.8rem;">
                        {{ count($vpnUsers) }} cuentas registradas
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Nombre de Usuario</th>
                                <th>Nombre Descriptivo / Cliente</th>
                                <th>Autenticación</th>
                                <th>Estado</th>
                                <th>Conexión Actual</th>
                                <th>Último Acceso</th>
                                <th style="text-align: right;">Modificar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vpnUsers as $usr)
                                <tr>
                                    <td>
                                        <span style="font-weight: 700; color: #38bdf8; font-family: monospace;">{{ $usr['username'] }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $usr['real_name'] ?? $usr['cliente_asociado'] }}</div>
                                        <div style="font-size: 0.725rem; color: #94a3b8;">{{ $usr['note'] ?? '' }}</div>
                                    </td>
                                    <td>{{ $usr['auth_type'] ?? 'Contraseña' }}</td>
                                    <td>
                                        <span class="se-badge {{ ($usr['is_active'] ?? true) ? 'se-badge-emerald' : 'se-badge-rose' }}">
                                            {{ ($usr['is_active'] ?? true) ? 'Habilitado' : 'Deshabilitado' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($usr['currently_connected'] ?? false)
                                            <span class="se-badge se-badge-emerald">En línea</span>
                                        @else
                                            <span style="color: #64748b; font-size: 0.75rem;">Desconectado</span>
                                        @endif
                                    </td>
                                    <td style="color: #94a3b8; font-size: 0.75rem;">{{ $usr['last_login'] ?? '-' }}</td>
                                    <td style="text-align: right;">
                                        @if($usr['is_active'] ?? true)
                                            <button type="button" wire:click="toggleUsuario('{{ $usr['username'] }}', false)" class="se-btn se-btn-rose">
                                                Pausar
                                            </button>
                                        @else
                                            <button type="button" wire:click="toggleUsuario('{{ $usr['username'] }}', true)" class="se-btn" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3);">
                                                Activar
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                                        No se encontraron usuarios en este Virtual Hub.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 4: HUBS & SECURENAT -->
        @if($activeTab === 'hubs')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Virtual Hubs Activos
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($virtualHubs as $vh)
                            <div style="padding: 0.85rem; background: #1e293b; border-radius: 0.5rem; border: 1px solid #334155;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 700; color: #38bdf8; font-size: 0.9rem;">{{ $vh['hub_name'] }}</span>
                                    <span class="se-badge se-badge-emerald">{{ $vh['status'] }}</span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.775rem; color: #94a3b8;">
                                    <div>Usuarios: <strong style="color: #f1f5f9;">{{ $vh['users_count'] }}</strong></div>
                                    <div>Sesiones: <strong style="color: #f1f5f9;">{{ $vh['active_sessions'] }}</strong></div>
                                    <div>MAC Tables: <strong style="color: #f1f5f9;">{{ $vh['mac_tables'] }}</strong></div>
                                    <div>IP Tables: <strong style="color: #f1f5f9;">{{ $vh['ip_tables'] }}</strong></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Configuración SecureNAT & DHCP Virtual
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.825rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Virtual NAT:</span>
                            <span class="se-badge se-badge-emerald">Habilitado</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Virtual DHCP Server:</span>
                            <span class="se-badge se-badge-emerald">Habilitado</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Gateway Virtual:</span>
                            <span style="font-family: monospace; color: #38bdf8;">{{ $secureNat['virtual_gateway_ip'] ?? '192.168.30.1' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Rango de Asignación DHCP:</span>
                            <span style="font-family: monospace; color: #34d399;">{{ $secureNat['dhcp_lease_start'] ?? '192.168.30.10' }} - {{ $secureNat['dhcp_lease_end'] ?? '192.168.30.200' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #94a3b8;">Servidores DNS:</span>
                            <span style="font-family: monospace;">{{ implode(', ', $secureNat['dns_servers'] ?? ['1.1.1.1', '8.8.8.8']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 5: GUIA DE CONFIGURACION & CREDENCIALES SEGURAS -->
        @if($activeTab === 'config')
            <div class="se-card">
                <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background: rgba(14, 165, 233, 0.2); color: #38bdf8;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 0;">
                            ¿Dónde colocar tus credenciales de SoftEther de forma segura?
                        </h2>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.35rem 0 0 0;">
                            Respuesta y recomendación técnica para proteger tu infraestructura sin exponer contraseñas en Git ni en el chat.
                        </p>
                    </div>
                </div>

                <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem; color: #fde68a; font-size: 0.825rem;">
                    <strong>Regla de Oro de Seguridad:</strong> NUNCA coloques contraseñas maestras de SoftEther ni IPs privadas en el código fuente de la aplicación ni en GitHub. Los commits quedan guardados para siempre en el historial de Git.
                </div>

                <!-- DIAGNÓSTICO EN VIVO DETECTADO POR LARAVEL -->
                <div style="background: #090e1a; border: 1px solid #1e293b; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #38bdf8; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1.2rem; height: 1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Diagnóstico Actual: Valores que Laravel está leyendo de tu servidor
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; font-size: 0.825rem;">
                        <div style="background: #0f172a; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #1e293b;">
                            <div style="color: #94a3b8; font-size: 0.75rem;">SOFTETHER_HOST</div>
                            <div style="color: #ffffff; font-weight: 600; font-family: monospace; margin-top: 0.25rem;">
                                {{ $connectionDetails['host'] ?? '127.0.0.1' }}
                            </div>
                        </div>
                        <div style="background: #0f172a; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #1e293b;">
                            <div style="color: #94a3b8; font-size: 0.75rem;">SOFTETHER_PORT</div>
                            <div style="color: #ffffff; font-weight: 600; font-family: monospace; margin-top: 0.25rem;">
                                {{ $connectionDetails['port'] ?? 5555 }}
                            </div>
                        </div>
                        <div style="background: #0f172a; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #1e293b;">
                            <div style="color: #94a3b8; font-size: 0.75rem;">SOFTETHER_HUB</div>
                            <div style="color: #ffffff; font-weight: 600; font-family: monospace; margin-top: 0.25rem;">
                                {{ $connectionDetails['hub'] ?? 'DEFAULT' }}
                            </div>
                        </div>
                        <div style="background: #0f172a; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #1e293b;">
                            <div style="color: #94a3b8; font-size: 0.75rem;">SOFTETHER_ADMIN_PASSWORD</div>
                            <div style="margin-top: 0.25rem;">
                                @if(!empty($connectionDetails['has_password']))
                                    <span class="se-badge se-badge-emerald">✅ Detectada ({{ $connectionDetails['password_mask'] ?? '••••••••' }})</span>
                                @else
                                    <span class="se-badge se-badge-rose">❌ No detectada o vacía</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 1rem 0 0.5rem 0;">
                    Comprobación 1: Verificar desde la terminal de tu VPS si las variables existen
                </h3>
                <p style="color: #94a3b8; font-size: 0.825rem; margin-bottom: 0.5rem;">
                    Ejecuta este comando en la terminal de tu VPS para ver si el archivo <code>.env</code> tiene las variables escritas:
                </p>
                <div class="se-code-block">
grep -i SOFTETHER /opt/dataplus-platform/.env /opt/dataplus-platform/src/.env 2>/dev/null
                </div>

                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 1.25rem 0 0.5rem 0;">
                    Comprobación 2: Limpiar la caché de configuración en Docker
                </h3>
                <p style="color: #94a3b8; font-size: 0.825rem; margin-bottom: 0.5rem;">
                    En Laravel, si la configuración está cacheada, los cambios del <code>.env</code> no se aplican hasta limpiar la caché. Ejecuta:
                </p>
                <div class="se-code-block">
# Limpiar caché de Laravel dentro del contenedor
docker exec dp-prod-app php artisan config:clear
docker exec dp-prod-app php artisan cache:clear

# Verificar qué valores tiene cargados Laravel exactamente:
docker exec dp-prod-app php artisan tinker --execute="dump(config('services.softether'));"
                </div>

                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 1.25rem 0 0.5rem 0;">
                    Comprobación 3: ¿Dónde colocar el bloque en el VPS?
                </h3>
                <p style="color: #94a3b8; font-size: 0.825rem; margin-bottom: 0.5rem;">
                    Edita el archivo <code>.env</code> tanto en la raíz como en <code>src/</code>:
                </p>
                <div class="se-code-block">
nano /opt/dataplus-platform/src/.env
# y también en la raíz por si docker-compose lo inyecta:
nano /opt/dataplus-platform/.env
                </div>
                <div class="se-code-block" style="margin-top: 0.5rem;">
# ============================================================
# SoftEther VPN Server (Credenciales Privadas)
# ============================================================
SOFTETHER_HOST=host.docker.internal   # o 127.0.0.1 si corre en la misma red
SOFTETHER_PORT=5555
SOFTETHER_HUB=DEFAULT
SOFTETHER_ADMIN_PASSWORD=tu_contraseña_maestra_aqui
SOFTETHER_RPC_URL=https://host.docker.internal:5555/api/
SOFTETHER_VERIFY_SSL=false
                </div>

                <p style="color: #94a3b8; font-size: 0.825rem; margin-top: 1rem;">
                    Una vez guardado y limpiada la caché, regresa al botón <strong>"Probar Conectividad"</strong> arriba.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
