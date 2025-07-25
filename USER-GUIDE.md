# Fashion Variation Swatches - User Guide

## 🎯 What This Plugin Does

The Fashion Variation Swatches plugin transforms your WooCommerce product variations into beautiful, clickable swatches. Instead of boring dropdown menus, your customers will see:

- **Color Swatches**: Circular color buttons for color variations
- **Size Swatches**: Square/rectangular buttons for size variations
- **Shop Filters**: Filter products by size and color on your shop page

Perfect for fashion, apparel, and any store with color/size variations!

## 🚀 Quick Start Guide

### Step 1: Install and Activate
1. Upload the plugin to `/wp-content/plugins/wc-variation-swatches/`
2. Activate the plugin in WordPress Admin > Plugins
3. The plugin will automatically start working!

### Step 2: Set Up Product Attributes
1. Go to **Products > Attributes** in your WordPress admin
2. Create two attributes:
   - **Size** (e.g., `pa_size` with values: XS, S, M, L, XL)
   - **Color** (e.g., `pa_color` with values: Red, Blue, Green, Black, White)

### Step 3: Add Variations to Products
1. Edit any product
2. Go to the **Product Data** section
3. Select **Variable product**
4. Add your attributes (Size, Color)
5. Create variations for each combination
6. Set colors for each variation (in the variation settings)

### Step 4: Configure Plugin Settings
1. Go to **WooCommerce > Settings > Fashion Variation Swatches**
2. Configure your preferences (see settings section below)

## ⚙️ Plugin Settings

### General Settings
- **Size Attribute**: Choose which attribute to use for sizes (default: `pa_size`)
- **Color Attribute**: Choose which attribute to use for colors (default: `pa_color`)
- **Enable Tooltips**: Show color names on hover
- **Enable Shop Filters**: Add size/color filters to shop pages

### Style Settings
- **Size Swatch Style**: Choose between square, circle, or rectangle
- **Color Swatch Style**: Choose between circle or square
- **Swatch Size**: Small, Medium, or Large

## 🎨 How It Works

### On Product Pages
1. **Color Swatches**: Customers see circular color buttons instead of dropdown
2. **Size Swatches**: Customers see square/rectangular size buttons
3. **Click to Select**: Click any swatch to select that variation
4. **Visual Feedback**: Selected swatches are highlighted
5. **Stock Status**: Out-of-stock variations are grayed out

### On Shop Pages
1. **Filter Widgets**: Size and color filters appear in the sidebar
2. **Multiple Selection**: Select multiple sizes or colors
3. **AJAX Filtering**: Results update instantly without page reload
4. **Clear Filters**: Easy button to clear all selections

## 📱 Elementor Integration

If you're using Elementor, you get additional widgets:

### Color Swatches Widget
1. Add the **Fashion Color Swatches** widget to your page
2. Select a product or product category
3. Choose display options (grid, list, etc.)
4. Customize colors, borders, and spacing

### Size Swatches Widget
1. Add the **Fashion Size Swatches** widget to your page
2. Select a product or product category
3. Choose display options
4. Customize styling

## 🛠️ Advanced Configuration

### Custom CSS
You can add custom CSS to further style your swatches:

```css
/* Custom swatch styling */
.fashion-color-swatch {
    border: 2px solid #333;
    transition: all 0.3s ease;
}

.fashion-color-swatch:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.fashion-size-swatch {
    font-weight: bold;
    text-transform: uppercase;
}
```

### Custom Attributes
To use different attribute names:

1. Go to **Products > Attributes**
2. Create attributes with different names (e.g., `pa_brand`, `pa_material`)
3. Update plugin settings to use your custom attributes

## 🎯 Best Practices

### For Color Variations
1. **Use Clear Color Names**: Red, Blue, Green (not "Option 1, Option 2")
2. **Set Accurate Colors**: Use the color picker in variation settings
3. **Provide Good Images**: Each variation should have a clear product image

### For Size Variations
1. **Use Standard Sizes**: XS, S, M, L, XL or numerical sizes
2. **Be Consistent**: Use the same size naming across all products
3. **Include Size Charts**: Link to size charts for better customer experience

### For Shop Filters
1. **Limit Options**: Don't overwhelm customers with too many filter options
2. **Clear Labels**: Use descriptive filter titles
3. **Test Performance**: Ensure filters work quickly on your site

## 🔧 Troubleshooting

### Swatches Not Showing
1. **Check Attributes**: Ensure you've created the correct attributes
2. **Verify Variations**: Make sure variations are created and published
3. **Check Settings**: Verify attribute names in plugin settings

### Filters Not Working
1. **Enable Filters**: Check if shop filters are enabled in settings
2. **Check Theme**: Ensure your theme supports WooCommerce sidebar
3. **Test AJAX**: Verify AJAX is working on your site

### Styling Issues
1. **Theme Conflicts**: Some themes may override plugin styles
2. **Custom CSS**: Add custom CSS to override theme styles
3. **Cache**: Clear any caching plugins

## 📊 Performance Tips

1. **Optimize Images**: Use properly sized product images
2. **Limit Variations**: Too many variations can slow down the page
3. **Use Caching**: Enable caching for better performance
4. **CDN**: Use a CDN for faster image loading

## 🆘 Support

If you need help:

1. **Check This Guide**: Review the troubleshooting section
2. **Test Compatibility**: Use the HPOS test file if needed
3. **Contact Support**: Reach out with specific issues

## 🎉 Success Stories

This plugin is perfect for:
- **Fashion Stores**: Clothing, shoes, accessories
- **Home & Garden**: Furniture, decor, plants
- **Electronics**: Phones, laptops, accessories
- **Sports Equipment**: Apparel, gear, equipment

## 🔄 Updates

Keep your plugin updated for:
- New features and improvements
- Security updates
- WooCommerce compatibility
- Performance enhancements

---

**Need more help?** Check the plugin documentation or contact support with specific questions! 