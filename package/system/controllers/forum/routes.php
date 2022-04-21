<?php

function routes_forum() {

    return array(
        // Предпросмотр первого сообщения темы
        array(
            'pattern' => '/^forum\/first-post$/i',
            'action'  => 'first_post_view_ajax'
        ),
        // Поддержка URL страниц форума InstantCMS v. 1.10... с пагинацией
        array(
            'pattern' => '/^forum\/thread([0-9]+)-([0-9]+).html$/i',
            'action'  => 'thread_redirect',
            1         => 'thread_id',
            2         => 'page'
        ),
        // Поддержка URL страниц форума InstantCMS v. 1.10...
        array(
            'pattern' => '/^forum\/thread([0-9]+).html$/i',
            'action'  => 'thread_redirect',
            1         => 'thread_id'
        ),
        // Поддержка URL страниц разделов форума InstantCMS v. 1.10...
        array(
            'pattern' => '/^forum\/([0-9]+)$/i',
            'action'  => 'category_redirect',
            1         => 'category_id'
        ),
        // Поддержка URL страниц разделов форума InstantCMS v. 1.10... с пагинацией
        array(
            'pattern' => '/^forum\/([0-9]+)-([0-9]+)$/i',
            'action'  => 'category_redirect',
            1         => 'category_id',
            2         => 'page'
        ),
        // Страница темы
        array(
            'pattern' => '/^forum\/([a-z0-9_\-\/]+).html$/i',
            'action'  => 'thread_view',
            1         => 'slug'
        ),
        // Страница раздела
        array(
            'pattern' => '/^forum\/([a-z0-9_\-\/]+)$/i',
            'action'  => 'category_view',
            1         => 'slug'
        )
    );

}
