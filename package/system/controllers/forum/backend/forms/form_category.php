<?php

class formForumCategory extends cmsForm {

    public $is_tabbed = true;

    public function init($do = false) {

        $options = cmsController::loadOptions('forum');

        $form = array(
            'basic' => array(
                'title'  => LANG_BASIC_OPTIONS,
                'type'   => 'fieldset',
                'childs' => array(
                    new fieldString('title', array(
                        'title' => LANG_CP_FORUM_CAT_TITLE,
                        'options' => array(
                            'max_length' => 200
                        ),
                        'rules'   => array(
                            array('required')
                        )
                    )),
                    new fieldText('description', array(
                        'title'   => LANG_CP_FORUM_CAT_DESCRIPTION,
                        'options' => array(
                            'max_length' => 4096,
                            'show_symbol_count' => true
                        )
                    )),
                    new fieldCheckbox('is_pub', array(
                        'title'   => LANG_CP_FORUM_CAT_IS_PUB,
                        'default' => 1,
                    )),
                    new fieldCheckbox('as_folder', array(
                        'title' => LANG_CP_FORUM_CAT_AS_FOLDER,
                        'hint'  => LANG_CP_FORUM_CAT_AS_FOLDER_HINT
                    ))
                )
            ),
            'advanced' => array(
                'type'   => 'fieldset',
                'title'  => LANG_CP_FORUM_ADVANCED_OPTIONS,
                'childs' => array(
                    new fieldImage('icon', array(
                        'title'   => LANG_CP_FORUM_CAT_ICON,
                        'hint'    => LANG_CP_FORUM_CAT_ICON_HINT,
                        'options' => array(
                            'sizes' => array(
                                'micro',
                                'small',
                                'normal'
                            )
                        )
                    )),
                    new fieldCheckbox('autoflood', array(
                        'title' => LANG_CP_FORUM_CAT_AUTOFLOOD,
                        'hint'  => LANG_CP_FORUM_CAT_AUTOFLOOD_HINT
                    )),
                    new fieldList('moderators', array(
                        'title' => LANG_CP_FORUM_CAT_MODERATORS,
                        'is_chosen_multiple' => true,
                        'generator' => function ($item) use ($options) {

                            $user_model = cmsCore::getModel('users');

                            if (!empty($options['can_moder']) && $options['can_moder'] !== array(0)) {
                                $user_model->filterGroups($options['can_moder']);
                            }

                            $users = $user_model->limit(false)->getUsers();

                            $items = ['' => ''];

                            if ($users) {
                                foreach ($users as $user) {
                                    $items[$user['id']] = $user['id'] . ' ' . $user['nickname'];
                                }
                            }

                            return $items;
                        }
                    )),
                    new fieldList('options:tpl_cats', array(
                        'title'     => LANG_FORUM_TPL_CATS,
                        'hint'      => LANG_FORUM_TPL_CATS_HINT,
                        'default'   => $options['tpl_cats'],
                        'generator' => function ($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'category_view*.tpl.php', cmsConfig::get('template'));
                        }
                    )),
                    new fieldList('options:tpl_threads', array(
                        'title'   => LANG_FORUM_TPL_THREADS,
                        'hint'    => LANG_FORUM_TPL_THREADS_HINT,
                        'default' => !empty($options['tpl_threads']) ? $options['tpl_threads'] : 'thread_view',
                        'generator' => function ($item) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'thread_view*.tpl.php', cmsConfig::get('template'));
                        }
                    ))
                )
            ),
            'seo' => array(
                'type'   => 'fieldset',
                'title'  => LANG_SEO,
                'childs' => array(
                    new fieldString('slug_key', array(
                        'title'   => LANG_CP_FORUM_CAT_SLUG_KEY,
                        'options' => array(
                            'max_length' => 255
                        ),
                        'prefix' => href_to('forum').'/',
                        'hint'   => LANG_CP_FORUM_CAT_SLUG_KEY_HINT
                    )),
                    new fieldString('seo_title', array(
                        'title'   => LANG_CP_FORUM_CAT_SEO_TITLE,
                        'options' => array(
                            'max_length' => 255
                        ),
                        'hint' => LANG_CP_FORUM_CAT_SEO_TITLE_HINT
                    )),
                    new fieldString('seo_keys', array(
                        'title'   => LANG_CP_FORUM_CAT_SEO_KEYS,
                        'options' => array(
                            'max_length' => 255
                        ),
                        'hint' => LANG_CP_FORUM_CAT_SEO_KEYS_HINT
                    )),
                    new fieldText('seo_desc', array(
                        'title'   => LANG_CP_FORUM_CAT_SEO_DESC,
                        'options' => array(
                            'max_length' => 255
                        ),
                        'hint' => LANG_CP_FORUM_CAT_SEO_DESC_HINT
                    ))
                )
            ),
            'perms' => array(
                'type'   => 'fieldset',
                'title'  => LANG_PERMISSIONS,
                'childs' => array(
                    new fieldListGroups('groups_read', array(
                        'title'       => LANG_CP_FORUM_CAT_GROUPS_READ,
                        'show_all'    => true,
                        'show_guests' => true
                    )),
                    new fieldListGroups('groups_edit', array(
                        'title'       => LANG_CP_FORUM_CAT_GROUPS_EDIT,
                        'show_all'    => true,
                        'show_guests' => false
                    ))
                )
            )
        );

        // если раздел создаётся, добавляем выбор категории
        if ($do == 'add') {
            $form['basic']['childs'][] = new fieldList('parent_id', array(
                'title' => LANG_CP_FORUM_CAT_PARENT_ID,
                'rules' => array(
                    array('required')
                ),
                'generator' => function ($cat) {

                    $tree = cmsCore::getModel('forum')->limit(0)->getCategoriesTree('forum');

                    if ($tree) {
                        foreach ($tree as $item) {

                            // при редактировании исключаем себя и вложенные
                            // подкатегории из списка выбора родителя
                            if (isset($cat['ns_left'])) {
                                if ($item['ns_left'] >= $cat['ns_left'] && $item['ns_right'] <= $cat['ns_right']) {
                                    continue;
                                }
                            }

                            $items[$item['id']] = str_repeat('- ', $item['ns_level']) . ' ' . $item['title'];
                        }
                    }

                    return $items;
                }
            ));
        }

        return $form;
    }

}
