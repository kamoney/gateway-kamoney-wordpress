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
		<strong>Não foi possível analisar o método junto à Kamoney</strong>: <?php echo $test->error; ?>
	</p>
</div>
