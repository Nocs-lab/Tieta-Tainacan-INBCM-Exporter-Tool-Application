<?php

namespace TietaTainacan;
use Tainacan\Mappers\Mapper;

if (!defined('ABSPATH')) {
    exit;
}


class BiblioteconomyMapper extends Mapper {
    public $name = 'INCBM Biblioteconomy Mapper';
    public $slug = 'incbm-bible';
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
        'titulo' => [
            'label' => 'Título',
        ],
        'tipo' => [
            'label' => 'Tipo',
        ],
        'identificacaoDeResponsabilidade' => [
            'label' => 'Identificação de responsabilidade',
        ],
        'localDeProducao' => [
            'label' => 'Local de produção',
        ],
        'editora' => [
            'label' => 'Editora',
        ],
        'dataDeProducao' => [
            'label' => 'Data de Produção',
        ],
        'dimensaoFisica' => [
            'label' => 'Dimensão física',
        ],
        'materialTecnica' => [
            'label' => 'Material / Técnica',
        ],
        'encadernacao' => [
            'label' => 'Encadernação',
        ],
        'resumoDescritivo' => [
            'label' => 'Resumo Descritivo',
        ],
        'estadoDeConservacao' => [
            'label' => 'Estado de Conservação',
        ],
        'assuntoPrincipal' => [
            'label' => 'Assunto Principal',
        ],
        'assuntoCronologico' => [
            'label' => 'Assunto Cronológico',
        ],
        'assuntoGeografico' => [
            'label' => 'Assunto Geográfico',
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
    public $type = 'INCBM';
}