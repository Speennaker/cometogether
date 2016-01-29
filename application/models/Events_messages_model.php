<?php
require_once('MY_base_model.php');
class Events_messages_model extends MY_base_model {

    /** @var  Users_model */
    protected $users_model;
    public $entity_name = 'event_message';

    public function __construct()
    {
        parent::__construct('events_messages');
        $this->_ci->load->model('events_to_users_model', 'etu_model');
        $this->etu_model = $this->_ci->etu_model;
        $this->_ci->load->model('users_model', 'users_model');
        $this->users_model = $this->_ci->users_model;
    }
    
    public function get_event_messages($event_id)
    {
        $this->db->select('em.id, em.created, em.message, u.id as author_id, u.username as author_username, u.birthday as author_birthday, u.gender as author_gender');
        $this->db->join($this->users_model->table.' u', 'em.users_id = u.id');
        $this->db->where('em.events_id', $event_id);
        $messages =  $this->db->get($this->table.' em')->result_array();
        if(!$messages) return [];
        foreach($messages as &$row)
        {
            $row['author_avatar'] = $this->users_model->get_photo($row['author_id'], $this->users_model->avatar);
        }
        return $messages;
    }


}