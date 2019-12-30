=== Gateway Kamoney ===
Contributors: claudecigoularte
Tags: cart, checkout, e-commerce, kamoney, criptomoedas, gateway, bitcoin, ethereum, cripo
Donate link: https://www.kamoney.com.br
Requires at least: 3.0
Tested up to: 5.3.2
Stable tag: trunk
Requires PHP: 7.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Aceite pagamentos em criptomoedas em sua loja virtual geranciada pelo WooCommerce via Kamoney.

== Description ==

O plugin Gateway Kamoney é a maneira mais fácil, rápida e segura de aceitar pagamentos com criptomoedas (Bitcoin, Ethereum, etc.), sem nenhum conhecimento específico com blockchain ou derivados.

<h4>Características principais</h4>

* Aceite bitcoin e altcoins e receba em Reais (R$)
* Cotação da moeda em Real Brasileiro (R$)
* Receba sem custo por transferência bancária (Real Brasileiro)
* Por segurança, estornos não são possíveis com pagamentos em criptomoeda
* Tenha uma visão geral de todas as suas vendas em sua conta de comerciante Kamoney em (https://bitpay.com/dashboard)

<h4>Funcionamento básico</h4>

1. Cliente cria o pedido normalmente em sua loja
2. Será gerada uma venda no site [Kamoney] (https://www.kamoney.com.br)
3. Seu cliente será redirecionado ao site [Kamoney] (https://www.kamoney.com.br) para selecionar a criptomoeda de sua preferência
4. Sue cliente realiza o pagamento
5. Nosso sistema irá monitorar o pagamento notificando cada status à sua loja
6. Quando confirmado o pagamento, a venda será concluída em sua loja

== Installation ==

<h4>Exigências<h4>

* Este plugin requer Woocommerce instalado.
* Uma conta lojista Kamoney ([Sandbox](https://sandbox.kamoney.com.br/) ou [Produção](https://www.kamoney.com.br/))

Nota: Após realizar o cadastro, solicite o credenciamento como lojista para receber pagamentos em criptomoedas.

<h4>Instalação do plugin</4>

1. Copie os arquivos do plugin para a pasta `/wp-content/plugins/gateway-kamoney`, ou instale-o diretamente na tela de plugins do WordPress;
2. Ative o plugin através da tela 'Plugins' no WordPress

Após ativação do plugin, o Gateway Kamoney aparecerá na seção Woocommerce>Pagamentos.

<h4>Configurações do plugin</4>

Depois de instalar o plugin Gateway Kamoney, as etapas de configuração são:

1. Crie uma conta em [Kamoney](https://www.kamoney.com.br);
2. Solicite o credenciamento como Lojista;
3. Após a aprovação do credenciamento, gere suas credenciais (chaves secreta e pública) no menu APIs;
4. Entre no seu painel de administração do WordPress, selecione Woocommerce>Pagamentos e clique no botão Configurar ao lado dos métodos de pagamento Gateway Kamoney;
5. Cole suas chaves geradas anteriormente, nos respectivos campos "Chave pública Kamoney" e "Chave secreta Kamoney";
6. Clique em "Salvar alterações" na parte inferior da página;

Caso deseje realizar simulações em testnet (rede de testes Bitcoin ou Litecoin), gere as chaves no ambiente Sandbox (teste) e marque a opção Utilizar Sandbox (api teste) nas configurações do plugin.

== Frequently Asked Questions ==

= Já solicitei o credenciamento, e agora? =

Se você já solicitou o credenciamento, basta aguardar nossa análise. Ela será feita em poucas horas.

= Possui algum limite de recebimento? =

Não. Porém, para realizar o saque dos respectivos valores recebidos, você possui um limite inicial de R$ 35 mil / mês. Para aumentar esse valor, é necessário solicitar a verificação de conta no menu Minha Conta > Verificação.

= Quais as taxas envolvidas? =

Atualmente, é cobrada uma taxa de 3% sobre a cotação da moeda ao pagador. Ao lojista não é cobrada nenhuma taxa. Se vender R$ 100, você recebe exatamente R$ 100 em sua conta após solicitar o saque em nossa plataforma.

== Screenshots ==

1. /assets/screenshot-1.png
2. /assets/screenshot-2.png
3. /assets/screenshot-3.png
4. /assets/screenshot-4.png
5. /assets/screenshot-5.png
6. /assets/screenshot-6.png

== Changelog ==

= 0.1 =
* Lançamento.

== Upgrade Notice ==

= 0.1 =
Versão de Lançamento