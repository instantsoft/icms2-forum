<?php

class formForumThreadVip extends cmsForm {

    public function init() {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldCheckbox('is_vip', [
                        'title' => LANG_FORUM_THREAD_VIP_ADD
                    ]),
                    new fieldDate('vip_expires', [
                        'title' => LANG_FORUM_THREAD_VIP_EXPIRES,
                        'hint'  => LANG_FORUM_THREAD_VIP_EXPIRES_HINT,
                        'options' => [
                            'show_time' => true
                        ],
                        'visible_depend' => ['is_vip' => ['show' => ['1']]]
                    ])
                )
            ]
        ];
    }

}
