<?php
require_once('MY_base_model.php');
class Events_to_users_model extends MY_base_model {

    public $entity_name = 'event_to_user';
    /** @var  Users_model */
    protected $users_model;

    public function __construct()
    {
        parent::__construct('events_to_users');
        $this->_ci->load->model('users_model', 'users_model');
        $this->users_model = $this->_ci->users_model;
    }



    public function get_event_joined($event_id, $count = false)
    {
        $this->db->select('etu.created as join_date, u.id, u.username, u.birthday, u.gender');
        $this->db->join($this->users_model->table.' u', 'etu.users_id = u.id');
        $this->db->where('etu.events_id', $event_id);
        if($count) return $this->db->count_all_results($this->table.' etu');
        $users =  $this->db->get($this->table.' etu')->result_array();
        if(!$users) return [];
        foreach($users as &$row)
        {
            $row['avatar'] = $this->users_model->get_photo($row['id'], $this->users_model->avatar);
        }

        return $users;

    }

}