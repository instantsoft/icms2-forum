<?php

class actionForumThreadInvite extends cmsAction {

    public function run($thread_id) {

        // Получаем данные по теме
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и теме
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        // Проверяем, может ли пользователь отправлять приглашения в тему
        if (!$this->thread_access->is_can_send_invite) {
            return cmsCore::error404();
        }

        // Поля из профиля пользователя
        $fields = cmsCore::getModel('content')->setTablePrefix('')->getContentFields('{users}');

        // Добавляем поле "Рейтинг"
        $fields[] = array(
            'title'   => LANG_RATING,
            'name'    => 'rating',
            'handler' => new fieldNumber('rating')
        );

        // Добавляем поле "Карма"
        $fields[] = array(
            'title'   => LANG_KARMA,
            'name'    => 'karma',
            'handler' => new fieldNumber('karma')
        );

        // Добавляем поле "Администратор"
        $fields[] = array(
            'title'   => LANG_USER_IS_ADMIN,
            'name'    => 'is_admin',
            'handler' => new fieldCheckbox('is_admin')
        );

        // Обрабатываем хуком admin_users_filter
        $fields = cmsEventsManager::hook('admin_users_filter', $fields);

        // Если форма отправлена
        if ($this->request->has('submit')) {

            $users_model = cmsCore::getModel('users');

            $filters = $this->request->get('filters', array());

            // Применяем фильтры
            $users_model->applyDatasetFilters(array('filters' => $filters));

            // Получаем список пользователей
            $users = $users_model->
                    filterNotEqual('id', $this->cms_user->id)->
                    getUsersIds();

            if ($users) {

                // Отправляем уведомления выбранным пользователям
                $count = $this->notifyInvitedUsers($users, $thread);

                // Показываем сообщение о том сколько пользователей уведомили
                if ($count > 0) {
                    cmsUser::addSessionMessage(sprintf(LANG_FORUM_THREAD_INVITED, html_spellcount($count, LANG_FORUM_THREAD_INVITED_1, LANG_FORUM_THREAD_INVITED_2, LANG_FORUM_THREAD_INVITED_10)), 'success');
                }
            }

            // Если пользователи не найдены, сообщаем об этом
            if (!$users) {
                cmsUser::addSessionMessage(sprintf(LANG_FORUM_THREAD_INVITED_NO_USERS));
            }
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        $this->cms_template->addBreadcrumb($thread['title'], href_to('forum', $thread['slug'] . '.html'));
        $this->cms_template->addBreadcrumb(LANG_FORUM_THREAD_INVITE);

        // Передаем данные в шаблон
        return $this->cms_template->render('thread_invite', [
            'fields'      => $fields,
            'filters'     => isset($filters) ? $filters : [],
            'cancel_href' => href_to('forum', 'pfind', $thread['last_post']['id']),
            'thread'      => $thread
        ]);
    }

}
