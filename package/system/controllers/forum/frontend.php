<?php

class forum extends cmsFrontend {

    protected $useOptions = true;

    private $category_ns_left;
    private $category_ns_right;

    /**
     * Доступы к теме
     * @var object
     */
    public $thread_access;
    /**
     * Доступы к разделу форума
     * @var object
     */
    public $cat_access;

    public function __construct( cmsRequest $request){

        parent::__construct($request);

        if (!empty($this->options['user_fields'])) {
            $this->model->post_user_fields = $this->options['user_fields'];
        }

        $this->cat_access = (object)[
            'is_moder'    => false,
            'is_set_read_access' => false,
            'is_can_read' => false,
            'is_can_add'  => false
        ];

        $this->thread_access = (object)[
            'post_add_karma'      => false,
            'thread_add_karma'    => false,
            'is_author'           => false,
            'is_can_send_invite'  => false,
            'is_can_poll_add'     => false,
            'is_can_poll_delete'  => false,
            'is_can_closed'       => false,
            'is_can_open'         => false,
            'is_can_delete'       => false,
            'is_can_fixed'        => false,
            'is_can_attach'       => false,
            'is_can_thread_add'   => false,
            'is_can_thread_edit'  => false,
            'is_can_thread_write' => false,
            'is_can_thread_vip'   => false
        ];
    }

    public function route($uri) {

        $action_name = $this->parseRoute($this->cms_core->uri);

        if (!$action_name) { return cmsCore::error404(); }

        $this->runAction($action_name);
    }

    public function loadThreadAccess($thread){

        $is_thread_karma_allowed = true;
        if (cmsUser::getPermissionValue('forum', 'thread_add_karma')) {
            if (!cmsUser::isPermittedLimitReached('forum', 'thread_add_karma', $this->cms_user->karma)) {
                $is_thread_karma_allowed = false;
                $this->thread_access->thread_add_karma = cmsUser::getPermissionValue('forum', 'thread_add_karma');
            }
        }

        // Автор темы
        $this->thread_access->is_author = $thread['user_id'] == $this->cms_user->id;

        // Может создавать тему
        $this->thread_access->is_can_thread_add = $this->cat_access->is_moder ||
                ($this->cat_access->is_can_add && cmsUser::isAllowed('forum', 'thread_add') && $is_thread_karma_allowed);

        // Может редактировать эту тему
        $this->thread_access->is_can_thread_edit = $this->cat_access->is_moder ||
                ($this->cat_access->is_can_add && (cmsUser::isAllowed('forum', 'thread_edit', 'all') ||
                (cmsUser::isAllowed('forum', 'thread_edit', 'own') && $this->thread_access->is_author && empty($thread['is_closed']))));

        $is_karma_allowed = true;
        if (cmsUser::getPermissionValue('forum', 'post_add_karma')) {
            if (!cmsUser::isPermittedLimitReached('forum', 'post_add_karma', $this->cms_user->karma)) {
                $is_karma_allowed = false;
                $this->thread_access->post_add_karma = cmsUser::getPermissionValue('forum', 'post_add_karma');
            }
        }

        // Может писать сообщения в этой теме
        $this->thread_access->is_can_write = empty($thread['is_closed']) && ($this->cat_access->is_moder ||
                ($this->cat_access->is_can_add && cmsUser::isAllowed('forum', 'post_add') && $is_karma_allowed));

        // Может прикреплять опросы
        $this->thread_access->is_can_poll_add = $this->cat_access->is_moder || cmsUser::isAllowed('forum', 'poll_add');
        // Может удалять опросы
        $this->thread_access->is_can_poll_delete = $this->cat_access->is_moder || $this->thread_access->is_author;

        // Может прикреплять файлы к сообщениям
        $this->thread_access->is_can_attach = ($this->cat_access->is_moder || cmsUser::isAllowed('forum', 'attach_add')) && $this->options['enable_file'];

        // Может ставить метку "решено"
        $this->thread_access->is_can_fixed = empty($thread['is_closed']) &&
                ($this->cat_access->is_moder || $this->thread_access->is_author);

        // Может выделять тему
        $this->thread_access->is_can_thread_vip = $this->cat_access->is_moder || cmsUser::isAllowed('forum', 'thread_vip');

        // Может удалять тему
        $this->thread_access->is_can_delete = $this->cat_access->is_moder || cmsUser::isAllowed('forum', 'thread_delete', 'all') || (cmsUser::isAllowed('forum', 'thread_delete', 'own') && $this->thread_access->is_author);

        // Может закрывать тему
        $this->thread_access->is_can_closed = empty($thread['is_closed']) && ($this->cat_access->is_moder || cmsUser::isAllowed('forum', 'thread_close', 'all') || ($this->thread_access->is_author && cmsUser::isAllowed('forum', 'thread_close', 'own')));
        // Может открывать тему
        $this->thread_access->is_can_open = !empty($thread['is_closed']) && ($this->cat_access->is_moder || cmsUser::isAllowed('forum', 'thread_open', 'all') || ($this->thread_access->is_author && cmsUser::isAllowed('forum', 'thread_open', 'own')));

        // Может отправлять приглашения в тему
        $this->thread_access->is_can_send_invite =
                $this->cat_access->is_moder ||
                ($this->cat_access->is_can_add && $this->thread_access->is_author && cmsUser::isAllowed('forum', 'send_invite'));

        return $this;
    }

