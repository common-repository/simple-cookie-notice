<?php
/**
 * Plugin Name: Simple Cookie Notice
 * Description: In simple way add personalized cookie info and link to wordpress privacy policy page.
 * Version: 2.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: JL-lovecoding
 * Author URI: https://love-coding.pl/en
 * Text Domain: jlplg_lovecoding
 * Domain Path: /languages
 * License: GPLv3
 * 
 * Simple Cookie Notice is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *   
 * Simple Cookie Notice is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with Simple Cookie Notice. If not, see http://www.gnu.org/licenses/gpl.html.
 */

defined( 'ABSPATH' ) or die( 'hey, you don\'t have an access to read this site' );


// adding 'Settings' link to plugin links
function jlplg_lovecoding_add_plugin_settings_link( $links ) {
    $url = admin_url()."options-general.php?page=privacy-policy";
    $settings_link = '<a href="'.esc_url( $url ).'">'.esc_html( 'Settings' ).'</a>';
    $links[] = $settings_link;
    return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'jlplg_lovecoding_add_plugin_settings_link');


// adding styles and scripts
function jlplg_lovecoding_enqueue_scripts() {
    // load styles and script for plugin only if cookies are not accepted
    if ( !isset( $_COOKIE['cookie-accepted'] ) ) {
        wp_enqueue_style( 'styles', plugins_url( 'styles.css', __FILE__ ) );
        wp_enqueue_script( 'jlplg_lovecoding_script', plugins_url( 'public/js/jlplg_lovecoding_script.js', __FILE__ ), array(), time(), true );
    }
}
add_action( 'wp_enqueue_scripts', 'jlplg_lovecoding_enqueue_scripts' );

// allowed html code in plugin message
function jlplg_lovecoding_allowed_html() {
    return array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'class' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
        'span' => array(
            'class' => array()
        ),
    );
}

