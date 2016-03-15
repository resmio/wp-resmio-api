<?php

/**
* Load custom backend css file
*/
add_action( 'admin_enqueue_scripts', 'load_admin_styles' );
function load_admin_styles() {
	wp_enqueue_style('resmiocss', get_stylesheet_directory_uri().'/resmio-api/css/style.css');
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

/** 
 * Callback function (resmio_admin_menu_api_options -> api_sec_update [Settings-Section]) 
 */
function api_sec_update_callback() {
	echo '<a href="https://www.resmio.de"><img src="'. get_stylesheet_directory_uri() .'/resmio-api/img/resmio-logo.png" style="width: 180px;"/></a>';
	echo '<h3>Einstellungen für resmio API</h3>';
	echo '<p><b>Im ersten Schritt wird die resmio ID gespeichert und die Daten werden von der resmio API importiert &rArr; 1) API Daten importieren<br>Im zweiten Schritt werden die importierten Daten in die WordPress Einstellungen übernommen &nbsp;&nbsp;&nbsp;&nbsp;&rArr;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2) API Daten übernehmen</b><br>Hinweis: vor der Übernahme (Schritt 2) können die importierten Daten manuell geändert werden</p>';
}

/**
 * Callback function (resmio_admin_menu_api_options -> api_sec_update [Settings-Section] -> api_sec_update_field_input [Settings-Field]) 
 */
function resmio_api_data_update() {
	$get_api_data = get_transient( 'resmio_api_data_save' );
	$options = get_option( 'resmio_admin_menu_api_options' );
	if ( $_REQUEST['saved'] && ( $get_api_data != FALSE )) {
		echo '<div class="updated fade"><p><strong>resmio API Daten erfolgreich importiert</strong></p></div>';
	}
	if ( $_REQUEST['update'] ) {
		echo '<div class="updated fade"><p><strong>Daten der Webseite erfolgreich aktualisiert</strong></p></div>';
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
			echo '<div class="error"><p><strong>resmio ID nicht gespeichert</strong></p></div>';
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
			//
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
			echo '<div class="error"><p><strong>resmio ID nicht gespeichert</strong></p></div>';
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
	echo '<div class="error fade"><p><strong>Diese ID wurde nicht gefunden</strong></p></div>'; 
	echo '<p><strong>Noch keine resmio ID? Melden Sie sich <a href="https://www.resmio.com" target="_blank">hier</a> kostenlos an!</strong></p><br>';

	else: 
	?>
	<br>
	<?php endif; ?>
	<div class="id-text">
		<h4 style="float:left;" class="resmio-api-input-label">Gib die ID des Restaurants ein:</h4>
		<input style="float:left;" class="id-input" size="30" id="resmio-facility-id" name="resmio-facility-id" type="text" value="<?php echo get_option('resmio-facility-id'); ?>" />
		<div class="resmio-float-buttons-wrapper">
			<div class="submit resmio-float-buttons">
				<input name="save" type="submit" class="button-primary" value="1) API Daten importieren	&nbsp;" />
				<input type="hidden" name="action" value="save" />
				<p><small style="float:left;" class="id-info">resmio ID Beispiel: the-fish</small></p>
			</div>
			<div class="submit resmio-float-buttons">
				<?php
				$alert_message = "Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!"; ?>
				<input name="update" type="submit" class="resmio-button-update button" onclick="return confirm('<?php echo $alert_message; ?>')" value="2) API Daten übernehmen" />
				<input type="hidden" name="action" value="update" />
			</div>
			<br>
		</div>
	</div>
	<br>
	<br>
	<h3>Vorschau Widget und Button</h3>
	<div class="showBtnWdgt">
		<div class="showWdgt">
			<?php 
			echo $get_api_data["widgetCode"]; 
			?>
		</div>
		<div class="showBtn"><?php 
			echo $get_api_data["buttonCode"];
			?>
		</div>
    </div>
	<table class="form-table-inner resmio-api-data-table" cellspacing="0">
		<tr>
			<td colspan="2">
				<h3>Restaurantname</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
					<label for="api_restaurant_name" class="resmio-api-input-label">
						<h4>Name</h4>
					</label>
					<input id="api_restaurant_name" type="text" size="36" name="resmio_admin_menu_api_options[api_restaurant_name]" value="<?php echo $apiRestName; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Adresse</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-address-street-wrapper">
					<label for="api_address_street" class="resmio-api-input-label">
						<h4>Straße</h4>
					</label>
					<input id="api_address_street" type="text" size="36" name="resmio_admin_menu_api_options[api_address_street]" value="<?php echo $apiAdrStr; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-address-zip-wrapper">
					<label for="api_address_zip" class="resmio-api-input-label">
						<h4>Postleitzahl</h4>
					</label>
					<input id="api_address_zip" type="text" size="8" name="resmio_admin_menu_api_options[api_address_zip]" value="<?php echo $apiAdrZip; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-address-city-wrapper">
					<label for="api_address_city" class="resmio-api-input-label">
						<h4>Ort</h4>
					</label>
					<input id="api_address_city" type="text" size="36" name="resmio_admin_menu_api_options[api_address_city]" value="<?php echo $apiAdrCity; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Kontakt</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-contact-phone-wrapper">
					<label for="api_contact_phone" class="resmio-api-input-label">
						<h4>Telefon</h4>
					</label>
					<input id="api_contact_phone" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_phone]" value="<?php echo $apiConPho; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-contact-email-wrapper">
					<label for="api_contact_email" class="resmio-api-input-label">
						<h4>E-Mail</h4>
					</label>
					<input id="api_contact_email" type="text" size="36" name="resmio_admin_menu_api_options[api_contact_email]" value="<?php echo $apiConMail; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>soziale Plattformen</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-social-facebook-wrapper">
					<label for="api_social_facebook" class="resmio-api-input-label">
						<h4>Facebook (URL)</h4>
					</label>
					<input id="api_social_facebook" type="text" size="63" name="resmio_admin_menu_api_options[api_social_facebook]" value="<?php echo $apiSocFb; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-social-google-wrapper">
					<label for="api_social_google" class="resmio-api-input-label">
						<h4>Google + (URL)</h4>
					</label>
					<input id="api_social_google" type="text" size="63" name="resmio_admin_menu_api_options[api_social_google]" value="<?php echo $apiSocGplus; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td>
				<h3>Öffnungszeiten</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r2_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r1_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r1_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r1_left]" value="<?php echo $apiOpenR1L; ?>" />
				</div>
				<div id="api_openh_r2_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r1_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r1_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r1_right]" value="<?php echo $apiOpenR1R; ?>" />
				</div>
			</td>
		</tr>	
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r2_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r2_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r2_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r2_left]" value="<?php echo $apiOpenR2L; ?>" />
				</div>
				<div id="api_openh_r2_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r2_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r2_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r2_right]" value="<?php echo $apiOpenR2R; ?>" />
				</div>
			</td>
		</tr>	
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r3_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r3_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r3_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r3_left]" value="<?php echo $apiOpenR3L; ?>" />
				</div>
				<div id="api_openh_r3_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r3_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r3_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r3_right]" value="<?php echo $apiOpenR3R; ?>" />
				</div>
			</td>
		</tr>	
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r4_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r4_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r4_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r4_left]" value="<?php echo $apiOpenR4L; ?>" />
				</div>
				<div id="api_openh_r4_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r4_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r4_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r4_right]" value="<?php echo $apiOpenR4R; ?>" />
				</div>
			</td>
		</tr>	
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r5_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r5_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r5_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r5_left]" value="<?php echo $apiOpenR5L; ?>" />
				</div>
				<div id="api_openh_r5_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r5_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r5_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r5_right]" value="<?php echo $apiOpenR5R; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r6_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r6_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r6_left" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r6_left]" value="<?php echo $apiOpenR6L; ?>" />
				</div>
				<div id="api_openh_r6_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r6_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r6_right" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r6_right]" value="<?php echo $apiOpenR6R; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-openh-row">
			<td colspan="2">
				<div id="api_openh_r7_left_wrapper" class="resmio-api-input-wrapper-alt-left">
					<label for="api_openh_r7_left" class="resmio-api-input-label">
						<h4>Tag(e)</h4>
					</label>
					<input id="api_openh_r7_left" class="resmio-api-input-field-alt" type="text" size="12" name="resmio_admin_menu_api_options[api_openh_r7_left]" value="<?php echo $apiOpenR7L; ?>" />
				</div>
				<div id="api_openh_r7_right_wrapper" class="resmio-api-input-wrapper-alt-right">
					<label for="api_openh_r7_right" class="resmio-api-input-label">
						<h4>Zeiten</h4>
					</label>
					<input id="api_openh_r7_right" class="resmio-api-input-field-alt" type="text" size="24" name="resmio_admin_menu_api_options[api_openh_r7_right]" value="<?php echo $apiOpenR7R; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<br>
				<br>
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Beschreibung</h3>
			</td>
		</tr>
		<tr class="resmio-api-data-2-col resmio-api-data-entry-row">
			<td class="resmio-api-data-row-label">
				<label for="api_descr" class="resmio-api-input-label">
					<h4>Lang (Über uns Text)</h4>
				</label>
			</td>
			<td>
				<textarea class="resmio-api-data-row-input" id="api_descr" rows="10" cols="63" name="resmio_admin_menu_api_options[api_descr]"><?php echo $apiDescr; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<br>
			</td>
		</tr>
		<tr class="resmio-api-data-entry-row">
			<td colspan="2">
				<div id="resmio-api-descr-short-wrapper">
					<label for="api_descr_short" class="resmio-api-input-label">
						<h4>Kurz (Intro Text)</h4>
					</label>
					<input id="api_descr_short" type="text" size="63" name="resmio_admin_menu_api_options[api_descr_short]" value="<?php echo $apiDescrShrt; ?>" />
				</div>
			</td>
		</tr>
	</table>
	<div class="submit resmio-float-buttons">
		<?php
		$alert_message = "Bist du dir sicher, dass du die Daten der resmio API übernehmen willst? Die aktuellen Werte werden damit überschrieben!"; ?>
		<input name="update" type="submit" class="resmio-button-update-end button" onclick="return confirm('<?php echo $alert_message; ?>')" value="2) API Daten übernehmen" />
		<input type="hidden" name="action" value="update" />
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