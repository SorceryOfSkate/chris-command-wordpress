# Release process

1. Merge a reviewed pull request into protected `main` after every required check passes.
2. Keep the plugin header, `readme.txt`, package metadata, and changelog on the same semantic version.
3. Build the release from a clean commit with `tools/build-release.ps1`.
4. Verify the ZIP with `tools/verify-release.ps1` and activate that exact ZIP on WordPress 7.1 with PHP 8.3.
5. Tag the reviewed commit as `vX.Y.Z`.
6. Let the release workflow rebuild and verify the package, then attach the ZIP and SHA-256 file to a GitHub prerelease.
7. Install through WordPress by uploading the release ZIP. Raw GitHub source archives are unsupported.

Phase 0 uses manual updates and manual production installation. Automatic update checks and production deployment are intentionally deferred.
