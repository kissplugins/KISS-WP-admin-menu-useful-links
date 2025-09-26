<?php
/**
 * Plugin Name:       KISS WP admin menu useful links
 * Plugin URI:        https://example.com/kiss-wp-admin-menu-useful-links
 * Description:       Adds custom user-defined links to the bottom of the Site Name menu in the WP admin toolbar on the front end.
 * Version:           1.7
 * Author:            KISS Plugins
 * Author URI:        https://example.com/kiss-plugins
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kiss-wp-admin-menu-useful-links
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include the Plugin Update Checker
require_once plugin_dir_path( __FILE__ ) . 'lib/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$update_checker = PucFactory::buildUpdateChecker(
    'https://github.com/kissplugins/KISS-WP-admin-menu-useful-links',
    __FILE__,
    'kiss-wp-admin-menu-useful-links'
);
// Optional: Set the branch that contains the stable release.
$update_checker->setBranch( 'main' );

define( 'KWAMUL_VERSION', '1.6' );
define( 'KWAMUL_DB_VERSION_OPTION', 'kwamul_db_version' );
define( 'KWAMUL_OPTION_NAME', 'kwamul_links_option' );
define( 'KWAMUL_SETTINGS_GROUP', 'kwamul_settings_group' );
define( 'KWAMUL_SETTINGS_PAGE_SLUG', 'kwamul_settings_page' );
define( 'KWAMUL_MAX_LINKS', 5 );
define( 'KWAMUL_FRONTEND_OPTION_NAME', 'kwamul_front_links_option' );
define( 'KWAMUL_FRONTEND_SETTINGS_GROUP', 'kwamul_frontend_settings_group' );
define( 'KWAMUL_FRONTEND_SECTION_PAGE', 'kwamul_frontend_settings_page' );

/**
 * Sets default options on plugin activation.
 *
 * This function is called only once when the plugin is activated.
 * It pre-populates the first two links if no options exist yet.
 */
function kwamul_plugin_activate() {
        // Check if the option already exists. If not (false), set defaults.
       if ( false === get_option( KWAMUL_OPTION_NAME ) ) {
               $default_options = [
                       'link_1_label' => __( 'Posts', 'kiss-wp-admin-menu-useful-links' ),
                       'link_1_url'   => '/wp-admin/edit.php',
                       'link_1_priority' => 10,
                       'link_2_label' => __( 'Pages', 'kiss-wp-admin-menu-useful-links' ),
                       'link_2_url'   => '/wp-admin/edit.php?post_type=page',
                       'link_2_priority' => 20,
                       'link_3_label' => __( 'Media Library', 'kiss-wp-admin-menu-useful-links' ),
                       'link_3_url'   => '/wp-admin/upload.php',
                       'link_3_priority' => 30,
                       'link_4_label' => '',
                       'link_4_url'   => '',
                       'link_4_priority' => 40,
                       'link_5_label' => '',
                       'link_5_url'   => '',
                       'link_5_priority' => 50,
               ];
               update_option( KWAMUL_OPTION_NAME, $default_options );
       }

       if ( false === get_option( KWAMUL_FRONTEND_OPTION_NAME ) ) {
               $default_front_options = [
                       'link_1_label' => __( 'Blog', 'kiss-wp-admin-menu-useful-links' ),
                       'link_1_url'   => '/blog',
                       'link_1_priority' => 10,
                       'link_2_label' => '',
                       'link_2_url'   => '',
                       'link_2_priority' => 20,
                       'link_3_label' => '',
                       'link_3_url'   => '',
                       'link_3_priority' => 30,
                       'link_4_label' => '',
                       'link_4_url'   => '',
                       'link_4_priority' => 40,
                       'link_5_label' => '',
                       'link_5_url'   => '',
                       'link_5_priority' => 50,
               ];
               update_option( KWAMUL_FRONTEND_OPTION_NAME, $default_front_options );
       }
       update_option( KWAMUL_DB_VERSION_OPTION, KWAMUL_VERSION );
}
register_activation_hook( __FILE__, 'kwamul_plugin_activate' );

/**
 * Upgrade routine for the plugin.
 *
 * Checks if the plugin has been updated and runs a routine to update settings
 * if necessary. This adds the 'priority' field to links from older versions.
 */
