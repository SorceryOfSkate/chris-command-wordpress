# Chris Command for WordPress

Public WordPress foundation for approved Chris Command command-center modules.

Version 0.1.0 is an intentionally inert Phase 0 prerelease. It establishes repository boundaries, plugin structure, security checks, packaging, and release conventions without migrating News or any private dashboard module.

## Requirements

- WordPress 7.1 or later
- PHP 8.2 or later
- PHP 8.3 is the primary development and CI runtime

## Installation

Install the versioned `chris-command-X.Y.Z.zip` attached to a GitHub Release through **Plugins → Add New → Upload Plugin**. Do not install GitHub's automatically generated source archive.

## Development

Install Composer and Node dependencies, then run PHPCS and the public-boundary scan. Local WordPress 7.1/PHP 8.3 activation uses the checked-in WordPress Playground blueprint; CI runs the official Plugin Check tool in the native `wp-env` container.

See `docs/architecture.md` and `docs/release-process.md` for the repository contract.
