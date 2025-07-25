# Troubleshooting Guide

## Common Installation Issues

### Error: "Failed opening required" or "Missing required file"

If you encounter an error like this:
```
Uncaught Error: Failed opening required '/path/to/plugin/includes/class-fashion-variation-swatches-core.php'
```

**Solution:**
1. **Run the diagnostic script**: Navigate to your plugin directory and run `plugin-diagnostics.php` in your browser to check file integrity
2. **Reinstall the plugin**: Delete the plugin completely and reinstall it
3. **Check file permissions**: Ensure the plugin directory has proper read permissions (755)
4. **Verify ZIP extraction**: Make sure the plugin ZIP file was extracted completely

### Plugin won't activate

If the plugin fails to activate with a "Plugin Activation Error":

**Solution:**
1. Check that all required files are present using the diagnostic script
2. Ensure WooCommerce is installed and activated
3. Check PHP version compatibility (requires PHP 7.4+)
4. Verify WordPress version compatibility (requires WordPress 5.0+)

### Swatches not appearing on product pages

If variation swatches are not showing up:

**Solution:**
1. **Check attribute configuration**: Go to WooCommerce > Products > Attributes and ensure you have size and color attributes set up
2. **Verify product setup**: Make sure your products have variations configured
3. **Check theme compatibility**: Some themes may override WooCommerce templates
4. **Clear cache**: Clear any caching plugins or server cache
5. **Check for conflicts**: Temporarily deactivate other plugins to identify conflicts

### Elementor widgets not available

If Elementor widgets are not showing up:

**Solution:**
1. **Verify Elementor installation**: Ensure Elementor is installed and activated
2. **Check Elementor version**: Plugin is tested with Elementor 3.25.0+
3. **Clear Elementor cache**: Go to Elementor > Tools > Regenerate CSS & Data
4. **Check for conflicts**: Other Elementor addons might conflict

## Diagnostic Tools

### Plugin Diagnostics Script

Run the diagnostic script to check plugin integrity:

1. Navigate to your plugin directory: `/wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/`
2. Open `plugin-diagnostics.php` in your browser
3. Review the diagnostic report for any issues

### Manual File Check

You can manually verify these files exist in your plugin directory:

```
fashion-variation-swatches-for-woocommerce-elementor/
├── wc-variation-swatches.php
├── includes/
│   ├── class-fashion-variation-swatches-core.php
│   ├── class-fashion-variation-swatches-admin.php
│   ├── class-fashion-variation-swatches-frontend.php
│   ├── class-fashion-variation-swatches-attributes.php
│   └── class-fashion-variation-swatches-shop-filters.php
├── assets/
└── languages/
```

## System Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **WordPress**: 5.0 or higher
- **WooCommerce**: 5.0 or higher
- **Elementor**: 3.0 or higher (for Elementor integration)

### Recommended Requirements
- **PHP**: 8.0 or higher
- **WordPress**: 6.0 or higher
- **WooCommerce**: 7.0 or higher
- **Elementor**: 3.25.0 or higher

## Performance Issues

### Slow loading swatches

**Solutions:**
1. **Optimize images**: Use appropriately sized images for swatches
2. **Enable caching**: Use a caching plugin
3. **Check server resources**: Ensure adequate server resources
4. **Optimize database**: Clean up unused product variations

### Memory issues

**Solutions:**
1. **Increase PHP memory limit**: Set `memory_limit = 256M` or higher
2. **Optimize product variations**: Reduce the number of variations per product
3. **Use lazy loading**: Consider implementing lazy loading for swatches

## Browser Compatibility

### Supported Browsers
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

### Mobile Compatibility
- iOS Safari 13+
- Chrome Mobile 80+
- Samsung Internet 10+

## Common Configuration Issues

### Swatches not saving

**Solution:**
1. Check user permissions (admin access required)
2. Clear browser cache
3. Check for JavaScript errors in browser console
4. Verify database write permissions

### Color swatches not displaying correctly

**Solution:**
1. Ensure color values are in valid hex format (#RRGGBB)
2. Check for CSS conflicts with theme
3. Verify color attribute is properly configured
4. Clear any CSS caching

### Size swatches showing wrong sizes

**Solution:**
1. Check attribute values in WooCommerce > Products > Attributes
2. Verify product variation configuration
3. Ensure size attribute is properly mapped
4. Check for duplicate attribute values

## Getting Help

### Before Contacting Support

1. **Run diagnostics**: Use the plugin-diagnostics.php script
2. **Check logs**: Review WordPress debug logs and server error logs
3. **Test in isolation**: Deactivate other plugins and switch to default theme
4. **Update everything**: Ensure WordPress, WooCommerce, and all plugins are updated

### Debug Mode

Enable WordPress debug mode to get more detailed error information:

```php
// Add to wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

### Error Logs

Check these locations for error logs:
- WordPress debug log: `/wp-content/debug.log`
- Server error logs: Check with your hosting provider
- Browser console: Press F12 and check Console tab

## Known Issues

### Theme Conflicts
Some themes may override WooCommerce templates and cause swatches not to display. In such cases:
1. Contact your theme developer
2. Use a child theme to override templates
3. Consider switching to a WooCommerce-compatible theme

### Plugin Conflicts
Known conflicts with:
- Some caching plugins (clear cache after configuration)
- Custom variation plugins (disable conflicting features)
- AJAX optimization plugins (may interfere with swatch loading)

### Server Issues
- **Shared hosting limitations**: Some shared hosts have file permission restrictions
- **PHP configuration**: Ensure `allow_url_fopen` is enabled
- **Memory limits**: Increase PHP memory limit if needed

## Version Compatibility

### WooCommerce HPOS
This plugin is compatible with WooCommerce High-Performance Order Storage (HPOS). If you're using HPOS:
1. Ensure WooCommerce 7.0+ is installed
2. HPOS compatibility is automatically declared
3. No additional configuration required

### Elementor Compatibility
- Tested with Elementor 3.25.0
- Widgets are automatically registered when Elementor is active
- No manual widget registration required

## Recovery Procedures

### Complete Plugin Reset

If the plugin is completely broken:

1. **Backup your site** (always do this first!)
2. **Deactivate the plugin** via WordPress admin
3. **Delete the plugin** completely
4. **Clear all caches** (WordPress, server, CDN)
5. **Reinstall the plugin** from a fresh download
6. **Reconfigure settings** (they will be reset)

### Database Cleanup

If you need to clean up plugin data:

```sql
-- Remove plugin options (WARNING: This will reset all settings)
DELETE FROM wp_options WHERE option_name LIKE 'fashion_variation_swatches_%';

-- Remove plugin transients
DELETE FROM wp_options WHERE option_name LIKE '_transient_fashion_variation_swatches_%';
DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_fashion_variation_swatches_%';
```

**⚠️ Warning**: Always backup your database before running any SQL commands.

## Contact Information

For additional support:
- **GitHub Issues**: [Plugin Repository](https://github.com/uditha-mahindarathna/fashion-variation-swatches)
- **Email**: melan.udi@gmail.com
- **Documentation**: Check the README.md and USER-GUIDE.md files

When contacting support, please include:
1. WordPress version
2. WooCommerce version
3. PHP version
4. Error messages (if any)
5. Diagnostic script output
6. Steps to reproduce the issue 