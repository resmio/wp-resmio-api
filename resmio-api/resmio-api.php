<?php

require("resmio-shortcodes.php");
/**
* Load custom backend css file
*/
add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
function load_admin_styles() {
	wp_enqueue_style('resmiocss', get_stylesheet_directory_uri().'/resmio-api/css/resmio-style.css');
}

/**
 * This function introduces the theme options into the 'Appearance' menu
 */
function add_resmio_admin_menu() {
	add_options_page(
		'resmio API', 																// The title to be displayed in the browser window for this page.
		'resmio API',																// The text to be displayed for this menu item
		'administrator',															// Which type of users can see this menu item
		'resmio_admin_menu_main_options',											// The unique ID - that is, the slug - for this menu item
		'resmio_admin_menu_display'													// The name of the function to call when rendering this menu's page
	);
	
	add_submenu_page(
		'resmio_admin_menu_main_options',											// The ID of the top-level menu page
		'resmio API',																// The value for the browser's title bar
		'resmio API',																// The label used in the administrator's sidebar
		'administrator',															// The roles which are able to access the menu
		'resmio_admin_menu_api_options',											// The ID / slug for this menu
		create_function( null, 'resmio_admin_menu_display( "api_options" );' )		// The callback function used to render this menu
	);
}
add_action( 'admin_menu', 'add_resmio_admin_menu' );

/**
 * Renders a simple page to display for the theme menu defined above.
 */
function resmio_admin_menu_display() {
?>

	<div class="wrap resmio-admin-menu">
		<div class="resmio-admin-menu-api-options"> <?php
			do_settings_sections( 'resmio_admin_menu_api_options' ); ?>
		</div>
	</div>
<?php
}

/** 
 * Default input values (resmio_admin_menu_api_options) 
 */
function resmio_admin_menu_default_api_options() {
	$defaults = array();
	return apply_filters( 'resmio_admin_menu_default_api_options', $defaults );
}

/** 
 * Initialize function (resmio_admin_menu_api_options) 
 */
function initialize_resmio_admin_menu_api_options() {
	if( get_option( 'resmio_admin_menu_api_options' ) == false ) {	
		add_option( 'resmio_admin_menu_api_options', apply_filters( 'resmio_admin_menu_default_api_options', resmio_admin_menu_default_api_options() ) );
	}

	add_settings_section(
		'api_sec_update',								// ID used to identify this section and with which to register options
		'',												// Title to be displayed on the administration page
		'api_sec_update_callback',						// Callback used to render the description of the section
		'resmio_admin_menu_api_options'					// Page on which to add this section of options
	);
	add_settings_field(
		'api_sec_update_field_input',					// ID used to identify the field throughout the theme
		'resmio',										// The label to the left of the option interface element
		'resmio_api_data_update',						// The name of the function responsible for rendering the option interface
		'resmio_admin_menu_api_options',				// The page on which this option will be displayed
		'api_sec_update'								// The name of the section to which this field belongs
	);
	register_setting(
		'resmio_admin_menu_api_options',
		'resmio_admin_menu_api_options'
	);
}
add_action( 'admin_init', 'initialize_resmio_admin_menu_api_options' );

add_action('after_setup_theme', 'my_theme_setup');
function my_theme_setup(){
    load_theme_textdomain( 'resmio_i18n', get_template_directory() . '/languages' );
}

/** 
 * Callback function (resmio_admin_menu_api_options -> api_sec_update [Settings-Section]) 
 */
function api_sec_update_callback() {
	?>
	<a href="https://www.resmio.de"><img src="<?php echo get_stylesheet_directory_uri(); ?>/resmio-api/img/resmio-logo.png" style="width: 180px;"/></a>
	<h3><?php _e('Einstellungen für resmio API', 'resmio_i18n'); ?></h3>
	<p><?php _e('Mit dieser API können Sie relevante Informationen über Ihr Restaurant in Ihrer WordPress Webseite hinzufügen. Wenn Sie das Widget oder den Button verwenden, können Ihre Gäste online Buchungen vornehmen.', 'resmio_i18n'); ?></p>
	<p><?php _e('Mit den in eckigen Klammern [ ] definierten Begriffe lassen sich Shortcodes innerhalb des Editors im Contentbereich verwenden.<br> ', 'resmio_i18n'); ?></p>
	<br>
	<?php
}

