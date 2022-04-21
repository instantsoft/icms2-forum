<?php

class onForumAwardsRulesList extends cmsAction {

    /**
     * Флаг, что в изначальном комплекте контроллера
     * этого хука не было
     *
     * @var boolean
     */
    public $external = 'awards';

    protected $extended_langs = ['awards'];

    public function run(){
        return [
            'name'  => $this->name,
            'types' => [
                'post_count' => sprintf(LANG_AW_QUANTITY, LANG_AW_QUANTITY_FORUM)
            ]
        ];
    }

}
