<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://code-press.pl
 * @since      1.0.0
 *
 * @package    Nip_Finder
 * @subpackage Nip_Finder/admin
 */

use GuzzleHttp\Exception\GuzzleException;
use NipFinder\enums\ApiData;
use NipFinder\enums\ApiEndpoints;
use NipFinder\exception\ResponseException;
use NipFinder\service\ApiClient;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nip_Finder
 * @subpackage Nip_Finder/admin
 * @author     CodePress <kontakt@code-press.pl>
 */
class Nip_Finder_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * @var string
     */
    private $api_key;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_key = get_option('nip_finder_api_key');

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nip-finder-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nip-finder-admin.js', array('jquery'), $this->version, false);


        wp_localize_script($this->plugin_name, 'nip_finder', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nip_finder_nonce')
        ));

    }

    /**
     * @return void
     */
    public function register_settings_page()
    {
        $template_path = plugin_dir_path(__FILE__) . 'views/settings-page.phtml';

        if (file_exists($template_path)) {
            include $template_path;
        }
    }

    public function update_nip_finder_settings() {
        woocommerce_update_options( $this->get_nip_finder_settings() );
    }

    /**
     * @return void
     */
    public function nip_finder_key_callback()
    {
        $field_path = plugin_dir_path(__FILE__) . 'views/components/api-key-field.phtml';

        if (file_exists($field_path)) {
            $api_key = get_option('nip_finder_api_key');

            include $field_path;
        }
    }

    /**
     * @return array
     */
    public function add_admin_menu()
    {
        $settings_tabs['nip_finder'] = __( 'NIP Finder', 'nip-finder' );

        return $settings_tabs;
    }

    public function wc_settings_tab_save() {
        woocommerce_update_options($this->get_nip_finder_settings());
    }

    public function get_nip_finder_settings() {
        return array(
            array(
                'title' => __( 'Ustawienia API', 'nip-finder' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'nip_finder_settings'
            ),
            array(
                'title'   => __( 'Klucz API', 'nip-finder' ),
                'id'      => 'nip_finder_api_key',
                'type'    => 'text',
                'desc'    => __( $this->generate_description_text_for_api_key(), 'nip-finder' ),
                'default' => '',
            ),
            array(
                'title'   => __( 'Pobieranie danych na podstawie NIP', 'nip-finder' ),
                'id'      => 'nip_finder_getting_nip',
                'type'    => 'select',
                'desc'    => __( 'Pobieranie danych przy finalizacji zamówienia na podstawie NIP.', 'nip-finder' ),
                'default' => '1',
                'options' => array(
                    '1' => __( 'Tak', 'nip-finder' ),
                    '0' => __( 'Nie', 'nip-finder' ),
                ),
            ),
            array(
                'title'   => __( 'Pobieranie danych na podstawie kodów pocztowych', 'nip-finder' ),
                'id'      => 'nip_finder_getting_postal_codes',
                'type'    => 'select',
                'desc'    => __( 'Pobieranie danych przy finalizacji zamówienia na podstawie kodów pocztowych', 'nip-finder' ),
                'default' => '1',
                'options' => array(
                    '1' => __( 'Tak', 'nip-finder' ),
                    '0' => __( 'Nie', 'nip-finder' ),
                ),
            ),
            array(
                'title'    => __( 'Test API', 'nip-finder' ),
                'id'       => 'nip_finder_test_api',
                'type'     => 'custom',
                'desc'     => __( 'Kliknij przycisk, aby przetestować komunikację z API.', 'nip-finder' ),
                'default'  => '',
                'callback' => array( $this, 'render_test_api_button' ),
            ),
            array(
                'title'    => __( 'Licencja', 'nip-finder' ),
                'id'       => 'nip_finder_manage_licence_api',
                'type'     => 'custom',
                'desc'     => __( 'Kliknij przycisk, aby zarządzać licencją.', 'nip-finder' ),
                'default'  => '',
                'callback' => array( $this, 'render_manage_licence_button' ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'nip_finder_settings'
            )
        );
    }

    public function nip_finder_maybe_update_token() {
        if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
            isset( $_GET['tab'] ) && 'nip_finder' === $_GET['tab'] &&
            isset( $_GET['token'] ) && ! empty( $_GET['token'] ) ) {

            $token = sanitize_text_field( $_GET['token'] );
            update_option( 'nip_finder_api_key', $token );
        }
    }

    public function render_test_api_button( $field ) {
        $template_path = plugin_dir_path(__FILE__) . 'views/components/button-field-check-status.phtml';

        if (file_exists($template_path)) {
            include $template_path;
        }
    }

    public function render_manage_licence_button( $field ) {
        $template_path = plugin_dir_path(__FILE__) . 'views/components/button-field-manage-licence.phtml';

        if (file_exists($template_path)) {
            include $template_path;
        }
    }

    /**
     * @return void
     */
    public function check_status()
    {
        check_ajax_referer('nip_finder_nonce', 'nonce');

        try {
            $apiClient = new ApiClient(ApiData::API_URL, $this->api_key);
            $data = $apiClient->getData(ApiEndpoints::CHECK_USER_ENDPOINT);

            if ($data) {
                wp_send_json_success(['success' => true, 'content' => $data]);
            }
        } catch (Exception|GuzzleException|ResponseException $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * @return void
     */
    public function register_key()
    {
        check_ajax_referer('nip_finder_nonce', 'nonce');

        try {
            $apiClient = new ApiClient(ApiData::API_URL, $this->api_key);
            $data = $apiClient->postData(ApiEndpoints::REGISTER_ENDPOINT);

            if ($data) {
                wp_send_json_success(['success' => true, 'content' => $data]);
            }
        } catch (Exception|GuzzleException|ResponseException $e) {
            wp_send_json_error(['message' => $e->getMessage()]);

        }
    }

    /**
     * @return void
     */
    public function register()
    {
        check_ajax_referer('nip_finder_nonce', 'nonce');

        try {
            $apiClient = new ApiClient(ApiData::API_LICENCE_URL);

            $data = $apiClient->postData(ApiEndpoints::GENERATE_REGISTER_KEY_ENDPOINT, [
                'email' => get_option('admin_email'),
                'remoteAddress' => $_SERVER['REMOTE_ADDR'],
                'pageUrl' => get_option('siteurl')
            ]);

            if ($data) {
                wp_send_json_success(['url' => ApiData::API_LICENCE_URL . ApiEndpoints::REGISTER_ENDPOINT_LICENCE_ENDPOINT. '?key='. $data->key]);
            }
        } catch (Exception|GuzzleException|ResponseException $e) {
            wp_send_json_error(['message' => $e->getMessage()]);

        }
    }

    /**
     * @return void
     */
    public function register_subscription()
    {
        check_ajax_referer('nip_finder_nonce', 'nonce');

        try {
            $apiClient = new ApiClient(ApiData::API_URL, $this->api_key);
            $data = $apiClient->postData(ApiEndpoints::REGISTER_SUBSCRIPTION_ENDPOINT);

            if ($data) {
                update_option('nip_finder_subscription_type', 'FREE');
                wp_send_json_success(['success' => true]);
            }
        } catch (Exception|GuzzleException|ResponseException $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * @return string
     */
    private function generate_description_text_for_api_key()
    {
        if ($this->api_key != null && $this->api_key != '') {
            return 'Wprowadź swój klucz API.';
        }

        return 'Wprowadź swój klucz API. <a href="#" id="nip-finder-generate-api-key">Wygeneruj go</a>';
    }
}
