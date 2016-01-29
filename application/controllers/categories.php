<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once($_SERVER['DOCUMENT_ROOT'].'/application/controllers/MY_base_controller.php');
class Categories extends MY_base_controller{

    public $per_page = 20;
    public $module = 'categories';
    function __construct()
    {
        parent::__construct('categories');
        $this->bulk_actions = [
            'separated' =>
                [
                    [
                        'url' => 'categories/ajax_delete',
                        'title' => lang('delete'),
                        'class' => 'danger'
                    ]
                ],
            [
                'url' => 'categories/ajax_activate',
                'title' => lang('make_active'),
                'class' => 'info'
            ],
            [
                'url' => 'categories/ajax_deactivate',
                'title' => lang('make_inactive'),
                'class' => 'warning'
            ],
            [
                'url' => 'categories/ajax_publish',
                'title' => lang('make_published'),
                'class' => 'info'
            ],
            [
                'url' => 'categories/ajax_unpublish',
                'title' => lang('make_unpublished'),
                'class' => 'warning'
            ],
            [
                'url' => 'categories/ajax_chargeable',
                'title' => lang('make_chargeable'),
                'class' => 'info'
            ],
            [
                'url' => 'categories/ajax_free',
                'title' => lang('make_free'),
                'class' => 'warning'
            ]
        ];
    }

    public function index($page = 1)
    {
        $pages = 12;
        $list = [
            [
                'title' => 'Сидя',
                'id' => 1,
                'active' => 1,
                'published' => 1,
                'free' => 0,
                'poses' => '-',
                'subcategories' => [['32'], ['21313'], ['13123']]
            ],
            [
                'title' => 'Стоя',
                'id' => 2,
                'active' => 0,
                'published' => 1,
                'free' => 1,
                'poses' => 8,
                'subcategories' => []
            ],
            [
                'title' => 'Лежа',
                'active' => 0,
                'id' => 3,
                'published' => 1,
                'free' => 0,
                'poses' => 14,
                'subcategories' => []
            ],
            [
                'title' => 'Сидя',
                'id' => 1,
                'active' => 1,
                'published' => 1,
                'free' => 0,
                'poses' => '-',
                'subcategories' => [['32'], ['21313'], ['13123']]
            ],
            [
                'title' => 'Стоя',
                'id' => 2,
                'active' => 0,
                'published' => 1,
                'free' => 1,
                'poses' => 8,
                'subcategories' => []
            ],
            [
                'title' => 'Лежа',
                'active' => 0,
                'id' => 3,
                'published' => 1,
                'free' => 0,
                'poses' => 14,
                'subcategories' => []
            ]
        ];

        $this->render_view('index', [['url' => 'categories', 'title' => 'Categories']], ['list' => $list, 'page' => $page, 'pages' => $pages] , 'categories');
    }

    public function edit($id)
    {
        $item = ['title' => 'Лежа',
            'active' => 0,
            'id' => 3,
            'published' => 1,
            'free' => 0,
            'poses' => 14,
            'subcategories' => []];

        $breadcrumbs = [
            ['url' => 'categories', 'title' => 'Categories'],
            ['url' => 'categories/edit/'.$id, 'title' => $item['title']]
        ];
        $this->render_view('form', $breadcrumbs, ['item' => $item] , 'categories');
    }

}
