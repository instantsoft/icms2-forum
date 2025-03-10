<?php

class modelForum extends cmsModel {

    use icms\traits\controllers\models\transactable;

    public $table_prefix = '';

    /**
     * Поля пользователей в постах
     * @var array
     */
    private $user_fields = [];
    private $user_fields_select = null;
    public $post_user_fields = [];
    public $post_user_avatar_size = '';

    /*
     * Получение префикса заголовка темы
     *
     * @param array $thread Данные темы
     * @return string
     */
    public static function getThreadTitleWithPrefix($thread) {

        $title = $thread['title'];

        if (!empty($thread['is_fixed'])) {
            $title = '['.LANG_FORUM_TOPIC_FIXED_PREFIX . '] ' . $title;
        } else if (!empty($thread['is_closed'])) {
            $title = '['.LANG_FORUM_TOPIC_CLOSED_PREFIX . '] ' . $title;
        }
        if (!empty($thread['is_deleted'])) {
            $title = '['.LANG_FORUM_TOPIC_DELETED_PREFIX . '] ' . $title;
        }

        return $title;
    }

    public static function getThreadBadges($thread) {

        $badges = [];

        if (!empty($thread['is_fixed'])) {
            $badges['success'] = LANG_FORUM_TOPIC_FIXED_PREFIX;
        }
        if (!empty($thread['is_closed'])) {
            $badges['secondary'] = LANG_FORUM_TOPIC_CLOSED_PREFIX;
        }
        if (!empty($thread['is_deleted'])) {
            $badges['danger'] = LANG_FORUM_TOPIC_DELETED_PREFIX;
        }
        if (!empty($thread['is_pinned'])) {
            $badges['warning'] = LANG_FORUM_THREAD_IS_PINNED;
        }
        if (!empty($thread['is_vip'])) {
            $badges['info'] = LANG_FORUM_THREAD_VIP;
        }

        return $badges;
    }

    /*
     * Проверка доступного времени на редактирование
     *
     * @param string $date_pub Дата публикации
     * @return integer
     */
    public function checkEditTime($date_pub) {

        $edit_time = cmsUser::getPermissionValue('forum', 'post_edit_time');

        if (!$edit_time) { return true; }

        return ($edit_time - round((time() - strtotime($date_pub)) / 60)) > 0;
    }

    /**
     * Увеличивает счётчик просмотров
     *
     * @param integer $id
     * @return boolean
     */
	public function incrementThreadHitsCounter($id){

        cmsCache::getInstance()->clean('forum.threads');

		return $this->filterEqual('id', $id)->increment('forum_threads', 'hits');
	}

    /**
     * Добавляет/обновляет информацию о просмотре темы
     *
     * @param integer $thread_id
     * @param integer $user_id
     * @return mixed
     */
	public function addThreadHit($thread_id, $user_id){
        return $this->insertOrUpdate('forum_threads_hits', ['user_id' => $user_id, 'thread_id' => $thread_id], ['date_pub' => null]);
	}

    /*
     * Все дочерние разделы, с учётом уровня вложенности
     *
     * @param array $category Выбранная категория
     * @param int $level Уровень вложенности разделов
     * @param function $item_callback Коллбэк
     * @return array
     */
    public function getCategoryChilds($category = ['id' => 1], $level = false, $item_callback = false) {

        if (!isset($category['ns_left'])) {
            $category = $this->getCategoryByField($category['id']);
        }

        if ($level) {
            $this->filterLtEqual('ns_level', ($category['ns_level'] + $level))->filterGt('ns_level', 0);
        }

        $this->filterGt('ns_left', $category['ns_left'])->
                filterLt('ns_right', $category['ns_right']);

        $this->orderBy('ns_left');

        return $this->getCategories($item_callback);
    }

    /*
     * Данные по разделу
     *
     * @param string $key_value - значение поля в базе данных
     * @param string $key_field - наименования поля в базе данных
     * @return array
     */
    public function getCategoryByField($key_value, $key_field = 'id') {
        return $this->getCategory('forum', $key_value, $key_field, [
            'icon', 'last_post', 'moderators', 'options',
            'groups_read', 'groups_edit'
        ]);
    }

    /*
     * Список разделов
     *
     * @param function $item_callback - функция обработки данных
     * @param string $key_field - наименования поля в базе данных
     * @return array
     */
    public function getCategories($item_callback = false, $key_field = 'id') {

        $this->useCache('forum.categories');

        return $this->get('forum_cats', function ($item, $model) use ($item_callback) {

            $item['last_post']   = cmsModel::yamlToArray($item['last_post']);
            $item['moderators']  = cmsModel::yamlToArray($item['moderators']);
            $item['options']     = cmsModel::yamlToArray($item['options']);
            $item['groups_read'] = cmsModel::yamlToArray($item['groups_read']);
            $item['groups_edit'] = cmsModel::yamlToArray($item['groups_edit']);

            if (is_callable($item_callback)) {
                $item = call_user_func_array($item_callback, array($item, $model));
                if ($item === false) {
                    return false;
                }
            }

            return $item;

        }, $key_field) ?: [];
    }

    /*
     * Количество тем в разделе
     * @param array $category - выбранная категория
     * @param bool $is_recursive - с учётом дочерних разделов
     * @return int
     */
    public function getCategoryThreadsCount($category, $is_recursive = false) {

        if ($is_recursive){

            $this->filterGt('c.parent_id', 0);

            $this->filterGtEqual('c.ns_left', $category['ns_left']);
            $this->filterLtEqual('c.ns_right', $category['ns_right']);

            $this->joinLeft('forum_cats', 'c', 'c.id = i.category_id');

        } else {

            $this->filterEqual('category_id', $category['id']);

        }

        $this->filterIsNull('is_deleted');

        return $this->getCount('forum_threads');
    }

