<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once($_SERVER['DOCUMENT_ROOT'].'/application/libraries/REST_Controller.php');
abstract class MY_base_REST_Controller extends REST_Controller {

    /** @var  Users_model */
    public $users_model;
    public $user_id;
    public $language = 'russian';
    public $language_header = 'language';
    public $languages = [
        'ru' => 'russian',
        'en' => 'english',
    ];

    function __construct()
    {
        parent::__construct();
        $this->load->model('users_model', 'users_model');
        $headers = $this->input->request_headers();
        if(array_key_exists($this->language_header, $headers) && array_key_exists($headers[$this->language_header], $this->languages))
        {
            $this->language = $this->languages[$headers[$this->language_header]];
        }
        $this->lang->load("main",$this->language);

    }


    protected function auth($api_only = false)
    {
        $headers = $this->input->request_headers();
        $api_key = array_key_exists('API_KEY', $headers) ? $headers['API_KEY'] : '';
        if($api_key != API_KEY){
            $this->response('Forbidden', 403);
        }
        if(!$api_only)
        {
            $token = array_key_exists('TOKEN',$headers) ? $headers['TOKEN'] : '';
            $user = $this->users_model->check_token($token);
            if(!$user){
                $this->response('Authorization Error', 401);
            }
            $this->user_id = $user['id'];
        }
    }




}
