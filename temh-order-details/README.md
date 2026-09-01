# TEMH Order Details

A secure WordPress/WooCommerce plugin that displays order details via shortcode with built-in security features.

## Features

✅ **Nonce Verification** — CSRF protection on all order detail requests  
✅ **Capability Checks** — Users can only view orders they have permission to see  
✅ **Input Sanitization** — All user input is properly sanitized  
✅ **WooCommerce Integration** — Uses native WooCommerce templates and functions  

## Installation

1. Download this repository as a ZIP file
2. Go to **WordPress Admin > Plugins > Add New > Upload Plugin**
3. Select the ZIP file and click **Install Now**
4. Click **Activate Plugin**

Or install via Git:
```bash
cd wp-content/plugins/
git clone https://github.com/TEMHDARWIN/temh-order-details.git
```

## Usage

### Add Shortcode to Page

Add the shortcode to any WordPress page or post:
```
[temh_order_details]
```

### Generate Secure Links

Use the helper function to create secure order detail links:

```php
$order_id = 123;
$page_id = 456; // Page with [temh_order_details] shortcode

$url = temh_get_order_details_url( $order_id, $page_id );
echo '<a href="' . esc_url( $url ) . '">View Order</a>';
```

### In WooCommerce Emails/Templates

```php
$page_id = 456;
$order_id = $order->get_id();
$url = temh_get_order_details_url( $order_id, $page_id );
?>
<a href="<?php echo esc_url( $url ); ?>">View Full Order Details</a>
```

## Security Features

### Nonce Verification
All order detail requests require a valid WordPress nonce token to prevent CSRF attacks.

### Capability Checks
The shortcode verifies that the current user has permission to view the specific order before displaying details.

### Input Sanitization
- Order ID is converted to integer with `absint()`
- Additional text sanitization with `sanitize_text_field()`

## Requirements

- WordPress 5.0+
- WooCommerce 3.0+
- PHP 7.4+

## Troubleshooting

### Shortcode displays nothing
- Verify nonce is included in the URL
- Check user has `view_order` capability for that order
- Ensure order exists and order ID is valid

### Plugin installation fails
- Ensure the ZIP file contains a subdirectory with the main plugin file
- The main file must be named `temh-order-details.php`
- Check that PHP short tags are not disabled on your server

## License

GPL v2 or later — See LICENSE file for details

## Support

For issues and questions, visit: https://github.com/TEMHDARWIN/temh-order-details/issues
