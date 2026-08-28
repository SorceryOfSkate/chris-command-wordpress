# Chris Command for WordPress

Public WordPress foundation for approved Chris Command command-center modules.

Version 0.3.1 fixes dashboard asset loading for the standalone page template, dashboard block, and shortcode entry points. The complete command-center shell remains available through all three entry points, with the existing seven-lane News service integrated directly into its News module.

## Requirements

- WordPress 7.1 or later
- PHP 8.2 or later
- PHP 8.3 is the primary development and CI runtime

## Installation

Install the versioned `chris-command-X.Y.Z.zip` attached to a GitHub Release through **Plugins → Add New → Upload Plugin**. Do not install GitHub's automatically generated source archive.

## Development

Install Composer and Node dependencies, then run PHPCS and the public-boundary scan. Local WordPress 7.1/PHP 8.3 activation uses the checked-in WordPress Playground blueprint; CI runs the official Plugin Check tool in the native `wp-env` container.

See `docs/architecture.md` and `docs/release-process.md` for the repository contract.

## Dashboard usage

- Block: insert **Chris Command Dashboard** and use full alignment when the theme supports it.
- Shortcode: use `[chris_command_dashboard]`.
- Standalone page: select the **Chris Command Dashboard** page template to bypass theme chrome and let the plugin own the complete visual frame.
- News remains available through `/wp-json/chris-command/v1/news`; the dashboard calls that endpoint itself and does not require seven separate blocks.
