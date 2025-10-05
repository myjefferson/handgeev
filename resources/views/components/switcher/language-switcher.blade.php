<!-- Language Dropdown -->
<div class="relative">
    <button 
        id="languageDropdownButton"
        data-dropdown-toggle="languageDropdown"
        class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-gray-300 bg-slate-800 border border-slate-600 rounded-lg hover:bg-slate-700 hover:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200"
        type="button"
    >
        <div class="flex items-center space-x-2">
            @php
                $currentLocale = app()->getLocale();
                $availableLocales = config('app.available_locales', []);
                $localeName = $availableLocales[$currentLocale] ?? strtoupper($currentLocale);
                
                $flags = [
                    'pt_BR' => 'br',
                    'pt' => 'br', 
                    'en' => 'us',
                    'es' => 'es'
                ];
                $flag = $flags[$currentLocale] ?? 'us';
            @endphp

            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M2 12c0 5.523 4.477 10 10 10s10-4.477 10-10S17.523 2 12 2S2 6.477 2 12"/><path d="M13 2.05S16 6 16 12s-3 9.95-3 9.95m-2 0S8 18 8 12s3-9.95 3-9.95M2.63 15.5h18.74m-18.74-7h18.74"/></g></svg>
            <span class="font-medium">{{ $localeName }}</span>
        </div>
        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
    </button>
    
    <!-- Dropdown menu -->
    <div 
        id="languageDropdown" 
        class="z-10 hidden bg-slate-800 divide-y divide-slate-600 rounded-lg shadow-sm w-44 border border-slate-600"
    >
        <ul class="py-2 text-sm text-gray-300" aria-labelledby="languageDropdownButton">
            @php
                $locales = config('app.available_locales', [
                    'pt_BR' => 'Português',
                    'en' => 'English',
                    'es' => 'Español'
                ]);
            @endphp
            
            @foreach($locales as $locale => $name)
                <li>
                    <a 
                        href="{{ route('lang.switch', $locale) }}" 
                        class="flex items-center justify-between px-4 py-2 hover:bg-slate-700 hover:text-white transition-colors duration-150 {{ app()->getLocale() == $locale ? 'text-teal-400 bg-slate-700' : '' }}"
                    >
                        <div class="flex items-center space-x-3">
                            @php
                                $flagIcons = [
                                    'pt_BR' => 'br',
                                    'pt' => 'br',
                                    'en' => 'us',
                                    'es' => 'es'
                                ];
                                $flagIcon = $flagIcons[$locale] ?? 'us';
                            @endphp
                            <span class="fi fi-{{ $flagIcon }}"></span>
                            <span>{{ $name }}</span>
                        </div>
                        @if(app()->getLocale() == $locale)
                            <svg class="w-4 h-4 text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>