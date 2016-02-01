<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('MY_base_REST_Controller.php');
class Event extends MY_base_REST_Controller {

    /** @var  Events_model */
    public $model;
    /** @var  Events_messages_model */
    public $em_model;
    /** @var  Device_tokens_model */
    public $dt_model;
    /** @var  CI_Email */
    public $email;


    function __construct()
    {
        parent::__construct();
        $this->load->model('events_model', 'model');
        $this->load->model('events_messages_model', 'em_model');
        $this->load->model('device_tokens_model', 'dt_model');
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
            $create_result['data'] = $this->model->get_event($create_result['data']['id']);
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
            $event = $this->model->get_by_id($event_id);
            if(!$event)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('event'))), 404);
            }
            $this->model->leave_event($event_id, $this->user_id);
            $this->send_notification('user_left', $event_id);
            $this->response(true, 200);
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
            $event = $this->model->get_by_id($event_id);
            if(!$event)
            {
                $this->response(sprintf(lang('not_found'), ucfirst(lang('event'))), 404);
            }
            if($this->model->join_event($event_id, $this->user_id))
            {
                $this->send_notification('user_join', $event_id);
                $this->response(true, 200);
            }
            else
            {
                $this->response('Error!', 500);
            }

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
            $coords = $this->model->gm->geoPlaceCoords($this->user['city_id']);
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
            $this->send_notification('new_message', $event_id);
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
                $coords = $this->model->gm->geoPlaceCoords($this->user['city_id']);
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

    protected function send_notification($type, $event_id)
    {
        $recipients = $this->dt_model->get_tokens_for_send($type, $event_id, $this->user_id);
        $emails = [];
        $tokens = [];
        foreach($recipients as $user)
        {
            if($user['push_allowed']) $tokens[] = $user['token'];
            if($user['email_allowed']) $emails[] = $user['email'];
        }
        if($tokens)
        {
           $this->send_push($type, $tokens, $event_id);
        }
        if($emails)
        {
            $this->send_email(implode(', ', $emails), lang("{$type}_subject"), sprintf(lang("{$type}_email"), $this->user['username'], $event_id));
        }


        return true;
    }

    protected function send_email($to, $subject, $text)
    {
        $this->load->library('email');
        $this->email->from(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $this->email->bcc($to);
        $this->email->bcc_batch_mode = true;
        $this->email->subject($subject);
        $this->email->message($text);
        $this->email->batch_bcc_send();
        return $this->email->send();
    }

    protected function send_push($type, $tokens, $event_id)
    {
        $this->load->library('apn');
        $this->apn->payloadMethod = 'enhance'; // включите этот метод для отладки
        $this->apn->connectToPush();

        // добавление собственных переменных в notification
        $this->apn->setData(['event_id' => $event_id]);

        foreach($tokens as $token)
        {
            $send_result = $this->apn->sendMessage($token, sprintf(lang("{$type}_push"), $this->user['username'], $event_id), /*badge*/ 1, /*sound*/ 'default'  );
            if($send_result)
                log_message('error','Отправлено успешно');
            else
                log_message('error',$this->apn->error);
        }
        $this->apn->disconnectPush();
    }









}
