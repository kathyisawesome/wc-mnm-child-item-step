<?php
/**
 * Plugin Name: WooCommerce Mix and Match - Child Item Step
 * Plugin URI: https://github.com/kathyisawesome/wc-mnm-child-item-step
 * Version: 1.0.0-beta-1
 * Description: Require container size to be in quantity mnultiples, ie: 12,16,20,etc. 
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Developer: Kathy Darling
 * Developer URI: http://kathyisawesome.com/
 * Text Domain: wc-mnm-child-item-step
 * Domain Path: /languages
 * 
 * GitHub Plugin URI: https://github.com/kathyisawesome/wc-mnm-child-item-step
 * Release Asset: true
 *
 * Copyright: © 2020 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


/**
 * The Main WC_MNM_Child_Item_Step class
 **/
if ( ! class_exists( 'WC_MNM_Child_Item_Step' ) ) :

class WC_MNM_Child_Item_Step {

	/**
	 * constants
	 */
	CONST VERSION = '1.0.0-beta-1';

	/**
	 * WC_MNM_Child_Item_Step Constructor
	 *
     * @return 	WC_MNM_Child_Item_Step
	 */
	public static function init() {

		// Quietly quit if MNM is not active.
		if ( ! function_exists( 'wc_mix_and_match' ) ) {
			return false;
		}

		// Load translation files.
		add_action( 'init', [ __CLASS__, 'load_plugin_textdomain' ] );

		// Add extra meta.
		add_action( 'wc_mnm_admin_product_options', [ __CLASS__, 'child_size_options' ], 15, 2 );
		add_action( 'woocommerce_admin_process_product_object', [ __CLASS__, 'process_meta' ], 20 );

		// Front-end validation.
		add_action( 'wc_mnm_child_item_quantity_input_step', [ __CLASS__, 'child_quantity_step' ], 10, 3 );

    }


	/*-----------------------------------------------------------------------------------*/
	/* Localization */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Make the plugin translation ready
	 *
	 * @return void
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'wc-mnm-child-item-step' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Admin */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Adds the child item step option to product data metabox.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function child_size_options( $post_id, $mnm_product_object ) {

		woocommerce_wp_text_input( array(
			'id'            => '_mnm_child_item_step',
			'label'         => esc_html__( 'Child Item Step', 'wc-mnm-child-item-step' ),
			'wrapper_class' => 'hide_if_variable-mix-and-match',
			'desc_tip'      => true,
			'description'   => esc_html__( 'Force customers to purchase child items in multiples.', 'wc-mnm-child-item-step' ),
			'type'          => 'number',
			'data_type'     => 'decimal',
			'value'         => $mnm_product_object->get_meta( '_mnm_child_item_step', true, 'edit' ),
			'desc_tip'      => true,
		) );

	}

	/**
	 * Saves the new meta field.
	 *
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function process_meta( $product ) {

		if ( $product->is_type( 'mix-and-match' ) ) {

			if( ! empty( $_POST[ '_mnm_child_item_step' ] ) ) {
				$product->update_meta_data( '_mnm_child_item_step', intval( wc_clean( wp_unslash( $_POST[ '_mnm_child_item_step' ] ) ) ) );
			} else {
				$product->delete_meta_data( '_mnm_child_item_step' );
			}

		}

	}


	/*-----------------------------------------------------------------------------------*/
	/* Frontend Functions */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Set "Step" for child items.
	 *
	 * @param  int $step multiples.
	 * @param  obj WC_MNM_Child_Item $child_item
	 * @param  obj WC_Product_Mix_and_Match $container
	 * @param obj WC_Product_Mix_and_Match
	 * @return int
	 */
	public static function child_quantity_step( $step, $child_item, $container ) {
		$new_step = intval( $container->get_meta( '_mnm_child_item_step', true ) );
		if ( $new_step > 0 ) {
			$step = $new_step;
		}
		return $step;
	}

} //end class: do not remove or there will be no more guacamole for you

endif; // end class_exists check

// Launch the whole plugin.
add_action( 'plugins_loaded', array( 'WC_MNM_Child_Item_Step', 'init' ), 20 );
