<?php
/*
Plugin Name: Social Links Manager
Description: Plugin to manage social media links and contact details via settings menu.
Version: 1.0.1
Author: Steven McCurrach
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register settings and admin menu
add_action( 'admin_menu', 'social_links_manager_add_admin_menu' );
add_action( 'admin_init', 'social_links_manager_register_settings' );

function social_links_manager_add_admin_menu() {
    // Place under Settings with desired label "Contact Details".
    add_options_page(
        'Contact Details',       // Page title
        'Contact Details',       // Menu title (under Settings)
        'manage_options',        // Capability
        'social-links-manager',  // Menu slug
        'social_links_manager_admin_page' // Callback
    );
}

function social_links_manager_register_settings() {
    register_setting( 'social_links_manager_options_group', 'social_links_manager_links', 'social_links_manager_sanitize_links' );
    register_setting( 'social_links_manager_options_group', 'social_links_manager_contact', 'social_links_manager_sanitize_contact' );
}

function social_links_manager_sanitize_links( $input ) {
    return array_map( 'esc_url_raw', (array) $input );
}

function social_links_manager_sanitize_contact( $input ) {
    $output = array();
    if ( isset( $input['address'] ) ) {
        $address = sanitize_textarea_field( (string) $input['address'] );
        if ( $address !== '' ) {
            $output['address'] = $address;
        }
    }
    if ( isset( $input['phone'] ) ) {
        $phone = wp_strip_all_tags( (string) $input['phone'] );
        // Allow digits, spaces, plus, parentheses, and hyphens only.
        $phone = preg_replace( '/[^0-9\s\+\-\(\)]/', '', $phone );
        $output['phone'] = $phone;
    }
    if ( isset( $input['email'] ) ) {
        $email = sanitize_email( (string) $input['email'] );
        if ( $email ) {
            $output['email'] = $email;
        }
    }
    return $output;
}

// Render admin page
function social_links_manager_admin_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Contact Details', 'social-links-manager' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'social_links_manager_options_group' );
            $links = get_option( 'social_links_manager_links', array(
                'facebook'  => '',
                'twitter'   => '',
                'instagram' => '',
                'linkedin'  => '',
                'whatsapp'  => '',
                'snapchat'  => '',
                'tiktok'    => '',
                'youtube'   => '',
            ) );
            $contact = get_option( 'social_links_manager_contact', array( 'address' => '', 'phone' => '', 'email' => '' ) );
            ?>
            <h2 style="margin-top:1.5em;"><?php esc_html_e( 'Contact Details', 'social-links-manager' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Address', 'social-links-manager' ); ?></th>
                    <td><textarea name="social_links_manager_contact[address]" class="large-text" rows="4" placeholder="123 High Street&#10;Anytown&#10;AB12 3CD"><?php echo esc_textarea( $contact['address'] ?? '' ); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Phone number', 'social-links-manager' ); ?></th>
                    <td><input type="text" name="social_links_manager_contact[phone]" value="<?php echo esc_attr( $contact['phone'] ?? '' ); ?>" class="regular-text" placeholder="+44 20 1234 5678"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Email address', 'social-links-manager' ); ?></th>
                    <td><input type="email" name="social_links_manager_contact[email]" value="<?php echo esc_attr( $contact['email'] ?? '' ); ?>" class="regular-text" placeholder="hello@example.com"></td>
                </tr>
            </table>

            <h2 style="margin-top:2em;"><?php esc_html_e( 'Social Profiles', 'social-links-manager' ); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Facebook URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[facebook]" value="<?php echo esc_attr( $links['facebook'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Twitter URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[twitter]" value="<?php echo esc_attr( $links['twitter'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Instagram URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[instagram]" value="<?php echo esc_attr( $links['instagram'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'LinkedIn URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[linkedin]" value="<?php echo esc_attr( $links['linkedin'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'WhatsApp URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[whatsapp]" value="<?php echo esc_attr( $links['whatsapp'] ?? '' ); ?>" class="regular-text" placeholder="https://wa.me/1234567890"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Snapchat URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[snapchat]" value="<?php echo esc_attr( $links['snapchat'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'TikTok URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[tiktok]" value="<?php echo esc_attr( $links['tiktok'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'YouTube URL', 'social-links-manager' ); ?></th>
                    <td><input type="url" name="social_links_manager_links[youtube]" value="<?php echo esc_attr( $links['youtube'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Shortcode to display social links
function social_links_manager_shortcode() {
    return social_links_manager_render_links();
}

/**
 * Shared renderer for shortcode and block.
 */
