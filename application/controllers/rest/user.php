<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('MY_base_REST_Controller.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/application/libraries/GoogleMap.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
use bart\GooglePlaces\GooglePlaces;
class User extends MY_base_REST_Controller {

    /** @var  Users_model */
    public $model;
    /** @var  Device_tokens_model */
    public $device_tokens_model;
    /** @var  User_settings_model */
    public $user_settings_model;


    function __construct()
    {
        parent::__construct();
        $this->model = $this->users_model;
        $this->load->model('device_tokens_model');
        $this->load->model('user_settings_model', 'user_settings_model');
    }



    public function login_post()
    {
        $this->auth(true);
        try
        {
            $profile = $this->model->login($this->post('login'), $this->post('password'));
            if(!$profile)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('user'))), 404);
            }
            if($this->post('device_token')) $this->device_tokens_model->add_token($profile['id'], $this->post('device_token'));
            $this->response($profile, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function profile_post()
    {
        $this->auth(true);
        try
        {
            if(!$this->post()) throw new Exception('Empty Request', 400);
            $data = $this->post();
            unset($data['device_token']);
            $device_token = $this->post('device_token');
            $reg_result = $this->model->register($data);
            if($reg_result['errors'])
            {
                $this->response($reg_result, 400);
            }
            $reg_result['profile'] = $this->model->get_extended_profile($reg_result['profile']['id']);
            if($device_token) $this->device_tokens_model->add_token($reg_result['profile']['id'], $device_token);
            $this->response($reg_result, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function profile_put()
    {
        $this->auth();
        try
        {
            if(!$this->put()) throw new Exception('Empty Request', 400);
            $profile = $this->model->get_profile($this->user_id);
            $update_result = $this->model->update_profile($this->user_id, $this->put());
            if($update_result['errors'])
            {
                $this->response($update_result, 400);
            }
            $update_result['profile'] = array_merge($profile, $this->put());
            unset($update_result['profile']['password']);
            unset($update_result['profile']['pass2']);
            $this->response($update_result, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function profile_get()
    {
        $this->auth();
        try
        {
            $profile = $this->model->get_profile($this->user_id);
            if(!$profile)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('user'))), 404);
            }
            $this->response($profile, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function avatar_post()
    {
        $this->auth();
        try
        {
            $this->response($this->model->photo_upload($this->user_id, $this->model->avatar), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function avatar_delete()
    {
        $this->auth();
        try
        {
            if($this->model->delete_photo($this->user_id, $this->model->avatar))
                $this->response(true, 200);
            else
                $this->response(sprintf(lang('not_found'), ucfirst(lang('avatar'))), 404);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function avatar_get()
    {
        $this->auth();
        try
        {
            $avatar = $this->model->get_photo($this->user_id, $this->model->avatar);
            $this->response($avatar, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function status_get($status)
    {
        $this->auth();
        try
        {
            $this->model->update($this->user_id, ['active' => $status]);
            $this->response(true, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function token_post()
    {
        $this->auth();
        try
        {
            if(!$this->post('device_token'))
            {
                throw new Exception('Missing Parameters', 400);
            }

            $this->response($this->device_tokens_model->add_token($this->user_id, $this->post('device_token')), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function settings_put()
    {
        $this->auth();
        try
        {
            if(!$this->put()) throw new Exception('Empty Request', 400);
            $this->user_settings_model->update_settings($this->user_id, $this->put());
            $this->response($this->user_settings_model->get_user_settings($this->user_id), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }

    public function settings_get()
    {
        $this->auth();
        try
        {
            $this->response($this->user_settings_model->get_user_settings($this->user_id), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }

    }
}
