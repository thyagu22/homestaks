<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Tarefas
{
    /**
     * ID do usuário que deu feedback
     * @var int
     */
    public $id;

    /**
     * Nome do usuário que deu feedback
     * @var int
     */
    public $cdfilho;

    /**
     * Texto do feedback
     * @var string
     */
    public $cdtarefa;

    /**
     * Data da publicação do feedback
     * @var string
     */
    public $dt_tarefa;

    /**
     * Cadastra instância atual no DB
     * @return boolean
     */
    public function cadastrar()
    {
        //DEFINE A DATA
        $this->dt_tarefa = date('Y-m-d H:i:s');

        $this->id = (new Database('tarefas'))->insert([
            'cdtarefa' => $this->cdtarefa,
            'cdfilho' => $this->cdfilho,
            'dt_tarefa' => $this->dt_tarefa
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
        //ATUALIZA FEEDBACK NO DB
        return (new Database('tarefas'))->update('id = '.$this->id,[
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    /**
     * Exclui feedback no DB com os dados da instancia atual
     * @return string
     */
    public function deletar()
    {
        //EXCLUI FEEDBACK NO DB
        return (new Database('tarefas'))->delete('id = '.$this->id);
    }

    /**
     * Retorna um feedback com base no ID
     * @param integer $id
     * @return Feedback
     */
    public static function getTarefasById($id)
    {
        return self::getTarefas('id = '.$id)->fetchObject(self::class);
        
    }

    /**
     * @param string|null $where
     * @param string|null $group
     * @param string|null $order
     * @param string|null $limit
     * @param string $fields
     * @return \WilliamCosta\DatabaseManager\PDOStatement
     */
    public static function getTarefas($where = null, $group = null , $order = null, $limit = null, $fields = '*')
    {
        return (new Database('tarefas'))->select($where,$group,$order,$limit,$fields);

    }

}