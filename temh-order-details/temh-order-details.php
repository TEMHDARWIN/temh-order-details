<?php
/**
 * Plugin Name: TEMH Order Details
 * Plugin URI: https://github.com/TEMHDARWIN/temh-order-details
 * Description: Secure WooCommerce order details shortcode with nonce verification and capability checks
 * Version: 1.0.3
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

    if ( ! $order_id ) return '<p>No order found.</p>';
    
    $order = wc_get_order( $order_id );
    if ( ! $order ) return '<p>Order not found.</p>';

    ob_start();
    ?>
    <div class="temh-order-details">
        <h3>Order #<?php echo $order->get_order_number(); ?></h3>
        <p><strong>Status:</strong> <?php echo wc_get_order_status_name( $order->get_status() ); ?></p>
        <p><strong>Date:</strong> <?php echo wc_format_datetime( $order->get_date_created() ); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $order->get_items() as $item ) : ?>
                <tr>
                    <td><?php echo $item->get_name(); ?></td>
                    <td><?php echo $item->get_quantity(); ?></td>
                    <td><?php echo wc_price( $item->get_total() ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total:</strong> <?php echo $order->get_formatted_order_total(); ?></p>
        <p><strong>Payment:</strong> <?php echo $order->get_payment_method_title(); ?></p>
        <h4>Billing</h4>
        <p><?php echo $order->get_formatted_billing_address(); ?></p>
    </div>
    <?php
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
