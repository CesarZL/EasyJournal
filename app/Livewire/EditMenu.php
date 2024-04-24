<?php

namespace App\Livewire;

use Livewire\Component;

class EditMenu extends Component
{
    public $article;

    public function mount($article)
    {
        $this->article = $article;
    }

    public function render()
    {
        return view('edit-menu', ['article' => $this->article]);
    }
}