function kwamul_upgrade_routine() {
    $current_db_version = get_option( KWAMUL_DB_VERSION_OPTION, '1.0' );

    if ( version_compare( $current_db_version, KWAMUL_VERSION, '<' ) ) {
        // Handle upgrade for backend links
        $backend_options = get_option( KWAMUL_OPTION_NAME, [] );
        if ( is_array( $backend_options ) ) {
            for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
                if ( ! isset( $backend_options[ "link_{$i}_priority" ] ) ) {
                    $backend_options[ "link_{$i}_priority" ] = 10;
                }
            }
            update_option( KWAMUL_OPTION_NAME, $backend_options );
        }

        // Handle upgrade for frontend links
        $frontend_options = get_option( KWAMUL_FRONTEND_OPTION_NAME, [] );
        if ( is_array( $frontend_options ) ) {
            for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
                if ( ! isset( $frontend_options[ "link_{$i}_priority" ] ) ) {
                    $frontend_options[ "link_{$i}_priority" ] = 10;
                }
            }
            update_option( KWAMUL_FRONTEND_OPTION_NAME, $frontend_options );
        }

        // Update the stored version to the current plugin version.
        update_option( KWAMUL_DB_VERSION_OPTION, KWAMUL_VERSION );
    }
}
add_action( 'plugins_loaded', 'kwamul_upgrade_routine' );


/**
 * Load plugin textdomain for internationalization.
 */
