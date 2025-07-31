# Installation Guide - TH Admin Bar

## 📦 Installation Methods

### Method 1: Composer Installation (Recommended)

```bash
composer require th/module-adminbar
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

### Method 2: Manual Installation

1. Download the module files
2. Extract to `app/code/TH/Adminbar/`
3. Run the following commands:

```bash
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

### Method 3: Git Clone

```bash
cd app/code/TH/
git clone https://github.com/tuanhaviet22/magento2-module-adminbar.git Adminbar
cd ../../../
bin/magento module:enable TH_Adminbar
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## 🔧 Configuration

After installation, configure the module:

1. Go to **Admin Panel > Stores > Configuration**
2. Navigate to **TH Extensions > Admin Bar**
3. Configure the settings according to your needs
4. Save configuration and clear cache

## ✅ Verification

To verify the installation:

1. Log into Magento Admin Panel
2. Visit your frontend store
3. The admin bar should appear at the top of the page
4. Check that all links work correctly

## 🎨 For Hyva Theme Users

If you're using Hyva theme, also install the compatibility module:

```bash
composer require th/module-adminbar-hyva
bin/magento module:enable TH_AdminbarHyva
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
```

## 🐛 Troubleshooting

### Module Not Showing

- Check if module is enabled: `bin/magento module:status TH_Adminbar`
- Verify you're logged into admin
- Check module configuration
- Clear all caches

### Permission Issues

- Ensure proper file permissions
- Check Magento file ownership
- Verify web server has read access

### Cache Issues

- Clear all caches: `bin/magento cache:clean`
- Flush cache storage: `bin/magento cache:flush`
- Clear generated files: `rm -rf generated/*`

## 📞 Support

For installation issues, please:

1. Check this guide first
2. Review the main README.md
3. Create an issue on GitHub
4. Provide detailed error messages and system information
