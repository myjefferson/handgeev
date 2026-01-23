import React, { useState, useRef, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';

const LanguageSwitcher = () => {
    const { props } = usePage();
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const dropdownRef = useRef(null);

    const currentLocale = props.locale || 'pt_BR';
    const availableLocales = props.available_locales || {
        'pt_BR': 'Português',
        'en': 'English', 
        'es': 'Español'
    };

    const localeName = availableLocales[currentLocale] || currentLocale.toUpperCase();

    const flags = {
        'pt_BR': 'br',
        'pt': 'br',
        'en': 'us',
        'es': 'es'
    };

    const currentFlag = flags[currentLocale] || 'us';

    // Fechar dropdown ao clicar fora
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsDropdownOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const toggleDropdown = () => {
        setIsDropdownOpen(!isDropdownOpen);
    };

    return (
        <div className="relative" ref={dropdownRef}>
            <button 
                onClick={toggleDropdown}
                className="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-gray-300 bg-slate-800 border border-slate-600 rounded-lg hover:bg-slate-700 hover:border-teal-400 focus:outline-none focus:ring-2 focus:ring-teal-500 transition-colors duration-200 cursor-pointer"
                type="button"
            >
                <div className="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5">
                            <path d="M2 12c0 5.523 4.477 10 10 10s10-4.477 10-10S17.523 2 12 2S2 6.477 2 12"/>
                            <path d="M13 2.05S16 6 16 12s-3 9.95-3 9.95m-2 0S8 18 8 12s3-9.95 3-9.95M2.63 15.5h18.74m-18.74-7h18.74"/>
                        </g>
                    </svg>
                    <span className="font-medium">{localeName}</span>
                </div>
                <svg 
                    className={`w-2.5 h-2.5 ms-3 transition-transform ${isDropdownOpen ? 'rotate-180' : ''}`} 
                    aria-hidden="true" 
                    xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 10 6"
                >
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>
            
            {/* Dropdown menu - Agora posicionado acima do botão */}
            <div 
                className={`absolute z-10 bg-slate-800 divide-y divide-slate-600 rounded-lg shadow-sm w-44 border border-slate-600 transition-all duration-200 ${
                    isDropdownOpen ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible -translate-y-2'
                }`}
                style={{ 
                    bottom: '100%', // Posiciona na parte inferior do elemento pai
                    right: 0, 
                    marginBottom: '0.5rem' // Adiciona um pequeno espaço entre o dropdown e o botão
                }}
            >
                <ul className="py-2 text-sm text-gray-300">
                    {Object.entries(availableLocales).map(([locale, name]) => {
                        const flagIcons = {
                            'pt_BR': 'br',
                            'pt': 'br',
                            'en': 'us',
                            'es': 'es'
                        };
                        const flagIcon = flagIcons[locale] || 'us';
                        
                        return (
                            <li key={locale}>
                                <Link 
                                    href={route('lang.switch', locale)}
                                    className={`flex items-center justify-between px-4 py-2 hover:bg-slate-700 hover:text-white transition-colors duration-150 ${
                                        currentLocale === locale ? 'text-teal-400 bg-slate-700' : ''
                                    }`}
                                    onClick={() => setIsDropdownOpen(false)}
                                >
                                    <div className="flex items-center space-x-3">
                                        <span className={`fi fi-${flagIcon}`}></span>
                                        <span>{name}</span>
                                    </div>
                                    {currentLocale === locale && (
                                        <svg className="w-4 h-4 text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
                                        </svg>
                                    )}
                                </Link>
                            </li>
                        );
                    })}
                </ul>
            </div>
        </div>
    );
};

export default LanguageSwitcher;