function kwamul_load_textdomain() {
	load_plugin_textdomain(
		'kiss-wp-admin-menu-useful-links',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'kwamul_load_textdomain' );

/**
 * Adds the plugin's settings page to the admin menu.
 */
function kwamul_add_admin_menu() {
	add_options_page(
		__( 'KISS Admin Useful Links', 'kiss-wp-admin-menu-useful-links' ),
		__( 'KISS Useful Links', 'kiss-wp-admin-menu-useful-links' ),
		'manage_options',
		KWAMUL_SETTINGS_PAGE_SLUG,
		'kwamul_options_page_html'
	);
}
add_action( 'admin_menu', 'kwamul_add_admin_menu' );

/**
 * Enqueues admin scripts and styles.
 */
function kwamul_admin_enqueue_scripts( $hook_suffix ) {
	if ( 'settings_page_' . KWAMUL_SETTINGS_PAGE_SLUG !== $hook_suffix ) {
		return;
	}

	wp_enqueue_script(
		'kwamul-admin-js',
		plugins_url( 'assets/admin.js', __FILE__ ),
		array(),
		KWAMUL_VERSION,
		true
	);

	// Add inline CSS for markdown content styling
	$custom_css = '
		.kwamul-markdown-content {
			max-width: 100%;
			margin-top: 20px;
		}
		.kwamul-markdown-content pre {
			max-height: 600px;
			overflow-y: auto;
			font-family: Consolas, Monaco, "Courier New", monospace;
			font-size: 13px;
			line-height: 1.4;
		}
		.kwamul-markdown-content .notice {
			margin: 20px 0;
		}
	';
	wp_add_inline_style( 'wp-admin', $custom_css );
}
add_action( 'admin_enqueue_scripts', 'kwamul_admin_enqueue_scripts' );

/**
 * Retrieves link options with transient caching.
 *
 * @param string $option_name Option key to retrieve.
 * @return array
 */
function kwamul_get_cached_options( string $option_name ): array {
    $transient_key = $option_name . '_transient';
    $options       = get_transient( $transient_key );

    if ( false === $options ) {
        $options = get_option( $option_name, [] );
        set_transient( $transient_key, $options, HOUR_IN_SECONDS );
    }

    return is_array( $options ) ? $options : [];
}

/**
 * Clears cached link options.
 */
function kwamul_clear_links_cache(): void {
    delete_transient( KWAMUL_OPTION_NAME . '_transient' );
    delete_transient( KWAMUL_FRONTEND_OPTION_NAME . '_transient' );
}
add_action( 'update_option_' . KWAMUL_OPTION_NAME, 'kwamul_clear_links_cache' );
add_action( 'update_option_' . KWAMUL_FRONTEND_OPTION_NAME, 'kwamul_clear_links_cache' );

/**
 * Registers plugin settings.
 */
function kwamul_register_settings(): void {
    register_setting( KWAMUL_SETTINGS_GROUP, KWAMUL_OPTION_NAME, 'kwamul_sanitize_links_options' );
    register_setting( KWAMUL_FRONTEND_SETTINGS_GROUP, KWAMUL_FRONTEND_OPTION_NAME, 'kwamul_sanitize_links_options' );
}

/**
 * Adds settings sections.
 */
function kwamul_add_settings_sections(): void {
    add_settings_section(
        'kwamul_main_section',
        __( 'Configure Custom Links', 'kiss-wp-admin-menu-useful-links' ),
        'kwamul_settings_section_callback',
        KWAMUL_SETTINGS_PAGE_SLUG
    );

    add_settings_section(
        'kwamul_frontend_section',
        __( 'Configure Frontend Links', 'kiss-wp-admin-menu-useful-links' ),
        'kwamul_frontend_settings_section_callback',
        KWAMUL_FRONTEND_SECTION_PAGE
    );
}

/**
 * Helper to generate link fields.
 */
function kwamul_add_link_fields( string $page, string $section, string $option_name, string $prefix = '', bool $frontend = false ): void {
    for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
        add_settings_field(
            "{$prefix}link_{$i}_label",
            sprintf( __( 'Link %d Label', 'kiss-wp-admin-menu-useful-links' ), $i ),
            'kwamul_render_text_field',
            $page,
            $section,
            [
                'option_name' => $option_name,
                'field_key'   => "link_{$i}_label",
                'label_for'   => "{$prefix}link_{$i}_label_id",
                'description' => sprintf( $frontend ? __( 'Enter the label for frontend link %d.', 'kiss-wp-admin-menu-useful-links' ) : __( 'Enter the label for custom link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
            ]
        );

        add_settings_field(
            "{$prefix}link_{$i}_url",
            sprintf( __( 'Link %d URL', 'kiss-wp-admin-menu-useful-links' ), $i ),
            'kwamul_render_url_field',
            $page,
            $section,
            [
                'option_name' => $option_name,
                'field_key'   => "link_{$i}_url",
                'label_for'   => "{$prefix}link_{$i}_url_id",
                'description' => sprintf( $frontend ? __( 'Enter the full URL for frontend link %d.', 'kiss-wp-admin-menu-useful-links' ) : __( 'Enter the full URL (e.g., https://example.com/page or /wp-admin/edit.php) for custom link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
            ]
        );

        add_settings_field(
            "{$prefix}link_{$i}_priority",
            sprintf( __( 'Link %d Priority', 'kiss-wp-admin-menu-useful-links' ), $i ),
            'kwamul_render_number_field',
            $page,
            $section,
            [
                'option_name' => $option_name,
                'field_key'   => "link_{$i}_priority",
                'label_for'   => "{$prefix}link_{$i}_priority_id",
                'description' => sprintf( __( 'Enter the priority for link %d (lower numbers appear higher).', 'kiss-wp-admin-menu-useful-links' ), $i ),
            ]
        );
    }
}

/**
 * Adds backend link fields.
 */
function kwamul_add_backend_fields(): void {
    kwamul_add_link_fields( KWAMUL_SETTINGS_PAGE_SLUG, 'kwamul_main_section', KWAMUL_OPTION_NAME, 'kwamul_' );
}

/**
 * Adds frontend link fields.
 */
function kwamul_add_frontend_fields(): void {
    kwamul_add_link_fields( KWAMUL_FRONTEND_SECTION_PAGE, 'kwamul_frontend_section', KWAMUL_FRONTEND_OPTION_NAME, 'kwamul_front_', true );
}

/**
 * Initializes plugin settings, sections, and fields.
 */
function kwamul_settings_init(): void {
    kwamul_register_settings();
    kwamul_add_settings_sections();
    kwamul_add_backend_fields();
    kwamul_add_frontend_fields();
}
add_action( 'admin_init', 'kwamul_settings_init' );

/**
 * Callback for the settings section.
 *
 * @param array $args Arguments passed to the callback.
 */
function kwamul_settings_section_callback( array $args ): void {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
		<?php esc_html_e( 'Define up to 5 custom labels and URLs to be added to the Site Name menu in the admin toolbar (front-end view).', 'kiss-wp-admin-menu-useful-links' ); ?>
	</p>
	<?php
}

function kwamul_frontend_settings_section_callback( array $args ): void {
       ?>
       <p id="<?php echo esc_attr( $args['id'] ); ?>">
               <?php esc_html_e( 'Define up to 5 front-end page links to be added under the Visit Site menu when viewing the admin dashboard.', 'kiss-wp-admin-menu-useful-links' ); ?>
       </p>
       <?php
}

/**
 * Renders a text input field for settings.
 *
 * @param array $args Arguments for the field.
 */
function kwamul_render_text_field( array $args ): void {
	$options     = get_option( $args['option_name'], [] ); // Default to empty array if option not found
	$field_key   = $args['field_key'];
	$value       = isset( $options[ $field_key ] ) ? $options[ $field_key ] : '';
	$description = isset( $args['description'] ) ? $args['description'] : '';
	?>
	<input type="text"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   name="<?php echo esc_attr( $args['option_name'] . '[' . $field_key . ']' ); ?>"
		   value="<?php echo esc_attr( $value ); ?>"
		   class="regular-text">
	<?php if ( ! empty( $description ) ) : ?>
		<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Renders a URL input field for settings.
 *
 * @param array $args Arguments for the field.
 */
// NOTE TO FUTURE MAINTAINERS: These URL fields intentionally use a plain
// text input and sanitize_text_field() to allow relative paths. Do NOT
// refactor this to use <input type="url"> or esc_url_raw unless explicitly
// requested.
function kwamul_render_url_field( array $args ): void {
        $options     = get_option( $args['option_name'], [] ); // Default to empty array
        $field_key   = $args['field_key'];
        $value       = isset( $options[ $field_key ] ) ? $options[ $field_key ] : '';
        $description = isset( $args['description'] ) ? $args['description'] : '';
        ?>
        <input type="text"
                   id="<?php echo esc_attr( $args['label_for'] ); ?>"
                   name="<?php echo esc_attr( $args['option_name'] . '[' . $field_key . ']' ); ?>"
                   value="<?php echo esc_attr( $value ); ?>"
                   class="regular-text"
                   placeholder="e.g., /wp-admin/edit.php or https://example.com">
        <a class="button" href="<?php echo esc_url( $value ); ?>" target="_blank" style="margin-left:5px;"><?php esc_html_e( 'Test Link', 'kiss-wp-admin-menu-useful-links' ); ?></a>
        <?php if ( ! empty( $description ) ) : ?>
                <p class="description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>
        <?php
}

/**
 * Renders a number input field for settings.
 *
 * @param array $args Arguments for the field.
 */
function kwamul_render_number_field( array $args ): void {
    $options     = get_option( $args['option_name'], [] ); // Default to empty array
    $field_key   = $args['field_key'];
    $value       = isset( $options[ $field_key ] ) ? $options[ $field_key ] : '';
    $description = isset( $args['description'] ) ? $args['description'] : '';
    ?>
    <input type="number"
           id="<?php echo esc_attr( $args['label_for'] ); ?>"
           name="<?php echo esc_attr( $args['option_name'] . '[' . $field_key . ']' ); ?>"
           value="<?php echo esc_attr( $value ); ?>"
           class="small-text">
    <?php if ( ! empty( $description ) ) : ?>
        <p class="description"><?php echo esc_html( $description ); ?></p>
    <?php endif; ?>
    <?php
}

/**
 * Validates and sanitizes URLs while supporting relative paths.
 * Prevents XSS via javascript:, data:, and other dangerous protocols.
 * Supports URL fragments (anchors) with # character.
 *
 * @param string $url The URL to validate
 * @return string Sanitized URL or empty string if invalid
 */
function kwamul_validate_url( $url ) {
    // First, sanitize the input
    $url = sanitize_text_field( trim( $url ) );

    // Allow empty URLs
    if ( empty( $url ) ) {
        return '';
    }

    // Define allowed protocols (no javascript:, data:, vbscript:, etc.)
    $allowed_protocols = array( 'http', 'https' );

    // Check if it's a relative path (starts with /)
    if ( strpos( $url, '/' ) === 0 ) {
        // Relative path - validate it doesn't contain dangerous patterns

        // Block any attempts to use protocols in relative paths
        if ( preg_match( '/^\/[a-zA-Z][a-zA-Z0-9+.-]*:/', $url ) ) {
            return ''; // Reject URLs like "/javascript:alert(1)"
        }

        // Additional security: block common XSS patterns in paths
        $dangerous_patterns = array(
            '/javascript:/i',
            '/data:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/<script/i',
            '/eval\(/i'
        );

        foreach ( $dangerous_patterns as $pattern ) {
            if ( preg_match( $pattern, $url ) ) {
                return '';
            }
        }

        // Validate that it's a reasonable path structure (including fragments with #)
        if ( ! preg_match( '/^\/[a-zA-Z0-9\/_.-]*(\?[a-zA-Z0-9&=_.-]*)?(#[a-zA-Z0-9_-]*)?$/', $url ) ) {
            return '';
        }

        return $url;
    }

    // For absolute URLs, use WordPress's built-in validation
    $validated_url = esc_url_raw( $url, $allowed_protocols );

    // Double-check that esc_url_raw didn't strip the protocol due to our restrictions
    if ( empty( $validated_url ) && ! empty( $url ) ) {
        // If esc_url_raw returned empty but we had a URL, it was likely rejected
        return '';
    }

    // Additional check: ensure the validated URL actually matches expected patterns
    if ( ! preg_match( '/^https?:\/\/[a-zA-Z0-9.-]+/', $validated_url ) ) {
        return '';
    }

    return $validated_url;
}

/**
 * Sanitizes the link options before saving.
 *
 * @param array $input Raw input from the settings form.
 * @return array Sanitized options.
 */
function kwamul_sanitize_links_options( array $input ): array {
    // Verify nonce for security - CRITICAL SECURITY FIX
    if ( ! wp_verify_nonce( $_POST['kwamul_nonce'] ?? '', 'kwamul_settings_nonce' ) ) {
        wp_die( __( 'Security check failed. Please try again.', 'kiss-wp-admin-menu-useful-links' ) );
    }

    $sanitized_input = [];
	if ( is_array( $input ) ) {
       for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
               $label_key = "link_{$i}_label";
               $url_key   = "link_{$i}_url";
               $priority_key = "link_{$i}_priority";

			if ( isset( $input[ $label_key ] ) ) {
				$sanitized_input[ $label_key ] = sanitize_text_field( $input[ $label_key ] );
			} else {
				$sanitized_input[ $label_key ] = ''; // Ensure key exists
			}

            // Use secure URL validation while maintaining relative path support
            if ( isset( $input[ $url_key ] ) ) {
                $sanitized_input[ $url_key ] = kwamul_validate_url( $input[ $url_key ] );
            } else {
                $sanitized_input[ $url_key ] = ''; // Ensure key exists
            }

            if ( isset( $input[ $priority_key ] ) ) {
                $sanitized_input[ $priority_key ] = absint( $input[ $priority_key ] );
            } else {
                $sanitized_input[ $priority_key ] = 10; // Default priority
            }
		}
	}
	return $sanitized_input;
}

/**
 * Renders markdown files using the KISS Markdown Viewer plugin or fallback.
 *
 * @param string $filename The markdown filename to render.
 * @return string The rendered HTML content.
 */
function kwamul_render_markdown_file( $filename ) {
    // Security: Only allow specific markdown files and sanitize filename
    $allowed_files = array( 'README.md', 'CHANGELOG.md' );
    if ( ! in_array( $filename, $allowed_files, true ) ) {
        return '<div class="notice notice-error"><p>' . esc_html__( 'Access to this file is not allowed.', 'kiss-wp-admin-menu-useful-links' ) . '</p></div>';
    }

    $markdown_file = plugin_dir_path( __FILE__ ) . sanitize_file_name( $filename );

    // Check if KISS Markdown Viewer plugin function exists
    if ( function_exists( 'kiss_mdv_render_file' ) ) {
        $html = kiss_mdv_render_file( $markdown_file );
    } else {
        // Fallback to plain text rendering
        if ( file_exists( $markdown_file ) ) {
            $content = file_get_contents( $markdown_file );
            $html    = '<pre style="white-space: pre-wrap; word-wrap: break-word; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">' . esc_html( $content ) . '</pre>';
        } else {
            $html = '<div class="notice notice-error"><p>' . sprintf(
                /* translators: %s: filename */
                esc_html__( 'Markdown file "%s" not found.', 'kiss-wp-admin-menu-useful-links' ),
                esc_html( $filename )
            ) . '</p></div>';
        }
    }

    return $html;
}

/**
 * Renders the HTML for the options page.
 */
function kwamul_options_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
                return;
        }

        // Verify nonce for form submissions
        if ( isset( $_POST['kwamul_submit'] ) && ! wp_verify_nonce( $_POST['kwamul_nonce'], 'kwamul_settings_nonce' ) ) {
                wp_die( __( 'Security check failed. Please try again.', 'kiss-wp-admin-menu-useful-links' ) );
        }

        // Determine current tab - expanded to include new tabs
        $allowed_tabs = array( 'backend', 'frontend', 'readme', 'changelog' );
        $current_tab = sanitize_text_field( $_GET['tab'] ?? '' );
        if ( ! in_array( $current_tab, $allowed_tabs, true ) ) {
            $current_tab = 'backend'; // Default tab
        }
        ?>
        <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() . ' v' . KWAMUL_VERSION ); ?></h1>

                <?php if ( in_array( $current_tab, array( 'backend', 'frontend' ), true ) ) : ?>
                    <p><?php esc_html_e( "Use the fields below to define your custom links. You can control the order of the links using the 'Priority' field. A lower number (e.g., 10) will place a link higher in the menu, while a higher number (e.g., 100) will place it lower.", 'kiss-wp-admin-menu-useful-links' ); ?></p>
                <?php endif; ?>

               <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'backend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'backend' === $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="backend">
                                <?php esc_html_e( 'Menu in Front End', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'frontend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'frontend' === $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="frontend">
                                <?php esc_html_e( 'Menu in Admin', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'readme', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'readme' === $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="readme">
                                <?php esc_html_e( 'Read Me', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'changelog', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'changelog' === $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="changelog">
                                <?php esc_html_e( 'Changelog', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                </h2>

                <?php if ( in_array( $current_tab, array( 'backend', 'frontend' ), true ) ) : ?>
                    <form id="kwamul-options-form" action="options.php" method="post">
                            <?php
                            wp_nonce_field( 'kwamul_settings_nonce', 'kwamul_nonce' );

                            if ( 'frontend' === $current_tab ) {
                                    settings_fields( KWAMUL_FRONTEND_SETTINGS_GROUP );
                                    do_settings_sections( KWAMUL_FRONTEND_SECTION_PAGE );
                            } else {
                                    settings_fields( KWAMUL_SETTINGS_GROUP );
                                    do_settings_sections( KWAMUL_SETTINGS_PAGE_SLUG );
                            }
                            // Use a custom name/id so form.submit() remains callable.
                            submit_button( __( 'Save Links', 'kiss-wp-admin-menu-useful-links' ), 'primary', 'kwamul_submit' );
                            ?>
                    </form>
                <?php elseif ( 'readme' === $current_tab ) : ?>
                    <div class="kwamul-markdown-content">
                        <?php echo kwamul_render_markdown_file( 'README.md' ); ?>
                    </div>
                <?php elseif ( 'changelog' === $current_tab ) : ?>
                    <div class="kwamul-markdown-content">
                        <?php echo kwamul_render_markdown_file( 'CHANGELOG.md' ); ?>
                    </div>
                <?php endif; ?>
        </div>
        <?php
}

/**
 * Adds custom links to the WordPress admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
 */
function kwamul_add_custom_admin_bar_links( WP_Admin_Bar $wp_admin_bar ): void {
    if ( ! is_admin_bar_showing() ) {
        return;
    }

    $options = is_admin() ? kwamul_get_cached_options( KWAMUL_FRONTEND_OPTION_NAME ) : kwamul_get_cached_options( KWAMUL_OPTION_NAME );
    $site_name_node = $wp_admin_bar->get_node('site-name');

    // Ensure the 'site-name' node exists before trying to add children to it.
    if ( ! $site_name_node ) {
        return;
    }

    $links = [];
    for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
        $label_key = "link_{$i}_label";
        $url_key   = "link_{$i}_url";
        $priority_key = "link_{$i}_priority";

        $label = isset( $options[ $label_key ] ) ? trim( $options[ $label_key ] ) : '';
        $url   = isset( $options[ $url_key ] ) ? trim( $options[ $url_key ] ) : '';
        $priority = isset( $options[ $priority_key ] ) ? absint( $options[ $priority_key ] ) : 10;

        if ( ! empty( $label ) && ! empty( $url ) ) {
            $links[] = [
                'id'       => "kwamul-custom-link-{$i}",
                'title'    => $label,
                'href'     => $url,
                'priority' => $priority,
            ];
        }
    }

    // Sort links by priority
    usort($links, function($a, $b) {
        return $a['priority'] - $b['priority'];
    });

    foreach ($links as $link) {
        $wp_admin_bar->add_node(
            [
                'parent' => 'site-name',
                'id'     => $link['id'],
                'title'  => esc_html( $link['title'] ),
                'href'   => esc_url( $link['href'] ),
                'meta'   => [
                    'class' => "kwamul-custom-link {$link['id']}",
                ],
            ]
        );
    }
}
add_action( 'admin_bar_menu', 'kwamul_add_custom_admin_bar_links', 999 );

?>
