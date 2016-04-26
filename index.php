<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//DE" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-
transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head profile="http://gmpg.org/xfn/11">

<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/resmio-api/css/resmio-style.css">
<?php wp_head(); ?>

</head>
<body id="example-page">

<div id="wrapper">

	<div id="header">
	<img src="<?php echo get_stylesheet_directory_uri(); ?>/resmio-api/img/resmio-logo.png"/>
	</div><!-- header -->

	<div id="main">
		<p><b><?php _e('Name', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-name]'); ?></p>
		<p><b><?php _e('Adresse', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-address]'); ?></p>
		<p><b><?php _e('Straße', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-street]'); ?></p>
		<p><b><?php _e('Postleitzahl', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-zipcode]'); ?></p>
		<p><b><?php _e('Ort', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-city]'); ?></p>
		<p><b><?php _e('Kontakt', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-contact]'); ?></p>
		<p><b><?php _e('Telefon', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-phone]'); ?></p>
		<p><b><?php _e('E-Mail', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-email]'); ?></p>
		<p><b><?php _e('soziale Plattformen', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-social]'); ?></p>
		<p><b><?php _e('Facebook (URL)', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-facebook]'); ?></p>
		<p><b><?php _e('Google+ (URL)', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-googleplus]'); ?></p>
		<p><b><?php _e('Öffnungszeiten', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-openinghours]'); ?></p>
		<p><b><?php _e('Beschreibung', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-description]'); ?></p>

	</div><!-- main -->

	<div id="sidebar">
		<p><b><?php _e('Button', 'resmio_i18n'); ?>:</b><br><br><?php echo do_shortcode('[resmio-button]'); ?></p>
		<br>
		<br>
		<p><b><?php _e('Widget', 'resmio_i18n'); ?>:</b><br><?php echo do_shortcode('[resmio-widget]'); ?></p>

	</div><!-- sidebar -->

	<div id="footer"></div><!-- footer -->

</div><!-- wrapper -->

</body>
</html>
