<?php

/** @var \WP_Post $post */
global $post;

$coupon = new \FitSpokane\Coupon( $post->ID );
$programs = \FitSpokane\Program::getAllPrograms();

?>

<table class="form-table">
	<tr>
		<th>
			<label for="fit-spokane-coupon-code">
				<span style="color:red;">*</span>Coupon Code:
			</label>
		</th>
		<td>
			<input name="code" class="form-control" id="fit-spokane-coupon-code" value="<?php echo esc_html( $coupon->getCode() ); ?>">
		</td>
	</tr>
    <tr>
        <th>
            <label for="fit-spokane-coupon-amount-off">
                Amount Off:
            </label>
        </th>
        <td>
            <input name="amount_off" class="form-control" id="fit-spokane-coupon-amount-off" value="$<?php echo number_format( $coupon->getAmountOff(), 2 ); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="fit-spokane-coupon-percent-off">
                Percent Off:
            </label>
        </th>
        <td>
            <input name="percent_off" class="form-control" id="fit-spokane-coupon-percent-off" value="<?php echo number_format( $coupon->getPercentOff() ); ?>%">
        </td>
    </tr>
    <tr>
        <th>
            <label for="fit-spokane-coupon-starts-at">
                Start Date:
            </label>
        </th>
        <td>
            <input name="starts_at" class="form-control" id="fit-spokane-coupon-starts-at" value="<?php echo $coupon->getStartsAt( 'n/j/Y' ); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="fit-spokane-coupon-ends-at">
                Expiration Date:
            </label>
        </th>
        <td>
            <input name="ends_at" class="form-control" id="fit-spokane-coupon-ends-at" value="<?php echo $coupon->getEndsAt( 'n/j/Y' ); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="fit-spokane-coupon-program-ids">
                Programs Valid For:
            </label>
        </th>
        <td>

            <input type="checkbox" name="program_id_0" value="0"<?php if ( $coupon->isValidForAllPrograms() ) { ?> checked<?php } ?>>
            All Programs

            <?php foreach ( $programs as $program ) { ?>
                <br>
                <input type="checkbox" name="program_id_<?php echo $program->getId(); ?>" value="<?php echo $program->getId(); ?>"<?php if ( ! $coupon->isValidForAllPrograms() && in_array( $program->getId(), $coupon->getProgramIds() ) ) { ?> checked<?php } ?>>
                <?php echo $program->getTitle(); ?>
            <?php } ?>

        </td>
    </tr>
</table>
