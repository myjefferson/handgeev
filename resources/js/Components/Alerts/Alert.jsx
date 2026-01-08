import React, { useEffect, useState } from "react";
import { usePage } from "@inertiajs/react";

export default function Alert() {
    const { props } = usePage();
    const { flash, errors } = props;

    const [visibleMessages, setVisibleMessages] = useState([]);

    useEffect(() => {
        const allMessages = [];

        // Converte mensagens flash (status, success, error, etc)
        if (flash) {
            Object.entries(flash).forEach(([type, message]) => {
                if (message) {
                    allMessages.push({
                        type,
                        message: Array.isArray(message)
                            ? message.join(", ")
                            : message,
                        autoHide: true,
                    });
                }
            });
        }

        // Mensagens de erro de validação
        if (errors && Object.keys(errors).length > 0) {
            allMessages.push({
                type: "error",
                message:
                    Object.keys(errors).length > 1
                        ? `Existem ${Object.keys(errors).length} erros no formulário:`
                        : Object.values(errors)[0],
                list:
                    Object.keys(errors).length > 1
                        ? Object.values(errors)
                        : null,
                autoHide: false,
            });
        }

        setVisibleMessages(allMessages);
    }, [flash, errors]);

    const styles = {
        status: {
            bg: "bg-blue-500/10",
            border: "border-blue-500/20",
            text: "text-blue-400",
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fillRule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clipRule="evenodd"
                    ></path>
                </svg>
            ),
        },
        success: {
            bg: "bg-green-500/10",
            border: "border-green-500/20",
            text: "text-green-400",
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fillRule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clipRule="evenodd"
                    ></path>
                </svg>
            ),
        },
        error: {
            bg: "bg-red-500/10",
            border: "border-red-500/20",
            text: "text-red-400",
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fillRule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clipRule="evenodd"
                    ></path>
                </svg>
            ),
        },
        warning: {
            bg: "bg-yellow-500/10",
            border: "border-yellow-500/20",
            text: "text-yellow-400",
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fillRule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clipRule="evenodd"
                    ></path>
                </svg>
            ),
        },
        info: {
            bg: "bg-cyan-500/10",
            border: "border-cyan-500/20",
            text: "text-cyan-400",
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        fillRule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clipRule="evenodd"
                    ></path>
                </svg>
            ),
        },
    };

    const handleClose = (index) => {
        setVisibleMessages((prev) =>
            prev.filter((_, i) => i !== index)
        );
    };

    useEffect(() => {
        visibleMessages.forEach((msg, i) => {
            if (msg.autoHide) {
                const timeout = setTimeout(() => {
                    handleClose(i);
                }, 5000);
                return () => clearTimeout(timeout);
            }
        });
    }, [visibleMessages]);

    if (visibleMessages.length === 0) return null;

    return (
        <div id="flash-messages">
            {visibleMessages.map((msg, index) => {
                const style = styles[msg.type] || styles.info;

                return (
                    <div
                        key={index}
                        className={`flash-message mb-4 p-4 ${style.bg} ${style.border} rounded-lg ${style.text} transition-all duration-300 ease-in-out`}
                    >
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <div className="flex-shrink-0">{style.icon}</div>
                                <div className="ml-3">
                                    <p className="text-sm font-medium">{msg.message}</p>
                                    {msg.list && (
                                        <ul className="mt-2 text-sm list-disc list-inside">
                                            {msg.list.map((err, i) => (
                                                <li key={i}>{err}</li>
                                            ))}
                                        </ul>
                                    )}
                                </div>
                            </div>
                            <button
                                type="button"
                                className="flash-close flex-shrink-0 ml-3 hover:opacity-70 transition-opacity focus:outline-none"
                                onClick={() => handleClose(index)}
                            >
                                <svg
                                    className="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clipRule="evenodd"
                                    ></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
