<?php
/**
 * Initialize the plugin and run sanity checks.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @link       https://roundhouse-designs.com
 * @since      0.1.0
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @package    Get_To_Work
 * @subpackage Get_To_Work/includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class Get_To_Work_Init {

	/**
	 * TGM Plugin Activation.
	 *
	 * @return void
	 */
	public function register_required_plugins() {
		/*
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = [
			[
				'name'               => 'WP GraphqQL',
				'slug'               => 'wp-graphql',
				'required'           => true,
				'version'            => '1.13.7',
				'force_activation'   => true,
				'force_deactivation' => false,
			],
			[
				'name'         => 'WP GraphQL CORS',
				'slug'         => 'wp-graphql-cors',
				'source'       => dirname( __DIR__ ) . '/lib/plugins/wp-graphql-cors.zip',
				'required'     => true,
				'external_url' => 'https://github.com/funkhaus/wp-graphql-cors',
			],
			[
				'name'         => 'WP GraphQL for Advanced Custom Fields',
				'slug'         => 'wp-graphql-acf',
				'source'       => dirname( __DIR__ ) . '/lib/plugins/wp-graphql-acf.zip',
				'required'     => true,
				'external_url' => 'https://github.com/wp-graphql/wp-graphql-acf',
			],
			[
				'name'     => 'Advanced Custom Fields',
				'slug'     => 'advanced-custom-fields',
				'required' => true,
			],
		];

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = [
			'id'           => 'gtw', // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '', // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'plugins.php', // Parent menu slug.
			'capability'   => 'manage_options', // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true, // Show admin notices or not.
			'dismissable'  => true, // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '', // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false, // Automatically activate plugins after installation or not.
			'message'      => '', // Message to output right before the plugins table.

			// phpcs:disable Squiz.PHP.CommentedOutCode.Found

			// TODO Customize TGMPA notices.
			/*
			'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'gtw' ),
			'menu_title'                      => __( 'Install Plugins', 'gtw' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', 'gtw' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', 'gtw' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'gtw' ),
			'notice_can_install_required'     => _n_noop(
			/* translators: 1: plugin name(s). * /
			'This theme requires the following plugin: %1$s.',
			'This theme requires the following plugins: %1$s.',
			'gtw'
			),
			'notice_can_install_recommended'  => _n_noop(
			/* translators: 1: plugin name(s). * /
			'This theme recommends the following plugin: %1$s.',
			'This theme recommends the following plugins: %1$s.',
			'gtw'
			),
			'notice_ask_to_update'            => _n_noop(
			/* translators: 1: plugin name(s). * /
			'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
			'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
			'gtw'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
			/* translators: 1: plugin name(s). * /
			'There is an update available for: %1$s.',
			'There are updates available for the following plugins: %1$s.',
			'gtw'
			),
			'notice_can_activate_required'    => _n_noop(
			/* translators: 1: plugin name(s). * /
			'The following required plugin is currently inactive: %1$s.',
			'The following required plugins are currently inactive: %1$s.',
			'gtw'
			),
			'notice_can_activate_recommended' => _n_noop(
			/* translators: 1: plugin name(s). * /
			'The following recommended plugin is currently inactive: %1$s.',
			'The following recommended plugins are currently inactive: %1$s.',
			'gtw'
			),
			'install_link'                    => _n_noop(
			'Begin installing plugin',
			'Begin installing plugins',
			'gtw'
			),
			'update_link' 					  => _n_noop(
			'Begin updating plugin',
			'Begin updating plugins',
			'gtw'
			),
			'activate_link'                   => _n_noop(
			'Begin activating plugin',
			'Begin activating plugins',
			'gtw'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'gtw' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'gtw' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'gtw' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'gtw' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'gtw' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'gtw' ),
			'dismiss'                         => __( 'Dismiss this notice', 'gtw' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'gtw' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'gtw' ),
			'nag_type'                        => 'error', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),
			 */

			// phpcs:enable Squiz.PHP.CommentedOutCode.Founda
		];

		tgmpa( $plugins, $config );
	}

}
