# WordPress ClickFunnels User Registration
Capture user data from a ClickFunnels form to be used for registration on a WordPress site.

In order for this plugin to work, the [ClickFunnels](https://wordpress.org/plugins/clickfunnels/) WordPress plugin must be installed, activated and configured first (see below).

## ClickFunnels Plugin Configuration

1. Go to the plugin's **Settings** section and fill-up the **Account Email** and **Authentication Token** fields under **API Connection**.
2. Go to the plugin's **Pages** section and click the **Add New** button.
3. Under **Choose Page Type**, select either `Regular Page` or `Home Page`.
4. Under **Choose Funnel**, select the funnel to be used.
5. Under **Choose Step**, select the funnel form to be used.
6. Under **Custom Slug**, enter the slug that you want to be used, e.g., `optin`.
7. Click the **Save Page** button.
8. Go back to the plugin's **Pages** section and click the **View Page** link under the **View** column of the newly created page . _This will be the page that needs to be presented to the user, in other words, the user data that will be submitted must come from this page._

## WordPress ClickFunnels User Registration Plugin Configuration

1. Install and activate the plugin.
2. Go to the plugin's **Settings** page and copy the value of the **Register URL** field and paste it on the ClickFunnels Form's **ON SUBMIT GO TO** field located under **General Settings** _(This field can be found on the ClickFunnels' Page Builder)_.
3. Under **Redirect URL**, specify a URL that will be used as a thank you page upon successful registration.
4. Open the active theme's `function.php` file, scroll at the very bottom and paste the following code snippet:

```php
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (is_plugin_active('wpcf-user-reg/wpcf-user-reg.php')) {
    function wpcfur_register() {
        if (wp_verify_nonce($_POST['nonce'], 'wpcfur_register')) {
            global $wpdb;

            $full_name = trim($_POST['full_name']);

            $split_full_name = explode(' ', $_POST['full_name']);
            $first_name = trim($split_full_name[0]);
            $last_name = trim($split_full_name[1]);

            $email_address = trim($_POST['email_address']);

            $default_password = trim(get_option('wpcfur_default_password'));

            $check_email = count($wpdb->get_row("SELECT ID FROM {$wpdb->prefix}users WHERE user_email = '$email_address'"));

            if ($check_email) {
                $response = 'email_exists';
            } else {
                $userdata = array(
                    'user_login' => $email_address,
                    'user_email' => $email_address,
                    'user_pass' => $default_password,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'display_name' => $full_name
                );

                $user_id = wp_insert_user($userdata);

                $headers[] = 'Content-Type: text/html; charset=UTF-8';

                $subject = trim(get_option('wpcfur_email_subject'));
                $template = trim(get_option('wpcfur_email_template'));

                $template_placeholders = array(
                    '[name]', 
                    '[password]',
                    '[email]'
                );

                $template_replacements = array(
                    $full_name,
                    $default_password,
                    $email_address
                );

                $message = str_replace($template_placeholders, $template_replacements, $template);

                wp_mail($email_address, stripslashes($subject), stripslashes($message), $headers);

                $response = 'success';
            }

            die($response);
        }
    }

    add_action('wp_ajax_wpcfur_register', 'wpcfur_register');
    add_action('wp_ajax_nopriv_wpcfur_register', 'wpcfur_register');
}
```