function social_links_manager_render_links( $attributes = array() ) {
    $links = get_option( 'social_links_manager_links', array(
        'facebook'  => '',
        'twitter'   => '',
        'instagram' => '',
        'linkedin'  => '',
        'whatsapp'  => '',
        'snapchat'  => '',
        'tiktok'    => '',
        'youtube'   => '',
    ) );

    $order = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'whatsapp', 'snapchat', 'tiktok', 'youtube' );

    // Ensure frontend styles are loaded for both shortcode and block
    wp_enqueue_style( 'social-links-manager' );


    $classes = array( 'social-links' );
    if ( isset( $attributes['align'] ) && in_array( $attributes['align'], array( 'left', 'center', 'right' ), true ) ) {
        $classes[] = 'align' . $attributes['align'];
    }
    // Appearance toggle: brand colors vs monochrome
    if ( isset( $attributes['brandColors'] ) && ! $attributes['brandColors'] ) {
        $classes[] = 'is-monochrome';
    }

    $output = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

    foreach ( $order as $platform ) {
        // Check block attributes for visibility toggles
        $attr_key = 'show' . ucfirst( $platform ); // e.g., showFacebook
        if ( array_key_exists( $attr_key, $attributes ) && ! $attributes[ $attr_key ] ) {
            continue;
        }

        $url = $links[ $platform ] ?? '';
        if ( ! empty( $url ) ) {
            $label = ucfirst( $platform );
            $icon  = social_links_manager_icon_svg( $platform );
            $output .= sprintf(
                '<a class="social-link social-link--%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s">%4$s<span class="screen-reader-text">%3$s</span></a>',
                esc_attr( $platform ),
                esc_url( $url ),
                esc_attr( $label ),
                $icon
            );
        }
    }
    $output .= '</div>';

    return $output;
}

/**
 * Return an icon element using Font Awesome Brands.
 * Icons inherit currentColor; theme enqueues FA CSS.
 */
function social_links_manager_icon_svg( $platform ) {
    switch ( $platform ) {
        case 'facebook':
            return '<i class="social-link__icon fa-brands fa-facebook-f" aria-hidden="true"></i>';
        case 'twitter':
            return '<i class="social-link__icon fa-brands fa-twitter" aria-hidden="true"></i>';
        case 'instagram':
            return '<i class="social-link__icon fa-brands fa-instagram" aria-hidden="true"></i>';
        case 'linkedin':
            return '<i class="social-link__icon fa-brands fa-linkedin-in" aria-hidden="true"></i>';
        case 'whatsapp':
            return '<i class="social-link__icon fa-brands fa-whatsapp" aria-hidden="true"></i>';
        case 'snapchat':
            return '<i class="social-link__icon fa-brands fa-snapchat" aria-hidden="true"></i>';
        case 'tiktok':
            return '<i class="social-link__icon fa-brands fa-tiktok" aria-hidden="true"></i>';
        case 'youtube':
            return '<i class="social-link__icon fa-brands fa-youtube" aria-hidden="true"></i>';
        default:
            return '<i class="social-link__icon fa-solid fa-link" aria-hidden="true"></i>';
    }
}

// Register frontend stylesheet for the shortcode
add_action( 'wp_enqueue_scripts', function() {
    wp_register_style(
        'social-links-manager',
        plugins_url( 'assets/social-links.css', __FILE__ ),
        array(),
        '1.0.0'
    );
} );

// Register the Gutenberg block (server-rendered)
add_action( 'init', function() {
    // Editor script for the block (no build step required)
    wp_register_script(
        'slm-block-social-links',
        plugins_url( 'blocks/social-links/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
        '1.0.0',
        true
    );

    // Register block type from metadata with shared render callback
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type( __DIR__ . '/blocks/social-links', array(
            'render_callback' => 'social_links_manager_render_links',
        ) );
    }
} );
// Register the Gutenberg Contact Info block (server-rendered)
add_action( 'init', function () {

    wp_register_script(
        'slm-block-contact-info',
        plugins_url( 'blocks/contact-info/index.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-server-side-render' ), // <-- the important one
        filemtime( plugin_dir_path( __FILE__ ) . 'blocks/contact-info/index.js' ),
        true
    );

    register_block_type(
        __DIR__ . '/blocks/contact-info',
        array(
            'render_callback' => 'slm_contact_info_shortcode', // must return a string
        )
    );
} );


add_shortcode( 'social_links', 'social_links_manager_shortcode' );

// Shortcode to display contact info (phone/email/address)
function slm_contact_info_shortcode() {
    $contact = get_option( 'social_links_manager_contact', array( 'address' => '', 'phone' => '', 'email' => '' ) );
    $address = isset( $contact['address'] ) ? trim( (string) $contact['address'] ) : '';
    $phone = isset( $contact['phone'] ) ? trim( (string) $contact['phone'] ) : '';
    $email = isset( $contact['email'] ) ? trim( (string) $contact['email'] ) : '';

    if (  $address === '' && $phone === '' && $email === '' ) {
        return '';
    }

    $out = '<div class="slm-contact">';
    if ( $address !== '' ) {
        $out .= sprintf(
            '<div class="slm-contact__item slm-contact__item--address">%s</div>',
            nl2br( esc_html( $address ) )
        );
    }
    if ( $phone !== '' ) {
        // Build tel link by stripping spaces and parentheses
        $tel = preg_replace( '/[\s\(\)]/', '', $phone );
        $out .= sprintf(
            '<a class="slm-contact__item slm-contact__item--phone" href="tel:%1$s">%2$s</a>',
            esc_attr( $tel ),
            esc_html( $phone )
        );
    }
    if ( $email !== '' ) {
        $out .= sprintf(
            '<a class="slm-contact__item slm-contact__item--email" href="mailto:%1$s">%1$s</a>',
            esc_html( $email )
        );
    }
    $out .= '</div>';
    return $out;
}
add_shortcode( 'slm_contact_info', 'slm_contact_info_shortcode' );



?>

