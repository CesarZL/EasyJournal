<x-edit-layout>
    @livewire('edit-menu', ['article' => $article])

    <x-article :article="$article" :content="$content" :templates="$templates" />
    
</x-edit-layout>
