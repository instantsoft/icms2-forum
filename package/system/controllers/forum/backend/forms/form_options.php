<?php

class formForumOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $item_fields = [
            'category'    => LANG_CATEGORY,
            'title'       => LANG_TITLE,
            'description' => LANG_DESCRIPTION,
            'date_pub'    => LANG_DATE,
            'posts_count' => LANG_FORUM_ANSWER_COUNT,
            'hits'        => LANG_HITS
        ];

        $meta_subscribe_fields = [
            'subjects'        => LANG_FORUM_SBSCR_SUBJECTS_URLS,
            'unsubscribe_url' => LANG_FORUM_SBSCR_UNSUBSCRIBE_URL,
            'list_url'        => LANG_FORUM_SBSCR_LIST_URL,
            'title'           => LANG_FORUM_SBSCR_LIST_TITLE,
            'site'            => LANG_CP_SETTINGS_SITENAME,
            'date'            => LANG_DATE,
            'time'            => LANG_PARSER_CURRENT_TIME,
            'nickname'        => LANG_USER
        ];

        $options = array(

            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_BASIC_OPTIONS,
                'childs' => array(

                    'basic_can_moder' => new fieldListGroups('can_moder', array(
                        'title' => LANG_FORUM_CAN_MODER,
                        'show_all' => true
                    )),

                    'basic_cats_level_view' => new fieldList('cats_level_view', array(
                        'title' => LANG_FORUM_CATS_LEVEL_VIEW,
                        'default' => 3,
                        'items' => array(
                            '1' => 1,
                            '2' => 2,
                            '3' => 3
                        )
                    )),

                    'basic_tpl_index' => new fieldList('tpl_index', array(
                        'title' => LANG_FORUM_TPL_INDEX,
                        'hint' => LANG_FORUM_TPL_INDEX_HINT,
                        'default' => 'index',
                        'generator' => function ($item){
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'index*.tpl.php', cmsConfig::get('template'));
                        }
                    ))

                )
            ),

            'cats' => array(
                'type' => 'fieldset',
                'title' => LANG_FORUM_CAT_VIEW,
                'childs' => array(

                    'cats_perpage_threads' => new fieldNumber('perpage_threads', array(
                        'title' => LANG_FORUM_PERPAGE_THREADS,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    'cats_is_rss' => new fieldCheckbox('is_rss', array(
                        'title' => LANG_FORUM_IS_RSS,
                        'default' => true,
                    )),

                    'cats_preview_thread' => new fieldCheckbox('preview_thread', array(
                        'title' => LANG_FORUM_PREVIEW_THREAD,
                        'default' => true,
                    )),

                    'cats_fix_threads_reads' => new fieldCheckbox('fix_threads_reads', array(
                        'title' => LANG_FORUM_FIX_THREADS_READS,
                        'default' => false,
                    )),

                    'cats_tpl_cats' => new fieldList('tpl_cats', array(
                        'title' => LANG_FORUM_TPL_CATS,
                        'hint' => LANG_FORUM_TPL_CATS_HINT,
                        'default' => 'category_view',
                        'generator' => function ($item){
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'category_view*.tpl.php', cmsConfig::get('template'));
                        }
                    )),

                    new fieldList('threads_sorting', array(
                        'title'  => LANG_FORUM_TREADS_SORTING,
                        'add_title'    => LANG_SORTING_ADD,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SORTING_FIELD,
                        'multiple_keys' => [
                            'by' => 'field', 'to' => 'field_select'
                        ],
                        'generator' => function(){
                            return [
                                'is_pinned'          => LANG_FORUM_ATTACHED_THREAD,
                                'is_vip'             => LANG_FORUM_THREAD_VIP,
                                'date_last_modified' => LANG_FORUM_MODIFY_DATE,
                                'date_pub'           => LANG_DATE_PUB,
                                'hits'               => LANG_FORUM_HITS_COUNT
                            ];
                        },
                        'value_items' => [
                            'asc'  => LANG_SORTING_ASC,
                            'desc' => LANG_SORTING_DESC
                        ],
                        'default' => [
                            [
                                'by' => 'is_pinned',
                                'to' => 'desc'
                            ],
                            [
                                'by' => 'date_pub',
                                'to' => 'desc'
                            ]
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ))
                )
            ),

            'ds_menu' => array(
                'type' => 'fieldset',
                'title' => LANG_FORUM_DATASETS,
                'childs' => [
                    'show_ds_menu_index' => new fieldCheckbox('show_ds_menu_index', array(
                        'title' => LANG_FORUM_SHOW_DS_MENU_INDEX,
                        'hint' => sprintf(LANG_FORUM_SHOW_DS_MENU_INDEX_HINT, href_to('forum'), href_to('forum', 'all')),
                        'default' => false
                    )),
                    'menu_index_seo_h1' => new fieldString('menu_index:seo:h1', array(
                        'title' => LANG_SEO_H1,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_index' => array('show' => array('1')))
                    )),
                    'menu_index_seo_title' => new fieldString('menu_index:seo:title', array(
                        'title' => LANG_SEO_TITLE,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_index' => array('show' => array('1')))
                    )),
                    'menu_index_seo_desc' => new fieldString('menu_index:seo:desc', array(
                        'title' => LANG_SEO_DESC,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_index' => array('show' => array('1')))
                    )),
                    'menu_index_seo_keys' => new fieldString('menu_index:seo:keys', array(
                        'title' => LANG_SEO_KEYS,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_index' => array('show' => array('1')))
                    )),
                    'show_ds_menu_lthr' => new fieldCheckbox('show_ds_menu_lthr', array(
                        'title' => LANG_FORUM_SHOW_DS_MENU_LTHR,
                        'default' => false
                    )),
                    'menu_lthr_seo_h1' => new fieldString('menu_lthr:seo:h1', array(
                        'title' => LANG_SEO_H1,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lthr' => array('show' => array('1')))
                    )),
                    'menu_lthr_seo_title' => new fieldString('menu_lthr:seo:title', array(
                        'title' => LANG_SEO_TITLE,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lthr' => array('show' => array('1')))
                    )),
                    'menu_lthr_seo_desc' => new fieldString('menu_lthr:seo:desc', array(
                        'title' => LANG_SEO_DESC,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lthr' => array('show' => array('1')))
                    )),
                    'menu_lthr_seo_keys' => new fieldString('menu_lthr:seo:keys', array(
                        'title' => LANG_SEO_KEYS,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lthr' => array('show' => array('1')))
                    )),
                    'show_ds_menu_lp' => new fieldCheckbox('show_ds_menu_lp', array(
                        'title' => LANG_FORUM_SHOW_DS_MENU_LP,
                        'default' => false
                    )),
                    'menu_lp_seo_h1' => new fieldString('menu_lp:seo:h1', array(
                        'title' => LANG_SEO_H1,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lp' => array('show' => array('1')))
                    )),
                    'menu_lp_seo_title' => new fieldString('menu_lp:seo:title', array(
                        'title' => LANG_SEO_TITLE,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lp' => array('show' => array('1')))
                    )),
                    'menu_lp_seo_desc' => new fieldString('menu_lp:seo:desc', array(
                        'title' => LANG_SEO_DESC,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lp' => array('show' => array('1')))
                    )),
                    'menu_lp_seo_keys' => new fieldString('menu_lp:seo:keys', array(
                        'title' => LANG_SEO_KEYS,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'visible_depend' => array('show_ds_menu_lp' => array('show' => array('1')))
                    )),
                    'show_ds_menu_mythr' => new fieldCheckbox('show_ds_menu_mythr', array(
                        'title' => LANG_FORUM_SHOW_DS_MENU_MYTHR,
                        'default' => false
                    )),
                    'show_ds_menu_myp' => new fieldCheckbox('show_ds_menu_myp', array(
                        'title' => LANG_FORUM_SHOW_DS_MENU_MYP,
                        'default' => false
                    ))
                ]
            ),
            'threads' => array(
                'type' => 'fieldset',
                'title' => LANG_FORUM_THREAD_VIEW,
                'childs' => array(

                    'threads_perpage_posts' => new fieldNumber('perpage_posts', array(
                        'title' => LANG_FORUM_PERPAGE_POSTS,
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),

                    'threads_show_rating' => new fieldCheckbox('show_rating', array(
                        'title' => LANG_FORUM_SHOW_RATING,
                        'default' => true
                    )),

                    'threads_poll_counts' => new fieldNumber('poll_counts', array(
                        'title' => LANG_FORUM_POLL_COUNTS,
                        'default' => 12,
                        'rules' => array(
                            array('required'),
                            array('min', 2)
                        )
                    )),

                    'threads_item_url_pattern' => new fieldString('item_url_pattern', array(
                        'title' => LANG_CP_FORUM_ITEM_URL_PATTERN,
                        'options'=>array(
                            'max_length'=> 255
                        ),
                        'prefix' => href_to('forum'),
                        'suffix' => '.html',
                        'hint' => LANG_CP_FORUM_ITEM_URL_PATTERN_HINT,
                        'default' => '{title}',
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldString('threads:seo_title_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    )),
                    new fieldString('threads:seo_keys_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_KEYS,
                        'default' => '{title|string_get_meta_keywords}',
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    )),
                    new fieldString('threads:seo_desc_pattern', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'default' => '{title|string_get_meta_description}',
                        'patterns_hint' => [
                            'patterns' =>  $item_fields
                        ]
                    )),

                    'threads_tpl_threads' => new fieldList('tpl_threads', array(
                        'title' => LANG_FORUM_TPL_THREADS,
                        'hint' => LANG_FORUM_TPL_THREADS_HINT,
                        'default' => 'thread_view',
                        'generator' => function ($item){
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'thread_view*.tpl.php', cmsConfig::get('template'));
                        }
                    )),

                    'threads_thread_prepend_html' => new fieldText('thread_prepend_html', array(
                        'title' => LANG_FORUM_THREAD_PREPEND_HTML,
                        'hint' => LANG_FORUM_THREAD_PREPEND_HTML_HINT,
                    )),

                    'threads_thread_append_html' => new fieldText('thread_append_html', array(
                        'title' => LANG_FORUM_THREAD_APPEND_HTML,
                        'hint' => LANG_FORUM_THREAD_APPEND_HTML_HINT,
                    )),
                    'thread_enable_subscriptions' => new fieldCheckbox('thread_enable_subscriptions', [
                        'title'   => LANG_FORUM_SBSCR_ON,
                        'default' => true
                    ]),
                    'thread_subscriptions_letter_tpl' => new fieldHtml('thread_subscriptions_letter_tpl', [
                        'title' => LANG_FORUM_SBSCR_LETTER_TPL,
                        'hint' => LANG_FORUM_SBSCR_LETTER_TPL_HINT,
                        'options' => ['editor' => 'ace'],
                        'patterns_hint' => [
                            'patterns' =>  $meta_subscribe_fields,
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ],
                        'visible_depend' => ['thread_enable_subscriptions' => ['show' => ['1']]]
                    ]),
                    'thread_subscriptions_notify_text' => new fieldString('thread_subscriptions_notify_text', [
                        'title' => LANG_FORUM_SBSCR_NOTIFY_TEXT,
                        'hint' => LANG_FORUM_SBSCR_NOTIFY_TEXT_HINT,
                        'is_clean_disable' => true,
                        'visible_depend' => ['thread_enable_subscriptions' => ['show' => ['1']]]
                    ])
                )

            ),

            'posts' => array(
                'type' => 'fieldset',
                'title' => LANG_FORUM_ADD_POSTS,
                'childs' => array(

                    'posts_user_fields' => new fieldListMultiple('user_fields', array(
                        'title' => LANG_FORUM_USER_FIELDS,
                        'generator' => function ($item){

                            $items = [];

                            $fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('{users}');

                            foreach ($fields as $field) {

                                if (in_array($field['name'], array('nickname', 'avatar', 'forum_sign'))){
                                    continue;
                                }

                                $items[$field['name']] = $field['title'];
                            }

                            return $items;
                        }
                    )),

                    'posts_show_users_groups' => new fieldCheckbox('show_users_groups', array(
                        'title' => LANG_FORUM_SHOW_USERS_GROUPS,
                        'default' => false
                    )),

                    'posts_post_interval' => new fieldNumber('post_interval', array(
                        'title' => LANG_FORUM_POST_INTERVAL,
                        'default' => 20,
                        'units' => LANG_SECOND2
                    )),

                    'posts_fast_answer' => new fieldCheckbox('fast_answer', array(
                        'title' => LANG_FORUM_FAST_ANSWER_FORM,
                        'default' => true
                    )),

                    'posts_combine_post' => new fieldCheckbox('combine_post', array(
                        'title' => LANG_FORUM_COMBINE_POST,
                    )),

                    'posts_combine_interval' => new fieldNumber('combine_interval', array(
                        'title' => LANG_FORUM_COMBINE_INTERVAL,
                        'hint' => LANG_FORUM_COMBINE_INTERVAL_HINT,
                        'default' => 1440,
                        'units' => LANG_FORUM_COMBINE_INTERVAL_UNITS,
                        'visible_depend' => array('combine_post' => array('show' => array('1')))
                    )),

                    'posts_quote_template' => new fieldHtml('quote_template', array(
                        'title' => LANG_FORUM_CP_QUOTE_TEMPLATE,
                        'options' => [
                            'editor' => 'ace'
                        ],
                        'patterns_hint' => [
                            'patterns' => [
                                'user_nickname' => LANG_NICKNAME,
                                'content' => LANG_CONTENT
                            ],
                            'text_panel' => '',
                            'always_show' => true,
                            'text_pattern' =>  LANG_CP_SEOMETA_HINT_PATTERN
                        ],
                        'rules' => [
                            ['required']
                        ]
                    )),

                    'posts_editor' => new fieldList('editor', array(
                        'title' => LANG_PARSER_HTML_EDITOR,
                        'default' => cmsConfig::get('default_editor'),
                        'generator' => function($item){
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    )),

                    'posts_editor_presets' => new fieldList('editor_presets', array(
                        'title'        => LANG_PARSER_HTML_EDITOR_GR,
                        'is_multiple'  => true,
                        'dynamic_list' => true,
                        'select_title' => LANG_SELECT,
                        'disable_array_key_rules' => true,
                        'multiple_keys' => array(
                            'group_id' => 'field', 'preset_id' => 'field_select'
                        ),
                        'generator' => function($item){
                            $users_model = cmsCore::getModel('users');

                            $items = [];

                            $groups = $users_model->getGroups(true);

                            foreach($groups as $group){
                                $items[$group['id']] = $group['title'];
                            }

                            return $items;
                        },
                        'values_generator' => function() {
                            $items = ['' => 'Textarea'];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    )),

                    'posts_is_html_filter' => new fieldCheckbox('is_html_filter', array(
                        'title' => LANG_PARSER_HTML_FILTERING,
                        'default' => true
                    )),

                    'posts_build_redirect_link' => new fieldCheckbox('build_redirect_link', array(
                        'title' => LANG_PARSER_BUILD_REDIRECT_LINK,
                        'default' => false,
                        'visible_depend' => array('is_html_filter' => array('show' => array('1')))
                    )),

                    'posts_build_tpl_posts' => new fieldList('tpl_posts', array(
                        'title' => LANG_FORUM_TPL_POSTS,
                        'hint' => LANG_FORUM_TPL_POSTS_HINT,
                        'default' => 'posts_view',
                        'generator' => function ($item){
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/forum', 'posts_view*.tpl.php', cmsConfig::get('template'));
                        }
                    ))

                )

            ),

            'attach' => array(
                'type' => 'fieldset',
                'title' => LANG_FORUM_ATTACHES,
                'childs' => array(

                    'attach_enable_file' => new fieldCheckbox('enable_file', array(
                        'title' => LANG_FORUM_ENABLE_FILE
                    )),

                    'attach_file_ext' => new fieldString('file_ext', array(
                        'title' => LANG_FORUM_FILE_EXT,
                        'hint' => LANG_FORUM_FILE_EXT_HINT,
                        'default' => 'txt, doc, zip, rar, arj, png, gif, jpg, jpeg',
                        'rules' => array(
                            array('required')
                        ),
                        'visible_depend' => ['enable_file' => ['show' => ['1']]]
                    )),

                    'attach_file_max_size' => new fieldNumber('file_max_size', array(
                        'title' => LANG_FORUM_FILE_MAX_SIZE,
                        'default' => 10,
                        'options' => array(
                            'units' => LANG_MB
                        ),
                        'rules' => array(
                            array('required')
                        ),
                        'visible_depend' => ['enable_file' => ['show' => ['1']]]
                    ))

                )

            ),

        );

        $options['seo'] = array(
            'type' => 'fieldset',
            'title' => LANG_ROOT_SEO,
            'childs' => array(

                new fieldString('seo_title', array(
                    'title' => LANG_SEO_TITLE,
                    'default' => LANG_FORUM_FORUMS
                )),

                new fieldString('seo_h1', array(
                    'title' => LANG_FORUM_OPTIONS_SEO_H1,
                    'default' => LANG_FORUM_FORUMS
                )),

                new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT
                )),

                new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT
                )),

            )
        );

        return $options;
    }

}
