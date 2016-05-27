<?php

/**
* define the shortcodes
*/
function get_rest_data( $atts, $content = null, $tag ) {
    switch( $tag ) {
        case "resmio-name":
            $src = get_option('resmio_admin_menu_api_options');
            $name = $src["api_restaurant_name"];
            return $name;
            break;
        
        case "resmio-street":
            $src = get_option('resmio_admin_menu_api_options');
            $street = $src["api_address_street"];
            return $street;
            break;
       
        case "resmio-zipcode":
            $src = get_option('resmio_admin_menu_api_options');
            $zip = $src["api_address_zip"];
            return $zip;
            break;
        
        case "resmio-city":
            $src = get_option('resmio_admin_menu_api_options');
            $city = $src["api_address_city"];
            return $city;
            break;
        
        case "resmio-address":
            $src = get_option('resmio_admin_menu_api_options');
            $street = $src["api_address_street"];
            $zipcode = $src["api_address_zip"];
            $city = $src["api_address_city"];
            return $street."<br>".$zipcode." ".$city;
            break;
        
        case "resmio-phone":
            $src = get_option('resmio_admin_menu_api_options');
            $phone = $src["api_contact_phone"];
            return $phone;
            break;
        
        case "resmio-email":
            $src = get_option('resmio_admin_menu_api_options');
            $email = $src["api_contact_email"];
            return $email;
            break;
        
        case "resmio-contact":
            $src = get_option('resmio_admin_menu_api_options');
            $phone = $src["api_contact_phone"];
            $email = $src["api_contact_email"];
            if (!empty($phone)) {
                $phone_1 = "<img class='phone'></img>&nbsp;".$phone."<br>";
            }
            if (!empty($email)) {
                $email_1 = "<img class='email'></img>&nbsp;".$email;
            }
            return $phone_1.$email_1;
            break;
       
        case "resmio-facebook":
            $src = get_option('resmio_admin_menu_api_options');
            $facebook = $src["api_social_facebook"];
            return $facebook;
            break;
       
        case "resmio-googleplus":
            $src = get_option('resmio_admin_menu_api_options');
            $google = $src["api_social_google"];
            return $google;
            break;

        case "resmio-social":
            $src = get_option('resmio_admin_menu_api_options');
            $facebook = $src["api_social_facebook"];
            $google = $src["api_social_google"];
            if (!empty($facebook)){
                $facebook_1 = "<img class='facebook'></img>&nbsp;".$facebook."<br>";
            }
            if (!empty($google)){
                $google_1 = "<img class='google-plus'></img>&nbsp;".$google;
            }
            return $facebook_1.$google_1;
            break;

        case "resmio-openinghours":
            $src = get_option('resmio_admin_menu_api_options');
            $openingDays1 = $src["api_openh_r1_left"];
            $openingHours1 = $src["api_openh_r1_right"];
            $openingDays2 = $src["api_openh_r2_left"];
            $openingHours2 = $src["api_openh_r2_right"];
            $openingDays3 = $src["api_openh_r3_left"];
            $openingHours3 = $src["api_openh_r3_right"];
            $openingDays4 = $src["api_openh_r4_left"];
            $openingHours4 = $src["api_openh_r4_right"];
            $openingDays5 = $src["api_openh_r5_left"];
            $openingHours5 = $src["api_openh_r5_right"];
            $openingDays6 = $src["api_openh_r6_left"];
            $openingHours6 = $src["api_openh_r6_right"];
            $openingDays7 = $src["api_openh_r7_left"];
            $openingHours7 = $src["api_openh_r7_right"];

        if (!empty($openingDays1) & !empty($openingHours1)) {
            $openDay[0] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays1;
            $openHour[0] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours1."</td></tr>";
        }
        if (!empty($openingDays2) & !empty($openingHours2)) {
            $openDay[1] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays2;
            $openHour[1] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours2."</td></tr>";
        }
        if (!empty($openingDays3) & !empty($openingHours3)) {
            $openDay[2] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays3;
            $openHour[2] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours3."</td></tr>";
        }      
        if (!empty($openingDays4) & !empty($openingHours4)) {
            $openDay[3] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays4;
            $openHour[3] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours4."</td></tr>";
        }    
        if (!empty($openingDays5) & !empty($openingHours5)) {
            $openDay[4] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays5;
            $openHour[4] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours5."</td></tr>";
        }    
        if (!empty($openingDays6) & !empty($openingHours6)) {
            $openDay[5] = "<tr style='border:none;'><td style='border:none; padding:0px;'>".$openingDays6;
            $openHour[5] = "</td><td style='border:none; padding:0px;'>&nbsp;".$openingHours6."</td></tr>";
        }    
        if (!empty($openingDays7) & !empty($openingHours7)) {
            $openDay[6] = "<tr style=''><td style=' padding:0px;'>".$openingDays7;
            $openHour[6] = "</td><td style='padding:0px;'>&nbsp;".$openingHours7."</td></tr>";
        }
            return 
                "<table style='width: auto; border:none; margin:0px;'><tbody>"
                .$openDay[0].$openHour[0].
                $openDay[1].$openHour[1].
                $openDay[2].$openHour[2].
                $openDay[3].$openHour[3].
                $openDay[4].$openHour[4].
                $openDay[5].$openHour[5].
                $openDay[6].$openHour[6].
                "</tbody></table>";

            break;
       
        case "resmio-description":
            $src = get_option('resmio_admin_menu_api_options');
            $descr = $src["api_descr"];
            return $descr;
            break;
       
        case "resmio-button":
            $id = get_option('resmio-facility-id');            
            $buttonCode =  '<script data-resmio-button="'.$id.'">
                                (function(d, s) {
                                    var js, rjs = d.getElementsByTagName(s)[0];
                                    js = d.createElement(s);
                                    js.src = "//static.resmio.com/static/de/button.js";
                                    js.async = true;
                                    rjs.parentNode.insertBefore(js, rjs); }(document, "script")
                                );
                            </script>';
            return $buttonCode;
            break;
        
        case "resmio-widget":
            $id = get_option('resmio-facility-id');            
            $widgetCode =  '<div id="resmio-'.$id.'"></div>
                            <script>(function(d, s) {
                                var js, rjs = d.getElementsByTagName(s)[0];
                                js = d.createElement(s);
                                js.src = "//static.resmio.com/static/de/widget.js#id='.$id.'&width=275px&height=400px";
                                rjs.parentNode.insertBefore(js, rjs);
                            }(document, "script"));
                            </script>';
            return $widgetCode;
            break;
    }   
}

