<?php

namespace TietaTainacan;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure this file is loaded after Tainacan's core files,
// so that the Tainacan classes and functions are available.
class TietaExporterRegister {
    public function __construct() {
        add_action('init', [$this, 'registerCustomExporter']);
    }

    public function registerCustomExporter() {
        global $Tainacan_Exporter_Handler;

        if (isset($Tainacan_Exporter_Handler)) {
            $Tainacan_Exporter_Handler->register_exporter([
                'name' => __('XLSX for the National Inventory of Museum Cultural Assets (INBCM)', 'tieta-tainacan'),
                'description' => __('Allows you to export your collection to an .XLXS file according to the model of IBRAM\'s national inventory of cultural assets.', 'tieta-tainacan'),
                'slug' => 'inbcm-exporter',
                'class_name' => '\TietaTainacan\MuseumInventoryExporter', // Ensure this class is correctly defined and autoloaded
                'manual_mapping' => false,
                'manual_collection' => true,
            ]);
        }
    }
}

// Assuming the class definition for CustomTermCSVExporter exists and is autoloaded correctly.
// This class should extend the appropriate Tainacan exporter base class and implement required methods.

// Finally, instantiate our registrar class.
new TietaExporterRegister();
