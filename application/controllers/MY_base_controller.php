<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class MY_base_controller extends CI_Controller {

    /** @var  CI_Loader */
    public $load;
    protected $menu_item;
    public $bulk_actions = [];

    function __construct($menu_item)
    {
        parent::__construct();
        $this->menu_item = $menu_item;
    }

    public function render_view($view, $breadcrumbs, $data, $folder = 'admin', $js = [], $css = [], $return = false)
    {
        // Загружаем хэдер
        $page['footer'] = $this->load->view('_blocks/header', [
            'page_title' => lang('main_title'), // Заголовок страницы
            'custom_js' => $js, // Кастомные JS
            'custom_css' => $css, // Кастомные стили
            'menus' => $this->get_menus(), // Элементы бокового меню
            'menu_item' => $this->menu_item, // Текущий элемент меню
            'breadcrumbs' => $breadcrumbs

        ], $return);

        // Загружаем шаблон страницы
        $page['body'] = $this->load->view($folder.'/'.$view, $data, $return);

        // Загружаем футер
        $page['footer'] = $this->load->view('_blocks/footer', [], $return);

        if($return) return implode('', $page);
    }

    private function get_menus()
    {
        $menus = [
            [
                'admin' => [
                    'url' => '',
                    'title' => lang('dashboard_title')
                ],
                'categories' => [
                    'url' => 'categories',
                    'title' => lang('categories_title')
                ],
                'rules' => [
                    'url' => 'rules',
                    'title' => lang('rules_title')
                ],
            ]
        ];
        return $menus;
    }

    public function ajax($function)
    {
        if(!$this->input->is_ajax_request() || !function_exists($this->$function()))
        {
            show_404();
            return false;
        }
        $this->$function();
        return true;
    }

    protected function delete()
    {
        echo "5555";
    }

    public function images_upload()
    {
        // Define a destination
        $targetFolder = '/assets/uploads'; // Relative to the root

//        $verifyToken = md5('unique_salt' . $_POST['timestamp']);

        if (!empty($_FILES)) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
            $targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];

            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
            $fileParts = pathinfo($_FILES['Filedata']['name']);

            if (in_array($fileParts['extension'],$fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);
                echo '1';
            } else {
                echo 'Invalid file type.';
            }
        }
    }

}