// displaying cookie info on page
function jlplg_lovecoding_display_cookie_info() {
    $cookie_message = get_option( "jlplg_lovecoding-field1-cookie-message", 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies' );
    $cookie_info_button = get_option( "jlplg_lovecoding-field3-cookie-button-text", 'Accept Cookies' );
    $show_policy_privacy = get_option( "jlplg_lovecoding-field2-checkbox-privacy-policy", false );
    $background_color = get_option( "jlplg_lovecoding-field5-background-color", '#444546' );
    $text_color = get_option( "jlplg_lovecoding-field6-text-color", '#ffffff' );
    $button_background_color = get_option( "jlplg_lovecoding-field7-button-background-color", '#dcf1ff' );
    $button_text_color = get_option( "jlplg_lovecoding-field8-button-text-color", '#000000' );
    $cookie_info_placemet = get_option( "jlplg_lovecoding-field4-cookie-plugin-placement", 'bottom' );
    $cookie_expire_time = get_option( "jlplg_lovecoding-field9-cookie-expire-time", '30' );
    $allowed_html = jlplg_lovecoding_allowed_html();
?>
    <div class="jlplg-lovecoding-cookie-info-container jlplg-hidden" 
        style="<?php echo '--jlplg-buton-bg-color: '.esc_attr( $button_background_color ).'; background-color: '.esc_attr( $background_color ).'; '.esc_attr( $cookie_info_placemet ).': 0' ?>" 
        id="jlplg-lovecoding-cookie-info-container">
        <form method="post" id="cookie-form"> 
            <p class="jlplg-lovecoding-cookie-info" style="<?php echo 'color: '.esc_attr( $text_color ) ?>"><?php echo wp_kses( $cookie_message, $allowed_html ); ?></p>
            <div class="jlplg-lovecoding-buttons">
            <button type="submit" name="jlplg-cookie-accept-button" class="jlplg-lovecoding-cookie-accept-button" id="cookie-accept-button" style="<?php echo 'background-color: '.esc_attr( $button_background_color ) ?>" data-expire="<?php echo esc_html( $cookie_expire_time ) ?>">
                <span class="button-text" style="<?php echo 'color: '.esc_attr( $button_text_color ) ?>"><?php echo esc_html( $cookie_info_button ); ?></span>
            </button>
            <?php if ( $show_policy_privacy ) { ?>
            <button type="submit" name="jlplg-cookie-privacy-policy" class="jlplg-lovecoding-cookie-privacy-policy" id="cookie-privacy-policy" style="<?php echo 'background-color: '.esc_attr( $button_background_color ) ?>">
                <span class="button-text" style="<?php echo 'color: '.esc_attr( $button_text_color ) ?>"><?php esc_html_e( 'Privacy Policy', 'jlplg_lovecoding' ) ?></span>
            </button>
            <?php } ?>
            </div>
        </form>
    </div>
<?php
}

// display cookie notice if cookie info is not set
function jlplg_lovecoding_display_cookie_notice() {
    // always display cookies info
    add_action('wp_footer', 'jlplg_lovecoding_display_cookie_info');

    // make action when privacy policy button was clicked
    if ( isset( $_POST['jlplg-cookie-privacy-policy'] ) ) {
        $privacy_policy = get_privacy_policy_url();
        if ( empty($privacy_policy) ) {
            $privacy_policy = get_home_url().'/privacy-policy';
        }
        wp_safe_redirect( $privacy_policy );
        exit;
    }
}
add_action( 'init', 'jlplg_lovecoding_display_cookie_notice');

// adding new page to admin menu
add_action( 'admin_menu', 'jlplg_lovecoding_add_new_page' );
function jlplg_lovecoding_add_new_page() {
    add_submenu_page(
        'options-general.php',                                  // $parent_slug
        'Privacy Policy',                                       // $page_title
        'Privacy Policy',                                       // $menu_title
        'manage_options',                                       // $capability
        'privacy-policy',                                       // $menu_slug
        'jlplg_lovecoding_page_html_content'                    // $function
    );
}


// adding settings and sections to page in admin menu
function jlplg_lovecoding_add_new_settings() {
    // register settings
    $configuration_settins_field1_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_textarea_field',
        'default' => 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies'
    );
    $configuration_settins_field2_arg = array(
        'type' => 'boolean',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_checkbox',
        'default' => false
    );
    $configuration_settins_field3_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_input_field',
        'default' => 'Accept Cookies'
    );
    $configuration_settins_field4_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_input_field',
        'default' => 'bottom'
    );
    $configuration_settins_field5_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_input_field',
        'default' => 'bottom'
    );
    $layout_settins_field1_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_color_input',
        'default' => '#444546'
    );
    $layout_settins_field2_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_color_input',
        'default' => '#ffffff'
    );
    $layout_settins_field3_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_color_input',
        'default' => '#dcf1ff'
    );
    $layout_settins_field4_arg = array(
        'type' => 'string',
        'sanitize_callback' => 'jlplg_lovecoding_sanitize_color_input',
        'default' => '#000000'
    );
    register_setting( 'jl_options', 'jlplg_lovecoding-field1-cookie-message', $configuration_settins_field1_arg);     // option group, option name, args
    register_setting( 'jl_options', 'jlplg_lovecoding-field2-checkbox-privacy-policy', $configuration_settins_field2_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field3-cookie-button-text', $configuration_settins_field3_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field4-cookie-plugin-placement', $configuration_settins_field4_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field5-background-color', $layout_settins_field1_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field6-text-color', $layout_settins_field2_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field7-button-background-color', $layout_settins_field3_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field8-button-text-color', $layout_settins_field4_arg);
    register_setting( 'jl_options', 'jlplg_lovecoding-field9-cookie-expire-time', $configuration_settins_field5_arg);

    // adding sections
    add_settings_section( 'jlplg_lovecoding_section_1_configuration', 'Configuration', null, 'jl-slug' );  // id (Slug-name to identify the section), title, callback, page slug
    add_settings_section( 'jlplg_lovecoding_section_2_layout', 'Layout', null, 'jl-slug-2' );

    // adding fields for section
    add_settings_field( 'field-1-cookie-message', 'Cookie Message', 'jlplg_lovecoding_field_1_callback', 'jl-slug', 'jlplg_lovecoding_section_1_configuration' );       // id (Slug-name to identify the field), title, callback, slug-name of the settings page on which to show the section, section, args (attr for field)
    add_settings_field( 'field-2-privacy-policy-button', 'Display Privacy Policy Button', 'jlplg_lovecoding_field_2_callback', 'jl-slug', 'jlplg_lovecoding_section_1_configuration' );
    add_settings_field( 'field-3-cookie-button-text', 'Cookie Button Text', 'jlplg_lovecoding_field_3_callback', 'jl-slug', 'jlplg_lovecoding_section_1_configuration' );
    add_settings_field( 'field-4-cookie-plugin-placement', 'Cookie info placement', 'jlplg_lovecoding_field_4_callback', 'jl-slug', 'jlplg_lovecoding_section_1_configuration' );
    add_settings_field( 'field-9-cookie-expire-time', 'Cookie expire time (in days)', 'jlplg_lovecoding_field_9_callback', 'jl-slug', 'jlplg_lovecoding_section_1_configuration' );
    add_settings_field( 'field-5-cookie-background-color', 'Background color', 'jlplg_lovecoding_field_5_callback', 'jl-slug-2', 'jlplg_lovecoding_section_2_layout' );
    add_settings_field( 'field-6-cookie-text-color', 'Text color', 'jlplg_lovecoding_field_6_callback', 'jl-slug-2', 'jlplg_lovecoding_section_2_layout' );
    add_settings_field( 'field-7-cookie-button-background-color', 'Button background color', 'jlplg_lovecoding_field_7_callback', 'jl-slug-2', 'jlplg_lovecoding_section_2_layout' );
    add_settings_field( 'field-8-cookie-button-text-color', 'Button text color', 'jlplg_lovecoding_field_8_callback', 'jl-slug-2', 'jlplg_lovecoding_section_2_layout' );
}
add_action( 'admin_init', 'jlplg_lovecoding_add_new_settings' );


