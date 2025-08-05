# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin called "KISS WP admin menu useful links" that adds custom user-defined links to the WordPress admin toolbar. The plugin allows administrators to add up to 5 custom links to the dropdown menu under the site name in the admin toolbar, with separate configurations for frontend and backend views.

## Plugin Architecture

### Single-File Architecture
The plugin follows a simple, single-file architecture:
- **Main Plugin File**: `kiss-wp-admin-menu-useful-links.php` - Contains all functionality including settings, admin interface, and toolbar modifications

### Key Components
- **Settings Management**: Uses WordPress Settings API with two separate option groups for backend and frontend links
- **Admin Interface**: Tabbed settings page under Settings > KISS Useful Links
- **Admin Bar Integration**: Hooks into `admin_bar_menu` action to add custom links with priority-based ordering
- **Upgrade System**: Includes version tracking and upgrade routines for backward compatibility

### Core Features
1. **Dual Context Support**: Separate link configurations for frontend view and admin dashboard
2. **Priority-Based Ordering**: Links can be ordered using numeric priority values (lower = higher position)
3. **Flexible URL Support**: Accepts both relative paths (`/wp-admin/edit.php`) and absolute URLs
4. **Internationalization**: Full i18n support with text domain `kiss-wp-admin-menu-useful-links`
5. **JavaScript-Enhanced Settings**: Client-side tab switching with form auto-save functionality

## Development Workflow

### Plugin Development Standards
- Follow WordPress Plugin Development standards and coding conventions
- Use WordPress APIs (Settings API, Admin Bar API, etc.) rather than custom implementations
- Maintain backward compatibility through upgrade routines
- Implement proper security measures (capability checks, nonce verification, data sanitization)

### Testing Approach
Since this is a WordPress plugin with no build process:
- Test manually in WordPress admin environment
- Verify functionality on both frontend and admin contexts
- Test with various user roles and capabilities
- Ensure proper escaping and sanitization of user input

### Key Constants and Configuration
- `KWAMUL_VERSION`: Current plugin version (1.3)
- `KWAMUL_MAX_LINKS`: Maximum number of links supported (5)
- `KWAMUL_OPTION_NAME`: Database option for backend links
- `KWAMUL_FRONTEND_OPTION_NAME`: Database option for frontend links

## Important Implementation Notes

### URL Handling
The plugin intentionally uses `sanitize_text_field()` instead of `esc_url_raw()` for URL fields to support relative paths. This is documented in the code and should not be changed without explicit requirements.

### Priority System
Links are sorted by priority value (ascending order) before being added to the admin bar. Default priority is 10, and priorities are stored as absolute integers.

### Security Considerations
- All user input is properly sanitized using WordPress functions
- Capability checks ensure only users with `manage_options` can modify settings
- Output is properly escaped using WordPress escaping functions
- No file uploads or external API calls present minimal security risk

## File Structure
```
kiss-wp-admin-menu-useful-links/
├── kiss-wp-admin-menu-useful-links.php (main plugin file)
├── README.md (plugin documentation)
├── LICENSE (GPL v2 license)
└── agents.md (development process documentation)
```

## WordPress Integration Points
- **Admin Menu**: Adds settings page under Settings menu
- **Admin Bar**: Modifies admin toolbar with custom links
- **Options API**: Stores settings in WordPress options table
- **Hooks Used**: `admin_menu`, `admin_init`, `admin_bar_menu`, `plugins_loaded`
- **Capabilities**: Requires `manage_options` for configuration access