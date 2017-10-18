<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<!-- BEGIN #sidebar -->
<aside id="sidebar" role="complementary">

	<?php if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'Primary' ) ) : endif; ?>

<!-- END #sidebar -->
</aside>