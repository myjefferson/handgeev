import React, { useEffect } from "react";
import { Head, Link } from "@inertiajs/react";
import Footer from "@/Components/Footer/Footer";

export default function LegalLayout({
    title = "HandGeev",
    description = "HandGeev - Crie e gerencie suas APIs de forma simples",
    keywords = "api, workspace, json, handgeev, desenvolvimento",
    children,
}) {
    // Controla o tema escuro
    useEffect(() => {
        const prefersDark =
            localStorage.getItem("dark-mode") === "true" ||
            (!localStorage.getItem("dark-mode") &&
                window.matchMedia("(prefers-color-scheme: dark)").matches);

        if (prefersDark) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    }, []);

    return (
        <>
            <Head>
                <title>{title ? `${title} - HandGeev` : "HandGeev"}</title>

                {/* SEO */}
                <meta name="description" content={description} />
                <meta name="keywords" content={keywords} />
            </Head>

            <body className="h-full bg-gray-900 text-gray-100 transition-colors duration-200 flex flex-col">
                {/* Header */}
                <header className="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between items-center py-4">
                            {/* Logo */}
                            <div className="flex items-center">
                                <Link href={route("landing.handgeev")}>
                                    <img
                                        className="w-44"
                                        src="/assets/images/logo.png"
                                        alt="Handgeev Logo"
                                    />
                                </Link>
                            </div>

                            {/* Botão Voltar */}
                            <nav className="flex items-center space-x-6">
                                <button
                                    onClick={() => window.history.back()}
                                    className="text-lg flex items-center bg-transparent hover:border-teal-500 font-medium transition-colors px-4 py-2 text-white border border-white rounded-lg"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            fill="currentColor"
                                            d="M19 11H7.83l4.88-4.88c.39-.39.39-1.03 0-1.42a.996.996 0 0 0-1.41 0l-6.59 6.59a.996.996 0 0 0 0 1.41l6.59 6.59a.996.996 0 1 0 1.41-1.41L7.83 13H19c.55 0 1-.45 1-1s-.45-1-1-1"
                                        />
                                    </svg>
                                    <span className="ml-2">Voltar</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="flex-1 py-14">
                    <div className="max-w-5xl mx-auto px-6">
                        <div className="legal-content prose dark:prose-invert max-w-none">
                            {children}
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <Footer />

                {/* Tailwind classes equivalentes ao CSS inline original */}
                <style>{`
                    .legal-content h1 {
                        @apply text-3xl font-bold text-gray-900 dark:text-white mb-6;
                    }
                    .legal-content h2 {
                        @apply text-2xl font-bold text-gray-900 dark:text-white mt-12 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700;
                    }
                    .legal-content h3 {
                        @apply text-xl font-semibold text-gray-800 dark:text-gray-200 mt-8 mb-4;
                    }
                    .legal-content h4 {
                        @apply text-lg font-medium text-gray-800 dark:text-gray-200 mt-6 mb-3;
                    }
                    .legal-content p {
                        @apply text-gray-700 dark:text-gray-300 mb-4 leading-relaxed;
                    }
                    .legal-content ul, .legal-content ol {
                        @apply text-gray-700 dark:text-gray-300 mb-6 space-y-2;
                    }
                    .legal-content ul {
                        @apply list-disc list-inside;
                    }
                    .legal-content ol {
                        @apply list-decimal list-inside;
                    }
                    .legal-content strong {
                        @apply font-semibold text-gray-900 dark:text-white;
                    }
                    .legal-content a {
                        @apply text-teal-600 dark:text-teal-400 hover:text-teal-700 dark:hover:text-teal-300 underline transition-colors;
                    }
                    .legal-content blockquote {
                        @apply border-l-4 border-teal-500 dark:border-teal-400 pl-4 py-2 my-4 bg-teal-50 dark:bg-teal-900/20 text-gray-700 dark:text-gray-300;
                    }
                    .legal-content .lead {
                        @apply text-lg text-gray-800 dark:text-gray-200 font-medium;
                    }
                `}</style>
            </body>
        </>
    );
}