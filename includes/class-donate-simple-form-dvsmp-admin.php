<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 18/06/18
 * Time: 05:03 PM
 */

class Donate_Simple_Form_DVSMP_Admin
{
    public function __construct()
    {
        $this->name = donate_simple_form_dvsmp()->name;
        $this->plugin_url = donate_simple_form_dvsmp()->plugin_url;
        $this->version = donate_simple_form_dvsmp()->plugin_url;
        $this->assets = donate_simple_form_dvsmp()->assets;
        add_action('admin_menu', array($this, 'donate_simple_form_dvsmp_menu'));
    }

    public function donate_simple_form_dvsmp_menu()
    {
        add_menu_page($this->name, $this->name, 'manage_options', 'menus' . donate_simple_form_dvsmp()->nameClean(), array($this,'menu' . donate_simple_form_dvsmp()->nameClean()), $this->assets .'img/favicon.png');
        $config = add_submenu_page('menus' . donate_simple_form_dvsmp()->nameClean(), __('Configuration', 'donate-simple-form'), __('Configuration', 'donate-simple-form'), 'manage_options', 'config-' . donate_simple_form_dvsmp()->nameClean(), array($this,'configInit'));
        $donations = add_submenu_page('menus' . donate_simple_form_dvsmp()->nameClean(), __('Donations', 'donate-simple-form'), __('Donations', 'donate-simple-form'), 'manage_options', 'donate-' . donate_simple_form_dvsmp()->nameClean(), array($this,'contentDoante'));
        remove_submenu_page('menus' . donate_simple_form_dvsmp()->nameClean(), 'menus' . donate_simple_form_dvsmp()->nameClean());
        add_action( 'admin_footer', array( $this, 'enqueue_scripts_admin' ) );
    }

