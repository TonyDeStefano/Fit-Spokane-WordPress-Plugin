<?php

global $post;
$custom = get_post_custom( $post->ID );
$payment_for = ( array_key_exists( 'payment_for', $custom ) ) ? $custom[ 'payment_for' ][0] : '';
$price = ( array_key_exists( 'price', $custom ) ) ? $custom[ 'price' ][0] : 0;
$email = ( array_key_exists( 'email', $custom ) ) ? $custom[ 'email' ][0] : '';
$address = ( array_key_exists( 'address', $custom ) ) ? $custom[ 'address' ][0] : '';
$city = ( array_key_exists( 'city', $custom ) ) ? $custom[ 'city' ][0] : '';
$state = ( array_key_exists( 'state', $custom ) ) ? $custom[ 'state' ][0] : '';
$zip = ( array_key_exists( 'zip', $custom ) ) ? $custom[ 'zip' ][0] : '';

?>

<table class="form-table">
	<tr>
		<th>
			<label for="freezy_payment_for">
				<?php echo __( 'Product or Service', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_payment_for" id="freezy_payment_for" value="<?php echo esc_html( $payment_for ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_price">
				<?php echo __( 'Price', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_price" id="freezy_price" value="<?php echo esc_html( $price ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_email">
				<?php echo __( 'Email', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_email" id="freezy_email" value="<?php echo esc_html( $email ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_address">
				<?php echo __( 'Address', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_address" id="freezy_address" value="<?php echo esc_html( $address ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_city">
				<?php echo __( 'City', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_city" id="freezy_city" value="<?php echo esc_html( $city ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_state">
				<?php echo __( 'State', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_state" id="freezy_state" value="<?php echo esc_html( $state ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label for="freezy_zip">
				<?php echo __( 'Zip', 'fit-spokane' ); ?>:
			</label>
		</th>
		<td>
			<input name="freezy_zip" id="freezy_zip" value="<?php echo esc_html( $zip ); ?>">
		</td>
	</tr>
</table>
