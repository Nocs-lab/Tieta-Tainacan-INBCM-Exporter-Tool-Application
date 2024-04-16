<?
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