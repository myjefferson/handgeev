@extends('template.template-legal')

@section('title', 'Termos de Uso')
@section('description', 'Termos de Uso do HandGeev - Conheça as regras e condições para uso da nossa plataforma')

@section('content_legal')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-white leading-7">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Termos de Uso
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Última atualização: {{ date('d/m/Y', strtotime('2025-10-01')) }}
        </p>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 lg:p-12 legal-content">
        <div class="max-w-none">
            <p class="lead mb-8">
                Bem-vindo ao HandGeev! Estes Termos de Uso regem o uso da nossa plataforma de criação e gerenciamento de workspaces para APIs.
            </p>

            <h2>1. Aceitação dos Termos</h2>
            <p>
                Ao acessar ou usar o HandGeev, você concorda em ficar vinculado a estes Termos de Uso. Se você não concordar com qualquer parte destes termos, não poderá acessar nosso serviço.
            </p>

            <h2>2. Definições</h2>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>HandGeev:</strong> Plataforma online para criação e gerenciamento de workspaces de API</li>
                <li><strong>Usuário:</strong> Pessoa física ou jurídica que utiliza nossos serviços</li>
                <li><strong>Workspace:</strong> Ambiente virtual para organização de tópicos e campos de API</li>
                <li><strong>Conteúdo do Usuário:</strong> Dados, informações e materiais inseridos pelo usuário</li>
                <li><strong>API:</strong> Interface de programação de aplicações fornecida pelo serviço</li>
            </ul>

            <h2>3. Cadastro e Conta</h2>
            <h3>3.1. Elegibilidade</h3>
            <p>
                Você deve ter pelo menos 18 anos ou a maioridade legal em sua jurisdição para usar nosso serviço. Ao se cadastrar, você declara e garante que possui a capacidade legal para celebrar este contrato.
            </p>

            <h3>3.2. Responsabilidade da Conta</h3>
            <p>
                Você é responsável por manter a confidencialidade de sua conta e senha, e por restringir o acesso ao seu computador. Você concorda em aceitar a responsabilidade por todas as atividades que ocorram sob sua conta ou senha.
            </p>

            <h3>3.3. Verificação de Conta</h3>
            <p>
                Reservamo-nos o direito de suspender ou encerrar contas que forneçam informações falsas, estejam envolvidas em atividades fraudulentas ou violem estes Termos.
            </p>

            <h2>4. Serviços e Limitações</h2>
            <h3>4.1. Funcionalidades</h3>
            <p>
                O HandGeev oferece as seguintes funcionalidades principais:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Criação e gerenciamento de workspaces</li>
                <li>Organização de dados em tópicos e campos</li>
                <li>Exportação e importação de dados em formato JSON</li>
                <li>Geração de endpoints API RESTful</li>
                <li>Colaboração em workspaces com outros usuários</li>
                <li>Controle de visibilidade de campos</li>
            </ul>

            <h3>4.2. Limitações de Uso</h3>
            <p>
                Você concorda em não utilizar o serviço para:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Atividades ilegais, fraudulentas ou não autorizadas</li>
                <li>Violar qualquer lei local, estadual, nacional ou internacional</li>
                <li>Enviar spam, vírus ou conteúdo malicioso</li>
                <li>Tentar obter acesso não autorizado a outros sistemas ou dados</li>
                <li>Interromper a integridade, desempenho ou segurança do serviço</li>
                <li>Criar conteúdo que seja ofensivo, difamatório ou prejudicial</li>
            </ul>

            <h3>4.3. Limites de Uso</h3>
            <p>
                Podemos estabelecer limites de uso para garantir a qualidade do serviço, incluindo limites de:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Número de workspaces por conta</li>
                <li>Número de campos por workspace</li>
                <li>Taxa de requisições API</li>
                <li>Armazenamento de dados</li>
            </ul>

            <h2>5. Conteúdo do Usuário</h2>
            <h3>5.1. Propriedade</h3>
            <p>
                Você mantém todos os direitos de propriedade intelectual sobre seu conteúdo. Ao enviar conteúdo para o HandGeev, você nos concede uma licença mundial não exclusiva para hospedar, armazenar, reproduzir e exibir esse conteúdo conforme necessário para fornecer o serviço.
            </p>

            <h3>5.2. Responsabilidade pelo Conteúdo</h3>
            <p>
                Você é exclusivamente responsável pelo conteúdo que criar, publicar ou exibir através do serviço. O HandGeev não se responsabiliza por qualquer conteúdo do usuário e não endossa opiniões expressas pelos usuários.
            </p>

            <h3>5.3. Conteúdo Proibido</h3>
            <p>
                Você não deve criar ou armazenar conteúdo que:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Infrinja direitos de propriedade intelectual de terceiros</li>
                <li>Seja ilegal, obsceno, difamatório ou ameaçador</li>
                <li>Contenha informações pessoais sensíveis sem consentimento</li>
                <li>Promova discriminação, ódio ou violência</li>
            </ul>

            <h2>6. Planos e Pagamentos</h2>
            <h3>6.1. Planos Gratuitos</h3>
            <p>
                Oferecemos um plano gratuito com funcionalidades limitadas. Reservamo-nos o direito de modificar, limitar ou descontinuar o plano gratuito a qualquer momento, mediante aviso prévio.
            </p>

            <h3>6.2. Planos Premium</h3>
            <p>
                Planos premium são cobrados conforme as tarifas publicadas em nosso site. Os pagamentos são processados de forma segura através de nossos provedores de pagamento autorizados. Planos pagos são não reembolsáveis, exceto conforme exigido por lei.
            </p>

            <h3>6.3. Renovações e Cancelamentos</h3>
            <p>
                Planos premium renovam-se automaticamente no final de cada período de faturamento. Você pode cancelar sua assinatura a qualquer momento através do painel de controle da sua conta.
            </p>

            <h2>7. Propriedade Intelectual</h2>
            <p>
                O HandGeev e seu conteúdo original, recursos, funcionalidades e design são e permanecerão propriedade exclusiva da HandGeev e de seus licenciadores. Nossas marcas registradas e marcas comerciais não podem ser usadas em conexão com qualquer produto ou serviço sem o consentimento prévio por escrito.
            </p>

            <h2>8. Limitação de Responsabilidade</h2>
            <p>
                Em nenhum caso o HandGeev, seus diretores, funcionários, parceiros ou agentes serão responsáveis por quaisquer danos indiretos, incidentais, especiais, consequenciais ou punitivos, incluindo perda de lucros, dados, uso, goodwill, ou outras perdas intangíveis, resultantes de:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li>Seu acesso ou uso ou incapacidade de acessar ou usar o serviço</li>
                <li>Qualquer conduta ou conteúdo de terceiros no serviço</li>
                <li>Qualquer conteúdo obtido do serviço</li>
                <li>Acesso não autorizado, uso ou alteração de suas transmissões ou conteúdo</li>
            </ul>

            <h2>9. Modificações do Serviço</h2>
            <p>
                Reservamo-nos o direito de modificar ou descontinuar, temporária ou permanentemente, o serviço (ou qualquer parte dele) com ou sem aviso prévio. Não seremos responsáveis perante você ou qualquer terceiro por qualquer modificação, alteração de preço, suspensão ou descontinuação do serviço.
            </p>

            <h2>10. Rescisão</h2>
            <p>
                Podemos rescindir ou suspender seu acesso imediatamente, sem aviso prévio ou responsabilidade, por qualquer motivo, incluindo, sem limitação, se você violar os Termos. Após a rescisão, seu direito de usar o serviço cessará imediatamente.
            </p>

            <h2>11. Lei Aplicável</h2>
            <p>
                Estes Termos serão regidos e interpretados de acordo com as leis da República Federativa do Brasil, sem considerar seus conflitos de disposições legais.
            </p>

            <h2>12. Disposições Gerais</h2>
            <h3>12.1. Acordo Integral</h3>
            <p>
                Estes Termos constituem o acordo integral entre você e o HandGeev em relação ao uso do serviço e substituem todos os acordos anteriores e contemporâneos.
            </p>

            <h3>12.2. Divisibilidade</h3>
            <p>
                Se qualquer disposição destes Termos for considerada inválida ou inexequível por um tribunal, as demais disposições permanecerão em pleno vigor e efeito.
            </p>

            <h2>13. Alterações nos Termos</h2>
            <p>
                Reservamo-nos o direito, a nosso exclusivo critério, de modificar ou substituir estes Termos a qualquer momento. Se uma revisão for material, tentaremos fornecer um aviso com pelo menos 30 dias de antecedência antes que os novos termos entrem em vigor. O que constitui uma alteração material será determinado a nosso exclusivo critério.
            </p>

            <h2>14. Contato</h2>
            <p>
                Se você tiver alguma dúvida sobre estes Termos, entre em contato conosco:
            </p>
            <ul class="list-disc list-inside space-y-2">
                <li><strong>Email legal:</strong> legal@handgeev.com</li>
                <li><strong>Suporte técnico:</strong> support@handgeev.com</li>
                <li><strong>Tempo de resposta:</strong> Até 48 horas úteis</li>
            </ul>

            <div class="mt-12 p-6 bg-teal-50 dark:bg-teal-900/20 rounded-lg border border-teal-200 dark:border-teal-800">
                <p class="text-sm text-teal-800 dark:text-teal-200">
                    <strong>Importante:</strong> Estes Termos de Uso constituem um acordo legal entre você e o HandGeev. Ao usar nosso serviço, você reconhece que leu, entendeu e concorda em ficar vinculado por estes termos. Recomendamos que você imprima uma cópia destes Termos para seus registros.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection