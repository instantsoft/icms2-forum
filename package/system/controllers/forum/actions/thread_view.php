<?php
/**
 * Просмотр темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 * @property \cmsRequest $request
 */
class actionForumThreadView extends cmsAction {

    public function run() {

        $slug = $this->request->get('slug', '');
        if (!$slug) {
            return cmsCore::error404();
        }

        // Получаем данные по теме
        $thread = $this->model->getThreadByField($slug, 'slug');
        if (!$thread) {
            return cmsCore::error404();
        }

        // Избавляемся от точек в cms_core->uri_action
        if (strpos($this->cms_core->uri_action, '.html') !== false) {
            $this->cms_core->uri_action = 'thread-view-' . $thread['id'];
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Добавляем предыдущую и следующую темы раздела
        $thread = $this->model->appendThreadNav($thread);

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        $this->loadCatAccess($category['path']);

        if (!$this->cat_access->is_can_read) {
            return cmsCore::error404();
        }

        // добавляем Last-Modified
        if(!$this->cms_user->is_logged){
            $this->cms_core->response->setLastModified($thread['date_last_modified']);
        }

        // Загружаем доступы для темы
        $this->loadThreadAccess($thread);

        // Ограничиваем количество сообщений на странице
        $page    = $this->request->get('page', 1);
        $perpage = $this->options['perpage_posts'];

        // Скрываем удаленные сообщения
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        $this->model->filterEqual('thread_id', $thread['id']);

        // Общее число сообщений в теме
        $total = $this->model->getPostsCount();

        // Устанавливаем лимит на количество сообщений на странице
        $this->model->limitPage($page, $perpage);

        // Получаем список сообщений в теме
        $posts = $this->model->getPosts($this->getPostActions($thread));
        if (!$posts) {
            return cmsCore::error404();
        }

        // Выводим первое сообщение в теме на второй и следующих страницах
        if ($page > 1 && !empty($thread['fixed_first_post'])) {

            $this->model->filterEqual('thread_id', $thread['id']);

            $first_post = $this->model->filterEqual('is_first', 1)->getPosts();

            $posts = $first_post + $posts;
        }

        // Если пользователь не автор темы, обновляем количество просмотров
        if (!$thread['is_mythread']) {
            $this->model->incrementThreadHitsCounter($thread['id']);
        }

        // Рейтинг
        if ($this->isControllerEnabled('rating') && !empty($this->options['show_rating'])) {

            $rating_controller = cmsCore::getController('rating');

            $rating_controller->setContext($this->name, 'forum');

            // вызывать после установки контекста
            $rating_controller->loadCurrentUserVoted(array_keys($posts));

            $is_rating_allowed = cmsUser::isAllowed('forum', 'rate', true, true);

            foreach ($posts as $id => $post) {
                $is_rating_enabled = $is_rating_allowed && ($post['user_id'] != $this->cms_user->id) && empty($post['is_deleted']);

                $posts[$id]['info_bar']['rating'] = [
                    'html'  => $rating_controller->getWidget($post['id'], $post['rating'], $is_rating_enabled)
                ];
            }
        }

        // Получаем голосование темы
        $thread_poll = $this->model->getThreadPoll($thread['id'], $this->cms_user);

        // форма для быстрого ответа
        $form = null;

        if ($this->options['fast_answer'] && !$thread['is_closed'] && $this->thread_access->is_can_write) {
            $form = $this->getPostFormFields([
                'is_fixed'     => $this->thread_access->is_can_fixed,
                'is_closed'    => $this->thread_access->is_can_closed,
                'is_autoflood' => !empty($category['autoflood']),
                'is_attach'    => $this->thread_access->is_can_attach
            ]);
        }

        // Ставим метку о прочитанности темы
        if ($this->cms_user->id && $this->options['fix_threads_reads']) {
            $this->model->addThreadHit($thread['id'], $this->cms_user->id);
        }

        list($category, $thread, $thread_poll, $posts, $form) = cmsEventsManager::hook('forum_before_item', [$category, $thread, $thread_poll, $posts, $form]);

        cmsModel::cacheResult('current_forum_category', $category);
        cmsModel::cacheResult('current_forum_thread', $thread);

        // Подписки системные
        if (!empty($this->options['thread_enable_subscriptions']) && cmsController::enabled('subscriptions') && !$thread['is_closed']) {

            $toolbar_html = $this->controller_subscriptions->renderSubscribeButton(array(
                'controller' => 'forum',
                'subject'    => 'thread',
                'params'     => [
                    'field_filters' => [],
                    'filters' => [
                        [
                            'field'     => 'thread_id',
                            'condition' => 'eq',
                            'value'     => (string)$thread['id']
                        ]
                    ],
                    'dataset' => []
                ]
            ));

            if ($toolbar_html) {
                $this->cms_template->addToBlock('before_body', $toolbar_html);
            }
        }

        // Файл шаблона темы, выбранный в настройках форума
        $tpl = $this->options['tpl_threads'];

        // Файл шаблона темы, выбранный в настройках раздела
        if (!empty($category['options']['tpl_threads'])) {
            $tpl = $category['options']['tpl_threads'];
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        // Добавляем к глубиномеру название темы
        $this->cms_template->addBreadcrumb($thread['title']);

        // SEO параметры
        $this->applyItemSeo($thread, $category);

        return $this->cms_template->render($tpl, [
            'user'               => $this->cms_user,
            'num'                => (($page - 1) * $perpage + 1),
            'user_avatar_size'   => $this->model->post_user_avatar_size,
            'form'               => $form,
            'thread'             => $thread,
            'category'           => $category,
            'thread_poll'        => $thread_poll,
            'posts'              => $posts,
            'is_moder'           => $this->cat_access->is_moder,
            'thread_access'      => $this->thread_access,
            'is_can_thread_vip'  => cmsUser::isAllowed('forum', 'thread_vip'),
            'users_groups'       => !empty($this->options['show_users_groups']) ? $this->model_users->getGroups() : [],
            'page'               => $page,
            'perpage'            => $perpage,
            'total'              => $total,
            'tpl_posts'          => $this->options['tpl_posts'],
            'options'            => $this->options
        ]);
    }

    public function applyItemSeo($thread, $category) {

        $seo_desc = $seo_keys = $seo_title = $thread['title'];

        $meta_item = $thread;
        $meta_item['category'] = $category['title'];

        if(!empty($this->options['threads']['seo_title_pattern'])){

            $seo_title = $this->options['threads']['seo_title_pattern'];

            $this->cms_template->setPageTitleItem($meta_item);
        }

        if(!empty($this->options['threads']['seo_keys_pattern'])){

            $seo_keys = $this->options['threads']['seo_keys_pattern'];

            $this->cms_template->setPageKeywordsItem($meta_item);
        }

        if(!empty($this->options['threads']['seo_desc_pattern'])){

            $seo_desc = $this->options['threads']['seo_desc_pattern'];

            $this->cms_template->setPageDescriptionItem($meta_item);
        }

        $this->cms_template->setPageTitle($seo_title);
        $this->cms_template->setPageKeywords($seo_keys);
        $this->cms_template->setPageDescription($seo_desc);

        return [
            'meta_item' => $meta_item,
            'title_str' => $seo_title,
            'keys_str'  => $seo_keys,
            'desc_str'  => $seo_desc
        ];
    }

    private function getPostActions($thread) {

        if (!$this->cms_user->is_logged || !empty($thread['is_closed'])) {
            return [];
        }

        $actions = [
            [
                'title' => LANG_REPLY,
                'icon' => 'reply-all',
                'href'  => href_to('forum', 'post_add', [$thread['id'], '{id}']),
                'handler' => function($post, $num){
                    return !$post['is_deleted'] && ($this->cat_access->is_moder || $this->thread_access->is_can_write);
                }
            ],
            [
                'title' => LANG_EDIT,
                'icon' => 'edit',
                'class' => 'ajax-modal',
                'href'  => href_to('forum', 'post_edit', ['{id}']),
                'handler' => function($post, $num){
                    return !$post['is_deleted'] && ($this->cat_access->is_moder || $this->isPostCanEdit($post));
                }
            ],
            [
                'title' => LANG_FORUM_MOVE_POST,
                'class' => 'ajax-modal',
                'icon' => 'exchange-alt',
                'href'  => href_to('forum', 'post_move', ['{id}']),
                'handler' => function($post, $num){
                    return !$post['is_first'] && $num>1 && !$post['is_deleted'] && $this->cat_access->is_moder;
                }
            ],
            [
                'title' => LANG_HIDE,
                'icon' => 'eye-slash',
                'href'  => href_to('forum', 'post', ['hide', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && $num>1 && !$post['is_deleted'] && !$post['is_hidden'] && $this->cat_access->is_moder;
                }
            ],
            [
                'title' => LANG_FORUM_VIEW,
                'icon' => 'eye',
                'href'  => href_to('forum', 'post', ['view', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && $num>1 && !$post['is_deleted'] && $post['is_hidden'] && $this->cat_access->is_moder;
                }
            ],
            [
                'title' => LANG_FORUM_PIN,
                'icon' => 'thumbtack',
                'href'  => href_to('forum', 'post', ['pin', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && !$post['is_deleted'] && !$post['is_pinned'] && $this->cat_access->is_moder;
                }
            ],
            [
                'title' => LANG_FORUM_UNPIN,
                'icon' => 'thumbtack',
                'href'  => href_to('forum', 'post', ['unpin', '{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && !$post['is_deleted'] && $post['is_pinned'] && $this->cat_access->is_moder;
                }
            ],
            [
                'title' => LANG_FORUM_RESTORE_MESSAGE,
                'icon' => 'trash-restore',
                'confirm' => LANG_FORUM_CONFIRM_RESTORE_POST,
                'href'  => href_to('forum', 'post_restore', ['{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && $num>1 && $post['is_deleted'] && $this->cms_user->is_admin;
                }
            ],
            [
                'title' => LANG_FORUM_DELETE_MESSAGE,
                'icon' => 'trash',
                'confirm' => LANG_FORUM_CONFIRM_DELETE_POST,
                'href'  => href_to('forum', 'post_delete', ['{id}']).'?csrf_token='.cmsForm::getCSRFToken(),
                'handler' => function($post, $num){
                    return !$post['is_first'] && $num>1 && ($this->cat_access->is_moder || $this->isPostCanEdit($post));
                }
            ]
        ];

        list($thread, $actions) = cmsEventsManager::hook('forum_post_actions', [$thread, $actions]);

        return $actions;
    }
}