    public function configInit()
    {
        $enviroment = get_option('donate-simple-form-enviroment-dvsmp');
        $apikey = get_option('donate-simple-form-apikey-dvsmp');
        $apikey_payu = get_option('donate-simple-form-apikey-payu-dvsmp');
        $merchantId = get_option('donate-simple-form-merchantid-payu-dvsmp');
        $accountId = get_option('donate-simple-form-accountid-payu-dvsmp');
        $currency = get_option('donate-simple-form-currency-dvsmp');

        ?>
        <div class="wrap about-wrap">
            <form id="donate-simple-form-config">
                <table>
                    <tbody>
                    <tr>
                        <th><?php _e('Environment', 'donate-simple-form'); ?></th>
                        <td>
                            <select name="donate-simple-form-enviroment-dvsmp" id="donate-simple-form-enviroment-dvsmp">
                                <option value="sandbox" <?php if ($enviroment == 'sandbox'){ echo 'selected'; } ?>><?php _e('Sandbox','donate-simple-form'); ?></option>
                                <option value="live" <?php if ($enviroment == 'live'){ echo 'selected'; } ?>><?php _e('Live','donate-simple-form'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Currency', 'donate-simple-form'); ?></th>
                        <td>
                            <select name="donate-simple-form-currency-dvsmp" id="donate-simple-form-enviroment-dvsmp">
                                <option value="ARS" <?php if ($currency == 'ARS'){ echo 'selected'; } ?>><?php _e('Argentine peso','donate-simple-form'); ?></option>
                                <option value="AUD" <?php if ($currency == 'AUD'){ echo 'selected'; } ?>><?php _e('Australian dollar','donate-simple-form'); ?></option>
                                <option value="BRL" <?php if ($currency == 'BRL'){ echo 'selected'; } ?>><?php _e('Brazilian real','donate-simple-form'); ?></option>
                                <option value="CAD" <?php if ($currency == 'CAD'){ echo 'selected'; } ?>><?php _e('Canadian dollar','donate-simple-form'); ?></option>
                                <option value="CNY" <?php if ($currency == 'CNY'){ echo 'selected'; } ?>><?php _e('Chinese Yuan','donate-simple-form'); ?></option>
                                <option value="CLP" <?php if ($currency == 'CLP'){ echo 'selected'; } ?>><?php _e('Chilean peso','donate-simple-form'); ?></option>
                                <option value="COP" <?php if ($currency == 'COP'){ echo 'selected'; } ?>><?php _e('Colombian peso','donate-simple-form'); ?></option>
                                <option value="CFP" <?php if ($currency == 'CFP'){ echo 'selected'; } ?>><?php _e('French franc','donate-simple-form'); ?></option>
                                <option value="HKD" <?php if ($currency == 'HKD'){ echo 'selected'; } ?>><?php _e('Hong Kong dollar','donate-simple-form'); ?></option>
                                <option value="INR" <?php if ($currency == 'INR'){ echo 'selected'; } ?>><?php _e('Indian Rupee','donate-simple-form'); ?></option>
                                <option value="EUR" <?php if ($currency == 'EUR'){ echo 'selected'; } ?>><?php _e('Ireland euro','donate-simple-form'); ?></option>
                                <option value="MYR" <?php if ($currency == 'MYR'){ echo 'selected'; } ?>><?php _e('Malaysian Ringgit','donate-simple-form'); ?></option>
                                <option value="MXN" <?php if ($currency == 'MXN'){ echo 'selected'; } ?>><?php _e('Mexican peso','donate-simple-form'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Visa checkout:', 'donate-simple-form'); ?></th>
                    </tr>
                    <tr>
                            <th><?php _e('Apikey', 'donate-simple-form'); ?></th>
                            <td>
                                <input type="text" name="donate-simple-form-apikey-dvsmp" value="<?php echo $apikey; ?>">
                            </td
                    </tr>
                    <tr>
                        <th><?php _e('payU latam:', 'donate-simple-form'); ?></th>
                    </tr>
                    <tr>
                        <th><?php _e('Api key', 'donate-simple-form'); ?></th>
                        <td>
                            <input type="text" name="donate-simple-form-apikey-payu-dvsmp" value="<?php echo $apikey_payu; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Merchant Id', 'donate-simple-form'); ?></th>
                        <td>
                            <input type="text" name="donate-simple-form-merchantid-payu-dvsmp" value="<?php echo $merchantId; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Account Id', 'donate-simple-form'); ?></th>
                        <td>
                            <input type="text" name="donate-simple-form-accountid-payu-dvsmp" value="<?php echo $accountId; ?>">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }


    public function contentDoante()
    {
        $payments = get_option('donate-simple-form-dvsmp-payments');
        ?>
        <div class="wrap about-wrap">
        <?php
        if (empty($payments)) {
            ?>
            <div class="about-text">
                <h2><?php _e('No donations made to show', 'donate-simple-form'); ?></h2>
            </div>
            <?php
        } else {
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <th><?php _e('Name', 'donate-simple-form'); ?></th>
                    <th><?php _e('Company', 'donate-simple-form'); ?></th>
                    <th><?php _e('Email', 'donate-simple-form'); ?></th>
                    <th><?php _e('Price', 'donate-simple-form'); ?></th>
                    <th><?php _e('Currency', 'donate-simple-form'); ?></th>
                    <th><?php _e('Payment method', 'donate-simple-form'); ?></th>
                </tr>
                </thead>
                <?php
                for ($i = 0; $i < count($payments); ++$i) {
                    echo '<tr class="alternate">
<th>' . $payments[$i]['name'] . '</th>
<th>' . $payments[$i]['company'] . '</th>
<th>' . $payments[$i]['email'] . '</th>
<th>' . $payments[$i]['price'] . '</th>
<th>' . $payments[$i]['currencyCode'] . '</th>
<th>' . $payments[$i]['payment'] . '</th>
</tr>';
                }
                ?>
            </table>
            </div>
            <?php

        }
    }

    public function enqueue_scripts_admin()
    {
        wp_enqueue_script( 'donate-simple-formdvsmp', $this->plugin_url . 'assets/js/config.js', array( 'jquery' ), $this->version, true );
    }
}