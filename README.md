# TH Admin Bar for Magento 2

A WordPress-style admin bar for Magento 2, inspired by the WordPress admin bar functionality.

## 🚀 Features

- **WordPress-style Interface**: Familiar admin bar experience for WordPress developers
- **Context-aware Actions**: 
  - Edit Product (when on product page)
  - Edit CMS Page (when on CMS page)
  - Cache Management
  - Quick Admin Dashboard access
- **Multi-theme Support**:
  - Luma Theme (built-in)
  - Hyva Theme (via TH_AdminbarHyva compatibility module)
- **Configurable**: Admin settings for appearance and behavior
- **Performance Optimized**: Minimal impact on frontend performance
- **Security**: Only shows for logged-in admin users

## 📦 Installation

### Manual Installation

1. Copy the module to your Magento installation:
```bash
cp -r TH/Adminbar app/code/TH/Adminbar
```

2. Enable the module:
```bash
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento cache:clean
```

### For Hyva Theme Users

Additionally install the Hyva compatibility module:

```bash
cp -r TH/AdminbarHyva app/code/TH/AdminbarHyva
bin/magento module:enable TH_AdminbarHyva
bin/magento setup:upgrade
bin/magento cache:clean
```

## ⚙️ Configuration

Navigate to **Stores > Configuration > TH Extensions > Admin Bar** to configure:

- **Enable/Disable** the admin bar
- **Show in Production Mode** setting
- **Position** (top or bottom)
- **Appearance** (background and text colors)

## 🎯 How It Works

1. **Authentication Check**: Verifies admin session via AJAX
2. **Context Detection**: Automatically detects current page type (product, CMS, etc.)
3. **Dynamic Rendering**: Shows relevant edit links based on context
4. **Theme Compatibility**: Uses appropriate template based on active theme

## 🔧 Technical Details

### Main Module (TH_Adminbar)
- **Backend Logic**: All business logic and configuration
- **Luma Compatibility**: Default templates work with Luma theme
- **AJAX Endpoints**: Authentication status checking
- **Admin Configuration**: System configuration options

### Hyva Compatibility Module (TH_AdminbarHyva)
- **Alpine.js Integration**: Reactive components
- **Tailwind CSS**: Modern styling
- **Template Overrides**: Hyva-specific templates
- **CSS Merging**: Automatic Tailwind configuration merging

## 🎨 Customization

### Template Customization

For Luma theme:
```
app/design/frontend/[Vendor]/[Theme]/TH_Adminbar/templates/adminbar.phtml
```

For Hyva theme:
```
app/design/frontend/[Vendor]/[Theme]/TH_AdminbarHyva/templates/hyva/adminbar.phtml
```

### Styling Customization

The admin bar uses inline styles by default but can be customized via:
- Admin configuration (colors)
- CSS overrides in your theme
- Tailwind utilities (Hyva theme)

## 🔒 Security

- Only displays for authenticated admin users
- Respects Magento's admin session management
- Can be disabled in production mode
- Uses Magento's built-in CSRF protection

## 📱 Responsive Design

- Mobile-friendly responsive design
- Collapsible on smaller screens
- Touch-friendly interface

## 🚀 Performance

- **Minimal HTTP Requests**: Single AJAX call for status
- **Lazy Loading**: Only loads when admin is authenticated
- **Cached Templates**: Leverages Magento's template caching
- **Optimized Assets**: Minimal CSS and JavaScript footprint

## 🔄 Compatibility

- **Magento Versions**: 2.4.x
- **PHP Versions**: 7.4, 8.1, 8.2, 8.3
- **Themes**: Luma, Hyva (with compatibility module)
- **Caching**: Compatible with all Magento caching layers

## 🐛 Troubleshooting

### Admin Bar Not Showing
1. Check if module is enabled: `bin/magento module:status TH_Adminbar`
2. Verify admin is logged in
3. Check configuration: Stores > Configuration > TH Extensions > Admin Bar
4. Clear cache: `bin/magento cache:clean`

### Hyva Theme Issues
1. Ensure TH_AdminbarHyva module is installed and enabled
2. Check Alpine.js is loaded on the page
3. Verify Tailwind CSS compilation includes admin bar styles

## 📄 License

Open Software License (OSL 3.0)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For support and questions, please create an issue in the repository or contact the development team.
