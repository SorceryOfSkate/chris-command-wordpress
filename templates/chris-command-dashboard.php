<?php
/**
 * Standalone public Chris Command page template.
 *
 * @package ChrisCommand
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'chris-command-dashboard-page' ); ?>>
<?php wp_body_open(); ?>
<?php
$dashboard = new \ChrisCommand\Modules\Dashboard\Dashboard_Renderer();
echo $dashboard->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Renderer escapes all dynamic values.
wp_footer();
?>
</body>
</html>
