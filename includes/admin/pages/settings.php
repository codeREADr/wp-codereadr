<?php
$is_api_key_valid = null;

if ( ! empty( $_POST['api-key'] ) ) {
	$_POST['api-key'] = sanitize_text_field( trim( $_POST['api-key'] ) );
	$request          = wp_remote_post(
		'https://api.codereadr.com/api/',
		array(
			'body' => array(
				'api_key' => $_POST['api-key'],
				'section' => 'users',
				'action'  => 'retrieve',
			),
		)
	);
	$is_api_key_valid = 'connection-problem';
	if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		error_log( print_r( $request, true ) );
	} else {
		$response         = wp_remote_retrieve_body( $request );
		$is_api_key_valid = true;
		if ( strpos( $response, 'API key is missing' ) ) {
			$is_api_key_valid = false;
		} else {
			update_option( 'codereadr-api-key', $_POST['api-key'] );
		}
	}
}

if ( false === $is_api_key_valid ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Invalid API key.', 'codereadr' ); ?></p>
	<?php
} elseif ( 'connection-problem' === $is_api_key_valid ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Connection problem! Please check your connection.', 'codereadr' ); ?></p>
	<?php
} elseif ( true === $is_api_key_valid ) {
	?>
		<p class ="codereadr-success-message"> 
		<?php
		_e( 'Api key saved successfully.', 'codereadr' );
		?>
		</p>
	<?php
}
$api_key = get_option( 'codereadr-api-key' );
?>
<div class="codereadr-admin-settings-page">
	<h3><?php _e( 'CodeREADr Settings ', 'codereadr' ); ?></h3>
	<div class="codereadr-admin-settings-page__content">
		<div class="codereadr-admin-settings-page__api-key">
			<h4><?php _e( 'API KEY ', 'codereadr' ); ?></h4>
			<form action="#" method="post">
				<div class="codereadr-admin-settings-page__api-key-label"><?php _e( 'Enter your api key here', 'codereadr' ); ?></div>
				<input <?php echo $api_key ? 'disabled' : ''; ?> type="text" name="api-key" class="codereadr-admin-settings-page__api-key-input" value="<?php echo $api_key ? esc_attr( str_repeat( '*', strlen( $api_key ) - 4 ) . substr( $api_key, -4 ) ) : ''; ?>" />
				<div class="codereadr-admin-settings-page__buttons">
					<a class="codereadr-button-secondary">Reset settings</a>
					<input type="submit" class="codereadr-button-primary" value="Save changes" />
				</div>
			</form>

		</div>
	</div>
</div>