    public function loadCatAccess($categories){

        if (!$categories){ return $this; }

        // Проверяем доступы к разделу и его "родителям"
        // перебор от родителей к детям
        foreach ($categories as $category) {

            // Заданы ограничения
            $this->cat_access->is_set_read_access = !empty($category['groups_read']);

            // Если не модератор категории, то проверяем дочерние разделы
            if(!$this->cat_access->is_moder){
                $this->cat_access->is_moder = $this->isModerator($category['moderators']);
            }

            // Если не модератор и раздел не публикуется
            if (!$this->cat_access->is_moder && empty($category['is_pub'])){
                $this->cat_access->is_can_read = false;
                $this->cat_access->is_can_add = false;
            }

            // Может просматривать раздел
            $this->cat_access->is_can_read = $this->cms_user->isInGroups($category['groups_read']);

            // Может участвовать в разделе
            $this->cat_access->is_can_add = empty($category['as_folder']) && $this->cms_user->isInGroups($category['groups_edit']);
        }

        return $this;
    }

    public function isPostCanEdit($post) {

        $is_author = $post['user_id'] == $this->cms_user->id;

        $is_time_left = $this->model->checkEditTime($post['date_pub']);

        $is_can_post_edit = (cmsUser::isAllowed('forum', 'post_edit', 'all') ||
                (cmsUser::isAllowed('forum', 'post_edit', 'own') && $is_author));

        return $is_can_post_edit && $is_time_left;
    }

    public function getDatasets(){

        $datasets = [];

        if(!empty($this->options['show_ds_menu_index'])){
            $datasets['index'] = [
                'name' => 'index',
                'title' => !empty($this->options['menu_index_title']) ? $this->options['menu_index_title'] : LANG_FORUM_FORUMS_ACTIVITY
            ];
        }

        $datasets['all'] = [
            'name' => 'all',
            'title' => empty($this->options['show_ds_menu_index']) ? LANG_ALL : LANG_FORUM_FORUMS_ALL
        ];

        if($this->cms_user->is_logged){

            if(!empty($this->options['show_ds_menu_mythr'])){
                $datasets['my_threads'] = [
                    'name' => 'my_threads',
                    'icon'  => 'address-book',
                    'title' => LANG_FORUM_MY_THREADS
                ];
            }
            if(!empty($this->options['show_ds_menu_myp'])){
                $datasets['my_posts'] = [
                    'name' => 'my_posts',
                    'icon'  => 'address-card',
                    'title' => LANG_FORUM_MY_POSTS
                ];
            }
        }

        if(!empty($this->options['show_ds_menu_lthr'])){
            $datasets['latest_threads'] = [
                'name' => 'latest_threads',
                'icon'  => 'newspaper',
                'title' => LANG_FORUM_NEW_THREADS
            ];
        }
        if(!empty($this->options['show_ds_menu_lp'])){
            $datasets['latest_posts'] = [
                'name' => 'latest_posts',
                'icon'  => 'fist-raised',
                'title' => LANG_FORUM_LATEST_POSTS
            ];
        }

        if ($this->cms_user->is_admin) {
            $datasets['deleted_threads'] = [
                'name' => 'deleted_threads',
                'icon'  => 'trash-alt',
                'title' => LANG_FORUM_DEL_THREADS
            ];
        }

        return cmsEventsManager::hook('forum_datasets', $datasets);
    }