    public function getThreadsCount() {
        return $this->getCount('forum_threads');
    }

    /*
     * Список тем
     * @param bool $is_reads_view - подсветка просмотренных тем
     * @param function $item_callback - функция обработки данных
     * @return array
     */
    public function getThreads($is_reads_view = false, $item_callback = false) {

        $user = cmsUser::getInstance();

        $user_date_log = strtotime(cmsUser::sessionGet('user:date_log'));

        $threads_read = [];

        if ($is_reads_view) {

            $this->select('th.date_pub', 'date_thread_read');

            $this->joinLeft('forum_threads_hits', 'th', "th.thread_id = i.id AND th.user_id = '{$user->id}'");
        } else {

            $threads_read = cmsUser::isSessionSet('threads_read') ? cmsUser::sessionGet('threads_read') : array();
        }

        $this->joinUser();

        $this->joinSessionsOnline();

        $this->useCache('forum.threads');

        return $this->get('forum_threads', function ($item, $model) use ($is_reads_view, $user_date_log, $threads_read, $user, $item_callback) {

            $item['title']     = modelForum::getThreadTitleWithPrefix($item);
            $item['last_post'] = cmsModel::yamlToArray($item['last_post']);
            $item['answers']   = !empty($item['posts_count']) ? $item['posts_count'] - 1 : 0;

            $item['user'] = array(
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => cmsModel::yamlToArray($item['user_avatar'])
            );

            if ($user->is_logged && !$item['is_deleted']) {

                if ($is_reads_view) {

                    $item['is_new'] = empty($item['date_thread_read']) && $is_reads_view && $user_date_log ? true : false;
                } else {

                    $item['is_new'] = $user_date_log && (strtotime($item['date_last_modified']) >= $user_date_log) ? true : false;

                    if ($item['is_new'] && array_key_exists('t' . $item['id'], $threads_read)) {
                        $item['is_new'] = $threads_read['t' . $item['id']] <= strtotime($item['date_last_modified']) ? true : false;
                    }
                }
            } else {
                $item['is_new'] = false;
            }

            if (is_callable($item_callback)) {
                $item = call_user_func_array($item_callback, array($item, $model));
                if ($item === false) {
                    return false;
                }
            }

            return $item;
        });
    }

    /*
     * Данные по теме
     * @param string $key_value - значение поля в базе данных
     * @param string $key_field - наименования поля в базе данных
     * @param function $item_callback - функция обработки данных
     * @return array
     */
    public function getThreadByField($key_value, $key_field = 'id', $item_callback = false) {

        $user = cmsUser::getInstance();

        $this->useCache('forum.threads');

        return $this->getItemByField('forum_threads', $key_field, $key_value, function ($item, $model) use ($user, $item_callback) {

            $item['badges']      = modelForum::getThreadBadges($item);
            $item['last_post']   = cmsModel::yamlToArray($item['last_post']);
            $item['is_mythread'] = $user->is_logged ? (bool) ($item['user_id'] == $user->id) : false;
            $item['answers']     = !empty($item['posts_count']) ? $item['posts_count'] - 1 : 0;

            if (!empty($item['from_cat'])) {
                $item['from_cat'] = $model->getCategoryByField($item['from_cat']);
            }

            if (is_callable($item_callback)) {
                $item = call_user_func_array($item_callback, array($item, $model));
                if ($item === false) {
                    return false;
                }
            }

            return $item;
        }, $key_field);
    }

    /*
     * Предыдущая и следующая темы
     * @param array $thread - текущая тема
     * @return array
     */
    public function appendThreadNav($thread) {

        $this->filterIsNull('is_deleted');

        $thread['prev_thread'] = $this->
            selectOnly('slug')->select('id')->select('title')->
            filterLt('id', $thread['id'])->
            filterEqual('category_id', $thread['category_id'])->
            orderBy('id', 'desc')->
            getItem('forum_threads', function($item){
                $item['title'] = modelForum::getThreadTitleWithPrefix($item);
                return $item;
            });

        $this->filterIsNull('is_deleted');

        $thread['next_thread'] = $this->
            selectOnly('slug')->select('id')->select('title')->
            filterGt('id', $thread['id'])->
            filterEqual('category_id', $thread['category_id'])->
            orderBy('id', 'asc')->
            getItem('forum_threads', function($item){
                $item['title'] = modelForum::getThreadTitleWithPrefix($item);
                return $item;
            });

        return $thread;
    }

    /*
     * Количество сообщений в теме
     *
     * @param int $thread_id ID темы
     * @return int
     */
    public function getPostsCount($thread_id = false) {

        if ($thread_id){ $this->filterEqual('thread_id', $thread_id); }

        return $this->getCount('forum_posts');
    }

