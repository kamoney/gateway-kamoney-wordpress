<?php
/**
 * Admin options screen.
 *
 * @package WooCommerce_Kamoney/Admin/Settings
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<h3><?php echo esc_html($this->method_title); ?></h3>

<?php
if ('yes' == $this->get_option('enabled')) {

    if (!$this->using_supported_currency()) {
        include dirname(__FILE__) . '/html-notice-currency-not-supported.php';
    }

    $kamoney = new Kamoney($this->kamoney_public_key, $this->kamoney_secret_key, $this->sandbox);
    $test = $kamoney->statusServiceOrder();
    if (array_key_exists("error", $test) || false === $test) {
        include dirname(__FILE__) . '/html-notice-key-missing.php';
    }
}
?>

<?php echo wpautop($this->method_description); ?>

<?php //include dirname( __FILE__ ) . '/html-admin-help-message.php'; ?>

<table class="form-table">
	<?php $this->generate_settings_html();?>
</table>
