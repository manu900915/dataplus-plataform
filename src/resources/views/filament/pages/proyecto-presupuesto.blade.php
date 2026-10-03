<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header del Presupuesto -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        PRESUPUESTO
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Código: <span class="font-mono font-bold">{{ $this->proyecto->codigo }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Fecha: {{ now()->format('d-m-Y') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Válido hasta: {{ now()->addDays(15)->format('d-m-Y') }}
                    </p>
                </div>
            </div>

            <!-- Información del Cliente -->
            @if($this->proyecto->cliente)
            <div class="border-t border-b border-gray-200 dark:border-gray-700 py-4 mb-6">
                <h2 class="font-bold text-gray-900 dark:text-white mb-2">Cliente:</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $this->proyecto->cliente->nombre }}</p>
                @if($this->proyecto->cliente->telefono)
                <p class="text-sm text-gray-600 dark:text-gray-400">Tel: {{ $this->proyecto->cliente->telefono }}</p>
                @endif
                @if($this->proyecto->ubicacion)
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Ubicación: {{ $this->proyecto->ubicacion->nombre }}
                    @if($this->proyecto->ubicacion->direccion)
                        - {{ $this->proyecto->ubicacion->direccion }}
                    @endif
                </p>
                @endif
            </div>
            @endif

            <!-- Objeto de Obra -->
            <div class="mb-6">
                <h2 class="font-bold text-gray-900 dark:text-white mb-2">Objeto de Obra:</h2>
                <p class="text-gray-700 dark:text-gray-300">{{ $this->proyecto->nombre }}</p>
                @if($this->proyecto->descripcion)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $this->proyecto->descripcion }}</p>
                @endif
            </div>
        </div>

        <!-- Tabla de Presupuesto -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Concepto</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cant</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Precio Unit.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total (USD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $equipamientoTotal = 0;
                        $manoObraTotal = 0;
                        $materialesTotal = 0;
                        $transporteTotal = 0;
                        $alimentacionTotal = 0;
                    @endphp

                    <!-- Equipamiento -->
                    @php $lineasEquipamiento = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'equipamiento'); @endphp
                    @if($lineasEquipamiento->count() > 0)
                        @foreach($lineasEquipamiento as $linea)
                            @php $equipamientoTotal += $linea->subtotal; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-cube class="w-4 h-4 text-blue-500" />
                                        <span class="font-medium">{{ $linea->descripcion }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ number_format($linea->cantidad, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($linea->costo_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">${{ number_format($linea->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Mano de Obra -->
                    @php $lineasManoObra = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'mano_obra'); @endphp
                    @if($lineasManoObra->count() > 0)
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <td colspan="4" class="px-4 py-2 font-bold text-sm">MANO DE OBRA</td>
                        </tr>
                        @foreach($lineasManoObra as $linea)
                            @php $manoObraTotal += $linea->subtotal; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 pl-8">{{ $linea->descripcion }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($linea->cantidad, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($linea->costo_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">${{ number_format($linea->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Materiales -->
                    @php $lineasMateriales = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'material'); @endphp
                    @if($lineasMateriales->count() > 0)
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <td colspan="4" class="px-4 py-2 font-bold text-sm">MATERIALES</td>
                        </tr>
                        @foreach($lineasMateriales as $linea)
                            @php $materialesTotal += $linea->subtotal; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3 pl-8">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $linea->descripcion }}</span>
                                        @if($linea->item)
                                            <x-heroicon-o-tag class="w-3 h-3 text-gray-400" />
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ number_format($linea->cantidad, 2) }} {{ $linea->item->unidad_medida ?? 'u' }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($linea->costo_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">${{ number_format($linea->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Alimentación -->
                    @php $lineasAlimentacion = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'alimentacion'); @endphp
                    @if($lineasAlimentacion->count() > 0)
                        @foreach($lineasAlimentacion as $linea)
                            @php $alimentacionTotal += $linea->subtotal; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-beaker class="w-4 h-4 text-green-500" />
                                        <span>{{ $linea->descripcion }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ number_format($linea->cantidad, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($linea->costo_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">${{ number_format($linea->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- Transporte -->
                    @php $lineasTransporte = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'transporte'); @endphp
                    @if($lineasTransporte->count() > 0)
                        @foreach($lineasTransporte as $linea)
                            @php $transporteTotal += $linea->subtotal; @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-truck class="w-4 h-4 text-orange-500" />
                                        <span>{{ $linea->descripcion }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ number_format($linea->cantidad, 2) }}</td>
                                <td class="px-4 py-3 text-right">${{ number_format($linea->costo_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-mono">${{ number_format($linea->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>

                <!-- Totales -->
                <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                    @if($equipamientoTotal > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal Equipamiento:</td>
                        <td class="px-4 py-3 text-right font-mono">${{ number_format($equipamientoTotal, 2) }}</td>
                    </tr>
                    @endif
                    @if($manoObraTotal > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal Mano de Obra:</td>
                        <td class="px-4 py-3 text-right font-mono">${{ number_format($manoObraTotal, 2) }}</td>
                    </tr>
                    @endif
                    @if($materialesTotal > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal Materiales:</td>
                        <td class="px-4 py-3 text-right font-mono">${{ number_format($materialesTotal, 2) }}</td>
                    </tr>
                    @endif
                    @if($alimentacionTotal > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal Alimentación:</td>
                        <td class="px-4 py-3 text-right font-mono">${{ number_format($alimentacionTotal, 2) }}</td>
                    </tr>
                    @endif
                    @if($transporteTotal > 0)
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Subtotal Transporte:</td>
                        <td class="px-4 py-3 text-right font-mono">${{ number_format($transporteTotal, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                        <td colspan="3" class="px-4 py-4 text-right text-lg">TOTAL PRESUPUESTO:</td>
                        <td class="px-4 py-4 text-right text-lg font-mono text-primary-600 dark:text-primary-400">
                            ${{ number_format($this->proyecto->presupuesto_total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notas -->
        @if($this->proyecto->notas)
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <h2 class="font-bold text-gray-900 dark:text-white mb-2">Notas:</h2>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $this->proyecto->notas }}</p>
        </div>
        @endif

        <!-- Botones de acción -->
        <div class="flex justify-end gap-3">
            <x-filament::button wire:click="exportarPDF" color="primary">
                <x-heroicon-o-document-arrow-down class="w-5 h-5" />
                Exportar PDF
            </x-filament::button>
            <x-filament::button wire:click="imprimir" color="secondary">
                <x-heroicon-o-printer class="w-5 h-5" />
                Imprimir
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>