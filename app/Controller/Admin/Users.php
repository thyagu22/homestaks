<?php

namespace App\Controller\Admin;


use App\Model\Entity\User as EntityUsers;
use App\Utils\View;
use App\Http\Request;
use WilliamCosta\DatabaseManager\Pagination;


class Users extends Page
{

    /**
     * obtem a renderização dos itens de Users para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUsersItems($request, &$obPagination)
    {
        //Users
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityUsers::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTÂCIA DE PAGINAÇAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityUsers::getUsers(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while($obUsers = $results->fetchObject(EntityUsers::class)) {
            $itens .= View::render('admin/modules/users/item', [
                'id' => $obUsers->id,
                'nome' => $obUsers->nome,
                'email' => $obUsers->email,
                'data' => date('d/m/Y H:i:s', strtotime($obUsers->data))
            ]);

        }
        //RETORNA OS UsersS
        return $itens;

    }

    /**
     * Renderiza a view de listagem de Users
     * @param Request $request
     * @return string
     */
    public static function getUsers($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUsersItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);


        //RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Users > TreinaTEK', $content, 'users');
    }

    /**
     * Retorna formulário de cadastro de novo User
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title' => 'Cadastrar Users',
            'nome' => '',
            'email' => '',
            'senha' => '',
            'status' => self::getStatus($request)


        ]);

        //CADASTRAR Users
        return parent::getPanel('Cadastrar Users > TreinaTEK', $content, 'users');
    }

    /**
     * Cadastra no DB o novo User
     * @param Request $request
     */
    public static function setNewUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //*** VALIDA EMAIL ***
        $obUsers = EntityUsers::getUserByEmail($email);

        //VALIDA @TEKNISA
        if(!preg_match_all('#@teknisa.com#',$email)){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/new?status=notek');
        }

        //VERIFICA DUPLICIDADE
        if($obUsers instanceof EntityUsers){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        // *** ---------- ***

        //NOVA INSTANCIA DE Users
        $obUsers = new EntityUsers();
        $obUsers->nome = $nome;
        $obUsers->email = $email;
        $obUsers->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obUsers->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/' . $obUsers->id . '/edit?status=created');

    }

    /**
     * Retorna mesagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if(!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
                break;
                case 'notek':
                return Alert::getError('Ooops! Por favor utilize um e-mail @teknisa.');
                break;
            case 'duplicated':
                return Alert::getError('Ooops! O e-mail digitado já está sendo utilizado por outro usuário. :(');
                break;
        }

    }

    /**
     * Retorna formulário de edição de Users
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id)
    {
        //OBTEM Users DO DB
        $obUsers = EntityUsers::getUsersById($id);

        //VALIDA A INSTANCIA
        if(!$obUsers instanceof EntityUsers) {
            $request->getRouter()->redirect('admin/users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title' => 'Editar Usuário',
            'nome' => $obUsers->nome,
            'email' => $obUsers->email,
            'status' => self::getStatus($request)


        ]);

        //CADASTRAR Users
        return parent::getPanel('Editar Usuário > TreinaTEK', $content, 'users');
    }

    /**
     * Grava a edição de Users
     * @param Request $request
     * @param integer $id
     */
    public static function setEditUser($request, $id)
    {
        //OBTEM Users DO DB
        $obUsers = EntityUsers::getUsersById($id);

        //VALIDA A INSTANCIA
        if(!$obUsers instanceof EntityUsers) {
            $request->getRouter()->redirect('admin/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email']?? '';
        $senha = $postVars['senha'] ?? '';


        //VALIDA @TEKNISA
        if(!preg_match_all('#@teknisa.com#',$email)){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=notek');
        }

        //VERIFICA DUPLICIDADE
        $obUserEmail = EntityUsers::getUserByEmail($email);
        if($obUserEmail instanceof EntityUsers && $obUserEmail->id != $id){
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/' . $id . '/edit?status=duplicated');
        }

        //ATUALIZA A INSTANCIA
        $obUsers->nome = $nome;
        $obUsers->email = $email;
        $obUsers->senha = password_hash($senha,PASSWORD_DEFAULT);
        $obUsers->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/' . $obUsers->id . '/edit?status=updated');

    }

    /**
     * Retorna formulário de exclusão de Users
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id)
    {
        //OBTEM Users DO DB
        $obUsers = EntityUsers::getUsersById($id);

        //VALIDA A INSTANCIA
        if(!$obUsers instanceof EntityUsers) {
            $request->getRouter()->redirect('/admin/users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('/admin/modules/users/delete', [

            'nome' => $obUsers->nome,
            'email' => $obUsers->email

        ]);

        //CADASTRAR Users
        return parent::getPanel('Excluir Usuário > TreinaTEK', $content, 'users');
    }

    /**
     * Exclui Users do DB
     * @param Request $request
     * @param integer $id
     */
    public static function setDeleteUser($request, $id)
    {
        //OBTEM Users DO DB
        $obUsers = EntityUsers::getUsersById($id);

        //VALIDA A INSTANCIA
        if(!$obUsers instanceof EntityUsers) {
            $request->getRouter()->redirect('admin/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTANCIA
        $obUsers->nome = $postVars['nome'] ?? $obUsers->nome;
        $obUsers->email = $postVars['email'] ?? $obUsers->email;
        $obUsers->deletar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');

    }


}