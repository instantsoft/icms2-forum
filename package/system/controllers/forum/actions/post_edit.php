<?php
/**
 * Редактирование поста
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumPostEdit extends cmsAction {

    public function run($post_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем данные сообщения
        $post = $this->model->getPost($post_id);
        if (!$post) {
            return cmsCore::error404();
        }

        // Получаем данные по теме форума
        $thread = $this->model->getThreadByField($post['thread_id']);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы и сообщения, доступны только администраторам сайта
        if (!$this->cms_user->is_admin && (!empty($post['is_deleted']) || !empty($thread['is_deleted']))) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category || !empty($category['as_folder'])) {
            return cmsCore::error404();
        }
        if (!$this->cms_user->is_admin && !$category['is_pub']) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и теме
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        if (!$this->cat_access->is_moder && !$this->isPostCanEdit($post)) {
            return cmsCore::error404();
        }

        // Может выбирать срок показа флуда
        $is_can_autoflood = !empty($category['autoflood']) && !$post['is_first'];

        // Форма добавления сообщения
        $form = $this->getPostFormFields([
            'is_fixed'     => $this->thread_access->is_can_fixed,
            'is_closed'    => $this->thread_access->is_can_closed,
            'is_autoflood' => $is_can_autoflood,
            'is_attach'    => $this->thread_access->is_can_attach
        ]);

        // Если форма отправлена
        if ($this->request->has('submit')) {

            // Получаем данные из формы о сообщении
            $post = array_merge($post, $form->parse($this->request, true, $post));

            $errors = $form->validate($this, $post);

            // Обрабатываем сообщение типографом
            $post['content_html'] = $this->options['is_html_filter'] ? cmsEventsManager::hook('html_filter', [
                'text'                => $post['content'],
                'is_auto_br'          => empty($this->options['editor']) || $this->options['editor'] === 'markitup',
                'build_smiles'        => $this->options['editor'] === 'markitup', // пока что только так
                'build_redirect_link' => $this->options['build_redirect_link']
            ]) : $post['content'];

            if(!$post['content_html']){
                $errors['content'] = LANG_VALIDATE_REQUIRED;
            }

            if(mb_strlen($post['content_html']) > 65535){
                $errors['content'] = sprintf(ERR_VALIDATE_MAX_LENGTH, 65535);
            }

            if (!$errors) {

                list($post, $thread, $category) = cmsEventsManager::hook('forum_before_edit_post', [$post, $thread, $category]);

                $this->model->updatePost($post_id, $post);

                list($post, $thread, $category) = cmsEventsManager::hook('forum_after_edit_post', [$post, $thread, $category]);

                // Обновляем запись в ленте активности
                cmsCore::getController('activity')->updateEntry('forum', 'add.post', $post['id'], [
                    'subject_title' => $thread['title'],
                    'subject_id'    => $post['id'],
                    'subject_url'   => href_to_rel('forum', 'pfind', [$post['id']])
                ]);

                // Обновляем количество и последнее сообщение в теме
                $this->model->updateLastPostAfterPostEdit($thread);

                // Обновляем количество и последнее сообщение родительских разделах
                $this->model->updateLastPostAfterThreadEdit($category);

                // Возвращаемся к сообщению
                return $this->runExternalAction('pfind', [$post['id']]);
            }

            // Если есть ошибки, уведомляем об этом
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

        $this->cms_template->addBreadcrumb($thread['title'], href_to('forum', $thread['slug'] . '.html'));

        return $this->cms_template->render('post_edit', [
            'form'     => $form,
            'post'     => $post,
            'thread'   => $thread,
            'category' => $category,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
