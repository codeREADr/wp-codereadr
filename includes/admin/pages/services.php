<?php
$api_key = get_option( 'codereadr-api-key' );
if ( ! $api_key ) { ?>
	<p class="codereadr-error-message"><?php _e( 'You should insert your api key first to retrieve the services list.', 'codereadr' ); ?> <a href="<?php echo admin_url( 'admin.php?page=codereadr-settings' ); ?>"><?php _e( 'Insert it from here', 'codereadr' ); ?></a></p>
	<?php
	exit;
}
$request = wp_remote_get(
	'https://api.codereadr.com/api/',
	array(
		'body' => array(
			'api_key' => $api_key,
			'section' => 'services',
			'action'  => 'retrieve',
		),
	)
);
if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
	?>
	<p class="codereadr-error-message"><?php _e( 'Error while retrieving list.', 'codereadr' ); ?> </p>
	<?php
	exit;
} else {
	$service          = wp_remote_retrieve_body( $request );
	$is_api_key_valid = true;
	if ( strpos( $service, 'API key is missing' ) ) {
		?>
		<p class="codereadr-error-message"><?php _e( 'Your API key is invalid!', 'codereadr' ); ?>  <a href="<?php echo admin_url( 'admin.php?page=codereadr-settings' ); ?>"><?php _e( 'Please re-insert it from here', 'codereadr' ); ?></a> </p>
		<?php
		exit;
	} else {
		$service = simplexml_load_string( $service );
		if ( ! $service ) {
			echo "Failed loading XML\n";
			foreach ( libxml_get_errors() as $error ) {
				echo "\t", $error->message;
			}
			exit;
		}
	}
}
?>

<div class='codereadr-admin-page codereadr-admin-services-page'>
	<h3> 
	<?php
	_e( 'CodeREADr Services ', 'codereadr' );
	?>
	</h3>
	<div class="codereadr-admin-page__content codereadr-admin-services-page__content">
		<div class="codereadr-admin-page__header">
			<h4><?php _e( 'Installed services ', 'codereadr' ); ?></h4> 
			<a class="codereadr-button-primary codereadr-add-new-service"> <?php _e( 'Add a new service', 'codereadr' ); ?></a>
		</div>
		<table> 
			<thead> 
				<tr> <th> Id </th> <th> Service name</th> <th>Validation Method</th> </tr>
			</thead>
			<tbody> 

			<?php foreach ( $service->service as $service ) { ?>
				<tr>
					<td> <?php echo $service->attributes()->id; ?> </td>
					<td> <?php echo $service->name; ?> </td>
					<td> <?php echo $service->validationmethod; ?> </td>
				</tr>
				<?php
			}
			?>
	</div>
	<div class="codereadr-modal codereadr-add-service-modal">
		<div class="codereadr-modal__content">
			<div class="codereadr-modal__header">
				<h3> <?php _e( 'Add a new service', 'codereadr' ); ?> </h3>
			</div>
			<div class="codereadr-modal__body">
			<div class="codereadr-flex codereadr-flex-column" style="padding: 0 0 20px; border-bottom: 1px solid #eee">
				<label style="margin-bottom: 10px;display: block;"> Service title </label>
				<input type="text" class="codereadr-service-title" />
			</div>
			<div class="codereadr-flex codereadr-flex-row" style="padding: 0 0 20px; border-bottom: 1px solid #eee">
				<label style="margin-right: 30px;display: block;"> Action name </label>
				<select class="codereadr-service-action-select">
					<option value=""></option>
				</select>
			</div>
			<div class="codereadr-flex codereadr-flex-column" style="padding: 20px 0;">  
				<div style="margin-bottom: 10px;"> Response Text </div>
				<textarea class="codereadr-service-text" style="min-height: 160px; min-height: 160px;border: 1px solid #e3e3e3;" ></textarea>
				<p style="color: #7e7c7c;background: #e3e3e3;padding: 10px;"> <stron> You can use the following tags:</strong> <br> 
					{{tid}}	The scanned barcode's value. <br>
					{{sid}}	The numeric ID of the service the scan was made under. <br>
				</p>
			</div>
			<input type="hidden" class="codereadr-service-id" />
			<div class="codereadr-admin-services-page__buttons">
				<a class="codereadr-button-secondary">Cancel</a>
				<a class="codereadr-button-primary">Save</a>
			</div>
		</div>
	</div>
</div>
