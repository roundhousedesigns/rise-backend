<?php

namespace RHD\Rise\Includes;

/**
 * WooCommerce integration functionality.
 *
 * @package    RHD\Rise
 * @subpackage RHD\Rise\Includes
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      0.1.0
 */
class WooCommerce {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var string $plugin_name The ID of this plugin.
	 * @since    0.1.0
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var string $version The current version of this plugin.
	 * @since    0.1.0
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Add job post data to order.
	 *
	 * @param  \WC_Order $order
	 * @param  array     $data
	 * @return void
	 */
	public function add_job_post_data_to_order( $order ) {
		$job_data = \WC()->session->get( 'new_job_post_awaiting_payment' );

		if ( $job_data ) {
			$order->add_meta_data( 'new_job_post_awaiting_payment', $job_data );
		}

		// Clean up the session data.
		\WC()->session->__unset( 'new_job_post_awaiting_payment' );
	}

	/**
	 * Create a job post from an order after payment is complete.
	 *
	 * @param  int    $order_id
	 * @return void
	 */
	public function create_job_post_from_order_after_payment_complete( $order_id ) {
		$order = \wc_get_order( $order_id );
		if ( !$order ) {
			return;
		}

		$job_post = $order->get_meta( 'new_job_post_awaiting_payment' );

		if ( !$job_post ) {
			return;
		}

		$job_post_id = $job_post->update_job_post();

		if ( is_wp_error( $job_post_id ) ) {
			error_log( $job_post_id->get_error_message() );
		}

		// Add the job post ID to the order.
		$order->update_meta_data( 'job_post_id', $job_post_id );

		// Clean up the order.
		$order->delete_meta_data( 'new_job_post_awaiting_payment' );
		$order->delete_meta_data( 'job_post_id' );

		// Save the order data.
		$order->save();
	}

	public function add_go_to_manage_jobs_button_to_thank_you_page( $order_id ) {
		$order = \wc_get_order( $order_id );
		if ( !$order || $order->get_status() !== 'completed' ) {
			return;
		}

		$job_post_id = $order->get_meta( 'job_post_id' );

		if ( !$job_post_id ) {
			return;
		}

		$job_post = get_post( $job_post_id );

		if ( !$job_post ) {
			return;
		}

		// Add a button to the thank you page that says "Go to Manage Jobs".
		echo '<a href="' . home_url( '/directory/#/jobs/manage/' ) . '">Go to Manage Jobs</a>';
	}

	/**
	 * Alias for create_job_post_from_order_after_payment_complete to match hook name.
	 */
	public function on_woocommerce_order_complete( $order_id ) {
		$this->create_job_post_from_order_after_payment_complete( $order_id );
	}
}