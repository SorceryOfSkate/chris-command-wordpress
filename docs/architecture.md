# Architecture

Chris Command is split into two independent products:

- The existing private dashboard owns personal data, private tools, experiments, credentials, and internal integrations.
- This repository owns only the public WordPress plugin and explicitly approved public modules.

The public repository has fresh Git history and never imports the private repository as a package, submodule, subtree, or runtime dependency. Public modules are registered explicitly through `Module_Registry`; filesystem discovery and automatic cross-repository synchronization are prohibited.

News is the first approved module. It keeps category configuration, feed transport, normalization, caching, REST delivery, rendering, block metadata, and tests isolated in its own namespace and directories.

The public endpoint is read-only and accepts only the seven configured category slugs. Feed URLs are generated internally and cannot be supplied by callers. Fresh results use a 15-minute transient; a separate cache may serve results for no more than 24 hours when a feed fails.

## Module boundary

Each approved module will own its PHP services, block source, compiled assets, templates, tests, storage classification, external-service disclosure, and REST permission model. A module is added only after completing `module-approval-checklist.md`.

Anonymous device preferences may use versioned browser storage. Shared configuration belongs in WordPress options, expiring remote data belongs in WordPress cache APIs, and publishable editorial content belongs in WordPress content types.
