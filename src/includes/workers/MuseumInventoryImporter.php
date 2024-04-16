<?php
namespace TietaTainacan;
use Tainacan\Importer\Importer;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Entity\Row;

if (!defined('ABSPATH')) {
    exit;
}

class MuseumInventoryImporter extends Importer {
    public function _construct() {
        parent::construct();
        $this->set_default_options([
            'foo' => 'bar'
        ]);

        $this->add_import_method('file');
        $this->remove_import_method('url');
        $this->set_accepted_mapping_methods('any');
    }

    // read header data from file
    public function get_source_metadata() {
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($this->get_option('file'));

        $header = $reader->getSheetIterator()->current()->getRowIterator()->current()->toArray();

        $reader->close();

        return $header;
    }

    public function process_item($item, $collection_definition) {
        // process item
        error_log("item: " . print_r($item, true) . "\n" . print_r($collection_definition, true));
    }

    public function options_form() {
        $form = '<div class="field">';
        $form .= '<label class="label">' . __('My Importer Option 1', 'tieta-tainacan') . '</label>';
        $form .= '<div class="control">';
        $form .= '<input type="text" class="input" name="my_importer_option_1" value="' . $this->get_option('my_importer_option_1') . '" />';
        $form .= '</div>';
        $form .= '</div>';

        return $form;
    }

}