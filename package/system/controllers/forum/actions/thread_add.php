<?php
/**
 * Создание темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThreadAdd extends cmsAction {

    public function run($category_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsUser::goLogin();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($category_id);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и доступы для темы
        $this->loadCatAccess($category['path'])->loadThreadAccess([
            'user_id' => $this->cms_user->id
        ]);

        // Не разрешено создавать темы
        if(!$this->thread_access->is_can_thread_add){
            // Ограничение по карме
            if ($this->thread_access->thread_add_karma !== false) {
                cmsUser::addSessionMessage(sprintf(LANG_FORUM_KARMA_LIMIT, $this->thread_access->thread_add_karma), 'error');
                $this->redirectBack();
            }
            return cmsCore::error404();
        }

        $thread = [];

        // Форма создания темы
        $form = $this->getForm('thread', ['add', $this->cat_access->is_moder]);

        // Добавляем поля первого поста
        $post_fields = $this->getPostFormFields([
            'is_attach' => $this->thread_access->is_can_attach
        ], false);
        foreach ($post_fields as $post_field) {
            $form->addField('basic', $post_field);
        }

        // Добавляем голосование, если разрешено
        if($this->thread_access->is_can_poll_add){

            $poll_fieldsets = $this->getThreadPollFormFieldsets();

            foreach ($poll_fieldsets as $fid => $poll_fieldset) {
                $form->addFieldset('', $fid, $poll_fieldset);
            }
        }

        // Обработка формы событиями
        list($form, $thread) = cmsEventsManager::hook("forum_thread_form", [$form, $thread]);

        // Если форма отправлена
        if ($this->request->has('submit')) {

            // Получаем данные из формы
            $thread = $form->parse($this->request, true);

            // Проверяем полученные данные
            $errors = $form->validate($this, $thread);

            $thread['content_html'] = !empty($this->options['is_html_filter']) ? cmsEventsManager::hook('html_filter', [
                'text'                => $thread['content'],
                'is_auto_br'          => empty($this->options['editor']) || $this->options['editor'] === 'markitup',
                'build_smiles'        => $this->options['editor'] === 'markitup', // пока что только так
                'build_redirect_link' => $this->options['build_redirect_link']
            ]) : $thread['content'];

            if(!$thread['content_html']){
                $errors['content'] = LANG_VALIDATE_REQUIRED;
            }

            if(mb_strlen($thread['content_html']) > 65535){
                $errors['content'] = sprintf(ERR_VALIDATE_MAX_LENGTH, 65535);
            }

            // Если ошибок нет
            if (!$errors) {

                $thread['user_id']            = $this->cms_user->id;
                $thread['category_id']        = $category['id'];
                $thread['date_last_modified'] = null;
                $thread['category']           = $category;
                $thread['url_pattern']        = $this->options['item_url_pattern'];

                // Опросы запрещены
                if (!$this->thread_access->is_can_poll_add) {
                    $thread['poll'] = [];
                }

                // Вложенные файлы запрещены
                if (!$this->thread_access->is_can_attach) {
                    $thread['files'] = null;
                }

                // Добавляем новую тему
                $thread = $this->model->addThread($thread, $category);

                // Добавляем запись в ленту активности
                cmsCore::getController('activity')->addEntry('forum', 'add.thread', array(
                    'user_id'       => $thread['user_id'],
                    'subject_title' => $thread['title'],
                    'subject_id'    => $thread['id'],
                    'is_parent_hidden' => $this->cat_access->is_set_read_access ? 1 : null,
                    'subject_url'   => href_to_rel('forum', $thread['slug'] . '.html')
                ));

                // Возвращаем пользователя к новой теме
                $this->redirectTo('forum', $thread['slug'] . '.html');
            }

            // Если есть ошибки, сообщаем об этом
            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        return $this->cms_template->render('thread_add', [
            'do'        => 'add',
            'page_title' => LANG_FORUM_NEW_THREAD,
            'form'     => $form,
            'thread'   => $thread,
            'category' => $category,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
