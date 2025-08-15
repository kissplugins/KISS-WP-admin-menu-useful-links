# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.6.0] - 2025-08-15

### 🚨 CRITICAL SECURITY FIXES
- **FIXED**: Critical nonce verification bypass vulnerability
  - Added proper nonce verification in `kwamul_sanitize_links_options()` function
  - Previously, nonce was only checked in display function, not during actual form processing
  - This prevented CSRF attacks on settings updates
- **FIXED**: URL validation security vulnerability
  - Implemented comprehensive `kwamul_validate_url()` function
  - Blocks dangerous protocols (javascript:, data:, vbscript:, etc.)
  - Prevents XSS attacks while maintaining relative path support
  - Validates URL structure and blocks malicious patterns
- **IMPROVED**: Superglobal access pattern
  - Updated `$_GET` access to use null coalescing operator
  - Follows WordPress coding standards more closely

### Security
- All critical and high-priority security issues resolved
- Plugin now follows WordPress security best practices
- Ready for production use with enhanced security posture

## [1.5.0] - Previous Release

### Added
- Priority system for link ordering
- Frontend and backend link separation
- Transient caching for improved performance
- Comprehensive admin interface with tabs

### Changed
- Improved code structure and documentation
- Enhanced user interface
- Better error handling

### Fixed
- Various minor bugs and improvements

## [1.4.0] - Previous Release

### Added
- Multiple link support (up to 5 links)
- Admin settings page
- Proper sanitization and validation

## [1.3.0] - Previous Release

### Added
- Basic functionality for adding custom links to admin bar
- WordPress admin integration

## [1.0.0] - Initial Release

### Added
- Initial plugin structure
- Basic link functionality
