<?php

namespace App\Http;

use WilliamCosta\DatabaseManager\Database;

class Request
{
    /**
     *
     * @var
     */
    private $router;

    /**
     * Método HTTP da requisição
     * @var mixed|string
     */
    private $httpMethod;

    /**
     * URI da página
     * @var mixed|string
     */
    private $uri;

    /**
     * Parâmetos da URL ($_GET)
     * @var array
     */
    private $queryParams;

    /**
     * Variáveis recebidas no POST da página ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição
     * @var array|false
     */
    private $headers = [];


    /**
     * Construtor da classe
     */
    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }

    /**
     * Define as variáveis do POST
    */
    private function setPostVars(){
        //VERIFICA METODO DA REQUISIÇÃO
        if($this->httpMethod == 'GET') return false;

        //POST PADRÃO
        $this->postVars = $_POST ?? [];

        //POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) &&  empty($_POST)) ? json_decode($inputRaw,true) : $this->postVars;

    }


    /**
     * Define a URI
     */
    private function setUri()
    {
        //URI COMPLETA (COM GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';


        //REMOVE GETS DA URI
        $xURI = explode('?',$this->uri);
        $this->uri = $xURI[0];
    }

    /**
     * Retorna a instâcia de ROUTER
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Método responsãvel por retornar o métoto HTTP da requisição
     * @return mixed|string
     */
    public function getHttpMethod(): mixed
    {
        return $this->httpMethod;
    }

    /**
     * Método responsãvel por retornar a URI da requisição
     * @return mixed|string
     */
    public function getUri(): mixed
    {
        return $this->uri;
    }

    /**
     * Método responsãvel por retornar os parâmetros da URL da requisição
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Método responsãvel por retornar as variáveis POST da requisição
     * @return array
     */
    public function getPostVars(): array
    {
        return $this->postVars;
    }

    /**
     * Método responsãvel por retornar os headers da requisição
     * @return array|false
     */
    public function getHeaders(): bool|array
    {
        return $this->headers;
    }


}