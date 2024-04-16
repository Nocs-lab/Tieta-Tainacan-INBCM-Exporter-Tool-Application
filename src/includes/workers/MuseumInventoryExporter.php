<?php
namespace TietaTainacan;
use Tainacan\Exporter\Exporter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Entity\Row;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class MuseumInventoryExporter extends Exporter {
    private $collection_name;
    private $writer;
    private $file_path;
    private $tempFilePath;

    public function __construct($attributes = array()) {
        parent::__construct($attributes);

        // Define o nome do arquivo baseado na coleção atual
        if ($current_collection = $this->get_current_collection_object()) {
            $name = $current_collection->get_name();
            $this->collection_name = sanitize_title($name) . "_export.xlsx"; // Atualizado para refletir o formato XLSX
        } else {
            $this->collection_name = "inbcm_export"; // Atualizado para refletir o formato XLSX
        }

        // O writer será inicializado mais tarde, no momento adequado do processo de exportação
        $this->writer = null;
        $upload_dir = wp_upload_dir();
        $this->filePath = $upload_dir['path'] . '/' . $this->collection_name;
        $this->tempFilePath = "";

        // As opções abaixo podem ser removidas ou adaptadas se não mais relevantes para a exportação em XLSX
        $this->set_default_options([
            'delimiter' => ',',
            'multivalued_delimiter' => '||',
            'enclosure' => '"',
        ]);
        $this->set_accepted_mapping_methods('list', "incbm-museum", ["incbm-museum", "incbm-library", "incbm-archive"]);
        $this->accept_no_mapping = false;
    }

    public function initializeWriter() {
        
        // Verifica se o arquivo já existe para ler os dados existentes
        if (file_exists($this->filePath)) {
            $tempFilePath = "";
            // Cria um novo arquivo temporário para escrita
            $tempFilePath = $this->filePath . "temp_" .  uniqid() . ".xlsx";
    
            $reader = ReaderEntityFactory::createReaderFromFile($this->filePath);
            $reader->open($this->filePath);
    
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($tempFilePath);
    
            // let's read the entire spreadsheet...
            foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                // Add sheets in the new file, as we read new sheets in the existing one
                if ($sheetIndex !== 1) {
                    $writer->addNewSheetAndMakeItCurrent();
                }

                foreach ($sheet->getRowIterator() as $row) {
                    // ... and copy each row into the new spreadsheet
                    $writer->addRow($row);
                }
            }
    
            $reader->close();
        } else {
            // Se o arquivo não existir, cria um novo arquivo
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToFile($this->filePath);
        }
    
        $this->writer = $writer;
        $this->tempFilePath = isset($tempFilePath) ? $tempFilePath : null;
    }
    
    
    
    public function finalizeWriter() {
        if (isset($this->writer)) {
            $this->writer->close();
    
            // Se um arquivo temporário foi criado, substitua o arquivo original por este.
            if ($this->tempFilePath) {
                // Remove o arquivo original
                if (file_exists($this->filePath)) {
                    unlink($this->filePath);
                }
                // Renomeia o arquivo temporário para ser o arquivo final
                rename($this->tempFilePath, $this->filePath);
            }
        }
    }

    public function filter_multivalue_separator($separator) {
        return $this->get_option('multivalued_delimiter');
    }

    public function filter_hierarchy_separator($separator) {
        return '>>';
    }

    private function get_date_value($meta) {
        $metadatum = $meta->get_metadatum();
        $date_value = 'ERROR ON FORMATING DATE';
        if (is_object($metadatum)) {
            $fto = $metadatum->get_metadata_type_object();
            if (is_object($fto) && method_exists($fto, 'get_value_as_html')) {
                $fto->output_date_format = 'Y-m-d';
                $date_value = $fto->get_value_as_html($meta);
            }
        }
        return $date_value;
    }

    function get_compound_metadata_cell($meta) {
        $enclosure = $this->get_option('enclosure');
        $delimiter = $this->get_option('delimiter');
        $multivalued_delimiter = $this->get_option('multivalued_delimiter');

        $metadata_type_options = $meta->get_metadatum()->get_metadata_type_options();
        $initial_values = [];
        foreach ($metadata_type_options['children_order'] as $order) {
            $initial_values[$order['id']] = "";
        }
        $values = ($meta->get_metadatum()->is_multiple() ? $meta->get_value() : [$meta->get_value()]);
        $array_meta = [];
        foreach ($values as $value) {
            $assoc_arr = array_reduce($value, function ($result, $item) {
                $metadatum_id = $item->get_metadatum()->get_id();
                if ($item->get_metadatum()->get_metadata_type() == 'Tainacan\Metadata_Types\Relationship') {
                    $result[$metadatum_id] = $item->get_value();
                } else {
                    $result[$metadatum_id] = $item->get_value_as_string();
                }
                return $result;
            }, $initial_values);

            $array_meta[] = $this->str_putcsv($assoc_arr, $delimiter, $enclosure);
        }
        return implode($multivalued_delimiter, $array_meta);
    }

    function get_document_cell($item) {
        $type = $item->get_document_type();
        if ($type == 'attachment') {
            $type = 'file';
        }

        $document = $item->get_document();

        if ($type == 'file') {
            $url = wp_get_attachment_url($document);
            if ($url) {
                $document = $url;
            }

        }

        return $type . ':' . $document;

    }

    function get_attachments_cell($item) {
        $attachments = $item->get_attachments();

        $attachments_urls = array_map(function ($a) {
            if (isset($a->guid)) {
                return $a->guid;
            }

        }, $attachments);

        return implode($this->get_option('multivalued_delimiter'), $attachments_urls);
    }

    function get_author_name_last_modification($item_id) {
        $last_id = get_post_meta($item_id, '_user_edit_lastr', true);
        if ($last_id) {
            $last_user = get_userdata($last_id);
            return apply_filters('tainacan-the-modified-author', $last_user->display_name);
        }
        return "";
    }

    private function get_description_title_meta($meta) {
        $meta_type = explode('\\', $meta->get_metadata_type());
        $meta_type = strtolower($meta_type[sizeof($meta_type) - 1]);

        $meta_section_name = '';
        if ($this->get_option('add_section_name') == 'yes' && $current_collection = $this->get_current_collection_object()) {
            $meta_section_id = $meta->get_metadata_section_id();
            $collection_id = $current_collection->get_id();

            if ($meta->is_repository_level()) {
                foreach ($meta_section_id as $section_id) {
                    if ($collection_id == get_post_meta($section_id, 'collection_id', true)) {
                        $meta_section_name = '(' . get_the_title($section_id) . ')';
                        continue;
                    }
                }
            } else {
                if ($meta_section_id != \Tainacan\Entities\Metadata_Section::$default_section_slug) {
                    $meta_section_name = '(' . get_the_title($meta_section_id) . ')';
                }
            }
        }

        if ($meta_type == 'compound') {
            $enclosure = $this->get_option('enclosure');
            $delimiter = $this->get_option('delimiter');
            $metadata_type_options = $meta->get_metadata_type_options();
            $desc_childrens = [];
            foreach ($metadata_type_options['children_objects'] as $children) {
                $children_meta_type = explode('\\', $children['metadata_type']);
                $children_meta_type = strtolower($children_meta_type[sizeof($children_meta_type) - 1]);
                $children_meta_type .= ($children['collection_key'] === 'yes' ? '|collection_key_yes' : '');
                $desc_childrens[] = $children['name'] . '|' . $children_meta_type;
            }
            $meta_type .= "(" . implode($delimiter, $desc_childrens) . ")";
            $desc_title_meta =
            $meta->get_name() .
                $meta_section_name .
                ('|' . $meta_type) .
                ($meta->is_multiple() ? '|multiple' : '') .
                ('|display_' . $meta->get_display());
        } else {
            $desc_title_meta =
            $meta->get_name() .
                $meta_section_name .
                ('|' . $meta_type) .
                ($meta->is_multiple() ? '|multiple' : '') .
                ($meta->is_required() ? '|required' : '') .
                ('|display_' . $meta->get_display()) .
                ($meta->is_collection_key() ? '|collection_key_yes' : '');
        }
        return $desc_title_meta;
    }

    function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');

        fputcsv($fp, $input, $delimiter, $enclosure);
        rewind($fp);
        //Getting detailed stats to check filesize:
        $fstats = fstat($fp);
        $data = fread($fp, $fstats['size']);
        fclose($fp);
        return rtrim($data, "\n");
    }   
    
    private function get_collections_names() {
        $collections_names = [];
        foreach ($this->collections as $col) {
            $collection = \Tainacan\Repositories\Collections::get_instance()->fetch((int) $col['id'], 'OBJECT');
            $collections_names[] = $collection->get_name();
        }
        return $collections_names;
    }


    public function process_item($item, $metadata) {
        
        // logs to error_log this call
        error_log('Processing item ' . $item->get_id());

        $line = [];
    
        $line[] = $item->get_id();
    
        add_filter('tainacan-item-metadata-get-multivalue-separator', [$this, 'filter_multivalue_separator'], 20);
        add_filter('tainacan-terms-hierarchy-html-separator', [$this, 'filter_hierarchy_separator'], 20);
    
        foreach ($metadata as $meta_key => $meta) {
            if (!$meta || empty($meta->get_value())) {
                $line[] = '';
                continue;
            }
    
            if ($meta->get_metadatum()->get_metadata_type() == 'Tainacan\Metadata_Types\Relationship') {
                $rel = $meta->get_value();
                $line[] = is_array($rel) ? implode($this->get_option('multivalued_delimiter'), $rel) : $rel;
            } elseif ($meta->get_metadatum()->get_metadata_type() == 'Tainacan\Metadata_Types\Compound') {
                $line[] = $this->get_compound_metadata_cell($meta);
            } elseif ($meta->get_metadatum()->get_metadata_type() == 'Tainacan\Metadata_Types\Date') {
                $line[] = $this->get_date_value($meta); // Supondo que você tenha um método para formatar a data
            } else {
                $line[] = $meta->get_value_as_string();
            }
        }
    
        remove_filter('tainacan-item-metadata-get-multivalue-separator', [$this, 'filter_multivalue_separator']);
        remove_filter('tainacan-terms-hierarchy-html-separator', [$this, 'filter_hierarchy_separator']);
    
        $line = array_merge($line, [
            $item->get_status(),
            $this->get_document_cell($item),
            $this->get_attachments_cell($item),
            $item->get_comment_status(),
            $item->get_author_name(),
            $item->get_creation_date(),
            $this->get_author_name_last_modification($item->get_id()),
            $item->get_modification_date(),
            get_permalink($item->get_id())
        ]);
        // Verifique se $this->writer é uma instância do writer do Spout e está aberto
        $this->initializeWriter();
        $row = WriterEntityFactory::createRowFromArray($line);
        $this->writer->addRow($row);
        $this->finalizeWriter();
   
        
    }
    // o exportador deve implementar o método output_header
    public function output_header() {
        
        $this->initializeWriter();

        $mapper = $this->get_current_mapper();
    
        $headerRowContents = ['special_item_id'];
    
        if ($mapper) {
            foreach ($mapper->metadata as $meta_slug => $meta) {
                $headerRowContents[] = $meta_slug;
            }
        } else {
            $collection = $this->get_current_collection_object();
            if ($collection) {
                $metadata = $collection->get_metadata();
                foreach ($metadata as $meta) {
                    $desc_title_meta = $this->get_description_title_meta($meta);
                    $headerRowContents[] = $desc_title_meta;
                }
            }
        }
    
        $headerRowContents = array_merge($headerRowContents, [
            'special_item_status',
            'special_document',
            'special_attachments',
            'special_comment_status',
            'author_name',
            'creation_date',
            'user_last_modified',
            'modification_date',
            'public_url'
        ]);
    
    
        $headerRow = WriterEntityFactory::createRowFromArray($headerRowContents);
        $this->writer->addRow($headerRow);

        $this->finalizeWriter();
       
    }
    
    public function output_footer() {
        // Este método será invocado no final da exportação de cada coleção
        $this->finalizeWriter();
    }
    /**
     * When exporter is finished, gets the final output
     */
    public function get_output() {

            // Obtém o caminho base do diretório de uploads do WordPress
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['path'];
            $file_name = $this->collection_name . '.xlsx';
            $file_path = $upload_path . '/' . $file_name;
    
            // Verifica se o arquivo foi criado
            if (file_exists($file_path)) {
                $current_user = wp_get_current_user();
                $author_name = $current_user->user_login;
    
                $file_url = $upload_dir['url'] . '/' . $file_name;
    
                $message = __('Target collections:', 'tieta-tainacan');
                $message .= " <b>" . implode(", ", $this->get_collections_names()) . "</b><br/>";
                $message .= __('Exported by:', 'tieta-tainacan');
                $message .= " <b>$author_name</b><br/>";
                $message .= __('Your XLSX file is ready! Access it in the link below:', 'tieta-tainacan');
                $message .= '<br/><br/>';
                $message .= '<a target="_blank" href="' . $file_url . '">Download</a>';
    
                return $message;
            } else {
                $this->add_error_log('Output file not found! Maybe you need to correct the permissions of your upload folder');
                return __('Error creating the file. Check the permissions of your upload folder.', 'tieta-tainacan');
            }

    }
    
    public function options_form() {
        ob_start();
        ?>
            <!-- select option field -->
            <div class="field">
            <label class="label"><?php _e('Tipo de Inventário', 'tieta-tainacan');?></label>
                <span class="help-wrapper">
                        <a class="help-button has-text-secondary">
                            <span class="icon is-small">
                                 <i class="tainacan-icon tainacan-icon-help" ></i>
                             </span>
                        </a>
                        <div class="help-tooltip">
                            <div class="help-tooltip-header">
                                <h5><?php _e('CSV Delimiter', 'tieta-tainacan');?></h5>
                            </div>
                            <div class="help-tooltip-body">
                                <p><?php _e('The inventory tipe you wish to export, each will result in a diferent csv format', 'tieta-tainacan');?></p>
                            </div>
                        </div>
                </span>
                <div class="control is-expanded">
                     <!-- Museologia, Biblioteconomia ou Arquivologia -->
                    <span class="select is-fullwidth is-empty">
                        <select name="tipo_inventario">
                            <option value="1">Inventário de Museologia</option>
                            <option value="2">Inventário de Biblioteconomia</option>
                            <option value="3">Inventário de Arquivologia</option>
                        </select>
                    </span>
                </div>
            </field>
            <div class="field">
                <label class="label"><?php _e('CSV Delimiter', 'tieta-tainacan');?></label>
                <span class="help-wrapper">
                        <a class="help-button has-text-secondary">
                            <span class="icon is-small">
                                 <i class="tainacan-icon tainacan-icon-help" ></i>
                             </span>
                        </a>
                        <div class="help-tooltip">
                            <div class="help-tooltip-header">
                                <h5><?php _e('CSV Delimiter', 'tieta-tainacan');?></h5>
                            </div>
                            <div class="help-tooltip-body">
                                <p><?php _e('The character used to separate each column in your CSV (e.g. , or ;)', 'tieta-tainacan');?></p>
                            </div>
                        </div>
                </span>
                <div class="control is-clearfix">
                    <input class="input" type="text" name="delimiter" maxlength="1" value="<?php echo esc_attr($this->get_option('delimiter')); ?>">
                </div>
            </div>

            <div class="field">
                <label class="label"><?php _e('Multivalued metadata delimiter', 'tieta-tainacan');?></label>
                <span class="help-wrapper">
                        <a class="help-button has-text-secondary">
                            <span class="icon is-small">
                                 <i class="tainacan-icon tainacan-icon-help" ></i>
                             </span>
                        </a>
                        <div class="help-tooltip">
                            <div class="help-tooltip-header">
                                <h5><?php _e('Multivalued metadata delimiter', 'tieta-tainacan');?></h5>
                            </div>
                            <div class="help-tooltip-body">
                                <p><?php _e('The character used to separate each value inside a cell with multiple values (e.g. ||). Note that the target metadatum must accept multiple values.', 'tainacan');?></p>
                            </div>
                        </div>
                </span>
                <div class="control is-clearfix">
                    <input class="input" type="text" name="multivalued_delimiter" value="<?php echo esc_attr($this->get_option('multivalued_delimiter')); ?>">
                </div>
            </div>

            <div class="field">
                <label class="label"><?php _e('Enclosure', 'tieta-tainacan');?></label>
                <span class="help-wrapper">
                        <a class="help-button has-text-secondary">
                            <span class="icon is-small">
                                 <i class="tainacan-icon tainacan-icon-help" ></i>
                             </span>
                        </a>
                        <div class="help-tooltip">
                            <div class="help-tooltip-header">
                                <h5><?php _e('Enclosure', 'tieta-tainacan');?></h5>
                            </div>
                            <div class="help-tooltip-body">
                                <p><?php _e('The character that wraps the content of each cell in your CSV. (e.g. ")', 'tieta-tainacan');?></p>
                            </div>
                        </div>
                </span>
                <div class="control is-clearfix">
                    <input class="input" type="text" name="enclosure" value="<?php echo esc_attr($this->get_option('enclosure')); ?>">
                </div>
            </div>

            <div class="field">
                <label class="label"><?php _e('Include metadata section name', 'tieta-tainacan');?></label>
                <span class="help-wrapper">
                        <a class="help-button has-text-secondary">
                            <span class="icon is-small">
                                <i class="tainacan-icon tainacan-icon-help" ></i>
                            </span>
                        </a>
                        <div class="help-tooltip">
                            <div class="help-tooltip-header">
                                <h5><?php _e('Include metadata section name', 'tieta-tainacan');?></h5>
                            </div>
                            <div class="help-tooltip-body">
                                <p><?php _e('Include metadatum section name after the metadatum name. Metadata inside the default section are not modified', 'tainacan');?></p>
                            </div>
                        </div>
                </span>
                <div class="control is-clearfix">
                    <label class="checkbox">
                        <input
                            type="checkbox"
                            name="add_section_name" checked value="yes"
                            >
                        <?php _e('Yes', 'tieta-tainacan');?>
                    </label>
                </div>
            </div>

          <?php
        return ob_get_clean();
    }
}