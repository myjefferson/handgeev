
<div class="relative block w-full p-5 border border-gray-700 rounded-lg shadow bg-gray-800">
    <div class="flex justify-end space-x-2 right-2 top-2">
        @include('components.buttons.button-card-viewjson', [
            'buttonViewJson' => [
                'route' => route('api.courseById'),
                'inputValue' => $course->id,
                'inputName' => 'idCourse'
            ],
        ])
        @include('components.course.dropdown-options', [ $index, $course ])
    </div>
    <div>
        <label class="text-sm text-gray-500">Curso</label>
        <p class="text-lg mb-2 font-bold tracking-tight text-gray-900 dark:text-white">{{ $course->title }}</p>
    </div>
    <div>
        <label class="text-sm text-gray-500">Descrição</label>
        <p class="font-normal"> {{ $course->description }} </p>
    </div>
</div>
