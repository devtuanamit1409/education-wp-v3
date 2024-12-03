<?php 

defined( 'ABSPATH' ) || exit;

/**
 * Checks if the constant is defined.
 *
 * NOTE: This function allows mocking constants when testing.
 *
 * @since 3.5
 *
 * @param string $constant_name Name of the constant to check.
 *
 * @return bool true when constant is defined; else, false.
 */
function rocket_has_constant( $constant_name ) {
	return defined( $constant_name );
}

/**
 * Gets the constant is defined.
 *
 * NOTE: This function allows mocking constants when testing.
 *
 * @since 3.5
 *
 * @param string     $constant_name Name of the constant to check.
 * @param mixed|null $default Optional. Default value to return if constant is not defined.
 *
 * @return mixed
 */
delete_transient( 'rocket_check_key_errors' );
delete_transient( 'wp_rocket_no_licence' );
$consumer_data = [
	'consumer_key'   => '********',
	'consumer_email' => 'noreply@gmail.com',
	'secret_key'     => hash( 'crc32', 'noreply@gmail.com' ),
];
update_option( 'wp_rocket_settings', array_merge( get_option( 'wp_rocket_settings', [] ), $consumer_data ) );
add_filter( 'pre_http_request', function( $pre, $parsed_args, $url ) {
	if ( strpos( $url, 'https://wp-rocket.me/valid_key.php' ) !== false ) {
		return [
			'response' => [ 'code' => 200, 'message' => 'ОК' ],
			'body'     => json_encode( [ 
				'success' => true,
				'data'    => $consumer_data,
			] )
		];
	} elseif ( strpos( $url, 'https://wp-rocket.me/stat/1.0/wp-rocket/user.php' ) !== false ) {
		return [
			'response' => [ 'code' => 200, 'message' => 'ОК' ],
			'body'     => json_encode( [
				'licence_account'    => '-1',
				'licence_expiration' => 1893456000,
				'has_one-com_account' => false,
			] )
		];
	}
	return $pre;
}, 10, 3 );

function rocket_get_constant( $constant_name, $default = null ) {
	if ( ! rocket_has_constant( $constant_name ) ) {
		return $default;
	}

	return constant( $constant_name );
}
