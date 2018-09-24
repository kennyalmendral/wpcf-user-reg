<div class="wpcfur-admin wrap">
	<h1>WordPress ClickFunnels User Registration</h1>

	<?php
		if (isset($_POST['submit']) && empty($error)) {
			wpcfur_show_success_message();
		} else if ( ! empty($error)) {
			wpcfur_show_error_message($error);
		}

		if ( ! is_plugin_active('clickfunnels/clickfunnels.php')) {
			$clickfunnels_plugin_url = get_site_url() . '/wp-admin/plugin-install.php?s=ClickFunnels+Etison%2C+LLC&tab=search&type=term';
			wpcfur_show_warning_message('Install, activate and configure the <a href="' . $clickfunnels_plugin_url . '">ClickFunnels</a> plugin first in order for this plugin to work.');
		}
	?>
	
	<form action="" method="POST" enctype="multipart/form-data">
		<table class="form-table">
			<tbody>
				<tr>
					<th colspan="2"><p class="section-title">GENERAL SETTINGS</p></th>
				</tr>

				<tr>
					<th><label>Register URL</label></th>
					<td>
						<code><?php echo $register_url; ?></code>
						<p class="description">Put this URL on the ClickFunnels Form's <strong>ON SUBMIT GO TO</strong> field located under <strong>General Settings</strong>.</p>
					</td>
				</tr>

				<tr>
					<th><label for="wpcfur-redirect-url">Redirect URL <em>(required)</em></label></th>
					<td>
						<input type="url" name="wpcfur_redirect_url" id="wpcfur-redirect-url" class="regular-text" value="<?php echo $redirect_url; ?>">
						<p class="description">Users will be redirected here when they are registered on the site successfully. Defaults to <strong><?php echo get_site_url(); ?></strong> if not specified.</p>
					</td>
				</tr>

				<tr>
					<th><label for="wpcfur-default-password">Default Password <em>(required)</em></label></th>
					<td>
						<input type="password" name="wpcfur_default_password" id="wpcfur-default-password" class="regular-text" value="<?php echo $default_password; ?>">
						<p class="description">This will be the password that will be sent and used by the user upon successful registration. Defaults to <strong>wpcfur_pass</strong> if not specified.</p>
					</td>
				</tr>

				<tr>
					<td colspan="2"><hr></td>
				</tr>

				<tr>
					<th colspan="2"><p class="section-title">EMAIL SETTINGS</p></th>
				</tr>

				<tr>
					<th><label for="wpcfur-email-subject">Email Subject <em>(required)</em></label></th>
					<td>
						<input type="text" name="wpcfur_email_subject" id="wpcfur-email-subject" class="regular-text" value="<?php echo stripslashes($email_subject); ?>">
						<p class="description">Defaults to <strong>Here's your login credentials</strong> if not specified.</p>
					</td>
				</tr>

				<tr>
					<th><label for="wpcfur-email-template">Email Template <em>(required)</em></label></th>
					<td>
						<textarea name="wpcfur_email_template" id="wpcfur-email-template" class="widefat" rows="30"><?php echo stripslashes($email_template); ?></textarea>
						<p class="description">Placeholders: <code>[name]</code>, <code>[email]</code>, <code>[password]</code></p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('wpcfur_update_settings'); ?>">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>
