<?php

namespace TietaTainacan;
use Tainacan\Mappers\Mapper;

if (!defined('ABSPATH')) {
    exit;
}


class ArchivologyMapper extends Mapper {
    public $name = 'INCBM Archivology Mapper';
    public $slug = 'incbm-archive';
    public $metadata = [
        'codigoDeReferencia' => [
            'label' => 'Cód. de Referência',
        ],
        'titulo' => [
            'label' => 'Título',
        ],
        'data' => [
            'label' => 'Data',
        ],
        'nivelDeDescricao' => [
            'label' => 'Nível de Descrição',
        ],
        'dimensaoESuporte' => [
            'label' => 'Dimensão e suporte',
        ],
        'nomeDoProdutor' => [
            'label' => 'Nome do Produtor',
        ],
        'historiaAdministrativaBiografia' => [
            'label' => 'História administrativa / Biografia',
        ],
        'historiaArquivistica' => [
            'label' => 'História Arquivística',
        ],
        'procedencia' => [
            'label' => 'Procedência',
        ],
        'ambitoEConteudo' => [
            'label' => 'Âmbito e Conteúdo',
        ],
        'sistemaDeArranjo' => [
            'label' => 'Sistema de Arranjo',
        ],
        'condicoesDeReproducao' => [
            'label' => 'Condições de Reprodução',
        ],
        'existenciaELocalizacaoDosOriginais' => [
            'label' => 'Existência e Localização dos Originais',
        ],
        'notasSobreConservacao' => [
            'label' => 'Notas sobre conservação',
        ],
        'pontosDeAcessoEIndexacaoDeAssuntos' => [
            'label' => 'Pontos de acesso e indexação de assuntos',
        ],
        'midiasRelacionadas' => [
            'label' => 'Mídias Relacionadas',
        ],
    ];
    
    public $allow_extra_fields = false;
    public $context_url = 'http://schema.org';
    public $type = 'CreativeWork';
}