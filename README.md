# Fashion Variation Swatches for WooCommerce and Elementor

[![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-5.0+-green.svg)](https://woocommerce.com/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%202.0+-orange.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A beautiful and professional WordPress plugin that transforms WooCommerce product variations into stunning, user-friendly swatches. Perfect for fashion, apparel, and any store with size/color variations.

## ✨ Features

- **🎨 Beautiful Swatches**: Replace boring dropdowns with attractive size and color swatches
- **📱 Mobile Responsive**: Optimized for all device sizes
- **⚡ AJAX Filtering**: Fast, smooth shop page filtering without page reloads
- **🎯 Elementor Integration**: Custom widgets for size and color swatches
- **🔧 HPOS Compatible**: Full compatibility with WooCommerce High-Performance Order Storage
- **🎨 Multiple Styles**: Square, rounded, and circle swatch styles
- **💡 Tooltips**: Optional tooltips showing attribute names
- **♿ Accessibility**: Full keyboard navigation and screen reader support
- **🚀 Performance Optimized**: Lightweight and fast loading

## 🎯 Perfect For

- **Fashion & Apparel Stores**: Clothing, shoes, accessories
- **Product Catalogs**: Any store with size/color variations
- **E-commerce Sites**: Improve user experience and conversion rates
- **WooCommerce Stores**: Professional variation selection

## 🚀 Quick Start

### Installation

1. **Download** the plugin files
2. **Upload** to `/wp-content/plugins/wc-variation-swatches/`
3. **Activate** the plugin in WordPress Admin > Plugins
4. **Configure** settings in WooCommerce > Variation Swatches

### Demo Setup

Run the demo setup script to see the plugin in action:

```
Access: /wp-content/plugins/wc-variation-swatches/demo-setup.php
```

This will create sample attributes and a demo product with all variations.

## 📖 Documentation

- **[Quick Start Guide](QUICK-START.md)** - Get started in 5 minutes
- **[User Guide](USER-GUIDE.md)** - Complete feature documentation
- **[HPOS Compatibility](README-HPOS.md)** - High-Performance Order Storage guide

## 🛠️ Configuration

### Basic Setup

1. **Go to** WooCommerce > Variation Swatches
2. **Configure** your attributes:
   - Size Attribute: `pa_size` (default)
   - Color Attribute: `pa_color` (default)
3. **Enable** features:
   - Shop Filters: Add filtering to shop pages
   - Tooltips: Show attribute names on hover

### Product Setup

1. **Create** a Variable Product
2. **Add** Size and Color attributes
3. **Create** variations for each combination
4. **Set** colors for color variations
5. **Publish** your product

## 🎨 Customization

### Swatch Styles

Choose from multiple visual styles:
- **Size Swatches**: Square, Rounded, Circle
- **Color Swatches**: Square, Circle

### Elementor Widgets

If using Elementor, you get additional widgets:
- **WC Size Swatches**: Display size options
- **WC Color Swatches**: Show color variations

Both widgets include extensive styling options.

### Custom CSS

Add custom styling to match your theme:

```css
.fashion-color-swatch {
    border: 2px solid #333;
    transition: all 0.3s ease;
}

.fashion-color-swatch:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
```

## 🔧 Technical Details

### Requirements

- **WordPress**: 5.0 or higher
- **WooCommerce**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Elementor**: 3.0+ (optional, for widgets)

### File Structure

```
wc-variation-swatches/
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   ├── frontend.css
│   │   └── elementor-editor.css
│   └── js/
│       ├── admin.js
│       ├── frontend.js
│       └── shop-filters.js
├── includes/
│   ├── class-fashion-variation-swatches-core.php
│   ├── class-fashion-variation-swatches-admin.php
│   ├── class-fashion-variation-swatches-frontend.php
│   ├── class-fashion-variation-swatches-attributes.php
│   ├── class-fashion-variation-swatches-shop-filters.php
│   └── elementor/
│       ├── class-fashion-variation-swatches-elementor.php
│       └── widgets/
│           ├── class-fashion-color-swatches-widget.php
│           └── class-fashion-size-swatches-widget.php
├── languages/
│   └── fashion-variation-swatches.pot
├── wc-variation-swatches.php
├── readme.txt
├── CHANGELOG.md
├── USER-GUIDE.md
├── QUICK-START.md
├── README-HPOS.md
├── demo-setup.php
├── test-hpos-compatibility.php
└── .gitignore
```

### Hooks and Filters

The plugin provides various hooks for customization:

```php
// Filter swatch HTML
add_filter( 'fashion_variation_swatches_html', 'custom_swatch_html', 10, 3 );

// Modify swatch styles
add_filter( 'fashion_variation_swatches_styles', 'custom_swatch_styles' );

// Customize filter behavior
add_filter( 'fashion_variation_swatches_filter_args', 'custom_filter_args' );
```

## 🆘 Support

### Troubleshooting

**Swatches Not Showing?**
- Check that variations are created and published
- Verify attributes are set to "Used for variations"
- Ensure the product is published

**Filters Not Working?**
- Check that shop filters are enabled in settings
- Ensure your theme has a sidebar on shop pages
- Clear any caching plugins

**Colors Not Displaying?**
- Make sure colors are set for each color term
- Check that the color attribute is properly configured

### Getting Help

1. **Check Documentation**: Review the guides in this repository
2. **Test Compatibility**: Use the HPOS test file if needed
3. **Contact Support**: Reach out with specific issues

## 🤝 Contributing

We welcome contributions! Please:

1. **Fork** the repository
2. **Create** a feature branch
3. **Make** your changes
4. **Test** thoroughly
5. **Submit** a pull request

### Development Setup

1. **Clone** the repository
2. **Install** in a WordPress development environment
3. **Activate** the plugin
4. **Run** the demo setup script
5. **Test** all features

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🙏 Credits

- **Author**: Uditha Mahindarathna
- **Email**: melan.udi@gmail.com
- **Plugin URI**: https://github.com/udi17live/Fashion-Variation-Swatches-for-WooCommerce-and-Elementor

## 📈 Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes.

## ⭐ Star This Repository

If you find this plugin helpful, please consider starring this repository! It helps others discover the project.

---

**Made with ❤️ for the WordPress community** 