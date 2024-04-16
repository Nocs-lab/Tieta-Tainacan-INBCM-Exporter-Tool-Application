<?php
/*
Plugin Name: Tieta - Tainacan INBCM Exporter Tool/Application
Description: Adds a custom exporter to Tainacan for museum inventory.
Version: beta.0.1
Author: Douglas de Araújo
Author URI: github.com/everbero
License: GPLv2 or later
Text Domain: tieta-tainacan
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include Composer autoload to load external dependencies
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

function TietaTainacanSuper() {
    // Initialization code and hooks here
    static $plugin;

    if (!isset($plugin)) {
        class _TietaTainacan {

            // Instância Singleton da classe
            private static $instance;

            // Método para obter a instância Singleton desta classe
            public static function get_instance() {
                if (null === self::$instance) {
                    self::$instance = new self();
                }
                return self::$instance;
            }

            // Métodos mágicos para prevenir clonagem e desserialização da instância
            public function __clone() {}
            public function __wakeup() {}

            // Constructor
            public function __construct() {
                // Initialization code and hooks here

                // Adds admin boot action and includes required files
                add_action('admin_init', [$this, 'install']);

                $this->includes();

                // If it is in the admin panel, it includes specific files
                if (is_admin()) {
                    $this->admin_includes();
                }

            }
            /**
             * Activation hook for the plugin.
             * Checks if Tainacan plugin is active before allowing this plugin to activate.
             * If Tainacan is not active, it prevents activation and shows an error message.
             */
            public function install() {
                // Checks if the plugin is active before running the installation
                if (!in_array('tainacan/tainacan.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                    deactivate_plugins(plugin_basename(__FILE__));
                    wp_admin_notice(
                        __('<strong>Tieta</strong> requires the <strong>Tainacan</strong> plugin to be installed and activated.', 'tieta-tainacan'),
                        array(
                            'id' => 'message',
                            'type' => 'error',
                            'dismissible' => true,
                        )
                    );
                    return;
                }
            }
            /**
             * Includes necessary files for the plugin and registers the custom exporter if Tainacan is active.
             */
            private function includes() {
                // public includes goes here
                if (is_plugin_active('tainacan/tainacan.php')) {
                    // Include the custom exporter and utility class files
                    include_once plugin_dir_path(__FILE__) . 'src/includes/handlers/ExportHandler.php';
                    include_once plugin_dir_path(__FILE__) . 'src/includes/handlers/ImportHandler.php';
                    include_once plugin_dir_path(__FILE__) . 'src/includes/handlers/MappersHandler.php';
                    // include our workers
                    include_once plugin_dir_path(__FILE__) . 'src/includes/workers/MuseumInventoryExporter.php';
                    include_once plugin_dir_path(__FILE__) . 'src/includes/workers/MuseumInventoryImporter.php';
                    // include our mappers
                    include_once plugin_dir_path(__FILE__) . 'src/includes/mappers/MuseologyMapper.php';
                    include_once plugin_dir_path(__FILE__) . 'src/includes/mappers/BiblioteconomyMapper.php';
                    include_once plugin_dir_path(__FILE__) . 'src/includes/mappers/ArchivologyMapper.php';
                }
            }

            // Método privado para inclusão de arquivos específicos do admin
            private function admin_includes() {
                

            }



        }
        // Cria uma instância Singleton do plugin, usando a classe interna
        $plugin = _TietaTainacan::get_instance();
    }
    return $plugin;
}

/**
 * Inicializa o serviço do plugin, carregando os scripts e definindo o carregamento dos arquivos de idioma.
 */
function _TietaTainacanStarter() {
    // Load plugin text domain
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    load_plugin_textdomain('tieta-tainacan', false, plugin_basename(dirname(__FILE__)) . '/languages');

    // Loads additional scripts and styles for the plugin if any
    function _TietaTainacanScriptLoader() {
        // register and enqueue scipts and styles here
    }

    // Add action to load scripts and styles
    add_action('admin_enqueue_scripts', '_TietaTainacanScriptLoader');

    // Loads the plugin service class
    TietaTainacanSuper();
}

// call the plugin service initialization
add_action('plugins_loaded', '_TietaTainacanStarter');