/**
* Register the shortcodes
*/
function register_shortcodes(){
    add_shortcode('resmio-name', 'get_rest_data');
    add_shortcode('resmio-street', 'get_rest_data');
    add_shortcode('resmio-zipcode', 'get_rest_data');
    add_shortcode('resmio-city', 'get_rest_data');
    add_shortcode('resmio-address', 'get_rest_data');
    add_shortcode('resmio-phone', 'get_rest_data');
    add_shortcode('resmio-email', 'get_rest_data');
    add_shortcode('resmio-contact', 'get_rest_data');
    add_shortcode('resmio-facebook', 'get_rest_data');
    add_shortcode('resmio-googleplus', 'get_rest_data');
    add_shortcode('resmio-social', 'get_rest_data');
    add_shortcode('resmio-openinghours', 'get_rest_data');
    add_shortcode('resmio-description', 'get_rest_data');
    add_shortcode('resmio-button', 'get_rest_data');
    add_shortcode('resmio-widget', 'get_rest_data');
}
add_action( 'init', 'register_shortcodes');

/**
* Add the shortcode-Button for the tinymce-toolbar
*/
function add_my_shortcode_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "add_tinymce_resmio_plugin");
        add_filter('mce_buttons', 'register_my_shortcode_button');
    }
}

function add_tinymce_resmio_plugin($plugin_array) {
    $plugin_array['custom_mce_button'] = get_template_directory_uri() . '/resmio-api/js/resmio-shortcodes.js';
    return $plugin_array;
}

function register_my_shortcode_button($buttons) {
   array_push($buttons, "custom_mce_button");
   return $buttons;
}
add_action('admin_head', 'add_my_shortcode_button');
?>