<?php

namespace TietaTainacan;
use Tainacan\Mappers\Mapper;

if (!defined('ABSPATH')) {
    exit;
}


class MuseologyMapper extends Mapper {
    public $name = 'INCBM Museology Mapper';
    public $slug = 'incbm-museum';
    public $metadata = [
        'numeroDeRegistro' => [
            'label' => 'Nº de Registro',
        ],
        'outrosNumeros' => [
            'label' => 'Outros Números',
        ],
        'situacao' => [
            'label' => 'Situação',
        ],
        'denominacao' => [
            'label' => 'Denominação',
        ],
        'titulo' => [
            'label' => 'Título',
        ],
        'autor' => [
            'label' => 'Autor',
        ],
        'classificacao' => [
            'label' => 'Classificação',
        ],
        'resumoDescritivo' => [
            'label' => 'Resumo Descritivo',
        ],
        'dimensoes' => [
            'label' => 'Dimensões',
        ],
        'altura' => [
            'label' => 'Altura',
        ],
        'largura' => [
            'label' => 'Largura',
        ],
        'profundidade' => [
            'label' => 'Profundidade',
        ],
        'diametro' => [
            'label' => 'Diâmetro',
        ],
        'espessura' => [
            'label' => 'Espessura',
        ],
        'unidadeDePesagem' => [
            'label' => 'Unid. de pesagem',
        ],
        'peso' => [
            'label' => 'Peso',
        ],
        'materialTecnica' => [
            'label' => 'Material/Técnica',
        ],
        'estadoDeConservacao' => [
            'label' => 'Estado de Conservação',
        ],
        'localDeProducao' => [
            'label' => 'Local de Produção',
        ],
        'dataDeProducao' => [
            'label' => 'Data de Produção',
        ],
        'condicoesDeReproducao' => [
            'label' => 'Condições de Reprodução',
        ],
        'midiasRelacionadas' => [
            'label' => 'Mídias Relacionadas',
        ],
    ];
    
    public $allow_extra_fields = false;
    public $context_url = 'http://schema.org';
    public $type = 'CreativeWork';
}