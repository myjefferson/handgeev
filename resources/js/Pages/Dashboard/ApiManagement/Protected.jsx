import { useState } from 'react';
import { Head } from '@inertiajs/react';
import Footer from '@/Components/Footer/Footer';

export default function Protected({ workspace, user, verifyUrl }) {
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const handleSubmit = async (e) => {
        e.preventDefault();

        setLoading(true);
        setError(null);

        try {
            const response = await fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ password }),
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                setError(data.message);
                setLoading(false);
            }
        } catch (err) {
            setError('Erro ao verificar senha. Tente novamente.');
            setLoading(false);
        }
    };

    const requestAccess = () => {
        alert('Entre em contato com o proprietário do workspace para solicitar acesso.');
    };

    return (
        <>
            <Head
                title={workspace.title}
                meta={[
                    { name: 'description', content: `Workspace compartilhado por ${user.name}` },
                ]}
            />

            <div className="bg-slate-900 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full space-y-8">
                    <div>
                        <div className="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-teal-100">
                            <i className="fas fa-lock text-teal-600"></i>
                        </div>

                        <h2 className="mt-6 text-center text-3xl font-extrabold text-white">
                            Workspace Protegido
                        </h2>

                        <p className="mt-2 text-center text-sm text-gray-400">
                            Este workspace está protegido por senha
                        </p>

                        <p className="mt-1 text-center text-sm text-gray-500">
                            {workspace.title}
                        </p>

                        <p className="mt-1 text-center text-xs text-gray-600">
                            Compartilhado por: {user.name}
                        </p>
                    </div>

                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                        <div>
                            <input
                                type="password"
                                required
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="Digite a senha do workspace"
                                className="
                                    relative block w-full px-3 py-3
                                    border border-gray-600
                                    rounded-lg
                                    bg-gray-700 text-white
                                    placeholder-gray-400
                                    focus:outline-none focus:ring-teal-500 focus:border-teal-500
                                "
                            />

                            {error && (
                                <div className="mt-2 text-sm text-red-500">
                                    {error}
                                </div>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="
                                w-full flex justify-center items-center
                                py-3 px-4 rounded-lg
                                text-sm font-medium text-white
                                bg-teal-600 hover:bg-teal-700
                                transition-colors
                                disabled:opacity-60
                            "
                        >
                            {loading ? (
                                <>
                                    Verificando...
                                    <i className="fas fa-spinner fa-spin ml-2"></i>
                                </>
                            ) : (
                                'Acessar Workspace'
                            )}
                        </button>
                    </form>

                    <div className="text-center">
                        <p className="text-sm text-gray-400">
                            Precisa de acesso?{' '}
                            <button
                                onClick={requestAccess}
                                className="font-medium text-teal-400 hover:text-teal-300"
                            >
                                Solicite ao proprietário
                            </button>
                        </p>
                    </div>
                </div>
            </div>

            <Footer />
        </>
    );
}
