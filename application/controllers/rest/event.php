<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('MY_base_REST_Controller.php');
class Event extends MY_base_REST_Controller {

    /** @var  Events_model */
    public $model;

    /** @var  Events_messages_model */
    public $em_model;


    function __construct()
    {
        parent::__construct();
        $this->load->model('events_model', 'model');
        $this->load->model('events_messages_model', 'em_model');
        $this->auth();
    }

    public function create_post()
    {
        try
        {
            $data = $this->post();
            $data['users_id'] = $this->user_id;
            $create_result = $this->model->create_event($data);
            if($create_result['errors'])
            {
                $this->response($create_result, 400);
            }
            $create_result['data'] = $this->model->get_by_id($create_result['data']['id']);
            $this->response($create_result, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }


    public function leave_get($event_id)
    {

        try
        {
            $profile = $this->users_model->get_profile($this->user_id);
            if(!$profile)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('user'))), 404);
            }
            $event = $this->model->get_by_id($event_id);
            if(!$event)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('event'))), 404);
            }
            $this->response($this->model->leave_event($event_id, $this->user_id), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function join_get($event_id)
    {

        try
        {
            $profile = $this->users_model->get_profile($this->user_id);
            if(!$profile)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('user'))), 404);
            }
            $event = $this->model->get_by_id($event_id);
            if(!$event)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('event'))), 404);
            }
            $this->response($this->model->join_event($event_id, $this->user_id), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function nearest_by_user_get()
    {

        try
        {
            $profile = $this->users_model->get_extended_profile($this->user_id);
            if(!$profile)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('user'))), 404);
            }
            $coords = $this->model->gm->geoPlaceCoords($profile['city_id']);
            $this->response($this->model->get_nearest($coords->lat, $coords->lng), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function nearest_by_coords_post()
    {

        try
        {
            if(!$this->post('lat') || !$this->post('lng') || !$this->post('radius') ) $this->response('Missing Parameters', 400);
            $this->response($this->model->get_nearest($this->post('lat'), $this->post('lng'), $this->post('radius')), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function details_get($event_id)
    {
        try
        {
            $this->response($this->model->get_detailed_event($event_id), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }

    public function message_post($event_id)
    {
        try
        {
            if(!$this->post('message')) $this->response('Missing Parameters', 400);
            $data = [
                'message' => $this->post('message'),
                'events_id' => $event_id,
                'users_id' => $this->user_id,
                'created'  => date('Y-m-d H:i:s')
            ];
            $data['id'] = $this->em_model->add($data);
            $this->response($data, 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }


    public function search_post()
    {
        try
        {
            $params = $this->post();
            if(
                !array_key_exists('per_page', $params) ||
                !array_key_exists('page', $params) ||
                !$params['page'] || !$params['page']
            )
            {
                $this->response('Missing Parameters', 400);
            }
            if(
                array_key_exists('lat', $params) &&
                array_key_exists('lng', $params) &&
                $params['lat'] && $params['lng']
            )
            {
                $lat = $params['lat'];
                $lng = $params['lng'];
                $radius = (array_key_exists('radius', $params) && $params['radius']) ? $params['radius'] : 5000;
            }
            else
            {
                $profile = $this->users_model->get_extended_profile($this->user_id);
                $coords = $this->model->gm->geoPlaceCoords($profile['city_id']);
                $lat = $coords->lat;
                $lng = $coords->lng;
                $radius = (array_key_exists('radius', $params) && $params['radius']) ? $params['radius'] : 50000;
            }
            $this->response($this->model->search($params['per_page'], $params['page'], $lat, $lng, $radius, $params), 200);
        }
        catch(Exception $e)
        {
            $this->response($e->getMessage(), $e->getCode());
        }
    }









}
