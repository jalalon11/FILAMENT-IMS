<?php

namespace App\Livewire;

use Livewire\Component;

class FloatingActionButton extends Component
{
    public bool $isOpen = false;

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.floating-action-button');
    }
}
