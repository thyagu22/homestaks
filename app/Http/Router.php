<?php

namespace App\Http;

use App\Utils\utilidades;
use \Closure;
use \Exception;
use \ReflectionFunction;
use App\Http\Middleware\Queue as MiddlewareQueue;

/**
 *
 */
class Router
{
    /**URL completa do projeto (RAIZ)
     * @var string
     */
    private $url = '';

    /**
     * Prefixo de todas as rotas
     * @var string
     */
    private $prefix = '';

    /**
     * Indice de rotas
     * @var array
     */
    private $routes;

    /**
     * Instancia de Request
     * @var Request
     */
    private $request;

    /**
     * Content type padrão do response
     * @var Request
     */
    private $contentType = 'text/html';

    /**
     * Inicia a classe
     * @param $url
     */
    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * @param $contentType
     * @return void
     */
    public function setContentType($contentType){
        $this->contentType = $contentType;
    }



    /**
     * Define prefixo das rotas
     * @return void
     */
    private function setPrefix()
    {
        //INFO DA URL ATUAL
        $parsUrl = parse_url($this->url);

        //DEFINE O PREFIXO
        $this->prefix = $parsUrl['path'] ?? '';
    }

    /**
     * Adiciona uma rota na classe
     * @param string $method
     * @param string $route
     * @param array $params
     */
    private function addRoute($method, $route, $params = [])
    {
        //VALIDAÇÃO DOS PARAMETROS
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        //MIDDLEWARES DA ROTA
        $params['middlewares'] = $params['middlewares'] ?? [];

        //VARIÁVEIS DA ROTA
        $params['variables'] = [];
        $patternVariable = '/{(.*?)}/';


        //PADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        //REMOVE BARRA NO FINAL DA ROTA
        $route = rtrim($route,'/');

        //PADRÃO DE VALIDAÇÃO URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;

    }

    /**
     * Retorna URI sem prefixo
     * @return string
     */
    public function getUri()
    {
        //URI DA REQUEST
        $uri = $this->request->getUri();

        //FATIA URI COM PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //RETORNA URI SEM PREFIXO
        return rtrim(end($xUri),'/');
    }

    /**
     * Retorna dados da rota atual
     * @return array
     */
    private function getRoute()
    {
        //URI
        $uri = $this->getUri();

        //METHOD
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach ($this->routes as $patternRoute => $methods) {
            //VERIFICA SE A URI ESTÁ NO PADRÃO
            if (preg_match($patternRoute, $uri,$matches)) {
                //VERIFICA O MÉTODO
                if (isset($methods[$httpMethod])) {
                    //REMOVE INDICE 0
                    unset($matches[0]);

                    //VARIÁVEIS PROCESSADAS
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys,$matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    //RETORNO DOS PARAMETROS DA ROTA
                    return $methods[$httpMethod];
                }
                //MÉTODO NÃO PERMITIDO/DEFINIDO
                throw new Exception("Método não permitido", 405);
            }
        }
        //URL NÃO ENCONTRADA
        throw new Exception("URL não encontrada", 404);
    }

    /**
     * Executa a rota atual
     * @return Response
     */
    public function run(): Response
    {
        try {
            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();

            //VERIFICA O CONTROLADOR
            if (!isset($route['controller'])) {
                throw new Exception("A URL não pode ser processada", 500);
            }

            //ARGUMENTOS DA FUNÇÃO
            $args = [];

            //REFLECTION
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';

            }

            //RETORNA A EXECUÇÃO DA FILA DE MIDDLEWARES
            return (new MiddlewareQueue($route['middlewares'], $route['controller'],$args))->next($this->request);
        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()),$this->contentType);
        }

    }

    /**
     * Retorna mensagem de erro conforme content type
     * @param string $message
     * @return mixed
     */
    private function getErrorMessage($message){
        switch($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];
                break;

            default:
                return $message;
                break;
        }
    }

    /**
     * Retorna a URL atual
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->url.$this->getUri();

    }

    /**
     * Redireciona
     * @return string
     */
    public function redirect($route)
    {
        //URL
        $url = $this->url.$route;

        //EXECUTA O REDIRECT
        header('location: '.$url);
        exit;

    }

    /**
     * Define rota de GET
     * @param string $route
     * @param array $params
     */
    public function get($route, $params = [])
    {
        return $this->addRoute('GET', $route, $params);

    }

    /**
     * Define rota de POST
     * @param string $route
     * @param array $params
     */
    public function post($route, $params = [])
    {
        return $this->addRoute('POST', $route, $params);

    }

    /**
     * Define rota de PUT
     * @param string $route
     * @param array $params
     */
    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);

    }

    /**
     * Define rota de DELETE
     * @param string $route
     * @param array $params
     */
    public function delete($route, $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);

    }

}