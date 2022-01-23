<?php
$responses = CodeReadr\Responses_Model::get_responses();
?>
<div class="codereadr-admin-page codereadr-admin-responses-page">
	<div class="codereadr-admin-page__header">
		<h3><?php _e( 'CodeREADr Responses ', 'codereadr' ); ?></h3>
	</div>
	<div class="codereadr-admin-page__content">
		<div class="codereadr-admin-page__header">
			<h4><?php _e( 'Registered Responses', 'codereadr' ); ?></h4>
			<a class="codereadr-button-primary codereadr-add-new-response"> <?php _e( 'Add a new response', 'codereadr' ); ?></a>
		</div>
		<?php if ( empty( $responses ) ) : ?>
			<div class="codereadr-not-found-message"> <?php _e( 'No responses were found!', 'codereadr' ); ?></div>
			<?php
		else :
			foreach ( $responses as $response ) :
				?>

				<?php
			endforeach;
		endif;
		?>
	</div>

	<div class="codereadr-modal codereadr-add-response-modal">
		<div class="codereadr-modal__content">
			<div class="codereadr-modal__header">
				<h3> <?php _e( 'Add a new repsonse', 'codereadr' ); ?> </h3>
			</div>
			<div class="codereadr-modal__body">
			<div class="codereadr-flex codreadr-flex-column" style="padding: 0 0 20px; border-bottom: 1px solid #eee">
				<label style="margin-right: 30px;display: block;"> Name </label>
				<input type="text" class="codereadr-response-name" />
			</div>
			<div class="codereadr-flex codreadr-flex-row" style="padding: 0 0 20px; border-bottom: 1px solid #eee">
				<label style="margin-right: 30px;display: inline-flex;align-items: center;"> Status </label>
				<select class="codereadr-response-status">
					<option value="0">0</option>
					<option value="1">1</option>
				</select>
				<p style="color: #7e7c7c; margin-left: 10px;"> This must be set to either 1 (Success) or 0 (Failure). </p>
			</div>
			<div class="codereadr-flex codereadr-flex-column" style="padding: 20px 0;">  
				<div style="margin-bottom: 10px;"> Text </div>
				<textarea class="codereadr-response-text" style="min-height: 160px; min-height: 160px;border: 1px solid #e3e3e3;" ></textarea>
				<p style="color: #7e7c7c;background: #e3e3e3;padding: 10px;"> <stron> You can use the following tags:</strong> <br> 
					{{tid}}	The scanned barcode's value. <br>
					{{sid}}	The numeric ID of the service the scan was made under. <br>
				</p>
			</div>
			<input type="hidden" class="codereadr-response-id" />
			<div class="codereadr-admin-responses-page__buttons">
				<a class="codereadr-button-secondary">Cancel</a>
				<a class="codereadr-button-primary">Save</a>
			</div>
		</div>
	</div>
</div>
