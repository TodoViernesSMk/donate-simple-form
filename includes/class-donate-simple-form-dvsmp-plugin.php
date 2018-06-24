<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 18/06/18
 * Time: 03:18 PM
 */

class Donate_Simple_Form_DVSMP_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var bool
     */
    private $_bootstrapped = false;
    /**
     * @var string
     */
    public $assets;

    public function __construct($file, $version, $name)
    {
        $this->file = $file;
        $this->version = $version;
        $this->name = $name;

        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->assets = $this->plugin_url . trailingslashit( 'assets');

        add_filter( 'plugin_action_links_' . plugin_basename( $this->file), array( $this, 'plugin_action_links' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_donate_simple_form_dvsmp',array($this,'donate_simple_form_dvsmp_ajax'));
        add_action( 'wp_ajax_nopriv_donate_simple_form_dvsmp',array($this,'donate_simple_form_dvsmp_ajax'));

        add_action('the_posts', array($this,'check_for_shortcode'));
    }


    public function run()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( __( 'Donate Simple Form can only be called once', 'donate-simple-form'));
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                do_action('notices_donate_simple_form_dvsmp', 'Donate Simple Form: ' . $e->getMessage());
            }
        }
    }

    protected function _run()
    {
        $this->_load_handlers();
    }

    protected function _load_handlers()
    {
        require_once ($this->includes_path . 'class-donate-simple-form-dvsmp-admin.php');
        require_once ($this->includes_path . 'class-donate-simple-form-dvsmp-shortcode.php');
        $this->admin = new Donate_Simple_Form_DVSMP_Admin();
        $this->shortcode = new Donate_Simple_Form_Shortcode();
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="'.admin_url( 'admin.php?page=config-donatesimpleform').'">' . esc_html__( 'Settings', 'donate-simple-form' ) . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function enqueue_scripts()
    {

        if ($this->enviroment()){
            $urlSrcVisa = 'https://assets.secure.checkout.visa.com/
checkout-widget/resources/js/integration/v1/sdk.js';
        }else{
            $urlSrcVisa = 'https://sandbox-assets.secure.checkout.visa.com/
checkout-widget/resources/js/integration/v1/sdk.js';
        }

        wp_enqueue_style('donate_simple_form_dvsmp_css', $this->plugin_url . 'assets/css/donate-simple-form-dvsmp.css', array(), $this->version, null);
        wp_enqueue_script( 'donate_simple_form_src', $urlSrcVisa, array( 'jquery' ), $this->version, true );
        wp_enqueue_script( 'donate_simple_form_dvsmp', $this->plugin_url . 'assets/js/donate-simple-form-dvsmp.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( 'donate_simple_form_dvsmp', 'donatesimpleform', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'apikey' => get_option('donate-simple-form-apikey-dvsmp'),
            'currency' => get_option('donate-simple-form-currency-dvsmp'),
            'locale' => get_locale(),
            'apikeyMsj' => __('The api key is required', 'donate-simple-form'),
            'successMsj' => __('The payment has been successful', 'donate-simple-form'),
            'cancelMsj' => __('You have canceled the payment', 'donate-simple-form'),
            'errorMsj' => __('Error when making the payment, please try again', 'donate-simple-form')
        ) );

    }

    public function donate_simple_form_dvsmp_ajax()
    {
        if (!empty($_POST)){

            if(!empty($_POST['donate-simple-form-enviroment-dvsmp'])){
                update_option('donate-simple-form-enviroment-dvsmp', sanitize_text_field($_POST['donate-simple-form-enviroment-dvsmp']));
                update_option('donate-simple-form-currency-dvsmp', sanitize_text_field($_POST['donate-simple-form-currency-dvsmp']));
                update_option('donate-simple-form-apikey-dvsmp', sanitize_text_field($_POST['donate-simple-form-apikey-dvsmp']));

                update_option('donate-simple-form-apikey-payu-dvsmp', sanitize_text_field($_POST['donate-simple-form-apikey-payu-dvsmp']));
                update_option('donate-simple-form-merchantid-payu-dvsmp', sanitize_text_field($_POST['donate-simple-form-merchantid-payu-dvsmp']));
                update_option('donate-simple-form-accountid-payu-dvsmp', sanitize_text_field($_POST['donate-simple-form-accountid-payu-dvsmp']));
            }

            if (!empty($_POST['company'])){

                $transaction = array(array('name' => sanitize_text_field($_POST['name']), 'company' => sanitize_text_field($_POST['company']), 'price' => sanitize_text_field($_POST['price']), 'currencyCode' => sanitize_text_field($_POST['currencyCode']),  'email' => sanitize_text_field($_POST['email']), 'payment' => $_POST['payment']));
                $this->saveDonations($transaction);

            }

            if(!empty($_POST['payment'])){
                $price = $_POST['price'];
                $referenceCode = 'donation' . time();
                $currency = get_option('donate-simple-form-currency-dvsmp');
                $apikey = get_option('donate-simple-form-apikey-payu-dvsmp');
                $merchantId = get_option('donate-simple-form-merchantid-payu-dvsmp');

                $signature = md5("$apikey~$merchantId~$referenceCode~$price~$currency");

                echo json_encode(array('signature' => $signature, 'reference' => $referenceCode) );

                die();
            }
        }
        die();
    }


    function check_for_shortcode($posts) {
        if ( empty($posts) )
            return $posts;

        // false because we have to search through the posts first
        $found = false;

        // search through each post
        foreach ($posts as $post) {
            // check the post content for the short code
            if ( strpos($post->post_content, '[donate_simple_form') !== false )
                // we have found a post with the short code
                $found = true;
            // stop the search
            break;
        }

        $style = 'bootstrap';

        if ( $found && !wp_style_is( $style, 'queue' ) && !wp_style_is( $style, 'done') ){

            wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), $this->version, null);

        }
        return $posts;
    }

    public function saveDonations($transaction)
    {
        $payments = get_option('donate-simple-form-dvsmp-payments');
        if (empty($payments)){
            update_option('donate-simple-form-dvsmp-payments', $transaction);
        }else{
            $array = array_merge($payments, $transaction);
            update_option('donate-simple-form-dvsmp-payments', $array);
        }

    }

    public function nameClean($domain = false)
    {
        $name = ($domain) ? str_replace(' ', '-', $this->name)  : str_replace(' ', '', $this->name);
        return strtolower($name);
    }

    public function enviroment()
    {
        $enviroment = get_option('donate-simple-form-enviroment-dvsmp');
        $enviroment = (empty($enviroment) || $enviroment == 'sandbox') ? false : true;

        return $enviroment;
    }
}