<?php
global $userOptionsAddress;
$adminOptionsResmioID = get_option('resmio-facility-id');


if( !function_exists('get_resmio_api_data') ):

function get_resmio_api_data() {
	$id='the-fish';
	$http = (!empty($_SERVER['HTTPS'])) ? "https" : "http";
	
	$resmio_api_url_a = 'https://api.resmio.com/v1/facility/' . $id;
	//lets fetch it
	$response = wp_remote_retrieve_body( wp_remote_get($resmio_api_url_a, array('sslverify' => false )));
	if( is_wp_error( $response ) ) {
	} else {
	$data = json_decode($response, true);
	}
	return $data;
}
endif;


$resmio_apiData = get_resmio_api_data();
add_option('resmioData', $resmio_apiData);
$resmio_apiData = get_option('resmioData');
delete_option('resmioData');
?>