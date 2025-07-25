# Fashion Variation Swatches - HPOS Compatibility

## Overview

This plugin has been updated to be fully compatible with WooCommerce's High-Performance Order Storage (HPOS) feature.

## What is HPOS?

High-Performance Order Storage (HPOS) is a WooCommerce feature that stores order data in custom database tables instead of WordPress posts. This provides better performance and scalability for stores with many orders.

## Compatibility Status

✅ **Fully Compatible with HPOS**

- Version 1.0.1 and later are HPOS compatible
- No order-related functionality in this plugin
- Only works with products and variations
- Safe to use with HPOS enabled

## Changes Made

### Version 1.0.1
- Added HPOS compatibility declaration
- Updated plugin description to mention HPOS compatibility
- Added proper hook for compatibility declaration

### Technical Details

The plugin declares HPOS compatibility using:

```php
add_action( 'before_woocommerce_init', [ $this, 'declare_hpos_compatibility' ] );

public function declare_hpos_compatibility() {
    if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
}
```

## Testing HPOS Compatibility

1. Access the test file: `/wp-content/plugins/wc-variation-swatches/test-hpos-compatibility.php`
2. Check if the plugin shows as compatible
3. Delete the test file after confirming compatibility

## Enabling HPOS

1. Go to WooCommerce > Settings > Advanced > Features
2. Find "Order data storage" section
3. Select "High-performance order storage (recommended)"
4. Save changes

## Requirements

- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- WordPress 5.0 or higher

## Support

If you encounter any issues with HPOS compatibility, please:

1. Check that you're using version 1.0.1 or later
2. Verify WooCommerce is up to date
3. Test with the compatibility test file
4. Contact support if issues persist

## Migration Notes

- No data migration required
- Plugin functionality remains unchanged
- All existing features continue to work
- No configuration changes needed 