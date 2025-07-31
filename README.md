# TH Admin Bar for Magento 2

[![Latest Stable Version](http://poser.pugx.org/th/module-adminbar/v)](https://packagist.org/packages/th/module-adminbar)
[![Total Downloads](http://poser.pugx.org/th/module-adminbar/downloads)](https://packagist.org/packages/th/module-adminbar)
[![License](http://poser.pugx.org/th/module-adminbar/license)](https://packagist.org/packages/th/module-adminbar)
[![CI](https://github.com/tuanhaviet22/magento2-module-adminbar/actions/workflows/ci.yml/badge.svg)](https://github.com/tuanhaviet22/magento2-module-adminbar/actions/workflows/ci.yml)

A WordPress-style toolbar for Magento 2, providing quick access to administrative functions while viewing the frontend.

## Key Features

- **WordPress-like interface**: Provides a familiar experience for WordPress developers
- **Context-aware functionality**:
    - Edit products (when on product page)
    - Edit CMS pages (when on CMS page)
    - Manage cache
    - Quick access to admin dashboard
- **Multi-theme support**:
    - Luma theme (built-in)
    - Hyva theme (via compatibility module TH_AdminbarHyva)
- **Easy configuration**: Admin settings for appearance and behavior
- **Performance optimized**: Minimal impact on frontend performance
- **Secure**: Only displayed for logged-in admin users

## Requirements

- PHP 7.4, 8.1, 8.2 or 8.3
- Magento 2.4.x
- Administrator privileges for module configuration

## Installation

### Via Composer (Recommended)

```bash
composer require th/module-adminbar
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

### Manual Installation

1. Download the module and extract it to the directory:

```bash
app/code/TH/Adminbar
```

2. Enable the module:

```bash
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## Configuration

Navigate to **Stores > Configuration > TH Extensions > Admin Bar** to configure:

- **Enable/Disable** the admin toolbar
- **Display in Production mode**
- **Position** (top or bottom of page)
- **Appearance** (background and text colors)

## How It Works

1. **Authentication check**: Verifies admin session via AJAX
2. **Context identification**: Automatically detects current page type (product, CMS, etc.)
3. **Dynamic display**: Shows relevant edit links based on context
4. **Theme compatibility**: Uses appropriate template based on active theme

## Technical Details

### Main Module (TH_Adminbar)

- **Backend logic**: All business logic and configuration
- **Luma compatibility**: Default templates working with Luma theme
- **AJAX endpoint**: Authentication status check
- **Admin configuration**: System configuration options

### Hyva Compatibility Module (TH_AdminbarHyva)

- **Alpine.js integration**: Reactive components
- **Tailwind CSS**: Modern styling
- **Template overrides**: Hyva-specific templates
- **CSS Merging**: Automatic Tailwind configuration merging

## Customization

### Template Customization

For Luma theme:

```
app/design/frontend/[Vendor]/[Theme]/TH_Adminbar/templates/adminbar.phtml
```

For Hyva theme:

```
app/design/frontend/[Vendor]/[Theme]/TH_AdminbarHyva/templates/hyva/adminbar.phtml
```

## License

Open Software License (OSL 3.0)

## Contribution

1. Fork repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## Support

For support and questions, please create an issue in the repository or contact the development team.