    /*
     * Коллбэк проверки прав доступа к разделам
     *
     * @param boolean $is_inversion Если true, то вернёт недоступные разделы
     * @return function
     */
    public function getChildsAccessCallback($is_inversion = false) {

        $this->category_ns_left = null;
        $this->category_ns_right = null;

        return function ($item, $model) use($is_inversion) {

            // Если не модератор данной категории, то проверяем родительские разделы
            $is_moder = $this->isModerator($item['moderators']);

            // Если не модератор и раздел не публикуется - скрываем его
            if (!$is_moder && !$item['is_pub']) {
                return $is_inversion ? $item : false;
            }

            // Пропускаем подразделы у недоступных разделов
            if ($this->category_ns_left && $this->category_ns_right) {
                if ($item['ns_left'] > $this->category_ns_left && $item['ns_right'] < $this->category_ns_right) {
                    return $is_inversion ? $item : false;
                }
            }

            $is_can_read = $this->cms_user->isInGroups($item['groups_read']) || $is_moder;
            if (!$is_can_read) {
                $this->category_ns_left  = $item['ns_left'];
                $this->category_ns_right = $item['ns_right'];
                return $is_inversion ? $item : false;
            }

            return $is_inversion ? false : $item;
        };
    }

    /*
     * Проверка пользователя на наличие в списке модераторов
     * @param array $moderators - список id пользователей, кто может быть модератором
     * @return bool
     */
    public function isModerator($moderators = []){

        // Неавторизованный пользователь не может быть модератором
        if (!$this->cms_user->id){ return false; }

        // Группа пользователя может модерировать все форумы
        if (cmsUser::isAllowed('forum', 'is_moderator')){ return true; }

        if(is_array($moderators)){
            $moderators = array_filter($moderators);

            // Пользователь в списке указанных модераторов раздела
            if ($moderators){
                if (in_array($this->cms_user->id, $moderators)){ return true; }
            }
        }

        return false;
    }

    /*
     * Разбивка подразделов на уровни
     * @param array $categories - все дочерние категории
     * @return array
     */
    public function getChildsLevels($categories){

        if (!$categories){ return []; }

        $cats_list   = [];
        $slug        = $this->request->get('slug', '');
        $first_level = false;

        foreach ($categories as $category){

            // Уровень первого элемента
            $first_level = $first_level ? $first_level : $category['ns_level'];

            // Формируем корневой уровень
            if($category['ns_level'] == $first_level || ($category['ns_level'] == ($first_level+1) && !$slug)){

            // Формируем разделы
            $cats_list[] = $category;

            } elseif ($category['ns_level'] == ($first_level+1) || ($category['ns_level'] == ($first_level+2) && !$slug)) {

                // Формируем подразделы второго уровня
                $k = array_keys($cats_list);
                $cats_list[end($k)]['sub_cats'][] = $category;

            } elseif($category['ns_level'] == ($first_level+2)) {

                $k = array_keys($cats_list);

                // Формируем подразделы третьего уровня
                $cats_list[end($k)]['sub_forums_cats'][] = $category;
            }

        }

        return $cats_list;
    }

    /*
     * Отправка уведомлений пользователям
     * согласно настроек в их профиле
     * @param array $users - список пользователей
     * @param array $thread - тема
     * @return int
     */
    public function notifyInvitedUsers($users, $thread){

        $subscribers = array();

        // Ставим ярлык отправки уведомлений из этой темы
        foreach ($users as $id) {

            $get_ups = cmsUser::getUPS('forum.send_invite.threads', $id);

            if (is_array($get_ups) && in_array($thread['id'], $get_ups)){ continue; }

            $subscribers[] = $id;

            $get_ups = is_array($get_ups) ?
                array_merge($get_ups, array($thread['id'])) :
                array($thread['id']);

            cmsUser::setUPS('forum.send_invite.threads', $get_ups, $id);

        }

        // Проверяем, наличие кого-либо в списке
        if (!$subscribers) { return count($users); }

        // Добавляем новых получателей в рассылке уведомлений
        $this->controller_messages->addRecipients($subscribers);

        // Отправляем уведомления в личных сообщениях
        $this->controller_messages->sendNoticePM(array(

            'content' => sprintf(LANG_FORUM_NOTIFY_THREAD_INVITE_NEW, $thread['title']),
            'options' => array(
                'is_closeable' => false
            ),
            'actions' => array(
                'view' => array(
                    'title' => LANG_SHOW,
                    'href' => href_to_abs('forum', $thread['slug'] . '.html')
                )
            )
        ), 'forum_invite_thread');

        // Отправляем уведомления на email
        $this->controller_messages->sendNoticeEmail('forum_invite_thread', array(
            'page_url' => href_to_abs('forum', $thread['slug'] . '.html'),
            'page_title' => $thread['title'],
            'author_url' => href_to_abs('users', $thread['user_id'])
        ));

        return count($users);
    }

