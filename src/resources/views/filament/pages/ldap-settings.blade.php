<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-between">
            <x-filament::button 
                type="button" 
                color="secondary" 
                wire:click="testConnection"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove wire:target="testConnection">
                    🔌 Probar Conexión
                </span>
                <span wire:loading wire:target="testConnection">
                    Probando...
                </span>
            </x-filament::button>

            <x-filament::button type="submit" color="primary">
                Guardar Configuración
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