    /**
     * Сообщения в теме
     *
     * @param array $actions Массив меню действий поста
     * @return array
     */
    public function getPosts($actions = false) {

        if(!$this->order_by){
            $this->orderByList([
                [
                    'by' => 'i.is_pinned',
                    'to' => 'desc'
                ],
                [
                    'by' => 'i.date_pub',
                    'to' => 'asc'
                ]
            ]);
        }

        $user_fields = $this->getPostsUserSelectFields();

        $this->joinUserLeft('user_id', $user_fields);

        $this->joinSessionsOnline();

        $this->useCache('forum.posts');

        $num = strpos($this->limit, '0,') === 0 ? 1 : 2;

        return $this->get('forum_posts', function($item, $model) use($actions, &$num) {

            if(!empty($item['title'])){
                $item['title'] = modelForum::getThreadTitleWithPrefix($item);
            }

            $item['user_groups'] = cmsModel::yamlToArray($item['user_groups']);

            // Данные пользователя
            $item['user'] = [
                'id' => $item['user_id'],
                'is_online' => $item['is_online']
            ];
            foreach (array_values($this->user_fields_select) as $field_name) {
                $item['user'][str_replace('user_', '', $field_name)] = $item[$field_name];
                unset($item[$field_name]);
            }

            // Поля, если есть
            $item['user_fields'] = [];
            if($this->user_fields){
                foreach ($this->user_fields as $field) {
                    if (empty($item['user'][$field['name']])) {
                        continue;
                    }
                    $uvalue = $field['handler']->setItem($item['user'])->getStringValue($item['user'][$field['name']]);
                    if($uvalue){
                        $item['user_fields'][$field['title']] = $uvalue;
                    }
                }
            }

            $item['files'] = cmsModel::yamlToArray($item['files']);
            if($item['files']){
                $ext = pathinfo($item['files']['name'], PATHINFO_EXTENSION);
                $item['files']['icon'] = 'download';
                if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])){
                    $item['files']['icon'] = 'image';
                }
                if(in_array($ext, ['zip', 'rar', 'gzip', '7z', 'tar', 'tgz'])){
                    $item['files']['icon'] = 'archive';
                }
                if(in_array($ext, ['doc', 'docx'])){
                    $item['files']['icon'] = 'word';
                }
                if(in_array($ext, ['xls', 'xlsx'])){
                    $item['files']['icon'] = 'excel';
                }
                if($ext == 'pdf'){
                    $item['files']['icon'] = 'pdf';
                }
            }

            if (!empty($item['from_thread_id'])){

                $from_thread = $model->getThreadByField($item['from_thread_id']);

                // Предварительно удаленные темы, доступны только администраторам сайта
                if (!empty($from_thread['is_deleted']) && !cmsUser::isAdmin()){ $from_thread = []; }

                if ($from_thread){

                    $item['from_thread_slug'] = $from_thread['slug'];
                    $item['from_thread_title'] = $from_thread['title'];

                } else {

                    $item['from_thread'] = null;
                    $model->updatePost($item['id'], array( 'from_thread' => null ));
                }
            }

            $item['info_bar'] = [];
            $item['actions'] = [];

            if (is_array($actions)){
                foreach($actions as $key => $action){

                    if (isset($action['handler'])){
                        $is_active = $action['handler']($item, $num);
                    } else {
                        $is_active = true;
                    }

                    if (!$is_active){ continue; }

                    if(empty($action['href'])){ continue; }

                    foreach($item as $cell_id => $cell_value){

                        if (is_null($cell_value) || is_array($cell_value) || is_object($cell_value)) { continue; }

                        $action['href']  = str_replace('{'.$cell_id.'}', $cell_value, $action['href']);
                        $action['title'] = str_replace('{'.$cell_id.'}', $cell_value, $action['title']);
                        $action['class'] = (isset($action['class']) ? $action['class'] : '');

                    }
                    $item['actions'][$key] = $action;
                }
            }

            $num++;

