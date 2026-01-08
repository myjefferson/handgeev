import React from 'react';
import { Link } from '@inertiajs/react';
import UpgradeBadge from '@/Components/Badges/UpgradeBadge';

const UpsellButton = ({ 
    title = 'Upgrade', 
    iconPrincipal = true, 
    iconLeft = null,
    href = null 
}) => {
    const buttonHref = href || route('subscription.pricing');

    return (
        <>
            <Link 
                href={buttonHref}
                className="flex items-center px-3 py-2 rounded-lg transition-all duration-300 group purple-neon-glow"
            >
                {iconPrincipal && (
                    <div className="w-8 h-8 rounded-full flex items-center justify-center mr-3 bg-gradient-to-r from-purple-500 to-purple-600 shadow-lg shadow-purple-500/30">
                        <i className="fas fa-crown text-white"></i>
                    </div>
                )}
                <div className="flex-1 pr-1">
                    {iconLeft && <span className="mr-2">{iconLeft}</span>}
                    <span className="font-semibold text-white">{title}</span> 
                    <UpgradeBadge />
                </div>
            </Link>

            <style jsx>{`
                .purple-neon-glow {
                    background: rgba(139, 92, 246, 0.12);
                    border: 1px solid rgba(167, 139, 250, 0.6);
                    box-shadow: 0 0 10px rgba(139, 92, 246, 0.4),
                                inset 0 0 15px rgba(139, 92, 246, 0.1);
                    position: relative;
                }

                .purple-neon-glow:hover {
                    background: rgba(139, 92, 246, 0.18);
                    border: 1px solid rgba(167, 139, 250, 0.9);
                    box-shadow: 0 0 20px rgba(139, 92, 246, 0.7),
                                0 0 40px rgba(139, 92, 246, 0.3),
                                inset 0 0 20px rgba(139, 92, 246, 0.15);
                    transform: translateY(-2px);
                }

                /* Efeito de brilho pulsante sutil */
                @keyframes purplePulse {
                    0% { 
                        box-shadow: 0 0 10px rgba(139, 92, 246, 0.4), 
                                   inset 0 0 15px rgba(139, 92, 246, 0.1); 
                    }
                    50% { 
                        box-shadow: 0 0 15px rgba(139, 92, 246, 0.6), 
                                   inset 0 0 20px rgba(139, 92, 246, 0.15); 
                    }
                    100% { 
                        box-shadow: 0 0 10px rgba(139, 92, 246, 0.4), 
                                   inset 0 0 15px rgba(139, 92, 246, 0.1); 
                    }
                }

                .purple-neon-glow {
                    animation: purplePulse 3s infinite ease-in-out;
                }
            `}</style>
        </>
    );
};

export default UpsellButton;