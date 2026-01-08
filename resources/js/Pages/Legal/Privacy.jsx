import React from 'react';
import { Head } from '@inertiajs/react';
import LegalLayout from '@/Layouts/LegalLayout';

export default function PrivacyPolicy() {
    const currentDate = new Date('2025-10-01').toLocaleDateString('pt-BR');

    return (
        <LegalLayout>
            <Head>
                <title>Política de Privacidade</title>
                <meta 
                    name="description" 
                    content="Política de Privacidade do HandGeev - Saiba como coletamos, usamos e protegemos seus dados" 
                />
            </Head>

            <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-white leading-7">
                {/* Header */}
                <div className="text-center mb-12">
                    <h1 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Política de Privacidade
                    </h1>
                    <p className="text-lg text-gray-600 dark:text-gray-400">
                        Última atualização: {currentDate}
                    </p>
                </div>

                {/* Content */}
                <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 lg:p-12 legal-content">
                    <div className="max-w-none">
                        <p className="lead mb-8 text-gray-700 dark:text-gray-300">
                            Sua privacidade é importante para nós. Esta Política de Privacidade explica como o HandGeev coleta, usa, armazena, compartilha e protege suas informações quando você usa nossa plataforma.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">1. Informações que Coletamos</h2>
                        
                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">1.1. Informações Pessoais</h3>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Coletamos as seguintes informações pessoais quando você se cadastra e usa nosso serviço:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li>Nome completo (obrigatório)</li>
                            <li>Endereço de e-mail (obrigatório)</li>
                            <li>Foto de perfil (opcional)</li>
                            <li>Informações de perfil profissional (opcional)</li>
                            <li>Dados de pagamento (apenas para planos premium)</li>
                        </ul>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">1.2. Dados de Uso e Técnicos</h3>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Coletamos automaticamente informações sobre como você interage com nosso serviço:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li>Endereço de IP e informações do navegador</li>
                            <li>Dispositivo utilizado e sistema operacional</li>
                            <li>Páginas visitadas e tempo de acesso</li>
                            <li>Funcionalidades utilizadas e padrões de uso</li>
                            <li>Logs de erro e dados de desempenho</li>
                            <li>Cookies e tecnologias similares</li>
                        </ul>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">1.3. Conteúdo do Workspace</h3>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Armazenamos todo o conteúdo que você cria em nossos workspaces:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li>Nomes, descrições e configurações de workspaces</li>
                            <li>Tópicos, campos e suas estruturas</li>
                            <li>Dados de chave-valor inseridos nos campos</li>
                            <li>Configurações de visibilidade e permissões</li>
                            <li>Histórico de modificações (opcional)</li>
                        </ul>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">2. Como Usamos Suas Informações</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Utilizamos as informações coletadas para os seguintes propósitos:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li><strong>Prestação do Serviço:</strong> Fornecer, operar e manter nossa plataforma</li>
                            <li><strong>Melhoria Contínua:</strong> Aprimorar, personalizar e expandir funcionalidades</li>
                            <li><strong>Análise e Pesquisa:</strong> Entender como os usuários utilizam nosso serviço</li>
                            <li><strong>Comunicação:</strong> Enviar notificações técnicas, atualizações e suporte</li>
                            <li><strong>Marketing:</strong> Enviar comunicações promocionais (com opção de cancelamento)</li>
                            <li><strong>Segurança:</strong> Prevenir fraudes, abusos e atividades maliciosas</li>
                            <li><strong>Conformidade Legal:</strong> Cumprir obrigações legais e regulatórias</li>
                        </ul>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">3. Bases Legais para o Tratamento</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Processamos seus dados pessoais com base nas seguintes fundamentações legais:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li><strong>Execução de Contrato:</strong> Para fornecer os serviços contratados</li>
                            <li><strong>Consentimento:</strong> Quando você autoriza explicitamente</li>
                            <li><strong>Interesse Legítimo:</strong> Para melhorar nossos serviços e segurança</li>
                            <li><strong>Obrigação Legal:</strong> Para cumprir exigências legais</li>
                        </ul>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">4. Compartilhamento de Informações</h2>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Não vendemos, comercializamos ou transferimos suas informações pessoais para terceiros, exceto nas seguintes situações:
                        </p>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.1. Prestadores de Serviço</h3>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Utilizamos serviços de terceiros confiáveis que nos auxiliam na operação:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                            <li><strong>Hospedagem:</strong> Serviços de cloud computing</li>
                            <li><strong>Análise:</strong> Ferramentas de analytics e monitoramento</li>
                            <li><strong>Pagamento:</strong> Processadores de pagamento seguros</li>
                            <li><strong>Comunicação:</strong> Serviços de e-mail e notificação</li>
                            <li><strong>Suporte:</strong> Sistemas de atendimento ao cliente</li>
                        </ul>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.2. Requisições Legais</h3>
                        <p className="mb-4 text-gray-700 dark:text-gray-300">
                            Podemos divulgar suas informações quando exigido por lei, processo legal ou autoridades governamentais.
                        </p>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.3. Proteção de Direitos</h3>
                        <p className="mb-4 text-gray-700 dark:text-gray-300">
                            Podemos compartilhar informações para proteger nossos direitos, propriedade ou segurança, ou dos nossos usuários.
                        </p>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">4.4. Reorganização Empresarial</h3>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Em caso de fusão, aquisição ou venda de ativos, suas informações podem ser transferidas.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">5. Segurança de Dados</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Implementamos medidas de segurança robustas para proteger suas informações:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-6 text-gray-700 dark:text-gray-300">
                            <li><strong>Criptografia:</strong> Dados em trânsito (SSL/TLS) e em repouso</li>
                            <li><strong>Controle de Acesso:</strong> Autenticação e autorização baseadas em função</li>
                            <li><strong>Monitoramento:</strong> Sistemas de detecção e prevenção de intrusões</li>
                            <li><strong>Backups:</strong> Cópias de segurança regulares e seguras</li>
                            <li><strong>Treinamento:</strong> Equipe capacitada em proteção de dados</li>
                            <li><strong>Auditoria:</strong> Revisões periódicas de segurança</li>
                        </ul>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">6. Retenção de Dados</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Mantemos suas informações pessoais apenas pelo tempo necessário para:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                            <li>Cumprir os propósitos descritos nesta política</li>
                            <li>Atender obrigações legais e regulatórias</li>
                            <li>Resolver disputas e fazer cumprir nossos acordos</li>
                            <li>Manter a segurança e integridade do serviço</li>
                        </ul>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Quando você exclui sua conta, removemos permanentemente seus dados pessoais dentro de prazos técnicos razoáveis, exceto quando a retenção for necessária por lei.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">7. Seus Direitos</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            De acordo com a legislação de proteção de dados, você tem os seguintes direitos:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                            <li><strong>Acesso:</strong> Solicitar cópia de seus dados pessoais</li>
                            <li><strong>Retificação:</strong> Corrigir dados imprecisos ou incompletos</li>
                            <li><strong>Exclusão:</strong> Solicitar a eliminação de seus dados pessoais</li>
                            <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                            <li><strong>Revogação:</strong> Retirar consentimentos fornecidos</li>
                            <li><strong>Oposição:</strong> Opor-se ao processamento de seus dados</li>
                            <li><strong>Limitação:</strong> Restringir o processamento em certas situações</li>
                        </ul>

                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Para exercer esses direitos, entre em contato através do email: privacy@handgeev.com
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">8. Cookies e Tecnologias Similares</h2>
                        
                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">8.1. O que são Cookies</h3>
                        <p className="mb-4 text-gray-700 dark:text-gray-300">
                            Cookies são pequenos arquivos de texto armazenados no seu dispositivo quando você visita nosso site. Eles nos ajudam a fornecer uma experiência melhor e mais personalizada.
                        </p>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">8.2. Tipos de Cookies que Utilizamos</h3>
                        <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                            <li><strong>Essenciais:</strong> Necessários para o funcionamento básico do serviço</li>
                            <li><strong>Funcionais:</strong> Lembram suas preferências e configurações</li>
                            <li><strong>Analíticos:</strong> Nos ajudam a entender como você usa nosso serviço</li>
                            <li><strong>Publicitários:</strong> Mostram anúncios relevantes (quando aplicável)</li>
                        </ul>

                        <h3 className="text-xl font-semibold mt-6 mb-3 text-gray-900 dark:text-white">8.3. Controle de Cookies</h3>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Você pode controlar o uso de cookies através das configurações do seu navegador. No entanto, a desativação de cookies essenciais pode afetar o funcionamento do serviço.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">9. Transferência Internacional de Dados</h2>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Seus dados podem ser processados em servidores localizados fora do seu país de residência. Garantimos que todas as transferências internacionais de dados cumprem com as leis de proteção de dados aplicáveis e utilizam mecanismos de proteção adequados, como cláusulas contratuais padrão.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">10. Dados de Menores</h2>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Nosso serviço não é destinado a menores de 18 anos. Não coletamos intencionalmente informações pessoais de menores. Se tomarmos conhecimento de que coletamos dados de um menor sem verificação do consentimento parental, tomaremos medidas para remover essas informações imediatamente.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">11. Links para Sites de Terceiros</h2>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Nosso serviço pode conter links para outros sites que não são operados por nós. Esta Política de Privacidade se aplica apenas ao HandGeev. Recomendamos que você revise as políticas de privacidade de qualquer site de terceiros que visitar.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">12. Alterações nesta Política</h2>
                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Podemos atualizar nossa Política de Privacidade periodicamente para refletir mudanças em nossas práticas ou por outros motivos operacionais, legais ou regulatórios. Notificaremos você sobre quaisquer alterações materiais publicando a nova política nesta página e, se as alterações forem significativas, forneceremos um aviso mais prominente.
                        </p>

                        <h2 className="text-2xl font-bold mt-8 mb-4 text-gray-900 dark:text-white">13. Contato</h2>
                        <p className="mb-3 text-gray-700 dark:text-gray-300">
                            Se você tiver alguma dúvida sobre esta Política de Privacidade ou sobre o tratamento de seus dados pessoais, entre em contato conosco:
                        </p>
                        <ul className="list-disc list-inside space-y-2 mb-4 text-gray-700 dark:text-gray-300">
                            <li><strong>Email de Privacidade:</strong> privacy@handgeev.com</li>
                            <li><strong>Encarregado de Proteção de Dados:</strong> dpo@handgeev.com</li>
                            <li><strong>Suporte Técnico:</strong> support@handgeev.com</li>
                            <li><strong>Tempo de Resposta:</strong> Até 72 horas úteis</li>
                        </ul>

                        <p className="mb-6 text-gray-700 dark:text-gray-300">
                            Você também tem o direito de apresentar uma reclamação à autoridade de proteção de dados competente se considerar que o tratamento de seus dados pessoais viola a legislação aplicável.
                        </p>

                        <div className="mt-12 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p className="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Observação Importante:</strong> Esta Política de Privacidade se aplica apenas ao HandGeev. Não somos responsáveis pelas práticas de privacidade de sites ou serviços de terceiros que possam ser vinculados de nossa plataforma. Ao usar nosso serviço, você concorda com os termos desta Política de Privacidade.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </LegalLayout>
    );
}