

<div class="flex justify-between items-center">
    <h3 class="title-header text-2xl font-semibold">{{ $title }}</h3>
    <div class="flex gap-3 items-center">
        @if (isset($options))
            @foreach ($options as $option)
                <a href="{{ $option['route'] }}" class="flex items-center font-medium bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl py-2 px-4">
                    {{ $option['title'] }}
                </a>
            @endforeach
        @endif
        @if (isset($buttonJson))
            @if ($buttonJson['active'])
                @include('components.buttons.button-json-workspace', $workspace)
            @endif
        @endif
        {{-- @if (isset($buttonJson))
            @if ($buttonJson['active'])
                @include('components.buttons.button-share-workspace')
            @endif
        @endif --}}
        @if (isset($buttonSettings))
            @if ($buttonSettings['active'])
                @include('components.buttons.button-settings-workspace', $workspace)
            @endif
        @endif
    </div>
</div>
