<?php
/**
 * Kamoney class
 *
 * @package WooCommerce_Kamoney/Classes/Kamoney
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Kamoney
{
    private $id_merchant = '';
    private $api = "https://api2.kamoney.com.br/v2";

    public function __construct($id_merchant)
    {
        $this->id_merchant = $id_merchant;
    }

    private function query($endpoint, $data = [], $type = 'GET')
    {
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);
        $req['merchant_id'] = $this->id_merchant;

        foreach ($data as $key => $value) {
            $req[$key] = $value;
        }

        $url = $this->api . $endpoint;

        $args = [
            'body' => $req,
            'timeout' => '10',
            'redirection' => '0',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => ["X-Device" => 'plug-in'],
            'cookies' => [],
        ];

        if ($type == 'POST') {
            $response = wp_remote_post($url, $args);
        } else {
            $response = wp_remote_get($url, $args);
        }

        return json_decode($response['body']);
    }

    public function status_merchant()
    {
        return $this->query("/public/services/merchant");
    }

    public function merchant_create($data)
    {
        return $this->query("/public/merchant/checkout", $data, 'POST');
    }
}
