<?php
require_once('MY_base_model.php');
class Events_model extends MY_base_model {

    public $entity_name = 'event';
    /** @var  Events_to_users_model */
    protected $etu_model;
    /** @var  Users_model */
    protected $users_model;
    /** @var  Events_messages_model */
    protected $em_model;
    /** @var GoogleMapAPI  */
    public $gm;
    protected $fields = [

        'gender' => [
            'required' => false,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
            'presets' => ['male', 'female']
        ],
        'date' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
        ],
        'place_id' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
        ],
        'place_type' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
            'presets' => ['movie_theater', 'art_gallery', 'museum', 'night_club', 'bar', 'restaurant', 'park', 'amusement_park', 'stadium', ]
        ],
        'time' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
            'presets' => ['morning', 'noon', 'evening']
        ],
        'comment' => [
            'required' => false,
            'max_length' => 255,
            'min_length' => 3,
            'unique' => false,
        ],
        'show_name' => [
            'required' => false,
            'max_length' => 255,
            'min_length' => 3,
            'unique' => false,
        ],
        'people' => [
            'required' => false,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
        ],
        'users_id' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
        ],
    ];

    public function __construct()
    {
        parent::__construct('events');
        $this->_ci->load->model('events_to_users_model', 'etu_model');
        $this->etu_model = $this->_ci->etu_model;
        $this->_ci->load->model('users_model', 'users_model');
        $this->users_model = $this->_ci->users_model;
        $this->_ci->load->model('events_messages_model', 'em_model');
        $this->em_model = $this->_ci->em_model;
        $this->gm = new GoogleMapAPI(array_flip($this->_ci->languages)[$this->_ci->language]);
    }


    public function create_event($data)
    {
        $errors = $this->validate($data);
        if($errors)
        {
            return ['errors' => $errors, 'data' => $data];
        }
        $info = $this->gm->geoPlaceInfo($data['place_id']);
        if(!$info)
        {
            $errors['place_id'] = sprintf(lang('invalid_field'),ucfirst(lang('place_id')));
            return ['errors' => $errors, 'data' => $data];
        }
        else
        {
            $data['place_info'] = json_encode([
                'name' => $info->result->name,
                'address' => $info->result->formatted_address,
                'icon' => $info->result->icon,
                'phone' => $info->result->international_phone_number,
                'website' => $info->result->website,
                'rating' => $info->result->rating

            ]);
            $data['lat'] = $info->result->geometry->location->lat;
            $data['lng'] = $info->result->geometry->location->lng;
        }
        $data['id'] = $this->add($data);
        $this->join_event($data['id'], $data['users_id']);
        return ['errors' => [], 'data' => $data];
    }


    public function join_event($event_id, $user_id)
    {
        if($this->db->get_where($this->etu_model->table, ['events_id' => $event_id, 'users_id' => $user_id])->row_array())
        {
            throw new Exception(lang('already_joined'), 400);
        }
        return !!$this->etu_model->add(['events_id' => $event_id, 'users_id' => $user_id]);
    }

    public function leave_event($event_id, $user_id)
    {
        if(!$this->db->get_where($this->etu_model->table, ['events_id' => $event_id, 'users_id' => $user_id])->row_array())
        {
            throw new Exception(lang('already_left'), 400);
        }
        return $this->db->delete($this->etu_model->table, ['events_id' => $event_id, 'users_id' => $user_id]);
    }

    public function get_nearest($lat, $lng, $radius = 500, $only_actual = true)
    {
        $this->db->select("e.*, round(( 3963 * acos( cos( radians( '{$lat}' ) ) * cos( radians( lat ) ) *
                cos( radians( lng ) - radians( '{$lng}' ) ) + sin( radians( '{$lat}' ) ) *
                sin( radians( lat ) ) ) ), 2) AS distance, e.users_id as creator_id, u.username as creator_username, u.birthday as creator_birthday, u.gender as creator_gender,
                (SELECT COUNT(etu.id) FROM {$this->etu_model->table} etu WHERE etu.events_id = e.id) as joined_count");
        $this->db->join($this->users_model->table.' u', 'e.users_id = u.id');
