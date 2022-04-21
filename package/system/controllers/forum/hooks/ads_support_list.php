<?php

class onForumAdsSupportList extends cmsAction {

    /**
     * Флаг, что в изначальном комплекте контроллера
     * этого хука не было
     *
     * @var boolean
     */
    public $external = 'ads';

    protected $extended_langs = ['ads'];

    public function run(){
        return [
            'name'  => $this->name,
            'types' => [
                'post_list' => sprintf(LANG_ADS_SHOW, LANG_FORUM_FORUMS_POSTS)
            ]
        ];
    }

}
