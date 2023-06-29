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
        $this->icon = plugins_url('assets/icon.png', plugin_dir_path(__FILE__));
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
        $this->api = new Kamoney($this->kamoney_id_merchant);

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
                'default' => 'Pagar com criptomoedas - ',
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'desc_tip' => 'Descrição da forma de pagamento.',
                'default' => 'Você será redirecionado para o site www.kamoney.com.br para efetuar o pagamento',
                'css' => 'max-width:400px;',
            ),
            'kamoney_id_merchant' => array(
                'title' => 'ID de Comerciante Kamoney',
                'type' => 'text',
                'desc_tip' => 'Acesse o menu "Comerciante/Sobre o Gateway e procure por Plug-in Wordpress. Lá vocÊ conseguirá visualizar seu ID.',
                'default' => '',
            ),
            'debug' => array(
                'title' => 'Modo de depuração',
                'label' => 'Ativar registro de log',
                'type' => 'checkbox',
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
        $test = $this->api->status_merchant();

        $available = 'yes' === $this->get_option('enabled') && true === $test->success && false === $test->data->maintenance && $this->using_supported_currency();

        return $available;
    }

    // Response handled for payment gateway
    public function process_payment($order_id)
    {
        global $woocommerce;
        
        $order = new WC_Order($order_id);
        $result = 'fail';
        $url = '';

        $token = hash_hmac('sha512', $order_id, $this->kamoney_id_merchant);
        $callback = WC()->api_request_url('wc_gateway_kamoney_payment') . "?token=$token";

        $data = [
            "amount" => $this->get_order_total(),
            "order_id" => $order_id,
            "additional_info" => "Venda realizada pelo Plug-In Wordpress",
            "callback" => $callback,
            'redirect' => $order->get_view_order_url(),
        ];

        $sale = $this->api->merchant_create($data);
        
        // {
        //     "success": true,
        //     "msg": "",
        //     "data": {
        //     "id": "MKM66461077"
        //     "redirect": "https://www.kamoney.com.br/merchant/checkout/MKM66461077"
        //     }
        // }

        if ($sale->success) {
            // Mark as on-hold (we're awaiting the transaction)
            $order->update_status('on-hold');

            // Remove cart
            $woocommerce->cart->empty_cart();

            // Update order meta
            $sale_data = (array) $sale->data;

            foreach ($sale_data as $key => $value) {
                update_post_meta($order->id, "kamoney_" . $key, $value);
            }

            // Add note informing Kamoney Payment ID
            $order->add_order_note('Identificação da venda na Kamoney: ' . $sale->data->id);

            $result = "success";
            $url = $sale->data->redirect;

            update_post_meta($order->id, "kamoney_redirect", $url);
        } else {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, $sale->error);
            }

            $order->update_status('failed');
            $order->add_order_note('Ocorre um erro ao conectar à Kamoney: ' . $sale->error);

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

        $data_json = file_get_contents('php://input');

        if(is_null($data_json)) {
            exit($msg_error);
        }
        
        if (!isset($_GET['token'])) {
            exit($msg_error);
        }

        $data = json_decode($data_json);        
        $token = sanitize_text_field($_GET['token']);
        $token_compare = hash_hmac('sha512', $data->external->order_id, $this->kamoney_id_merchant);      
        
        if ($token !== $token_compare) {
            exit($msg_error);
        }

        if (!isset($data->id)) {
            exit($msg_error);
        }
        
        $id = sanitize_text_field($data->id);
        $status_code = sanitize_text_field($data->status->code);
        $currency = sanitize_text_field($data->asset->asset);
        $currency_name = sanitize_text_field($data->asset->name);
        $address = sanitize_text_field($data->address);
        $txid = sanitize_text_field($data->txid->id);
        $amount = floatval($data->txid->amount);
        $amount_order = floatval($data->asset->total);
        $amount_order_partial = floatval($data->asset->received);
        $order_id = intval($data->external->order_id);

        $order = wc_get_order($order_id);
        $order_id_wp  = $order->get_id(); // Get the order ID
        
        if ($order == false) {
            exit($msg_error);
        }
        
        update_post_meta($order_id_wp, "kamoney_asset", "$currency_name ($currency)");
        update_post_meta($order_id_wp, "kamoney_address", $address);
        
        if ($status_code == 'PROCESSING' && $order->get_status() == "processing") {
            exit($msg_ok);
        }
        
        if (get_post_meta($order_id_wp, "kamoney_id", true) !== $id) {
            if ('yes' === $this->debug) {
                $this->log->add($this->id, "Invalid Kamoney POST");
            }
            
            exit($msg_error);
        }
        
        if ('yes' === $this->debug) {
            $this->log->add($this->id, "Order $order_id_wp exists and match");
            $this->log->add($this->id, "Order $order_id_wp has status " . $status_code);
        }
        
        $order_note_msg = '';

        switch ($status_code) {
            case "PROCESSING":
                if($order->get_status() == "on-hold") {
                    wc_reduce_stock_levels($order_id);
                }

                $order->update_status('processing');
                $order_note_msg = 'O pagamento foi identificado, estamos aguardando a confirmação na blockchain.';
                break;
            case "PARTIAL":
                if($order->get_status() == "on-hold") {
                    wc_reduce_stock_levels($order_id);
                }

                $order->update_status('processing');
                $order_note_msg = 'Cliente pagou um valor inferior. Estamos aguardando o restante das critpomoedas.';
                break;
            case "COMPLETED":
                if($order->get_status() == "on-hold") {
                    wc_reduce_stock_levels($order_id);
                }

                $order->update_status('completed');
                $order_note_msg = 'Pagamento concluído. O valor já está disponível em sua conta Kamoney.';
                break;
            case "CANCELED":
                $order->update_status('cancelled');
                $order_note_msg = 'O pedido foi cancelado pelo cliente na pltaforma Kamoney.';
                break;
        }
        
        if(!empty($order_note_msg)) {
            $order->add_order_note($order_note_msg);
        }

        exit($msg_ok);
    }

}
