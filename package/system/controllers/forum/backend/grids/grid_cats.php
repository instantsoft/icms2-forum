<?php

function grid_cats($controller, $category) {

    $options = [
        'is_sortable' => false,
        'order_by'    => 'ns_left'
    ];

    $columns        = [
        'id'    => [
            'title'  => 'id',
            'width'  => 30,
            'filter' => 'like'
        ],
        'title' => [
            'title'   => LANG_TITLE,
            'href'    => $controller->cms_template->href_to('category_edit', ['{id}']),
            'filter'  => 'like',
            'handler' => function ($v, $row) use ($category) {
                return str_repeat('-- ', max(0, ($row['ns_level'] - $category['ns_level']) - 1)) . $v;
            }
        ],
        'threads_count' => [
            'title'  => LANG_FORUM_THREADS,
            'width'  => 80,
            'filter' => 'like'
        ],
        'posts_count'   => [
            'title'  => LANG_FORUM_POSTS,
            'width'  => 80,
            'filter' => 'like'
        ],
        'is_pub'        => [
            'title'       => LANG_ON,
            'width'       => 40,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'forum_cats', 'is_pub'])
        ],
    ];

    $actions = [
        [
            'title' => LANG_VIEW,
            'class' => 'view',
            'href'  => href_to('forum', '{slug}')
        ],
        [
            'title' => LANG_FORUM_CAT_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'category_edit', ['{id}'])
        ],
        [
            'title'   => LANG_CP_FORUM_CAT_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'category_delete', ['{id}']),
            'confirm' => LANG_CP_FORUM_CAT_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];

}
