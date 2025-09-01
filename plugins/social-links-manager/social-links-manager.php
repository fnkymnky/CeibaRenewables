<?php
/*
Plugin Name: Social Links Manager
Description: A plugin to manage social links and display them via a shortcode. Links left empty will not display on the frontend.
Version: 1.0.1
Author: Steven McCurrach
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: social-links-manager
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register settings and admin menu
add_action( 'admin_menu', 'social_links_manager_add_admin_menu' );
add_action( 'admin_init', 'social_links_manager_register_settings' );

function social_links_manager_add_admin_menu() {
    // Place under Settings with desired label "Social Links".
    add_options_page(
        'Social Links',          // Page title
        'Social Links',          // Menu title (under Settings)
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
        <h1><?php esc_html_e( 'Social Links', 'social-links-manager' ); ?></h1>
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
            $contact = get_option( 'social_links_manager_contact', array( 'phone' => '', 'email' => '' ) );
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Phone number', 'social-links-manager' ); ?></th>
                    <td><input type="text" name="social_links_manager_contact[phone]" value="<?php echo esc_attr( $contact['phone'] ?? '' ); ?>" class="regular-text" placeholder="+44 20 1234 5678"></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Email address', 'social-links-manager' ); ?></th>
                    <td><input type="email" name="social_links_manager_contact[email]" value="<?php echo esc_attr( $contact['email'] ?? '' ); ?>" class="regular-text" placeholder="hello@example.com"></td>
                </tr>
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
 * Return a simple monochrome SVG icon for a platform.
 * Icons inherit currentColor for easy theming.
 */
