<?php

namespace App\Utils;

use Exception;
use stdClass;
use WP_Error;

class View
{

    /**
     * Variáveis padrões
     *
     * @var array
     */
    private static $vars = [];

    /**
     * Método responsável por definir osa dados iniciais da classe 
     *
     */
    public static function init($vars = [])
    {
        Self::$vars = $vars;
    }

    /**
     * Método responsável por retornar o conteúdo de uma view
     *
     * @param string $view
     * @return void
     */
    private static function getContentView($view)
    {
        $extensions = array('php', 'html');
        foreach ($extensions as $extension) {
            $file = MASTER_LND_MASTER_DEV . 'resources/view/' . $view . '.' . $extension;
            if (file_exists($file)) {
                return file_get_contents($file);
                
            }
        }
        throw new Exception("Arquivo de view não encontrado para '$view'");
        
    }

    /**
     * Método responsável por retornar o conteúdo renderizado de uma view
     *
     * @param string $view
     * @param array $vars (string/numeric)
     * @return string
     */
    public static function render($view, $vars = [])
    {
        $contentView = (string) Self::getContentView($view);

        // Função recursiva para converter objetos stdClass em arrays associativos
        $convertObjectToArray = function ($vars) use (&$convertObjectToArray) {
            // Se o parâmetro não for um array, converte para array
            if (!is_array($vars)) {
                $vars = (array) $vars;
            }
            foreach ($vars as $key => $value) {
                // Se o valor for um objeto stdClass, converte para array
                if (is_object($value) && $value instanceof stdClass) {
                    $vars[$key] = $convertObjectToArray((array) $value);
                }
            }
            return $vars;
        };

        // Converte objetos stdClass em arrays associativos recursivamente
        $vars = $convertObjectToArray($vars);

        // Função recursiva para substituir as chaves no conteúdo da view
        $replaceVariables = function ($content, $vars) use (&$replaceVariables) {
            foreach ($vars as $key => $value) {
                // Se o valor for um array, chama a função recursivamente
                if (is_array($value)) {
                    $content = $replaceVariables($content, $value);
                } else {

                    // Se não, substitui a chave pelo valor
                    $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
                }
            }
            return $content;
        };

        // Substitui as chaves no conteúdo da view
        $contentView = $replaceVariables($contentView, $vars);
        return $contentView;
    }
}