    /*
     * Возвращает HTML списка сообщений
     *
     * @param string $page_url Ссылка на страницу
     * @return string
     */
    public function renderForumPostsList($page_url = null){

        $page = $this->request->get('page', 1);
        $perpage = $this->options['perpage_posts'];

        // Считаем общее количество сообщений до присоединения других таблиц
        // в целях увеличения производительности
        $total = !empty($this->count) ? $this->count : $this->model->getPostsCount();

        $this->model->orderBy('date_pub', 'desc');
        //$this->model->forceIndex('date_pub', 2);

        $this->model->select('t.title', 'title');
        $this->model->select('t.slug', 'thread_slug');
        $this->model->select('t.is_fixed', 'is_fixed');
        $this->model->select('t.is_closed', 'is_closed');

        $this->model->joinLeft('forum_threads', 't', 't.id = i.thread_id');

        // Скрываем удаленные сообщения
        if (!$this->cms_user->is_admin) { $this->model->filterIsNull('is_deleted'); }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        $posts = $this->model->getPosts();

        // если запрос через URL
        if($this->request->isStandard()){
            if(!$posts && $page > 1){ cmsCore::error404(); }
        }

        // Рейтинг
        if ($this->isControllerEnabled('rating') && !empty($this->options['show_rating'])) {

            $rating_controller = cmsCore::getController('rating');

            $rating_controller->setContext($this->name, 'forum');

            foreach ($posts as $id => $post) {
                // Просто показываем рейтинг, без возможности голосовать
                $posts[$id]['info_bar']['rating'] = [
                    'html'  => $rating_controller->getWidget($post['id'], $post['rating'], false)
                ];
            }
        }

        $posts = cmsEventsManager::hook('forum_posts_before_list', $posts);

        return $this->cms_template->renderInternal($this, 'list_index', [
            'options'          => $this->options,
            'user_avatar_size' => $this->model->post_user_avatar_size,
            'users_groups'     => !empty($this->options['show_users_groups']) ? $this->model_users->getGroups() : [],
            'page_url'         => $page_url,
            'page'             => $page,
            'perpage'          => $perpage,
            'total'            => $total,
            'posts'            => $posts,
            'user'             => $this->cms_user
        ]);
    }

    public function getPostFormFields($options = [], $return_form = true) {

        $options = array_merge([
            'is_fixed'     => false,
            'is_closed'    => false,
            'is_autoflood' => false,
            'is_attach'    => false
        ], $options);

        $fields = [
            new fieldHtml('content', [
                'title' => LANG_FORUM_FIRST_POST,
                'options' => [
                    'editor'              => $this->options['editor'],
                    'editor_options'      => ['id' => 'content'],
                    'editor_presets'      => !empty($this->options['editor_presets']) ? $this->options['editor_presets'] : [],
                    'is_html_filter'      => !empty($this->options['is_html_filter']),
                    'build_redirect_link' => !empty($this->options['build_redirect_link']),
                    'is_auto_br'          => false
                ],
                'rules' => [
                    ['required']
                ]
            ])
        ];

        // Пользователь может ставить метку "Решено"
        if (!empty($options['is_fixed'])) {
            $fields[] = new fieldCheckbox('is_fixed', [
                'title' => LANG_FORUM_TOPIC_FIXED_LABEL
            ]);
        }

        // Пользователь может закрыть тему
        if (!empty($options['is_closed'])) {
            $fields[] = new fieldCheckbox('is_closed', [
                'title' => LANG_FORUM_TOPIC_CLOSED_LABEL
            ]);
        }

        // Пользователь может выбрать срок публикации сообщения в качестве флуда
        if (!empty($options['is_autoflood'])) {
            $fields[] = new fieldList('flood_type', [
                'items' => [
                    ''   => LANG_FORUM_IS_NOT_FLOOD,
                    '1'  => LANG_FORUM_IS_FLOOD_1,
                    '3'  => LANG_FORUM_IS_FLOOD_3,
                    '6'  => LANG_FORUM_IS_FLOOD_6,
                    '12' => LANG_FORUM_IS_FLOOD_12,
                    '24' => LANG_FORUM_IS_FLOOD_24,
                ]
            ]);
        }

        // Пользователь может прикрепить файл к сообщению
        if (!empty($options['is_attach'])) {
            $fields[] = new fieldFile('files', [
                'title' => LANG_FORUM_ATTACH_FILES,
                'options' => [
                    'show_name'    => false,
                    'extensions'   => $this->options['file_ext'],
                    'max_size_mb'  => $this->options['file_max_size'],
                    'show_size'    => true,
                    'show_counter' => true
                ]
            ]);
        }

        if(!$return_form){
            return $fields;
        }

        $form = new cmsForm();

        $form->addFieldset('', 'basic');

        foreach ($fields as $field) {
            $form->addField('basic', $field);
        }

        return $form;
    }

