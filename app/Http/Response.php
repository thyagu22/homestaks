<?php

namespace App\Http;

class Response
{
    /**
     * Código do status HTTP
     * @var int
     */
    private $httpCode = 200;

    /**
     * Cabeçalho do response
     * @var array
     */
    private $headers = [];

    /**
     * Tipo de conteúdo que está sendo retornado
     * @var string
     */
    private $contentType = 'text/html';

    /**
     * Conteúdo do response
     * @var
     */
    private $content;


    /**
     * Inicia a classe e define valores
     * @param $httpCode
     * @param $content
     * @param $contentType
     */
    public function __construct($httpCode, $content, $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);

    }

    /**
     * Altera o content type do response
     * @param $contentType
     * @return void
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    /**
     * Adiciona um registro no cabeçalho de response
     * @param $key
     * @param $value
     * @return void
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

    }

    /**
     * Envia os Headers para o navegador
     * @return void
     */
    private function senHeaders()
    {
        //STATUS
        http_response_code($this->httpCode);

        //ENVIAR HEADERS
        foreach($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    /**
     * Envia resposta para o usuário
     *
     */
    public function sendResponse()
    {
        //ENVIA OS HEADERS
        $this->senHeaders();

        //IMPRIME CONTEÚDO
        switch($this->contentType) {
            case 'text/html':
                echo $this->content;
                exit;
            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
        }
    }

}