# Release Process

## Pre-release

1. Ensure all changes are merged into `develop`
2. Update the version number in **all three** locations:
   - `php-constants-manager.php` — `Version: X.Y.Z` in the plugin header
   - `readme.txt` — `Stable tag: X.Y.Z`
   - `readme.txt` — add a changelog entry under `== Changelog ==`
3. Update `Tested up to` in `readme.txt` if WordPress has released a new version

## Release

1. Merge `develop` into `master`
2. Push `master` to GitHub

The GitHub Actions workflow handles everything from here:

- Detects the version from `php-constants-manager.php`
- Skips if a tag for that version already exists
- Builds a zip excluding dev files
- Creates a git tag (`vX.Y.Z`) and GitHub Release with auto-generated notes
- Deploys to WordPress.org SVN (only if the GitHub Release succeeds)

## Post-release

1. Verify the GitHub Release appears at https://github.com/cartpauj/php-constants-manager/releases
2. Verify the new version appears on https://wordpress.org/plugins/php-constants-manager/
3. Merge `master` back into `develop` so the branches stay in sync

## Troubleshooting

- **Release skipped?** The workflow skips if the tag already exists. Make sure you bumped the version number.
- **WP.org deploy failed?** Check that `SVN_USERNAME` and `SVN_PASSWORD` are set in the repo secrets. Review the workflow run logs for details.
- **Assets not showing on WP.org?** Add banner and icon images to a `.wordpress-org` directory in the repo root. The 10up action uploads these automatically.
