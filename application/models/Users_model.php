<?php
require_once('MY_base_model.php');
class Users_model extends MY_base_model {

    public $min_pass_length = 4;
    public $entity_name = 'user';
    public $avatar = 'avatar';
    /** @var  User_settings_model */
    public $user_settings_model;
    protected $fields = [
        'email' => [
            'required' => true,
            'max_length' => 255,
            'min_length' => 3,
            'unique' => true
        ],
        'password' => [
            'required' => false,
            'max_length' => 50,
            'min_length' => 4,
            'unique' => false
        ],
        'username' => [
            'required' => true,
            'max_length' => 20,
            'min_length' => 4,
            'unique' => true
        ],
        'gender' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
            'presets' => ['male', 'female']
        ],
        'city_id' => [
            'required' => true,
            'max_length' => 0,
            'min_length' => 0,
            'unique' => false,
        ],
    ];

    public function __construct()
    {
        parent::__construct('users');
        $this->_ci->load->model('user_settings_model', 'user_settings_model');
        $this->user_settings_model = $this->_ci->user_settings_model;
    }


    public function login($login, $password)
    {
        if(!$login || !$password) return false;
        $this->db->select("id, username, email, birthday, created, gender, hash as token, updated");
        $user = $this->db->get_where($this->table, "(email = '{$login}' OR username = '{$login}') AND password = SHA1('{$password}')")->row_array();
        if(!$user) return false;
        $user['avatar'] = $this->get_photo($user['id'], $this->avatar);
        $user['token'] = $this->set_token($user['id']);
        return $user;
    }



    function register($data)
    {
        $this->fields['password']['required'] = true;
        $errors = $this->validate($data);
        unset($data['pass2']);
        if($errors)
        {
            unset($data['password']);
            return ['errors' => $errors, 'profile' => $data];
        }
        $data['password'] = sha1($data['password']);
        $data['active'] = true;
        $data['hash'] = $this->generate_token();
        $data['id'] = $this->add($data);
        unset($data['password']);
        $this->user_settings_model->add(['users_id' => $data['id']]);
        $this->send_email($data['id'], lang('welcome_email_subject'), lang('welcome_email'));
        return ['errors' => '', 'profile' => $data];


    }

    function update_profile($id, $data)
    {
        $errors = $this->validate($data, $id);
        unset($data['pass2']);
        if($errors)
        {
            unset($data['password']);
            return ['errors' => $errors, 'profile' => $data];
        }
        if(array_key_exists('password', $data)) $data['password'] = sha1($data['password']);
        $this->update($id, $data);
        unset($data['password']);
        return ['errors' => '', 'profile' => $data];
    }

    function get_profile($id)
    {
        $this->db->select("id, username, email, birthday, created, gender, '{$this->get_photo($id, $this->avatar)}' as avatar");
        return $this->get_by_id($id);
    }

    function get_extended_profile($id)
    {
        $this->db->select("id, username, email, birthday, created, gender, city_id, hash as token, updated, '{$this->get_photo($id, $this->avatar)}' as avatar");
        return $this->get_by_id($id);
    }



    protected function validate($data, $id = null)
    {
        $errors = parent::validate($data, $id);
        if(
            !array_key_exists('password', $errors) &&
            array_key_exists('password', $data) &&
            (
                (array_key_exists('pass2', $data) && $data['password'] != $data['pass2']) ||
                !array_key_exists('pass2', $data)
            )
        )
        {
            $errors['password'] = lang('pass_dont_match');
        }
        if(!array_key_exists('city_id', $errors) &&
            array_key_exists('city_id', $data))
        {
            $gm = new GoogleMapAPI();
            $coords = $gm->geoPlaceCoords($data['city_id']);
            if(!$coords)
            {
                $errors['city_id'] = sprintf(lang('invalid_field'),ucfirst(lang('city_id')));
            }
        }

        return $errors;
    }

    public function check_token($token)
    {
        return $this->db->get_where($this->table, ['hash' => $token])->row_array();
    }

    public function set_token($id)
    {
        $token = $this->generate_token();
        $this->update($id, ['hash' => $token]);
        return $token;
    }


    private function generate_token()
    {
        return random_string('sha1', 15);
    }


    public function send_email($user_id, $subject, $text)
    {
        $profile = $this->get_profile($user_id);
        if(!$profile || !$this->is_valid_email($profile['email']))
        {
            return false;
        }

        $this->_ci->load->library('email');
//        $this->email = $this->_ci->email;
        $this->_ci->email->from(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $this->_ci->email->to($profile['email']);

        $this->_ci->email->subject($subject);
        $this->_ci->email->message($text);

        return $this->_ci->email->send();
    }








}