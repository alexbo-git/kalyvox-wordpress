# Kalyvox AI Receptionist – Call Widget

Lightweight WordPress plugin that adds a configurable click-to-call widget and shortcode for Kalyvox or any business phone number.

## Features

- Floating call widget with left/right positioning
- Desktop + mobile or mobile-only visibility
- Configurable label and color
- `[kalyvox_call_button]` shortcode
- No remote assets, JavaScript dependency or tracking
- WordPress-native sanitization and capability checks
- Translation-ready through the WordPress.org translation system

## Test without installing WordPress locally

1. Open [WordPress Playground](https://playground.wordpress.net/).
2. Upload a release ZIP whose root directory is `kalyvox-ai-receptionist-call-widget/`.
3. Activate **Kalyvox AI Receptionist – Call Widget**.
4. Open **Settings → Kalyvox**.

## Shortcode

```text
[kalyvox_call_button]
```

Optional overrides:

```text
[kalyvox_call_button label="Call sales" phone="+33123456789"]
```

## Development

No build step or package manager is required.

```bash
php -l kalyvox-ai-receptionist.php
php -l uninstall.php
```

## License

GPL-2.0-or-later.
