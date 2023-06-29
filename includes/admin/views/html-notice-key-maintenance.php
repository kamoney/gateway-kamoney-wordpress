<?php
/**
 * Admin View: Notice - Secret Key missing
 *
 * @package WooCommerce_Kamoney/Admin/Notices
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="error inline">
	<p>
		<strong>Método de pagamento Kamoney desabilitado</strong><br />
		O serviço está temporariamente desabilitado.<br />
		- <?php echo $test->error; ?>
	</p>
</div>
