<x-filament-panels::page>
    <style>
        .se-container {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #f1f5f9;
        }
        .se-server-bar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }
        .se-server-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #1e293b;
            background: #0f172a;
            color: #94a3b8;
            transition: all 0.2s;
        }
        .se-server-btn.active {
            background: rgba(14, 165, 233, 0.15);
            color: #38bdf8;
            border-color: rgba(14, 165, 233, 0.5);
            box-shadow: 0 0 15px -3px rgba(14, 165, 233, 0.25);
        }
        .se-server-btn:hover:not(.active) {
            background: #1e293b;
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
        .se-btn-amber {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
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
        .se-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
        }
        .se-modal {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            padding: 1.5rem;
            max-width: 650px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>

    <div class="se-container">
        <!-- BARRA MULTI-SERVIDOR (VPS 1 vs VPS 2) -->
        <div class="se-server-bar">
            <span style="font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">
                Servidor SoftEther:
            </span>

            @foreach($connectionDetails['available_servers'] ?? [] as $srv)
                <button type="button" 
                        wire:click="selectServer('{{ $srv['id'] }}')" 
                        wire:loading.attr="disabled"
                        wire:target="selectServer('{{ $srv['id'] }}')"
                        class="se-server-btn {{ $selectedServer === $srv['id'] ? 'active' : '' }}"
                        style="position: relative;">
                    <!-- Spinner mientras conmuta servidor -->
                    <svg wire:loading wire:target="selectServer('{{ $srv['id'] }}')" style="width: 14px; height: 14px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="selectServer('{{ $srv['id'] }}')" style="width: 8px; height: 8px; border-radius: 50%; background: {{ $srv['is_configured'] ? '#34d399' : '#f59e0b' }};"></span>
                    <span>{{ $srv['name'] }}</span>
                    @if($srv['is_configured'])
                        <span style="font-size: 0.7rem; opacity: 0.75; font-family: monospace;">({{ $srv['host'] }})</span>
                    @else
                        <span style="font-size: 0.7rem; color: #fbbf24;">(Sin configurar)</span>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Hero Header -->
        <div class="se-hero">
            <div style="display: flex; flex-direction: column; gap: 1rem; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                        <span class="se-badge {{ ($connectionDetails['is_configured'] ?? false) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                            {{ ($connectionDetails['is_configured'] ?? false) ? "Conectado a {$connectionDetails['server_name']}" : "Modo Diagnóstico (Sin credenciales para {$connectionDetails['server_name']})" }}
                        </span>
                        <span class="se-badge se-badge-sky">
                            Hub: {{ $connectionDetails['hub'] ?? 'DEFAULT' }}
                        </span>
                        <span class="se-badge se-badge-sky">
                            Host: {{ $connectionDetails['host'] ?? '127.0.0.1' }}:{{ $connectionDetails['port'] ?? 5555 }}
                        </span>
                        <span wire:loading wire:target="selectServer, loadData" class="se-badge se-badge-amber">
                            <svg style="width: 12px; height: 12px; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                                <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sincronizando RPC...
                        </span>
                    </div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #ffffff; margin: 0;">
                        Gestión & Telemetría SoftEther VPN
                    </h1>
                    <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.35rem 0 0 0;">
                        Monitoreo de enlaces 4G, túneles remotos, cuentas de clientes y servidor SecureNAT para DataPlus.
                    </p>
                </div>

                <!-- Botonera de Comandos Rápidos -->
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <button type="button" wire:click="testConnection" wire:loading.attr="disabled" class="se-btn se-btn-sky">
                        <svg wire:loading.remove wire:target="testConnection" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <svg wire:loading wire:target="testConnection" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Probar Conectividad</span>
                    </button>
                    <button type="button" wire:click="loadData(true, true)" wire:loading.attr="disabled" class="se-btn" style="background: #1e293b; color: #f1f5f9; border: 1px solid #334155;">
                        <svg wire:loading.remove wire:target="loadData" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <svg wire:loading wire:target="loadData" style="width: 1rem; height: 1rem; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Actualizar Datos</span>
                    </button>
                    <button type="button" wire:click="guardarEnDisco" class="se-btn se-btn-amber" title="Guarda la configuración actual en vpn_server.config inmediatamente">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                        <span>Guardar en Disco (Flush)</span>
                    </button>
                    <button type="button" 
                            wire:click="reiniciarServicio" 
                            wire:confirm="¿Está seguro de reiniciar el servicio SoftEther VPN en este servidor? Las conexiones activas se reconectarán en unos segundos."
                            class="se-btn se-btn-rose"
                            title="Reinicia el servicio SoftEther en el VPS seleccionado">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span>Reiniciar SoftEther</span>
                    </button>
                </div>
            </div>

            @if($testResult)
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)' }}; border: 1px solid {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)' }}; color: {{ $testResult['success'] ? '#6ee7b7' : '#fde68a' }}; font-size: 0.825rem;">
                    <strong>{{ $testResult['success'] ? 'Resultado:' : 'Aviso de Conexión:' }}</strong>
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
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                <span>Configuración (.env)</span>
            </button>
        </div>

        <!-- TAB 1: RESUMEN DEL SERVIDOR -->
        @if($activeTab === 'resumen')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div class="se-card">
                    <div style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Sesiones Activas</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #38bdf8; margin-top: 0.25rem;">
                        {{ $serverStatus['total_sessions'] ?? count($activeSessions) }}
                    </div>
                    <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">Túneles en tiempo real</div>
                </div>

                <div class="se-card">
                    <div style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Cuentas de Clientes</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #34d399; margin-top: 0.25rem;">
                        {{ $serverStatus['total_users'] ?? count($vpnUsers) }}
                    </div>
                    <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">Usuarios configurados</div>
                </div>

                <div class="se-card">
                    <div style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Tráfico Total Recibido</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #a78bfa; margin-top: 0.25rem;">
                        {{ $serverStatus['traffic_total_in'] ?? '0 B' }}
                    </div>
                    <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">Inbound acumulado</div>
                </div>

                <div class="se-card">
                    <div style="color: #94a3b8; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Tráfico Total Enviado</div>
                    <div style="font-size: 1.75rem; font-weight: 700; color: #f43f5e; margin-top: 0.25rem;">
                        {{ $serverStatus['traffic_total_out'] ?? '0 B' }}
                    </div>
                    <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">Outbound acumulado</div>
                </div>
            </div>

            <!-- Ficha Técnica del Servidor -->
            <div class="se-card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                    Información de la Instancia VPN ({{ $connectionDetails['server_name'] }})
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; font-size: 0.825rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Nombre del Producto:</span>
                        <span style="font-weight: 600; color: #ffffff;">{{ $serverStatus['server_name'] ?? 'SoftEther VPN Server' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Versión & Build:</span>
                        <span style="font-weight: 600; color: #38bdf8;">{{ $serverStatus['version'] ?? 'v4.38 Build 9760' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Sistema Operativo:</span>
                        <span style="font-weight: 600; color: #ffffff;">{{ $serverStatus['os_name'] ?? 'Linux' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Tiempo Activo (Uptime):</span>
                        <span style="font-weight: 600; color: #34d399;">{{ $serverStatus['uptime'] ?? 'Activo' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Tipo de Servidor:</span>
                        <span style="font-weight: 600; color: #ffffff;">{{ $serverStatus['server_type'] ?? 'Standalone Server' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">Conexiones TCP Activas:</span>
                        <span style="font-weight: 600; color: #38bdf8;">{{ $serverStatus['total_tcp_connections'] ?? 8 }}</span>
                    </div>
                </div>
            </div>

            <!-- Listeners y Puertos -->
            <div class="se-card">
                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                    Puertos de Escucha (TCP Listeners)
                </h3>
                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Puerto</th>
                                <th>Protocolo / Función</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serverStatus['listeners'] ?? [] as $listener)
                                <tr>
                                    <td style="font-family: monospace; font-weight: 600; color: #38bdf8;">{{ $listener['port'] }}</td>
                                    <td>{{ $listener['protocol'] }}</td>
                                    <td>
                                        <span class="se-badge se-badge-emerald">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                            {{ $listener['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 2: SESIONES EN VIVO -->
        @if($activeTab === 'sesiones')
            <div class="se-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                        Túneles & Enlaces Activos en {{ $connectionDetails['server_name'] }}
                    </h3>
                    <span class="se-badge se-badge-sky">
                        Total Conectados: {{ count($activeSessions) }}
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Sesión</th>
                                <th>Usuario / Cliente</th>
                                <th>IP Pública</th>
                                <th>Protocolo</th>
                                <th>Duración</th>
                                <th>Tráfico</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeSessions as $sesion)
                                <tr>
                                    <td>
                                        <div style="font-family: monospace; font-weight: 600; color: #38bdf8;">
                                            {{ $sesion['session_name'] ?? 'SID' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #ffffff;">{{ $sesion['cliente_nombre'] ?? $sesion['username'] }}</div>
                                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $sesion['username'] ?? '' }}</div>
                                    </td>
                                    <td style="font-family: monospace;">{{ $sesion['ip_publica_origen'] ?? '-' }}</td>
                                    <td>{{ $sesion['protocolo'] ?? 'SoftEther VPN' }}</td>
                                    <td>{{ $sesion['duracion'] ?? 'Activa' }}</td>
                                    <td>
                                        <span style="color: #34d399;">↓ {{ $sesion['trafico_in'] ?? '0 MB' }}</span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" 
                                                    wire:click="inspeccionarSesion('{{ $sesion['session_name'] }}')" 
                                                    class="se-btn se-btn-sky" 
                                                    title="Ver detalles técnicos completos de la sesión">
                                                <span>Detalles</span>
                                            </button>
                                            <button type="button" 
                                                    wire:click="desconectarSesion('{{ $sesion['session_name'] }}')" 
                                                    wire:confirm="¿Seguro que desea desconectar la sesión {{ $sesion['session_name'] }}?"
                                                    class="se-btn se-btn-rose"
                                                    title="Forzar desconexión de sesión">
                                                <span>Desconectar</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                        No hay sesiones activas en este servidor SoftEther actualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 3: CUENTAS DE CLIENTES VPN -->
        @if($activeTab === 'usuarios')
            <div class="se-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                            Cuentas de Usuarios VPN ({{ $connectionDetails['server_name'] }})
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.8rem; margin: 0.25rem 0 0 0;">
                            Usuarios autorizados para conectar routers 4G y puestos remotos al Virtual Hub <strong>{{ $connectionDetails['hub'] }}</strong>.
                        </p>
                    </div>
                    <button type="button" wire:click="$set('isCreatingUser', true)" class="se-btn se-btn-sky">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>Crear Usuario VPN</span>
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre / Descripción</th>
                                <th>Autenticación</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vpnUsers as $user)
                                <tr>
                                    <td style="font-family: monospace; font-weight: 600; color: #38bdf8;">
                                        {{ $user['username'] }}
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #ffffff;">{{ $user['real_name'] }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $user['note'] ?? '' }}</div>
                                    </td>
                                    <td>{{ $user['auth_type'] ?? 'Contraseña' }}</td>
                                    <td>
                                        <span class="se-badge {{ ($user['is_active'] ?? true) ? 'se-badge-emerald' : 'se-badge-rose' }}">
                                            {{ ($user['is_active'] ?? true) ? 'Habilitado' : 'Deshabilitado' }}
                                        </span>
                                    </td>
                                    <td>{{ $user['last_login'] ?? 'Nunca' }}</td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button type="button" 
                                                    wire:click="toggleUsuario('{{ $user['username'] }}', {{ ($user['is_active'] ?? true) ? 'false' : 'true' }})" 
                                                    class="se-btn {{ ($user['is_active'] ?? true) ? 'se-btn-rose' : 'se-btn-sky' }}">
                                                <span>{{ ($user['is_active'] ?? true) ? 'Deshabilitar' : 'Habilitar' }}</span>
                                            </button>
                                            <button type="button" 
                                                    wire:click="eliminarUsuario('{{ $user['username'] }}')" 
                                                    wire:confirm="¿Seguro que desea eliminar permanentemente al usuario {{ $user['username'] }}?"
                                                    class="se-btn se-btn-rose"
                                                    title="Eliminar usuario de SoftEther">
                                                <span>Eliminar</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                        No se encontraron usuarios VPN registrados en este hub.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- TAB 4: VIRTUAL HUBS & SECURENAT -->
        @if($activeTab === 'hubs')
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Virtual Hubs en {{ $connectionDetails['server_name'] }}
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @forelse($virtualHubs as $hub)
                            <div style="background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <div style="font-weight: 700; color: #38bdf8; font-size: 1.05rem;">{{ $hub['hub_name'] }}</div>
                                    <span class="se-badge {{ ($hub['status'] ?? '') === 'Online' ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                        {{ $hub['status'] ?? 'Desconocido' }}
                                    </span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem; color: #94a3b8;">
                                    <div>Usuarios: <strong style="color: #ffffff;">{{ $hub['users_count'] ?? 0 }}</strong></div>
                                    <div>Sesiones: <strong style="color: #ffffff;">{{ $hub['active_sessions'] ?? 0 }}</strong></div>
                                    <div>Tablas MAC: <strong style="color: #ffffff;">{{ $hub['mac_tables'] ?? 0 }}</strong></div>
                                    <div>Tablas IP: <strong style="color: #ffffff;">{{ $hub['ip_tables'] ?? 0 }}</strong></div>
                                    <div>Tráfico: <strong style="color: #38bdf8;">{{ $hub['traffic'] ?? '0 B' }}</strong></div>
                                    <div>Última com.: <span style="font-size: 0.75rem;">{{ $hub['last_comm'] ?? 'N/D' }}</span></div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #94a3b8; padding: 2rem; background: #090e1a; border: 1px dashed #334155; border-radius: 0.5rem;">
                                <svg style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem auto; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                <div>No se detectaron Virtual Hubs activos en este servidor.</div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Verifique la conexión RPC con el VPS.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="se-card">
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem;">
                        Estado SecureNAT & DHCP Virtual (Hub: {{ $connectionDetails['hub'] }})
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.825rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Virtual NAT:</span>
                            <span class="se-badge {{ !empty($secureNat['nat_enabled']) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                {{ !empty($secureNat['nat_enabled']) ? 'Habilitado' : 'Deshabilitado' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Virtual DHCP Server:</span>
                            <span class="se-badge {{ !empty($secureNat['dhcp_enabled']) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                {{ !empty($secureNat['dhcp_enabled']) ? 'Habilitado' : 'Deshabilitado' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Gateway Virtual:</span>
                            <span style="font-family: monospace; color: {{ !empty($secureNat['virtual_gateway_ip']) && $secureNat['virtual_gateway_ip'] !== 'No disponible' ? '#38bdf8' : '#64748b' }};">
                                {{ $secureNat['virtual_gateway_ip'] ?? 'No disponible' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Rango Asignación DHCP:</span>
                            <span style="font-family: monospace; color: {{ !empty($secureNat['dhcp_enabled']) ? '#34d399' : '#64748b' }};">
                                @if(!empty($secureNat['dhcp_enabled']))
                                    {{ $secureNat['dhcp_lease_start'] ?? 'N/D' }} - {{ $secureNat['dhcp_lease_end'] ?? 'N/D' }}
                                @else
                                    Inactivo
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Servidores DNS (DHCP):</span>
                            <span style="font-family: monospace; color: #a78bfa;">
                                {{ !empty($secureNat['dns_servers']) ? implode(', ', $secureNat['dns_servers']) : 'Por defecto' }}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Leases Activos DHCP:</span>
                            <span style="font-weight: 600; color: #ffffff;">{{ $secureNat['active_dhcp_leases'] ?? 0 }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8;">Sesiones NAT Activas:</span>
                            <span style="font-weight: 600; color: #ffffff;">
                                {{ $secureNat['total_nat_sessions'] ?? 0 }}
                                @if(($secureNat['total_nat_sessions'] ?? 0) > 0)
                                    <span style="font-size: 0.75rem; color: #64748b; font-weight: normal;">
                                        ({{ $secureNat['tcp_sessions'] ?? 0 }} TCP, {{ $secureNat['udp_sessions'] ?? 0 }} UDP)
                                    </span>
                                @endif
                            </span>
                        </div>
                        @if(!empty($secureNat['is_kernel_mode']))
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #94a3b8;">Modo Kernel:</span>
                                <span class="se-badge se-badge-sky">Activo</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- TAB 5: GUIA DE CONFIGURACION & ARCHIVO .ENV (VPS 1 & VPS 2) -->
        @if($activeTab === 'config')
            <div class="se-card">
                <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background: rgba(14, 165, 233, 0.2); color: #38bdf8;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 0;">
                            Configuración Multi-Servidor (VPS 1 & VPS 2) en tu archivo .env
                        </h2>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.35rem 0 0 0;">
                            Estructura para gestionar ambos servidores SoftEther sin exponer contraseñas en Git.
                        </p>
                    </div>
                </div>

                <div class="se-code-block" style="margin-bottom: 1.5rem;">
# ============================================================
# SOFTETHER VPN - SERVIDOR 1 (VPS 1 - Principal)
# ============================================================
SOFTETHER_VPS1_NAME="VPS 1 (Principal - 152.206.177.55)"
SOFTETHER_VPS1_HOST=152.206.177.55
SOFTETHER_VPS1_PORT=5555
SOFTETHER_VPS1_HUB=DEFAULT
SOFTETHER_VPS1_ADMIN_PASSWORD=T3l3c0*2023ss
SOFTETHER_VPS1_VERIFY_SSL=false

# ============================================================
# SOFTETHER VPN - SERVIDOR 2 (VPS 2 - Secundario)
# ============================================================
SOFTETHER_VPS2_NAME="VPS 2 (Secundario)"
SOFTETHER_VPS2_HOST=IP_DE_TU_VPS2
SOFTETHER_VPS2_PORT=5555
SOFTETHER_VPS2_HUB=DEFAULT
SOFTETHER_VPS2_ADMIN_PASSWORD=clave_administrador_vps2
SOFTETHER_VPS2_VERIFY_SSL=false
                </div>

                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 1rem 0 0.5rem 0;">
                    Comandos para refrescar la configuración en tu VPS:
                </h3>
                <div class="se-code-block">
cd /opt/dataplus-platform
git pull origin feature/softether-management
docker exec dp-prod-app php artisan config:clear
docker exec dp-prod-app php artisan cache:clear
                </div>
            </div>
        @endif

        <!-- MODAL DETALLE DE SESIÓN (GetSessionStatus) -->
        @if($inspectSession)
            <div class="se-modal-overlay">
                <div class="se-modal">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin: 0;">
                            Detalles de Sesión: {{ $inspectSession['Name_str'] ?? 'Sesión' }}
                        </h3>
                        <button type="button" wire:click="cerrarModalInspeccion" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.825rem;">
                        <div><span style="color: #94a3b8;">Usuario:</span> <strong style="color: #ffffff;">{{ $inspectSession['Username_str'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">IP Cliente:</span> <strong style="color: #38bdf8; font-family: monospace;">{{ $inspectSession['Client_Ip_Address_ip'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">Host Cliente:</span> <strong style="color: #ffffff;">{{ $inspectSession['SessionStatus_ClientHostName_str'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">Software Cliente:</span> <strong style="color: #ffffff;">{{ $inspectSession['ClientProductName_str'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">SO Cliente:</span> <strong style="color: #ffffff;">{{ $inspectSession['ClientOsName_str'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">Cifrado SSL:</span> <strong style="color: #34d399;">{{ $inspectSession['CipherName_str'] ?? 'AES-256' }}</strong></div>
                        <div><span style="color: #94a3b8;">Protocolo Base:</span> <strong style="color: #ffffff;">{{ $inspectSession['UnderlayProtocol_str'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">Conexiones TCP:</span> <strong style="color: #ffffff;">{{ $inspectSession['MaxTcpConnections_u32'] ?? 1 }}</strong></div>
                        <div><span style="color: #94a3b8;">Inicio de Conexión:</span> <strong style="color: #ffffff;">{{ $inspectSession['StartTime_dt'] ?? '-' }}</strong></div>
                        <div><span style="color: #94a3b8;">Tráfico Enviado:</span> <strong style="color: #38bdf8;">{{ number_format(($inspectSession['TotalSendSize_u64'] ?? 0) / 1048576, 2) }} MB</strong></div>
                        <div><span style="color: #94a3b8;">Tráfico Recibido:</span> <strong style="color: #34d399;">{{ number_format(($inspectSession['TotalRecvSize_u64'] ?? 0) / 1048576, 2) }} MB</strong></div>
                    </div>

                    <div style="margin-top: 1.5rem; text-align: right;">
                        <button type="button" wire:click="cerrarModalInspeccion" class="se-btn" style="background: #334155; color: #ffffff;">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- MODAL CREAR USUARIO VPN (CreateUser) -->
        @if($isCreatingUser)
            <div class="se-modal-overlay">
                <div class="se-modal">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin: 0;">
                            Nuevo Usuario VPN en {{ $connectionDetails['server_name'] }}
                        </h3>
                        <button type="button" wire:click="$set('isCreatingUser', false)" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                    </div>

                    <form wire:submit.prevent="crearUsuario" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre de Usuario (VPN)</label>
                            <input type="text" wire:model="newUsername" required placeholder="ej: cliente_playa" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre Real / Cliente Asociado</label>
                            <input type="text" wire:model="newRealname" placeholder="ej: Router 4G Moraima Guanabo" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Contraseña VPN</label>
                            <input type="password" wire:model="newPassword" required placeholder="Contraseña segura" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nota / Descripción</label>
                            <input type="text" wire:model="newNote" placeholder="ej: Enlace principal de telemetría" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                            <button type="button" wire:click="$set('isCreatingUser', false)" class="se-btn" style="background: #334155; color: #ffffff;">
                                Cancelar
                            </button>
                            <button type="submit" class="se-btn se-btn-sky">
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
