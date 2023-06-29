=== Gateway Kamoney ===
Contributors: csgoularte
Tags: gateway, bitcoin, ethereum, cart, checkout, criptomoedas, cripo, e-commerce, kamoney, payment gateway
Requires at least: 3.0
Tested up to: 5.3.2
Stable tag: trunk
Requires PHP: 7.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Aceite pagamentos em criptomoedas em sua loja virtual geranciada pelo WooCommerce via Kamoney.

== Description ==

O plugin Gateway Kamoney é a maneira mais fácil, rápida e segura de aceitar pagamentos com criptomoedas (Bitcoin, Ethereum, etc.), sem nenhum conhecimento específico com blockchain ou derivados.

#### Características principais

* Aceite bitcoin e altcoins e receba em Reais (R$)
* Cotação da moeda em Real Brasileiro (R$)
* Receba sem custo por transferência bancária (Real Brasileiro)
* Por segurança, estornos não são possíveis com pagamentos em criptomoeda
* Tenha uma visão geral de todas as suas vendas em sua conta de comerciante Kamoney em (https://www.kamoney.com.br)

#### Funcionamento básico

1. Cliente cria o pedido normalmente em sua loja
2. Será gerada uma venda no site [Kamoney] (https://www.kamoney.com.br)
3. Seu cliente será redirecionado ao site [Kamoney] (https://www.kamoney.com.br) para selecionar a criptomoeda de sua preferência
4. Sue cliente realiza o pagamento
5. Nosso sistema irá monitorar o pagamento notificando cada status à sua loja
6. Quando confirmado o pagamento, a venda será concluída em sua loja

== Installation ==

#### Exigências

* Este plugin requer WooCommerce instalado;
* E necessário ter uma conta junto à [Kamoney](https://www.kamoney.com.br).

#### Instalação do plugin

1. Faça o download do plugin e copie os arquivos para a pasta `/wp-content/plugins/gateway-kamoney` ou instale-o diretamente na tela de plugins do WordPress;
2. Ative o plugin no painel de administração do WordPress.

Após ativação do plugin, o Gateway Kamoney aparecerá na seção Woocommerce>Pagamentos.

#### Configurações do plugin

Depois de instalar o plugin, as etapas de configuração são:

1. Crie uma conta em [Kamoney](https://www.kamoney.com.br);
2. Copie seu ID de Comerciante acessando o menu "Comerciante > Sobre o Gateway" em sua [Conta Kamoney](https://dash.kamoney.com.br) e procure por "Plug-in Wordpress";
3. No seu painel de administração do WordPress, selecione Woocommerce>Pagamentos e clique no botão Configurar ao lado dos métodos de pagamento Gateway Kamoney;
4. Insira seu ID de comerciante obtidio no passo 2;
5. Salve as alterações na parte inferior da página.

== Frequently Asked Questions ==

= Preciso ser CNPJ para utilizar o Gateway Kamoney? =

Não. Qualquer pessoa, física ou jurídica pode utilizar este plugin e aceitar critpoemodas em seu comércio.

= Possui limite de valores? =

Sim. Ao se cadastrar na Kamoney, você possui um limite inicial de movimentação. Para aumento do limite, é necessário realizar a verificação de conta, junto à plataforma da [Kamoney](https://dash.kamoney.com.br). Caso necessite de mais limite, entre em contato com o [Suporte Kamoney](https://web.whatsapp.com/send/?phone=553184724987&text=Estou com dúvidas no Plugin Wordpress&type=phone_number&app_absent=0).

= Qual a taxa de transação? =

A taxa de transação é cobrada do cliente final, ou seja, o comerciante recebe o valor integração em sua conta [Kamoney](https://dash.kamoney.com.br).

= É possível acompanhar meu histórico de vendas na plataforma? =

Sim. Acessando sua conta [Kamoney](https://dash.kamoney.com.br) você terá um histórico completo de suas vendas, dados pessoais, entre outros. Atráves ainda da sua conta, você pode solicitar o saque para sua conta bancária.

== Screenshots ==

1. /assets/screenshot-1.png
2. /assets/screenshot-2.png
3. /assets/screenshot-3.png
4. /assets/screenshot-4.png
5. /assets/screenshot-5.png
6. /assets/screenshot-6.png
7. /assets/screenshot-7.png
8. /assets/screenshot-8.png
9. /assets/screenshot-9.png

== Changelog ==

= 2023-06-28 =
* Atualização para utilização da API v2 Kamoney publicada em 2023-06-23.

== Upgrade Notice ==

= 2023-06-28 =
* Atualização para utilização da API v2 Kamoney publicada em 2023-06-23.
