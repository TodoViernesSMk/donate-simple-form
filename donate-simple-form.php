<?php
/*
Plugin Name: Donate Simple Form
Description: Donation simple form with Visa payment method.
Version: 1.0.0
Author: Saul Morales Pacheco
Author URI: https://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages/
*/

if (!defined( 'ABSPATH' )) exit;
if(!defined('DONATE_SIMPLE_FORM_DVSMP_VERSION')){
    define('DONATE_SIMPLE_FORM_DVSMP_VERSION', '1.0.0');
}

add_action('plugins_loaded','donate_simple_form_dvsmp_init',0);

function donate_simple_form_dvsmp_init(){

    load_plugin_textdomain('donate-simple-form', FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    if (!requeriments_donate_simple_form_dvsmp()){
        return;
    }

    donate_simple_form_dvsmp()->run();

    if(get_option('notices_donate_simple_form_dvsmp_activation_redirect', false)){
        delete_option('notices_donate_simple_form_dvsmp_activation_redirect');
        wp_redirect(admin_url('admin.php?page=config-donatesimpleform'));
    }

}

add_action('notices_donate_simple_form_dvsmp', 'notices_donate_simple_form_dvsmp_action', 10, 1);

function notices_donate_simple_form_dvsmp_action($notice){
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function requeriments_donate_simple_form_dvsmp(){

    if ( version_compare( '5.6.0', PHP_VERSION, '>' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $php = __( 'Donate Simple Form: Requires php version 5.6.0 or higher.', 'donate-simple-form' );
            do_action('notices_donate_simple_form_dvsmp', $php);
        }
        return false;
    }

    $openssl_warning = __( 'Donate Simple Form requires OpenSSL >= 1.0.1 to be installed on your server', 'donate-simple-form' );
    if ( ! defined( 'OPENSSL_VERSION_TEXT' ) ) {
        do_action('notices_donate_simple_form_dvsmp', $openssl_warning);
        return false;
		}

    preg_match( '/^(?:Libre|Open)SSL ([\d.]+)/', OPENSSL_VERSION_TEXT, $matches );
    if ( empty( $matches[1] ) ) {
        do_action('notices_donate_simple_form_dvsmp', $openssl_warning);
        return false;
    }

    if ( ! version_compare( $matches[1], '1.0.1', '>=' ) ) {
        do_action('notices_donate_simple_form_dvsmp', $openssl_warning);
        return false;
    }

    return true;
}

function donate_simple_form_dvsmp()
{
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-donate-simple-form-dvsmp-plugin.php');
        $plugin = new Donate_Simple_Form_DVSMP_Plugin(__FILE__,DONATE_SIMPLE_FORM_DVSMP_VERSION, 'Donate Simple Form');
    }
    return $plugin;
}

function donate_simple_form_dvsmp_activation(){
    add_option('notices_donate_simple_form_dvsmp_activation_redirect', true);
}

register_activation_hook(__FILE__,'donate_simple_form_dvsmp_activation');