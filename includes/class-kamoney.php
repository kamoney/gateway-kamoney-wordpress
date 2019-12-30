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
    private $public_key = '';
    private $secret_key = '';
    private $api = "https://api.kamoney.com.br";

    public function __construct($public_key, $secret_key, $sandbox)
    {
        $this->public_key = $public_key;
        $this->secret_key = $secret_key;

        if ($sandbox = 'yes') {
            $this->api = "https://sandbox-api.kamoney.com.br";
        }
    }

    private function query($endpoint, $data = array(), $type = 'GET')
    {
        // create sign
        $mt = explode(' ', microtime());
        $req['nonce'] = $mt[1] . substr($mt[0], 2, 6);

        foreach ($data as $key => $value) {
            $req[$key] = $value;
        }

        $data_query = http_build_query($req, '', '&');
        $sign = hash_hmac('sha512', $data_query, $this->secret_key);

        $url = $this->api . $endpoint;

        $args = array(
            'body' => $req,
            'timeout' => '10',
            'redirection' => '0',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                "public" => $this->public_key,
                "sign" => $sign,
            ),
            'cookies' => array(),
        );

        if ($type == 'POST') {
            $response = wp_remote_post($url, $args);
        } else {
            $response = wp_remote_get($url, $args);
        }

        return json_decode($response['body'], true);
    }

    public function statusServiceOrder()
    {
        return $this->query("/servicestatus/order");
    }

    public function salesCreateChk($data)
    {
        return $this->query("/sales/checkout", $data, 'POST');
    }
}
