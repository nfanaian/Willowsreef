<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox');
?>
<style>
    .wc-email-confirmation{
        background: #fff;
      //  max-width: 1000px;
        padding: 20px;

    }
    .wc-left {
    //    max-width: 650px;
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

<div class="wc-email-confirmation">
    <h1>Woocommerce Email Verification Template Settings</h1>
    <?php
    if (isset($_POST["wc_email_save"])) {
        foreach ($_POST as $wc_em_key => $value) {
            update_option($wc_em_key, stripslashes($value));
        }
        echo "<div style='color:green'>Template Is saved</div>";
    }
  //  echo do_shortcode("[wcemailverificationcode]");
    ?>
    <div class="wc-left">
        <form class="" method="post">
            <div class="form-group">
                <label for="wc_email_title"><?php _e("Email Subject Title"); ?></label>
                <input type="text" name="wc_email_conf_title" value="<?php echo get_option("wc_email_conf_title"); ?>" id="wc_email_title" class="wc-form-input" placeholder="Verification code"/>
            </div>
            <div class="form-group">
                <label for="wc_email_confemail"><?php _e("Sender Email")?></label>
                <input type="email" name="wc_email_confemail" value="<?php echo get_option("wc_email_confemail") ?>"id="wc_email_confemail" class="wc-form-input"/>
            </div>
          
            <div class="form-group">
                <label for="wc_email_confemail"><?php _e("Email Template Content");?></label>
                <?php wp_editor(get_option("wc-email-header"), "wc-email-header"); ?>
            </div>
            <div class="form-group">
                <input type="Submit" name="wc_email_save" class="wc-form-input" value="<?php _e("Save Temaplate");?>"/>
            </div>
        </form>
    </div>
    <div class="wc-right">
        <div class="wc-email-shortcode">
            <p>
                <?php _e("Copy & paste this Shortcode inside the Editor<br>[wcemailverificationcode]<br />");?>
            </p>
        </div>
    </div>
    <div class="clear" style="clear:both"></div>
</div>