            return $item;
        }) ?: [];
    }

    /*
     * Поля пользователей в постах темы форума
     * @return array
     */
    public function getPostsUserSelectFields() {
        return $this->loadUserFields()->user_fields_select;
    }
    public function getPostsUserFields() {
        return $this->loadUserFields()->user_fields;
    }

    /**
     * Загружает дополнительные поля юзеров в постах
     *
     * @return $this
     */
    private function loadUserFields() {

        if($this->user_fields_select !== null){
            return $this;
        }

        $this->user_fields_select = [
            'u.nickname'     => 'user_nickname',
            'u.avatar'       => 'user_avatar',
            'u.slug'         => 'user_slug',
            'u.groups'       => 'user_groups',
            'u.is_admin'     => 'user_is_admin',
            'u.is_locked'    => 'user_is_locked',
            'u.karma'        => 'user_karma',
            'u.date_log'     => 'user_date_log',
            'u.forum_sign'   => 'user_forum_sign',
            'u.forum_posts_count' => 'user_forum_posts_count'
        ];

        $post_user_fields = $this->post_user_fields;
        $post_user_fields[] = 'avatar';

        $this->user_fields = cmsCore::getModel('content')->
                setTablePrefix('')->getContentFields('{users}', false, true, $post_user_fields);

        if($this->user_fields){
            foreach ($this->user_fields as $name => $user_field) {

                if($name == 'avatar'){
                    $this->post_user_avatar_size = $user_field['options']['size_teaser'];
                    continue;
                }

                $this->user_fields_select['u.' . $user_field['name']] = 'user_' . $user_field['name'];

                if ($user_field['handler']->is_denormalization) {

                    $field_name = $user_field['handler']->getDenormalName();

                    $this->user_fields_select['u.' . $field_name] = 'user_' . $field_name;
                }
            }
        }

        return $this;
    }

    /*
     * Полная информация по голосованию темы
     * @param integer $thread_id Id темы
     * @param \cmsUser $user
     * @param string $field_name По какому полю получать
     * @return array
     */
    public function getThreadPoll($thread_id, $user, $field_name = 'thread_id') {

        $this->filterEqual($field_name, $thread_id);

        return $this->getItem('forum_polls', function($item, $model) use($user) {

            $item['answers'] = cmsModel::yamlToArray($item['answers']);
            $item['options'] = cmsModel::yamlToArray($item['options']);

            $item['results'] = $model->getPollResult($item);

            $item['is_closed'] = false;
            if($item['date_pub_end']){
                $diff_time = strtotime($item['date_pub_end']) - time();
                if($diff_time <= 0){
                    $item['is_closed'] = true;
                }
            }

            if ($item['results']['total'] > 0 && $user->is_logged){
                $item['user_answer_ids'] = $model->getUserPollVote($item['id'], $user->id);
            } else {
                $item['user_answer_ids'] = [];
            }

            $item['is_can_vote'] = true;
            if (!$user->is_logged) {
                $item['is_can_vote'] = false;
            }

            if (!$item['options']['result']) {
                $item['allow_show_result'] = true;
            } elseif ($item['options']['result'] == 1 && $item['user_answer_ids']) {
                $item['allow_show_result'] = true;
            } elseif ($item['options']['result'] == 2 && $item['is_closed']) {
                $item['allow_show_result'] = true;
            } else {
                $item['allow_show_result'] = false;
            }

            $item['first_show_result'] = false;
            if($item['user_answer_ids'] ||
                    ((!$item['is_can_vote'] ||$item['is_closed']) && $item['allow_show_result'])
                    ){
                $item['first_show_result'] = true;
            }

            return $item;
        }) ?: [];
    }

    /*
     * Краткая информация по голосованию темы
     * @param array $thread_id - выбранная тема
     * @return array
     */
    public function getPoll($thread_id) {
        return $this->getItemByField('forum_polls', 'thread_id', $thread_id);
    }

    /*
     * Результаты голосования пользователя
     *
     * @param integer $poll_id Id голосования
     * @param integer $user_id Id пользователя
     * @return array
     */
    public function getUserPollVote($poll_id, $user_id){

        $this->filterEqual('poll_id', $poll_id);
        $this->filterEqual('user_id', $user_id);

        return $this->selectOnly('answer_id')->get('forum_poll_votes', function($item){
            return $item['answer_id'];
        }, false) ?: [];
    }

    /*
     * Результаты голосования
     *
     * @param array $poll Голосование
     * @return array Массив с результатами и общее кол-во голосов
     */
    public function getPollResult($poll){

        $this->selectOnly('COUNT(user_id)', 'result_count');
        $this->select('answer_id');

        $this->filterEqual('poll_id', $poll['id']);
        $this->groupBy('answer_id');

        $answers = [];
        foreach ($poll['answers'] as $id => $q) {
            $answers[$id] = 0;
        }

        $this->get('forum_poll_votes', function ($item, $model) use(&$answers){
            $answers[$item['answer_id']] = $item['result_count'];
            return false;
        }, false) ?: [];

        $total = array_sum($answers);
        $percents = [];

        foreach ($answers as $answer_id => $count) {
            $percents[$answer_id] = $total ? ceil($count / $total * 100) : 0;
        }

        return [
            'answers' => $answers, 'percents' => $percents, 'total' => $total
        ];
    }

    public function getPollVotesUserCount() {
        return $this->getCount('forum_poll_votes');
    }

    public function getPollVotesUsers() {

        $this->orderBy('date_pub');

        $this->joinUserLeft();

        return $this->get('forum_poll_votes', function ($item, $model) {

            $item['user'] = array(
                'id'       => $item['user_id'],
                'slug'     => $item['user_slug'],
                'nickname' => $item['user_nickname'],
                'avatar'   => $item['user_avatar']
            );

            return $item;
        });
    }

    /*
     * Удаление голоса пользователя
     *
     * @param array $poll_id Id опроса
     * @param integer $user_id Id пользователя
     * @return boolean
     */
    public function deleteVote($poll_id, $user_id){

        $this->filterEqual('poll_id', $poll_id);
        $this->filterEqual('user_id', $user_id);

        return $this->deleteFiltered('forum_poll_votes');
    }

    /*
     * Удаление опроса
     *
     * @param integer $poll_id Id опроса
     * @return boolean
     */
    public function deletePoll($poll_id) {

        $this->filterEqual('poll_id', $poll_id);
        $this->deleteFiltered('forum_poll_votes');

        return $this->delete('forum_polls', $poll_id);
    }

    /*
     * Добавление голоса в опросе
     *
     * @param array $vote
     * @return integer
     */
    public function addPollVote($vote) {
        return $this->insert('forum_poll_votes', $vote);
    }

    /*
     * Обновление опроса
     *
     * @param integer $poll_id Id опроса
     * @param array $poll Опрос: новые данные
     * @param array $old_poll Опрос: старые данные
     * @return boolean
     */
    public function updatePoll($poll_id, $poll, $old_poll = []) {

        $poll['answers'] = array_filter($poll['answers']);
        if(!$poll['date_pub_end']){
            $poll['date_pub_end'] = false;
        }

        // Изменённые голоса удаляем
        $chaged_ids = [];
        foreach ($poll['answers'] as $id => $answer_text) {
            if(isset($old_poll['answers'][$id]) && $old_poll['answers'][$id] != $answer_text){
                $chaged_ids[] = $id;
            }
        }
        if($chaged_ids){
            $this->filterEqual('poll_id', $old_poll['id']);
            $this->filterIn('answer_id', $chaged_ids);
            $this->deleteFiltered('forum_poll_votes');
        }

        return $this->update('forum_polls', $poll_id, $poll);
    }

    /*
     * Добавление опроса с теме
     *
     * @param array $poll Опрос
     * @return integer
     */
    public function addPoll($poll){
        $poll['answers'] = array_filter($poll['answers']);
        if(!$poll['date_pub_end']){
            $poll['date_pub_end'] = false;
        }
        return $this->insert('forum_polls', $poll);
    }

    /*
     * Сообщение в теме
     * @param int $id - id сообщения
     * @return array
     */
    public function getPost($id){

        $this->joinUser('user_id', array(), 'left');

        return $this->getItemById('forum_posts', $id, function($item, $model){

            $item['files'] = cmsModel::yamlToArray($item['files']);
            $item['is_author'] = ($item['user_id'] == cmsUser::get('id'));
            $is_time_left = $this->checkEditTime($item['date_pub']);
            $is_can_post_edit = (cmsUser::isAllowed('forum', 'post_edit', 'all') || (cmsUser::isAllowed('forum', 'post_edit', 'own') && $item['is_author'])) ? true : false;
            $item['is_author_can_edit'] = $is_can_post_edit && $is_time_left && $item['is_author'];

            return $item;
        });
    }

    /*
     * Создание темы
     * @param array $thread - данные темы
     * @return array
     */
    public function addThread($thread, $category){

        $cache = cmsCache::getInstance();

        $thread['id'] = $this->insert('forum_threads', $thread);

        $thread['slug'] = $this->getThreadSlug($thread);

        $this->update('forum_threads', $thread['id'], [
            'slug' => $thread['slug']
        ]);

        if (!empty($thread['poll']['title'])) {

            $thread['poll']['thread_id'] = $thread['id'];

            $this->addPoll($thread['poll']);
        }

        // Добавляем первый пост в новую тему
        $post = [
            'thread_id'    => $thread['id'],
            'user_id'      => $thread['user_id'],
            'is_first'     => 1,
            'content'      => $thread['content'],
            'content_html' => $thread['content_html'],
            'files'        => $thread['files']
        ];

        $post['id'] = $this->addPost($post);

        // Обновляем количество сообщений пользователя
        $post['forum_posts_count'] = $this->update('{users}', $thread['user_id'], [
            'forum_posts_count' => $this->getUserPostsCount($thread['user_id'])
        ], true);

        // Обновляем количество и последнее сообщение в теме
        $this->updateLastPostAfterPostEdit($thread);

        // Обновляем количество и последнее сообщение в родительских разделах
        $this->updateLastPostAfterThreadEdit($category);

        // Очищаем кэш
        $cache->clean('users.list');
        $cache->clean('users.user.'.$thread['user_id']);

        return $thread;
    }

    /*
     * Получение slug-ссылки темы
     * @param int $thread - данные темы
     * @return bool
     */
    public function getThreadSlug($thread){

        $pattern = !empty($thread['url_pattern']) ? trim($thread['url_pattern'], '/') : '{title}';

        preg_match_all('/{([a-z0-9\_]+)}/i', $pattern, $matches);

        if (!$matches) { return lang_slug($thread['id']); }

        list($tags, $names) = $matches;

        if (in_array('category', $names)){
            $pattern = str_replace('{category}', $thread['category']['slug'], $pattern);
            unset($names[ array_search('category', $names) ]);
        }

        $pattern = trim($pattern, '/');

        foreach($names as $idx => $name){

            if (empty($thread[$name])){continue;}

            $value = str_replace('/', '', $thread[$name]);

            $pattern = str_replace($tags[$idx], $value, $pattern);
        }

        $slug = lang_slug($pattern);
        $slug = mb_substr($slug, 0, 150);

        if($this->filterNotEqual('id', $thread['id'])->
                filterEqual('slug', $slug)->
                getFieldFiltered('forum_threads', 'id')){

            $slug = mb_substr($slug, 0, (150 - 1 - strlen($thread['id'])));
            $slug .= '-' . $thread['id'];
        }

        return $slug;
    }

    /*
     * Обновление темы
     *
     * @param integer $thread_id Id темы
     * @param array $thread Данные для обновления
     * @param array $category Данные категории темы
     * @return boolean
     */
    public function updateThread($thread_id, $thread, $category = []){

        if (!empty($thread['poll']['title'])) {

            // Если опрос уже был, обновляем, иначе добавляем
            if (!empty($thread['poll']['id'])) {
                $this->updatePoll($thread['poll']['id'], $thread['poll'], $thread['poll']);
            } else {

                $thread['poll']['thread_id'] = $thread_id;

                $this->addPoll($thread['poll']);
            }
        }

        $this->update('forum_threads', $thread_id, $thread);

        if(empty($thread['id'])){
            $thread['id'] = $thread_id;
        }

        // Обновляем количество и последнее сообщение в теме
        $this->updateLastPostAfterPostEdit($thread);

        // Обновляем количество и последнее сообщение в разделах
        if($category){
            $this->updateLastPostAfterThreadEdit($category);
        }

        // Если тема была перенесена в новый раздел, обновляем его последнее сообщение
        if (!empty($thread['from_cat'])) {

            $new_cat = $this->getCategoryByField($thread['category_id']);

            // Обновляем количество тем и последнее сообщение в новом разделе
            $this->updateLastPostAfterThreadEdit($new_cat);

            // Обновляем id категории в постах темы
            $this->filterEqual('thread_id', $thread_id);
            $this->updateFiltered('forum_posts', [
                'category_id' => $thread['category_id']
            ]);

            cmsCache::getInstance()->clean('forum.posts');
        }

        return true;
    }

    /*
     * Количество сообшений пользователя
     * @param array $user_id - id пользователя
     * @return int
     */
    public function getUserPostsCount($user_id) {
        return $this->filterEqual('user_id', $user_id)->
                filterIsNull('is_deleted')->
                getCount('forum_posts', 'id', true);
    }

    /*
     * Обновляем количество сообщений и последнее сообщение в теме
     *
     * @param array $thread Тема
     * @param array $post Последнее сообщение
     * @return boolean
     */
    public function updateLastPostAfterPostEdit($thread, $post = false) {

        $cache = cmsCache::getInstance();

        if (!$post) {
            $post = $this->getThreadLastPost($thread['id']);
        }

        if (!$post){
            return false;
        }

        $posts_count = $this->getThreadPostsCount($thread['id']);

        $cache->clean('forum.threads');
        $cache->clean("forum.threads.{$thread['id']}");

        return $this->update('forum_threads', $thread['id'], [
            'last_post'          => $post,
            'date_last_modified' => $post['date_pub'],
            'posts_count'        => $posts_count
        ]);
    }

    /*
     * Последнее сообщение в теме
     *
     * @param integer $thread_id Id темы
     * @param array $fields Массив дополнительных полей
     * @return array
     */
    public function getThreadLastPost($thread_id, $fields = []){

        $this->selectOnly('i.id');
        $this->select('i.date_pub');
        $this->select('i.user_id');
        $this->select('t.title', 'thread_title');
        if($fields){
            foreach ($fields as $field_name) {
                $this->select('i.'.$field_name);
            }
        }

        $this->joinUserLeft();
        $this->joinInner('forum_threads', 't', 'i.thread_id = t.id');

        $this->orderBy('date_pub', 'desc');

        $this->filterEqual('thread_id', $thread_id);
        $this->filterIsNull('is_deleted');

        return $this->getItem('forum_posts', function($item, $model){
            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => cmsModel::yamlToArray($item['user_avatar'])
            ];
            unset($item['user_id']);
            return $item;
        });
    }

    /*
     * Количество сообщений в теме
     * @param int $thread_id - id темы
     * @return int
     */
    public function getThreadPostsCount($thread_id) {

        $this->filterEqual('thread_id', $thread_id);

        $this->filterIsNull('is_deleted');

        return $this->getCount('forum_posts', 'id', true);
    }

    /*
     * Обновляем количество сообщений, тем и последнее сообщение в родительских разделах
     * @param array $category - раздел
     * @param array $post - сообщение
     * @return bool
     */
    public function updateLastPostAfterThreadEdit($category, $post = false) {

        if (!$post) { $post = $this->getCategoryLastPost($category); }

        $posts_count = $this->getCategoryPostsCount($category);
        $threads_count = $this->getCategoryThreadsCount($category);

        $this->resetFilters()->update('forum_cats', $category['id'], [
            'last_post'          => $post,
            'threads_count'      => $threads_count,
            'date_last_modified' => $post['date_pub'],
            'posts_count'        => $posts_count
        ]);

        cmsCache::getInstance()->clean('forum.categories');
        cmsCache::getInstance()->clean("forum.categories.{$category['id']}");

        if (!empty($category['path'])){
            foreach ($category['path'] as $parent_cat) {
                $this->updateLastPostAfterThreadEdit($parent_cat, $post);
            }
        }

        return true;
    }

    /*
     * Последнее сообщение в разделе
     *
     * @param array $category Раздел
     * @return array
     */
    public function getCategoryLastPost($category){

        $this->selectOnly('i.id');
        $this->select('i.date_pub');
        $this->select('i.user_id');
        $this->select('t.title', 'thread_title');

        $this->orderBy('date_pub', 'desc');

        $this->filterGtEqual('c.ns_left', $category['ns_left']);
        $this->filterLtEqual('c.ns_right', $category['ns_right']);
        $this->filterIsNull('is_deleted');

        $this->joinUser();
        $this->joinInner('forum_threads', 't', 't.id = i.thread_id');
        $this->joinInner('forum_cats', 'c', 'c.id = t.category_id');

        return $this->getItem('forum_posts', function($item){

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => cmsModel::yamlToArray($item['user_avatar'])
            ];
            unset($item['user_id']);

            return $item;
        });
    }

    /*
     * Количество сообщений в разделе
     * @param array $category - раздел форума
     * @return int
     */
    public function getCategoryPostsCount($category){

        $this->selectOnly('IFNULL(SUM(i.posts_count), 0)', 'posts_count');

        $this->filterGtEqual('c.ns_left', $category['ns_left']);
        $this->filterLtEqual('c.ns_right', $category['ns_right']);

        $this->joinLeft('forum_cats', 'c', 'c.id=i.category_id');

        $this->filterIsNull('is_deleted');

        return $this->getItem('forum_threads', function($item){
            return $item['posts_count'];
        });
    }

    /**
     * Время последнего сообщения
     * @param integer $user_id ID пользователя
     * @return type
     */
    public function getUserLastPostTime($user_id) {

        $time = $this->filterEqual('user_id', $user_id)->
                orderBy('date_pub', 'desc')->
                getFieldFiltered('forum_posts', 'date_pub');

        return $time ? strtotime($time) : 0;
    }

    /*
     * Отметка "РЕШЕНО" в теме
     *
     * @param integer $thread_id Id темы
     * @return boolean
     */
    public function fixedThread($thread_id) {

        $cache = cmsCache::getInstance();

        $cache->clean('forum.threads');
        $cache->clean("forum.threads.{$thread_id}");

        return $this->update('forum_threads', $thread_id, [
            'is_fixed' => 1
        ]);
    }

    /*
     * Отметка "ЗАКРЫТО" в теме
     *
     * @param integer $thread_id Id темы
     * @return boolean
     */
    public function closeThread($thread_id) {

        $cache = cmsCache::getInstance();

        $cache->clean('forum.threads');
        $cache->clean("forum.threads.{$thread_id}");

        return $this->update('forum_threads', $thread_id, [
            'is_closed' => 1
        ]);
    }

    /*
     * Обновляем сообщение в теме
     * @param int $post_id - id изменяемого сообщения
     * @param array $post - содержимое сообщения
     * @return array
     */
    public function updatePost($post_id, $post) {

        if(isset($post['modified_count'])){
            $post['modified_count'] = $post['modified_count'] + 1;
            $post['date_last_modified'] = null;
        }

        // Если это флуд, устанавливаем срок публикации такого сообщения
        if (!empty($post['flood_type'])) {
            $post['flood_time'] = date('Y-m-d H:i:s', ($post['flood_type'] * 60 * 60 + time()));
        }

        cmsCache::getInstance()->clean('forum.posts');

        $this->update('forum_posts', $post_id, $post);

        // Если решено - ставим метку
        if (!empty($post['is_fixed'])) {
            $this->fixedThread($post['thread_id']);
        }

        // Закрываем тему, если выбрано
        if (!empty($post['is_closed'])) {
            $this->closeThread($post['thread_id']);
        }

        return true;
    }

    /*
     * Добавляем сообщение в теме
     * @param array $post Содержимое сообщения
     * @return int
     */
    public function addPost($post) {

        $cache = cmsCache::getInstance();

        // Если решено - ставим метку
        if (!empty($post['is_fixed'])) {
            $this->fixedThread($post['thread_id']);
        }

        // Закрываем тему, если отмечено
        if (!empty($post['is_closed'])) {
            $this->closeThread($post['thread_id']);
        }

        // Очищаем просмотренные темы
        $this->filterEqual('thread_id', $post['thread_id'])->
                deleteFiltered('forum_threads_hits');

        // Если это флуд, устанавливаем срок публикации такого сообщения
        if (!empty($post['flood_type'])) {
            $post['flood_time'] = date('Y-m-d H:i:s', ($post['flood_type'] * 60 * 60 + time()));
        }

        $post['date_last_modified'] = null;

        $id = $this->insert('forum_posts', $post);

        $cache->clean('forum.posts');

        // Обновляем количество сообщений пользователя
        $this->update('{users}', $post['user_id'], [
            'forum_posts_count' => $this->getUserPostsCount($post['user_id'])
        ], true);

        // Очищаем кэш
        $cache->clean('users.list');
        $cache->clean('users.user.'.$post['user_id']);

        return $id;
    }

    /*
     * id сообщений темы
     * @param int $thread_id - id темы
     * @return array
     */
    public function getThreadPostsIds($thread_id) {

        $this->selectOnly('id')->select('files')->select('is_deleted');

        $this->filterEqual('thread_id', $thread_id);

        return $this->limit(false)->get('forum_posts', function($item, $model){
            $item['files'] = cmsModel::yamlToArray($item['files']);
            return $item;
        });
    }

    /*
     * Удаление сообщения
     * @param integer $post Сообщение
     * @param boolean $allow_delete Разрешено полностью удалять
     * @return boolean
     */
    public function deletePost($post, $allow_delete = false) {

        // Первичное удаление, скрывает сообщение
        if (empty($post['is_deleted'])) {

            $this->updatePost($post['id'], ['is_deleted' => 1]);

            cmsCore::getController('activity')->deleteEntry('forum', 'add.post', $post['id']);

            return true;
        }

        // При повторном удалении администратором, сообщение удаляется полностью
        if ($allow_delete) {

            // Удаляем прикрепленные файлы
            $this->deletePostAttaches($post);

            $this->delete('forum_posts', $post['id']);

            cmsEventsManager::hook('forum_after_delete_post', $post);

            return true;
        }

        return false;
    }

    /*
     * Удаление файлов сообщения
     *
     * @param array $post Массив сообщения с вложением
     * @return boolean
     */
    public function deletePostAttaches($post){

        $files_model = cmsCore::getModel('files');

        // Удаляем прикрепленные файлы
        if (!empty($post['files']['id'])){
            $files_model->deleteFile($post['files']['id']);
        }

        // Ищем и удаляем картинки в тексте сообщения
        $paths = string_html_get_images_path($post['content_html']);

        if($paths){

            foreach($paths as $path){

                $file = $files_model->getFileByPath($path);

                if(!$file){
                    continue;
                }

                if ($file['target_controller'] === 'forum' && $file['target_id'] === $post['id']){
                    $files_model->deleteFile($file);
                }

            }

        }

        return true;
    }

    /*
     * Удаление темы
     *
     * @param array $thread Тема
     * @param boolean $allow_delete Разрешено полностью удалять
     * @return boolean
     */
    public function deleteThread($thread, $allow_delete = false){

        $cache = cmsCache::getInstance();

        // Получаем id сообщений темы и вложенные файлы, если есть
        $posts_ids = $this->getThreadPostsIds($thread['id']);

        $activity = cmsCore::getController('activity');

        if ($posts_ids) {

            if (empty($thread['is_deleted'])) {

                $posts_keys = array_keys($posts_ids);

                $act_posts = $activity->model->getType('forum', 'add.post');
                $act_vote_posts = $activity->model->getType('forum', 'vote.post');

                // Удаляем записи в ленте активности о сообщениях
                $activity->model->
                        filterEqual('type_id', $act_posts['id'])->
                        filterIn('subject_id', $posts_keys)->
                        deleteFiltered('activity');

                // Удаляем записи в ленте активности о голосованиях
                $activity->model->
                        filterEqual('type_id', $act_vote_posts['id'])->
                        filterIn('subject_id', $posts_keys)->
                        deleteFiltered('activity');

                // Удаляем запись о теме в ленте активности
                $activity->deleteEntry('forum', 'add.thread', $thread['id']);
            }

            // Удаляем сообщения
            foreach ($posts_ids as $post) {
                $this->deletePost($post, $allow_delete);
            }
        }

        $cache->clean('forum.threads');
        $cache->clean("forum.threads.{$thread['id']}");

        if (empty($thread['is_deleted'])){
            return $this->update('forum_threads', $thread['id'], [
                'is_deleted' => 1
            ]);
        }

        if(!$allow_delete){
            return false;
        }

        // Удаляем голосования и голоса
        $poll = $this->getPoll($thread['id']);
        if ($poll){ $this->deletePoll($poll['id']); }

        // Удаляем тему
        $this->delete('forum_threads', $thread['id']);

        cmsEventsManager::hook('forum_after_delete_thread', $thread);

        return true;
    }

    /*
     * Удаление компонента
     * @param int $id - id компонента
     * @return
     */
    public function deleteController($id) {

        // удаляем таблицы компонента
        $this->db->dropTable('forum_cats');
        $this->db->dropTable('forum_polls');
        $this->db->dropTable('forum_poll_votes');
        $this->db->dropTable('forum_posts');
        $this->db->dropTable('forum_threads');
        $this->db->dropTable('forum_threads_hits');

        // удаляем записи из ленты активности
        $activity_types = $this->filterEqual('controller', 'forum')->get('activity_types');

        if ($activity_types){
            foreach ($activity_types as $id => $value) {
                $this->filterEqual('type_id', $id)->deleteFiltered('activity');
            }
        }

        $this->filterEqual('controller', 'forum')->deleteFiltered('activity_types');

        // удаляем страницы виджетов форума
        $this->filterEqual('controller', 'forum')->deleteFiltered('widgets_pages');

        // удаляем виджеты
        $this->filterEqual('controller', 'forum')->deleteFiltered('widgets');

        // удаляем правила доступов
        $rules = $this->filterEqual('controller', 'forum')->get('perms_rules');
        if ($rules){
            foreach ($rules as $rule) {
                $this->filterEqual('rule_id', $rule['id'])->deleteFiltered('perms_users');
            }
            $this->filterEqual('controller', 'forum')->deleteFiltered('perms_rules');
        }

        // удаляем задания планировщика
        $this->filterEqual('controller', 'forum')->deleteFiltered('scheduler_tasks');

        // удаляем настройки из компонента rss
        $this->filterEqual('ctype_name', 'forum')->deleteFiltered('rss_feeds');

        // удаляем вкладку профиля пользователя
        $this->filterEqual('name', 'forum')->deleteFiltered('{users}_tabs');

        // удаляем поле подписи на форуме
        $this->filterEqual('name', 'forum_sign')->deleteFiltered('{users}_fields');

        // удаляем колонки форума из таблицы cms_users
        $this->db->dropTableField('users', 'forum_sign');
        $this->db->dropTableField('users', 'forum_posts_count');

        // удаление запись из cms_controllers
        return parent::deleteController($id);
    }

