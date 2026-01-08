import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import LanguageSwitcher from '@/Components/Switchers/LanguageSwitcher';
import useLang from '@/Hooks/useLang';

const Footer = ({ currentYear }) => {
    const { __ } = useLang('footer')
    return (
        <footer className="footer py-6 px-4 mt-auto">
            <div className="max-w-7xl mx-auto">
                <div className="flex flex-col md:flex-row justify-between items-center">
                    <div className="flex items-center mb-4 md:mb-0">
                        <Link href={route('landing.handgeev')}>
                            <img 
                                className="w-28 opacity-60 grayscale hover:grayscale-0 transition-all" 
                                src="/assets/images/logo.png" 
                                alt="Handgeev Logo" 
                            />
                        </Link>
                    </div>
                    
                    <div className="flex space-x-6 mb-4 md:mb-0">
                        <Link
                            href={route('legal.terms')} 
                            className="text-slate-400 hover:text-primary-500 transition-colors text-sm"
                        >
                            {__('links.terms')}
                        </Link>
                        <Link 
                            href={route('legal.privacy')} 
                            className="text-slate-400 hover:text-primary-500 transition-colors text-sm"
                        >
                            {__('links.privacy')}
                        </Link>
                        <a 
                            href="#" 
                            className="text-slate-400 hover:text-primary-500 transition-colors text-sm"
                        >
                            {__('links.support')}
                        </a>
                    </div>
                    
                    <div className="flex space-x-4">
                        <a 
                            href="https://www.instagram.com/handgeev/" 
                            target="_blank"
                            rel="noopener noreferrer"
                            className="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" 
                            title={__('social.instagram')}
                        >
                            <i className="fab fa-instagram"></i>
                        </a>
                        <a 
                            href="https://www.linkedin.com/company/handgeev" 
                            target="_blank"
                            rel="noopener noreferrer"
                            className="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" 
                            title={__('social.linkedin')}
                        >
                            <i className="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div className="flex flex-col md:flex-row justify-between items-center border-t border-slate-700 mt-4 pt-4">
                    <p className="text-xs text-slate-500 mb-4 md:mb-0">
                        {__('copyright').replace(':year', currentYear)}
                    </p>
                    <LanguageSwitcher />
                </div>
            </div>
        </footer>
    );
};

export default Footer;