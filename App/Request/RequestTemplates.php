php

namespace App\Controller\Request;

use App\Http\RemoteRequestHandler;
use App\Model\InserterDB;
use App\Utils\WooCommerce\User;

class RequestTemplates
{
    /**
     * Url para requisitar templates
     *
     * @var String
     */
    private static $url = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/files';

    /**
     * Url para requisitar categorias doz templates
     *
     * @var String
     */
    private static $url_category = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/templates/v1/categories';

    public static function init()
    {
        Self::insert_db_categories();
        Self::insert_db_templates();
    }

    /**
     * Método responsável por requisitar templates
     *
     * @return Object
     */
    public static function request_templates()
    {
        $body = array(
            'access_url' => get_site_url(),
            'data' => User::get_user_simple_data(),
        );
        $request = RemoteRequestHandler::makeRequest(Self::$url, 'POST', $body, true);

        return $request;
    }

    /**
     * Método responsável por requisitar categorias de templates
     *
     * @return Object
     */
    public static function request_templates_categories()
    {
        $body = array(
            'access_url' => get_site_url(),
            'data' => User::get_user_simple_data(),
        );
        $request = RemoteRequestHandler::makeRequest(Self::$url_category, 'POST', $body, true);

        return $request;
    }

    /**
     * Método responsável por inserir as categorias dos templates no db
     * @return void
     */
    public static function insert_db_categories()
    {
        $categories = Self::request_templates_categories();
        $inserter = new InserterDB();

        foreach ($categories as $category) {
            $data = [
                'id' => $category->id,
                'name' => $category->name,
                'parent_id' => $category->parent_id
            ];
            $inserter->insert_templates_categories($data);
        }
    }

    /**
     * Método responsável por inserir os templates no db
     *
     * @return void
     */
    public static function insert_db_templates()
    {
        $templates = Self::request_templates();
        $inserter = new InserterDB();

        foreach ($templates as $template) {
            $data = [
                'id' => $template->id,
                'category_id' => $template->category_id,
                'img' => $template->img,
                'filename' => $template->filename,
            ];
            $inserter->insert_templates_files($data);
        }
    }

    /**
     * Método responsável por remover caracteres especiais do slug
     *
     * @param String $text
     * @return String
     */
    public static function slugify($text)
    {
        // Remove caracteres especiais
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Converte para minúsculas
        $text = strtolower($text);

        // Remove caracteres indesejados
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Remove hífens duplicados
        $text = trim($text, '-');

        // Substitui espaços por hífens
        $text = str_replace(' ', '-', $text);

        return $text;
    }
}
