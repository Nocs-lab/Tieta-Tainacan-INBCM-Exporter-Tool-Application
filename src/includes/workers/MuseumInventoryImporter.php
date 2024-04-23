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
        // error_log('Processing item at index ' . $index . ' in collection ' . print_r($collection_definition, true));
    
        // Initialize the result array
        $results = []; // This will be an array of mappings
    
        // Check file existence
        if (file_exists($this->get_tmp_file())) {
            $reader = ReaderEntityFactory::createReaderFromFile($this->get_tmp_file());
            $reader->open($this->get_tmp_file());
    
            $header = [];
            $rowCounter = 0;
    
            // Iterate over each sheet and row
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    if ($rowCounter === 0) {
                        $header = $row->toArray();
                    } else if ($rowCounter - 1 === $index) {
                        $rowData = $row->toArray();
                        // $mappedData = ['collection' => $collection_definition['id']];
                        foreach ($collection_definition['mapping'] as $id => $columnName) {
                            $columnIndex = array_search($columnName, $header);
                            if ($columnIndex !== false) {
                                // Fill the 'mapping' array with metadata ID as key and cell data as value
                                $mappedData[$columnName] = strval($rowData[$columnIndex]);
                            } else {
                                error_log('Column not found for mapping: ' . $columnName);
                            }
                        }
                        $results = $mappedData; // Append the mapped data for this item to the results
                        break; // Process only the needed row
                    }
                    $rowCounter++;
                }
                if ($rowCounter - 1 > $index) {
                    break; // Stop if passed the needed row
                }
            }
            $reader->close();
        } else {
            // Handle file not found error
            // error_log('File not found: ' . $this->get_tmp_file());
            return false;
        }
    
        // error_log('Mapped data: ' . print_r($results, true));
        return $results; // Return the results array
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