// field 1 - cookie message
function jlplg_lovecoding_field_1_callback() {
    echo '<textarea type="text" cols="50" rows="4" name="jlplg_lovecoding-field1-cookie-message" >'.esc_textarea( get_option( "jlplg_lovecoding-field1-cookie-message", 'We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies' ) ).'</textarea>';
    echo '<div>Tags allowed in message: a, br, em, strong, span</div>';
}

// field 2 - show privacy policy button
function jlplg_lovecoding_field_2_callback() {
    if ( get_option( "jlplg_lovecoding-field2-checkbox-privacy-policy", false ) ) {
        echo '<input type="checkbox" name="jlplg_lovecoding-field2-checkbox-privacy-policy" checked />';
        echo ' <a href="'.esc_url(admin_url()."options-privacy.php").'" style="margin-left: 20px">Set Privacy Policy Page</a>';
    } else {
        echo '<input type="checkbox" name="jlplg_lovecoding-field2-checkbox-privacy-policy" />';
    }
}

// field 3 - cookie button text
function jlplg_lovecoding_field_3_callback() {
    echo '<input type="text" name="jlplg_lovecoding-field3-cookie-button-text" value="'.esc_html( get_option( "jlplg_lovecoding-field3-cookie-button-text", 'Accept Cookies' ) ).'" />';
}

// field 4 - cookie info placement
function jlplg_lovecoding_field_4_callback() {
    $isChecked = get_option( "jlplg_lovecoding-field4-cookie-plugin-placement", 'bottom' );
    ?>
    <input type="radio" name="jlplg_lovecoding-field4-cookie-plugin-placement" value="top" <?php echo esc_html( $isChecked ) === 'top' ? "checked" : null ?> /> Top <br><br>
    <input type="radio" name="jlplg_lovecoding-field4-cookie-plugin-placement" value="bottom" <?php echo esc_html( $isChecked ) === 'bottom' ? "checked" : null ?> /> Bottom
    <?php
}

// field 5 - background color
function jlplg_lovecoding_field_5_callback() {
    echo '<input type="color" name="jlplg_lovecoding-field5-background-color" value="'.esc_html( get_option( "jlplg_lovecoding-field5-background-color", '#444546' ) ).'" />';
}

// field 6 - text color
function jlplg_lovecoding_field_6_callback() {
    echo '<input type="color" name="jlplg_lovecoding-field6-text-color" value="'.esc_html( get_option( "jlplg_lovecoding-field6-text-color", '#ffffff' ) ).'" />';
}

// field 7 - button background color
function jlplg_lovecoding_field_7_callback() {
    echo '<input type="color" name="jlplg_lovecoding-field7-button-background-color" value="'.esc_html( get_option( "jlplg_lovecoding-field7-button-background-color", '#dcf1ff' ) ).'" />';
}

// field 8 - button text color
function jlplg_lovecoding_field_8_callback() {
    echo '<input type="color" name="jlplg_lovecoding-field8-button-text-color" value="'.esc_html( get_option( "jlplg_lovecoding-field8-button-text-color", '#000000' ) ).'" />';
}

// field 8 - cookie expire time
function jlplg_lovecoding_field_9_callback() {
    echo '<input type="text" name="jlplg_lovecoding-field9-cookie-expire-time" value="'.esc_html( get_option( "jlplg_lovecoding-field9-cookie-expire-time", '30' ) ).'" />';
}

// sanitize textarea
function jlplg_lovecoding_sanitize_textarea_field( $input ) {
    if ( isset( $input ) ) {
        $allowed_html = jlplg_lovecoding_allowed_html();
        $input = wp_kses( $input, $allowed_html );
    }
    return $input;
}

// sanitize input
function jlplg_lovecoding_sanitize_input_field( $input ) {
    if ( isset( $input ) ) {
        $input = sanitize_text_field( $input );
    }
    return $input;
}

// sanitize checkbox
function jlplg_lovecoding_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

// sanitize color input
function jlplg_lovecoding_sanitize_color_input( $input ) {
    if ( isset( $input ) ) {
        $input = sanitize_hex_color( $input );
    }
    return $input;
}


// adding content to menu page
function jlplg_lovecoding_page_html_content() {
    if ( ! current_user_can( 'manage_options' ) ) {
        ?>
        <div style="font-size: 20px; margin-top: 20px"> <?php echo esc_html_e( "You don't have permission to manage this page", "jlplg_lovecoding" ); ?> </div>
        <?php
        return;
    }

    ?>
    <div class="wrap">
        <h2><?php echo esc_html( 'Privacy Policy & Cookie Info') ?></h2>
        <form action="options.php" method="post">
            <?php
            // outpus settings fields (without this there is error after clicking save settings button)
            settings_fields( 'jl_options' );                        // A settings group name. This should match the group name used in register_setting()
            // output setting sections and their fields
            do_settings_sections( 'jl-slug' );                      // The slug name of settings sections you want to output.
            echo "<hr>";
            do_settings_sections( 'jl-slug-2' );                      // The slug name of settings sections you want to output.
            // output save settings button
            submit_button( 'Save Settings', 'primary', 'submit', true );     // Button text, button type, button id, wrap, any other attribute
            ?>
        </form>
    </div>
    <?php
}



