<?php
require_once('MY_base_model.php');
class User_settings_model extends MY_base_model {

    public $entity_name = 'user_settings';
    /** @var  Users_model */
    protected $users_model;

    public function __construct()
    {
        parent::__construct('user_settings');
        $this->_ci->load->model('users_model', 'users_model');
        $this->users_model = $this->_ci->users_model;
        $this->boolean_fields = $this->table_fields;
    }

    public function get_user_settings($user_id)
    {
        $res = $this->db->get_where($this->table, ['users_id' => $user_id])->row_array();
        unset($res['users_id']);
        unset($res['id']);
        return  $this->process_row($res);
    }

    public function update_settings($user_id, array $data)
    {
        $data = $this->check_fields($data);
        unset($data['users_id']);
        if(!$data) return false;
        return $this->db->update($this->table, $data, ['users_id' => $user_id]);
    }

}