/**
 * Callback function (resmio_admin_menu_api_options -> api_sec_update [Settings-Section] -> api_sec_update_field_input [Settings-Field]) 
 */
function resmio_api_data_update() {
	$get_api_data = get_transient( 'resmio_api_data_save' );
	$options = get_option( 'resmio_admin_menu_api_options' );
	if ( $_REQUEST['saved'] && ( $get_api_data != FALSE )) {
		echo '<div class="updated fade"><p><strong>'.__('resmio API Daten erfolgreich importiert', 'resmio_i18n').'</strong></p></div>';
	}
	if ( $_REQUEST['update'] ) {
		echo '<div class="updated fade"><p><strong>'.__('Daten der Webseite erfolgreich aktualisiert', 'resmio_i18n').'</strong></p></div>';
	}
	?>
	<form id="resmio-admin-menu-form-api-options" method="post" action="" enctype="multipart/form-data">
	<?php
	
	// register the $_POST variable
	$resmio_api_id = $_POST['resmio-facility-id'];
	$optionsInput = $_POST['resmio_admin_menu_api_options'];
	
	// if save button hit
	if( $_POST['save']  ) {
		// if no resmio id set
		if( (!isset($resmio_api_id)) || ($resmio_api_id == "") ) {
			echo '<div class="error"><p><strong>'.__('resmio ID nicht gespeichert', 'resmio_i18n').'</strong></p></div>';
		}
		// if resmio id set
		else {
			update_option('resmio-facility-id',$resmio_api_id);
			$get_resmio_api_data = get_option('resmio-facility-id');
			$api_data = get_resmio_api_data($resmio_api_id);
			delete_transient( 'resmio_api_data_save' );
			set_transient( 'resmio_api_data_save', $api_data );
			
			$get_api_data = get_transient( 'resmio_api_data_save' );

			$facilityName = $get_api_data["name"]; if ($facilityName == "") {$facilityNameStr = "";} else {$facilityNameStr = $facilityName;}
			$facilityStreet = $get_api_data["street"]; if ($facilityStreet == "") {$facilityStreetStr = "";} else {$facilityStreetStr = $facilityStreet;}
			$facilityZip = $get_api_data["zip_code"]; if ($facilityZip == "") {$facilityZipStr = "";} else {$facilityZipStr = $facilityZip;}
			$facilityCity = $get_api_data["city"]; if ($facilityCity == "") {$facilityCityStr = "";} else {$facilityCityStr = $facilityCity;}
			$facilityPhone = $get_api_data["phone"]; if ($facilityPhone == "") {$facilityPhoneStr = "";} else {$facilityPhoneStr = $facilityPhone;}
			$facilityMail = $get_api_data["email"]; if ($facilityMail == "") {$facilityMailStr = "";} else {$facilityMailStr = $facilityMail;}
			$facilityFb = $get_api_data["facebook_page"]; if ($facilityFb == "") {$facilityFbStr = "";} else {$facilityFbStr = $facilityFb;}
			$facilityGplus = $get_api_data["google_page"]; if ($facilityGplus == "") {$facilityGplusStr = "";} else {$facilityGplusStr = $facilityGplus;}
			$facilityDescr = $get_api_data["description"]; if ($facilityDescr == "") {$facilityDescrStr = "";} else {$facilityDescrStr = $facilityDescr;}
			$facilityDescrShort = $get_api_data["short_description"]; if ($facilityDescrShort == "") {$facilityDescrShortStr = "";} else {$facilityDescrShortStr = $facilityDescrShort;}
			$facilityOpening = $get_api_data["opening_hours"];
			if ($facilityOpening == "") {
				$facilityOpeningStr = "";
			} 
			elseif (is_array($facilityOpening)) {
				$loopCount = 0;
				$facilityOpeningStr = array();
				foreach ($facilityOpening as $openingEntry) {
					$loopCount++;
					$daysArray = $openingEntry["weekdays"];
					if (count($daysArray) > 1) {
						${'dayA' . $loopCount} = $daysArray[0];
						$theDayA = get_resmio_api_day(${'dayA' . $loopCount});
						${'dayB' . $loopCount} = end($daysArray);
						$theDayB = get_resmio_api_day(${'dayB' . $loopCount});
						${'dayStr' . $loopCount} = $theDayA . ' - ' . $theDayB . ':';
					} else {
						${'dayA' . $loopCount} = $daysArray[0];
						$theDayA = get_resmio_api_day(${'dayA' . $loopCount});
						${'dayStr' . $loopCount} = $theDayA . ':';
					}
					$facilityOpeningStrInner['dayStr'] = ${'dayStr' . $loopCount};
					$openFrom = $openingEntry["begins"];
					if ($openFrom == "") {$openFrom = 'N/A';}
					$openTo = $openingEntry["ends"];
					if ($openTo == "") {$openTo = 'N/A';}
					${'openhStr' . $loopCount} = $openFrom . ' bis ' . $openTo . ' Uhr';
					$facilityOpeningStrInner['openhStr'] = ${'openhStr' . $loopCount};
					$facilityOpeningStr[] = $facilityOpeningStrInner;
					$facilityOpeningStrInner = array();
				}
			}
			else {
				$facilityOpeningStr = $facilityOpening;
			}
			
			$options['api_restaurant_name'] = $facilityNameStr;
			$options['api_address_street'] = $facilityStreetStr;
			$options['api_address_zip'] = $facilityZipStr;
			$options['api_address_city'] = $facilityCityStr;
			$options['api_contact_phone'] = $facilityPhoneStr;
			$options['api_contact_email'] = $facilityMailStr;
			$options['api_social_facebook'] = $facilityFbStr;
			$options['api_social_google'] = $facilityGplusStr;
			$options['api_descr'] = $facilityDescrStr;
			$options['api_descr_short'] = $facilityDescrShortStr;

			$loopCount = 0;
			if (is_array($facilityOpeningStr)) {
				foreach ($facilityOpeningStr as $openingEntry) {
					$loopCount++;
					$left = $openingEntry["dayStr"];
					$right = $openingEntry["openhStr"];
					if ($loopCount <= 7) {
						$options['api_openh_r'.$loopCount.'_left'] = $left;
						$options['api_openh_r'.$loopCount.'_right'] = $right;
					}
				}
				if ($loopCount < 7) {
					for ($x=($loopCount+1); $x<=7; $x++) {
						$options['api_openh_r'.$x.'_left'] = "";
						$options['api_openh_r'.$x.'_right'] = "";
					}
				}
			} 
			else {
				$options['api_openh_r1_left'] = "Info";
				$options['api_openh_r1_right'] = $facilityOpeningStr;
				for ($x=2; $x<=7; $x++) {
					$options['api_openh_r'.$x.'_left'] = "";
					$options['api_openh_r'.$x.'_right'] = "";
				}
			}		
			update_option('resmio_admin_menu_api_options', $options);
			
			echo "<SCRIPT LANGUAGE='JavaScript'>
			window.location='". admin_url('/'). "admin.php?page=resmio_admin_menu_api_options&saved=true" . "';
			</script>";
		}
	} 
	// if update button hit
	else if( $_POST['update']  ) {
		$get_resmio_api_data = get_option('resmio-facility-id');
		update_option('resmio_admin_menu_api_options', $optionsInput);
		
		// if no resmio id set
		if( !isset($get_resmio_api_data) ) {
			echo '<div class="error"><p><strong>'.__('resmio ID nicht gespeichert', 'resmio_i18n').'</strong></p></div>';
		}
		// if resmio id set
		else {
			echo "<SCRIPT LANGUAGE='JavaScript'>
			window.location='". admin_url('/'). "admin.php?page=resmio_admin_menu_api_options&update=true" . "';
			</script>";
		}
	}

	if( isset( $options['api_restaurant_name'] ) ) { $apiRestName = $options['api_restaurant_name']; } else { $apiRestName = ''; }
	if( isset( $options['api_address_street'] ) ) { $apiAdrStr = $options['api_address_street']; } else { $apiAdrStr = ''; }
	if( isset( $options['api_address_zip'] ) ) { $apiAdrZip = $options['api_address_zip']; } else { $apiAdrZip = ''; }
	if( isset( $options['api_address_city'] ) ) { $apiAdrCity = $options['api_address_city']; } else { $apiAdrCity = ''; }
	if( isset( $options['api_contact_phone'] ) ) { $apiConPho = $options['api_contact_phone']; } else { $apiConPho = ''; }
	if( isset( $options['api_contact_email'] ) ) { $apiConMail = $options['api_contact_email']; } else { $apiConMail = ''; }
	if( isset( $options['api_social_facebook'] ) ) { $apiSocFb = esc_url($options['api_social_facebook']); } else { $apiSocFb = ''; }
	if( isset( $options['api_social_google'] ) ) { $apiSocGplus = esc_url($options['api_social_google']); } else { $apiSocGplus = ''; }
	if( isset( $options['api_openh_r1_left'] ) ) { $apiOpenR1L = $options['api_openh_r1_left']; } else { $apiOpenR1L = ''; }
	if( isset( $options['api_openh_r1_right'] ) ) { $apiOpenR1R = $options['api_openh_r1_right']; } else { $apiOpenR1R = ''; }
	if( isset( $options['api_openh_r2_left'] ) ) { $apiOpenR2L = $options['api_openh_r2_left']; } else { $apiOpenR2L = ''; }
	if( isset( $options['api_openh_r2_right'] ) ) { $apiOpenR2R = $options['api_openh_r2_right']; } else { $apiOpenR2R = ''; }
	if( isset( $options['api_openh_r3_left'] ) ) { $apiOpenR3L = $options['api_openh_r3_left']; } else { $apiOpenR3L = ''; }
	if( isset( $options['api_openh_r3_right'] ) ) { $apiOpenR3R = $options['api_openh_r3_right']; } else { $apiOpenR3R = ''; }
	if( isset( $options['api_openh_r4_left'] ) ) { $apiOpenR4L = $options['api_openh_r4_left']; } else { $apiOpenR4L = ''; }
	if( isset( $options['api_openh_r4_right'] ) ) { $apiOpenR4R = $options['api_openh_r4_right']; } else { $apiOpenR4R = ''; }
	if( isset( $options['api_openh_r5_left'] ) ) { $apiOpenR5L = $options['api_openh_r5_left']; } else { $apiOpenR5L = ''; }
	if( isset( $options['api_openh_r5_right'] ) ) { $apiOpenR5R = $options['api_openh_r5_right']; } else { $apiOpenR5R = ''; }
	if( isset( $options['api_openh_r6_left'] ) ) { $apiOpenR6L = $options['api_openh_r6_left']; } else { $apiOpenR6L = ''; }
	if( isset( $options['api_openh_r6_right'] ) ) { $apiOpenR6R = $options['api_openh_r6_right']; } else { $apiOpenR6R = ''; }
	if( isset( $options['api_openh_r7_left'] ) ) { $apiOpenR7L = $options['api_openh_r7_left']; } else { $apiOpenR7L = ''; }
	if( isset( $options['api_openh_r7_right'] ) ) { $apiOpenR7R = $options['api_openh_r7_right']; } else { $apiOpenR7R = ''; }
	if( isset( $options['api_descr'] ) ) { $apiDescr = $options['api_descr']; } else { $apiDescr = ''; }
	if( isset( $options['api_descr_short'] ) ) { $apiDescrShrt = $options['api_descr_short']; } else { $apiDescrShrt = ''; }

	if( $get_api_data == FALSE ):
	echo '<div class="error fade"><p><strong>'.__('Ungültige ID', 'resmio_i18n').'</strong></p></div>'; 
	echo '<p><strong>'.__('Noch keine resmio ID? Melden Sie sich <a href="https://www.resmio.com" target="_blank">hier</a> kostenlos an!', 'resmio_i18n').'</strong></p><br>';

	else: 
	?>
	<body onload="additionalOpenHours()">
	</body>
	<head>
	<script type = "text/javascript" src = "http://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
	</head> 
	<script type = "text/javascript" language = "javascript">
		function additionalOpenHours() {
			var php_var = "<?php echo $apiOpenR3L; ?>";
			if (php_var) {
				$('#api_openh_r3_left').on('input',function(){
    				if( ($(this).val() != 0))
						$("#openHoursHide").show();
					else 
        				$("#openHoursHide").hide();
    			});
			} else {
				$("#openHoursHide").hide();
				$('#api_openh_r3_left').on('input',function(){
    				if( ($(this).val() != 0))
						$("#openHoursHide").show();
					else 
       				$("#openHoursHide").hide();
   				});
			}
		}
	</script>
	<p><?php _e('Schritt 1 - Gib die ID des Restaurants ein und importiere deine Restaurantinformationen', 'resmio_i18n'); ?>&nbsp;<?php _e('(Noch keine resmio ID? Melden Sie sich <a href="https://www.resmio.com" target="_blank">hier</a> kostenlos an).', 'resmio_i18n'); ?></p>
	<br>
	<p class="float-left-id"><?php _e('resmio ID', 'resmio_i18n'); ?></p>
	<input class="input-field-id" size="20" id="resmio-facility-id" name="resmio-facility-id" type="text" placeholder="the-fish" value="<?php echo get_option('resmio-facility-id'); ?>" />
	<?php echo '<input name="save" type="submit" class="button-primary button" value="'.__('Daten importieren', 'resmio_i18n').'" />'; ?>
	<input type="hidden" name="action" value="save" />
	<br>
	<br>
	<br>
	<?php $alert_message = __("Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!", 'resmio_i18n' ); ?>
	<p class="float-left"><?php _e('Schritt 2 - Bearbeite deine Restaurantinformationen und speichere diese anschließend für deine WordPress Webseite.', 'resmio_i18n'); ?></p>
	<input name="update" type="submit" class="resmio-button-update button" onclick="return confirm('<?php echo $alert_message; ?>')" value="<?php echo esc_attr(__('Daten speichern','resmio_i18n')); ?>" />
	<input type="hidden" name="action" value="update" />
	<br>
	<br>
	<?php endif; ?>
	<div class="wrapper-head-1">
    	<div class="one">
    		<p><b><?php _e('Felder', 'resmio_i18n'); ?></b></p>
    		<p><?php _e('(hier kannst du die Restaurantinformationen bearbeiten)', 'resmio_i18n'); ?></p>
		</div>
    	<div class="two">
    		<p><b><?php _e('Ergebnis', 'resmio_i18n'); ?></b></p>
    		<p><?php _e('(so wird die Information auf der Webseite dargestellt)', 'resmio_i18n'); ?></p>
    	</div>
    	<div class="three">
    		<p><b><?php _e('Shortcode', 'resmio_i18n'); ?></b></p>
    		<p><?php _e('(so wird der Code kopiert)', 'resmio_i18n'); ?></p>
    	</div>
	</div>
	<div class="wrapper-head-2">
    	<div class="one">
    		<p><b><?php _e('Felder und Shortcodes', 'resmio_i18n'); ?></b></p>
    		<p><?php _e('(kopiere den blauen Code <font color="#00a7c4">[resmio-x]</font> in deine WordPress Webseite)', 'resmio_i18n'); ?></p>
		</div>
    	<div class="two">
    	</div>
    	<div class="three">
    	</div>
	</div>
	<br>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_restaurant_name">
				<p><?php _e('Name', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_restaurant_name" type="text" size="36" name="resmio_admin_menu_api_options[api_restaurant_name]" value="<?php echo $apiRestName; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-name]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-name]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_street">
				<p><?php _e('Straße', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_address_street" type="text" size="36" name="resmio_admin_menu_api_options[api_address_street]" value="<?php echo $apiAdrStr; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-street]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-street]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_zip">
				<p><?php _e('Postleitzahl', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_address_zip" type="text" size="8" name="resmio_admin_menu_api_options[api_address_zip]" value="<?php echo $apiAdrZip; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-zipcode]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-zipcode]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_city">
				<p><?php _e('Ort', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_address_city" type="text" size="36" name="resmio_admin_menu_api_options[api_address_city]" value="<?php echo $apiAdrCity; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-city]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-city]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<p class="label-for-values"><?php _e('(Adresse)', 'resmio_i18n'); ?>
			<p class="label-for-values-4"><?php _e('(Straße, Postleitzahl & Ort)', 'resmio_i18n'); ?></p>    		
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-address]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-address]</p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_contact_phone">
				<p><?php _e('Telefon', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_contact_phone" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_phone]" value="<?php echo $apiConPho; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-phone]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-phone]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_contact_email">
				<p><?php _e('E-Mail', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_contact_email" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_email]" value="<?php echo $apiConMail; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-email]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-email]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<p class="label-for-values"><?php _e('(Kontakt)', 'resmio_i18n'); ?></p>    					
    		<p><?php _e('(Telefon & E-Mail)', 'resmio_i18n'); ?></p>    		
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-contact]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-contact]</p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_social_facebook">
			<p><?php _e('Facebook', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_social_facebook" type="text" size="36" name="resmio_admin_menu_api_options[api_social_facebook]" value="<?php echo $apiSocFb; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-facebook]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-facebook]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_social_google">
				<p><?php _e('Google+', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values" id="api_social_google" type="text" size="36" name="resmio_admin_menu_api_options[api_social_google]" value="<?php echo $apiSocGplus; ?>" />
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-googleplus]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-googleplus]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<p class="label-for-values"><?php _e('soziale Plattformen ', 'resmio_i18n'); ?></p>
    		<p><?php _e('(Facebook & Google+)', 'resmio_i18n'); ?></p>
    	</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-social]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-social]</p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_descr">
				<p><?php _e('Text', 'resmio_i18n'); ?></p>
			</label>
			<textarea class="input-field-values" id="api_descr" rows="7" cols="36" name="resmio_admin_menu_api_options[api_descr]"><?php echo $apiDescr; ?></textarea>
		</div>
		<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-description]'); ?>&nbsp;</p>
    	</div>
    	<div class="three">
    		<p class="label-for-shortcode">[resmio-description]</p>
    	</div>
	</div>
	<br>
	<div class="wrapper">
    	<div class="one">
    		<p><b><?php _e('Öffnungszeiten', 'resmio_i18n'); ?></b></p>
		</div>
		<div class="two">
    		&nbsp;
    	</div>
    	<div class="three-half">
    		<p class="label-for-shortcode">[resmio-openinghours]</p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values-2" for="api_openh_r1_left">
				<p><?php _e('Tag(e)', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r1_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r1_left]" value="<?php echo $apiOpenR1L; ?>" />
			<label class="label-for-values-3" for="api_openh_r1_right" >
				<p><?php _e('Zeiten', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r1_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r1_right]" value="<?php echo $apiOpenR1R; ?>" />
			<br>
			<br>
			<label class="label-for-values-2" for="api_openh_r2_left">
				<p><?php _e('Tag(e)', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r2_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r2_left]" value="<?php echo $apiOpenR2L; ?>" />
			<label class="label-for-values-3" for="api_openh_r2_right" >
				<p><?php _e('Zeiten', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r2_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r2_right]" value="<?php echo $apiOpenR2R; ?>" />
			<br>
			<br>
			<label class="label-for-values-2" for="api_openh_r3_left">
				<p><?php _e('Tag(e)', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r3_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r3_left]" value="<?php echo $apiOpenR3L; ?>" />
			<label class="label-for-values-3" for="api_openh_r3_right" >
				<p><?php _e('Zeiten', 'resmio_i18n'); ?></p>
			</label>
			<input class="input-field-values-2" id="api_openh_r3_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r3_right]" value="<?php echo $apiOpenR3R; ?>" />
			<br>
			<br>
			<div id="openHoursHide">
				<label class="label-for-values-2" for="api_openh_r4_left">
					<p><?php _e('Tag(e)', 'resmio_i18n'); ?></p>
				</label>
				<input class="input-field-values-2" id="api_openh_r4_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r4_left]" value="<?php echo $apiOpenR4L; ?>" />
				<label class="label-for-values-3" for="api_openh_r4_right" >
					<p><?php _e('Zeiten', 'resmio_i18n'); ?></p>
				</label>
				<input class="input-field-values-2" id="api_openh_r4_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r4_right]" value="<?php echo $apiOpenR4R; ?>" />
				<br>
				<br>
				<label class="label-for-values-2" for="api_openh_r5_left">
					<p><?php _e('Tag(e)', 'resmio_i18n'); ?></p>
				</label>
				<input class="input-field-values-2" id="api_openh_r5_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r5_left]" value="<?php echo $apiOpenR5L; ?>" />
				<label class="label-for-values-3" for="api_openh_r5_right" >
					<p><?php _e('Zeiten', 'resmio_i18n'); ?></p>
				</label>
				<input class="input-field-values-2" id="api_openh_r5_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r5_right]" value="<?php echo $apiOpenR5R; ?>" />
			</div>
		</div>
    	<div class="two">
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-openinghours]'); ?>&nbsp;</p>
    	</div>
	</div>
	<div class="wrapper">
	   	<div class="one">
		</div>
	   	<div class="one-half">
	   		<p class="label-for-shortcode">[resmio-openinghours]</p>
	   	</div>
	</div>
	<br>
	<div class="wrapper">
    	<div class="one">
    		<p><b><?php _e('Widget & Button', 'resmio_i18n'); ?></b></p>
		</div>
	</div>
	<div class="wrapper">
    	<div class="widget-button-big">
    		<div class="label-for-shortcode-widget"><?php echo do_shortcode('[resmio-widget]'); ?>&nbsp;</div>
    		<div class="label-for-shortcode-w">[resmio-widget]</div>
    		<div class="label-for-shortcode-button"><?php echo do_shortcode('[resmio-button]'); ?>&nbsp;</div>
    		<div class="label-for-shortcode-b">[resmio-button]</div>
    	</div>
    </div>
    <div class="wrapper">
    	<div class="widget-button-small">
   			<div class="label-for-shortcode-w-2">[resmio-widget]</div>
    		<div class="label-for-shortcode-button-2"><?php echo do_shortcode('[resmio-button]'); ?>&nbsp;</div>
    		<div class="label-for-shortcode-b-2">[resmio-button]</div>
    	</div>
    </div>
    <br>
    <br>
    <?php $alert_message = __("Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!", 'resmio_i18n' ); ?>
	<input name="update" type="submit" class="resmio-button-update-end button" onclick="return confirm('<?php echo $alert_message; ?>')" value="<?php echo esc_attr(__('Daten speichern','resmio_i18n')); ?>" />
	<input type="hidden" name="action" value="update" />
