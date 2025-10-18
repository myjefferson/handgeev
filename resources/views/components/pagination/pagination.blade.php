@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-lg">
                    <i class="fas fa-chevron-left mr-2"></i>
                    {{ __('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-slate-800 transition-colors">
                    <i class="fas fa-chevron-left mr-2"></i>
                    {{ __('pagination.previous') }}
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 rounded-lg hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-slate-800 transition-colors">
                    {{ __('pagination.next') }}
                    <i class="fas fa-chevron-right ml-2"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-lg">
                    {{ __('pagination.next') }}
                    <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-slate-400">
                    {!! __('Mostrando') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-white">{{ $paginator->firstItem() }}</span>
                        {!! __('a') !!}
                        <span class="font-medium text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        <span class="font-medium text-white">{{ $paginator->count() }}</span>
                    @endif
                    {!! __('de') !!}
                    <span class="font-medium text-white">{{ $paginator->total() }}</span>
                    {!! __('resultados') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-l-lg" aria-hidden="true">
                                <i class="fas fa-chevron-left w-5 h-5"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 rounded-l-lg hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors" aria-label="{{ __('pagination.previous') }}">
                            <i class="fas fa-chevron-left w-5 h-5"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-teal-600 border border-teal-500 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-300 bg-slate-800 border border-slate-600 rounded-r-lg hover:bg-slate-700 focus:z-10 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-teal-500 transition-colors" aria-label="{{ __('pagination.next') }}">
                            <i class="fas fa-chevron-right w-5 h-5"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-slate-500 bg-slate-800 border border-slate-600 cursor-default rounded-r-lg" aria-hidden="true">
                                <i class="fas fa-chevron-right w-5 h-5"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif