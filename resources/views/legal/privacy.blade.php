@extends('template.template-legal')

@section('title', 'Política de Privacidade')
@section('description', 'Política de Privacidade do HandGeev - Saiba como coletamos, usamos e protegemos seus dados')

@section('content_legal')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-white leading-7">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Política de Privacidade
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Última atualização: {{ date('d/m/Y', strtotime('2025-10-01')) }}
        </p>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 lg:p-12 legal-content">
        <div class="max-w-none">
            <p class="lead mb-8">
                Sua privacidade é importante para nós. Esta Política de Privacidade explica como o HandGeev coleta, usa, armazena, compartilha e protege suas informações quando você usa nossa plataforma.
            </p>

            <h2>1. Informações que Coletamos</h2>
            <h3>1.1. Informações Pessoais</h3>
            <p>
                Coletamos as seguintes informações pessoais quando você se cadastra e usa nosso serviço:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Nome completo (obrigatório)</li>
                <li>Endereço de e-mail (obrigatório)</li>
                <li>Foto de perfil (opcional)</li>
                <li>Informações de perfil profissional (opcional)</li>
                <li>Dados de pagamento (apenas para planos premium)</li>
            </ul>

            <h3>1.2. Dados de Uso e Técnicos</h3>
            <p>
                Coletamos automaticamente informações sobre como você interage com nosso serviço:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Endereço de IP e informações do navegador</li>
                <li>Dispositivo utilizado e sistema operacional</li>
                <li>Páginas visitadas e tempo de acesso</li>
                <li>Funcionalidades utilizadas e padrões de uso</li>
                <li>Logs de erro e dados de desempenho</li>
                <li>Cookies e tecnologias similares</li>
            </ul>

            <h3>1.3. Conteúdo do Workspace</h3>
            <p>
                Armazenamos todo o conteúdo que você cria em nossos workspaces:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Nomes, descrições e configurações de workspaces</li>
                <li>Tópicos, campos e suas estruturas</li>
                <li>Dados de chave-valor inseridos nos campos</li>
                <li>Configurações de visibilidade e permissões</li>
                <li>Histórico de modificações (opcional)</li>
            </ul>

            <h2>2. Como Usamos Suas Informações</h2>
            <p>
                Utilizamos as informações coletadas para os seguintes propósitos:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Prestação do Serviço:</strong> Fornecer, operar e manter nossa plataforma</li>
                <li><strong>Melhoria Contínua:</strong> Aprimorar, personalizar e expandir funcionalidades</li>
                <li><strong>Análise e Pesquisa:</strong> Entender como os usuários utilizam nosso serviço</li>
                <li><strong>Comunicação:</strong> Enviar notificações técnicas, atualizações e suporte</li>
                <li><strong>Marketing:</strong> Enviar comunicações promocionais (com opção de cancelamento)</li>
                <li><strong>Segurança:</strong> Prevenir fraudes, abusos e atividades maliciosas</li>
                <li><strong>Conformidade Legal:</strong> Cumprir obrigações legais e regulatórias</li>
            </ul>

            <h2>3. Bases Legais para o Tratamento</h2>
            <p>
                Processamos seus dados pessoais com base nas seguintes fundamentações legais:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Execução de Contrato:</strong> Para fornecer os serviços contratados</li>
                <li><strong>Consentimento:</strong> Quando você autoriza explicitamente</li>
                <li><strong>Interesse Legítimo:</strong> Para melhorar nossos serviços e segurança</li>
                <li><strong>Obrigação Legal:</strong> Para cumprir exigências legais</li>
            </ul>

            <h2>4. Compartilhamento de Informações</h2>
            <p>
                Não vendemos, comercializamos ou transferimos suas informações pessoais para terceiros, exceto nas seguintes situações:
            </p>

            <h3>4.1. Prestadores de Serviço</h3>
            <p>
                Utilizamos serviços de terceiros confiáveis que nos auxiliam na operação:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Hospedagem:</strong> Serviços de cloud computing</li>
                <li><strong>Análise:</strong> Ferramentas de analytics e monitoramento</li>
                <li><strong>Pagamento:</strong> Processadores de pagamento seguros</li>
                <li><strong>Comunicação:</strong> Serviços de e-mail e notificação</li>
                <li><strong>Suporte:</strong> Sistemas de atendimento ao cliente</li>
            </ul>

            <h3>4.2. Requisições Legais</h3>
            <p>
                Podemos divulgar suas informações quando exigido por lei, processo legal ou autoridades governamentais.
            </p>

            <h3>4.3. Proteção de Direitos</h3>
            <p>
                Podemos compartilhar informações para proteger nossos direitos, propriedade ou segurança, ou dos nossos usuários.
            </p>

            <h3>4.4. Reorganização Empresarial</h3>
            <p>
                Em caso de fusão, aquisição ou venda de ativos, suas informações podem ser transferidas.
            </p>

            <h2>5. Segurança de Dados</h2>
            <p>
                Implementamos medidas de segurança robustas para proteger suas informações:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Criptografia:</strong> Dados em trânsito (SSL/TLS) e em repouso</li>
                <li><strong>Controle de Acesso:</strong> Autenticação e autorização baseadas em função</li>
                <li><strong>Monitoramento:</strong> Sistemas de detecção e prevenção de intrusões</li>
                <li><strong>Backups:</strong> Cópias de segurança regulares e seguras</li>
                <li><strong>Treinamento:</strong> Equipe capacitada em proteção de dados</li>
                <li><strong>Auditoria:</strong> Revisões periódicas de segurança</li>
            </ul>

            <h2>6. Retenção de Dados</h2>
            <p>
                Mantemos suas informações pessoais apenas pelo tempo necessário para:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Cumprir os propósitos descritos nesta política</li>
                <li>Atender obrigações legais e regulatórias</li>
                <li>Resolver disputas e fazer cumprir nossos acordos</li>
                <li>Manter a segurança e integridade do serviço</li>
            </ul>
            <p>
                Quando você exclui sua conta, removemos permanentemente seus dados pessoais dentro de prazos técnicos razoáveis, exceto quando a retenção for necessária por lei.
            </p>

            <h2>7. Seus Direitos</h2>
            <p>
                De acordo com a legislação de proteção de dados, você tem os seguintes direitos:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Acesso:</strong> Solicitar cópia de seus dados pessoais</li>
                <li><strong>Retificação:</strong> Corrigir dados imprecisos ou incompletos</li>
                <li><strong>Exclusão:</strong> Solicitar a eliminação de seus dados pessoais</li>
                <li><strong>Portabilidade:</strong> Receber seus dados em formato estruturado</li>
                <li><strong>Revogação:</strong> Retirar consentimentos fornecidos</li>
                <li><strong>Oposição:</strong> Opor-se ao processamento de seus dados</li>
                <li><strong>Limitação:</strong> Restringir o processamento em certas situações</li>
            </ul>

            <p>
                Para exercer esses direitos, entre em contato através do email: privacy@handgeev.com
            </p>

            <h2>8. Cookies e Tecnologias Similares</h2>
            <h3>8.1. O que são Cookies</h3>
            <p>
                Cookies são pequenos arquivos de texto armazenados no seu dispositivo quando você visita nosso site. Eles nos ajudam a fornecer uma experiência melhor e mais personalizada.
            </p>

            <h3>8.2. Tipos de Cookies que Utilizamos</h3>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Essenciais:</strong> Necessários para o funcionamento básico do serviço</li>
                <li><strong>Funcionais:</strong> Lembram suas preferências e configurações</li>
                <li><strong>Analíticos:</strong> Nos ajudam a entender como você usa nosso serviço</li>
                <li><strong>Publicitários:</strong> Mostram anúncios relevantes (quando aplicável)</li>
            </ul>

            <h3>8.3. Controle de Cookies</h3>
            <p>
                Você pode controlar o uso de cookies através das configurações do seu navegador. No entanto, a desativação de cookies essenciais pode afetar o funcionamento do serviço.
            </p>

            <h2>9. Transferência Internacional de Dados</h2>
            <p>
                Seus dados podem ser processados em servidores localizados fora do seu país de residência. Garantimos que todas as transferências internacionais de dados cumprem com as leis de proteção de dados aplicáveis e utilizam mecanismos de proteção adequados, como cláusulas contratuais padrão.
            </p>

            <h2>10. Dados de Menores</h2>
            <p>
                Nosso serviço não é destinado a menores de 18 anos. Não coletamos intencionalmente informações pessoais de menores. Se tomarmos conhecimento de que coletamos dados de um menor sem verificação do consentimento parental, tomaremos medidas para remover essas informações imediatamente.
            </p>

            <h2>11. Links para Sites de Terceiros</h2>
            <p>
                Nosso serviço pode conter links para outros sites que não são operados por nós. Esta Política de Privacidade se aplica apenas ao HandGeev. Recomendamos que você revise as políticas de privacidade de qualquer site de terceiros que visitar.
            </p>

            <h2>12. Alterações nesta Política</h2>
            <p>
                Podemos atualizar nossa Política de Privacidade periodicamente para refletir mudanças em nossas práticas ou por outros motivos operacionais, legais ou regulatórios. Notificaremos você sobre quaisquer alterações materiais publicando a nova política nesta página e, se as alterações forem significativas, forneceremos um aviso mais prominente.
            </p>

            <h2>13. Contato</h2>
            <p>
                Se você tiver alguma dúvida sobre esta Política de Privacidade ou sobre o tratamento de seus dados pessoais, entre em contato conosco:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Email de Privacidade:</strong> privacy@handgeev.com</li>
                <li><strong>Encarregado de Proteção de Dados:</strong> dpo@handgeev.com</li>
                <li><strong>Suporte Técnico:</strong> support@handgeev.com</li>
                <li><strong>Tempo de Resposta:</strong> Até 72 horas úteis</li>
            </ul>

            <p>
                Você também tem o direito de apresentar uma reclamação à autoridade de proteção de dados competente se considerar que o tratamento de seus dados pessoais viola a legislação aplicável.
            </p>

            <div class="mt-12 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>Observação Importante:</strong> Esta Política de Privacidade se aplica apenas ao HandGeev. Não somos responsáveis pelas práticas de privacidade de sites ou serviços de terceiros que possam ser vinculados de nossa plataforma. Ao usar nosso serviço, você concorda com os termos desta Política de Privacidade.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection