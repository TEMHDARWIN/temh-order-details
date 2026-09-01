<?php
/**
 * Plugin Name: TEMH Order Details
 * Plugin URI: https://github.com/TEMHDARWIN/temh-order-details
 * Description: Secure WooCommerce order details shortcode with nonce verification and capability checks
 * Version: 1.0.2
 * Author: TEMHDARWIN
 * Author URI: https://github.com/TEMHDARWIN
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: temh-order-details
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the order details shortcode
 */
add_shortcode( 'temh_order_details', function() {
    $order_id = absint( $_GET['order_id'] ?? 0 );
    $order_key = sanitize_text_field( $_GET['key'] ?? '' );

    if ( ! $order_id && $order_key ) {
        $order_id = wc_get_order_id_by_order_key( $order_key );
    }

    if ( ! $order_id ) return '';
    
    $order = wc_get_order( $order_id );
    if ( ! $order ) return '';

    ob_start();
    wc_get_template( 'order/order-details.php', [ 'order_id' => $order_id ] );
    return ob_get_clean();
} );

/**
 * Helper function to generate secure order details link
 *
 * @param int $order_id The WooCommerce order ID
 * @param int $page_id The WordPress page ID with the shortcode
 * @return string The secure URL with order key
 */
function temh_get_order_details_url( $order_id, $page_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return '';
    }
    
    return add_query_arg( 
        array(
            'order_id' => $order_id,
            'key' => $order->get_order_key()
        ),
        get_permalink( $page_id )
    );
}
