<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 19/06/18
 * Time: 09:14 AM
 */

class Donate_Simple_Form_Shortcode
{
    public function __construct()
    {
        add_shortcode( 'donate_simple_form', array( $this,'donate_simple_form_dvsmp_shortcode' ));
    }

    public function donate_simple_form_dvsmp_shortcode()
    {
        $img = donate_simple_form_dvsmp()->enviroment()  ? 'https://secure.checkout.visa.com/wallet-services-web/xo/button.png'  : 'https://sandbox.secure.checkout.visa.com/wallet-services-web/xo/button.png';
        $payuUrl = donate_simple_form_dvsmp()->enviroment() ? 'https://checkout.payulatam.com/ppp-web-gateway-payu/' : 'https://sandbox.checkout.payulatam.com/ppp-web-gateway-payu/';
        $payuTest = donate_simple_form_dvsmp()->enviroment() ? 0 : 1;

        $description = __('Donation', 'donate-simple-form') . ' ' . get_bloginfo( 'name' );
        $currency = get_option('donate-simple-form-currency-dvsmp');
        $merchantId = get_option('donate-simple-form-merchantid-payu-dvsmp');
        $accountId = get_option('donate-simple-form-accountid-payu-dvsmp');

        $html = '';

        if (isset($_REQUEST['referenceCode']) && isset($_REQUEST['transactionState'])){

            $transactionId = $_REQUEST['transactionId'];

            if ($_REQUEST['transactionState'] == 4 ) {

                $namedonate = $_REQUEST['extra1'];
                $company = $_REQUEST['extra2'];
                $value = $_REQUEST['TX_VALUE'];
                $currency = $_REQUEST['currency'];
                $email = $_REQUEST['extra3'];

                $transaction = array(array('name' => sanitize_text_field($namedonate), 'company' => sanitize_text_field($company), 'price' => sanitize_text_field($value), 'currencyCode' => sanitize_text_field($currency),  'email' => sanitize_text_field($email), 'payment' => 'payu Latam'));
                donate_simple_form_dvsmp()->saveDonations($transaction);
                $html = sprintf(__('Approved transaction, ID of the transaction: %s','donate-simple-form'), $transactionId);
            }

            else if ($_REQUEST['transactionState'] == 6 ) {
                $html = "Transaction declined";
            }

            else if ($_REQUEST['transactionState'] == 104 ) {
                $html = "Error";
            }

            else if ($_REQUEST['transactionState'] == 7 ) {

                $html = sprintf(__('Pending transaction, ID of the transaction: %s','donate-simple-form'), $transactionId);
            }

            return $html;
        }


        $html = "<div class='container-fluid'>
<div id='container-donate-simple-form'>
<form id='donate_simple_form_frontend'>
<div class='form-group'>
<h2 id='donate-simple-form-title'>" . __('Donate your support', 'donate-simple-form') . "</h2>
<div class='donate-simple-form-alert alert' style='display:none;'>
</div>
<label class='control-label' for='donate-simple-form-price'>" . __('Amount', 'donate-simple-form') . "</label>
<input type='number' min='20' class='form-control' name='donate-simple-form-price' id='donate-simple-form-price' required=''>
<label class='control-label' for='donate-simple-form-name-last'>" . __('Name and last name', 'donate-simple-form') . "</label>
<input type='text' class='form-control' id='donate-simple-form-name-last' name='donate-simple-form-name-last' required=''>
<label class='control-label' for='donate-simple-form-company'>" . __('Company Name', 'donate-simple-form') . "</label>
<input type='text' class='form-control' id='donate-simple-form-company' name='donate-simple-form-company' required=''>
<label class='control-label' for='donate-simple-form-email'>" . __('Email Address', 'donate-simple-form') . "</label>
<input type='email' class='form-control' id='donate-simple-form-email' name='donate-simple-form-email' required=''>

<fieldset>
                <legend>" . __('Payment Method','donate-simple-form') . "</legend>
                <label class='radio-inline'>
                <input class='form-check-input' type='radio' id='donate-simple-form-gateway-payment-visa' name='gateway-payment' value='visa' required>Visa checkout
                </label>
                <label class='radio-inline'>
                <input class='form-check-input' type='radio' id='donate-simple-form-gateway-payment-payu' name='gateway-payment' value='payu' required>payU Latam
                </label>
            </fieldset>
            <button type='submit' class='btn btn-dark'><span class='glyphicon glyphicon-heart'></span> " . __('Donate your support', 'donate-simple-form') . "</button>
<div class='v-checkout-wrapper' style='display: none;'>
<img
class='v-button' role='button' alt='Visa Checkout'
src='" . $img . "'>
<a class='v-learn v-learn-default' href='#' data-locale='" . get_locale() . "'>" . __('Tell Me More', 'donate-simple-form') . "</a>
</div>
</div>
</form>
<form method='post' action='" . $payuUrl . "' id='donate-simple-form-frontend-payu'>
  <input name='merchantId'    type='hidden' value='" . $merchantId . "'   >
  <input name='accountId'     type='hidden'  value='" . $accountId . "' >
  <input name='description'   type='hidden'  value='" . $description . "'  >
  <input name='referenceCode' type='hidden'  value='' >
  <input name='amount'        type='hidden'  value='' >
  <input name='currency'      type='hidden'  value='" . $currency . "' >
  <input name='signature'     type='hidden'  value='' >
  <input name='test'          type='hidden'  value='" . $payuTest . "' >
  <input name='buyerEmail'    type='hidden' value=''>
  <input name='extra1'   type='hidden'  value='' >
  <input name='extra2'   type='hidden'  value='' >
  <input name='extra3'   type='hidden'  value='' >
  <input name='responseUrl'   type='hidden'  value='' >
  <input name='confirmationUrl'    type='hidden'  value='" . site_url() . "' >
</form>
</div>
</div>";
        return $html;

    }
}