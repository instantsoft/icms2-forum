<?php

function grid_cats($controller, $category) {

    $options = array(
        'is_auto_init'  => false,
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'show_id'       => true,
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
    );

    $columns = array(
        'id' => array(
            'title'  => 'id',
            'width'  => 30,
            'filter' => 'like'
        ),
        'title' => array(
            'title'  => LANG_TITLE,
            'href'   => href_to($controller->root_url, 'category_edit', ['{id}']),
            'filter' => 'like',
            'handler' => function ($v, $row) use($category){
                return str_repeat('-- ', ($row['ns_level']-$category['ns_level']-1)) . $v;
            }
        ),
        'threads_count' => array(
            'title'  => LANG_FORUM_THREADS,
            'width'  => 80,
            'filter' => 'like'
        ),
        'posts_count' => array(
            'title'  => LANG_FORUM_POSTS,
            'width'  => 80,
            'filter' => 'like'
        ),
        'is_pub' => array(
            'title'       => LANG_ON,
            'width'       => 40,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', array('{id}', 'forum_cats', 'is_pub'))
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'view',
            'href'  => href_to('forum', '{slug}')
        ),
        array(
            'title' => LANG_FORUM_CAT_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'category_edit', ['{id}'])
        ),
        array(
            'title'   => LANG_CP_FORUM_CAT_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'category_delete', ['{id}']),
            'confirm' => LANG_CP_FORUM_CAT_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );

}
