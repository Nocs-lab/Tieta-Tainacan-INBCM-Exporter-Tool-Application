<?php

namespace TietaTainacan;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure this file is loaded after Tainacan's core files,
// so that the Tainacan classes and functions are available.
class TietaRegisterExporter {
    public function __construct() {
        add_action('init', [$this, 'Tieta_registerCustomExporter']);
    }

    public function Tieta_registerCustomExporter() {
        global $Tainacan_Exporter_Handler;

        if (isset($Tainacan_Exporter_Handler)) {
            $Tainacan_Exporter_Handler->register_exporter([
                'name' => __('XLSX para o Inventário Nacional dos Bens Culturais Musealizados (INBCM)', 'tieta-tainacan'),
                'description' => __('Permite exportar sua coleção para um arquivo .XLSX conforme o modelo do inventário nacional dos bens culturais musealizados do IBRAM.', 'tieta-tainacan'),
                'slug' => 'inbcm-exporter',
                'class_name' => '\TietaTainacan\MuseumInventoryExporter', // Ensure this class is correctly defined and autoloaded
                'manual_mapping' => true,
                'manual_collection' => true,
            ]);
        }
    }
}

// Finally, instantiate our registrar class.
new TietaRegisterExporter();