//        $this->db->join($this->etu_model->table.' etu', 'etu.events_id = e.id', 'left');
        $this->db->having("distance <= {$radius}");
        if($only_actual) $this->db->where('e.date >= CURDATE()');
        $events =  $this->db->get($this->table.' e')->result_array();
        foreach($events as &$event)
        {
            $event['creator_avatar'] = $this->users_model->get_photo($event['users_id'], $this->users_model->avatar);
            unset($event['users_id']);
            $event['place_info'] = json_decode($event['place_info']);
        }
        return $events;
    }

    public function get_event($event_id, $place_info = true)
    {
        $this->db->select('e.*, e.users_id as creator_id, u.username as creator_username, u.birthday as creator_birthday, u.gender as creator_gender');
        $this->db->join($this->users_model->table.' u', 'e.users_id = u.id');
        $this->db->where('e.id', $event_id);
        $event =  $this->db->get($this->table.' e')->row_array();
        if(!$event) return [];
        $event['creator_avatar'] = $this->users_model->get_photo($event['users_id'], $this->users_model->avatar);
        unset($event['users_id']);
        $event['place_info'] = json_decode($event['place_info']);
        return $event;


    }

    public function get_detailed_event($event_id)
    {
        $event = $this->get_event($event_id);
        if(!$event) return [];
        $event['joined'] = $this->etu_model->get_event_joined($event_id);
        $event['joined_count'] = count($event['joined']);
        $event['messages'] = $this->em_model->get_event_messages($event_id);

        return $event;
    }

    public function search($per_page, $page, $lat, $lng, $radius, $search_params)
    {
        $this->db->select("e.*, round(( 3963 * acos( cos( radians( '{$lat}' ) ) * cos( radians( lat ) ) *
                cos( radians( lng ) - radians( '{$lng}' ) ) + sin( radians( '{$lat}' ) ) *
                sin( radians( lat ) ) ) ), 2) AS distance, e.users_id as creator_id, u.username as creator_username, u.birthday as creator_birthday, u.gender as creator_gender,
                (SELECT COUNT(etu.id) FROM {$this->etu_model->table} etu WHERE etu.events_id = e.id) as joined_count");
        $this->db->join($this->users_model->table.' u', 'e.users_id = u.id');
        $this->db->having("distance <= {$radius}");
        foreach($search_params as $param => $value)
        {
            if(!$value) continue;
            switch($param)
            {
                case 'show_name':
                    $this->db->like('e.show_name', $value);
                    break;
                case 'place_type':
                case 'gender':
                case 'time':
                    if(!in_array($value, $this->fields[$param]['presets']))
                    {
                        throw new Exception(sprintf(lang('invalid_field'),ucfirst(lang($param))), 400);
                    }
                    $this->db->where('e.'.$param, $value);
                    break;
                case 'date_from':
                    $this->db->where("e.date >= '{$value}'");
                    break;
                case 'date_to':
                    $this->db->where("e.date <= '{$value}'");

            }
        }
        $db2 = clone $this->db;
        $total_count = count($db2->get($this->table.' e')->result_array());
        $this->db->limit($per_page, $per_page*($page-1));
        $events =  $this->db->get($this->table.' e')->result_array();
        foreach($events as &$event)
        {
            $event['creator_avatar'] = $this->users_model->get_photo($event['users_id'], $this->users_model->avatar);
            unset($event['users_id']);
            $event['place_info'] = json_decode($event['place_info']);

        }
        $result = [
            'result' => $events,
            'current_page' => $page,
            'total_pages'  => (int) ceil($total_count / $per_page),
            'total_count'  => $total_count

        ];
        return $result;
    }


    public function get_my($user_id)
    {
        $this->db->select("e.*, e.users_id as creator_id, u.username as creator_username, u.birthday as creator_birthday, u.gender as creator_gender,
                (SELECT COUNT(etu.id) FROM {$this->etu_model->table} etu WHERE etu.events_id = e.id) as joined_count");
        $this->db->join($this->users_model->table.' u', 'e.users_id = u.id');
        $this->db->where('e.users_id', $user_id);
        $events =  $this->db->get($this->table.' e')->result_array();
        foreach($events as &$event)
        {
            $event['creator_avatar'] = $this->users_model->get_photo($event['users_id'], $this->users_model->avatar);
            unset($event['users_id']);
            $event['place_info'] = json_decode($event['place_info']);
        }
        return $events;
    }

    public function get_my_joined($user_id)
    {
        $this->db->select("e.*, e.users_id as creator_id, u.username as creator_username, u.birthday as creator_birthday, u.gender as creator_gender,
                (SELECT COUNT(etu.id) FROM {$this->etu_model->table} etu WHERE etu.events_id = e.id) as joined_count");
        $this->db->join($this->users_model->table.' u', 'e.users_id = u.id');
        $this->db->join($this->etu_model->table.' etu', 'etu.events_id = e.id', 'left');
        $this->db->where('etu.users_id', $user_id);
        $this->db->where('e.users_id !=', $user_id);
        $events =  $this->db->get($this->table.' e')->result_array();
        foreach($events as &$event)
        {
            $event['creator_avatar'] = $this->users_model->get_photo($event['users_id'], $this->users_model->avatar);
            unset($event['users_id']);
            $event['place_info'] = json_decode($event['place_info']);
        }
        return $events;
    }











}