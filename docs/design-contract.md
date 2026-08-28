# Design contract

The private dashboard and public plugin share a visual language, not a code dependency.

Future public modules will use component-scoped `--cc-*` design tokens for the red command-center palette, typography, spacing, borders, glow, focus indicators, and motion. Public styles must be scoped to the plugin root so they cannot alter the active WordPress theme or administration interface.

Accessibility is part of the contract: visible keyboard focus, semantic controls, sufficient contrast, reduced-motion support, responsive scrolling, and no interaction that depends solely on color or pointer input.

Existing private imagery and fonts are excluded unless ownership, redistribution license, metadata, and performance have been reviewed. Visual consistency is maintained through this contract and later screenshot fixtures, not a shared package.
