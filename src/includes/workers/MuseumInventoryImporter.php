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

    public function get_source_metadata() {
        if(file_exists($this->get_tmp_file())) {
            $reader = ReaderEntityFactory::createReaderFromFile($this->get_tmp_file());
            $reader->open($this->get_tmp_file());
            foreach ($reader->getSheetIterator() as $sheet) {
                // read first row and return it
                foreach ($sheet->getRowIterator() as $row) {
                    // do stuff with the row
                    $cells = $row->toArray();
                    return $cells;
                }
            }
        }
        return [];
    }

    public function process_item($index, $collection_definition) {
        error_log('Processing item at index ' . $index . ' in collection ' . print_r($collection_definition, true));
    
        $mappedData = [];
        $mappedData['id'] = $collection_definition['id'];
        if (file_exists($this->get_tmp_file())) {
            $reader = ReaderEntityFactory::createReaderFromFile($this->get_tmp_file());
            $reader->open($this->get_tmp_file());
    
            $header = [];
            $rowCounter = 0;
    
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowCounter === 0) {
                        $header = $row->toArray();
                        error_log('Headers found: ' . print_r($header, true));
                    } else {
                        if ($rowCounter - 1 === $index) {
                            $rowData = $row->toArray();
                            error_log('Data for index ' . $index . ': ' . print_r($rowData, true));
                            foreach ($collection_definition['mapping'] as $id => $columnName) {
                                $columnIndex = array_search($columnName, $header);
                                if ($columnIndex !== false) {
                                    $mappedData['mapping'][$id] = $rowData[$columnIndex];
                                } else {
                                    error_log('Column not found for mapping: ' . $columnName);
                                }
                            }
                            break;
                        }
                    }
                    $rowCounter++;
                }
                if ($rowCounter - 1 > $index) {
                    break;
                }
            }
            $reader->close();
        }
    
        error_log('Mapped data: ' . print_r($mappedData, true));
        return $mappedData;
    }
    
    

    public function options_form() { 
        ob_start();
        ?>
         <div>
                <h2><?php _e('INCMB Exporter', 'tieta-tainacan'); ?></h2>
                <p><?php _e('This importer will generatemap a XLSX file with the metadata of the items in the collection.', 'tieta-tainacan'); ?></p>
                <p><?php _e('Before importer make sure to map your collection accordingly', 'tieta-tainacan'); ?></p>
            </div>
        <?php
        return ob_get_clean();
    }

}