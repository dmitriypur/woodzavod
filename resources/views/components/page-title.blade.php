<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg mb-6">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $title }}</h1>
        
        @if(isset($breadcrumbs))
            <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        @endif
        
        @if(isset($description))
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $description }}</p>
        @endif
    </div>
</div>