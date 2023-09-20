<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Fechamento
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
     * @var int
     */
    public $qtdfs;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdgl;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdls;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdaq;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdlq;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdlc;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdgr;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdsl;

    /**
     * Texto do feedback
     * @var int
     */
    public $qtdll;


    /**
     * Texto do feedback
     * @var int
     */
    public $totalfc;

    /**
     * Data da publicação do feedback
     * @var string
     */
    public $data;

    /**
     * Cadastra instância atual no DB
     * @return boolean
     */
    public function cadastrar()
    {
        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        $this->id = (new Database('fechamento'))->insert([
            'cdfilho' => $this->cdfilho,
            'qtdfs' => $this->qtdfs,
            'qtdgl' => $this->qtdgl,
            'qtdls' => $this->qtdls,
            'qtdaq' => $this->qtdaq,
            'qtdll' => $this->qtdlq,
            'qtdsl' => $this->qtdsl,
            'qtdlc' => $this->qtdlc,
            'qtdgr' => $this->qtdgr,
            'totalfc' => $this->totalfc,
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
        return (new Database('fechamento'))->delete('id = '.$this->id);
    }

    /**
     * Retorna um feedback com base no ID
     * @param string $id
     * @param string $mes
     * @param string $ano
     * @param string $tipo
     * @return \WilliamCosta\DatabaseManager\PDOStatement
     */
    public static function getFechamentoById($id,$mes,$ano,$tipo)
    {
        $group = '';
        $fildes = '';
        $tipoSelect = $tipo;

        switch($tipoSelect){

            case 'mensal' :

            $group = 'cdfilho';
            $fields = 'r.cdfilho, count(*) as qtd, sum(t.valortarefa) as totalm';
            break;

            case 'tarefa' :

                $group = 'cdtarefa';
                $fields = 'r.cdtarefa, count(*) as qtd, sum(t.valortarefa) as totalt';
                break;
        }

        $where = 'r.cdtarefa = t.cdtarefa and r.cdfilho = '.$id.' and Month(dt_tarefa) = '.$mes.' and Year(dt_tarefa) = '.$ano;

        return (new Database('tarefas r, tipotarefa t'))->select($where, $group,null,null,$fields );
    }

    /**
     * @param string|null $where
     * @param string|null $group
     * @param string|null $order
     * @param string|null $limit
     * @param string $fields
     * @return \WilliamCosta\DatabaseManager\PDOStatement
     */
    public static function getFechamento($where = null, $group = null , $order = null, $limit = null, $fields = '*')
    {
        return (new Database('fechamento'))->select($where,$group,$order,$limit,$fields);

    }

    /**
     * Retorna um feedback com base no ID
     * @param integer $id
     * @return Fechamento\PDOStatement
     */
    public static function getFechamentoMes($id,$mes,$ano)
    {
        return self::getFechamento('cdfilho = '.$id.' and Month(data) = '.$mes.' and Year(data) = '.$ano, null,null,null, 'count(*) as qtd')->fetchObject(self::class);

    }

    /**
     * @param string $id
     * @param string $mes
     * @param string $ano
     * @return \WilliamCosta\DatabaseManager\PDOStatement
     */
    public static function getTarefas($id, $mes, $ano)
    {
        return (new Database('tarefas'))->select('cdfilho = '.$id.' and Month(dt_tarefa) = '.$mes.' and Year(dt_tarefa) = '.$ano, 'cdfilho,cdtarefa',null,null, 'cdfilho, cdtarefa, count(*) as qtd');

    }

    /**
     * @param string $id
     * @param string $ano
     * @return \WilliamCosta\DatabaseManager\PDOStatement
     */
    public static function getFechamentos($id,$ano)
    {
        $where = '';

        switch($id){
            case 0 :
                $where = 'Year(data) = '.$ano;
                break;
            case 2:
            case 1 :
                $where = 'cdfilho = '.$id.' and Year(data) = '.$ano;
                break;

        }
        return (new Database('fechamento'))->select($where, null,'data desc',null, 'id,cdfilho, totalfc,data');

    }

}