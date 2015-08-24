<?php
/**
 * Plugin Name: WooFees by Marcus
 * Plugin URI: http://www.marcusbattle.com/plugins/woo-fees
 * Description: A WooCommerce extension for adding surcharge fees
 * Version: 0.1.0
 * Author: Marcus Battle
 * Author URI: http://marcusbattle.com
 * Requires at least: 4.0
 * domain: embee-woo-fees
 */

class Embee_Woo_Fees {
	
	static $single_instance; 

	public function __construct() { }
	
	static function init() {
		
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;

	}

	public function hooks() { 

		add_filter( 'woocommerce_get_settings_checkout', array( $this, 'settings_for_fees' ), 10, 1 );

		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'woocommerce_custom_surcharge' ) );

	}

	public function settings_for_fees( $settings ) {

		$settings[] = array( 
			'name' 		=> __( 'Fees', 'embee-woo-fees' ), 
			'type' 		=> 'title', 
			'desc' 		=> __( '', 'embee-woo-fees' ), 
			'id' 		=> 'checkout-fees' 
		);

		$settings[] = array(
			'name'     	=> __( 'Enable surcharge on checkout', 'embee-woo-fees' ),
			'id'       	=> 'checkout_fees_enabled',
			'type'     	=> 'checkbox',
			'css'      	=> 'min-width:300px;',
			'desc'     	=> __( 'Enable surcharge', 'embee-woo-fees' ),
		);

		$settings[] = array(
			'name'     	=> __( 'Surcharge Fee (%)', 'embee-woo-fees' ),
			'id'       	=> 'checkout_fees_amount',
			'type'     	=> 'text',
			'desc'		=> __( 'The percentage per transaction for the surcharge (i.e. 2.9)', 'embee-woo-fees' ),
		);

		$settings[] = array( 
			'type' => 'sectionend', 
			'id' => 'checkout-fees' 
		);

		return $settings;

	}

	public function woocommerce_custom_surcharge() {

		global $woocommerce;

		if ( is_admin() && ! defined( 'DOING_AJAX' ) )
			return;

		$checkout_enabled = get_option( 'checkout_fees_enabled' );
		$checkout_percentage = get_option( 'checkout_fees_amount' );

		if ( $checkout_enabled && $checkout_percentage ) {

			$percentage = $checkout_percentage / 100;
			$surcharge = ( $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total ) * $percentage;	
			
			// Display the fee on the checkout cart page
			$woocommerce->cart->add_fee( 'Surcharge', $surcharge, true, '' );

		}

	}

}

add_action( 'plugins_loaded', array( Embee_Woo_Fees::init(), 'hooks' ) );
