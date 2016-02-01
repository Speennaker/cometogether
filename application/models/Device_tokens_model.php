<?php
require_once('MY_base_model.php');
class Device_tokens_model extends MY_base_model {

    public $entity_name = 'device_token';
    /** @var  User_settings_model */
    public $user_settings_model;
    /** @var  Events_to_users_model */
    public $etu_model;
    /** @var  Users_model */
    public $users_model;


    public function __construct()
    {
        parent::__construct('device_tokens');
        $this->_ci->load->model('user_settings_model', 'user_settings_model');
        $this->user_settings_model = $this->_ci->user_settings_model;
        $this->_ci->load->model('events_to_users_model', 'etu_model');
        $this->etu_model = $this->_ci->etu_model;
        $this->_ci->load->model('users_model', 'users_model');
        $this->users_model = $this->_ci->users_model;
    }



    public function add_token($user_id, $token)
    {
        $ar = ['users_id' => $user_id, 'token' => $token];
        $check = $this->db->get_where($this->table, $ar)->row_array();
        if($check) return false;
        $this->add($ar);
        return true;
    }


    public function delete_token($user_id, $token)
    {
        $ar = ['users_id' => $user_id, 'token' => $token];
        return $this->db->delete($this->table, $ar);
    }

    public function delete_user_tokens($user_id)
    {
        $ar = ['users_id' => $user_id];
        $this->db->delete($this->table, $ar);
    }

    public function get_user_tokens($user_id)
    {
        $ar = ['users_id' => $user_id];
        $this->db->select('token');
        $res =  $this->db->get_where($this->table, $ar)->result_array();
        return array_column($res, 'token');

    }

    public function get_tokens_for_send($type, $event_id, $user_id)
    {
        $this->db->select("u.email as email, dt.token as token, etu.events_id, us.{$type}_push as push_allowed, us.{$type}_email as email_allowed");
        $this->db->join($this->users_model->table.' u', 'u.id = dt.users_id');
        $this->db->join($this->etu_model->table.' etu', 'etu.users_id = dt.users_id');
        $this->db->join($this->user_settings_model->table.' us', 'us.users_id = dt.users_id');
        $this->db->from($this->table.' dt');
        $this->db->where("etu.events_id", $event_id);
        $this->db->where("(us.{$type}_push = 1 OR us.{$type}_email = 1)");
        $this->db->where("u.id != {$user_id}");
        return $this->db->get()->result_array();
    }



}