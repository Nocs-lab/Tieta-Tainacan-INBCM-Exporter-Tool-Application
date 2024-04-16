<?php

namespace TietaTainacan;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure this file is loaded after Tainacan's core files,
// so that the Tainacan classes and functions are available.
class TietaRegisterMappers {
    public function __construct() {
        add_action('init', [$this, 'Tieta_registerCustomMappers']);
    }

    public function Tieta_registerCustomMappers() {
       
        function tietaMappers($mappers) {
            error_log('Registering custom mappers'. print_r($mappers, true));

            $mappers->register_mapper('\TietaTainacan\MuseologyMapper');
            $mappers->register_mapper('\TietaTainacan\BiblioteconomyMapper');
            $mappers->register_mapper('\TietaTainacan\ArchivologyMapper');
        }
        
        add_action('tainacan-register-mappers', 'tietaMappers');
        error_log('Custom mappers registered');
    }
}

// Finally, instantiate our registrar class.
new TietaRegisterMappers();