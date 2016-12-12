<?php

/** @var \FitSpokane\Controller $fit_spokane_controller */
global $fit_spokane_controller;

if ( strlen( $fit_spokane_controller->get_attribute( 'kitchen_rescue_pak' ) ) > 0 )
{
    include( 'kitchen-rescue-pak.php' );
    return;
}

$id = uniqid();
$stripe_keys = \FitSpokane\Controller::getStripeKeys();
$title = ( strlen( get_option( 'fit_spokane_company_name', '' ) ) > 0 ) ? get_option( 'fit_spokane_company_name' ) : get_option( 'blogname' );
$logo = get_option( 'fit_spokane_company_logo', '' );
$currency = 'USD';
$suppress = ( get_option( 'fit_spokane_suppress_https_warning' ) == 1 ) ? TRUE : FALSE;

\FitSpokane\Stripe\Stripe::setApiKey( $stripe_keys['secret'] );

$program_id = $this->get_attribute( 'program' );
$program = new FitSpokane\Program( $program_id );

?>

<?php if ( isset( $_POST['fit_spokane_action'] ) && ! isset( $fit_spokane_error_posted ) ) { ?>

	<div class="fit-spokane-alert" id="fit-spokane-error-<?php echo $id; ?>" data-id="freezy-a-error-<?php echo $id; ?>" style="display:none">
		<div class="alert alert-danger">
			<?php _e( 'There was a problem charging your credit card.', 'fit-spokane' ); ?>
		</div>
	</div>
	<a href="#TB_inline?width=600&height=550&inlineId=fit-spokane-error-<?php echo $id; ?>" id="freezy-a-error-<?php echo $id; ?>" class="thickbox"></a>

	<?php $fit_spokane_error_posted = TRUE; ?>

<?php } ?>

<?php if ( ! isset( $_POST['fit_spokane_action'] ) && isset( $_GET['fit_spokane'] ) && $_GET['fit_spokane'] == 'success' && ! isset( $fit_spokane_success_posted ) ) { ?>

	<div class="fit-spokane-alert" id="fit-spokane-success-<?php echo $id; ?>" data-id="fit-spokane-a-success-<?php echo $id; ?>" style="display:none">
		<div class="alert alert-success">
			<?php _e( 'Success! Your card was charged.', 'fit-spokane' ); ?>
		</div>
	</div>
	<a href="#TB_inline?width=300&height=200&inlineId=fit-spokane-success-<?php echo $id; ?>" id="fit-spokane-a-success-<?php echo $id; ?>" class="thickbox"></a>

	<?php $fit_spokane_success_posted = TRUE; ?>

<?php } ?>

<?php if ( strlen( $stripe_keys['pub'] ) > 0 && strlen( $stripe_keys['secret'] ) > 0 && $program->isVisible() && $program->getPrice() > 0 ) { ?>

	<script>

		if (typeof fit_spokane_handlers === 'undefined') {
			var fit_spokane_handlers = [];
		}

		var fit_spokane_handler = StripeCheckout.configure({
			key: '<?php echo $stripe_keys['pub']; ?>',
			<?php if ( strlen( $logo ) > 0 ) { ?>
				image: '<?php echo $logo; ?>',
			<?php } ?>
			locale: 'auto',
			token: function(token) {
				jQuery('#fit-spokane-token-<?php echo $id; ?>').val(token.id);
				jQuery('#fit-spokane-form-<?php echo $id; ?>').submit();
			}
		});

		fit_spokane_handlers.push({
			id: '<?php echo $id; ?>',
			handler: fit_spokane_handler
		});

		// Close Checkout on page navigation
		jQuery(window).on('popstate', function() {
			for (var h=0; h<fit_spokane_handlers.length; h++) {
				fit_spokane_handlers[h].handler.close();
			}
		});

	</script>

	<div class="fit-spokane-form-container">

		<form method="post" autocomplete="off" id="fit-spokane-form-<?php echo $id; ?>">
			<?php wp_nonce_field( 'fit_spokane_nonce' ); ?>
			<input type="hidden" name="fit_spokane_action" value="charge">
			<input type="hidden" name="token" id="fit-spokane-token-<?php echo $id; ?>">
			<input type="hidden" name="id" value="<?php echo $program->getId(); ?>">
			<strong>
				<?php echo $program->getTitle() ; ?>
			</strong>
			<br>
			<em>
				$<?php echo number_format( $program->getPrice(), 2 ); ?>
				<?php if ( $program->isRecurring() && $program->getRecurPeriod() > 1 ) { ?>
				/ month
				<br>for <?php echo $program->getRecurPeriod(); ?> months
				<?php } ?>
			</em>
			<input type="hidden" name="price" id="fit-spokane-price-<?php echo $id; ?>" value="<?php echo round( $program->getPrice() * 100 ); ?>">
			<br>
			<button
				type="submit"
				class="fit-spokane-submit"
				data-currency="usd"
				data-name="<?php echo esc_html( $title ); ?>"
				data-description="<?php echo esc_html( $program->getTitle() ); ?>"
				data-id="<?php echo $id; ?>">
				<?php _e( 'Purchase Now', 'fit-spokane' ); ?>
			</button>

			<?php if ( ! $suppress) { ?>
				<div class="fit-spokane-ssl-check" data-if-error-show="<?php _e( 'Warning: Your payment may not be secure.<br>Always check for HTTPS in the URL.', 'fit-spokane' ); ?>"></div>
			<?php } ?>

		</form>

	</div>
<?php } ?>
