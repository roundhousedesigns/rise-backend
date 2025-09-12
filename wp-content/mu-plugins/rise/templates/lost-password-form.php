<?php
/**
 * Template for the lost password form.
 *
 * @var bool $show_title Whether to show the title.
 * @package RISE
 * @subpackage Templates
 *
 * @since 1.2
 */
?>

<div id="password-lost-form" class="widecolumn password-lost-form">
	<?php if ( $attributes['show_title'] ) : ?>
		<h3><?php _e( 'Forgot Your Password?', 'personalize-login' ); ?></h3>
	<?php endif; ?>
	<p class="wp-block-paragraph">
		<?php
		_e(
			"Enter your email address and we'll send you a link you can use to pick a new password.",
			'personalize_login'
		);
		?>
	</p>
	<form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
		<p class="form-row">
			<label for="user_login"><?php _e( 'Email', 'personalize-login' ); ?>
			<input type="text" name="user_login" id="user_login">
		</p>
		<p class="lostpassword-submit">
			<input type="submit" name="submit" class="lostpassword-button"
			value="<?php _e( 'Reset Password', 'personalize-login' ); ?>"/>
		</p>
	</form>
</div>