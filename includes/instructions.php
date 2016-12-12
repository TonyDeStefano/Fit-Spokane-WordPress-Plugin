<?php

/**
 * @var \FitSpokane\Controller $this
 */

?>

<div class="wrap">

	<h1>
		Fit Spokane <?php _e( 'Settings', 'fit-spokane' ); ?>
	</h1>

	<form method="post" action="options.php" autocomplete="off">

		<?php

		settings_fields('fit_spokane_settings');
		do_settings_sections( 'fit_spokane_settings' );
		$suppress = ( get_option( 'fit_spokane_suppress_https_warning' ) == 1 ) ? 1 : 0;

		?>

		<table class="form-table">
			<thead>
				<tr>
					<th></th>
					<th><?php _e('Current Value', 'fit-spokane'); ?></th>
					<th><?php _e('Change to', 'fit-spokane'); ?></th>
				</tr>
			</thead>
			<tr>
				<td colspan="2">
					<?php _e('In order to accept credit card payments on the website, you must fill in the API keys. They can be found in your Stripe account by clicking on "My Account" (usually in the top-right corner of your account) and then choosing the "API Keys" icon', 'fit-spokane'); ?>
				</td>
			</tr>
            <tr valign="top">
                <th scope="row">
                    <label for="fit_spokane_company_name">
						<?php _e( 'MailChimp API Key', 'fit-spokane' ); ?>
                    </label>
                </th>
				<td><?php echo $this->getMailChimpApiKey(); ?></td>
                <td><input type="text" id="fit_spokane_mailchimp_api_key" name="fit_spokane_mailchimp_api_key" value="<?php echo $this->getMailChimpApiKey(); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="fit_spokane_company_name">
						<?php _e( 'MailChimp API URL', 'fit-spokane' ); ?>
                    </label>
                </th>
                <td><?php echo $this->getMailChimpApiUrl(); ?></td>
                <td><input type="text" id="fit_spokane_mailchimp_api_url" name="fit_spokane_mailchimp_api_url" value="<?php echo $this->getMailChimpApiUrl(); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="fit_spokane_company_name">
						<?php _e( 'MailChimp List ID', 'fit-spokane' ); ?>
                    </label>
                </th>
                <td><?php echo $this->getMailChimpListId(); ?></td>
                <td><input type="text" id="fit_spokane_mailchimp_list_id" name="fit_spokane_mailchimp_list_id" value="<?php echo $this->getMailChimpListId(); ?>" /></td>
            </tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_test_secret_key">
						Stripe Test Secret Key
					</label>
				</th>
				<td><?php echo get_option( 'fit_spokane_test_secret_key', '<span style="color:red">' . __( 'NOT SET', 'fit-spokane' ) . '</span>' ) ; ?></td>
				<td><input type="text" id="fit_spokane_test_secret_key" name="fit_spokane_test_secret_key" value="<?php echo esc_attr( get_option('fit_spokane_test_secret_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_test_pub_key">
						Stripe Test Publishable Key
					</label>
				</th>
				<td><?php echo get_option('fit_spokane_test_pub_key', '<span style="color:red">' . __( 'NOT SET', 'fit-spokane' ) . '</span>' ) ; ?></td>
				<td><input type="text" id="fit_spokane_test_pub_key" name="fit_spokane_test_pub_key" value="<?php echo esc_attr( get_option('fit_spokane_test_pub_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_live_secret_key">
						Stripe Live Secret Key
					</label>
				</th>
				<td><?php echo get_option( 'fit_spokane_live_secret_key', '<span style="color:red">' . __( 'NOT SET', 'fit-spokane' ) . '</span>' ); ?></td>
				<td><input type="text" id="fit_spokane_live_secret_key" name="fit_spokane_live_secret_key" value="<?php echo esc_attr( get_option('fit_spokane_live_secret_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_live_pub_key">
						Stripe Live Publishable Key
					</label>
				</th>
				<td><?php echo get_option( 'fit_spokane_live_pub_key', '<span style="color:red">' . __( 'NOT SET', 'fit-spokane' ) . '</span>' ); ?></td>
				<td><input type="text" id="fit_spokane_live_pub_key" name="fit_spokane_live_pub_key" value="<?php echo esc_attr( get_option('fit_spokane_live_pub_key') ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_mode">
						<?php _e( 'Mode', 'fit-spokane' ); ?>
					</label>
				</th>
				<td>
					<?php echo ( get_option( 'fit_spokane_mode' ) == 'live' ) ? __( 'Live Mode', 'fit-spokane' ) : __( 'Test Mode', 'fit-spokane' ) ?>
				</td>
				<td>
					<select id="fit_spokane_mode" name="fit_spokane_mode">
						<option value="live"<?php if ( get_option( 'fit_spokane_mode' ) == 'live' ) { ?> selected<?php } ?>>
							<?php _e( 'Live Mode', 'fit-spokane' ); ?>
						</option>
						<option value="test"<?php if ( get_option('fit_spokane_mode') != 'live' ) { ?> selected<?php } ?>>
							<?php _e( 'Test Mode', 'fit-spokane' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_suppress_https_warning">
						<?php _e( 'Suppress HTTPS Warning', 'fit-spokane' ); ?>
						<span style="color:red">***</span>
					</label>

				</th>
				<td>
					<?php echo ( $suppress == 1 ) ? __( 'Yes - Not Recommended', 'fit-spokane' ) : __( 'No', 'fit-spokane' ); ?>
				</td>
				<td>
					<select id="fit_spokane_suppress_https_warning" name="fit_spokane_suppress_https_warning">
						<option value="0"<?php if ($suppress == 0) { ?> selected<?php } ?>>
							<?php _e( 'No - Recommended', 'fit-spokane' ); ?>
						</option>
						<option value="1"<?php if ($suppress == 1) { ?> selected<?php } ?>>
							<?php _e( 'Yes - Not Recommended', 'fit-spokane' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<span style="color:red">***</span>
					<?php

					_e( 'By default, a warning message will show up if your website is not secure. If you choose to turn this off and use this plugin on a non-secure website, please be aware that your transactions are not 100% safe. ' , 'fit-spokane');
					_e( 'There are several options for securing your website. The best option is to talk to your hosting company about getting an SSL certificate. Another alternative is to use the free SSL service available at ', 'fit-spokane' );

					?>
					<a href="http://cloudflare.com" target="_blank">cloudflare.com</a>.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_company_name">
						<?php _e( 'Company Name', 'fit-spokane' ); ?>
					</label>
				</th>
				<?php $title = ( strlen( get_option( 'fit_spokane_company_name', '' ) ) > 0 ) ? get_option( 'fit_spokane_company_name' ) : get_option( 'blogname' ); ?>
				<td><?php echo $title; ?></td>
				<td><input type="text" id="fit_spokane_company_name" name="fit_spokane_company_name" value="<?php echo esc_attr( $title ); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="fit_spokane_company_logo">
						<?php _e( 'Logo', 'fit-spokane' ); ?>
					</label>
				</th>
				<td>
					<div id="fit-spokane-company-logo">
						<?php if ( get_option( 'fit_spokane_company_logo', '' ) != '' ) { ?>
							<img src="<?php echo get_option( 'fit_spokane_company_logo' ); ?>">
						<?php } else { ?>
							<?php _e( 'NOT SET', 'fit-spokane' ); ?>
						<?php } ?>
					</div>
				</td>
				<td>
					<input type="hidden" name="fit_spokane_company_logo" id="fit_spokane_company_logo" value="<?php echo esc_attr( get_option( 'fit_spokane_company_logo' ) ); ?>">
					<input id="fit-spokane-upload-logo" class="button-primary" value="<?php _e( 'Add Logo', 'fit-spokane' ); ?>" type="button">
					<input id="fit-spokane-remove-logo" class="button-secondary" value="<?php _e( 'Remove Logo', 'fit-spokane' ); ?>" type="button" <?php if ( get_option( 'fit_spokane_company_logo', '' ) == '' ) { ?> style="display:none"<?php } ?>>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>

</div>
