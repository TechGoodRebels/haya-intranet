<?php

function get_compared_colors( $results, $compare_results, $date_different ) {
	if ( $compare_results != 0 ) {
		$compare = number_format( ( ( $results - $compare_results ) / $compare_results ) * 100, 2 ) . "%";
	} else {
		return;
	}

	return array(
		$compare > 0 ? '#00c853' : '#fa5825',
		$compare > 0 ? '#4ed98817' : '#ffffff'
	);
}

function pa_email_include_single_general( $current, $stats, $old_stats, $date_different ) {

	ob_start();
	?>

	<!-- <tr>
		<td valign="top" style="border: 1px solid #e2e5e8;">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#f9fafa">
				<tr>
					<td style="font: normal 16px 'Roboto slab', Arial, Helvetica, sans-serif; padding: 11px 20px;"><font color="#444444"><?php // analytify_e( 'General Statistics', 'wp-analytify' ) ?></font></td>
				</tr>
			</table>
		</td>
	</tr> -->

	<tr>
		<td bgcolor="#ffffff"  class="session-table">
			<table cellspacing="20" cellpadding="0" border="0" align="center" bgcolor="#f9fafa" width="100%" class="box-table">
				<tr>
					<td style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php analytify_e( 'Sessions', 'wp-analytify' ) ?></font></td>
							</tr>
							<tr>
								<td width="45" ></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo $stats->totalsForAllResults['ga:sessions']  ?></font></td>
							</tr>
						</table>
					</td>
					<td  style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php analytify_e( 'Visitors', 'wp-analytify' ) ?></font></td>
							</tr>
							<tr>
								<td width="45"></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:users'] ) ?></font></td>
							</tr>

						</table>
					</td>

					<td  style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php analytify_e( 'Page View', 'wp-analytify' ) ?></font></td>
							</tr>
							<tr>
								<td width="45"></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:pageviews'] ) ?></font></td>
							</tr>

						</table>
					</td>
				</tr>
				<tr>
					<td  style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php _e( 'Avg. time on page', 'wp-analytify-email' ) ?></font></td>
							</tr>
							<tr>
								<td width="45"></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_time( $stats->totalsForAllResults['ga:avgTimeOnPage'] ) ?></font></td>
							</tr>

						</table>
					</td>

					<td  style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php _e( 'Bounce Rate', 'wp-analytify-email' ) ?></font></td>
							</tr>
							<tr>
								<td width="45"></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo number_format( $stats->totalsForAllResults['ga:bounceRate'] ) ?>%</font></td>
							</tr>
						</table>
					</td>
					<td style="border: 1px solid #e2e5e8;" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff">
							<tr>
								<td align="center" colspan="3" style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;"><font color="#848484"><?php analytify_e( '% New Session', 'wp-analytify' ) ?></font></td>
							</tr>
							<tr>
								<td width="45" ></td><td align="center"><hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;"/></td><td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:percentNewSessions'] ) ?>%</font></td>
							</tr>



						</table>
					</td>
				</tr>

			</table>
		</td>
	</tr>

	<tr>
		<td>
			<table cellpadding="0" cellspacing="16px" border="0" width="100%" bgcolor="#f9fafa">
				<tr>
					<td width="32" style="text-align: right;"><img src="<?php echo ANALYTIFY_IMAGES_PATH . "anlytify_about_icon.png" ?>" alt=""></td>
					<td style="font: normal 13px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php analytify_e( 'Did you know that total time on your page is', 'wp-analytify' ) ?> <?php echo WPANALYTIFY_Utils::pretty_time( $stats->totalsForAllResults['ga:avgSessionDuration'] ) ?></font></td>
				</tr>
			</table>
		</td>
	</tr>

	<?php if ( ! class_exists( 'WP_Analytify_Email' ) ) : ?>
		<tr>
			<td valign="top" class="analytify-promo-inner-table" style="padding: 30px 45px;">
				<table style="margin: 0 auto;" cellspacing="0" cellpadding="0" width="100%" align="center">
					<tbody><tr>
						<td valign="top" colspan="2" style="font-size: 26px; font-family: 'Roboto'; font-weight: bold; line-height: 26px; padding-bottom: 24px;" align="center " class="analytify-promo-heading"><font color="#313133">Customize weekly and monthly reports</font> </td>
					</tr>
					<tr>
						<td valign="top" colspan="2" style="font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px; padding-bottom: 15px;"><font color="#383b3d">Email notifications add-on extends the Analytify Pro, and enables more control on customizing Analytics Email reports for your websites, delivers Analytics summaries straight in your inbox weekly and monthly.</font></td>
					</tr>
					<tr>
						<td valign="top" class="analytify-promo-lists" width="40%">
							<table cellspacing="0" cellpadding="0" width="100%" align="center">
								<tbody><tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Add your logo</font></td>
								</tr>
								<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Edit Email Subject</font></td>
								</tr>
								<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Choose your own metrics to display in reports</font></td>
								</tr>
						</tbody></table>
					</td>
					<td valign="top" class="analytify-promo-lists" width="52%" style="padding-left: 8%;">
						<table cellspacing="0" cellpadding="0" width="100%" align="center">
							<tbody><tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Add personal note</font></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Schedule weekly reports</font></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d">Schedule monthly reports</font></td>
							</tr>
						</tbody></table>
					</td>
					</tr> 
					<?php if ( class_exists( 'WP_Analytify_Pro_Base' ) ) : ?>
						<tr>
							<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_html( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-pro&utm_medium=email-reports&utm_content=cta&utm_campaign=addons-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/c29b00f7-b5fa-4e04-9a28-e9d77c69ba15.png" alt="Buy Email Notifications addon"></a></td>
						</tr>
					<?php else : ?>
						<tr>
							<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_html( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-lite&utm_medium=email-reports&utm_content=cta&utm_campaign=bundle-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/3c067584-abb3-4c6b-8c28-4cc265e67bfa.png" alt="Upgrade to Analytify Pro + Email Notifications bundle" class="analytify-update-pro"></a></td>
						</tr>
					<?php endif; ?>
			</tbody></table>
		</td>
	</tr>
	<?php endif; ?>

	<tr>
		<td style="padding:15px;"></td>
	</tr>
	<?php
	$message = ob_get_clean();
	return $message;
}
?>