    public function getThreadPollFormFieldsets($options = []) {

        $options = array_merge([
            'do'     => 'add',
            'poll'   => [],
            'poll_counts' => $this->options['poll_counts']
        ], $options);

        $fieldsets = [
            'thread_poll' => [
                'type'  => 'fieldset',
                'is_collapsed' => !$options['poll'],
                'title' => LANG_FORUM_ATTACH_POLL,
                'childs' => [
                    new fieldString('poll:title', [
                        'title' => LANG_FORUM_QUESTION,
                        'options' => [
                            'max_length' => 100,
                            'min_length' => 3
                        ]
                    ]),
                    new fieldString('poll:description', [
                        'title' => LANG_FORUM_COMMENT_FOR_POLL,
                        'options' => [
                            'max_length' => 255
                        ]
                    ]),
                    new fieldDate('poll:date_pub_end', [
                        'title' => LANG_FORUM_LENGTH_POLL,
                        'options' => [
                            'show_time' => true
                        ]
                    ]),
                    new fieldList('poll:options:result', [
                        'title' => LANG_FORUM_SHOW_RESULT,
                        'items' => [
                            0 => LANG_FORUM_FOR_ALL_EVER,
                            1 => LANG_FORUM_ONLY_VOTERS,
                            2 => LANG_FORUM_ONLY_END_POLL
                        ]
                    ]),
                    new fieldCheckbox('poll:options:change', [
                        'title' => LANG_FORUM_CHANGE_VOTE_USER
                    ]),
                    new fieldCheckbox('poll:options:answers_is_pub', [
                        'title' => LANG_FORUM_POLL_ANSWERS_IS_PUB
                    ])
                ]
            ]
        ];

        // Для созданного опроса нельзя включить множественный выбор
        if($options['do'] == 'add'){
            $fieldsets['thread_poll']['childs'][] = new fieldCheckbox('poll:options:multi_answer', [
                'title' => LANG_FORUM_POLL_MULTI_ANSWER
            ]);
        } else {
            $fieldsets['thread_poll']['childs'][] = new fieldHidden('poll:options:multi_answer');
        }

        for ($poll_id = 1; $poll_id <= $options['poll_counts']; $poll_id++) {
            $fieldsets['thread_poll']['childs'][] = new fieldString('poll:answers:'.$poll_id, [
                'title' => $poll_id == 1 ? LANG_FORUM_OPTIONS_ANSWER : null,
                'attributes' => [
                    'placeholder' => LANG_FORUM_OPTION.' №'.$poll_id
                ],
                'rules' => $poll_id == 2 ? [
                    [function($controller, $data, $value) {
                        // Убираем незаполненные поля
                        $data['poll']['answers'] = array_filter($data['poll']['answers']);

                        if($data['poll']['answers'] && count($data['poll']['answers']) < 2){
                            return ERR_VALIDATE_REQUIRED;
                        }
                        return true;
                    }]
                ] : []
            ]);
        }

        return $fieldsets;
    }

    public function renderIndex() {

        // Получаем список разделов отсортированных по уровням вложенности
        // и проверяем пользователя на доступ к просмотру их
        $category = $this->model->getCategoryChilds(['id' => 1], $this->options['cats_level_view'], $this->getChildsAccessCallback());

        // Разбиваем подразделы на уровни
        $cats_list = $this->getChildsLevels($category);

        // Используем rss
        $is_rss = false;

        if (!empty($this->options['is_rss'])) {
            $is_rss = cmsController::enabled('rss') && $this->model->db->getField('rss_feeds', 'ctype_name = "forum"', 'is_enabled') ? true : false;
        }

        $datasets = $this->getDatasets();

        $dataset_name = 'all';

        $dataset = isset($datasets[$dataset_name]) ? $datasets[$dataset_name] : [];

        // Передаем данные в выбранный шаблон
        return $this->cms_template->render($this->options['tpl_index'], [
            'base_ds_url'  => href_to($this->name) . '%s',
            'datasets'     => $datasets,
            'dataset_name' => $dataset_name,
            'dataset'      => $dataset,
            'user'         => $this->cms_user,
            'cats_list'    => $cats_list,
            'is_rss'       => $is_rss,
            'options'      => $this->options
        ]);
    }
}
