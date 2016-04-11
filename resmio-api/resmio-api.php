<?php

require("resmio-shortcodes.php");

/**
* Load custom backend css file
*/
add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
function load_admin_styles() {
	wp_enqueue_style('resmiocss', get_stylesheet_directory_uri().'/resmio-api/css/resmio-style.css');
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
}

/**
* Load Font Awesome
*/
add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome' );
function enqueue_font_awesome() {
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css' );
}

add_action('admin_head', 'fontawesome_icon_dashboard');
function fontawesome_icon_dashboard() {
   echo "<style type='text/css' media='screen'>
      #adminmenu #menu-posts-press div.wp-menu-image:before {
	 font-family:'FontAwesome' !important; content:'\\f0a4'; }	
	 </style>";
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
	echo '<a href="https://www.resmio.de"><img src="'. get_stylesheet_directory_uri() .'/resmio-api/img/resmio-logo.png" style="width: 180px;"/></a>';
	echo '<h3>'.__('Einstellungen für resmio API', 'resmio_i18n').'</h3>';
	echo '<p><b>'.__('Im ersten Schritt wird die resmio ID gespeichert und die Daten werden von der resmio API importiert &rArr; 1) API Daten importieren<br>Im zweiten Schritt werden die importierten Daten in die WordPress Einstellungen übernommen &nbsp;&nbsp;&nbsp;&nbsp;&rArr;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2) API Daten übernehmen</b><br>Hinweis: vor der Übernahme (Schritt 2) können die importierten Daten manuell geändert werden', 'resmio_i18n').'</p>';
	echo '<p><b>'.__('<br>Mit den in eckigen Klammern [ ] definierten Begriffe lassen sich Shortcodes innerhalb des Editors im Contentbereich verwenden.<br> ', 'resmio_i18n').'</p>';
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
	<br>
	<?php endif; ?>
	<div class="wrapper">
    	<div class="one">
			
			</div>
    	<div class="two">
		</div>
		<br>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<div class="one-id">
    			<?php echo '<h4 class="label-for-values-id">'.__('Gib die ID des Restaurants ein:', 'resmio_i18n').'</h4>'; ?>
				<input class="input-field-values" size="30" id="resmio-facility-id" name="resmio-facility-id" type="text" value="<?php echo get_option('resmio-facility-id'); ?>" />
				<?php echo '<p><small>'.__('resmio ID Beispiel: the-fish', 'resmio_i18n').'</small></p>'; ?>
				<?php echo '<input name="save" type="submit" class="button-primary button" value="'.__('1) API Daten importieren	&nbsp;', 'resmio_i18n').'" />'; ?>
				<input type="hidden" name="action" value="save" />
				<?php $alert_message = __("Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!", 'resmio_i18n' ); ?>
				<input name="update" type="submit" class="resmio-button-update button" onclick="return confirm('<?php echo $alert_message; ?>')" value="<?php echo esc_attr(__('2) API Daten übernehmen','resmio_i18n')); ?>" />
				<input type="hidden" name="action" value="update" />
			</div>
    		<br>
		</div>
		<div class="two">
		</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<h4 class="label-for-shortcode">[resmio-widget]</h4>
			<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-widget]'); ?></p>
    	</div>
    	<div class="two">
			<h4 class="label-for-shortcode">[resmio-button]</h4>
			<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-button]'); ?></p>
    	</div>
    </div>
    <div class="wrapper">
    	<div class="one">
    		<h3><?php _e('Restaurantname', 'resmio_i18n'); ?></h3>
		</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_restaurant_name">
				<h4><?php _e('Name', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_restaurant_name" type="text" size="36" name="resmio_admin_menu_api_options[api_restaurant_name]" value="<?php echo $apiRestName; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-name]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-name]'); ?></p>
    		<br>
			<br>
			<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<h3><?php _e('Adresse', 'resmio_i18n'); ?></h3>    		
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-address]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-address]'); ?></p>
    		<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_street">
				<h4><?php _e('Straße', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_address_street" type="text" size="36" name="resmio_admin_menu_api_options[api_address_street]" value="<?php echo $apiAdrStr; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-street]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-street]'); ?></p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_zip">
				<h4><?php _e('Postleitzahl', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_address_zip" type="text" size="8" name="resmio_admin_menu_api_options[api_address_zip]" value="<?php echo $apiAdrZip; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-zipcode]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-zipcode]'); ?></p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values" for="api_address_city">
				<h4><?php _e('Ort', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_address_city" type="text" size="36" name="resmio_admin_menu_api_options[api_address_city]" value="<?php echo $apiAdrCity; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-city]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-city]'); ?></p>
    		<br>
			<br>
			<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<h3><?php _e('Kontakt', 'resmio_i18n'); ?></h3>
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-contact]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-contact]'); ?></p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_contact_phone">
				<h4><?php _e('Telefon', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_contact_phone" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_phone]" value="<?php echo $apiConPho; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-phone]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-phone]'); ?></p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_contact_email">
				<h4><?php _e('E-Mail', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_contact_email" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_email]" value="<?php echo $apiConMail; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-email]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-email]'); ?></p>
    		<br>
			<br>
			<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<h3><?php _e('soziale Plattformen', 'resmio_i18n'); ?></h3>
    	</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-social]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-social]'); ?></p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_social_facebook">
			<h4><?php _e('Facebook (URL)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_social_facebook" type="text" size="36" name="resmio_admin_menu_api_options[api_social_facebook]" value="<?php echo $apiSocFb; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-facebook]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-facebook]'); ?></p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values" for="api_social_google">
				<h4><?php _e('Google + (URL)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_social_google" type="text" size="36" name="resmio_admin_menu_api_options[api_social_google]" value="<?php echo $apiSocGplus; ?>" />
		</div>
    	<div class="two">
    		<h4 class="label-for-shortcode">[resmio-googleplus]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-googleplus]'); ?></p>
    		<br>
			<br>
			<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<h3><?php _e('Öffnungszeiten', 'resmio_i18n'); ?></h3>
		</div>
		<div class="two">
    		<h4 class="label-for-shortcode">[resmio-openinghours]</h4>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-openinghours]'); ?></p>
    	</div>
	</div>
    <div class="wrapper">
    	<div class="one">
    		<label class="label-for-values-2" for="api_openh_r1_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r1_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r1_left]" value="<?php echo $apiOpenR1L; ?>" />
			<label class="label-for-values-2" for="api_openh_r1_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r1_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r1_right]" value="<?php echo $apiOpenR1R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values-2" for="api_openh_r2_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r2_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r2_left]" value="<?php echo $apiOpenR2L; ?>" />
			<label class="label-for-values-2" for="api_openh_r2_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r2_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r2_right]" value="<?php echo $apiOpenR2R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values-2" for="api_openh_r3_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r3_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r3_left]" value="<?php echo $apiOpenR3L; ?>" />
			<label class="label-for-values-2" for="api_openh_r3_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r3_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r3_right]" value="<?php echo $apiOpenR3R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">		
			<label class="label-for-values-2" for="api_openh_r4_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r4_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r4_left]" value="<?php echo $apiOpenR4L; ?>" />
			<label class="label-for-values-2" for="api_openh_r4_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r4_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r4_right]" value="<?php echo $apiOpenR4R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values-2" for="api_openh_r5_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r5_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r5_left]" value="<?php echo $apiOpenR5L; ?>" />
			<label class="label-for-values-2" for="api_openh_r5_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r5_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r5_right]" value="<?php echo $apiOpenR5R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">	
			<label class="label-for-values-2" for="api_openh_r6_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r6_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r6_left]" value="<?php echo $apiOpenR6L; ?>" />
			<label class="label-for-values-2" for="api_openh_r6_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r6_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r6_right]" value="<?php echo $apiOpenR6R; ?>" />
			</div>
    	<div class="two">
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
			<label class="label-for-values-2" for="api_openh_r7_left">
				<h4><?php _e('Tag(e)', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values-2" id="api_openh_r7_left" type="text" size="10" name="resmio_admin_menu_api_options[api_openh_r7_left]" value="<?php echo $apiOpenR7L; ?>" />
			<label class="label-for-values-2" for="api_openh_r7_right" >
				<h4><?php _e('Zeiten', 'resmio_i18n'); ?></h4>
			</label>
			<input class="input-field-values" id="api_openh_r7_right" type="text" size="17" name="resmio_admin_menu_api_options[api_openh_r7_right]" value="<?php echo $apiOpenR7R; ?>" />
			</div>
    	<div class="two">
    		<br>
			<br>
			<br>
			<br>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<h3><?php _e('Beschreibung', 'resmio_i18n'); ?></h3>
		</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    		<label class="label-for-values-2" for="api_descr">
				<h4><?php _e('Text', 'resmio_i18n'); ?></h4>
			</label>
			<textarea class="input-field-values" id="api_descr" rows="10" cols="46" name="resmio_admin_menu_api_options[api_descr]"><?php echo $apiDescr; ?></textarea>
		</div>
		<div class="two">
    		<h4 class="label-for-shortcode">[resmio-description]</h4>
    		<br>
    		<br>
    		<br>
    		<p class="label-for-shortcode-value"><?php echo do_shortcode('[resmio-description]'); ?></p>
    	</div>
	</div>
	<div class="wrapper">
    	<div class="one">
    	</div>
    	<div class="two">
    		<?php $alert_message = __("Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!", 'resmio_i18n' ); ?>
			<input name="update" type="submit" class="resmio-button-update-end button" onclick="return confirm('<?php echo $alert_message; ?>')" value="<?php echo esc_attr(__('2) API Daten übernehmen','resmio_i18n')); ?>" />
			<input type="hidden" name="action" value="update" />
		</div>
	</div>
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