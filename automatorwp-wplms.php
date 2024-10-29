<?php
/**
 * Plugin Name:           AutomatorWP - WPLMS integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wplms-integration/
 * Description:           Connect AutomatorWP with WPLMS.
 * Version:               1.0.3
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wplms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.9
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WPLMS
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_WPLMS_Integration {

    /**
     * @var         AutomatorWP_WPLMS_Integration $instance The one true AutomatorWP_WPLMS_Integration
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_WPLMS_Integration self::$instance The one true AutomatorWP_WPLMS_Integration
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_WPLMS_Integration();
            
            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                self::$instance->load_textdomain();

            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_WPLMS_VER', '1.0.3' );

        // Plugin file
        define( 'AUTOMATORWP_WPLMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPLMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPLMS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Triggers
            require_once AUTOMATORWP_WPLMS_DIR . 'includes/triggers/complete-quiz.php';
            require_once AUTOMATORWP_WPLMS_DIR . 'includes/triggers/complete-assignment.php';
            require_once AUTOMATORWP_WPLMS_DIR . 'includes/triggers/complete-unit.php';
            require_once AUTOMATORWP_WPLMS_DIR . 'includes/triggers/complete-course.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'wplms', array(
            'label' => 'WPLMS',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wplms.svg',
        ) );

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - WPLMS requires %s and %s in order to work. Please install and activate them.', 'automatorwp-wplms-integration' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wplms.io/" target="_blank">WPLMS</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php elseif ( $this->pro_installed() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php echo __( 'You can uninstall AutomatorWP - WPLMS Integration because you already have the pro version installed and includes all the features of the free version.', 'automatorwp-wplms-integration' ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'WPLMS_Front_End' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_WPLMS' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_WPLMS_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_wplms_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-wplms-integration' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-wplms-integration', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-wplms-integration/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-wplms-integration/ folder
            load_textdomain( 'automatorwp-wplms-integration', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-wplms-integration/languages/ folder
            load_textdomain( 'automatorwp-wplms-integration', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-wplms-integration', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_WPLMS_Integration instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_WPLMS_Integration The one true AutomatorWP_WPLMS_Integration
 */
function AutomatorWP_WPLMS_Integration() {
    return AutomatorWP_WPLMS_Integration::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_WPLMS_Integration' );
