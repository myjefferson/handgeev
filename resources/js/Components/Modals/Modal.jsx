// resources/js/Components/Modal.jsx
import React, { useEffect } from 'react';

export default function Modal({ children, show = false, maxWidth = '2xl', onClose = () => {} }) {
    useEffect(() => {
        if (show) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [show]);

    if (!show) {
        return null;
    }

    const maxWidthClass = {
        'sm': 'sm:max-w-sm',
        'md': 'sm:max-w-md',
        'lg': 'sm:max-w-lg',
        'xl': 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
        '5xl': 'sm:max-w-5xl',
        '6xl': 'sm:max-w-6xl',
        '7xl': 'sm:max-w-7xl',
    }[maxWidth];

    return (
        <div className="fixed flex inset-0 items-center px-4 py-6 sm:px-0 z-50">
            <div className="fixed inset-0 transform transition-all backdrop-blur-sm">
                <div className="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" onClick={onClose}></div>
            </div>

            <div className={`mb-6 bg-transparent rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto ${maxWidthClass}`}>
                {children}
            </div>
        </div>
    );
}