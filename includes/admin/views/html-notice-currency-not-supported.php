<?php
/**
 * Admin View: Notice - Currency not supported.
 *
 * @package WooCommerce_Kamoney/Admin/Notices
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="error inline">
	<p><strong>Método de pagamento Kamoney desabilitado</strong>: 
	<?php printf('Moeda <code>%s</code> não é suportada atualmente. Utilize Real Brasileiro (R$).', get_woocommerce_currency() ); ?>
	</p>
</div>
