<?php

namespace TietaTainacan;

if (!defined('ABSPATH')) {
    exit;
}

// Ensure this file is loaded after Tainacan's core files,
// so that the Tainacan classes and functions are available.
class TietaRegisterMappers {
    public function __construct() {
        $this->registerCustomMappers();
    }

    function tietaMappers($mappers) {
        $mappers->register_mapper('TietaTainacan\MuseologyMapper');
        $mappers->register_mapper('TietaTainacan\BiblioteconomyMapper');
        $mappers->register_mapper('TietaTainacan\ArchivologyMapper');
    }

    public function registerCustomMappers() {
        add_action('tainacan-register-mappers', [$this, 'tietaMappers']);
    }
}

// Finally, instantiate our registrar class.
new TietaRegisterMappers();