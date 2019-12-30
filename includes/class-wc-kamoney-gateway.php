<?php
/**
 * WooCommerce Kamoney Gateway class
 *
 * @package WooCommerce_Kamoney/Classes/Gateway
 * @version 1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WC_Kamoney_Gateway extends WC_Payment_Gateway
{
    private $chk = "https://www.kamoney.com.br/merchant/checkout/";

    public function __construct()
    {
        $this->id = "kamoney_payment";
        $this->icon = plugins_url('assets/images/kamoney.png', plugin_dir_path(__FILE__));
        $this->method_title = "Gateway Kamoney";
        $this->method_description = "Aceite criptomoedas na sua loja WooCommerce.<br /><b>Este plugin requer um cadastro na plataforma <a href='https://www.kamoney.com.br' target='_blank'>Kamoney</a>.";
        $this->title = "Gateway Kamoney";
        $this->has_fields = true;

        // Load form fields
        $this->init_form_fields();

        // Load settings
        $this->init_settings();

        // Turn these settings into variables we can use
        foreach ($this->settings as $setting_key => $value) {
            $this->$setting_key = $value;
        }

        // Kamoney API
        $this->api = new Kamoney($this->kamoney_public_key, $this->kamoney_secret_key, $this->sandbox);

        // Active logs.
        if ('yes' === $this->debug) {
            if (function_exists('wc_get_logger')) {
                $this->log = wc_get_logger();
            } else {
                $this->log = new WC_Logger();
            }
        }

        // Main actions.
        add_action('woocommerce_api_wc_gateway_kamoney_payment', array($this, 'ipn_handler'));
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Habilitar / desabilitar',
                'label' => 'Ativar gateway de pagamento Kamoney',
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'title' => array(
                'title' => 'Título',
                'type' => 'text',
                'desc_tip' => 'Título da forma de pagamento.',
                'default' => 'Pagar com criptomoedas - by Kamoney',
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'desc_tip' => 'Descrição da forma de pagamento.',
                'default' => 'Você será redirecionado para https://www.kamoney.com.br para efetuar o pagamento',
                'css' => 'max-width:400px;',
            ),
            'kamoney_public_key' => array(
                'title' => 'Chave pública Kamoney',
                'type' => 'text',
                'desc_tip' => 'Esta é a chave pública fornecida pelo kamoney.com.br quando você se inscreveu em uma conta.',
                'default' => '',
            ),
            'kamoney_secret_key' => array(
                'title' => 'Chave secreta Kamoney',
                'type' => 'password',
                'desc_tip' => 'Essa é a chave secreta fornecida pelo kamoney.com.br quando você se inscreveu em uma conta.',
            ),
            'debug' => array(
                'title' => 'Modo de depuração',
                'label' => 'Ativar registro de log',
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'sandbox' => array(
                'title' => 'Utilizar Sandbox (api teste)',
                'label' => 'Ativar sandbox (Cadastre-se em https://sandbox.kamoney.com.br/registro e solicite ao suporte para haiblitar sua conta)',
                'type' => 'checkbox',
                'desc_tip' => 'Você poderá utilizar uma versão de testes Kamoney chamada de sandbox.',
                'default' => 'no',

            ),
        );
    }

    /**
     * Admin page.
     */
    public function admin_options()
    {
        include dirname(__FILE__) . '/admin/views/html-admin-page.php';
    }

    /**
     * Returns a bool that indicates if currency is amongst the supported ones.
     *
     * @return bool
     */
    public function using_supported_currency()
    {
        return 'BRL' === get_woocommerce_currency();
    }

    /**
     * Returns a value indicating the the Gateway is available or not. It's called
     * automatically by WooCommerce before allowing customers to use the gateway
     * for payment.
     *
     * @return bool
     */
    public function is_available()
    {
        // Test if is valid for use.
        $test = $this->api->statusServiceOrder();

        $available = 'yes' === $this->get_option('enabled') && !array_key_exists("error", $test) && false !== $test && $this->using_supported_currency();
        return $available;
    }

    // Response handled for payment gateway
    public function process_payment($order_id)
    {
        global $woocommerce;
        $order = new WC_Order($order_id);
        $result = 'fail';
        $url = '';

        $data = array(
            "amount" => $this->get_order_total(),
            "order_id" => $order_id,
            "callback" => WC()->api_request_url('wc_gateway_kamoney_payment'),
            'redirect' => $order->get_view_order_url(),//
        );

        $sale = $this->api->salesCreateChk($data);

        if ('yes' === $this->debug) {
            $this->log->add($this->id, $sale["error"]);
        }

        if (($sale !== false) && (!array_key_exists("error", $sale))) {

            if ($this->sandbox == "yes") {
                $this->chk = "https://sandbox.kamoney.com.br/merchant/checkout/";
            }

            // Mark as on-hold (we're awaiting the transaction)
            $order->update_status('on-hold');

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Update order meta
            foreach ($sale as $key => $value) {
                update_post_meta($order->id, "kamoney_" . $key, $value);
            }

            // Add note informing Kamoney Payment ID
            $order->add_order_note('ID Kamoney: ' . $sale["id"]);

            $result = "success";
            $url = $this->chk . $sale["id"];

            update_post_meta($order->id, "kamoney_redirect", $url);

        } else {

            // Kamoney Sale Creating Error
            $error_message = $sale["erorr"];

        }

        if ($result == "fail") {
            // Update order status
            $order->update_status('failed');
            $order->add_order_note('Ocorre um erro ao conectar à Kamoney: ' . $error_message);

            // Report error in the checkout page
            wc_add_notice('Erro ao conectar à kamoney.com.br', 'error');
            return;
        }

        return array(
            'result' => $result,
            'redirect' => $url,
        );

    }

    // Validate fields
    public function validate_fields()
    {
        return true;
    }

    /**
     * IPN handler.
     */
    public function ipn_handler()
    {
        @ob_clean();

        header('HTTP/1.1 200 OK');

        $msg_error = "*error*";
        $msg_ok = "*ok*";
        
        $headers = getallheaders();

        if (!array_key_exists('Host', $headers) || !array_key_exists('signature', $headers) || !isset($_POST)) {
            exit($msg_error);
        }

        $signature = $headers['signature'];
        $post_data = http_build_query($_POST, '', '&');
        $signature_valid = hash_hmac('sha512', $post_data, $this->kamoney_secret_key);

        if ($signature != $signature_valid) {
            exit($msg_error);
        }

        if (!array_key_exists("order_id", $_POST) && !array_key_exists("id", $_POST)) {
            exit($msg_error);
        }

        $id = sanitize_text_field($_POST["id"]);
        $status_code = sanitize_text_field($_POST["status_code"]);
        $currency = sanitize_text_field($_POST["currency"]);
        $currency_name = sanitize_text_field($_POST["currency_name"]);
        $address = sanitize_text_field($_POST["address"]);
        $txid = sanitize_text_field($_POST["txid"]);
        $amount = floatval($_POST["amount"]);
        $amount_order = floatval($_POST["amount_order"]);
        $amount_order_partial = floatval($_POST["amount_order_partial"]);
        $order_id = intval($_POST["order_id"]);
        
        $order = wc_get_order($order_id);

        if ($order == false) {
            exit($msg_error);
        }

        if ($order->get_status() == "processing") {
            exit($msg_ok);
        }
        
        if (get_post_meta($order->id, "kamoney_id", true) !== $id) {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, "Invalid Kamoney POST");
            }

            exit($msg_error);
        }
        
        if ('yes' === $this->debug) {
            $this->log->add($this->id, "Order $order->id exists and match");
        }

        if ('yes' === $this->debug) {
            $this->log->add($this->id, "Order $order->id has status " . $status_code);
        }

        $confirmed_status = array(
            "WAITING_CONFIRMS",
            "UNCONFIRMED_APPROVED",
            "CONFIRMED",
        );

        if (in_array($status_code, $confirmed_status) && $order->get_status() == "on-hold") {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, "Order $order->id has a confirmed status");
            }

            wc_reduce_stock_levels($order_id);
            $order->update_status('processing');
        }
        
        switch ($status_code) {
            case "WAITING_CONFIRMS":
                $order->add_order_note('Transação identificada. Aguardando confirmação na blockchain.');
                break;
            case "UNCONFIRMED_APPROVED":
                $order->add_order_note('Montante enviado aprovado. Aguardando confirmação na blockchain.');
                break;
            case "UNCONFIRMED_PARTIAL":
                $order->add_order_note('Montante enviado está abaixo do devido. Aguardando confirmação na blockchain.');
                break;
            case "UNCONFIRMED_REOPENED":
                $order->add_order_note('Novo montante foi enviado. Aguardando confirmação na blockchain.');
                break;
            case "CONFIRMED_PARTIAL":
                $order->add_order_note('Pagamento confirmado na blockchain. O montante enviado está abaixo do devido.');
                break;
            case "CANCELED":
                $order->add_order_note('Ordem cancelada.');
                $order->update_status('cancelled');
                break;
            case "CONFIRMED":
                $order->add_order_note('Pagamento confirmado na blockchain.');
                break;
        }

        exit($msg_ok);
    }

}
