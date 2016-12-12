<?php

/** @var \WP_Post $post */
global $post;

$program = new \FitSpokane\Program( $post->ID );

?>

<table class="form-table">
	<tr>
		<th>
			<label for="fit-spokane-program-is-active">
				Status:
			</label>
		</th>
		<td>
			<select name="is_visible" id="fit-spokane-program-is-active">
				<option value="1"<?php if ( $program->isVisible() ) { ?> selected<?php } ?>>
					ON
				</option>
				<option value="0"<?php if ( ! $program->isVisible() ) { ?> selected<?php } ?>>
					OFF
				</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>
			<label for="fit-spokane-program-price">
				<span style="color:red;">*</span>Price:
			</label>
		</th>
		<td>
			<input name="price" id="fit-spokane-program-price" value="<?php echo ( $program->getPrice() > 0 ) ? number_format( $program->getPrice(), 2 ) : ''; ?>" placeholder="ex: $99.99">
		</td>
	</tr>
	<tr>
		<th></th>
		<td>
			<span style="color:red;">*</span> If this is a recurring payment, enter the price for each payment here
		</td>
	</tr>
	<tr>
		<th>
			<label for="fit-spokane-program-is-recurring">
				Recurring Payment:
			</label>
		</th>
		<td>
			<select name="is_recurring" id="fit-spokane-program-is-recurring">
				<option value="1"<?php if ( $program->isRecurring() ) { ?> selected<?php } ?>>
					YES
				</option>
				<option value="0"<?php if ( ! $program->isRecurring() ) { ?> selected<?php } ?>>
					NO
				</option>
			</select>
		</td>
	</tr>
	<tr id="tr-recur-period" style="display:none;">
		<th>
			<label for="fit-spokane-program-recur-period">
				How Many Months?
			</label>
		</th>
		<td>
			<input name="recur_period" id="fit-spokane-program-recur-period" value="<?php echo ( $program->getRecurPeriod() > 0 ) ? number_format( $program->getRecurPeriod() ) : ''; ?>" placeholder="12">
		</td>
	</tr>
</table>

<script>
	jQuery(function(){
		fitSpokaneChangeRecurring();
	})
</script>
