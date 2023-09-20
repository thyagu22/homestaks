<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DatabaseManager\Pagination;

class User extends Api
{
    /**
     * obtem a renderização dos itens de usuários para a API
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUsersItens($request, &$obPagination)
    {
        //USERS
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityUser::getUsers(null, null, null,null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂCIA DE PAGINAÇAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null, null,'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUser = $results->fetchObject(EntityUser::class)) {

            $itens[] =  [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email,
                'data' => $obUser->data
            ];

        }
        //RETORNA OS FEEDBACKS
        return $itens;

    }

    /**
     * Retorna os usuários cadastrados
     * @param Request $request
     * @return array
     */
    public static function getUsers(Request $request): array
    {
        return [
            'users' => self::getUsersItens($request,$obPagination),
            'paginacao' => parent::getPagination($request,$obPagination)
        ];
        
    }

    /**
     * Retorna o usuário pelo email
     * @param integer $id
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function getUser($request,$id)
    {
        //VALIDA SE O ID É INT
        if(!is_numeric($id)){
            throw new \Exception("O id '" .$id. "' não é válido",400);
        }
        
        //BUSCA USUÁRIO
        $obUser = EntityUser::getUsersById($id);



        //VALIDA SE USUÁRIO EXISTE
        if(!$obUser instanceof EntityUser){
            throw new \Exception("O Usuário " .$id. " não foi encontrado",404);
        }

        //RETORNA OS DETALHES DO FEEDBACK
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'data' => $obUser->data
        ];

    }

    /**
     * Cadastra um novo feedback
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function setNewUser($request){
        // POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['email'])){
            throw new \Exception("Os campos 'nome' e 'email' são obrigatórios",40 );
        }

        //VALIDA SE O EMAIL É @TEKNISA
        if(!preg_match_all('#@teknisa.com#',$postVars['email'])){
            throw new \Exception("utilize um e-mail @teknisa",40 );
        }

        $obUsers = EntityUser::getUserByEmail($postVars['email']);

        //VERIFICA DUPLICIDADE
        if($obUsers instanceof EntityUser){
            throw new \Exception("O 'email' já existe ",40 );
        }

        //GERA SENHA ALEATÓRIA
        $chars = 'aabbccddeeffgghhiijjkklmmnnooppqqrrssttuuvvwwxxyyzz112233445566778899';
        $pswRamdon = substr(str_shuffle($chars),0,6);

        //NOVO USUÁRIO
        $obUser = new EntityUser();
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($pswRamdon, PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //RETORNA OS DETALHES DO USUÁRIO CADASTRADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'data' => $obUser->data
        ];

    }

    /**
     * Atualiza um  feedback
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function setEditFeedback($request, $id){
        // POST VARS
        $postVars = $request->getPostVars();

        //VALIDA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['mensagem'])){
            throw new \Exception("Os campos 'nome' e 'mensagem' são obrigatórios",40 );
        }

        //BUSCA O FEEDBACK NO BANCO
        $obUser = EntityFeedback::getFeedbackById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityFeedback){
            throw new \Exception("O depoimento " .$id. " não foi encontrado",404);
        }

        //ATUALIZA FEEBACK
        $obUser->nome = $postVars['nome'];
        $obUser->mensagem = $postVars['mensagem'];
        $obUser->atualizar();

        //RETORNA OS DETALHES DO FEEDBACK ATUALIZADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'mensagem' => $obUser->mensagem,
            'data' => $obUser->data
        ];

    }

    /**
     * Exclui um  feedback
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Exception
     */
    public static function setDeleteUser($request, $id): array
    {
        //BUSCA O FEEDBACK NO BANCO
        $obUser = EntityUser::getUsersById($id);

        //VALIDA A INSTANCIA
        if(!$obUser instanceof EntityUser){
            throw new \Exception("Usuário com  " .$id. " não foi encontrado",404);
        }

        //EXCLUI FEEBACK
        $obUser->deletar();

        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso' => true
        ];

    }


}