function social_links_manager_icon_svg( $platform ) {
    $svg_attrs = 'class="social-link__icon" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="img" fill="currentColor"';
    switch ( $platform ) {
        case 'facebook':
            return '<svg ' . $svg_attrs . '><path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.35 2 1.87 6.48 1.87 12.07c0 4.99 3.66 9.13 8.44 9.93v-7.02H7.9V12.1h2.41V9.93c0-2.38 1.42-3.7 3.6-3.7 1.04 0 2.13.19 2.13.19v2.34h-1.2c-1.18 0-1.55.73-1.55 1.48v1.85h2.64l-.42 2.88h-2.22V22c4.78-.8 8.44-4.94 8.44-9.93z"/></svg>';
        case 'twitter':
            return '<svg ' . $svg_attrs . '><path d="M22 5.92c-.73.33-1.5.55-2.3.65a3.98 3.98 0 0 0 1.74-2.2 7.93 7.93 0 0 1-2.52.96 3.97 3.97 0 0 0-6.77 3.62 11.27 11.27 0 0 1-8.18-4.15 3.97 3.97 0 0 0 1.23 5.3c-.62-.02-1.21-.19-1.73-.47v.05c0 1.92 1.36 3.52 3.16 3.89-.33.09-.69.14-1.05.14-.26 0-.51-.02-.76-.07.51 1.6 2 2.77 3.76 2.8A7.97 7.97 0 0 1 2 19.54 11.26 11.26 0 0 0 8.11 21c7.53 0 11.65-6.24 11.65-11.65 0-.18 0-.35-.01-.53.8-.58 1.5-1.3 2.06-2.13z"/></svg>';
        case 'instagram':
            return '<svg ' . $svg_attrs . '><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11zm0 2a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7zm5.75-2.25a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5z"/></svg>';
        case 'linkedin':
            return '<svg ' . $svg_attrs . '><path d="M6.94 8.5V21H3.56V8.5h3.38zM5.25 3a2 2 0 1 1 0 4.01 2 2 0 0 1 0-4zM21 12.26V21h-3.38v-7.5c0-1.79-.64-3.01-2.25-3.01-1.23 0-1.96.83-2.28 1.64-.12.29-.15.69-.15 1.1V21H9.56s.04-12.43 0-13.72h3.38v1.94c.45-.7 1.25-1.7 3.04-1.7 2.22 0 5.02 1.31 5.02 4.74z"/></svg>';
        case 'whatsapp':
            return '<svg ' . $svg_attrs . '><path d="M20.52 3.48A11.46 11.46 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.08 1.52 5.8L0 24l6.35-1.66A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.24-6.2-3.48-8.52zM12 21.33c-1.84 0-3.55-.5-5.02-1.37l-.36-.21-3.76.98 1-3.66-.24-.38A9.28 9.28 0 1 1 21.28 12 9.3 9.3 0 0 1 12 21.33zm5.35-6.5c-.29-.15-1.72-.85-1.98-.95-.26-.1-.45-.15-.64.15-.19.29-.74.95-.9 1.15-.17.19-.33.22-.62.07-.29-.15-1.24-.46-2.36-1.47-.87-.78-1.46-1.74-1.63-2.03-.17-.29-.02-.45.13-.6.13-.13.29-.33.43-.49.15-.17.19-.29.29-.48.1-.19.05-.36-.02-.51-.07-.15-.64-1.54-.88-2.11-.23-.56-.47-.49-.64-.49-.16 0-.35-.02-.54-.02-.19 0-.5.07-.76.36-.26.29-1 1-1 2.43 0 1.43 1.03 2.81 1.17 3 .15.19 2.02 3.09 4.9 4.33.69.3 1.23.48 1.65.61.69.22 1.32.19 1.82.11.56-.08 1.72-.7 1.97-1.37.24-.67.24-1.24.17-1.37-.07-.13-.26-.2-.55-.35z"/></svg>';
        case 'snapchat':
            // Updated Snapchat icon per provided SVG (scaled via viewBox), inherits currentColor
            return '<svg class="social-link__icon" width="24" height="24" viewBox="0 0 38 38" aria-hidden="true" role="img" fill="currentColor"><path d="M35.8336 26.7701C35.6786 26.2535 34.9294 25.8918 34.9294 25.8918C34.8623 25.8505 34.7951 25.8195 34.7434 25.7936C33.4983 25.1943 32.3978 24.471 31.473 23.6495C30.7291 22.9882 30.0884 22.2649 29.5769 21.49C28.9518 20.5497 28.6573 19.7592 28.5333 19.3355C28.461 19.0565 28.4765 18.948 28.5333 18.8034C28.585 18.6846 28.7245 18.5657 28.7968 18.5141C29.2153 18.2196 29.8921 17.7804 30.3054 17.5118C30.6619 17.2793 30.9719 17.0778 31.1527 16.9538C31.7365 16.5456 32.1344 16.1323 32.372 15.6828C32.6768 15.1042 32.713 14.4635 32.4753 13.8384C32.155 12.9911 31.3645 12.4848 30.3622 12.4848C30.1401 12.4848 29.9076 12.5106 29.6803 12.5571C29.1068 12.6811 28.5591 12.8878 28.1045 13.0634C28.0735 13.0789 28.0373 13.0531 28.0373 13.0169C28.0838 11.8855 28.1407 10.3665 28.0167 8.9199C27.903 7.61278 27.6343 6.51232 27.1952 5.55135C26.7509 4.58522 26.1826 3.87225 25.7279 3.36076C25.2991 2.86995 24.55 2.14664 23.4133 1.49566C21.8169 0.581195 19.9983 0.116211 18.0092 0.116211C16.0253 0.116211 14.2118 0.581195 12.6102 1.4905C11.4116 2.17764 10.6418 2.95261 10.2905 3.3556C9.84097 3.87225 9.26749 4.58522 8.82317 5.54619C8.38402 6.50715 8.11536 7.60761 8.0017 8.91473C7.8777 10.3614 7.92937 11.7615 7.98103 13.0118C7.98103 13.0479 7.94487 13.0738 7.91387 13.0583C7.45922 12.8826 6.91157 12.6759 6.33809 12.5519C6.11076 12.5003 5.88344 12.4796 5.65611 12.4796C4.65382 12.4796 3.86334 12.9859 3.54302 13.8332C3.30536 14.4635 3.34153 15.099 3.64635 15.6777C3.88401 16.1271 4.28183 16.5405 4.86564 16.9486C5.04647 17.0726 5.35646 17.2741 5.71295 17.5066C6.1211 17.7701 6.77724 18.1937 7.19573 18.4934C7.24739 18.5296 7.42305 18.6639 7.47988 18.8034C7.54188 18.9532 7.55221 19.0617 7.47472 19.3562C7.34555 19.785 7.05107 20.5652 6.43625 21.49C5.92477 22.2649 5.28413 22.9882 4.54015 23.6495C3.61019 24.471 2.50972 25.1943 1.26977 25.7936C1.21294 25.8195 1.14061 25.8556 1.06311 25.9021C0.985611 25.9486 0.319134 26.2793 0.179639 26.7701C-0.0270202 27.4934 0.520627 28.1702 1.08894 28.537C2.00858 29.1312 3.1297 29.4515 3.78068 29.622C3.96151 29.6685 4.12683 29.715 4.27666 29.7615C4.36966 29.7925 4.60215 29.8803 4.70548 30.0095C4.82948 30.1697 4.84498 30.3711 4.89147 30.5985C4.96381 30.9808 5.12397 31.4509 5.59412 31.7764C6.11076 32.1329 6.77208 32.1588 7.60388 32.1898C8.47702 32.2208 9.56198 32.2673 10.8019 32.6754C11.3754 32.8666 11.8972 33.1869 12.5017 33.5589C13.7623 34.3338 15.3329 35.3 18.0143 35.3C20.6958 35.3 22.2767 34.3287 23.5477 33.5537C24.147 33.1869 24.6636 32.8666 25.2268 32.6806C26.4667 32.2724 27.5517 32.2311 28.4248 32.1949C29.2566 32.1639 29.9179 32.1381 30.4346 31.7816C30.9409 31.4354 31.0856 30.9136 31.1527 30.521C31.1889 30.3298 31.2147 30.1542 31.3232 30.0095C31.4162 29.8855 31.6332 29.8028 31.7365 29.7667C31.8915 29.7202 32.0569 29.6737 32.248 29.622C32.899 29.4463 33.7101 29.2449 34.7021 28.6817C35.8749 28.0101 35.9576 27.1783 35.8336 26.7701Z"></path></svg>';
        case 'tiktok':
            return '<svg ' . $svg_attrs . '><path d="M21.5 8.6a7.2 7.2 0 0 1-5.4-2.45v8.38c0 3.35-2.71 6.06-6.06 6.06S3.98 17.88 3.98 14.53s2.71-6.06 6.06-6.06c.34 0 .67.03 1 .09v3.04a3.02 3.02 0 0 0-1-.18 3.02 3.02 0 1 0 3.02 3.02V2h2.65a7.2 7.2 0 0 0 5.4 2.45V8.6z"/></svg>';
        case 'youtube':
            return '<svg ' . $svg_attrs . '><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.3 3.5 12 3.5 12 3.5s-7.3 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c2.1.6 9.4.6 9.4.6s7.3 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>';
        default:
            // Fallback: generic link icon
            return '<svg ' . $svg_attrs . '><path d="M3.9 12a5 5 0 0 1 5-5h3v2h-3a3 3 0 1 0 0 6h3v2h-3a5 5 0 0 1-5-5zm6.2 1h3a3 3 0 1 0 0-6h-3V5h3a5 5 0 0 1 0 10h-3v-2z"/></svg>';
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
add_shortcode( 'social_links', 'social_links_manager_shortcode' );

// Shortcode to display contact info (phone/email)
function slm_contact_info_shortcode() {
    $contact = get_option( 'social_links_manager_contact', array( 'phone' => '', 'email' => '' ) );
    $phone = isset( $contact['phone'] ) ? trim( (string) $contact['phone'] ) : '';
    $email = isset( $contact['email'] ) ? trim( (string) $contact['email'] ) : '';

    if ( $phone === '' && $email === '' ) {
        return '';
    }

    $out = '<div class="slm-contact">';
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
