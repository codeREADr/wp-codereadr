<?php
wp_enqueue_script( 'codereadr-highlight-js' );
wp_enqueue_style( 'codereadr-highlight-css' );
?>
<div class="codereadr-admin-page codereadr-admin-integrations-page">
	<h3><?php _e( 'CodeREADr Integrations ', 'codereadr' ); ?></h3>
	<div class="codereadr-admin-integrations-page__content">
		<div class="codereadr-integration codereadr-event-tickets-integration">
			<img src="<?php echo CODEREADR_PLUGIN_URL . '/includes/admin/assets/images/event-tickets-icon.png'; ?>" />
			<h4> <a target="_blank" href="https://theeventscalendar.com/products/wordpress-event-tickets/">Event Tickets Plus </a></h4>
		</div>
		<div class="codereadr-integration codereadr-box-office-integration">
			<img src="<?php echo CODEREADR_PLUGIN_URL . '/includes/admin/assets/images/woocommerce-box-office-icon.png'; ?>" />
			<h4><a href="https://woocommerce.com/products/woocommerce-box-office/" target="_blank"> Woocommerce Box Office </a> + <a href="https://woocommerce.com/products/woocommerce-order-barcodes/">Woocommerce Order Barcode </a></h4>
		</div>
		<div class="codereadr-integration codereadr-event-custom-integration">
			<svg focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="SettingsIcon" tabindex="-1" title="Settings">
				<path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"></path>
			</svg>
			<h4> Build Your Custom Integration </h4>
		</div>
	</div>

	<div class="codereadr-modal">
		<div class="codereadr-modal__content" style="max-width: 900px">
			<div class="codereadr-modal__header">
				<h3> <?php _e( 'Custom Integration', 'codereadr' ); ?> </h3>
				<div class="codereadr-modal__close">
					<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
						<path fill="none" stroke="#000" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>
					</svg>
				</div>
			</div>
	
			<div class="codereadr-modal__body">
			<h3> Copy the following code and insert it in your child theme </h3>
			<pre style="background: #272821; padding: 30px;">   
				<code class="language-php">
/** 
* Building custom integration for Codereadr
*/

namespace CodeReadr;
use CodeReadr\Abstracts\Action;
use CodeReadr\Managers\Actions_Manager;


/**
* Let Codereadr know about my integration
*/
add_filter( 
	'codereadr_integrations',
	function( $integrations ) {
		$integrations['my-custom-integration'] = 'My Custom Integration'; 	

		return $integrations;
	}
);


/**
* Adding an action for this integration via the following action class
*
*/
if( !class_exists( 'Codereadr_My_Custom_Action' ) ) {
  class Codereadr_My_Custom_Action extends Action {

	/**
	* Action name.
	* It must be a unique name.
	*
	* @var string
	*
	*/
	public $name = 'my-custom-action';


	/**
	* Integration slug
	*
	* @var string
	*/
	public $integration_slug = 'my-custom-integration';

	/**
	* Action title.
	* The action title that will appear on admin dashboard.
	*
	* @var string
	*
	*/
	public $title = 'Redeam Ticket';

	/**
	* Default invalid conditions.
	*
	* @var array
	*/
	public $default_invalid_conditions = array(
	  'ticket_not_found' => array(
		'title'                 => 'If ticket is not found',
		'default'               => true,
		'default_response_text' => 'Ticket Not found',
	   ),
	);

	/**
	* Process action.
	* After processing any action we should set action data with a new value 
	* to be able to access it via handle_response method.
	*
	*/
	public function process_action( $scan_data, $meta ) {
	  $ticket_id = $scan_data['tid'];

	  // Remove the hash from the following line and insert your query.
	  // $is_ticket_found = Do  Your Query Here!
	  if ( ! $is_ticket_found ) {
		return array(
		  'status' => 0,
		  'text'   => $meta['default_invalid_conditions']['ticket_not_found']['response_text'],
		);
	  }
			
	  $success_reponse_text = $meta['success_response_txt'] );
	  return array(
		'status' => 1,
		'text'   => $success_reponse_text,
	  );


	  // At all cases you must return an array with 
	  // "status" key with value of 0 in case on invalid response  and 1 for valid response.
	  // And "text" for the response text.
	}
  }

  Actions_Manager::instance()->register( new Codereadr_My_Custom_Action() );
}

					</code>
				</pre>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($) {
		hljs.highlightAll();
	})
</script>
