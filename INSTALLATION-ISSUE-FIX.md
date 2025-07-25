# Installation Issue Fix - Fashion Variation Swatches

## 🚨 Problem Identified

The plugin was experiencing activation errors due to **versioned directory extraction issues**. When ZIP files were extracted, WordPress was creating directories with version numbers (e.g., `fashion-variation-swatches-for-woocommerce-elementor-v1.0.5`) instead of the proper plugin directory.

### Error Message
```
Plugin Activation Error
Fashion Variation Swatches could not be activated because the following required files are missing:

includes/class-fashion-variation-swatches-core.php
includes/class-fashion-variation-swatches-admin.php
includes/class-fashion-variation-swatches-frontend.php
includes/class-fashion-variation-swatches-attributes.php
includes/class-fashion-variation-swatches-shop-filters.php
Plugin Directory: /home/azpire/tfc.aphrodite-azd.online/wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor-v1.0.5/
```

## 🔍 Root Cause Analysis

1. **ZIP File Structure**: ZIP files contained subdirectories with version numbers
2. **Extraction Behavior**: WordPress extracted to versioned directories instead of the main plugin directory
3. **File Path Issues**: Required files existed in the main directory but not in the versioned directory
4. **Multiple Installations**: Multiple ZIP files in the plugins directory caused conflicts

## ✅ Solutions Implemented

### 1. Enhanced Directory Detection
- **File**: `wc-variation-swatches.php`
- **Function**: `fashion_variation_swatches_get_plugin_dir()`
- **Improvement**: Better detection of versioned directories and fallback to correct paths

### 2. Improved Activation Error Handling
- **File**: `wc-variation-swatches.php`
- **Function**: `activate()`
- **Improvement**: Detects versioned directories and provides clear guidance to users

### 3. Cleanup Script
- **File**: `cleanup-installation.php`
- **Purpose**: Identifies and helps fix installation issues
- **Features**:
  - Detects versioned directories
  - Identifies problematic ZIP files
  - Provides step-by-step fix instructions

### 4. Proper ZIP Creation Script
- **File**: `create-proper-zip.php`
- **Purpose**: Creates correctly structured ZIP files
- **Features**:
  - Ensures files are at ZIP root (not in subdirectories)
  - Excludes development files
  - Verifies ZIP structure before distribution

### 5. Updated Documentation
- **File**: `README.md`
- **Improvement**: Added installation troubleshooting section
- **Content**: Clear instructions for preventing and fixing versioned directory issues

## 🛠️ How to Fix Current Issues

### For Users Experiencing the Error:

1. **Access the cleanup script**:
   ```
   /wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/cleanup-installation.php
   ```

2. **Follow the recommendations**:
   - Remove ZIP files from plugins directory
   - Delete versioned directories
   - Reinstall the plugin properly

3. **Verify installation**:
   - Plugin should be in: `fashion-variation-swatches-for-woocommerce-elementor/`
   - NOT in: `fashion-variation-swatches-for-woocommerce-elementor-v1.0.5/`

### For Developers Creating Distribution ZIPs:

1. **Use the proper ZIP creation script**:
   ```
   /wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/create-proper-zip.php
   ```

2. **Verify ZIP structure**:
   - Files should be at the root of the ZIP
   - No versioned subdirectories
   - All required files included

## 🔧 Technical Details

### Directory Detection Logic
```php
// Check if we're in a versioned directory (e.g., -v1.0.5)
if ( preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $current_dir ) ) {
    // Handle versioned directory case
}
```

### File Path Resolution
```php
// Try multiple path construction methods
$file_paths = [
    $actual_plugin_dir . $file,
    FASHION_VARIATION_SWATCHES_PLUGIN_DIR . $file,
    dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $file,
    dirname( __FILE__ ) . '/' . $file,
];
```

### Activation Prevention
```php
if ( preg_match( '/^' . preg_quote( $base_plugin_name, '/' ) . '-v\d+\.\d+\.\d+/', $current_dir ) ) {
    // Deactivate and show helpful error message
    deactivate_plugins( plugin_basename( __FILE__ ) );
    wp_die( 'Installation issue detected...' );
}
```

## 📋 Prevention Checklist

### Before Distribution:
- [ ] Use `create-proper-zip.php` to create ZIP files
- [ ] Verify ZIP structure (files at root, no subdirectories)
- [ ] Test installation on clean WordPress site
- [ ] Remove any existing ZIP files from plugins directory

### For Users:
- [ ] Remove ZIP files from plugins directory after installation
- [ ] Verify plugin is in correct directory (no `-v1.0.x` suffix)
- [ ] Use cleanup script if issues arise
- [ ] Report any versioned directory issues

## 🎯 Expected Directory Structure

### ✅ Correct:
```
wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/
├── wc-variation-swatches.php
├── includes/
│   ├── class-fashion-variation-swatches-core.php
│   ├── class-fashion-variation-swatches-admin.php
│   └── ...
├── assets/
└── ...
```

### ❌ Incorrect:
```
wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor-v1.0.5/
├── wc-variation-swatches.php
├── includes/
│   ├── class-fashion-variation-swatches-core.php
│   ├── class-fashion-variation-swatches-admin.php
│   └── ...
└── ...
```

## 📞 Support

If you continue to experience issues:

1. **Run the diagnostics script**:
   ```
   /wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/plugin-diagnostics.php
   ```

2. **Check the troubleshooting guide**:
   ```
   /wp-content/plugins/fashion-variation-swatches-for-woocommerce-elementor/TROUBLESHOOTING.md
   ```

3. **Contact support** with the diagnostic output

---

**Last Updated**: July 25, 2025
**Version**: 1.0.5
**Status**: ✅ Fixed 