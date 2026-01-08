import React, { useState, useRef, useEffect } from 'react';

export default function StructureTypesDropdown({ 
    value, 
    onChange, 
    options, 
    className = "",
    disabled = false,
    placeholder = "Selecione..."
}) {
    const [isOpen, setIsOpen] = useState(false);
    const dropdownRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const selectedOption = options.flatMap(group => group.options).find(opt => opt.value === value);

    return (
        <div className="relative" ref={dropdownRef}>
            <button
                type="button"
                onClick={() => !disabled && setIsOpen(!isOpen)}
                disabled={disabled}
                className={`w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white text-left focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent flex justify-between items-center ${className} ${disabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-500'}`}
            >
                <div className="flex items-center">
                    {selectedOption ? (
                        <>
                            {selectedOption.icon && (
                                <i className={`fas ${selectedOption.icon} mr-2 text-teal-400`}></i>
                            )}
                            <span>{selectedOption.label}</span>
                            {selectedOption.badge}
                        </>
                    ) : (
                        <span className="text-gray-400">{placeholder}</span>
                    )}
                </div>
                <i className={`fas fa-chevron-${isOpen ? 'up' : 'down'} text-gray-400`}></i>
            </button>

            {isOpen && (
                <div className="absolute z-10 w-full mt-1 bg-slate-700 border border-slate-600 rounded-lg shadow-lg max-h-96 overflow-y-auto">
                    {options.map((group, groupIndex) => (
                        <div key={groupIndex}>
                            {group.label && (
                                <div className="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-slate-800">
                                    {group.label}
                                </div>
                            )}
                            {group.options.map((option) => (
                                <button
                                    key={option.value}
                                    type="button"
                                    onClick={() => {
                                        onChange(option.value);
                                        setIsOpen(false);
                                    }}
                                    className="w-full px-3 py-2 text-left hover:bg-slate-600 flex items-center justify-between"
                                >
                                    <div className="flex items-center">
                                        {option.icon && (
                                            <i className={`fas ${option.icon} mr-2 text-teal-400`}></i>
                                        )}
                                        <span className="text-white">{option.label}</span>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        {option.badge}
                                        {value === option.value && (
                                            <i className="fas fa-check text-teal-400"></i>
                                        )}
                                    </div>
                                </button>
                            ))}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}