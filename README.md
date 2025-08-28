# Bagisto Wompi Payment Gateway

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/webkul/bagisto-wompi)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Bagisto](https://img.shields.io/badge/bagisto-2.x-orange.svg)](https://bagisto.com)

A comprehensive Wompi Payment Gateway integration for Bagisto e-commerce platform, specifically designed for Panama market with support for Clave and international credit/debit cards.

## Features

- 🇵🇦 **Panama Market Focus**: Full support for Wompi Panama payment methods
- 💳 **Multiple Payment Methods**: Credit cards, debit cards, and Clave (Panama)
- 🔐 **Secure Transactions**: 3D Secure authentication and SHA256 signatures
- 📱 **Widget Integration**: Embedded and popup payment forms
- 🔔 **Real-time Webhooks**: Automatic order status updates
- 🌍 **Multi-language**: Spanish and English support
- 📊 **Transaction Tracking**: Complete transaction history and status monitoring
- ⚡ **Easy Configuration**: Simple admin panel setup

## Requirements

- **Bagisto**: 2.x
- **PHP**: 8.1 or higher  
- **Laravel**: 10.x or 11.x
- **Extensions**: curl, json

## Installation

### Method 1: Manual Installation

1. **Copy the module** to your Bagisto project:
   ```bash
   cp -r packages/Webkul/Wompi /your-bagisto-project/packages/Webkul/Wompi
   ```

2. **Register the module** in `config/concord.php`:
   ```php
   'modules' => [
       // ... other modules
       \Webkul\Wompi\Providers\WompiServiceProvider::class,
   ],
   ```

3. **Run migrations**:
   ```bash
   php artisan migrate
   php artisan config:cache
   ```

### Method 2: Composer Installation (Future)

```bash
composer require webkul/bagisto-wompi
php artisan migrate
```

## Configuration

1. **Access Admin Panel** → Sales → Payment Methods
2. **Enable Wompi** payment method
3. **Configure credentials**:
   - **Sandbox Mode**: Enable for testing
   - **Public Key (Sandbox)**: Your sandbox public key
   - **Private Key (Sandbox)**: Your sandbox private key  
   - **Public Key (Production)**: Your production public key
   - **Private Key (Production)**: Your production private key

### Wompi Dashboard Configuration

Configure these URLs in your Wompi dashboard:

- **Webhook URL**: `https://your-domain.com/wompi/webhook`
- **Success URL**: `https://your-domain.com/wompi/success`
- **Cancel URL**: `https://your-domain.com/wompi/cancel`

## Usage

1. **Customer selects Wompi** as payment method during checkout
2. **Redirect to payment page** with Wompi widget
3. **Customer completes payment** using their preferred method
4. **Automatic webhook processing** updates order status
5. **Customer redirected** to success/cancel page based on result

## Supported Payment Methods

- **Credit Cards**: Visa, MasterCard
- **Debit Cards**: Local and international
- **Clave**: Panama's national payment system
- **3D Secure**: Enhanced security for card transactions

## File Structure

```
packages/Webkul/Wompi/
├── src/
│   ├── Config/system.php           # Admin configuration
│   ├── Http/Controllers/           # Controllers
│   ├── Models/                     # Eloquent models  
│   ├── Payment/Wompi.php          # Main payment class
│   ├── Repositories/              # Data repositories
│   └── Resources/                 # Views and translations
├── composer.json                   # Package definition
└── README.md                      # Documentation
```

## For New Projects

To use this module in another Bagisto project:

1. **Copy the entire folder**:
   ```bash
   cp -r packages/Webkul/Wompi /new-project/packages/Webkul/Wompi
   ```

2. **Add to concord.php** in the new project:
   ```php
   \Webkul\Wompi\Providers\WompiServiceProvider::class,
   ```

3. **Run setup**:
   ```bash
   cd /new-project
   php artisan migrate
   php artisan config:cache
   ```

4. **Configure in Admin Panel** with your Wompi credentials

## License

MIT License - see LICENSE file for details.

---

**Ready to use in any Bagisto project!**