//============================================================================//
//                              Админка                                       //
//============================================================================//

    public function deleteCategory($ctype_name, $id){
        parent::deleteCategory($ctype_name, $id);
    }

    /*
     * Все темы раздела
     *
     * @param array $category Раздел
     * @param boolean $only_ids Возвращать только id тем
     * @return array
     */
    public function getCatThreads($category, $only_ids = false) {

        $this->filterGtEqual('c.ns_left', $category['ns_left']);
        $this->filterLtEqual('c.ns_right', $category['ns_right']);

        $this->limit(false);
        $this->joinLeft('forum_cats', 'c', 'c.id = i.category_id');

        if (!$only_ids) {
            return $this->get('forum_threads', function ($item, $model) {
                $item['last_post'] = cmsModel::yamlToArray($item['last_post']);
                return $item;
            });
        }

        return $this->selectOnly('i.id')->get('forum_threads', function ($item, $model) {
            return $item['id'];
        }, false) ?: [];
    }

//============================================================================//
//                                  События                                   //
//============================================================================//

    /*
     * Удаление тем пользователя
     *
     * @param integer $user_id ID удаляемого пользователя
     * @return boolean
     */
    public function deleteUserPosts($user_id){

        // Удаляем все голоса пользователя в голосованиях
        $this->filterEqual('user_id', $user_id)->deleteFiltered('forum_poll_votes');

        // Удаляем темы пользователя
        $threads = $this->filterEqual('user_id', $user_id)->get('forum_threads');

        if ($threads){

            $cats = [];

            foreach ($threads as $thread) {

                if(!isset($cats[$thread['category_id']])){

                    $category = $this->getCategoryByField($thread['category_id']);
                    if (!$category){ continue; }

                    $cats[$thread['category_id']] = $category;
                }

                $this->deleteThread($thread);
            }

            foreach ($cats as $category) {
                $this->updateLastPostAfterThreadEdit($category);
            }

            return true;
        }

        return false;
    }

    /*
     * Оценка рейтинга сообщения
     * @param array $subject - оцениваемый материал
     * @param int $id - id сообщения
     * @return array
     */
    public function getRatingTarget($subject, $id){

        $this->select('t.title', 'title');

        $this->joinInner('forum_threads', 't', 't.id = i.thread_id');

        $item = $this->getItemById('forum_posts', $id);

        $item['page_url'] = href_to('forum', 'pfind', $id);

        return $item;
    }

    /*
     * Обновление оценки рейтинга сообщения
     * @param array $subject - оцениваемый материал
     * @param int $id - id сообщения
     * @param int $rating - итоговый рейтинг сообщения
     * @return bool
     */
    public function updateRating($subject, $id, $rating){
        $this->updatePost($id, ['rating' => $rating]);
    }

    /*
     * URL и заголовок целевой страницы
     * @param array $target_subject - тема или сообщение
     * @param int $target_id - id сообщения
     * @return array
     */
    public function getTargetItemInfo($target_subject, $target_id){

        if ($target_subject == 'posts'){

            $this->select('t.title', 'thread_title');
            $this->join('forum_threads', 't', 't.id = i.thread_id');

            $item = $this->getItemById('forum_posts', $target_id);

            if (!$item ){ return []; }

            return [
                'url' => href_to('forum', 'pfind', [$item['id']]),
                'title' => $item['thread_title']
            ];
        }

        $item = $this->getItemById('forum_threads', $target_id);
        if (!$item ){ return []; }

        return [
            'url' => href_to('forum', $item['slug']),
            'title' => $item['title']
        ];
    }

}
