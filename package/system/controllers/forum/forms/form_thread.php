<?php

class formForumThread extends cmsForm {

    public function init($do, $is_moder) {

        $fields = [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_BASIC_OPTIONS,
                'childs' => [
                    new fieldString('title', [
                        'title'   => LANG_FORUM_THREAD_TITLE,
                        'options' => [
                            'max_length' => 250
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('description', [
                        'title'   => LANG_FORUM_THREAD_DESCRIPTION,
                        'options' => [
                            'max_length' => 250
                        ]
                    ])
                ]
            ]
        ];

        if($is_moder){
            $fields['basic']['childs'][] = new fieldCheckbox('fixed_first_post', [
                'title'   => LANG_FORUM_FIXED_FIRST_POST,
                'default' => false
            ]);
            if($do == 'edit'){
                $fields['basic']['childs'][] = new fieldCheckbox('change_url', [
                    'title'   => LANG_FORUM_THREAD_CHANGE_URL,
                    'default' => false
                ]);
                $fields['basic']['childs'][] = new fieldList('category_id', [
                    'title' => LANG_FORUM_MOVE_THREAD_IN_FORUM,
                    'generator' => function ($item, $request) {

                        $list = ['' => ''];

                        $user = cmsUser::getInstance();
                        $model = cmsCore::getModel('forum');

                        if (!$user->is_admin){
                            $model->filterEqual('c.is_pub', 1);
                        }

                        $cats = $model->getCategoriesTree('forum', false);

                        if ($cats) {
                            foreach ($cats as $cat) {

                                $cat['moderators'] = cmsModel::yamlToArray($cat['moderators']);

                                if (!$user->isInGroups($cat['moderators']) && !$user->is_admin) {
                                    continue;
                                }

                                if ($cat['ns_level'] > 1) {
                                    $cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
                                }

                                if ($cat['as_folder']) {
                                    $list['opt'.$cat['id']] = [$cat['title']];
                                } else {
                                    $list[$cat['id']] = $cat['title'];
                                }
                            }
                        }
                        return $list;
                    },
                    'rules' => [
                        ['required']
                    ]
                ]);
            }
        }

        return $fields;
    }

}
