<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Banner Principal con Estadísticas y Acciones Rápidas --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-sky-950 p-6 shadow-xl border border-slate-700/50">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold tracking-wide 
                        {{ $this->connectionStatus === true ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($this->connectionStatus === false ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-sky-500/20 text-sky-300 border border-sky-500/30') }}">
                        <span class="w-2 h-2 rounded-full {{ $this->connectionStatus === true ? 'bg-emerald-400 animate-pulse' : ($this->connectionStatus === false ? 'bg-rose-400' : 'bg-sky-400') }}"></span>
                        {{ $this->connectionStatus === true ? 'Enlace LDAP Activo (' . $this->connectionLatency . ' ms)' : ($this->connectionStatus === false ? 'Conexión Fallida' : 'Servicio Listo para Conectar') }}
                    </div>

                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                        Directorio de Usuarios LDAP
                    </h2>
                    <p class="text-sm text-slate-300 max-w-2xl">
                        Sincroniza cuentas automáticamente desde tu servidor LLDAP / OpenLDAP hacia DataPlus para habilitar acceso unificado y asignación de roles.
                    </p>
                </div>

                {{-- Botones de Acción Inmediata --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button 
                        type="button"
                        wire:click="testConnection"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-800/80 hover:bg-slate-700 border border-slate-600 transition shadow-sm hover:shadow"
                    >
                        <x-heroicon-o-signal class="w-4 h-4 text-sky-400" wire:loading.remove wire:target="testConnection" />
                        <svg wire:loading wire:target="testConnection" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="testConnection">Probar Conexión</span>
                        <span wire:loading wire:target="testConnection">Comprobando...</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="loadDirectoryUsers"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-sky-600/90 hover:bg-sky-500 border border-sky-400/30 transition shadow-sm hover:shadow"
                    >
                        <x-heroicon-o-users class="w-4 h-4" wire:loading.remove wire:target="loadDirectoryUsers" />
                        <svg wire:loading wire:target="loadDirectoryUsers" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Explorar Directorio</span>
                    </button>

                    <button 
                        type="button"
                        wire:click="syncAllUsers"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-900/40 border border-emerald-400/30 transition transform active:scale-95"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4 text-white" wire:loading.remove wire:target="syncAllUsers" />
                        <svg wire:loading wire:target="syncAllUsers" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="syncAllUsers">Sincronizar Todo</span>
                        <span wire:loading wire:target="syncAllUsers">Sincronizando...</span>
                    </button>
                </div>
            </div>

            {{-- Métricas Rápidas --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800">
                <div class="bg-slate-800/40 backdrop-blur-sm rounded-xl p-3.5 border border-slate-700/40">
                    <div class="text-xs font-medium text-slate-400">Total Usuarios en BD</div>
                    <div class="text-2xl font-bold text-white mt-1">{{ $this->totalUsers }}</div>
                </div>

                <div class="bg-slate-800/40 backdrop-blur-sm rounded-xl p-3.5 border border-slate-700/40">
                    <div class="text-xs font-medium text-sky-400">Vinculados con LDAP</div>
                    <div class="text-2xl font-bold text-sky-300 mt-1">{{ $this->syncedCount }}</div>
                </div>

                <div class="bg-slate-800/40 backdrop-blur-sm rounded-xl p-3.5 border border-slate-700/40">
                    <div class="text-xs font-medium text-slate-400">Servidor y Puerto</div>
                    <div class="text-sm font-semibold text-slate-200 mt-2 truncate">{{ $data['host'] ?? 'lldap' }}:{{ $data['port'] ?? 3890 }}</div>
                </div>

                <div class="bg-slate-800/40 backdrop-blur-sm rounded-xl p-3.5 border border-slate-700/40">
                    <div class="text-xs font-medium text-slate-400">Cifrado</div>
                    <div class="text-sm font-semibold text-slate-200 mt-2">
                        @if(!empty($data['ssl']))
                            <span class="text-emerald-400">LDAPS (Cifrado)</span>
                        @elseif(!empty($data['tls']))
                            <span class="text-sky-400">STARTTLS</span>
                        @else
                            <span class="text-slate-400">Plano (3890)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Selector de Pestañas --}}
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-2">
            <button 
                type="button"
                wire:click="$set('activeTab', 'config')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 
                    {{ $activeTab === 'config' ? 'bg-primary-600 text-white shadow' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                <span>Configuración de Servidor</span>
            </button>

            <button 
                type="button"
                wire:click="loadDirectoryUsers"
                class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 
                    {{ $activeTab === 'directory' ? 'bg-primary-600 text-white shadow' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                <x-heroicon-o-user-group class="w-4 h-4" />
                <span>Explorador de Cuentas LDAP</span>
                @if(count($directoryUsers) > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-xs rounded-full bg-sky-500/20 text-sky-300 font-bold">
                        {{ count($directoryUsers) }}
                    </span>
                @endif
            </button>

            <button 
                type="button"
                wire:click="$set('activeTab', 'diagnostics')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 
                    {{ $activeTab === 'diagnostics' ? 'bg-primary-600 text-white shadow' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
            >
                <x-heroicon-o-command-line class="w-4 h-4" />
                <span>Comando CLI & Cron</span>
            </button>
        </div>

        {{-- CONTENIDO DE PESTAÑAS --}}

        {{-- TAB 1: FORMULARIO DE CONFIGURACIÓN --}}
        @if($activeTab === 'config')
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <x-filament::button type="submit" size="lg" color="primary">
                        Guardar Parámetros LDAP
                    </x-filament::button>
                </div>
            </form>
        @endif

        {{-- TAB 2: EXPLORADOR DE USUARIOS LDAP --}}
        @if($activeTab === 'directory')
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Cuentas detectadas en el servidor LDAP</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Puedes importar usuarios de forma individual o sincronizarlos todos a la vez.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            wire:click="loadDirectoryUsers" 
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition"
                        >
                            Refrescar Lista
                        </button>
                        <button 
                            type="button" 
                            wire:click="syncAllUsers" 
                            class="px-3.5 py-1.5 text-xs font-semibold rounded-lg text-white bg-emerald-600 hover:bg-emerald-500 transition shadow"
                        >
                            Sincronizar Todos
                        </button>
                    </div>
                </div>

                @if(empty($directoryUsers))
                    <div class="text-center py-12 px-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900">
                        <x-heroicon-o-users class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">No se han consultado usuarios aún</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                            Haz clic en "Explorar Directorio" para conectar con tu servidor LDAP y listar los usuarios disponibles.
                        </p>
                        <button 
                            type="button"
                            wire:click="loadDirectoryUsers"
                            class="mt-4 px-4 py-2 text-xs font-semibold rounded-lg text-white bg-sky-600 hover:bg-sky-500 transition"
                        >
                            Cargar Usuarios de LDAP Ahora
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-800">
                                <tr>
                                    <th class="px-4 py-3">Usuario / Nombre</th>
                                    <th class="px-4 py-3">Identificador (UID)</th>
                                    <th class="px-4 py-3">Correo Electrónico</th>
                                    <th class="px-4 py-3 text-center">Estado en DataPlus</th>
                                    <th class="px-4 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($directoryUsers as $u)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-bold text-xs flex items-center justify-center shadow">
                                                    {{ strtoupper(substr($u['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $u['name'] }}</div>
                                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $u['dn'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <code class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-xs font-mono text-sky-600 dark:text-sky-400">
                                                {{ $u['uid'] }}
                                            </code>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            {{ $u['email'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($u['is_synced'])
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Sincronizado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-300 dark:border-amber-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Pendiente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button 
                                                type="button"
                                                wire:click="syncIndividual('{{ $u['uid'] }}', '{{ $u['email'] }}', '{{ addslashes($u['name']) }}')"
                                                class="px-3 py-1 text-xs font-semibold rounded-lg transition {{ $u['is_synced'] ? 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' : 'text-white bg-sky-600 hover:bg-sky-500 shadow-sm' }}"
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

        {{-- TAB 3: DIAGNÓSTICO Y CLI --}}
        @if($activeTab === 'diagnostics')
            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-command-line class="w-5 h-5 text-sky-500" />
                        Sincronización Automatizada vía Consola
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                        Puedes ejecutar la sincronización de usuarios manualmente en cualquier momento desde el servidor usando Artisan, o programarla en el Cron de Linux para que se actualice cada hora.
                    </p>

                    <div class="space-y-3 pt-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Comando Artisan</label>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-950 font-mono text-sm text-sky-300 border border-slate-800">
                            <code>docker compose -f docker-compose.prod.yml exec -T app php artisan ldap:sync-users</code>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Automatización en Cron de Linux (crontab -e)</label>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-950 font-mono text-xs text-emerald-300 border border-slate-800">
                            <code>0 * * * * cd /home/jpaz/deploy/dataplus-platform && docker compose -f docker-compose.prod.yml exec -T app php artisan ldap:sync-users >/dev/null 2>&1</code>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
