<?php
/**
 * Plugin Name:       KISS WP admin menu useful links
 * Plugin URI:        https://example.com/kiss-wp-admin-menu-useful-links
 * Description:       Adds custom user-defined links to the bottom of the Site Name menu in the WP admin toolbar on the front end.
 * Version:           1.1
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
                        'link_2_label' => __( 'Pages', 'kiss-wp-admin-menu-useful-links' ),
                        'link_2_url'   => '/wp-admin/edit.php?post_type=page',
                        'link_3_label' => __( 'Media Library', 'kiss-wp-admin-menu-useful-links' ),
                        'link_3_url'   => '/wp-admin/upload.php',
                        'link_4_label' => __( 'Blog', 'kiss-wp-admin-menu-useful-links' ),
                        'link_4_url'   => '/blog',
                        'link_5_label' => '',
                        'link_5_url'   => '',
                ];
                update_option( KWAMUL_OPTION_NAME, $default_options );
        }

       if ( false === get_option( KWAMUL_FRONTEND_OPTION_NAME ) ) {
               $default_front_options = [
                       'link_1_label' => '',
                       'link_1_url'   => '',
                       'link_2_label' => '',
                       'link_2_url'   => '',
                       'link_3_label' => '',
                       'link_3_url'   => '',
                       'link_4_label' => '',
                       'link_4_url'   => '',
                       'link_5_label' => '',
                       'link_5_url'   => '',
               ];
               update_option( KWAMUL_FRONTEND_OPTION_NAME, $default_front_options );
       }
}
register_activation_hook( __FILE__, 'kwamul_plugin_activate' );


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
 * Initializes plugin settings, sections, and fields.
 */
