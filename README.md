# Google Analytics 4 (GA4) Server-Side Event Sender for PHP

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-8892BF.svg?logo=php&logoColor=white)](https://www.php.net/)
[![License](https://img.shields.io/github/license/barns101/GoogleAnalyticsEvent.svg)](LICENSE)
[![Code Style](https://img.shields.io/badge/code%20style-PSR--12-blue.svg)](https://www.php-fig.org/psr/psr-12/)
[![Issues](https://img.shields.io/github/issues/barns101/GoogleAnalyticsEvent)](https://github.com/barns101/GoogleAnalyticsEvent/issues)
[![Stars](https://img.shields.io/github/stars/barns101/GoogleAnalyticsEvent?style=social)](https://github.com/barns101/GoogleAnalyticsEvent)
[![Forks](https://img.shields.io/github/forks/barns101/GoogleAnalyticsEvent)](https://github.com/barns101/GoogleAnalyticsEvent)

> A lightweight PHP class to send server-side GA4 events using the Measurement Protocol.

---

## 🚀 Features

- ✅ Sends GA4 events directly from your PHP server
- ✅ Extracts `client_id` and `session_id` from GA cookies
- ✅ Graceful fallback when cookies are unavailable
- ✅ Uses `cURL` and JSON for secure POST requests
- ✅ PHP 8+ syntax and type safety
- ✅ Error logging and response checking included

---

## 📦 Installation

Download or clone this repo, then place `GoogleAnalyticsEvent.php` in your project directory.

```bash
src/
└── Classes/
    └── GoogleAnalyticsEvent.php
```

---

## ⚙️ Configuration

```php
private string $apiSecret = '';
private string $measurementId = '';
```

You must set these values from your [Google Analytics 4 property](https://support.google.com/analytics/answer/9304153?hl=en), and create a Measurement Protocol API secret.

---

## 🧪 Usage Example

```php
use Classes\GoogleAnalyticsEvent;

$ga = new GoogleAnalyticsEvent();

$ga->sendEvent('login', [
    'method' => 'email',
    'success' => true,
]);
```

---

## 🛠 Requirements

- PHP 8.0 or higher
- Google Analytics 4 property
- Measurement Protocol API Secret
- `cURL` extension enabled

  ---

## 🤝 Contributing
Contributions are welcome! Feel free to submit a PR.

---

## 📬 Support
Have a question, bug report, or feature idea? [Open an issue](https://github.com/barns101/GoogleAnalyticsEvent/issues) or start a discussion.

---

## 📊 Related Links
- [GA4 Measurement Protocol Documentation](https://developers.google.com/analytics/devguides/collection/protocol/ga4)
