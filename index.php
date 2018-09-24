<div class="wpcfur">
	<div id="response"></div>

	<div id="loading">
		<i class="fa fa-cog fa-spin fa-3x fa-fw"></i> <span>Please wait&hellip;</span>
	</div>

	<input type="hidden" name="wpcfur_nonce" id="wpcfur-nonce" value="<?php echo wp_create_nonce('wpcfur_register'); ?>">

	<script>
		(function($) {
			$(document).ready(function() {
				var cfName = window.localStorage.getItem('garlic:<?php echo $site_host; ?>*>input.name'),
					cfEmail = window.localStorage.getItem('garlic:<?php echo $site_host; ?>*>input.email');

				if (cfName == '' || cfEmail == '') {
					window.location.href = '<?php echo get_site_url(); ?>';
				}

				$.ajax({
					url: '<?php echo admin_url('admin-ajax.php'); ?>',
					method: 'POST',
					dataType: 'text',
					data: {
						full_name: cfName,
						email_address: cfEmail,
						nonce: $('#wpcfur-nonce').val(),
						action: 'wpcfur_register'
					},
					beforeSend: function() {
					},
					success: function(response) {
						$('#loading').hide();

						if (response == 'email_exists') {
							$('#response').html("<div class='error'><i class='fa fa-exclamation-triangle'></i> The email address you've entered is already in use. <a href='<?php echo $_SERVER['HTTP_REFERER']; ?>'>Click here to try again.</a></div>").show();
						} else if (response == 'success') {
							$('#response').html("<div class='info'><i class='fa fa-refresh fa-spin fa-3x fa-fw'></i>You're being redirected&hellip;</div>").show();

							location.href = '<?php echo $redirect_url; ?>';
						}
					}
				});
			});
		})(jQuery);
	</script>
</div>
