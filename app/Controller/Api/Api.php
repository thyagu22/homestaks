<?php

namespace App\Controller\Api;

use App\Http\Request;
use WilliamCosta\DatabaseManager\Pagination;

class Api
{
    /**
     * Retorna os detalhes da API
     * @param Request $request
     * @return array
     */
    public static function getDetails($request)
    {
        return [
            'nome' => 'API - TreinaTEK',
            'versao' => 'v1.0.0',
            'autor' => 'Thiago Campos',
            'email' => 'thiago_stecnico@hotmail.com'
        ];
        
    }

    /**
     * @param Request $request
     * @param Pagination $obpagination
     * @return array
     */
    protected static function getPagination($request, $obpagination) {
        //QUERY PARAMS
        $queryparams = $request->getQueryParams();

        //PÁGINA
        $pages = $obpagination->getPages();

        //RETORNO
        return [
            'paginaAtual' => isset($queryparams['page']) ? (int)$queryparams['pages'] : 1,
            'quantidadedePaginas' => !empty($pages) ? count($pages) : 1
        ];

    }

}