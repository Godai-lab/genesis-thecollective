<?php

namespace App\Livewire\Generador\Components;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Modelable;

/**
 * Selector de modelos reutilizable (UI flotante inferior izquierda)
 *
 * IMPORTANTE: COMPATIBILIDAD TOTAL
 * - Si NO se pasa :eventName → emite 'image-generator-model-selected' (comportamiento anterior)
 * - Si SÍ se pasa :eventName → emite el evento personalizado
 * 
 * Props esperadas:
 * - models: array<string,string> [key => label]
 * - selected: string clave actual
 * - eventName: string nombre del evento a emitir al seleccionar (OPCIONAL)
 * - title: string título del selector
 * 
 * Ejemplos de uso:
 * - Sin personalizar: <livewire:generador.components.model-selector />
 * - Con personalizar: <livewire:generador.components.model-selector :eventName="'mi-evento'" />
 */
class ModelSelector extends Component
{
   
    public array $models = [];
    #[Modelable]
    public string $selected = 'imagen-4.0-generate-preview-06-06';
    public ?string $eventName = null; // ✅ CAMBIO: Ahora es nullable para compatibilidad
    public string $title = 'Modelo de IA';

    public function mount()
    {
        // Inicialización del componente
    }
    
    /**
     * Cambia el modelo seleccionado y emite el evento correspondiente
     * 
     * COMPATIBILIDAD TOTAL:
     * - Si eventName está configurado → emite ese evento
     * - Si eventName NO está configurado → emite 'image-generator-model-selected' (comportamiento anterior)
     */
    public function cambiarModelo(string $key)
    {
        $this->selected = $key; 
        
        // ✅ COMPATIBILIDAD TOTAL: Si no se pasa eventName, usar el comportamiento anterior
        if ($this->eventName && $this->eventName !== '') {
            // Uso personalizado: emitir el evento configurado
            $this->dispatch($this->eventName, key: $key);
        } else {
            // Comportamiento anterior (compatible): emitir el evento por defecto
            $this->dispatch('image-generator-model-selected', key: $key);
        }
    }
    
    // Helper para obtener el nombre del modelo
    public function getModelName(string $key): string
    {
        $model = $this->models[$key] ?? null;
        if (is_array($model)) {
            return $model['name'] ?? 'Desconocido';
        }
        return $model ?? 'Desconocido';
    }

    // Helper para verificar si el modelo tiene información detallada
    public function hasDetailedInfo(string $key): bool
    {
        return isset($this->models[$key]) && is_array($this->models[$key]);
    }
    
    public function render()
    {
        return view('livewire.generador.components.model-selector');
    }
}


