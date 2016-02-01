<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'admin';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['rest/(:any)/(:any)']        = '404';
$route['api/events']                = 'rest/event/create';
$route['api/(:any)']                = 'rest/user/$1';
$route['api/profile/avatar']        = 'rest/user/avatar';
$route['api/events/city']           = 'rest/event/nearest_by_user';

$route['api/events/(:num)/join']    = 'rest/event/join/$1';
$route['api/events/(:num)/leave']   = 'rest/event/leave/$1';
$route['api/events/(:num)']         = 'rest/event/details/$1';
$route['api/events/(:num)/message'] = 'rest/event/message/$1';
$route['api/events/nearest']        = 'rest/event/nearest_by_coords';
$route['api/events/search']         = 'rest/event/search';
$route['api/events/my']             = 'rest/event/my_events';
$route['api/profile/settings']      = 'rest/user/settings';
//$route['api/profile/deactivate']    = 'rest/user/status/0';

$route['(:any)/ajax_(:any)']        = '$1/ajax/$2';

