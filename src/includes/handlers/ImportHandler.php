<?php

namespace TietaTainacan;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure this file is loaded after Tainacan's core files,
// so that the Tainacan classes and functions are available.
class TietaRegisterImporter {
    public function __construct() {
        add_action('init', [$this, 'Tieta_registerCustomImporter']);
    }

    public function Tieta_registerCustomImporter() {
        global $Tainacan_Importer_Handler;

        if (isset($Tainacan_Importer_Handler)) {
            $Tainacan_Importer_Handler->register_importer([
                'name' => __('XLSX for the National Inventory of Museum Cultural Assets (INBCM)', 'tieta-tainacan'),
                'description' => __('Allows you to import your collection from a .XLXS file according to the model of IBRAM\'s national inventory of cultural assets.', 'tieta-tainacan'),
                'slug' => 'inbcm-importer',
                'class_name' => '\TietaTainacan\MuseumInventoryImporter', // Ensure this class is correctly defined and autoloaded
                'manual_mapping' => true,
                'manual_collection' => true,
            ]);
        }
    }
}

// Finally, instantiate our registrar class.
new TietaRegisterImporter();
