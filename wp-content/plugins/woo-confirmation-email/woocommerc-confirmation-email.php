<?php
/**
 * Plugin Name:Woocommerce Email Verification
 * Plugin URI: http://www.sandeepsoni.work/woocommerce-confirmation-email/
 * Description: Verify user email address when user goes to registration 
 * Author: Sandeep
 * Author URI: http://www.sandeepsoni.work/
 * Version: 2.5.1
 * Text Domain: woocommerce-extension
 * Domain Path: /i18n/languages/    
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//include plugin_dir_path( __FILE__ )."admin/woocommerce-confirmation-email-admin.php";
class woocommerce_confirmation_email {

    public $my_account_id;
    private $user_id;
    private $email_id;
    private $plugin_slug;

    public function __construct() {
        @session_start();
        $this->my_account = get_option('woocommerce_myaccount_page_id');
        register_activation_hook(__FILE__, array($this, 'activate_plugins_wc_email'));
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_menu_page'));
        }
        add_action("woocommerce_login_redirect", array($this, "authorized_is_valid_user"), 10, 2);
        add_action("woocommerce_before_customer_login_form", array($this, "getcokkies_on_vrification"));
        add_shortcode('wcemailverificationcode', array($this, 'wc_email_verification_code'));
        add_action('wp_ajax_verify_code', array($this, 'verify_code'));
        add_action('wp_ajax_nopriv_verify_code', array($this, 'verify_code'));
        add_action('wp_ajax_resend_verify_code', array($this, 'resend_verify_code'));
        add_action('wp_ajax_nopriv_resend_verify_code', array($this, 'resend_verify_code'));
        add_filter('manage_users_columns', array($this, 'update_user_table'), 10, 1);
        add_filter('manage_users_custom_column', array($this, 'new_modify_user_table_row'), 10, 3);
        add_filter("woocommerce_registration_auth_new_customer", array($this, "new_user_registeration"), 10, 2);
        add_action("admin_head", array($this, "manual_verify_user"));
        add_action("wp", array($this, "authenticate_user_by_email"));
    }

    public function activate_plugins_wc_email() {
        ob_start();
        include plugin_dir_path(__FILE__) . "/view/demo_email.html";
        $demo_email_content = ob_get_clean();
        update_option("wc-email-header", $demo_email_content);
        update_option("wc_email_confemail", get_option("admin_email"));
        update_option("wc_email_conf_title", "Please Verify Your email Account");
    }

    public function add_menu_page() {
        add_menu_page('WC Email Setting', 'WC Email Setting', 'manage_options', 'wc-email-confirmation', array($this, 'add_admin_page'));
        add_submenu_page("wc-email-confirmation", "Bulk Email Verify", "Bulk Email Verify", 'administrator', 'wc_bulk_email_verification', array($this, "wc_bulk_email_verification"));
    }

    function add_admin_page() {
        include plugin_dir_path(__FILE__) . "/view/admin.php";
    }

    public function wc_bulk_email_verification() {
        ?>

        <style>
            .wc-email-confirmation{
                background: #fff;
                
                padding: 20px;

            }
            .wc-left {
                
                width:100%;
                float: left;
                padding: 5px
            }
            .wc-right{
                max-width: 350px;
                float: left;
                padding: 5px
            }
            .form-group {
                margin: 6px;
            }
            .form-group label {
                display: block;
                font-size: 18px;
                line-height: 29px
            }
            .form-group .wc-form-input {
                width: 100%;
                padding: 9px;
            }
        </style>
        <div class="form-group">
            <?php
            $userEmail = array();
            if (isset($_POST["wc_email_bulk_verification"])):

                $args = array('role' => $_POST["wc_email_user_role"]);
                $user_query = new WP_User_Query($args);
                $userEmail = array();
                if (!empty($user_query->results)) {
                    foreach ($user_query->results as $user) {
                        update_user_meta($user->ID, "wcemailverified", "true");
                        $userEmail[] = "verified";
                    }
                    ?>            
                    <div class="updated fade"><p><b><?php echo count($userEmail) ?> users is verified.</p></b></div>
                    <?php
                } else {
                    ?>
                    <div class="updated fade"><p><b><?php echo 0 ?> users is verified.</p></b></div> 
                    <?php
                }
            endif;
            ?>    

        </div>
        <div class="wc-email-confirmation">
            <h1>Woocommerce Email Verification (Bulk Verification)</h1>
            <div class="wc-left">


                <form class="" method="post">
                    <div class="form-group">
                        <label for="wc_email_title"><?php _e("Select User Role"); ?></label>
                        <select name="wc_email_user_role">
                            <?php wp_dropdown_roles(); ?>
                        </select>
                        <input type="Submit" name="wc_email_bulk_verification" class="button button-primary button-large" value="<?php _e("Verify"); ?>"/>
                    </div>
                </form>
            </div>

            <div class="clear" style="clear:both"></div>
        </div>
        <?php
    }

    public function codeMailSender($email) {
        $Email_title = get_option("wc_email_conf_title");
        $sender_email = get_option("wc_email_confemail");
        $message = get_option("wc-email-header");
        $header = "From: $Email_title <$sender_email> \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";
        $preMesaage = "<html><body><div style='width:700px;padding:5px;margin:auto;font-size:14px;line-height:18px'>" . apply_filters('the_content', $message) . "<div style='clear:both'></div></div></body></html>";
        //global $email
        do_action("woocommerce_confirmation_email_before_sending", $email, $Email_title, $preMesaage, $header);
        wp_mail($email, $Email_title, $preMesaage, $header);
    }

    public function new_user_registeration($customer, $user_id) {
        $current_user = get_user_by('id', $user_id);
        $this->user_id = $current_user->ID;
        $this->email_id = $current_user->user_email;
        $scret_code = md5($this->user_id . time());
        update_user_meta($user_id, "wcemailverifiedcode", $scret_code);
        $this->codeMailSender($current_user->user_email);
        wc_add_notice(__('Please check email inbox form confirmation email', 'woocommerce'), "notice");

        $_SESSION["verify_email"] = array("verify_email_user" => $user_id);
        //setcookie("verify_email", $user_meta[1], time() + 3600);
        wp_logout();
        wp_redirect(get_the_permalink($this->my_account));
        exit();
        return $customer;
    }

    function please_confirm_email_message() {
        $user = $_SESSION["verify_email"];
        $link = add_query_arg(array("wc_confirmation_resend" => base64_encode($user["verify_email_user"])), get_the_permalink($this->my_account));
        ?>
        <ul class="woocommerce-info">
            <li><strong>Confirm Email</strong>: Please Check Your Email. <a style='font-size:14px;color:red 'href="<?php echo $link ?>"> Resend Confirmation Email</a></li>
        </ul>
        <?php
    }

    function please_login_email_message() {
        ?>
        <ul class="woocommerce-info">
            <li><strong>Your Email Address is verified</strong>: Now You Can Login.</li>
        </ul>
        <?php
    }

    function authenticate_user_by_email() {
        if (isset($_GET["woo_confirmation_verify"])) {
            $user_meta = explode("@", base64_decode($_GET["woo_confirmation_verify"]));
            if (get_user_meta((int) $user_meta[1], "wcemailverifiedcode", TRUE) == $user_meta[0]) {
                update_user_meta((int) $user_meta[1], "wcemailverified", "true");
                unset($_SESSION["verify_email"]);
                $_SESSION["Show_login_message"] = array("verify_email_user" => $user_meta[1]);
            }
        }
        if (isset($_GET["wc_confirmation_resend"])) {
            $user_id = base64_decode($_GET["wc_confirmation_resend"]);
            $this->new_user_registeration(1, $user_id);
        }
    }

    /*
     * Deprecate Function not used
     * 
     */

    public function wc_email_verification_code() {
        $secret = get_user_meta($this->user_id, "wcemailverifiedcode", true);
        $createLink = $secret . "@" . $this->user_id;
        $hyperlink = add_query_arg(array("woo_confirmation_verify" => base64_encode($createLink)), get_the_permalink($this->my_account));

        //    $hyperlink = get_the_permalink($this->my_account) . "?woo_confirmation_verify=" . base64_encode($createLink);
        $link = "<a href='" . $hyperlink . "'> Click here to verify</a>";
        return $link;
        // return get_option("wc-email-header");
    }

    public function emailSecurityCode() {
        $scret_code = md5($this->user_id . time());
        update_user_meta($this->user_id, "wcemailverifiedcode", $scret_code);
        return $scret_code;
    }

    public function verify_code() {
        if (isset($_POST["action"])) {
            extract($_POST);
            $validation = array();
            if (get_user_meta((int) $user_id, "wcemailverifiedcode", TRUE) == $_POST["verifycode"]) {
                $validation["valid"] = 1;
                $validation["reload"] = 1;
                update_user_meta((int) $user_id, "wcemailverified", "true");
                if (apply_filters('woocommerce_registration_auth_new_customer', true, $user_id)) {
                    wc_set_customer_auth_cookie($user_id);
                }
                setcookie("emailverification_failed", $user_ID, time() - 3600);
            } else {
                $validation["valid"] = 0;
                $validation["reload"] = 0;
            }
            echo json_encode($validation);
        }
        exit();
    }

    public function resend_verify_code() {
        extract($_POST);
        $error["user_id"] = $user_id;
        $this->user_id = $user_id;
        $user = get_user_by("id", $user_id);
        $scret_code = md5($user->user_email . time());
        update_user_meta((int) $user_id, "wcemailverifiedcode", $scret_code);
        $this->codeMailSender($user->user_email);
        echo json_encode(array("resend" => 1));
        exit();
    }

    public function authorized_is_valid_user($redirect, $user) {
        $user_ID = $user->ID;
        $status = get_user_meta((int) $user_ID, "wcemailverified", true);
        if (!is_super_admin()) {
            if ($status != "true") {
                $myaccount_id = get_option('woocommerce_myaccount_page_id');
                $location = get_the_permalink($myaccount_id);
                wc_add_notice(__('Your email address is not verified please confirm your email address', 'woocommerce'), "notice");
                $_SESSION["verify_email"] = array("verify_email_user" => $user_ID);
                wp_logout();
                wp_redirect($location);
            } else {
                unset($_SESSION["verify_email"]);
                //  setcookie("emailverification_failed", $user_ID, time() - 3600);
                return $redirect;
            }
        } else {
            return $redirect;
        }
    }

    public function getcokkies_on_vrification() {

        if (isset($_SESSION["verify_email"])) {
            $this->please_confirm_email_message();
        }
        if (isset($_SESSION["Show_login_message"])) {
            unset($_SESSION["Show_login_message"]);
            $this->please_login_email_message();
        }
        global $post;
        ?>
        <script>
            window.history.pushState({"html": "", "pageTitle": "<?php echo get_the_title($post->ID); ?>"}, "", "<?php echo get_the_permalink($post->ID); ?>");
        </script>
        <?php
    }

    public function update_user_table($column) {
        $column['wc_verified'] = 'Verified user';
        $column['wc_manual_verified'] = 'Manual Verify';
        return $column;
    }

    public function new_modify_user_table_row($val, $column_name, $user_id) {
        $user_role = get_userdata($user_id);
        if ($column_name == "wc_verified") {

            if ($user_role->roles[0] != "administrator") {
                if (get_user_meta($user_id, "wcemailverified", true) == "true") {
                    return "<img src='" . plugin_dir_url(__FILE__) . "/images/right_arrow.png' width=20 height=20>";
                } else {
                    return "<img src='" . plugin_dir_url(__FILE__) . "/images/wrong_arrow.png' width=20 height=20>";
                }
            } else {
                return "Admin";
            }
        }

        if ($column_name == "wc_manual_verified") {
            if ($user_role->roles[0] != "administrator") {
                if (get_user_meta($user_id, "wcemailverified", true) != "true") {
                    //$_GET["wc_confirm"]="true";
                    $text = "Verify This";
                    return "<a href=" . add_query_arg(array("user_id" => $user_id, "wp_nonce" => wp_create_nonce("wc_email"), "wc_confirm" => "true"), get_admin_url() . "users.php") . ">" . apply_filters("wc_email_confirmation_manual_verify", $text) . "</a>";
                }
            }
        }
    }

    public function manual_verify_user() {
        //    var_dump(wp_verify_nonce($_GET["wp_nonce"], "wc_email"));
        if (isset($_GET["user_id"]) && wp_verify_nonce($_GET["wp_nonce"], "wc_email")) {
            update_user_meta($_GET["user_id"], "wcemailverified", "true");
        }
    }

}

function add_notice_when_woocommerc_not_installed() {
    ?>
    <div class="error">
        <p><?php _e('Woocommerce email Confirmation: Error Please Install Woocommerce First', 'woocommerce   '); ?></p>
    </div>
    <?php
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    new woocommerce_confirmation_email();
} else {
    add_action('admin_notices', 'add_notice_when_woocommerc_not_installed');
}
    