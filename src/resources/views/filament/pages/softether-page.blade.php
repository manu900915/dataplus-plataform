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
        .se-btn-emerald {
            background: rgba(16, 185, 129, 0.18);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }
        .se-btn-emerald:hover {
            background: rgba(16, 185, 129, 0.28);
        }
        .se-btn-indigo {
            background: rgba(99, 102, 241, 0.18);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.35);
        }
        .se-btn-indigo:hover {
            background: rgba(99, 102, 241, 0.28);
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
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            padding: 1rem;
            animation: seFadeIn 0.11s ease-out;
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
            animation: sePopIn 0.13s cubic-bezier(0.16, 1, 0.3, 1);
        }
        [x-cloak] {
            display: none !important;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes seFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes sePopIn {
            from { opacity: 0; transform: translateY(6px) scale(0.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>

    <script>
        if (!window.softetherFastPanel) {
            window.softetherFastPanel = function ($wire, defaults) {
                return {
                    activeTab: $wire.entangle('activeTab'),
                    selectedHub: $wire.entangle('selectedHub'),
                    userHubFilter: $wire.entangle('userHubFilter'),
                    l3SwitchFilter: $wire.entangle('l3SwitchFilter'),

                    // Modal Inspección Sesión (apertura optimista en 0ms)
                    inspectSession: $wire.entangle('inspectSession'),
                    inspectModalOpen: false,
                    inspectSessionTitle: '',
                    inspectLoading: false,

                    // Modal Crear Usuario (0ms)
                    isCreatingUser: $wire.entangle('isCreatingUser'),
                    newUserHub: $wire.entangle('newUserHub'),
                    newUsername: $wire.entangle('newUsername'),
                    newRealname: $wire.entangle('newRealname'),
                    newPassword: $wire.entangle('newPassword'),
                    newNote: $wire.entangle('newNote'),

                    // Modal Deshabilitar Usuario Temporal (0ms)
                    userToTimedDisable: $wire.entangle('userToTimedDisable'),
                    disableDurationPreset: $wire.entangle('disableDurationPreset'),
                    disableUntilCustom: $wire.entangle('disableUntilCustom'),
                    disableReason: $wire.entangle('disableReason'),

                    // Modal Gestión de Virtual Hub (apertura optimista 0ms + 1 RPC paralelo)
                    managingHub: $wire.entangle('managingHub'),
                    hubManageTab: $wire.entangle('hubManageTab'),
                    hubLoading: false,
                    hubRoutesList: $wire.entangle('hubRoutesList'),
                    hubApplyPushRoutes: $wire.entangle('hubApplyPushRoutes'),
                    newHubRouteNetwork: $wire.entangle('newHubRouteNetwork'),
                    newHubRouteMask: $wire.entangle('newHubRouteMask'),
                    newHubRouteGateway: $wire.entangle('newHubRouteGateway'),
                    hubRouteError: '',

                    // Modal Crear / Eliminar Hub (0ms)
                    isCreatingHub: $wire.entangle('isCreatingHub'),
                    newHubName: $wire.entangle('newHubName'),
                    newHubPassword: $wire.entangle('newHubPassword'),
                    hubToDelete: $wire.entangle('hubToDelete'),

                    // Modales Layer 3 (0ms)
                    isCreatingL3Switch: $wire.entangle('isCreatingL3Switch'),
                    newL3SwitchName: $wire.entangle('newL3SwitchName'),
                    isEditingL3If: $wire.entangle('isEditingL3If'),
                    editingL3IfOriginal: $wire.entangle('editingL3IfOriginal'),
                    l3IfFormSwitch: $wire.entangle('l3IfFormSwitch'),
                    l3IfFormHub: $wire.entangle('l3IfFormHub'),
                    l3IfFormIp: $wire.entangle('l3IfFormIp'),
                    l3IfFormMask: $wire.entangle('l3IfFormMask'),
                    isEditingL3Route: $wire.entangle('isEditingL3Route'),
                    editingL3RouteOriginal: $wire.entangle('editingL3RouteOriginal'),
                    l3RouteFormSwitch: $wire.entangle('l3RouteFormSwitch'),
                    l3RouteFormNetwork: $wire.entangle('l3RouteFormNetwork'),
                    l3RouteFormMask: $wire.entangle('l3RouteFormMask'),
                    l3RouteFormGateway: $wire.entangle('l3RouteFormGateway'),
                    l3RouteFormMetric: $wire.entangle('l3RouteFormMetric'),

                    isValidIpv4(ip) {
                        return /^(25[0-5]|2[0-4]\d|1?\d\d?)\.(25[0-5]|2[0-4]\d|1?\d\d?)\.(25[0-5]|2[0-4]\d|1?\d\d?)\.(25[0-5]|2[0-4]\d|1?\d\d?)$/.test(String(ip || '').trim());
                    },

                    defaultTomorrowLocal() {
                        const d = new Date(Date.now() + 86400000);
                        const pad = (n) => String(n).padStart(2, '0');
                        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                    },

                    openInspectSession(sessionName) {
                        this.inspectSessionTitle = sessionName || 'Sesión';
                        this.inspectSession = null;
                        this.inspectLoading = true;
                        this.inspectModalOpen = true;
                        $wire.inspeccionarSesion(sessionName).finally(() => {
                            this.inspectLoading = false;
                        });
                    },

                    closeInspectModal() {
                        this.inspectModalOpen = false;
                        this.inspectLoading = false;
                        this.inspectSession = null;
                    },

                    openCreateUserModal(hub = null) {
                        this.newUserHub = hub || this.userHubFilter || this.selectedHub || defaults.defaultHub || 'DEFAULT';
                        this.newUsername = '';
                        this.newRealname = '';
                        this.newPassword = '';
                        this.newNote = '';
                        this.isCreatingUser = true;
                    },

                    openTimedDisableModal(username, hub = null) {
                        this.userToTimedDisable = {
                            username: username,
                            hub_name: hub || this.selectedHub || defaults.defaultHub || 'DEFAULT',
                        };
                        this.disableDurationPreset = '60';
                        this.disableUntilCustom = this.defaultTomorrowLocal();
                        this.disableReason = '';
                    },

                    openHubModal(hubName, subTab = 'nat_dhcp') {
                        const targetHub = String(hubName || '').trim();
                        if (!targetHub) return;

                        this.managingHub = targetHub;
                        this.hubManageTab = subTab || 'nat_dhcp';
                        this.hubRouteError = '';
                        this.newHubRouteNetwork = '';

                        // Pre-rellenado instantáneo (0ms) desde datos en memoria mientras llega RPC paralelo
                        const cachedHubRoutes = Array.isArray($wire.allHubRoutes) ? $wire.allHubRoutes : [];
                        const matchedRouteEntry = cachedHubRoutes.find((r) => String(r.hub_name || '').toLowerCase() === targetHub.toLowerCase());
                        if (matchedRouteEntry) {
                            this.hubRoutesList = Array.isArray(matchedRouteEntry.routes)
                                ? matchedRouteEntry.routes.map((r) => ({ ...r }))
                                : [];
                            this.hubApplyPushRoutes = Boolean(matchedRouteEntry.apply_dhcp_push_routes);
                        } else {
                            this.hubRoutesList = [];
                        }

                        this.hubLoading = true;
                        $wire.abrirGestionHub(targetHub).finally(() => {
                            this.hubLoading = false;
                        });
                    },

                    closeHubModal() {
                        this.managingHub = null;
                        this.hubLoading = false;
                        this.hubRouteError = '';
                    },

                    addHubRouteLocal() {
                        const net = String(this.newHubRouteNetwork || '').trim();
                        const mask = String(this.newHubRouteMask || '').trim();
                        const gw = String(this.newHubRouteGateway || '').trim();

                        if (!this.isValidIpv4(net) || !this.isValidIpv4(mask) || !this.isValidIpv4(gw)) {
                            this.hubRouteError = 'Red destino, máscara de subred y gateway deben ser direcciones IPv4 válidas.';
                            return;
                        }

                        this.hubRouteError = '';
                        const list = Array.isArray(this.hubRoutesList) ? this.hubRoutesList : [];
                        this.hubRoutesList = [...list, { network: net, mask: mask, gateway: gw }];
                        this.newHubRouteNetwork = '';
                    },

                    removeHubRouteLocal(idx) {
                        const list = Array.isArray(this.hubRoutesList) ? [...this.hubRoutesList] : [];
                        list.splice(idx, 1);
                        this.hubRoutesList = list;
                    },

                    openCreateHubModal() {
                        this.newHubName = '';
                        this.newHubPassword = '';
                        this.isCreatingHub = true;
                    },

                    confirmDeleteHub(hubName) {
                        if (String(hubName || '').toUpperCase() === 'DEFAULT') {
                            $wire.confirmarEliminarHub(hubName);
                            return;
                        }
                        this.hubToDelete = hubName;
                    },

                    openCreateL3SwitchModal() {
                        this.newL3SwitchName = '';
                        this.isCreatingL3Switch = true;
                    },

                    openNewL3IfModal(switchName = '') {
                        const switches = Array.isArray($wire.l3Switches) ? $wire.l3Switches : [];
                        const hubs = Array.isArray($wire.virtualHubs) ? $wire.virtualHubs : [];
                        this.editingL3IfOriginal = null;
                        this.l3IfFormSwitch = switchName || this.l3SwitchFilter || (switches[0]?.name || '');
                        this.l3IfFormHub = hubs[0]?.hub_name || 'DEFAULT';
                        this.l3IfFormIp = '';
                        this.l3IfFormMask = '255.255.255.0';
                        this.isEditingL3If = true;
                    },

                    openEditL3IfModal(switchName, hubName, ip, mask) {
                        this.editingL3IfOriginal = {
                            switch_name: switchName,
                            hub_name: hubName,
                            ip_address: ip,
                            subnet_mask: mask,
                        };
                        this.l3IfFormSwitch = switchName;
                        this.l3IfFormHub = hubName;
                        this.l3IfFormIp = ip;
                        this.l3IfFormMask = mask;
                        this.isEditingL3If = true;
                    },

                    openNewL3RouteModal(switchName = '') {
                        const switches = Array.isArray($wire.l3Switches) ? $wire.l3Switches : [];
                        this.editingL3RouteOriginal = null;
                        this.l3RouteFormSwitch = switchName || this.l3SwitchFilter || (switches[0]?.name || '');
                        this.l3RouteFormNetwork = '';
                        this.l3RouteFormMask = '255.255.255.0';
                        this.l3RouteFormGateway = '';
                        this.l3RouteFormMetric = 1;
                        this.isEditingL3Route = true;
                    },

                    openEditL3RouteModal(switchName, network, mask, gateway, metric) {
                        const parsedMetric = Math.max(1, parseInt(metric, 10) || 1);
                        this.editingL3RouteOriginal = {
                            switch_name: switchName,
                            network_address: network,
                            subnet_mask: mask,
                            gateway_address: gateway,
                            metric: parsedMetric,
                        };
                        this.l3RouteFormSwitch = switchName;
                        this.l3RouteFormNetwork = network;
                        this.l3RouteFormMask = mask;
                        this.l3RouteFormGateway = gateway;
                        this.l3RouteFormMetric = parsedMetric;
                        this.isEditingL3Route = true;
                    },

                    closeTopModal() {
                        if (this.userToTimedDisable) { this.userToTimedDisable = null; return; }
                        if (this.isEditingL3Route) { this.isEditingL3Route = false; this.editingL3RouteOriginal = null; return; }
                        if (this.isEditingL3If) { this.isEditingL3If = false; this.editingL3IfOriginal = null; return; }
                        if (this.isCreatingL3Switch) { this.isCreatingL3Switch = false; return; }
                        if (this.hubToDelete) { this.hubToDelete = null; return; }
                        if (this.isCreatingHub) { this.isCreatingHub = false; return; }
                        if (this.isCreatingUser) { this.isCreatingUser = false; return; }
                        if (this.inspectModalOpen || this.inspectSession) { this.closeInspectModal(); return; }
                        if (this.managingHub) { this.closeHubModal(); }
                    },
                };
            };
        }
    </script>

    <div class="se-container"
         x-data="softetherFastPanel($wire, { defaultHub: {{ \Illuminate\Support\Js::from($connectionDetails['hub'] ?? 'DEFAULT') }} })"
         @keydown.escape.window="closeTopModal()">
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
                        @if($serverStatus['is_live'] ?? false)
                            <span class="se-badge se-badge-emerald" title="El servidor SoftEther está respondiendo a las consultas JSON-RPC en tiempo real">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                                En línea (RPC OK): {{ $connectionDetails['server_name'] }}
                            </span>
                        @elseif($connectionDetails['is_configured'] ?? false)
                            <span class="se-badge se-badge-rose" title="Credenciales presentes en .env, pero el servidor no responde a las llamadas JSON-RPC">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                                Sin respuesta RPC: {{ $connectionDetails['server_name'] }}
                            </span>
                        @else
                            <span class="se-badge se-badge-amber">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                                Sin configurar (.env): {{ $connectionDetails['server_name'] }}
                            </span>
                        @endif

                        <span class="se-badge se-badge-sky">
                            Hubs: {{ !empty($virtualHubs) ? count($virtualHubs) : 1 }}
                        </span>

                        @if(!empty($virtualHubs) && count($virtualHubs) > 1)
                            <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <label style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">Hub Activo:</label>
                                <select wire:change="selectHub($event.target.value)" 
                                        style="background: #0f172a; color: #38bdf8; border: 1px solid #0284c7; border-radius: 9999px; padding: 0.2rem 0.65rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; outline: none;">
                                    @foreach($virtualHubs as $vh)
                                        <option value="{{ $vh['hub_name'] }}" {{ ($connectionDetails['hub'] ?? '') === $vh['hub_name'] ? 'selected' : '' }}>
                                            {{ $vh['hub_name'] }} ({{ $vh['active_sessions'] ?? 0 }} ses.)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <span class="se-badge se-badge-sky">
                                Hub Activo: {{ $connectionDetails['hub'] ?? 'DEFAULT' }}
                            </span>
                        @endif

                        <span class="se-badge se-badge-sky">
                            Host: {{ $connectionDetails['host'] ?? '127.0.0.1' }}:{{ $connectionDetails['port'] ?? 5555 }}
                        </span>

                        <span wire:loading wire:target="selectServer, selectHub, loadData" class="se-badge se-badge-amber">
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

            <!-- Panel de Alerta si el servidor no responde RPC -->
            @if(!($serverStatus['is_live'] ?? false) && ($connectionDetails['is_configured'] ?? false))
                <div style="margin-top: 1.25rem; padding: 1rem 1.25rem; border-radius: 0.75rem; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.35); color: #fecdd3; font-size: 0.85rem;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #fb7185; flex-shrink: 0; margin-top: 0.15rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div style="flex: 1;">
                            <strong style="color: #ffffff; font-size: 0.95rem;">SoftEther en {{ $connectionDetails['server_name'] }} no responde a la API JSON-RPC</strong>
                            <div style="margin-top: 0.35rem; color: #fca5a5; font-family: monospace; font-size: 0.8rem; background: rgba(0, 0, 0, 0.25); padding: 0.35rem 0.65rem; border-radius: 0.35rem;">
                                {{ $serverStatus['error_message'] ?? $connectionDetails['last_error'] ?? 'Tiempo de espera agotado al conectar al puerto ' . ($connectionDetails['port'] ?? 5555) . ' en ' . ($connectionDetails['host'] ?? '') }}
                            </div>
                            <div style="margin-top: 0.65rem; color: #cbd5e1; font-size: 0.8rem; line-height: 1.5;">
                                <strong>Diagnóstico y solución:</strong>
                                <ul style="margin: 0.35rem 0 0.5rem 1.25rem; padding: 0; list-style-type: disc;">
                                    <li><strong>Si el error es HTTP 404 en el puerto 443:</strong> En el puerto 443 de este VPS está corriendo un servidor web <strong>Apache</strong>. SoftEther VPN está corriendo en otro puerto (habitualmente <code style="color: #38bdf8;">5555</code> o <code style="color: #38bdf8;">992</code>).</li>
                                    <li><strong>Comprobar en qué puertos escucha SoftEther:</strong> En la terminal del VPS 2 ejecuta: <code style="color: #38bdf8;">sudo ss -tulpn | grep vpnserver</code>.</li>
                                    <li><strong>Abrir el puerto de SoftEther en el firewall del VPS 2:</strong> Ejecuta en VPS 2: <code style="color: #38bdf8;">sudo ufw allow 5555/tcp && sudo ufw allow 992/tcp</code>.</li>
                                    <li><strong>Configurar el puerto en .env:</strong> En <code style="color: #38bdf8;">/opt/dataplus-platform/.env</code> configura <code style="color: #38bdf8;">SOFTETHER_VPS2_PORT=5555</code> (o <code style="color: #38bdf8;">992</code>).</li>
                                </ul>
                            </div>
                            <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <button type="button" wire:click="testConnection" class="se-btn se-btn-sky" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                    Ejecutar Diagnóstico Paso a Paso
                                </button>
                                <button type="button" wire:click="loadData(true, true)" class="se-btn" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                                    Reintentar Conexión
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($testResult)
                <div style="margin-top: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)' }}; border: 1px solid {{ $testResult['success'] ? 'rgba(16, 185, 129, 0.3)' : 'rgba(245, 158, 11, 0.3)' }}; color: {{ $testResult['success'] ? '#6ee7b7' : '#fde68a' }}; font-size: 0.825rem;">
                    <strong>{{ $testResult['success'] ? 'Resultado del Diagnóstico:' : 'Aviso de Conexión:' }}</strong>
                    {{ $testResult['message'] }}
                </div>
            @endif
        </div>

        <!-- Navigation Tabs (conmutación instantánea 0ms en cliente) -->
        <div class="se-tabs">
            <button type="button" @click="activeTab = 'resumen'" :class="{ 'active': activeTab === 'resumen' }" class="se-tab-btn {{ $activeTab === 'resumen' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span>Resumen & Servidor</span>
            </button>

            <button type="button" @click="activeTab = 'sesiones'" :class="{ 'active': activeTab === 'sesiones' }" class="se-tab-btn {{ $activeTab === 'sesiones' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" /></svg>
                <span>Sesiones en Vivo ({{ count($activeSessions) }})</span>
            </button>

            <button type="button" @click="activeTab = 'usuarios'" :class="{ 'active': activeTab === 'usuarios' }" class="se-tab-btn {{ $activeTab === 'usuarios' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <span>Cuentas VPN ({{ count(!empty($allVpnUsers) ? $allVpnUsers : $vpnUsers) }})</span>
            </button>

            <button type="button" @click="activeTab = 'hubs'" :class="{ 'active': activeTab === 'hubs' }" class="se-tab-btn {{ $activeTab === 'hubs' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                <span>Virtual Hubs & SecureNAT ({{ count($virtualHubs) }})</span>
            </button>

            <button type="button" @click="activeTab = 'l3'" :class="{ 'active': activeTab === 'l3' }" class="se-tab-btn {{ $activeTab === 'l3' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                <span>Interfaces L3 & Rutas ({{ count($l3Interfaces) }})</span>
            </button>

            <button type="button" @click="activeTab = 'config'" :class="{ 'active': activeTab === 'config' }" class="se-tab-btn {{ $activeTab === 'config' ? 'active' : '' }}">
                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                <span>Configuración (.env)</span>
            </button>
        </div>

        <!-- TAB 1: RESUMEN DEL SERVIDOR -->
        <div x-show="activeTab === 'resumen'" x-cloak>
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
        </div>

        <!-- TAB 2: SESIONES EN VIVO -->
        <div x-show="activeTab === 'sesiones'" x-cloak>
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
                                                    data-session="{{ $sesion['session_name'] }}"
                                                    @click="openInspectSession($el.dataset.session)" 
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
        </div>

        <!-- TAB 3: CUENTAS DE CLIENTES VPN (RELACIONADAS CON VIRTUAL HUB) -->
        <div x-show="activeTab === 'usuarios'" x-cloak>
            @php
                $totalAllUsers = count(!empty($allVpnUsers) ? $allVpnUsers : $vpnUsers);
                $filteredUsers = $this->getFilteredVpnUsers();
                $totalFilteredUsers = count($filteredUsers);
                $paginatedUsers = $this->getPaginatedVpnUsers();
                $totalUserPages = $this->getTotalUserPages();
            @endphp
            <div class="se-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                            Cuentas de Usuarios VPN ({{ $connectionDetails['server_name'] }})
                        </h3>
                        <p style="color: #94a3b8; font-size: 0.8rem; margin: 0.25rem 0 0 0;">
                            Cuentas VPN vinculadas por Virtual Hub en todo el servidor ({{ $totalAllUsers }} cuentas totales).
                        </p>
                    </div>
                    <button type="button" @click="openCreateUserModal()" class="se-btn se-btn-sky">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        <span>Crear Usuario VPN</span>
                    </button>
                </div>

                <!-- Barra de Búsqueda por Usuario / Virtual Hub y Filtros -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.65rem; align-items: center; margin-bottom: 0.85rem; padding: 0.75rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem;">
                    <div style="position: relative; flex: 1; min-width: 240px;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text"
                               wire:model.live.debounce.150ms="userSearch"
                               placeholder="Buscar por usuario, nombre o Virtual Hub..."
                               style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 0.5rem; padding: 0.45rem 2rem 0.45rem 2.25rem; color: #ffffff; font-size: 0.8rem; outline: none;" />
                        @if(!empty($userSearch))
                            <button type="button" wire:click="$set('userSearch', '')" style="position: absolute; right: 0.65rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; cursor: pointer;">✕</button>
                        @endif
                    </div>

                    <select wire:model.live="userHubFilter"
                            style="background: #0f172a; border: 1px solid #334155; border-radius: 0.5rem; padding: 0.45rem 0.75rem; color: #38bdf8; font-size: 0.8rem; font-weight: 600; outline: none; cursor: pointer;">
                        <option value="">Todos los Virtual Hubs ({{ count($virtualHubs) }})</option>
                        @foreach($virtualHubs as $vh)
                            <option value="{{ $vh['hub_name'] }}">{{ $vh['hub_name'] }} ({{ $vh['users_count'] ?? 0 }})</option>
                        @endforeach
                    </select>

                    <select wire:model.live="userStatusFilter"
                            style="background: #0f172a; border: 1px solid #334155; border-radius: 0.5rem; padding: 0.45rem 0.75rem; color: #e2e8f0; font-size: 0.8rem; outline: none; cursor: pointer;">
                        <option value="todos">Todos los estados</option>
                        <option value="habilitados">Solo Habilitados</option>
                        <option value="deshabilitados">Solo Deshabilitados</option>
                        <option value="temporal">Deshabilitados Temporalmente</option>
                    </select>

                    @if(!empty($userSearch) || !empty($userHubFilter) || $userStatusFilter !== 'todos')
                        <button type="button" wire:click="limpiarFiltrosUsuarios" class="se-btn" style="background: rgba(255,255,255,0.08); color: #cbd5e1; font-size: 0.75rem;">
                            Limpiar filtros
                        </button>
                    @endif
                </div>

                <!-- Resumen de conteo -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.75rem; color: #94a3b8;">
                    <span>
                        Mostrando <strong style="color: #ffffff;">{{ count($paginatedUsers) }}</strong> de <strong style="color: #38bdf8;">{{ $totalFilteredUsers }}</strong> cuentas coincidentes
                    </span>
                    @if($totalUserPages > 1)
                        <span>Página <strong style="color: #38bdf8;">{{ $userPage }}</strong> de {{ $totalUserPages }}</span>
                    @endif
                </div>

                <div style="overflow-x: auto;">
                    <table class="se-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Virtual Hub</th>
                                <th>Nombre / Descripción</th>
                                <th>Autenticación</th>
                                <th>Estado</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedUsers as $user)
                                @php
                                    $uHub = $user['hub_name'] ?? ($connectionDetails['hub'] ?? 'DEFAULT');
                                    $isActive = (bool) ($user['is_active'] ?? true);
                                    $isTimed = (bool) ($user['is_timed_disabled'] ?? false);
                                @endphp
                                <tr>
                                    <td style="font-family: monospace; font-weight: 600; color: #38bdf8;">
                                        <div>{{ $user['username'] }}</div>
                                        @if(!empty($user['currently_connected']))
                                            <span style="font-size: 0.65rem; color: #34d399; font-family: sans-serif;">● Conectado ahora</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button"
                                                data-hub="{{ $uHub }}"
                                                @click="openHubModal($el.dataset.hub, 'users')"
                                                class="se-badge se-badge-sky"
                                                style="cursor: pointer; font-family: monospace;"
                                                title="Gestionar Virtual Hub {{ $uHub }}">
                                            {{ $uHub }}
                                        </button>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #ffffff;">{{ $user['real_name'] }}</div>
                                        <div style="font-size: 0.75rem; color: #64748b;">{{ $user['note'] ?? '' }}</div>
                                    </td>
                                    <td>{{ $user['auth_type'] ?? 'Contraseña' }}</td>
                                    <td>
                                        @if($isActive)
                                            <span class="se-badge se-badge-emerald">Habilitado</span>
                                        @elseif($isTimed)
                                            <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                                <span class="se-badge se-badge-amber">Deshab. Temporal</span>
                                                <span style="font-size: 0.7rem; color: #fbbf24; font-family: monospace;">
                                                    Hasta: {{ $user['disabled_until'] }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="se-badge se-badge-rose">Deshabilitado</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.75rem;">{{ $user['last_login'] ?? 'Nunca' }}</td>
                                    <td>
                                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                            @if($isActive)
                                                <button type="button"
                                                        data-user="{{ $user['username'] }}"
                                                        data-hub="{{ $uHub }}"
                                                        @click="openTimedDisableModal($el.dataset.user, $el.dataset.hub)"
                                                        class="se-btn se-btn-amber"
                                                        style="padding: 0.35rem 0.65rem; font-size: 0.75rem;"
                                                        title="Deshabilitar por tiempo definido o indefinido">
                                                    Deshabilitar
                                                </button>
                                            @else
                                                <button type="button"
                                                        wire:click="toggleUsuario('{{ $user['username'] }}', true, '{{ $uHub }}')"
                                                        class="se-btn se-btn-emerald"
                                                        style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
                                                    Habilitar
                                                </button>
                                            @endif
                                            <button type="button"
                                                    wire:click="eliminarUsuario('{{ $user['username'] }}', '{{ $uHub }}')"
                                                    wire:confirm="¿Seguro que desea eliminar permanentemente al usuario {{ $user['username'] }} del Hub {{ $uHub }}?"
                                                    class="se-btn se-btn-rose"
                                                    style="padding: 0.35rem 0.65rem; font-size: 0.75rem;"
                                                    title="Eliminar usuario de SoftEther">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                        @if(!empty($userSearch) || !empty($userHubFilter) || $userStatusFilter !== 'todos')
                                            No se encontraron cuentas VPN con los filtros aplicados.
                                            <div style="margin-top: 0.75rem;">
                                                <button type="button" wire:click="limpiarFiltrosUsuarios" class="se-btn se-btn-sky" style="font-size: 0.75rem;">
                                                    Limpiar filtros
                                                </button>
                                            </div>
                                        @else
                                            No se encontraron usuarios VPN registrados en este servidor.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación de Cuentas VPN -->
                @if($totalUserPages > 1)
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid #1e293b; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="font-size: 0.75rem; color: #94a3b8;">
                            Página {{ $userPage }} de {{ $totalUserPages }} ({{ $totalFilteredUsers }} cuentas)
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                            <button type="button"
                                    wire:click="setUserPage({{ $userPage - 1 }})"
                                    {{ $userPage <= 1 ? 'disabled' : '' }}
                                    class="se-btn"
                                    style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: {{ $userPage <= 1 ? '#090e1a' : '#1e293b' }}; color: {{ $userPage <= 1 ? '#475569' : '#e2e8f0' }}; cursor: {{ $userPage <= 1 ? 'not-allowed' : 'pointer' }};">
                                ◀ Anterior
                            </button>
                            @for($up = max(1, $userPage - 2); $up <= min($totalUserPages, $userPage + 2); $up++)
                                <button type="button"
                                        wire:click="setUserPage({{ $up }})"
                                        style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer; border: 1px solid {{ $up === $userPage ? '#0284c7' : '#334155' }}; background: {{ $up === $userPage ? 'rgba(14, 165, 233, 0.25)' : '#0f172a' }}; color: {{ $up === $userPage ? '#38bdf8' : '#94a3b8' }};">
                                    {{ $up }}
                                </button>
                            @endfor
                            <button type="button"
                                    wire:click="setUserPage({{ $userPage + 1 }})"
                                    {{ $userPage >= $totalUserPages ? 'disabled' : '' }}
                                    class="se-btn"
                                    style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: {{ $userPage >= $totalUserPages ? '#090e1a' : '#1e293b' }}; color: {{ $userPage >= $totalUserPages ? '#475569' : '#e2e8f0' }}; cursor: {{ $userPage >= $totalUserPages ? 'not-allowed' : 'pointer' }};">
                                Siguiente ▶
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 4: VIRTUAL HUBS & SECURENAT -->
        <div x-show="activeTab === 'hubs'" x-cloak>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                <div class="se-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div>
                            <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                                Virtual Hubs en {{ $connectionDetails['server_name'] }}
                            </h3>
                            <span style="font-size: 0.75rem; color: #94a3b8;">{{ count($virtualHubs) }} detectado(s) en SoftEther</span>
                        </div>
                        <button type="button" @click="openCreateHubModal()" class="se-btn se-btn-emerald" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                            <svg style="width: 0.9rem; height: 0.9rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            <span>Crear Virtual Hub</span>
                        </button>
                    </div>

                    <!-- Buscador de Virtual Hubs -->
                    <div style="margin-bottom: 0.75rem; display: flex; gap: 0.5rem; align-items: center;">
                        <div style="position: relative; flex: 1;">
                            <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none;">
                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" 
                                   wire:model.live.debounce.150ms="hubSearch" 
                                   placeholder="Buscar Virtual Hub por nombre o estado..." 
                                   style="width: 100%; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem; padding: 0.45rem 2rem 0.45rem 2.25rem; color: #ffffff; font-size: 0.8rem; outline: none;" />
                            @if(!empty($hubSearch))
                                <button type="button" 
                                        wire:click="limpiarBusquedaHub" 
                                        style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #94a3b8; font-size: 0.9rem; cursor: pointer;">
                                    ✕
                                </button>
                            @endif
                        </div>
                        @if(!empty($hubSearch))
                            <button type="button" wire:click="limpiarBusquedaHub" class="se-btn" style="background: rgba(255,255,255,0.06); color: #94a3b8; font-size: 0.75rem; padding: 0.45rem 0.75rem;">
                                Limpiar
                            </button>
                        @endif
                    </div>

                    @php
                        $allFilteredHubs = $this->getFilteredVirtualHubs();
                        $totalFiltered = count($allFilteredHubs);
                        $paginatedHubs = $this->getPaginatedVirtualHubs();
                        $totalPages = $this->getTotalHubPages();
                    @endphp

                    <!-- Resumen de resultados y página -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.75rem; color: #94a3b8;">
                        <span>
                            @if(!empty($hubSearch))
                                Filtrados: <strong style="color: #38bdf8;">{{ $totalFiltered }}</strong> de {{ count($virtualHubs) }} hubs
                            @else
                                Mostrando <strong style="color: #ffffff;">{{ count($paginatedHubs) }}</strong> de {{ count($virtualHubs) }} hubs
                            @endif
                        </span>
                        @if($totalPages > 1)
                            <span>Página <strong style="color: #38bdf8;">{{ $hubPage }}</strong> de {{ $totalPages }}</span>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @forelse($paginatedHubs as $hub)
                            <div style="background: #090e1a; border: 1px solid {{ ($connectionDetails['hub'] ?? '') === $hub['hub_name'] ? 'rgba(14, 165, 233, 0.5)' : '#1e293b' }}; border-radius: 0.5rem; padding: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="font-weight: 700; color: #38bdf8; font-size: 1.05rem;">{{ $hub['hub_name'] }}</div>
                                        @if(($connectionDetails['hub'] ?? '') === $hub['hub_name'])
                                            <span class="se-badge se-badge-sky" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">Hub Activo</span>
                                        @endif
                                    </div>
                                    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                        <span class="se-badge {{ ($hub['status'] ?? '') === 'Online' ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                            {{ $hub['status'] ?? 'Desconocido' }}
                                        </span>
                                        @if(($connectionDetails['hub'] ?? '') !== $hub['hub_name'])
                                            <button type="button" 
                                                    wire:click="selectHub('{{ $hub['hub_name'] }}')"
                                                    class="se-btn se-btn-sky" style="padding: 0.2rem 0.55rem; font-size: 0.75rem;">
                                                Seleccionar
                                            </button>
                                        @endif
                                        @if(strtoupper($hub['hub_name']) !== 'DEFAULT')
                                            <button type="button" 
                                                    data-hub="{{ $hub['hub_name'] }}"
                                                    @click="confirmDeleteHub($el.dataset.hub)"
                                                    class="se-btn se-btn-rose" 
                                                    style="padding: 0.2rem 0.55rem; font-size: 0.75rem;"
                                                    title="Eliminar Virtual Hub '{{ $hub['hub_name'] }}'">
                                                <svg style="width: 0.85rem; height: 0.85rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                <span>Eliminar</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem; color: #94a3b8;">
                                    <div>Usuarios: <strong style="color: #ffffff;">{{ $hub['users_count'] ?? 0 }}</strong></div>
                                    <div>Sesiones: <strong style="color: #ffffff;">{{ $hub['active_sessions'] ?? 0 }}</strong></div>
                                    <div>Tablas MAC: <strong style="color: #ffffff;">{{ $hub['mac_tables'] ?? 0 }}</strong></div>
                                    <div>Tablas IP: <strong style="color: #ffffff;">{{ $hub['ip_tables'] ?? 0 }}</strong></div>
                                    <div>Tráfico: <strong style="color: #38bdf8;">{{ $hub['traffic'] ?? '0 B' }}</strong></div>
                                    <div>Rutas SecureNAT: <strong style="color: {{ ($hub['push_routes_count'] ?? 0) > 0 ? '#34d399' : '#94a3b8' }};">{{ $hub['push_routes_count'] ?? 0 }}</strong></div>
                                </div>
                                <!-- Acciones de gestión directa del Virtual Hub (apertura 0ms) -->
                                <div style="display: flex; gap: 0.45rem; flex-wrap: wrap; margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px solid #1e293b;">
                                    <button type="button"
                                            data-hub="{{ $hub['hub_name'] }}"
                                            @click="openHubModal($el.dataset.hub, 'nat_dhcp')"
                                            class="se-btn se-btn-emerald"
                                            style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                        ⚙ DHCP & SecureNAT
                                    </button>
                                    <button type="button"
                                            data-hub="{{ $hub['hub_name'] }}"
                                            @click="openHubModal($el.dataset.hub, 'routes')"
                                            class="se-btn se-btn-amber"
                                            style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                        🛣 Rutas ({{ $hub['push_routes_count'] ?? 0 }})
                                    </button>
                                    <button type="button"
                                            data-hub="{{ $hub['hub_name'] }}"
                                            @click="openHubModal($el.dataset.hub, 'users')"
                                            class="se-btn se-btn-sky"
                                            style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                        👥 Usuarios ({{ $hub['users_count'] ?? 0 }})
                                    </button>
                                    <button type="button"
                                            data-hub="{{ $hub['hub_name'] }}"
                                            @click="openHubModal($el.dataset.hub, 'logs')"
                                            class="se-btn se-btn-indigo"
                                            style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                        📋 Logs
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #94a3b8; padding: 2rem; background: #090e1a; border: 1px dashed #334155; border-radius: 0.5rem;">
                                <svg style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem auto; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                @if(!empty($hubSearch))
                                    <div>No se encontraron Virtual Hubs que coincidan con "{{ $hubSearch }}".</div>
                                    <button type="button" wire:click="limpiarBusquedaHub" class="se-btn se-btn-sky" style="margin-top: 0.75rem; font-size: 0.75rem; padding: 0.3rem 0.75rem;">
                                        Limpiar búsqueda
                                    </button>
                                @else
                                    <div>No se detectaron Virtual Hubs activos en este servidor.</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Verifique la conexión RPC con el VPS secundario.</div>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <!-- Controles de Paginación -->
                    @if($totalPages > 1)
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #1e293b; flex-wrap: wrap; gap: 0.5rem;">
                            <div style="font-size: 0.75rem; color: #94a3b8;">
                                Página {{ $hubPage }} de {{ $totalPages }} ({{ $totalFiltered }} hubs)
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <button type="button" 
                                        wire:click="setHubPage({{ $hubPage - 1 }})" 
                                        {{ $hubPage <= 1 ? 'disabled' : '' }}
                                        class="se-btn" 
                                        style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: {{ $hubPage <= 1 ? '#090e1a' : '#1e293b' }}; color: {{ $hubPage <= 1 ? '#475569' : '#e2e8f0' }}; cursor: {{ $hubPage <= 1 ? 'not-allowed' : 'pointer' }};">
                                    ◀ Anterior
                                </button>

                                @for($p = max(1, $hubPage - 2); $p <= min($totalPages, $hubPage + 2); $p++)
                                    <button type="button" 
                                            wire:click="setHubPage({{ $p }})" 
                                            style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer; border: 1px solid {{ $p === $hubPage ? '#0284c7' : '#334155' }}; background: {{ $p === $hubPage ? 'rgba(14, 165, 233, 0.25)' : '#0f172a' }}; color: {{ $p === $hubPage ? '#38bdf8' : '#94a3b8' }};">
                                        {{ $p }}
                                    </button>
                                @endfor

                                <button type="button" 
                                        wire:click="setHubPage({{ $hubPage + 1 }})" 
                                        {{ $hubPage >= $totalPages ? 'disabled' : '' }}
                                        class="se-btn" 
                                        style="padding: 0.3rem 0.65rem; font-size: 0.75rem; background: {{ $hubPage >= $totalPages ? '#090e1a' : '#1e293b' }}; color: {{ $hubPage >= $totalPages ? '#475569' : '#e2e8f0' }}; cursor: {{ $hubPage >= $totalPages ? 'not-allowed' : 'pointer' }};">
                                    Siguiente ▶
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="se-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #1e293b; padding-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 0;">
                            Estado SecureNAT & DHCP Virtual (Hub: {{ $connectionDetails['hub'] }})
                        </h3>
                        <div style="display: flex; gap: 0.45rem; flex-wrap: wrap;">
                            <button type="button"
                                    wire:click="toggleHubSecureNat({{ !empty($secureNat['securenat_enabled']) ? 'false' : 'true' }}, '{{ $connectionDetails['hub'] }}')"
                                    class="se-btn {{ !empty($secureNat['securenat_enabled']) ? 'se-btn-amber' : 'se-btn-emerald' }}"
                                    style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                {{ !empty($secureNat['securenat_enabled']) ? 'Desactivar SecureNAT' : 'Activar SecureNAT' }}
                            </button>
                            <button type="button"
                                    data-hub="{{ $connectionDetails['hub'] }}"
                                    @click="openHubModal($el.dataset.hub, 'nat_dhcp')"
                                    class="se-btn se-btn-sky"
                                    style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                Configurar DHCP / NAT
                            </button>
                        </div>
                    </div>
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

                        <!-- Rutas Estáticas SecureNAT del Hub Activo -->
                        <div style="margin-top: 0.5rem; padding: 0.75rem; border-radius: 0.5rem; background: #090e1a; border: 1px solid #1e293b;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; gap: 0.5rem;">
                                <span style="color: #cbd5e1; font-weight: 600; font-size: 0.8rem;">
                                    Rutas Estáticas (DHCP Push Routes)
                                </span>
                                <button type="button"
                                        data-hub="{{ $connectionDetails['hub'] }}"
                                        @click="openHubModal($el.dataset.hub, 'routes')"
                                        class="se-btn se-btn-amber"
                                        style="padding: 0.2rem 0.55rem; font-size: 0.72rem;">
                                    Editar Rutas
                                </button>
                            </div>
                            @if(!empty($secureNat['dhcp_push_routes_list']))
                                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                    @foreach($secureNat['dhcp_push_routes_list'] as $rItem)
                                        <div style="font-family: monospace; font-size: 0.75rem; color: #38bdf8; background: #0f172a; border: 1px solid #1e293b; border-radius: 0.35rem; padding: 0.3rem 0.5rem;">
                                            {{ $rItem['network'] }} / {{ $rItem['mask'] }} → <strong style="color: #34d399;">{{ $rItem['gateway'] }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: #64748b;">
                                    Sin rutas estáticas push configuradas en este Virtual Hub.
                                </div>
                            @endif
                        </div>

                        @if(empty($secureNat['nat_enabled']) && empty($secureNat['dhcp_enabled']))
                            <div style="padding: 0.75rem; border-radius: 0.5rem; background: rgba(148, 163, 184, 0.08); border: 1px dashed #334155; color: #94a3b8; font-size: 0.775rem; margin-top: 0.5rem; line-height: 1.45;">
                                <strong style="color: #e2e8f0;">Nota técnica sobre SecureNAT:</strong> En este hub, SecureNAT (Virtual NAT y DHCP de SoftEther) está <em>deshabilitado</em>. Esto es lo habitual cuando el servidor utiliza <strong>Local Bridge (TAP)</strong> conectado a la interfaz física de red del VPS, o cuando las IPs las asigna un DHCP externo/dnsmasq del sistema operativo en lugar de la función interna de SoftEther.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: INTERFACES L3 (SWITCHES CAPA 3) & RUTAS SECURENAT POR HUB -->
        <div x-show="activeTab === 'l3'" x-cloak>
            @php
                $filteredL3Interfaces = $this->getFilteredL3Interfaces();
                $filteredL3Tables = $this->getFilteredL3Tables();
                $filteredHubRoutes = $this->getFilteredAllHubRoutes();
            @endphp
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- SECCIÓN A: SWITCHES E INTERFACES LAYER 3 -->
                <div class="se-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #1e293b; padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0;">
                                Switches Virtuales L3 e Interfaces Capa 3 ({{ $connectionDetails['server_name'] }})
                            </h3>
                            <p style="font-size: 0.78rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
                                Enrutamiento entre Virtual Hubs mediante Virtual Layer 3 Switches ({{ count($l3Switches) }} switch(es), {{ count($l3Interfaces) }} interfaz(ces), {{ count($l3Tables) }} ruta(s)).
                            </p>
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <button type="button" @click="openCreateL3SwitchModal()" class="se-btn se-btn-emerald" style="font-size: 0.75rem;">
                                + Nuevo Switch L3
                            </button>
                            <button type="button" @click="openNewL3IfModal()" class="se-btn se-btn-sky" style="font-size: 0.75rem;">
                                + Nueva Interfaz L3
                            </button>
                            <button type="button" @click="openNewL3RouteModal()" class="se-btn se-btn-indigo" style="font-size: 0.75rem;">
                                + Nueva Ruta L3
                            </button>
                        </div>
                    </div>

                    <!-- Tarjetas resumen de Switches L3 -->
                    @if(!empty($l3Switches))
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0.85rem; margin-bottom: 1.25rem;">
                            @foreach($l3Switches as $sw)
                                <div style="background: #090e1a; border: 1px solid {{ !empty($sw['active']) ? 'rgba(16, 185, 129, 0.35)' : '#1e293b' }}; border-radius: 0.5rem; padding: 0.85rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem; gap: 0.5rem;">
                                        <span style="font-family: monospace; font-weight: 700; color: #38bdf8; font-size: 0.95rem;">{{ $sw['name'] }}</span>
                                        <span class="se-badge {{ !empty($sw['active']) && !empty($sw['online']) ? 'se-badge-emerald' : (!empty($sw['active']) ? 'se-badge-amber' : 'se-badge-rose') }}">
                                            {{ !empty($sw['active']) ? (!empty($sw['online']) ? 'Activo / En línea' : 'Activo (Error/Offline)') : 'Detenido' }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.78rem; color: #94a3b8; margin-bottom: 0.65rem;">
                                        Interfaces: <strong style="color: #ffffff;">{{ count($sw['interfaces'] ?? []) }}</strong> ·
                                        Tablas de rutas: <strong style="color: #ffffff;">{{ count($sw['tables'] ?? []) }}</strong>
                                    </div>
                                    <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                        <button type="button"
                                                wire:click="toggleL3Switch('{{ $sw['name'] }}', {{ !empty($sw['active']) ? 'false' : 'true' }})"
                                                class="se-btn {{ !empty($sw['active']) ? 'se-btn-amber' : 'se-btn-emerald' }}"
                                                style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                            {{ !empty($sw['active']) ? 'Detener' : 'Iniciar' }}
                                        </button>
                                        <button type="button"
                                                data-switch="{{ $sw['name'] }}"
                                                @click="openNewL3IfModal($el.dataset.switch)"
                                                class="se-btn se-btn-sky"
                                                style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                            + Interfaz
                                        </button>
                                        <button type="button"
                                                wire:click="eliminarL3Switch('{{ $sw['name'] }}')"
                                                wire:confirm="¿Seguro que desea eliminar el Switch Virtual L3 '{{ $sw['name'] }}' y todas sus interfaces?"
                                                class="se-btn se-btn-rose"
                                                style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Filtros para Interfaces L3 -->
                    <div style="display: flex; flex-wrap: wrap; gap: 0.65rem; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem;">
                        <input type="text"
                               wire:model.live.debounce.150ms="l3Search"
                               placeholder="Buscar por Switch L3, Virtual Hub, IP o Máscara..."
                               style="flex: 1; min-width: 240px; background: #0f172a; border: 1px solid #334155; border-radius: 0.45rem; padding: 0.45rem 0.75rem; color: #ffffff; font-size: 0.8rem; outline: none;" />
                        <select wire:model.live="l3SwitchFilter"
                                style="background: #0f172a; border: 1px solid #334155; border-radius: 0.45rem; padding: 0.45rem 0.75rem; color: #38bdf8; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                            <option value="">Todos los Switches L3 ({{ count($l3Switches) }})</option>
                            @foreach($l3Switches as $sw)
                                <option value="{{ $sw['name'] }}">{{ $sw['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tabla de Todas las Interfaces L3 -->
                    <h4 style="font-size: 0.85rem; font-weight: 600; color: #38bdf8; margin: 0 0 0.5rem 0;">
                        Todas las Interfaces L3 ({{ count($filteredL3Interfaces) }})
                    </h4>
                    <div style="overflow-x: auto; margin-bottom: 1.5rem;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Switch L3</th>
                                    <th>Estado Switch</th>
                                    <th>Virtual Hub Asignado</th>
                                    <th>Dirección IP L3</th>
                                    <th>Máscara de Subred</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredL3Interfaces as $if)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 700; color: #ffffff;">{{ $if['switch_name'] }}</td>
                                        <td>
                                            <span class="se-badge {{ !empty($if['switch_active']) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                                {{ !empty($if['switch_active']) ? 'Activo' : 'Detenido' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button"
                                                    data-hub="{{ $if['hub_name'] }}"
                                                    @click="openHubModal($el.dataset.hub, 'routes')"
                                                    class="se-badge se-badge-sky"
                                                    style="cursor: pointer; font-family: monospace;">
                                                {{ $if['hub_name'] }}
                                            </button>
                                        </td>
                                        <td style="font-family: monospace; font-weight: 600; color: #34d399;">{{ $if['ip_address'] }}</td>
                                        <td style="font-family: monospace; color: #cbd5e1;">{{ $if['subnet_mask'] }}</td>
                                        <td>
                                            <div style="display: flex; gap: 0.4rem;">
                                                <button type="button"
                                                        data-switch="{{ $if['switch_name'] }}"
                                                        data-hub="{{ $if['hub_name'] }}"
                                                        data-ip="{{ $if['ip_address'] }}"
                                                        data-mask="{{ $if['subnet_mask'] }}"
                                                        @click="openEditL3IfModal($el.dataset.switch, $el.dataset.hub, $el.dataset.ip, $el.dataset.mask)"
                                                        class="se-btn se-btn-sky"
                                                        style="padding: 0.3rem 0.6rem; font-size: 0.73rem;">
                                                    Modificar
                                                </button>
                                                <button type="button"
                                                        wire:click="eliminarInterfazL3('{{ $if['switch_name'] }}', '{{ $if['hub_name'] }}')"
                                                        wire:confirm="¿Eliminar la interfaz L3 del Hub '{{ $if['hub_name'] }}' en el Switch '{{ $if['switch_name'] }}'?"
                                                        class="se-btn se-btn-rose"
                                                        style="padding: 0.3rem 0.6rem; font-size: 0.73rem;">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                                            No se encontraron interfaces L3 registradas en este servidor SoftEther.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabla de Enrutamiento Estático L3 -->
                    <h4 style="font-size: 0.85rem; font-weight: 600; color: #a5b4fc; margin: 0 0 0.5rem 0;">
                        Tabla de Rutas de Switches L3 ({{ count($filteredL3Tables) }})
                    </h4>
                    <div style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Switch L3</th>
                                    <th>Red Destino</th>
                                    <th>Máscara</th>
                                    <th>Gateway</th>
                                    <th>Métrica</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredL3Tables as $tbl)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 700; color: #ffffff;">{{ $tbl['switch_name'] }}</td>
                                        <td style="font-family: monospace; color: #38bdf8;">{{ $tbl['network_address'] }}</td>
                                        <td style="font-family: monospace;">{{ $tbl['subnet_mask'] }}</td>
                                        <td style="font-family: monospace; color: #34d399;">{{ $tbl['gateway_address'] }}</td>
                                        <td style="font-family: monospace;">{{ $tbl['metric'] }}</td>
                                        <td>
                                            <div style="display: flex; gap: 0.4rem;">
                                                <button type="button"
                                                        data-switch="{{ $tbl['switch_name'] }}"
                                                        data-network="{{ $tbl['network_address'] }}"
                                                        data-mask="{{ $tbl['subnet_mask'] }}"
                                                        data-gateway="{{ $tbl['gateway_address'] }}"
                                                        data-metric="{{ (int) ($tbl['metric'] ?? 1) }}"
                                                        @click="openEditL3RouteModal($el.dataset.switch, $el.dataset.network, $el.dataset.mask, $el.dataset.gateway, $el.dataset.metric)"
                                                        class="se-btn se-btn-sky"
                                                        style="padding: 0.3rem 0.6rem; font-size: 0.73rem;">
                                                    Modificar
                                                </button>
                                                <button type="button"
                                                        wire:click="eliminarRutaL3('{{ $tbl['switch_name'] }}', '{{ $tbl['network_address'] }}', '{{ $tbl['subnet_mask'] }}', '{{ $tbl['gateway_address'] }}', {{ (int) ($tbl['metric'] ?? 1) }})"
                                                        wire:confirm="¿Eliminar la ruta L3 {{ $tbl['network_address'] }}/{{ $tbl['subnet_mask'] }} del Switch '{{ $tbl['switch_name'] }}'?"
                                                        class="se-btn se-btn-rose"
                                                        style="padding: 0.3rem 0.6rem; font-size: 0.73rem;">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                                            No hay rutas estáticas configuradas en los Switches L3.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SECCIÓN B: RUTAS SECURENAT CONFIGURADAS POR CADA VIRTUAL HUB -->
                <div class="se-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #1e293b; padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.75rem;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0;">
                                Rutas Configuradas en SecureNAT por Virtual Hub
                            </h3>
                            <p style="font-size: 0.78rem; color: #94a3b8; margin: 0.25rem 0 0 0;">
                                Vista consolidada de las rutas estáticas empujadas por DHCP (DhcpPushRoutes) en cada Virtual Hub.
                            </p>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.150ms="routeHubSearch"
                               placeholder="Filtrar por Virtual Hub o IP de ruta..."
                               style="min-width: 260px; background: #090e1a; border: 1px solid #334155; border-radius: 0.45rem; padding: 0.45rem 0.75rem; color: #ffffff; font-size: 0.8rem; outline: none;" />
                    </div>

                    <div style="overflow-x: auto;">
                        <table class="se-table">
                            <thead>
                                <tr>
                                    <th>Virtual Hub</th>
                                    <th>Push Routes Activo</th>
                                    <th>Rutas Configuradas (Red / Máscara → Gateway)</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filteredHubRoutes as $hr)
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 700; color: #38bdf8;">{{ $hr['hub_name'] }}</td>
                                        <td>
                                            <span class="se-badge {{ !empty($hr['apply_dhcp_push_routes']) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                                {{ !empty($hr['apply_dhcp_push_routes']) ? 'Sí (Aplicadas)' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(!empty($hr['routes']))
                                                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                                    @foreach($hr['routes'] as $r)
                                                        <span style="font-family: monospace; font-size: 0.74rem; padding: 0.2rem 0.5rem; border-radius: 0.35rem; background: #090e1a; border: 1px solid #334155; color: #e2e8f0;">
                                                            <strong style="color: #38bdf8;">{{ $r['network'] }}</strong>/{{ $r['mask'] }} → <strong style="color: #34d399;">{{ $r['gateway'] }}</strong>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="color: #64748b; font-size: 0.75rem;">Sin rutas configuradas</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button"
                                                    data-hub="{{ $hr['hub_name'] }}"
                                                    @click="openHubModal($el.dataset.hub, 'routes')"
                                                    class="se-btn se-btn-amber"
                                                    style="padding: 0.3rem 0.65rem; font-size: 0.73rem;">
                                                Editar Rutas
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                                            No se encontraron configuraciones de SecureNAT para mostrar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: GUIA DE CONFIGURACION & ARCHIVO .ENV (VPS 1 & VPS 2) -->
        <div x-show="activeTab === 'config'" x-cloak>
            <div class="se-card">
                <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background: rgba(14, 165, 233, 0.2); color: #38bdf8;">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 0;">
                            Configuración Multi-Servidor (VPS 1 & VPS 2) en /opt/dataplus-platform/.env
                        </h2>
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0.35rem 0 0 0;">
                            Estructura para gestionar ambos servidores SoftEther sin exponer credenciales en Git.
                        </p>
                    </div>
                </div>

                <div style="background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.3); border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 0.95rem; font-weight: 700; color: #38bdf8; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Valores actualmente cargados por Laravel para {{ $connectionDetails['server_name'] }}
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; font-size: 0.8rem;">
                        <div><span style="color: #94a3b8;">Servidor ID:</span> <strong style="color: #ffffff;">{{ $connectionDetails['server_key'] }}</strong></div>
                        <div><span style="color: #94a3b8;">Host:</span> <strong style="color: #38bdf8; font-family: monospace;">{{ $connectionDetails['host'] ?: 'No definido' }}</strong></div>
                        <div><span style="color: #94a3b8;">Puerto:</span> <strong style="color: #38bdf8; font-family: monospace;">{{ $connectionDetails['port'] ?: 5555 }}</strong></div>
                        <div><span style="color: #94a3b8;">Hub Inicial:</span> <strong style="color: #ffffff;">{{ $connectionDetails['hub'] ?: 'DEFAULT' }}</strong></div>
                        <div><span style="color: #94a3b8;">Contraseña:</span> <strong style="{{ $connectionDetails['has_password'] ? 'color: #34d399;' : 'color: #fb7185;' }}">{{ $connectionDetails['has_password'] ? 'Cargada (' . $connectionDetails['password_mask'] . ')' : 'No configurada (vacía)' }}</strong></div>
                        <div><span style="color: #94a3b8;">Verificar SSL:</span> <strong style="color: #ffffff;">{{ $connectionDetails['verify_ssl'] ? 'true' : 'false' }}</strong></div>
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
SOFTETHER_VPS2_HOST=152.206.177.165
# Nota: Si Apache ocupa el 443, SoftEther escucha en 5555 o 992
SOFTETHER_VPS2_PORT=5555
SOFTETHER_VPS2_HUB=DEFAULT
SOFTETHER_VPS2_ADMIN_PASSWORD=tu_clave_administrador_vps2
SOFTETHER_VPS2_VERIFY_SSL=false
                </div>

                <h3 style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin: 1rem 0 0.5rem 0;">
                    Comando para probar el JSON-RPC del VPS 2 en el puerto de SoftEther (5555):
                </h3>
                <div class="se-code-block" style="margin-bottom: 1.5rem;">
curl -k -X POST -H "Content-Type: application/json" \
  -H "X-VPNADMIN-PASSWORD: tu_clave_administrador_vps2" \
  -d '{"jsonrpc":"2.0","id":"1","method":"Test","params":{"IntValue_u32":12345}}' \
  https://152.206.177.165:5555/api/
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
        </div>

        <!-- MODAL DETALLE DE SESIÓN (GetSessionStatus - apertura optimista 0ms) -->
        <div x-show="inspectModalOpen || Boolean(inspectSession)" x-cloak @click.self="closeInspectModal()" class="se-modal-overlay">
            <div class="se-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin: 0;">
                        Detalles de Sesión: <span x-text="inspectSession?.Name_str || inspectSessionTitle || 'Sesión'"></span>
                    </h3>
                    <button type="button" @click="closeInspectModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <div x-show="inspectLoading && !inspectSession" style="padding: 1.75rem 0; text-align: center; color: #94a3b8; font-size: 0.85rem; display: flex; flex-direction: column; align-items: center; gap: 0.65rem;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: #38bdf8; animation: spin 0.8s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Consultando telemetría de la sesión en vivo...</span>
                </div>

                <div x-show="inspectSession" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; font-size: 0.825rem;">
                    <div><span style="color: #94a3b8;">Usuario:</span> <strong style="color: #ffffff;" x-text="inspectSession?.Username_str || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">IP Cliente:</span> <strong style="color: #38bdf8; font-family: monospace;" x-text="inspectSession?.Client_Ip_Address_ip || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">Host Cliente:</span> <strong style="color: #ffffff;" x-text="inspectSession?.SessionStatus_ClientHostName_str || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">Software Cliente:</span> <strong style="color: #ffffff;" x-text="inspectSession?.ClientProductName_str || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">SO Cliente:</span> <strong style="color: #ffffff;" x-text="inspectSession?.ClientOsName_str || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">Cifrado SSL:</span> <strong style="color: #34d399;" x-text="inspectSession?.CipherName_str || 'AES-256'"></strong></div>
                    <div><span style="color: #94a3b8;">Protocolo Base:</span> <strong style="color: #ffffff;" x-text="inspectSession?.UnderlayProtocol_str || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">Conexiones TCP:</span> <strong style="color: #ffffff;" x-text="inspectSession?.MaxTcpConnections_u32 ?? 1"></strong></div>
                    <div><span style="color: #94a3b8;">Inicio de Conexión:</span> <strong style="color: #ffffff;" x-text="inspectSession?.StartTime_dt || '-'"></strong></div>
                    <div><span style="color: #94a3b8;">Tráfico Enviado:</span> <strong style="color: #38bdf8;" x-text="(Number(inspectSession?.TotalSendSize_u64 || 0) / 1048576).toFixed(2) + ' MB'"></strong></div>
                    <div><span style="color: #94a3b8;">Tráfico Recibido:</span> <strong style="color: #34d399;" x-text="(Number(inspectSession?.TotalRecvSize_u64 || 0) / 1048576).toFixed(2) + ' MB'"></strong></div>
                </div>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="button" @click="closeInspectModal()" class="se-btn" style="background: #334155; color: #ffffff;">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL CREAR USUARIO VPN (CreateUser - 0ms) -->
        <div x-show="isCreatingUser" x-cloak @click.self="isCreatingUser = false" class="se-modal-overlay">
            <div class="se-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: #38bdf8; margin: 0;">
                        Nuevo Usuario VPN en {{ $connectionDetails['server_name'] }}
                    </h3>
                    <button type="button" @click="isCreatingUser = false" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <form wire:submit.prevent="crearUsuario" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Virtual Hub de Destino <span style="color: #fb7185;">*</span></label>
                        <select x-model="newUserHub" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #38bdf8; font-weight: 600;">
                            @forelse($virtualHubs as $vh)
                                <option value="{{ $vh['hub_name'] }}">{{ $vh['hub_name'] }} ({{ $vh['users_count'] ?? 0 }} usuarios)</option>
                            @empty
                                <option value="{{ $connectionDetails['hub'] ?? 'DEFAULT' }}">{{ $connectionDetails['hub'] ?? 'DEFAULT' }}</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre de Usuario (VPN)</label>
                        <input type="text" x-model="newUsername" required placeholder="ej: cliente_playa" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre Real / Cliente Asociado</label>
                        <input type="text" x-model="newRealname" placeholder="ej: Router 4G Moraima Guanabo" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Contraseña VPN</label>
                        <input type="password" x-model="newPassword" required placeholder="Contraseña segura" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nota / Descripción</label>
                        <input type="text" x-model="newNote" placeholder="ej: Enlace principal de telemetría" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                        <button type="button" @click="isCreatingUser = false" class="se-btn" style="background: #334155; color: #ffffff;">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="crearUsuario" class="se-btn se-btn-sky">
                            <span wire:loading.remove wire:target="crearUsuario">Crear Usuario</span>
                            <span wire:loading wire:target="crearUsuario">Creando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DESHABILITAR USUARIO POR TIEMPO DEFINIDO (0ms) -->
        <div x-show="Boolean(userToTimedDisable)" x-cloak @click.self="userToTimedDisable = null" class="se-modal-overlay" style="z-index: 60;">
            <div class="se-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #fbbf24; margin: 0;">
                        Deshabilitar Cuenta VPN: <span x-text="userToTimedDisable?.username || ''"></span>
                    </h3>
                    <button type="button" @click="userToTimedDisable = null" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <div style="font-size: 0.825rem; color: #cbd5e1; margin-bottom: 1rem;">
                    Virtual Hub: <span class="se-badge se-badge-sky" style="font-family: monospace;" x-text="userToTimedDisable?.hub_name || ''"></span>
                </div>

                <form wire:submit.prevent="confirmarDeshabilitarUsuario" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.35rem;">Duración de la deshabilitación</label>
                        <select x-model="disableDurationPreset" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.55rem; color: #ffffff;">
                            <option value="60">1 hora (reactivación automática)</option>
                            <option value="360">6 horas (reactivación automática)</option>
                            <option value="720">12 horas (reactivación automática)</option>
                            <option value="1440">24 horas / 1 día (reactivación automática)</option>
                            <option value="4320">3 días (reactivación automática)</option>
                            <option value="10080">7 días (reactivación automática)</option>
                            <option value="custom">Fecha y hora personalizada</option>
                            <option value="indefinite">Indefinido (hasta reactivación manual)</option>
                        </select>
                    </div>

                    <div x-show="disableDurationPreset === 'custom'" x-cloak>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Reactivar automáticamente el:</label>
                        <input type="datetime-local" x-model="disableUntilCustom" :required="disableDurationPreset === 'custom'" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>

                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Motivo / Nota interna (opcional)</label>
                        <input type="text" x-model="disableReason" placeholder="ej: Suspensión por mantenimiento o pago pendiente" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                    </div>

                    <div style="padding: 0.65rem 0.85rem; border-radius: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #fde68a; font-size: 0.75rem; line-height: 1.4;">
                        ℹ La cuenta será bloqueada de inmediato en SoftEther. Si elige un tiempo definido, se reactivará automáticamente al cumplirse el plazo.
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                        <button type="button" @click="userToTimedDisable = null" class="se-btn" style="background: #334155; color: #ffffff;">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="confirmarDeshabilitarUsuario" class="se-btn se-btn-amber">
                            <span wire:loading.remove wire:target="confirmarDeshabilitarUsuario">Confirmar Deshabilitación</span>
                            <span wire:loading wire:target="confirmarDeshabilitarUsuario">Aplicando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL GESTIÓN COMPLETA DE VIRTUAL HUB (DHCP, SECURENAT, RUTAS, USUARIOS, LOGS - 0ms) -->
        <div x-show="Boolean(managingHub)" x-cloak @click.self="closeHubModal()" class="se-modal-overlay">
            <div class="se-modal" style="max-width: 840px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                            <h3 style="font-size: 1.15rem; font-weight: 700; color: #38bdf8; margin: 0;">
                                Gestión de Virtual Hub: <span style="font-family: monospace; color: #ffffff;" x-text="managingHub || ''"></span>
                            </h3>
                            <span x-show="hubLoading" class="se-badge se-badge-amber" style="font-size: 0.7rem;">
                                <svg style="width: 11px; height: 11px; animation: spin 0.8s linear infinite;" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sincronizando en vivo...
                            </span>
                        </div>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Servidor: {{ $connectionDetails['server_name'] }}</span>
                    </div>
                    <button type="button" @click="closeHubModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <!-- Sub-pestañas del Hub (conmutación instantánea 0ms) -->
                <div class="se-tabs" style="margin-bottom: 1.25rem;">
                    <button type="button" @click="hubManageTab = 'nat_dhcp'" :class="{ 'active': hubManageTab === 'nat_dhcp' }" class="se-tab-btn {{ $hubManageTab === 'nat_dhcp' ? 'active' : '' }}">
                        <span>⚙ DHCP & SecureNAT</span>
                    </button>
                    <button type="button" @click="hubManageTab = 'routes'" :class="{ 'active': hubManageTab === 'routes' }" class="se-tab-btn {{ $hubManageTab === 'routes' ? 'active' : '' }}">
                        <span>🛣 Rutas SecureNAT (<span x-text="(hubRoutesList || []).length"></span>)</span>
                    </button>
                    <button type="button" @click="hubManageTab = 'users'" :class="{ 'active': hubManageTab === 'users' }" class="se-tab-btn {{ $hubManageTab === 'users' ? 'active' : '' }}">
                        <span>👥 Usuarios del Hub ({{ count($hubManageUsers) }})</span>
                    </button>
                    <button type="button" @click="hubManageTab = 'logs'" :class="{ 'active': hubManageTab === 'logs' }" class="se-tab-btn {{ $hubManageTab === 'logs' ? 'active' : '' }}">
                        <span>📋 Logs del Hub</span>
                    </button>
                </div>

                <!-- SUB-TAB 1: DHCP & SECURENAT -->
                <div x-show="hubManageTab === 'nat_dhcp'" x-cloak>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-radius: 0.5rem; background: #090e1a; border: 1px solid #1e293b; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.85rem; color: #cbd5e1; font-weight: 600;">Estado Motor SecureNAT:</span>
                            <span class="se-badge {{ !empty($hubManageSecureNat['securenat_enabled']) ? 'se-badge-emerald' : 'se-badge-amber' }}">
                                {{ !empty($hubManageSecureNat['securenat_enabled']) ? 'Activo en SoftEther' : 'Desactivado' }}
                            </span>
                        </div>
                        <button type="button"
                                wire:click="toggleHubSecureNat({{ !empty($hubManageSecureNat['securenat_enabled']) ? 'false' : 'true' }}, '{{ $managingHub }}')"
                                wire:loading.attr="disabled"
                                wire:target="toggleHubSecureNat"
                                class="se-btn {{ !empty($hubManageSecureNat['securenat_enabled']) ? 'se-btn-rose' : 'se-btn-emerald' }}"
                                style="font-size: 0.75rem;">
                            {{ !empty($hubManageSecureNat['securenat_enabled']) ? 'Desactivar SecureNAT Ahora' : 'Activar SecureNAT Ahora' }}
                        </button>
                    </div>

                    <form wire:submit.prevent="guardarConfigSecureNatDhcp" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.825rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.65rem; padding: 0.75rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #e2e8f0; cursor: pointer;">
                                <input type="checkbox" wire:model="natForm.securenat_enabled" />
                                <span>Activar Motor SecureNAT</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #e2e8f0; cursor: pointer;">
                                <input type="checkbox" wire:model="natForm.use_nat" />
                                <span>Habilitar Virtual NAT</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #e2e8f0; cursor: pointer;">
                                <input type="checkbox" wire:model="natForm.use_dhcp" />
                                <span>Habilitar Servidor DHCP</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #e2e8f0; cursor: pointer;">
                                <input type="checkbox" wire:model="natForm.save_log" />
                                <span>Guardar Log NAT/DHCP</span>
                            </label>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;">
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">IP Interfaz Virtual (Host NAT)</label>
                                <input type="text" wire:model="natForm.virtual_gateway_ip" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Máscara de Subred</label>
                                <input type="text" wire:model="natForm.virtual_subnet_mask" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Rango DHCP Inicio</label>
                                <input type="text" wire:model="natForm.dhcp_lease_start" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #34d399; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Rango DHCP Fin</label>
                                <input type="text" wire:model="natForm.dhcp_lease_end" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #34d399; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Gateway Asignado (DHCP)</label>
                                <input type="text" wire:model="natForm.dhcp_gateway_ip" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Tiempo Concesión Lease (seg)</label>
                                <input type="number" min="60" wire:model="natForm.dhcp_lease_expire_seconds" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Servidor DNS 1</label>
                                <input type="text" wire:model="natForm.dns_server_1" placeholder="8.8.8.8" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #a78bfa; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Servidor DNS 2</label>
                                <input type="text" wire:model="natForm.dns_server_2" placeholder="1.1.1.1" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #a78bfa; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre de Dominio (Opcional)</label>
                                <input type="text" wire:model="natForm.domain_name" placeholder="ej: dataplus.local" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">MTU (Default 1500)</label>
                                <input type="number" min="576" max="1500" wire:model="natForm.mtu" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">NAT TCP Timeout (seg)</label>
                                <input type="number" min="60" wire:model="natForm.nat_tcp_timeout" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">NAT UDP Timeout (seg)</label>
                                <input type="number" min="10" wire:model="natForm.nat_udp_timeout" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-family: monospace;" />
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
                            <button type="button" @click="closeHubModal()" class="se-btn" style="background: #334155; color: #ffffff;">Cerrar</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="guardarConfigSecureNatDhcp" class="se-btn se-btn-emerald">
                                <span wire:loading.remove wire:target="guardarConfigSecureNatDhcp">Guardar DHCP & SecureNAT</span>
                                <span wire:loading wire:target="guardarConfigSecureNatDhcp">Guardando...</span>
                            </button>
                        </div>
                    </form>

                    @if(!empty($hubManageDhcpTable))
                        <div style="margin-top: 1.25rem; border-top: 1px solid #1e293b; padding-top: 1rem;">
                            <h4 style="font-size: 0.85rem; font-weight: 600; color: #38bdf8; margin: 0 0 0.5rem 0;">
                                Concesiones DHCP Activas ({{ count($hubManageDhcpTable) }})
                            </h4>
                            <div style="overflow-x: auto;">
                                <table class="se-table" style="font-size: 0.75rem;">
                                    <thead>
                                        <tr>
                                            <th>IP Asignada</th>
                                            <th>MAC</th>
                                            <th>Hostname</th>
                                            <th>Expira</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hubManageDhcpTable as $lease)
                                            <tr>
                                                <td style="font-family: monospace; color: #34d399;">{{ $lease['IpAddress_ip'] ?? '-' }}</td>
                                                <td style="font-family: monospace;">{{ $lease['MacAddress_bin'] ?? '-' }}</td>
                                                <td>{{ $lease['Hostname_str'] ?? '-' }}</td>
                                                <td>{{ $lease['ExpireTime_dt'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- SUB-TAB 1B: RUTAS ESTÁTICAS DE SECURENAT (edición local 0ms + 1 guardar) -->
                <div x-show="hubManageTab === 'routes'" x-cloak>
                    <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.825rem;">
                        <div style="padding: 0.85rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.55rem; color: #e2e8f0; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" x-model="hubApplyPushRoutes" />
                                <span>Aplicar y enviar rutas estáticas vía DHCP a los clientes (`ApplyDhcpPushRoutes`)</span>
                            </label>
                            <span class="se-badge" :class="hubApplyPushRoutes ? 'se-badge-emerald' : 'se-badge-amber'" x-text="hubApplyPushRoutes ? 'Activo' : 'Inactivo'"></span>
                        </div>

                        <!-- Lista editable de rutas configuradas -->
                        <div style="overflow-x: auto;">
                            <table class="se-table" style="font-size: 0.78rem;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Red Destino (IP)</th>
                                        <th>Máscara de Subred</th>
                                        <th>Gateway (IP)</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(routeRow, rIdx) in (hubRoutesList || [])" :key="rIdx">
                                        <tr>
                                            <td style="color: #64748b;" x-text="rIdx + 1"></td>
                                            <td>
                                                <input type="text" x-model="routeRow.network" style="width: 100%; min-width: 125px; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.35rem 0.5rem; color: #38bdf8; font-family: monospace;" />
                                            </td>
                                            <td>
                                                <input type="text" x-model="routeRow.mask" style="width: 100%; min-width: 125px; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.35rem 0.5rem; color: #ffffff; font-family: monospace;" />
                                            </td>
                                            <td>
                                                <input type="text" x-model="routeRow.gateway" style="width: 100%; min-width: 125px; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.35rem 0.5rem; color: #34d399; font-family: monospace;" />
                                            </td>
                                            <td>
                                                <button type="button" @click="removeHubRouteLocal(rIdx)" class="se-btn se-btn-rose" style="padding: 0.3rem 0.55rem; font-size: 0.72rem;">
                                                    Quitar
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="!(hubRoutesList && hubRoutesList.length)">
                                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 1.25rem;">
                                            No hay rutas estáticas configuradas en SecureNAT para <span x-text="managingHub || ''"></span>.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Agregar nueva ruta estática (0ms local) -->
                        <div style="padding: 0.85rem; background: #090e1a; border: 1px dashed #334155; border-radius: 0.5rem;">
                            <div style="font-size: 0.8rem; font-weight: 600; color: #38bdf8; margin-bottom: 0.55rem;">
                                + Agregar Nueva Ruta a SecureNAT
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.55rem; align-items: end;">
                                <div>
                                    <label style="display: block; color: #94a3b8; font-size: 0.72rem; margin-bottom: 0.2rem;">Red Destino</label>
                                    <input type="text" x-model="newHubRouteNetwork" @keydown.enter.prevent="addHubRouteLocal()" placeholder="ej: 192.168.10.0" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.4rem; color: #ffffff; font-family: monospace;" />
                                </div>
                                <div>
                                    <label style="display: block; color: #94a3b8; font-size: 0.72rem; margin-bottom: 0.2rem;">Máscara de Subred</label>
                                    <input type="text" x-model="newHubRouteMask" @keydown.enter.prevent="addHubRouteLocal()" placeholder="255.255.255.0" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.4rem; color: #ffffff; font-family: monospace;" />
                                </div>
                                <div>
                                    <label style="display: block; color: #94a3b8; font-size: 0.72rem; margin-bottom: 0.2rem;">Gateway</label>
                                    <input type="text" x-model="newHubRouteGateway" @keydown.enter.prevent="addHubRouteLocal()" placeholder="ej: 192.168.30.1" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.4rem; padding: 0.4rem; color: #ffffff; font-family: monospace;" />
                                </div>
                                <button type="button" @click="addHubRouteLocal()" class="se-btn se-btn-sky" style="justify-content: center; height: 34px;">
                                    + Añadir a la lista
                                </button>
                            </div>
                            <div x-show="hubRouteError" x-cloak style="margin-top: 0.5rem; font-size: 0.75rem; color: #fb7185;" x-text="hubRouteError"></div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" @click="closeHubModal()" class="se-btn" style="background: #334155; color: #ffffff;">Cerrar</button>
                            <button type="button" wire:click="guardarRutasHub" wire:loading.attr="disabled" wire:target="guardarRutasHub" class="se-btn se-btn-emerald">
                                <span wire:loading.remove wire:target="guardarRutasHub">Guardar Rutas en SecureNAT</span>
                                <span wire:loading wire:target="guardarRutasHub">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- SUB-TAB 2: USUARIOS DEL HUB -->
                <div x-show="hubManageTab === 'users'" x-cloak>
                    <form wire:submit.prevent="crearUsuarioEnHub" style="padding: 0.9rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <div style="font-size: 0.825rem; font-weight: 600; color: #38bdf8; margin-bottom: 0.65rem;">
                            + Agregar Nuevo Usuario en <span x-text="managingHub || ''"></span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 0.65rem;">
                            <input type="text" wire:model="hubNewUsername" required placeholder="Usuario (ej: router_sucursal)" style="background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-size: 0.8rem;" />
                            <input type="text" wire:model="hubNewRealname" placeholder="Nombre / Equipo" style="background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-size: 0.8rem;" />
                            <input type="password" wire:model="hubNewPassword" required placeholder="Contraseña VPN" style="background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-size: 0.8rem;" />
                            <input type="text" wire:model="hubNewNote" placeholder="Nota (opcional)" style="background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff; font-size: 0.8rem;" />
                        </div>
                        <div style="display: flex; justify-content: flex-end; margin-top: 0.65rem;">
                            <button type="submit" wire:loading.attr="disabled" wire:target="crearUsuarioEnHub" class="se-btn se-btn-sky" style="font-size: 0.78rem;">
                                <span wire:loading.remove wire:target="crearUsuarioEnHub">Agregar Usuario</span>
                                <span wire:loading wire:target="crearUsuarioEnHub">Agregando...</span>
                            </button>
                        </div>
                    </form>

                    <div style="overflow-x: auto;">
                        <table class="se-table" style="font-size: 0.78rem;">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Último Acceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($hubManageUsers as $hu)
                                    @php
                                        $hActive = (bool) ($hu['is_active'] ?? true);
                                        $hTimed = (bool) ($hu['is_timed_disabled'] ?? false);
                                    @endphp
                                    <tr>
                                        <td style="font-family: monospace; font-weight: 600; color: #38bdf8;">{{ $hu['username'] }}</td>
                                        <td>{{ $hu['real_name'] }}</td>
                                        <td>
                                            @if($hActive)
                                                <span class="se-badge se-badge-emerald">Habilitado</span>
                                            @elseif($hTimed)
                                                <span class="se-badge se-badge-amber" title="Hasta {{ $hu['disabled_until'] }}">Temporal ({{ $hu['disabled_until'] }})</span>
                                            @else
                                                <span class="se-badge se-badge-rose">Deshabilitado</span>
                                            @endif
                                        </td>
                                        <td>{{ $hu['last_login'] ?? 'Nunca' }}</td>
                                        <td>
                                            <div style="display: flex; gap: 0.4rem;">
                                                @if($hActive)
                                                    <button type="button"
                                                            data-user="{{ $hu['username'] }}"
                                                            data-hub="{{ $managingHub }}"
                                                            @click="openTimedDisableModal($el.dataset.user, $el.dataset.hub)"
                                                            class="se-btn se-btn-amber"
                                                            style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                                        Deshabilitar
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            wire:click="toggleUsuario('{{ $hu['username'] }}', true, '{{ $managingHub }}')"
                                                            class="se-btn se-btn-emerald"
                                                            style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                                        Habilitar
                                                    </button>
                                                @endif
                                                <button type="button"
                                                        wire:click="eliminarUsuario('{{ $hu['username'] }}', '{{ $managingHub }}')"
                                                        wire:confirm="¿Eliminar permanentemente al usuario {{ $hu['username'] }} del Hub {{ $managingHub }}?"
                                                        class="se-btn se-btn-rose"
                                                        style="padding: 0.25rem 0.55rem; font-size: 0.72rem;">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: #94a3b8; padding: 1.5rem;">
                                            <span x-text="hubLoading ? 'Cargando usuarios del Virtual Hub...' : 'No hay usuarios registrados en el Virtual Hub ' + (managingHub || '') + '.'"></span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SUB-TAB 3: LOGS DEL HUB -->
                <div x-show="hubManageTab === 'logs'" x-cloak>
                    <form wire:submit.prevent="guardarConfigLogsHub" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                        <div style="padding: 1rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.6rem; font-weight: 600; color: #ffffff; cursor: pointer;">
                                    <input type="checkbox" wire:model="logForm.save_security_log" />
                                    <span>Activar Log de Seguridad (Security Log)</span>
                                </label>
                                <span class="se-badge {{ !empty($logForm['save_security_log']) ? 'se-badge-emerald' : 'se-badge-rose' }}">
                                    {{ !empty($logForm['save_security_log']) ? 'Activo' : 'Desactivado' }}
                                </span>
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; font-size: 0.78rem; margin-bottom: 0.25rem;">Ciclo de rotación del archivo de Security Log</label>
                                <select wire:model="logForm.security_log_switch_type" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff;">
                                    <option value="0">Sin rotación (archivo único)</option>
                                    <option value="1">Por segundo</option>
                                    <option value="2">Por minuto</option>
                                    <option value="3">Cada hora</option>
                                    <option value="4">Diario (Recomendado)</option>
                                    <option value="5">Mensual</option>
                                </select>
                            </div>
                        </div>

                        <div style="padding: 1rem; background: #090e1a; border: 1px solid #1e293b; border-radius: 0.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.6rem; font-weight: 600; color: #ffffff; cursor: pointer;">
                                    <input type="checkbox" wire:model="logForm.save_packet_log" />
                                    <span>Activar Log de Paquetes (Packet Log)</span>
                                </label>
                                <span class="se-badge {{ !empty($logForm['save_packet_log']) ? 'se-badge-emerald' : 'se-badge-rose' }}">
                                    {{ !empty($logForm['save_packet_log']) ? 'Activo' : 'Desactivado' }}
                                </span>
                            </div>
                            <div>
                                <label style="display: block; color: #94a3b8; font-size: 0.78rem; margin-bottom: 0.25rem;">Ciclo de rotación del archivo de Packet Log</label>
                                <select wire:model="logForm.packet_log_switch_type" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.45rem; padding: 0.45rem; color: #ffffff;">
                                    <option value="0">Sin rotación (archivo único)</option>
                                    <option value="1">Por segundo</option>
                                    <option value="2">Por minuto</option>
                                    <option value="3">Cada hora</option>
                                    <option value="4">Diario (Recomendado)</option>
                                    <option value="5">Mensual</option>
                                </select>
                            </div>
                            <div style="margin-top: 0.6rem; font-size: 0.75rem; color: #94a3b8;">
                                Nota: El Packet Log registra cabeceras de tráfico (TCP/DHCP). Actívelo solo si necesita auditoría o diagnóstico de tráfico, ya que consume espacio en disco.
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" @click="closeHubModal()" class="se-btn" style="background: #334155; color: #ffffff;">Cerrar</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="guardarConfigLogsHub" class="se-btn se-btn-emerald">
                                <span wire:loading.remove wire:target="guardarConfigLogsHub">Guardar Configuración de Logs</span>
                                <span wire:loading wire:target="guardarConfigLogsHub">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL CREAR VIRTUAL HUB (CreateHub - 0ms) -->
        <div x-show="isCreatingHub" x-cloak @click.self="isCreatingHub = false" class="se-modal-overlay">
            <div class="se-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="padding: 0.4rem; border-radius: 0.35rem; background: rgba(16, 185, 129, 0.2); color: #34d399;">
                            <svg style="width: 1.1rem; height: 1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #ffffff; margin: 0;">
                            Nuevo Virtual Hub en {{ $connectionDetails['server_name'] }}
                        </h3>
                    </div>
                    <button type="button" @click="isCreatingHub = false" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <form wire:submit.prevent="crearVirtualHub" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">
                            Nombre del Virtual Hub <span style="color: #fb7185;">*</span>
                        </label>
                        <input type="text" x-model="newHubName" required placeholder="ej: Cliente_EmpresaX o Sucursal_Playa" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;" />
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                            Solo caracteres alfanuméricos, guiones y guiones bajos (sin espacios ni acentos).
                        </div>
                    </div>

                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">
                            Contraseña de Administración del Hub <span style="color: #64748b; font-size: 0.75rem;">(Opcional)</span>
                        </label>
                        <input type="password" x-model="newHubPassword" placeholder="Dejar vacío o asignar clave específica para este Hub" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff;" />
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                            Si se define, permite administración remota exclusiva para los administradores de este hub.
                        </div>
                    </div>

                    <div style="padding: 0.65rem 0.85rem; border-radius: 0.5rem; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.25); color: #7dd3fc; font-size: 0.75rem; line-height: 1.4;">
                        ℹ El Virtual Hub se creará en modo <strong>Online</strong> inmediatamente en el servidor SoftEther seleccionado.
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                        <button type="button" @click="isCreatingHub = false" class="se-btn" style="background: #334155; color: #ffffff;">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="crearVirtualHub" class="se-btn se-btn-emerald">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span wire:loading.remove wire:target="crearVirtualHub">Crear Virtual Hub</span>
                            <span wire:loading wire:target="crearVirtualHub">Creando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CONFIRMAR ELIMINACIÓN DE VIRTUAL HUB (DeleteHub - 0ms) -->
        <div x-show="Boolean(hubToDelete)" x-cloak @click.self="hubToDelete = null" class="se-modal-overlay">
            <div class="se-modal">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="padding: 0.4rem; border-radius: 0.35rem; background: rgba(244, 63, 94, 0.2); color: #fb7185;">
                            <svg style="width: 1.1rem; height: 1.1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #fb7185; margin: 0;">
                            ¿Eliminar Virtual Hub '<span x-text="hubToDelete || ''"></span>'?
                        </h3>
                    </div>
                    <button type="button" @click="hubToDelete = null" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>

                <div style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 1.25rem;">
                    <p style="margin: 0 0 0.75rem 0;">
                        Está a punto de eliminar permanentemente el Virtual Hub <strong style="color: #38bdf8; font-family: monospace;" x-text="hubToDelete || ''"></strong> en <strong>{{ $connectionDetails['server_name'] }}</strong>.
                    </p>
                    <div style="padding: 0.75rem 1rem; border-radius: 0.5rem; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.35); color: #fecdd3; font-size: 0.8rem;">
                        <strong>Advertencia crítica de SoftEther:</strong>
                        <ul style="margin: 0.35rem 0 0 1.2rem; padding: 0;">
                            <li>Se desconectarán todas las sesiones VPN y túneles activos en este Hub.</li>
                            <li>Se borrarán todos los usuarios, contraseñas, grupos y reglas asociadas al Hub.</li>
                            <li>Esta acción no se puede deshacer.</li>
                        </ul>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" @click="hubToDelete = null" class="se-btn" style="background: #334155; color: #ffffff;">
                        Cancelar
                    </button>
                    <button type="button" wire:click="eliminarVirtualHub" wire:loading.attr="disabled" wire:target="eliminarVirtualHub" class="se-btn se-btn-rose">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        <span wire:loading.remove wire:target="eliminarVirtualHub">Sí, Eliminar Virtual Hub</span>
                        <span wire:loading wire:target="eliminarVirtualHub">Eliminando...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL CREAR SWITCH L3 (0ms) -->
        <div x-show="isCreatingL3Switch" x-cloak @click.self="isCreatingL3Switch = false" class="se-modal-overlay">
            <div class="se-modal" style="max-width: 480px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #34d399; margin: 0;">
                        Nuevo Virtual Layer 3 Switch
                    </h3>
                    <button type="button" @click="isCreatingL3Switch = false" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>
                <form wire:submit.prevent="crearL3Switch" style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Nombre del Switch L3 <span style="color: #fb7185;">*</span></label>
                        <input type="text" x-model="newL3SwitchName" required placeholder="ej: L3_Router_Principal" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;" />
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="isCreatingL3Switch = false" class="se-btn" style="background: #334155; color: #ffffff;">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="crearL3Switch" class="se-btn se-btn-emerald">
                            <span wire:loading.remove wire:target="crearL3Switch">Crear Switch L3</span>
                            <span wire:loading wire:target="crearL3Switch">Creando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CREAR / MODIFICAR INTERFAZ L3 (0ms) -->
        <div x-show="isEditingL3If" x-cloak @click.self="isEditingL3If = false; editingL3IfOriginal = null" class="se-modal-overlay">
            <div class="se-modal" style="max-width: 520px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #38bdf8; margin: 0;" x-text="editingL3IfOriginal ? 'Modificar Interfaz L3' : 'Nueva Interfaz L3'"></h3>
                    <button type="button" @click="isEditingL3If = false; editingL3IfOriginal = null" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>
                <form wire:submit.prevent="guardarInterfazL3" style="display: flex; flex-direction: column; gap: 0.9rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Switch L3 <span style="color: #fb7185;">*</span></label>
                        <select x-model="l3IfFormSwitch" :disabled="Boolean(editingL3IfOriginal)" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #38bdf8; font-family: monospace;">
                            @foreach($l3Switches as $sw)
                                <option value="{{ $sw['name'] }}">{{ $sw['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Virtual Hub Conectado <span style="color: #fb7185;">*</span></label>
                        <select x-model="l3IfFormHub" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;">
                            @foreach($virtualHubs as $vh)
                                <option value="{{ $vh['hub_name'] }}">{{ $vh['hub_name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Dirección IP L3 <span style="color: #fb7185;">*</span></label>
                            <input type="text" x-model="l3IfFormIp" required placeholder="192.168.10.1" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #34d399; font-family: monospace;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Máscara de Subred <span style="color: #fb7185;">*</span></label>
                            <input type="text" x-model="l3IfFormMask" required placeholder="255.255.255.0" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;" />
                        </div>
                    </div>
                    <div style="padding: 0.65rem 0.85rem; border-radius: 0.5rem; background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.25); color: #7dd3fc; font-size: 0.75rem;">
                        ℹ Si el Switch L3 está activo, el sistema lo detendrá brevemente, aplicará el cambio de interfaz y lo iniciará nuevamente de forma automática.
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="isEditingL3If = false; editingL3IfOriginal = null" class="se-btn" style="background: #334155; color: #ffffff;">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="guardarInterfazL3" class="se-btn se-btn-sky">
                            <span wire:loading.remove wire:target="guardarInterfazL3">Guardar Interfaz L3</span>
                            <span wire:loading wire:target="guardarInterfazL3">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL CREAR / MODIFICAR RUTA L3 (0ms) -->
        <div x-show="isEditingL3Route" x-cloak @click.self="isEditingL3Route = false; editingL3RouteOriginal = null" class="se-modal-overlay">
            <div class="se-modal" style="max-width: 520px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 0.75rem; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #a5b4fc; margin: 0;" x-text="editingL3RouteOriginal ? 'Modificar Ruta de Switch L3' : 'Nueva Ruta de Switch L3'"></h3>
                    <button type="button" @click="isEditingL3Route = false; editingL3RouteOriginal = null" style="background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer;">✕</button>
                </div>
                <form wire:submit.prevent="guardarRutaL3" style="display: flex; flex-direction: column; gap: 0.9rem; font-size: 0.85rem;">
                    <div>
                        <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Switch L3 <span style="color: #fb7185;">*</span></label>
                        <select x-model="l3RouteFormSwitch" :disabled="Boolean(editingL3RouteOriginal)" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #38bdf8; font-family: monospace;">
                            @foreach($l3Switches as $sw)
                                <option value="{{ $sw['name'] }}">{{ $sw['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Red Destino <span style="color: #fb7185;">*</span></label>
                            <input type="text" x-model="l3RouteFormNetwork" required placeholder="192.168.20.0" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #38bdf8; font-family: monospace;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Máscara <span style="color: #fb7185;">*</span></label>
                            <input type="text" x-model="l3RouteFormMask" required placeholder="255.255.255.0" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Gateway <span style="color: #fb7185;">*</span></label>
                            <input type="text" x-model="l3RouteFormGateway" required placeholder="192.168.10.254" style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #34d399; font-family: monospace;" />
                        </div>
                        <div>
                            <label style="display: block; color: #94a3b8; margin-bottom: 0.25rem;">Métrica <span style="color: #fb7185;">*</span></label>
                            <input type="number" min="1" x-model.number="l3RouteFormMetric" required style="width: 100%; background: #1e293b; border: 1px solid #475569; border-radius: 0.5rem; padding: 0.5rem; color: #ffffff; font-family: monospace;" />
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="button" @click="isEditingL3Route = false; editingL3RouteOriginal = null" class="se-btn" style="background: #334155; color: #ffffff;">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="guardarRutaL3" class="se-btn se-btn-indigo">
                            <span wire:loading.remove wire:target="guardarRutaL3">Guardar Ruta L3</span>
                            <span wire:loading wire:target="guardarRutaL3">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
