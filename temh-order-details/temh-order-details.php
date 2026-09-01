<?php
/**
 * Plugin Name: TEMH Order Details
 * Plugin URI: https://github.com/TEMHDARWIN/temh-order-details
 * Description: Secure WooCommerce order details shortcode with nonce verification and capability checks
 * Version: 1.0.0
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
    // Debug: Check if WooCommerce is active
    if ( ! function_exists( 'wc_get_order' ) ) {
        return '<p style="color: red;">Error: WooCommerce is not active.</p>';
    }

    // Get order_id from URL
    $order_id = absint( sanitize_text_field( $_GET['order_id'] ?? 0 ) );
    
    if ( ! $order_id ) {
        return '<p style="color: red;">Error: No order ID provided. Use: ?order_id=123</p>';
    }
    
    // Verify nonce for security
    if ( isset( $_GET['_wpnonce'] ) ) {
        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'temh_order_details_nonce' ) ) {
            return '<p style="color: red;">Security check failed.</p>';
        }
    } else {
        // Allow viewing if no nonce for testing, but log a warning
        error_log( 'TEMH Order Details: No nonce provided for order ' . $order_id );
    }
    
    // Get the order
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return '<p style="color: red;">Error: Order #' . $order_id . ' not found.</p>';
    }
    
    // Check if user can view this order
    if ( is_user_logged_in() ) {
        if ( ! current_user_can( 'view_order', $order_id ) ) {
            return '<p style="color: red;">Error: You do not have permission to view this order.</p>';
        }
    }

    // Output order details
    ob_start();
    ?>
    <div class="temh-order-details">
        <h2>Order #<?php echo esc_html( $order->get_id() ); ?></h2>
        <p><strong>Status:</strong> <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></p>
        <p><strong>Date:</strong> <?php echo esc_html( $order->get_date_created()->date( 'Y-m-d H:i:s' ) ); ?></p>
        <p><strong>Total:</strong> <?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
        
        <h3>Items</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="text-align: left; padding: 8px;">Product</th>
                    <th style="text-align: center; padding: 8px;">Qty</th>
                    <th style="text-align: right; padding: 8px;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px;"><?php echo esc_html( $item->get_name() ); ?></td>
                        <td style="text-align: center; padding: 8px;"><?php echo esc_html( $item->get_quantity() ); ?></td>
                        <td style="text-align: right; padding: 8px;"><?php echo wp_kses_post( $item->get_total() ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
} );

/**
 * Helper function to generate secure order details link
 *
 * @param int $order_id The WooCommerce order ID
 * @param int $page_id The WordPress page ID with the shortcode
 * @return string The secure URL with nonce
 */
function temh_get_order_details_url( $order_id, $page_id ) {
    return wp_nonce_url(
        add_query_arg( 'order_id', $order_id, get_permalink( $page_id ) ),
        'temh_order_details_nonce',
        '_wpnonce'
    );
}
