<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class BuscarNegocios extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationGroup = 'Operaciones';
    protected static ?string $title = 'Buscar Negocios';
    protected static ?string $navigationLabel = 'Buscar Negocios';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.buscar-negocios';

    public ?string $busqueda = '';
    public array $resultados = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('busqueda')
                    ->label('Buscar negocio')
                    ->placeholder('Ej: Vinil Arte, Cucaña, El Patio, Peaky Pan...')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state) {
                        $this->buscar($state);
                    }),
            ]);
    }

    public function buscar(?string $termino): void
    {
        if (empty($termino) || strlen($termino) < 2) {
            $this->resultados = [];
            return;
        }

        $this->resultados = ClienteUbicacion::with('cliente')
            ->where('tipo', 'negocio')
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'ilike', '%' . $termino . '%')
                  ->orWhereHas('cliente', function ($q2) use ($termino) {
                      $q2->where('nombre', 'ilike', '%' . $termino . '%');
                  });
            })
            ->where('activo', true)
            ->limit(50)
            ->get()
            ->map(fn ($u) => [
                'cliente_id' => $u->cliente->codigo,
                'cliente_nombre' => $u->cliente->nombre,
                'cliente_telefono' => $u->cliente->telefono,
                'cliente_email' => $u->cliente->email,
                'ubicacion_nombre' => $u->nombre,
                'ubicacion_direccion' => $u->direccion,
                'ubicacion_ciudad' => $u->municipio,
                'contacto' => $u->contacto_nombre,
                'telefono_local' => $u->contacto_telefono,
                'tipo_negocio' => $u->tipo_negocio_id ? 'Sí' : 'Sin categoría',
                'link' => url('/admin/clientes/' . $u->cliente_id . '/edit'),
            ])
            ->toArray();
    }
}