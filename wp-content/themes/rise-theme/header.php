<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<?php
	$pod        = pods( 'page', get_the_ID() );
	$page_color = 'bg-color-' . ( $pod->field( 'background_color' ) ? $pod->field( 'background_color' ) : 'bg-color-dark' );
?>

<body <?php body_class( $page_color ); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<header id="masthead" class="site-header">
			<div class="site-branding">
				<?php
					if ( has_custom_logo() ):
						the_custom_logo();
					else:
				?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<?php
						endif;
					?>
			</div>

			<div class="spacer"></div>

			<!-- <div class="directory-link wp-block-buttons">
				<div class="wp-block-button">
					<a href="<?php echo esc_url( home_url( '/directory' ) ); ?>" class="wp-block-button__link">Directory</a>
				</div>
			</div> -->

			<button 
				class="menu-toggle" 
				aria-controls="primary-menu" 
				aria-expanded="false"
				aria-label="Toggle navigation menu">
				<span class="hamburger-icon"></span>
			</button>

			<div class="menu-container">
				 <?php wp_nav_menu( [
				 		'theme_location' => 'primary',
				 		'menu_id'        => 'primary-menu',
				 		'menu_class'     => 'primary-menu',
				 		'container'      => false,
				 		'fallback_cb'    => false,
				 ] ); ?>
			</div>
		</header>
