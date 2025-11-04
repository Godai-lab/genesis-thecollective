<?php

namespace App\Livewire\Generador\Components;

use Livewire\Component;
use Livewire\Attributes\Reactive;

/**
 * Componente de estado "Generando..." reutilizable
 *
 * Props esperadas:
 * - message: string mensaje personalizable (ej: "Generando imagen...", "Procesando video...")
 * - show: bool controla la visibilidad del componente
 * - icon: string (opcional) icono SVG personalizado
 * - subtitle: string (opcional) texto adicional debajo del mensaje principal
 */
class GeneratingStatus extends Component
{
    #[Reactive]
    public bool $show = false;
    
    public string $message = 'Generando...';
    public ?string $subtitle = null;
    public ?string $icon = null;
    

    public function mount(
        bool $show = false,
        string $message = 'Generando...',
        ?string $subtitle = null,
        ?string $icon = null
    ): void {
        $this->show = $show;
        $this->message = $message;
        $this->subtitle = $subtitle;
        $this->icon = $icon;
    }

    public function render()
    {
        return view('livewire.generador.components.generating-status');
    }
}