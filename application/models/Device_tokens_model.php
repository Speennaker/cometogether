<?php
require_once('MY_base_model.php');
class Device_tokens_model extends MY_base_model {

    public $entity_name = 'device_token';

    public function __construct()
    {
        parent::__construct('device_tokens');
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



}