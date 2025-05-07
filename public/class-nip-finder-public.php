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
     * Pobiera dane z GUS przez AJAX na podstawie numeru NIP.
     *
     * @return void
     */
    public function nip_finder_fetch_gus_data(): void {
        check_ajax_referer( 'nip_finder_gus_nonce', 'nonce' );

        $raw_nip = isset( $_POST['nip'] ) ? wp_unslash( $_POST['nip'] ) : '';
        if ( '' === $raw_nip ) {
            wp_send_json_error( [ 'message' => __( 'Brak numeru NIP.', 'nip-finder' ) ] );
        }

        if ( ! preg_match( '/^\d{10}$/', $raw_nip ) ) {
            wp_send_json_error( [ 'message' => __( 'Nieprawidłowy format NIP. NIP musi składać się z 10 cyfr.', 'nip-finder' ) ] );
        }

        $nip = sanitize_text_field( $raw_nip );

        try {
            $apiClient = new ApiClient( ApiData::API_URL, $this->api_key );
            $data      = $apiClient->getData( '/api/v1/gus?nip=' . urlencode( $nip ) );

            if ( $data && isset( $data->name ) ) {
                wp_send_json_success( [
                    'first_name' => ! empty( $data->first_name )
                        ? sanitize_text_field( $data->first_name ) : '',
                    'last_name'  => ! empty( $data->last_name )
                        ? sanitize_text_field( $data->last_name )  : '',
                    'company'    => sanitize_text_field( $data->name ),
                    'address'    => sanitize_text_field( $data->street )
                        . ' ' . sanitize_text_field( $data->houseNumber ?? '' ),
                    'city'       => ! empty( $data->city )
                        ? sanitize_text_field( $data->city ) : '',
                    'postcode'   => ! empty( $data->postalCode )
                        ? sanitize_text_field( $data->postalCode ) : '',
                ] );
            }

            wp_send_json_error( [ 'message' => __( 'Nie znaleziono danych dla podanego NIP.', 'nip-finder' ) ] );
        } catch ( Exception | GuzzleException $e ) {
            wp_send_json_error( [
                'message' => __( 'Wystąpił błąd: ', 'nip-finder' ) . esc_html( $e->getMessage() ),
            ] );
        }
    }


    /**
     * Zapisuje pole NIP do zamówienia.
     *
     * @param int $order_id
     * @return void
     */
    public function nip_finder_save_nip_billing_field( $order_id ): void {
        if ( empty( $_POST['billing_nip'] ) ) {
            return;
        }

        if (
            ! isset( $_POST['_wpnonce'] )
            || ! wp_verify_nonce( wp_unslash( $_POST['_wpnonce'] ), 'nip_finder_save_billing_field' )
        ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $order_id ) ) {
            return;
        }

        $raw_nip = wp_unslash( $_POST['billing_nip'] );
        $nip     = sanitize_text_field( $raw_nip );

        if ( ! preg_match( '/^\d{10}$/', $nip ) ) {
            return;
        }

        update_post_meta( $order_id, '_billing_nip', $nip );
    }


    /**
     * Wyświetla numer NIP dla szczegółów płatności w panelu administratora.
     *
     * @param WC_Order $order
     * @return void
     */
    public function nip_finder_display_nip_in_admin_order_meta( $order ) {
        $nip = get_post_meta( $order->get_id(), '_billing_nip', true );
        if ( ! empty( $nip ) ) {
            echo '<p><strong>' . esc_html__( 'NIP (Szczegóły płatności):', 'nip-finder' ) . '</strong> ' . esc_html( $nip ) . '</p>';
        }
    }

    /**
     * Waliduje numer NIP przy wypełnianiu formularza.
     *
     * @return void
     */
    public function nip_finder_validate_nip_billing_field() {
        if ( ! isset( $_POST['nip_finder_nonce'] )
            || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nip_finder_nonce'] ) ), 'nip_finder_action' )
        ) {
            wc_add_notice( __( 'Błąd bezpieczeństwa – spróbuj odświeżyć stronę i wysłać formularz ponownie.', 'nip-finder' ), 'error' );
            return;
        }

        if ( ! current_user_can( 'edit_posts' ) ) {
            wc_add_notice( __( 'Nie masz uprawnień do wykonania tej akcji.', 'nip-finder' ), 'error' );
            return;
        }

        $billing_nip = isset( $_POST['billing_nip'] )
            ? sanitize_text_field( wp_unslash( $_POST['billing_nip'] ) )
            : '';

        if ( empty( $billing_nip ) ) {
            wc_add_notice( __( 'Pole NIP (Szczegóły płatności) jest wymagane.', 'nip-finder' ), 'error' );
        } elseif ( ! preg_match( '/^\d{10}$/', $billing_nip ) ) {
            wc_add_notice( __( 'NIP musi składać się z 10 cyfr.', 'nip-finder' ), 'error' );
        }
    }

    /**
     * Dodaje pole NIP do formularza w panelu kasowym.
     *
     * @param array $fields
     * @return array
     */
    public function nip_finder_add_nip_billing_field( array $fields ): array {
        $fields['billing']['billing_nip'] = array(
            'type'        => 'text',
            'label'       => __( 'NIP', 'nip-finder' ),
            'placeholder' => __( 'Wprowadź NIP', 'nip-finder' ),
            'required'    => false,
            'class'       => array( 'form-row-wide' ),
            'priority'    => 25,
        );

        $fields['billing']['nip_finder_nonce'] = array(
            'type'    => 'hidden',
            'default' => wp_create_nonce( 'nip_finder_action' ),
        );

        return $fields;
    }

    /**
     * Ładuje skrypt do pobierania kodów pocztowych.
     *
     * @return void
     */
    public function nip_finder_enqueue_postalcode_script() {
        if ( get_option( 'nip_finder_getting_postal_codes' ) == 1 ) {
            wp_enqueue_script(
                'nip-finder-postcode-fetch',
                plugin_dir_url( __FILE__ ) . '/js/nip-finder-postalcode-fetch.js',
                array( 'jquery' ),
                '1.0.0',
                true
            );

            wp_localize_script( 'nip-finder-postcode-fetch', 'nip_finder_postalcode_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'nip_finder_postcode_nonce' ),
            ) );
        }
    }

    /**
     * Fetch cities via AJAX based on a postal code.
     *
     * @return void
     * @throws GuzzleException
     */
    public function nip_finder_fetch_cities_by_postcode(): void {
        check_ajax_referer( 'nip_finder_postcode_nonce', 'nonce' );

        $raw_postcode    = isset( $_POST['postcode'] ) ? wp_unslash( $_POST['postcode'] ) : '';
        $raw_countryCode = isset( $_POST['countryCode'] ) ? wp_unslash( $_POST['countryCode'] ) : '';

        if (
            ! preg_match( '/^\d{2}-\d{3}$/', $raw_postcode ) &&
            ! preg_match( '/^\d{5}$/',   $raw_postcode )
        ) {
            wp_send_json_error( [
                'message' => __( 'Nieprawidłowy kod pocztowy.', 'nip-finder' ),
            ] );
        }

        $postcode    = sanitize_text_field( $raw_postcode );
        $countryCode = sanitize_text_field( $raw_countryCode );

        try {
            $apiClient = new ApiClient( ApiData::API_URL, $this->api_key );
            $data      = $apiClient->getData( '/api/v1/postal-code?postalCode=' . urlencode( $postcode ) . '&countryCode=' . urlencode( $countryCode ) );

            if ( is_array( $data ) && ! empty( $data ) ) {
                $cities = array_unique( wp_list_pluck( $data, 'city' ) );
                // 5) Wyjście JSON – WP automatycznie escape’uje key/value
                wp_send_json_success( $cities );
            }

            wp_send_json_error( [
                'message' => __( 'Nie znaleziono miejscowości dla podanego kodu pocztowego.', 'nip-finder' ),
            ] );
        } catch ( Exception $e ) {
            wp_send_json_error( [
                'message' => __( 'Wystąpił błąd: ', 'nip-finder' ) . esc_html( $e->getMessage() ),  // escape late przy wyświetlaniu błędu :contentReference[oaicite:2]{index=2}
            ] );
        }
    }
}
