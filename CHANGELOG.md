# Changelog

## 0.3.0 - 2026-08-28

- Make the redesigned public Chris Command dashboard the primary WordPress frontend.
- Add one dynamic dashboard block, shortcode fallback, and standalone page template.
- Integrate the seven-lane News REST service into the dashboard's News surface.
- Add scoped responsive command-center CSS, internal scrolling, folding rails, module navigation, focus timer, and an optional browser-local Spotify embed.
- Exclude all private source modules, personal payloads, credentials, URLs, and the unapproved illustrated background.

## 0.2.0 - 2026-08-28

- Add seven allowlisted Google News RSS categories through WordPress `fetch_feed()`.
- Normalize public article data and provide bounded transient-backed stale fallback.
- Add the public read-only News REST endpoint.
- Add the dynamic Chris Command News block and shared shortcode renderer.
- Add unit coverage for normalization and failure behavior.

## 0.1.0 - 2026-08-28

- Establish the public WordPress plugin repository and security boundary.
- Add an inert plugin bootstrap and explicit module contract.
- Add lean CI, activation smoke testing, and release ZIP verification.
- Include no public modules or migrated private application code.
