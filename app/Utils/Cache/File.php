<?php

namespace App\Utils\Cache;

class File
{

    /**
     * Retorna o caminho ató o arquivo de cache
     * @param string $hash
     * @return string
     */
    private static function getFilePath($hash)
    {
        //DIRETORIO DE CACHE
        $dir = getenv('CACHE_DIR');

        //VERIFICA A EXISTENCIA DO DIRETÓRIO
        if(!file_exists($dir)){
            mkdir($dir,0755,true);
        }

        //RETORNA O CAMINHO ATÉ O ARQUIVO
        return  $dir.'/'.$hash;

    }

    /**
     * Guarda informações no cache
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($hash, $content)
    {
        //SERIALIZA O RETORNO
        $serialize = serialize($content);

        //OBTEM O CAMINHO ATÉ O ARQUIVO DE CACHE
        $cacheFile = self::getFilePath($hash);

        //GRAVA AS INFORMAÇÕES NO ARQUIVO
        return file_put_contents($cacheFile,$serialize);


    }

    /**
     * Retorna o conteudo gravado no cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    private static function getContentCache(string $hash, int $expiration): mixed
    {
        //OBTEM O CAMINHO DO ARQUIVO
        $cacheFile = self::getFilePath($hash);

        //VERIFICA A EXISTENCIA DO ARQUIVO
        if(!file_exists($cacheFile)){
            return false;
        }

        //VALIDA A EXPIRAÇÃO DO CACHE
        $createTime = filectime($cacheFile);
        $difTime = time() - $createTime;
        if($difTime > $expiration){
            return false;
        }

        //RETORNA O DADO REAL
        $serialize = file_get_contents($cacheFile);
        $options = [];
        return unserialize($serialize, $options);

    }

    /**
     * Obtem uma informação do cache
     * @param string $hash
     * @param integer $expiration
     * @param \Closure $function
     * @return mixed
     */
    public static function getCache($hash, $expiration, $function){
        //VERIFICA O CONTEÚDO GRAVADO
        if($content = self::getContentCache($hash,$expiration)){
            return $content;
        }

        //EXECUTA A FUNÇÃO
        $content = $function();

        //GRAVA O RETORNO DO CACHE
        self::storageCache($hash,$content);

        //RETORNA O CONTEUDO
        return $content;
    }



}