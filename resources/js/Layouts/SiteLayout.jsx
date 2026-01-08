import React from "react";
import { Head } from "@inertiajs/react";
import Footer from "@/Components/Footer/Footer";
import '@/Layouts/css/dashboard.css'

export default function SiteLayout({
    title = "Handgeev",
    description = "HandGeev - Crie e gerencie suas APIs de forma simples",
    keywords = "api, workspace, json, handgeev, desenvolvimento",
    children,
    styles = [],
    scripts = [],
}) {
    return (
        <html lang={document.documentElement.lang || "pt-BR"}>
            <Head>
                <meta name="csrf-token" content={document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content")}
                />

                {/* Título dinâmico */}
                <title>{title ? `${title} - Handgeev` : "Handgeev"}</title>

                {/* Meta tags SEO */}
                <meta name="description" content={description} />
                <meta name="keywords" content={keywords} />

                {/* Estilos adicionais (opcionais) */}
                {styles.map((href, i) => (
                    <link key={i} rel="stylesheet" href={href} />
                ))}
            </Head>

            <body className="font-sans antialiased text-white bg-slate-900 min-h-screen flex flex-col">
                <main className="flex-grow">
                    {children}
                </main>

                <Footer />

                {/* Scripts adicionais (se houver) */}
                {scripts.map((src, i) => (
                    <script key={i} src={src}></script>
                ))}
            </body>
        </html>
    );
}