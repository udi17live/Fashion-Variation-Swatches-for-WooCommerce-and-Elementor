# 📦 Installation Guide - Fashion Variation Swatches

## 🎯 Quick Installation Methods

### Method 1: WordPress Admin Upload (Recommended)

1. **Download** the plugin package: `fashion-variation-swatches-v1.0.1.zip`
2. **Go to** your WordPress admin panel
3. **Navigate** to **Plugins > Add New**
4. **Click** **Upload Plugin** button
5. **Choose** the downloaded ZIP file
6. **Click** **Install Now**
7. **Click** **Activate Plugin**

### Method 2: Manual Upload via FTP

1. **Download** and **extract** the plugin package
2. **Upload** the `fashion-variation-swatches` folder to `/wp-content/plugins/`
3. **Go to** WordPress admin > **Plugins**
4. **Find** "Fashion Variation Swatches" and click **Activate**

### Method 3: From GitHub Repository

1. **Clone** the repository:
   ```bash
   git clone https://github.com/udi17live/fashion-variation-swatches-for-woocommerce-elementor.git
   ```
2. **Rename** the folder to `fashion-variation-swatches-for-woocommerce-elementor`
3. **Upload** to `/wp-content/plugins/`
4. **Activate** in WordPress admin

## ⚙️ System Requirements

### Minimum Requirements
- **WordPress**: 5.0 or higher
- **WooCommerce**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher

### Recommended Requirements
- **WordPress**: 6.0 or higher
- **WooCommerce**: 8.0 or higher
- **PHP**: 8.0 or higher
- **MySQL**: 5.7 or higher

### Optional Requirements
- **Elementor**: 3.0+ (for additional widgets)
- **HPOS**: Compatible with WooCommerce High-Performance Order Storage

## 🔧 Pre-Installation Checklist

Before installing, ensure you have:

- ✅ **WordPress** installed and configured
- ✅ **WooCommerce** plugin installed and activated
- ✅ **Backup** of your website (recommended)
- ✅ **PHP ZIP extension** enabled (for automatic packaging)
- ✅ **Write permissions** on `/wp-content/plugins/` directory

## 📋 Installation Steps

### Step 1: Download the Plugin

**Option A: Use the Package Script**
1. Access: `/wp-content/plugins/wc-variation-swatches/package-plugin.php`
2. Click the download link to get the ZIP file

**Option B: Download from GitHub**
1. Go to: https://github.com/udi17live/Fashion-Variation-Swatches-for-WooCommerce-and-Elementor
2. Click **Code > Download ZIP**
3. Extract the ZIP file

### Step 2: Install in WordPress

1. **Log in** to your WordPress admin panel
2. **Go to** **Plugins > Add New**
3. **Click** **Upload Plugin** (top of page)
4. **Choose File** and select the plugin ZIP
5. **Click** **Install Now**
6. **Wait** for installation to complete
7. **Click** **Activate Plugin**

### Step 3: Initial Configuration

1. **Go to** **WooCommerce > Variation Swatches**
2. **Review** the default settings:
   - Size Attribute: `pa_size`
   - Color Attribute: `pa_color`
   - Shop Filters: Enabled
   - Tooltips: Enabled
3. **Click** **Save Changes**

### Step 4: Set Up Product Attributes

1. **Go to** **Products > Attributes**
2. **Create** or verify these attributes exist:
   - **Size** (pa_size) with terms: XS, S, M, L, XL, XXL
   - **Color** (pa_color) with terms: Red, Blue, Green, Black, White
3. **Set colors** for color terms using the color picker

### Step 5: Test the Installation

1. **Run Demo Setup**: Access `/wp-content/plugins/wc-variation-swatches/demo-setup.php`
2. **Create** a test variable product
3. **Add** size and color variations
4. **View** the product page to see swatches
5. **Visit** shop page to see filter widgets

## 🚨 Troubleshooting

### Common Installation Issues

**"Plugin could not be activated"**
- Check PHP version (requires 7.4+)
- Verify WooCommerce is installed and activated
- Check file permissions on plugin directory

**"ZIP file could not be opened"**
- Ensure the ZIP file is not corrupted
- Try downloading the file again
- Check server upload limits

**"Plugin not showing in admin"**
- Verify the plugin folder is in `/wp-content/plugins/`
- Check file permissions (755 for folders, 644 for files)
- Clear any caching plugins

**"Swatches not displaying"**
- Ensure variations are created and published
- Check that attributes are set to "Used for variations"
- Verify the product is published

### Server Configuration Issues

**PHP Memory Limit**
```php
// Add to wp-config.php if needed
define('WP_MEMORY_LIMIT', '256M');
```

**Upload File Size**
```php
// Add to .htaccess if needed
php_value upload_max_filesize 64M
php_value post_max_size 64M
```

**File Permissions**
```bash
# Set correct permissions
chmod 755 wp-content/plugins/fashion-variation-swatches
chmod 644 wp-content/plugins/fashion-variation-swatches/*.php
```

## 🔄 Updating the Plugin

### Automatic Updates (if available)
- WordPress will notify you of updates
- Click **Update Now** in the plugins list

### Manual Updates
1. **Deactivate** the current plugin
2. **Delete** the old plugin folder
3. **Upload** the new version
4. **Activate** the plugin

### Backup Before Updates
- **Export** your plugin settings
- **Backup** your database
- **Test** on a staging site first

## 🧹 Uninstallation

### Complete Removal
1. **Deactivate** the plugin
2. **Delete** the plugin folder
3. **Remove** any custom CSS/JS you added
4. **Clean up** any database options (optional)

### Preserve Settings
If you want to keep settings for reinstallation:
```sql
-- Backup plugin options
SELECT * FROM wp_options WHERE option_name LIKE 'fashion_variation_swatches%';
```

## 📞 Support

### Getting Help
1. **Check** the [User Guide](USER-GUIDE.md)
2. **Review** [Troubleshooting](USER-GUIDE.md#troubleshooting)
3. **Test** [HPOS Compatibility](test-hpos-compatibility.php)
4. **Contact** support with specific issues

### Useful Links
- [Quick Start Guide](QUICK-START.md)
- [User Guide](USER-GUIDE.md)
- [HPOS Compatibility](README-HPOS.md)
- [GitHub Repository](https://github.com/udi17live/fashion-variation-swatches-for-woocommerce-elementor)

---

**Need immediate help?** Run the demo setup script to verify everything is working correctly! 