# Public module approval checklist

Before a module enters this repository, confirm all of the following:

- Its purpose and complete data flow are safe for public distribution.
- It contains no private dashboard module, personal record, customer information, note, credential, private URL, or internal configuration.
- Every external service and asset has documented terms, privacy impact, license, timeout, cache, and failure behavior.
- WordPress capabilities, REST permissions, nonces, validation, sanitization, and output escaping are defined.
- Browser, WordPress option, transient, user-meta, and content storage are explicitly classified.
- Keyboard, focus, responsive, reduced-motion, and failure states are testable.
- Installation, upgrade, uninstall, and rollback behavior are documented.
- The public-boundary scan and all required CI gates pass.
