
<div class="relative block w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
    <div>
        <div class="absolute right-2 top-2">
            @include('components.projects.dropdown-options', [ $index, $project ])
        </div>
        <label class="text-sm">projeto</label>
        <p class="mb-2 font-bold tracking-tight text-gray-900 dark:text-white">{{ $project->title }}</p>
    </div>
    <div>
        <label class="text-sm">sobre</label>
        <p class="mb-2 font-bold tracking-tight text-gray-900 dark:text-white">{{ $project->subtitle }}</p>
    </div>
</div>
