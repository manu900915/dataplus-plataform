<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        {{-- Caja de búsqueda --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                🔍 Buscar negocios por nombre
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                Escribe el nombre de un negocio, restaurante, tienda, etc. También puedes buscar por el nombre del cliente propietario.
                <br><span class="text-amber-500 font-medium">Ejemplos:</span> Vinil Arte, Cucaña, El Patio, Peaky Pan
            </p>
            {{ $this->form }}
        </div>

        @if(!empty($this->resultados))
            <div class="space-y-4">
                <p class="text-sm font-medium text-gray-400 dark:text-gray-400">
                    {{ count($this->resultados) }} resultado(s) encontrado(s)
                </p>

                @foreach($this->resultados as $r)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-600 p-5 hover:shadow-lg transition">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            
                            <div class="flex-1">
                                {{-- Nombre del negocio --}}
                                <div class="flex items-center gap-2 mb-2">
                                    <x-heroicon-o-building-storefront class="w-6 h-6 text-amber-500" />
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $r['ubicacion_nombre'] }}
                                    </h3>
                                    <span class="px-2 py-0.5 text-xs font-bold bg-amber-500 text-white rounded-full">
                                        NEGOCIO
                                    </span>
                                </div>
                                
                                {{-- Cliente propietario --}}
                                <p class="text-base text-gray-700 dark:text-gray-200 mb-3">
                                    <span class="font-semibold text-gray-900 dark:text-white">👤 Cliente:</span> 
                                    {{ $r['cliente_nombre'] }} 
                                    <span class="text-sm font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ $r['cliente_id'] }}</span>
                                </p>

                                {{-- Dirección --}}
                                @if($r['ubicacion_direccion'])
                                    <p class="text-sm text-gray-800 dark:text-gray-200 mb-2 flex items-start gap-1.5">
                                        <x-heroicon-o-map-pin class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" />
                                        <span>
                                            {{ $r['ubicacion_direccion'] }}
                                            @if($r['ubicacion_ciudad'])
                                                <span class="text-blue-600 dark:text-blue-400 font-medium">, {{ $r['ubicacion_ciudad'] }}</span>
                                            @endif
                                        </span>
                                    </p>
                                @endif

                                {{-- Datos de contacto --}}
                                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                                    @if($r['cliente_telefono'])
                                        <span class="inline-flex items-center gap-1.5 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg">
                                            <x-heroicon-o-phone class="w-4 h-4 text-green-500" />
                                            {{ $r['cliente_telefono'] }}
                                        </span>
                                    @endif
                                    @if($r['telefono_local'])
                                        <span class="inline-flex items-center gap-1.5 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg">
                                            <x-heroicon-o-device-phone-mobile class="w-4 h-4 text-green-500" />
                                            Local: {{ $r['telefono_local'] }}
                                        </span>
                                    @endif
                                    @if($r['contacto'])
                                        <span class="inline-flex items-center gap-1.5 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg">
                                            <x-heroicon-o-user class="w-4 h-4 text-purple-500" />
                                            Contacto: <span class="font-medium">{{ $r['contacto'] }}</span>
                                        </span>
                                    @endif
                                    @if($r['cliente_email'])
                                        <span class="inline-flex items-center gap-1.5 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg">
                                            <x-heroicon-o-envelope class="w-4 h-4 text-blue-500" />
                                            {{ $r['cliente_email'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Botón ver cliente --}}
                            <div class="flex-shrink-0">
                                <a href="{{ $r['link'] }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                    Ver cliente
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(strlen($this->busqueda) >= 2)
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                <p class="text-lg">No se encontraron negocios con ese nombre.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>