<?php
}
/**
 * Fetch the resmio API data
 */
if( !function_exists('get_resmio_api_data') ):
// function get_resmio_api_data($key='', $id='') {
function get_resmio_api_data($id='') {
	$http = (!empty($_SERVER['HTTPS'])) ? "https" : "http";
	
	$resmio_api_url_a = 'https://api.resmio.com/v1/facility/' . $id;
	$response = wp_remote_retrieve_body( wp_remote_get($resmio_api_url_a, array('sslverify' => false )));
	if( is_wp_error( $response ) ) {
	} else {
	$data = json_decode($response, true);
	}
	return $data;
}
endif;

/**
 * Return the weekdays
 */
if( !function_exists('get_resmio_api_day') ):
function get_resmio_api_day($day='7') {
	switch ($day) {
	    case 0:
	        $dayVal = 'Mo.';
	        break;
	    case 1:
	        $dayVal = 'Di.';
	        break;
	    case 2:
	        $dayVal = 'Mi.';
	        break;
		case 3:
	        $dayVal = 'Do.';
	        break;
		case 4:
	        $dayVal = 'Fr.';
	        break;
		case 5:
	        $dayVal = 'Sa.';
	        break;
		case 6:
	        $dayVal = 'So.';
	        break;
		case 7:
	        $dayVal = 'N/A';
	        break;
	}
	return $dayVal;
}
endif;
?>