# Chris Command for WordPress

Public WordPress foundation for approved Chris Command command-center modules.

Version 0.2.0 adds the first deliberately small public module: server-rendered News. It exposes seven allowlisted RSS categories through a dynamic block, shortcode, and read-only REST endpoint without including any private dashboard module or data.

## Requirements

- WordPress 7.1 or later
- PHP 8.2 or later
- PHP 8.3 is the primary development and CI runtime

## Installation

Install the versioned `chris-command-X.Y.Z.zip` attached to a GitHub Release through **Plugins → Add New → Upload Plugin**. Do not install GitHub's automatically generated source archive.

## Development

Install Composer and Node dependencies, then run PHPCS and the public-boundary scan. Local WordPress 7.1/PHP 8.3 activation uses the checked-in WordPress Playground blueprint; CI runs the official Plugin Check tool in the native `wp-env` container.

See `docs/architecture.md` and `docs/release-process.md` for the repository contract.

## News usage

- Block: insert **Chris Command News**, then choose a category and story count in the block settings.
- Shortcode: use `[chris_command_news]` or `[chris_command_news category="philippines" limit="5"]`.
- REST: request `/wp-json/chris-command/v1/news` for all categories or append `?category=tech` for one allowlisted lane.
