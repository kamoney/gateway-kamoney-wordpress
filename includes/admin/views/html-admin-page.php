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

    $kamoney = new Kamoney($this->kamoney_id_merchant);
    $test = $kamoney->status_merchant();

    if ($test->success == false) {
        include dirname(__FILE__) . '/html-notice-key-missing.php';
    } else if ($test->data->maintenance == true) {
        include dirname(__FILE__) . '/html-notice-key-maintenance.php';
    }
}
?>

<?php echo wpautop($this->method_description); ?>

<?php //include dirname( __FILE__ ) . '/html-admin-help-message.php'; ?>

<table class="form-table">
	<?php $this->generate_settings_html();?>
</table>
