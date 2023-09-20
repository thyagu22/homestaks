<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;
use WilliamCosta\DatabaseManager\PDOStatement;
use App\Http\Request;

class Chart
{
    /**
     * ID do usuário que deu feedback
     * @var int
     */
    public $total;

    /**
     * Nome do usuário que deu feedback
     * @var string
     */
    public $edicao;

    /**
     * Texto do feedback
     * @var string
     */
    public $mensagem;

    /**
     * Data da publicação do feedback
     * @var string
     */
    public $data;

    /**
     * Data da publicação do feedback
     * @var int
     */
    public $idFilter;

    /**
     * @param int $idFilter
     *
     */
    public function setIdFilter(int $idFilter)
    {
        $this->idFilter = $idFilter;

    }

    /**
     * @return integer
     *
     */
    public function getIdFilter()
    {
       return $this->idFilter;

    }

    /**
     * Cadastra instância atual no DB
     * @return boolean
     */
    public function cadastrar()
    {
        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        $this->id = (new Database('feedbacks'))->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
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
        return (new Database('feedbacks'))->update('id = ' . $this->id, [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    /**
     * Exclui feedback no DB com os dados da instancia atual
     *
     * @return string
     */
    public function deletar()
    {
        //EXCLUI FEEDBACK NO DB
        return (new Database('feedbacks'))->delete('id = ' . $this->id);
    }

    /**
     * Retorna um feedback com base no ID
     * @param integer $id
     * @return Feedback
     */
    public static function getFeedbackById($id)
    {

        return self::getFeedbacks('id = ' . $id)->fetchObject(self::class);

    }

    /**
     * @param string|null $where
     * @param string|null $group
     * @param string|null $order
     * @param string|null $limit
     * @param string $idFilter
     * @param string $fields
     * @return PDOStatement
     */
    public static function getCharts($idFilter,$where = null, $group = null, $order = null, $limit = null, $fields = '*')
    {


        switch($idFilter) {
            case '1':
                return (new Database('resultado r, edicoes e'))->select("r.cdedicao = e.cdedicao and r.cdstatuso = 'A' and r.cdstatus = 'C'", 'GROUP BY e.nmedicao, e.dataedc', 'e.nmedicao, e.dataedc', $limit, 'COUNT(*) AS total, e.nmedicao AS edicao');

            case '2':
                return (new Database('resultadod r, edicoes e'))->select("r.cdedicao = e.cdedicao and r.cdstatuso = 'A' and r.cdstatus = 'C'", 'GROUP BY e.nmedicao, e.dataedc', 'e.nmedicao, e.dataedc', $limit, 'COUNT(*) AS total, e.nmedicao AS edicao');

            case '3':

            case '4':

            case '5':

            case '6':

            case '7':
            echo "<pre>";
            print_r('chegou aki');
            echo "<pre>";
            exit;

            case '8':

        }

        return (new Database('feedbacks'))->select($where, $order, $limit, $fields);

    }

}