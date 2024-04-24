<x-app-layout>
    <div class="flex flex-col xl:flex-row ">
        <!-- Columna izquierda -->
        <div class="flex-1 xl:pe-3 py-3 xl:py-0">
            <div class="rounded-lg bg-red-50 dark:bg-gray-700 h-screen">
                <embed class="rounded-lg" src="{{ $pdfUrl }}" type="application/pdf" width="100%" height="100%" />
           </div>
        </div>
        
        <!-- Columna derecha -->
        <div class="flex-1">

        </div>
    </div>
</x-app-layout>
