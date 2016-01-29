<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once($_SERVER['DOCUMENT_ROOT'].'/application/controllers/MY_base_controller.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/application/libraries/GoogleMap.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php');
use bart\GooglePlaces\GooglePlaces;
class Admin extends MY_base_controller{

    /** @var  Users_model */
    public $users_model;
    /** @var  Events_model */
    public $events_model;
    /** @var  Events_to_users_model */
    public $etu_model;
    function __construct()
    {
        parent::__construct('admin');
    }

    public function index()
    {

//
        $gm = new GoogleMapAPI();
////        $result = $gm->geoPlaceInfo('ChIJiw-rY5-gJ0ERCr6kGmgYTC0');
//        $result = $gm->geoPlaceCoords('ChIJ01SmsCgLJ0ER9lfv6vhNKi0');
        $this->load->model('events_model');
        $this->load->model('users_model');
        $this->load->model('events_to_users_model', 'etu_model');
        $profile = $this->users_model->get_extended_profile(5);
        $coords = $gm->geoPlaceCoords($profile['city_id']);
        $params = ['gender' => ''];
        $r =$this->events_model->search(5,1, $coords->lat, $coords->lng, 5000, $params);
        dump($r);die;
        $this->render_view('index', [], [] , 'admin');
    }

}
