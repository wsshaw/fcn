# Version Management Guide

This document outlines the version management practices for Fantasy Collecting.

## Version Source of Truth

The authoritative version is stored in the `VERSION` file in the project root. This file contains a single line with the current version number using semantic versioning (e.g., `0.2.0`).

## Semantic Versioning

Fantasy Collecting follows [Semantic Versioning](https://semver.org/) with the format `MAJOR.MINOR.PATCH`:

- **MAJOR**: Incompatible changes, database schema changes, major feature overhauls
- **MINOR**: New features, significant improvements, backwards-compatible changes  
- **PATCH**: Bug fixes, security patches, minor improvements

## Release Process

### 1. Update Version
```bash
# Update the VERSION file
echo "0.3.0" > VERSION

# Verify version is loaded correctly
php -r "require 'config.php'; echo getVersionInfo();"
```

### 2. Create Git Tag
```bash
# Create annotated tag
git tag -a v0.3.0 -m "Release v0.3.0"

# Push tag to remote
git push origin v0.3.0
```

### 3. Update Documentation
- Update README.md title with new version
- Update CHANGELOG.md with release notes
- Review any hardcoded version references in code

## Version Functions

The following functions are available for version management:

```php
// Get current version
$version = getVersion(); // "0.2.0"

// Get formatted version info
$info = getVersionInfo(); // "Fantasy Collecting v0.2.0 (development)"
$simple = getVersionInfo(false); // "Fantasy Collecting v0.2.0"

// Compare versions
$result = compareVersion('0.1.0'); // 1 (current is newer)
```

## Best Practices

1. **Never hardcode versions** in individual files - use `getVersion()` instead
2. **Update VERSION file first**, then create git tags
3. **Use annotated tags** for releases: `git tag -a v0.2.0 -m "Release message"`
4. **Test version detection** after updates: `php -r "require 'config.php'; echo getVersionInfo();"`
5. **Document changes** in commit messages and release notes

## File Version Headers

For individual PHP files, use the version helper instead of hardcoded versions:

```php
/**
 * @version    <?php echo getVersion(); ?> (modernized)
 */
```

Or reference the major version only:
```php
/**
 * @version    0.2 (modernized)
 */
```