=== Chris Command ===
Contributors: sorceryofskate
Tags: command-center, dashboard, news, rss
Requires at least: 7.1
Tested up to: 7.1
Requires PHP: 8.2
Stable tag: 0.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Public WordPress frontend for approved Chris Command modules.

== Description ==

Chris Command 0.2.0 adds the first public module: a minimal server-rendered News feed with seven approved categories, bounded caching, a read-only REST endpoint, a dynamic block, and a shortcode fallback.

== Installation ==

1. Download the versioned release ZIP.
2. In WordPress, open Plugins > Add New > Upload Plugin.
3. Upload the ZIP and activate Chris Command.

== External services ==

The News module requests RSS feeds from Google News only when a News block, shortcode, or REST response needs uncached data. Those server-side requests disclose the WordPress server's IP address, the configured News query, and a Chris Command user-agent string to Google.

Google Privacy Policy: https://policies.google.com/privacy
Google Terms of Service: https://policies.google.com/terms

== Changelog ==

= 0.2.0 =
* Add the first visible News module with seven approved Google News RSS categories.
* Add bounded transient caching, stale fallback, and the public read-only News REST endpoint.
* Add a dynamic News block and shared shortcode renderer.

= 0.1.0 =
* Initial Phase 0 plugin foundation.
