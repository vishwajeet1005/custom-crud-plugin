<?php 
/*
Plugin Name: Custom CRUD Plugin
Description: A Custom plugin to perform crud operation
Version: 1.0
Author: Vishwajeet Kumar
*/

// Exit if accessed directly
if(!defined('ABSPATH')){
    exit;
}

// Register a custom database table
register_activation_hook(__FILE__, 'ccp_create_table');

function ccp_create_table(){
    global $wpdb;
    $table_name_ccp = $wpdb->prefix . 'ccp_entry';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name_ccp ( 
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name text NOT NULL,
        email text NOT NULL,
        message text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Enqueue jQuery and custom JS

add_action('wp_enqueue_scripts', 'ccp_enqueue_scripts');
function ccp_enqueue_scripts(){
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js', array('jquery'), null, true);
    wp_enqueue_script('ccp-ajax-script', plugin_dir_url(__FILE__) . 'js/ccp-ajax.js', array('jquery', 'jquery-validate'), null, true);

    wp_localize_script('ccp-ajax-script', 'ccp_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}


//Handle Ajax Request
add_action('wp_ajax_ccp_handle_form', 'ccp_handle_form');
add_action('wp_ajax_nopriv_ccp_handle_form', 'ccp_handle_form');

function ccp_handle_form() {
    global $wpdb;
    $table_name_ccp = $wpdb->prefix . 'ccp_entry';

    $name = sanitize_text_field($_POST['full_name']);
    $email = sanitize_email($_POST['email']);
    $message = sanitize_textarea_field($_POST['message']);

    $wpdb->insert($table_name_ccp, array(
        'name' => $name,
        'email' => $email,
        'message' => $message,
    ));

    if($wpdb->insert_id){
        wp_send_json_success('Your form has been submitted successfully!');
    } else {
        wp_send_json_error('There was an issue with your form submission.');
    }

    wp_die();
}
// Create Form Shortcode
add_shortcode('ccp_form', 'ccp_form_shortcode');
function ccp_form_shortcode(){

    ob_start();

    ?>
    <section class="main-section">
        <div class="container">
            <div class="row form-row">
                <div class="custom-crud-form">
                    <form id="ccp-form">

                        <input type="text" name="full_name" placeholder="Full Name" required>
                        <input type="email" name="email" placeholder="Email Address" required>
                        <textarea name="message" placeholder="Your Message" required></textarea>
                        <button type="submit">Submit</button>
                        
                        <div id="ccp-message"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php 
    return ob_get_clean();
}
