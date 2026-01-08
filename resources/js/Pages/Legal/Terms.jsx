import React from 'react';
import { Head } from '@inertiajs/react';
import LegalLayout from '@/Layouts/LegalLayout';

export default function TermsOfUse() {
    const currentDate = new Date('2025-10-01').toLocaleDateString('pt-BR');

    return (
        <LegalLayout>
            <div className='px-10'>
                <Head className="m-10">
                    <title>Termos de Uso</title>
                    <meta 
                        name="description" 
                        content="Termos de Uso do HandGeev - Conheça as regras e condições para uso da nossa plataforma" 
                    />
                </Head>

                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-white leading-7">
                    {/* Header */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            Termos de Uso
                        </h1>
                        <p className="text-lg text-gray-600 dark:text-gray-400">
                            Última atualização: {currentDate}
                        </p>
                    </div>

                    {/* Content */}
                    <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 lg:p-12 legal-content">
                        <div className="max-w-none">
                            <p className="lead mb-8">
                                Bem-vindo ao HandGeev! Estes Termos de Uso regem o uso da nossa plataforma de criação e gerenciamento de workspaces para APIs.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">1. Aceitação dos Termos</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Ao acessar ou usar o HandGeev, você concorda em ficar vinculado a estes Termos de Uso. Se você não concordar com qualquer parte destes termos, não poderá acessar nosso serviço.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">2. Definições</h2>
                            <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                                <li><strong>HandGeev:</strong> Plataforma online para criação e gerenciamento de workspaces de API</li>
                                <li><strong>Usuário:</strong> Pessoa física ou jurídica que utiliza nossos serviços</li>
                                <li><strong>Workspace:</strong> Ambiente virtual para organização de tópicos e campos de API</li>
                                <li><strong>Conteúdo do Usuário:</strong> Dados, informações e materiais inseridos pelo usuário</li>
                                <li><strong>API:</strong> Interface de programação de aplicações fornecida pelo serviço</li>
                            </ul>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">3. Cadastro e Conta</h2>
                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">3.1. Elegibilidade</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Você deve ter pelo menos 18 anos ou a maioridade legal em sua jurisdição para usar nosso serviço. Ao se cadastrar, você declara e garante que possui a capacidade legal para celebrar este contrato.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">3.2. Responsabilidade da Conta</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Você é responsável por manter a confidencialidade de sua conta e senha, e por restringir o acesso ao seu computador. Você concorda em aceitar a responsabilidade por todas as atividades que ocorram sob sua conta ou senha.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">3.3. Verificação de Conta</h3>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Reservamo-nos o direito de suspender ou encerrar contas que forneçam informações falsas, estejam envolvidas em atividades fraudulentas ou violem estes Termos.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">4. Serviços e Limitações</h2>
                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.1. Funcionalidades</h3>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                O HandGeev oferece as seguintes funcionalidades principais:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                                <li>Criação e gerenciamento de workspaces</li>
                                <li>Organização de dados em tópicos e campos</li>
                                <li>Exportação e importação de dados em formato JSON</li>
                                <li>Geração de endpoints Geev APIful</li>
                                <li>Colaboração em workspaces com outros usuários</li>
                                <li>Controle de visibilidade de campos</li>
                            </ul>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.2. Limitações de Uso</h3>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                Você concorda em não utilizar o serviço para:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                                <li>Atividades ilegais, fraudulentas ou não autorizadas</li>
                                <li>Violar qualquer lei local, estadual, nacional ou internacional</li>
                                <li>Enviar spam, vírus ou conteúdo malicioso</li>
                                <li>Tentar obter acesso não autorizado a outros sistemas ou dados</li>
                                <li>Interromper a integridade, desempenho ou segurança do serviço</li>
                                <li>Criar conteúdo que seja ofensivo, difamatório ou prejudicial</li>
                            </ul>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.3. Limites de Uso</h3>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                Podemos estabelecer limites de uso para garantir a qualidade do serviço, incluindo limites de:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                                <li>Número de workspaces por conta</li>
                                <li>Número de campos por workspace</li>
                                <li>Taxa de requisições API</li>
                                <li>Armazenamento de dados</li>
                            </ul>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">5. Conteúdo do Usuário</h2>
                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">5.1. Propriedade</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Você mantém todos os direitos de propriedade intelectual sobre seu conteúdo. Ao enviar conteúdo para o HandGeev, você nos concede uma licença mundial não exclusiva para hospedar, armazenar, reproduzir e exibir esse conteúdo conforme necessário para fornecer o serviço.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">5.2. Responsabilidade pelo Conteúdo</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Você é exclusivamente responsável pelo conteúdo que criar, publicar ou exibir através do serviço. O HandGeev não se responsabiliza por qualquer conteúdo do usuário e não endossa opiniões expressas pelos usuários.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">5.3. Conteúdo Proibido</h3>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                Você não deve criar ou armazenar conteúdo que:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                                <li>Infrinja direitos de propriedade intelectual de terceiros</li>
                                <li>Seja ilegal, obsceno, difamatório ou ameaçador</li>
                                <li>Contenha informações pessoais sensíveis sem consentimento</li>
                                <li>Promova discriminação, ódio ou violência</li>
                            </ul>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">6. Planos e Pagamentos</h2>
                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">6.1. Planos Gratuitos</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Oferecemos um plano gratuito com funcionalidades limitadas. Reservamo-nos o direito de modificar, limitar ou descontinuar o plano gratuito a qualquer momento, mediante aviso prévio.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">6.2. Planos Premium</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Planos premium são cobrados conforme as tarifas publicadas em nosso site. Os pagamentos são processados de forma segura através de nossos provedores de pagamento autorizados. Planos pagos são não reembolsáveis, exceto conforme exigido por lei.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">6.3. Renovações e Cancelamentos</h3>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Planos premium renovam-se automaticamente no final de cada período de faturamento. Você pode cancelar sua assinatura a qualquer momento através do painel de controle da sua conta.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">7. Propriedade Intelectual</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                O HandGeev e seu conteúdo original, recursos, funcionalidades e design são e permanecerão propriedade exclusiva da HandGeev e de seus licenciadores. Nossas marcas registradas e marcas comerciais não podem ser usadas em conexão com qualquer produto ou serviço sem o consentimento prévio por escrito.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">8. Limitação de Responsabilidade</h2>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                Em nenhum caso o HandGeev, seus diretores, funcionários, parceiros ou agentes serão responsáveis por quaisquer danos indiretos, incidentais, especiais, consequenciais ou punitivos, incluindo perda de lucros, dados, uso, goodwill, ou outras perdas intangíveis, resultantes de:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                                <li>Seu acesso ou uso ou incapacidade de acessar ou usar o serviço</li>
                                <li>Qualquer conduta ou conteúdo de terceiros no serviço</li>
                                <li>Qualquer conteúdo obtido do serviço</li>
                                <li>Acesso não autorizado, uso ou alteração de suas transmissões ou conteúdo</li>
                            </ul>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">9. Modificações do Serviço</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Reservamo-nos o direito de modificar ou descontinuar, temporária ou permanentemente, o serviço (ou qualquer parte dele) com ou sem aviso prévio. Não seremos responsáveis perante você ou qualquer terceiro por qualquer modificação, alteração de preço, suspensão ou descontinuação do serviço.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">10. Rescisão</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Podemos rescindir ou suspender seu acesso imediatamente, sem aviso prévio ou responsabilidade, por qualquer motivo, incluindo, sem limitação, se você violar os Termos. Após a rescisão, seu direito de usar o serviço cessará imediatamente.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">11. Lei Aplicável</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Estes Termos serão regidos e interpretados de acordo com as leis da República Federativa do Brasil, sem considerar seus conflitos de disposições legais.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">12. Disposições Gerais</h2>
                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">12.1. Acordo Integral</h3>
                            <p className="mb-4 text-gray-700 dark:text-gray-300">
                                Estes Termos constituem o acordo integral entre você e o HandGeev em relação ao uso do serviço e substituem todos os acordos anteriores e contemporâneos.
                            </p>

                            <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">12.2. Divisibilidade</h3>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Se qualquer disposição destes Termos for considerada inválida ou inexequível por um tribunal, as demais disposições permanecerão em pleno vigor e efeito.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">13. Alterações nos Termos</h2>
                            <p className="mb-6 text-gray-700 dark:text-gray-300">
                                Reservamo-nos o direito, a nosso exclusivo critério, de modificar ou substituir estes Termos a qualquer momento. Se uma revisão for material, tentaremos fornecer um aviso com pelo menos 30 dias de antecedência antes que os novos termos entrem em vigor. O que constitui uma alteração material será determinado a nosso exclusivo critério.
                            </p>

                            <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">14. Contato</h2>
                            <p className="mb-3 text-gray-700 dark:text-gray-300">
                                Se você tiver alguma dúvida sobre estes Termos, entre em contato conosco:
                            </p>
                            <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                                <li><strong>Email legal:</strong> legal@handgeev.com</li>
                                <li><strong>Suporte técnico:</strong> support@handgeev.com</li>
                                <li><strong>Tempo de resposta:</strong> Até 48 horas úteis</li>
                            </ul>

                            <div className="mt-12 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p className="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Importante:</strong> Estes Termos de Uso constituem um acordo legal entre você e o HandGeev. Ao usar nosso serviço, você reconhece que leu, entendeu e concorda em ficar vinculado por estes termos. Recomendamos que você imprima uma cópia destes Termos para seus registros.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </LegalLayout>
    );
}