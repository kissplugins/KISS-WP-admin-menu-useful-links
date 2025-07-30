<?php
/**
 * Plugin Name:       KISS WP admin menu useful links
 * Plugin URI:        https://example.com/kiss-wp-admin-menu-useful-links
 * Description:       Adds custom user-defined links to the bottom of the Site Name menu in the WP admin toolbar on the front end.
 * Version:           1.4
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

define( 'KWAMUL_VERSION', '1.4' );
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
 * Sanitizes the link options before saving.
 *
 * @param array $input Raw input from the settings form.
 * @return array Sanitized options.
 */
function kwamul_sanitize_links_options( array $input ): array {

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

                       if ( isset( $input[ $url_key ] ) ) {
                               // NOTE: Using sanitize_text_field to allow relative paths. Do not refactor.
                               $sanitized_input[ $url_key ] = sanitize_text_field( $input[ $url_key ] );
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
 * Renders the HTML for the options page.
 */
function kwamul_options_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) {
                return;
        }
       $current_tab = ( isset( $_GET['tab'] ) && 'frontend' === $_GET['tab'] ) ? 'frontend' : 'backend';
        ?>
        <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <p><?php esc_html_e( "Use the fields below to define your custom links. You can control the order of the links using the 'Priority' field. A lower number (e.g., 10) will place a link higher in the menu, while a higher number (e.g., 100) will place it lower.", 'kiss-wp-admin-menu-useful-links' ); ?></p>
               <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'backend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'frontend' !== $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="backend">
                                <?php esc_html_e( 'Menu in Front End', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'frontend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab kwamul-tab <?php echo 'frontend' === $current_tab ? 'nav-tab-active' : ''; ?>" data-tab="frontend">
                                <?php esc_html_e( 'Menu in Admin', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                </h2>
                <form id="kwamul-options-form" action="options.php" method="post">
                        <?php
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
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                        function safeSet(key, value) {
                                try { if (window.localStorage) { localStorage.setItem(key, value); } } catch (e) {}
                        }
                        function safeGet(key) {
                                try { return window.localStorage ? localStorage.getItem(key) : null; } catch (e) { return null; }
                        }
                        function safeRemove(key) {
                                try { if (window.localStorage) { localStorage.removeItem(key); } } catch (e) {}
                        }

                        var tabs = document.querySelectorAll('.kwamul-tab');
                        var form = document.getElementById('kwamul-options-form');
                        var submitBtn = document.getElementById('kwamul_submit');

                        if (submitBtn) {
                                submitBtn.addEventListener('click', function() {
                                        safeSet('kwamul_last_save', Date.now().toString());
                                });
                        }

                        function changeTab(target) {
                                var url = new URL(window.location.href);
                                if (url.searchParams.get('tab') !== target) {
                                        url.searchParams.set('tab', target);
                                        window.location.href = url.toString();
                                }
                        }

                        tabs.forEach(function(tab) {
                                tab.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        var nextTab = this.getAttribute('data-tab');
                                        safeSet('kwamul_next_tab', nextTab);

                                        var lastSave = parseInt(safeGet('kwamul_last_save') || '0', 10);
                                        var now = Date.now();

                                        if (!form || now - lastSave < 5000) {
                                                changeTab(nextTab);
                                        } else {
                                                safeSet('kwamul_last_save', now.toString());
                                                form.submit();
                                        }
                                });
                        });

                        var nextTab = safeGet('kwamul_next_tab');
                        if (nextTab) {
                                safeRemove('kwamul_next_tab');
                                changeTab(nextTab);
                        }
                });
                </script>
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