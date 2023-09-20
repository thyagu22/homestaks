<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;

class Queue
{
    /**
     * Mapeamento de middlewares
     * @var array
     */
    private static  $map = [];

    /**
     * Mapeamento de middlewares usados em todas as rotas
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares a serem executados
     * @var array
     */
    private $middlewares = [];

    /**
     * Função de execução do controlador
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos da função do controlador
     * @var array
     */
    private $controllerArgs = [];

    /**
     *
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default,$middlewares);        ;
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;

    }

    /**
     * Define o mapeamento de Middlewares
     * @param array $map
     */
    public static function setMap($map)
    {
        self::$map = $map;
        
    }

    /**
     * Define o mapeamento de Middlewares padrões
     * @param array $default
     */
    public static function setDefault($default)
    {
        self::$default = $default;

    }

    /**
     * Executa o proximo nível de fila de Middleware
     * @param Request $request
     * @return Response
     */
    public function next($request)
    {
        //VERIFICA SE A FILA ESTÁ VAZIA
        if (empty($this->middlewares)) return call_user_func_array($this->controller,$this->controllerArgs);

        //MIDDLEWEARE
        $middleware = array_shift($this->middlewares);

        //VERIFICA O MAPEAMENTO
        if (!isset(self::$map[$middleware])){
            throw new \Exception("Problemas ao processar o middleware da requisição", 500);
        }

        //NEXT
        $queue = $this;
        $next = function($request) use($queue){
            return $queue->next($request);
        };

        return (new  self::$map[$middleware])->handle($request,$next);


    }


}



