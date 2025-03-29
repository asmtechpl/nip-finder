<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://code-press.pl
 * @since      1.0.0
 *
 * @package    Nip_Finder
 * @subpackage Nip_Finder/public
 */

use GuzzleHttp\Exception\GuzzleException;
use NipFinder\enums\ApiData;
use NipFinder\service\ApiClient;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Nip_Finder
 * @subpackage Nip_Finder/public
 * @author     CodePress <kontakt@code-press.pl>
 */
class Nip_Finder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

    private $api_key;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->api_key = get_option('nip_finder_api_key');
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nip_Finder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nip_Finder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/nip-finder-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nip_Finder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nip_Finder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nip-finder-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * @return void
     */
    public function nip_finder_enqueue_gus_script()
    {
        if (is_checkout() && get_option('nip_finder_getting_nip') == 1) {
            wp_enqueue_script(
                'nip-finder-gus-fetch',
                plugin_dir_url(__FILE__) . '/js/nip-finder-gus-fetch.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_localize_script('nip-finder-gus-fetch', 'nip_finder_gus_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nip_finder_gus_nonce')
            ));
        }
    }

    /**
     * @return void
     */
    public function nip_finder_fetch_gus_data(): void
    {
        check_ajax_referer('nip_finder_gus_nonce', 'nonce');

        if (!isset($_POST['nip']) || !preg_match('/^\d{10}$/', $_POST['nip'])) {
            wp_send_json_error(array('message' => 'Nieprawidłowy NIP.'));
        }

        $nip = sanitize_text_field($_POST['nip']);

        try {
            $apiClient = new ApiClient(ApiData::API_URL, $this->api_key);
            $data = $apiClient->getData('/api/v1/gus?nip=' . $nip);


            if ($data && isset($data->name)) {
                wp_send_json_success(array(
                    'first_name' => $data->first_name ?? '',
                    'last_name' => $data->last_name ?? '',
                    'company' => $data->name,
                    'address' => $data->street. ' ' . $data->houseNumber ?? '',
                    'city' => $data->city ?? '',
                    'postcode' => $data->postalCode ?? '',
                ));
            } else {
                wp_send_json_error(array('message' => 'Nie znaleziono danych dla podanego NIP.'));
            }

        } catch (Exception | GuzzleException $e) {
            wp_send_json_error(array('message' => 'Wystąpił błąd: ' . $e->getMessage()));
        }
    }

    /**
     * Add update data
     *
     * @param $order_id
     * @return void
     */
    public function nip_finder_save_nip_billing_field($order_id)
    {
        if (!empty($_POST['billing_nip'])) {
            update_post_meta($order_id, '_billing_nip', sanitize_text_field($_POST['billing_nip']));
        }
    }

    /**
     * Add display data in admin panel
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function nip_finder_display_nip_in_admin_order_meta($order)
    {
        $nip = get_post_meta($order->get_id(), '_billing_nip', true);
        if (!empty($nip)) {
            echo '<p><strong>' . __('NIP (Szczegóły płatności):', 'nip-finder') . '</strong> ' . esc_html($nip) . '</p>';
        }
    }

    /**
     * Validation data
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function nip_finder_validate_nip_billing_field()
    {
        if (empty($_POST['billing_nip'])) {
            wc_add_notice(__('Pole NIP (Szczegóły płatności) jest wymagane.', 'nip-finder'), 'error');
        } elseif (!preg_match('/^\d{10}$/', $_POST['billing_nip'])) {
            wc_add_notice(__('NIP musi składać się z 10 cyfr.', 'nip-finder'), 'error');
        }
    }

    /**
     * Add custom field
     *
     * @param array $fields
     * @return array
     * @since  1.0.0
     */
    public function nip_finder_add_nip_billing_field(array $fields): array
    {
        $fields['billing']['billing_nip'] = array(
            'type' => 'text',
            'label' => __('NIP', 'nip-finder'),
            'placeholder' => __('Wprowadź NIP', 'nip-finder'),
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 25,
        );

        return $fields;
    }

    /**
     * @return void
     */
    public function nip_finder_enqueue_postalcode_script()
    {
        if (get_option('nip_finder_getting_postal_codes') == 1) {
            wp_enqueue_script(
                'nip-finder-postcode-fetch',
                plugin_dir_url(__FILE__) . '/js/nip-finder-postalcode-fetch.js',
                array('jquery'),
                '1.0.0',
                true
            );

            wp_localize_script('nip-finder-postcode-fetch', 'nip_finder_postalcode_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('nip_finder_postcode_nonce')
            ));
        }
    }

    /**
     * Fetch cities based on postal code
     *
     * @return void
     * @throws GuzzleException
     * @since 1.0.0
     */
    public function nip_finder_fetch_cities_by_postcode(): void
    {
        check_ajax_referer('nip_finder_postcode_nonce', 'nonce');

        if (!isset($_POST['postcode']) || (!preg_match('/^\d{2}-\d{3}$/', $_POST['postcode']) && !preg_match('/^\d{5}$/', $_POST['postcode']))) {
            wp_send_json_error(array('message' => 'Nieprawidłowy kod pocztowy.'));
        }

        $postcode = sanitize_text_field($_POST['postcode']);
        $countryCode = sanitize_text_field($_POST['countryCode']);

        try {
            $apiClient = new ApiClient(ApiData::API_URL, $this->api_key);
            $data = $apiClient->getData('/api/v1/postal-code?postalCode=' . $postcode . '&countryCode='.$countryCode);

            if ($data && is_array($data)) {
                $cities = array_unique(array_column($data, 'city'));
                wp_send_json_success($cities);
            } else {
                wp_send_json_error(array('message' => 'Nie znaleziono miejscowości dla podanego kodu pocztowego.'));
            }

        } catch (Exception $e) {
            wp_send_json_error(array('message' => 'Wystąpił błąd: ' . $e->getMessage()));
        }
    }
}
