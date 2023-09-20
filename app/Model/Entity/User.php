<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário
     * @var int
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * Email do usuário
     * @var string
     */
    public $email;

    /**
     * Senha do usuário
     * @var string
     */
    public $senha;

    /**
     * Retorna usuário com base no email
     * @param string $email
     * @return user
     */
    public static function getUserByEmail($email)
    {
        return self::getUsers('email = "'.$email.'"')->fetchObject(self::class);
    }

/**
 * Cadastra instância atual no DB
 * @return boolean
 */
public function cadastrar()
{
    //DEFINE A DATA
    $this->data = date('Y-m-d H:i:s');

    $this->id = (new Database('users'))->insert([
        'nome' => $this->nome,
        'email' => $this->email,
        'senha' => $this->senha,
        'data' => $this->data
    ]);

    //SUCESSO
    return true;
}

/**
 * Atualiza DB com os dados da instancia atual
 * @return string
 */
public function atualizar()
{
    //ATUALIZA USER NO DB
    return (new Database('users'))->update('id = '.$this->id,[
        'nome' => $this->nome,
        'email' => $this->email,
        'senha' => $this->senha
    ]);
}

/**
 * Exclui USER no DB com os dados da instancia atual
 * @return string
 */
public function deletar()
{
    //EXCLUI USER NO DB
    return (new Database('users'))->delete('id = '.$this->id);
}

/**
 * Retorna um USER com base no ID
 * @param integer $id
 * @return Feedback
 */
public static function getUsersById($id)
{
    return self::getUsers('id = '.$id)->fetchObject(self::class);

}

/**
 * Retorna todos USERS
 * @param string|null $where
 * @param string|null $order
 * @param string|null $limit
 * @param string $fields
 * @return \PDOStatement
 */
public static function getUsers($where = null, $group = null, $order = null, $limit = null, $fields = '*')
{
    return (new Database('users'))->select($where,$group,$order,$limit,$fields);

}

}