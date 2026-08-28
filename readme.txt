=== Chris Command ===
Contributors: sorceryofskate
Tags: command-center, dashboard, news, rss
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 0.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Public WordPress frontend for approved Chris Command modules.

== Description ==

Chris Command 0.3.0 adds the complete public command-center dashboard shell. One dashboard block, shortcode, or standalone page template renders the responsive interface, and the existing seven-category News service appears inside its News module.

== Installation ==

1. Download the versioned release ZIP.
2. In WordPress, open Plugins > Add New > Upload Plugin.
3. Upload the ZIP and activate Chris Command.
4. Create a page and add the Chris Command Dashboard block, use the dashboard shortcode, or select the standalone Chris Command Dashboard page template.

== External services ==

The News module requests RSS feeds from Google News only when a News block, shortcode, or REST response needs uncached data. Those server-side requests disclose the WordPress server's IP address, the configured News query, and a Chris Command user-agent string to Google.

Google Privacy Policy: https://policies.google.com/privacy
Google Terms of Service: https://policies.google.com/terms

Dashboard pages request the Orbitron, Rajdhani, and Work Sans font stylesheets from Google Fonts to preserve the established Chris Command typography. This browser request discloses the visitor's IP address and standard request metadata to Google.

Google Fonts: https://fonts.google.com/

The optional Audio Channel does not make a request until a visitor supplies a public open.spotify.com URL. If used, the browser loads Spotify's embed player and discloses the visitor's IP address, the selected Spotify content URL, and standard request metadata to Spotify. The selected URL remains in that browser's local storage and is not written to WordPress.

Spotify Privacy Policy: https://www.spotify.com/legal/privacy-policy/
Spotify Terms and Conditions: https://www.spotify.com/legal/end-user-agreement/

== Changelog ==

= 0.3.0 =
* Add the complete public Chris Command dashboard shell as a dynamic block, shortcode, and standalone page template.
* Integrate live News into the dashboard's existing News surface.
* Add scoped responsive assets, internal scrolling, folding rails, module navigation, focus timer, and browser-local Audio Channel.
* Keep private dashboard modules, data, credentials, URLs, and the unapproved source background image out of the public plugin.

= 0.2.0 =
* Add the first visible News module with seven approved Google News RSS categories.
* Add bounded transient caching, stale fallback, and the public read-only News REST endpoint.
* Add a dynamic News block and shared shortcode renderer.

= 0.1.0 =
* Initial Phase 0 plugin foundation.