function kwamul_settings_init() {
       register_setting( KWAMUL_SETTINGS_GROUP, KWAMUL_OPTION_NAME, 'kwamul_sanitize_links_options' );
       register_setting( KWAMUL_FRONTEND_SETTINGS_GROUP, KWAMUL_FRONTEND_OPTION_NAME, 'kwamul_sanitize_links_options' );

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

       for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
               add_settings_field(
                       "kwamul_link_{$i}_label",
                       sprintf( __( 'Link %d Label', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       'kwamul_render_text_field',
                       KWAMUL_SETTINGS_PAGE_SLUG,
                       'kwamul_main_section',
                       [
                               'option_name' => KWAMUL_OPTION_NAME,
                               'field_key'   => "link_{$i}_label",
                               'label_for'   => "kwamul_link_{$i}_label_id",
                               'description' => sprintf( __( 'Enter the label for custom link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       ]
               );

               add_settings_field(
                       "kwamul_link_{$i}_url",
                       sprintf( __( 'Link %d URL', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       'kwamul_render_url_field',
                       KWAMUL_SETTINGS_PAGE_SLUG,
                       'kwamul_main_section',
                       [
                               'option_name' => KWAMUL_OPTION_NAME,
                               'field_key'   => "link_{$i}_url",
                               'label_for'   => "kwamul_link_{$i}_url_id",
                               'description' => sprintf( __( 'Enter the full URL (e.g., https://example.com/page or /wp-admin/edit.php) for custom link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       ]
               );

               add_settings_field(
                       "kwamul_front_link_{$i}_label",
                       sprintf( __( 'Link %d Label', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       'kwamul_render_text_field',
                       KWAMUL_FRONTEND_SECTION_PAGE,
                       'kwamul_frontend_section',
                       [
                               'option_name' => KWAMUL_FRONTEND_OPTION_NAME,
                               'field_key'   => "link_{$i}_label",
                               'label_for'   => "kwamul_front_link_{$i}_label_id",
                               'description' => sprintf( __( 'Enter the label for frontend link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       ]
               );

               add_settings_field(
                       "kwamul_front_link_{$i}_url",
                       sprintf( __( 'Link %d URL', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       'kwamul_render_url_field',
                       KWAMUL_FRONTEND_SECTION_PAGE,
                       'kwamul_frontend_section',
                       [
                               'option_name' => KWAMUL_FRONTEND_OPTION_NAME,
                               'field_key'   => "link_{$i}_url",
                               'label_for'   => "kwamul_front_link_{$i}_url_id",
                               'description' => sprintf( __( 'Enter the full URL for frontend link %d.', 'kiss-wp-admin-menu-useful-links' ), $i ),
                       ]
               );
       }
}
add_action( 'admin_init', 'kwamul_settings_init' );

/**
 * Callback for the settings section.
 *
 * @param array $args Arguments passed to the callback.
 */
function kwamul_settings_section_callback( $args ) {
	?>
	<p id="<?php echo esc_attr( $args['id'] ); ?>">
		<?php esc_html_e( 'Define up to 5 custom labels and URLs to be added to the Site Name menu in the admin toolbar (front-end view).', 'kiss-wp-admin-menu-useful-links' ); ?>
	</p>
	<?php
}

function kwamul_frontend_settings_section_callback( $args ) {
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
function kwamul_render_text_field( $args ) {
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
function kwamul_render_url_field( $args ) {
	$options     = get_option( $args['option_name'], [] ); // Default to empty array
	$field_key   = $args['field_key'];
	$value       = isset( $options[ $field_key ] ) ? $options[ $field_key ] : '';
	$description = isset( $args['description'] ) ? $args['description'] : '';
	?>
	<input type="url"
		   id="<?php echo esc_attr( $args['label_for'] ); ?>"
		   name="<?php echo esc_attr( $args['option_name'] . '[' . $field_key . ']' ); ?>"
		   value="<?php echo esc_attr( $value ); ?>"
		   class="regular-text"
		   placeholder="e.g., /wp-admin/edit.php or https://example.com">
	<?php if ( ! empty( $description ) ) : ?>
		<p class="description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Sanitizes the link options before saving.
 *
 * @param array $input The input array from the settings form.
 * @return array The sanitized array.
 */
function kwamul_sanitize_links_options( $input ) {
	$sanitized_input = [];
	if ( is_array( $input ) ) {
       for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
               $label_key = "link_{$i}_label";
               $url_key   = "link_{$i}_url";

			if ( isset( $input[ $label_key ] ) ) {
				$sanitized_input[ $label_key ] = sanitize_text_field( $input[ $label_key ] );
			} else {
				$sanitized_input[ $label_key ] = ''; // Ensure key exists
			}

			if ( isset( $input[ $url_key ] ) ) {
				$sanitized_input[ $url_key ] = esc_url_raw( trim( $input[ $url_key ] ) );
			} else {
				$sanitized_input[ $url_key ] = ''; // Ensure key exists
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
                <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'backend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab <?php echo 'frontend' !== $current_tab ? 'nav-tab-active' : ''; ?>">
                                <?php esc_html_e( 'Backend Links', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                        <a href="<?php echo esc_url( add_query_arg( 'tab', 'frontend', menu_page_url( KWAMUL_SETTINGS_PAGE_SLUG, false ) ) ); ?>" class="nav-tab <?php echo 'frontend' === $current_tab ? 'nav-tab-active' : ''; ?>">
                                <?php esc_html_e( 'Frontend Links', 'kiss-wp-admin-menu-useful-links' ); ?>
                        </a>
                </h2>
                <form action="options.php" method="post">
                        <?php
                        if ( 'frontend' === $current_tab ) {
                                settings_fields( KWAMUL_FRONTEND_SETTINGS_GROUP );
                                do_settings_sections( KWAMUL_FRONTEND_SECTION_PAGE );
                        } else {
                                settings_fields( KWAMUL_SETTINGS_GROUP );
                                do_settings_sections( KWAMUL_SETTINGS_PAGE_SLUG );
                        }
                        submit_button( __( 'Save Links', 'kiss-wp-admin-menu-useful-links' ) );
                        ?>
                </form>
        </div>
        <?php
}

/**
 * Adds custom links to the WordPress admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
 */
function kwamul_add_custom_admin_bar_links( $wp_admin_bar ) {
       if ( ! is_admin_bar_showing() ) {
               return;
       }

       $options = is_admin() ? get_option( KWAMUL_FRONTEND_OPTION_NAME, [] ) : get_option( KWAMUL_OPTION_NAME, [] );
       $site_name_node = $wp_admin_bar->get_node('site-name');

	// Ensure the 'site-name' node exists before trying to add children to it.
	if ( ! $site_name_node ) {
		return;
	}

	for ( $i = 1; $i <= KWAMUL_MAX_LINKS; $i++ ) {
		$label_key = "link_{$i}_label";
		$url_key   = "link_{$i}_url";

		$label = isset( $options[ $label_key ] ) ? trim( $options[ $label_key ] ) : '';
		$url   = isset( $options[ $url_key ] ) ? trim( $options[ $url_key ] ) : '';

		if ( ! empty( $label ) && ! empty( $url ) ) {
			$final_url = $url;
            // If URL starts with /wp-admin/ or is just a path segment like 'edit.php'
            // it's likely an admin URL. For consistency, let admin_url() resolve it.
            // However, users might want to link to /wp-login.php?action=logout or other relative paths.
            // Using esc_url() handles both absolute and relative paths well for href.
            // If the provided URL is relative like "/wp-admin/edit.php", esc_url will make it site_url() + path.
            // If it's a full URL, it will be used as is after sanitization.

                       $wp_admin_bar->add_node(
                               [
                                       'parent' => 'site-name',
                                       'id'     => "kwamul-custom-link-{$i}",
                                       'title'  => esc_html( $label ),
                                       'href'   => esc_url( $final_url ),
                                       'meta'   => [
                                               'class' => "kwamul-custom-link kwamul-link-{$i}",
                                       ],
                               ]
                       );
               }
       }
}
add_action( 'admin_bar_menu', 'kwamul_add_custom_admin_bar_links', 999 );

?>
