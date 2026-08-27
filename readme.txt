=== Kalyvox AI Receptionist – Call Widget ===
Contributors: kalyvox
Tags: ai receptionist, call button, click to call, phone, customer service
Requires at least: 6.5
Tested up to: 7.0.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a clean click-to-call widget for your AI receptionist, with mobile controls, custom styling and a reusable shortcode.

== Description ==

Kalyvox AI Receptionist – Call Widget adds a lightweight phone CTA to your WordPress site.

Use it with your Kalyvox number so website visitors can call your AI receptionist directly, or use it with any callable business number.

**What the plugin does**

* Adds an optional floating call button.
* Supports bottom-left or bottom-right placement.
* Can appear everywhere or only on mobile.
* Lets you customize the label and button color.
* Includes the `[kalyvox_call_button]` shortcode for pages, posts and compatible builders.
* Uses standard `tel:` links — no JavaScript dependency and no remote assets.
* Is translation-ready and follows WordPress internationalization conventions.

**About Kalyvox**

Kalyvox is an AI phone receptionist for inbound business calls. Depending on your Kalyvox configuration, the assistant can answer calls 24/7, qualify requests, answer common questions, transfer calls, book appointments and send structured summaries or alerts to your team.

A Kalyvox account is not required for the plugin itself: the call widget works with any phone number. Kalyvox is an optional external service that can answer calls placed to a Kalyvox number.

Kalyvox website: https://kalyvox.ai/

**Privacy**

The plugin itself does not send visitor data to Kalyvox or any other external service. It renders a standard `tel:` hyperlink in the visitor's browser. A connection to a telephone service occurs only when the visitor chooses to call the configured number, outside WordPress.

The administration page contains clearly identified links to Kalyvox resources. Those links are only requested when an administrator clicks them.

== Installation ==

1. Install and activate the plugin.
2. Go to **Settings → Kalyvox**.
3. Enter your Kalyvox or business phone number.
4. Customize the label, position, visibility and color.
5. Save your settings and test the button on your site.

To place a call button inside content, use:

`[kalyvox_call_button]`

Optional overrides:

`[kalyvox_call_button label="Call sales" phone="+33123456789"]`

== Frequently Asked Questions ==

= Do I need a Kalyvox account? =

No. The widget can call any telephone number. If the configured number is handled by Kalyvox, callers can reach your AI receptionist.

= Does the plugin load scripts from Kalyvox? =

No. The public widget has no external JavaScript, fonts, images or tracking calls.

= Can I show the button only on mobile? =

Yes. Choose **Mobile only** in the plugin settings.

= Can I insert the button in a page instead of floating it? =

Yes. Use the `[kalyvox_call_button]` shortcode. You can disable the floating widget and keep using the shortcode.

= Is the plugin translatable? =

Yes. The admin and default button label ship in English, French, Spanish and German, and all strings use the `kalyvox-ai-receptionist` text domain.

== Screenshots ==

1. Kalyvox settings page with guided setup and widget options.
2. Floating call widget on a website.
3. Shortcode usage and preview.

== Changelog ==

= 1.0.0 =
* Initial release.
* Floating call widget.
* Mobile-only visibility.
* Position and color controls.
* Configurable button label.
* Reusable call-button shortcode.
* Bundled English, French, Spanish and German interface support.
