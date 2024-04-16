<?php

namespace TietaTainacan;
use Tainacan\Mappers\Mapper;

if (!defined('ABSPATH')) {
    exit;
}


class BiblioteconomyMapper extends Mapper {
    public $name = 'Museum Inventory of Cultural Assets (INBCM)';
    public $slug = 'incbm-bible';
    public $metadata = [
        'name' => [
            'label'=> 'Name',
            'URI'=> 'http://schema.org/name'
        ],
        'alternativeHeadline' => [
            'label'=> 'Alternative Headline',
            'URI'=> 'http://schema.org/alternativeHeadline'
        ],
    ];
    public $allow_extra_fields = false;
    public $context_url = 'http://schema.org';
    public $type = 'INCBM';
}