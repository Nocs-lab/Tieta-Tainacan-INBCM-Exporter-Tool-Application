<?php

namespace TietaTainacan;
use Tainacan\Mappers\Mapper;

if (!defined('ABSPATH')) {
    exit;
}


class MuseologyMapper extends Mapper {
    public $name = 'Schema.org Creative Works';
    public $slug = 'incbm-museum';
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
    public $type = 'CreativeWork';
}