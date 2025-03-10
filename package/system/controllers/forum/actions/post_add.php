<?php
/**
 * Добавление поста
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 * @property \cmsRequest $request
 */
class actionForumPostAdd extends cmsAction {

    public function run($thread_id, $post_id = false) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsUser::goLogin();
        }

        // Получаем данные темы
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread || $thread['is_closed']) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
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

        // Проверка на права доступа
        if (!$this->thread_access->is_can_write) {
            // Ограничение по карме на ответы в теме
            if ($this->thread_access->post_add_karma !== false) {
                cmsUser::addSessionMessage(sprintf(LANG_FORUM_KARMA_LIMIT, $this->thread_access->post_add_karma), 'error');
                $this->redirectBack();
            }
            return cmsCore::error404();
        }

        // Проверка интервала между публикациями сообщений одного пользователя
        if ($this->options['post_interval']) {

            $last_post_time = $this->model->getUserLastPostTime($this->cms_user->id);
            if ($last_post_time) {
                $diff_time = time() - $last_post_time;
                if($diff_time <= $this->options['post_interval']){

                    cmsUser::addSessionMessage(sprintf(LANG_FORUM_SMALL_POST_INTERVAL, $this->options['post_interval'], ($this->options['post_interval'] - $diff_time)), 'error');
                    $this->redirectBack();
                }
            }
        }

        $post = [];

        // Форма добавления сообщения
        $form = $this->getPostFormFields([
            'is_fixed'     => $this->thread_access->is_can_fixed,
            'is_closed'    => $this->thread_access->is_can_closed,
            'is_autoflood' => !empty($category['autoflood']),
            'is_attach'    => $this->thread_access->is_can_attach
        ]);

        // Родительское сообщение
        // Если указано, то новое сообщение будет считаться ответом на него
        $parent_post = $post_id ? ($this->model->getPost($post_id) ?: []) : [];

        // Предварительно удаленные сообщения, доступны только администраторам сайта
        if ($parent_post && !$this->cms_user->is_admin && !empty($parent_post['is_deleted'])) {
            $parent_post = [];
        }

        // Добавляем цитату из родительского сообщения
        if ($parent_post) {
            $post['content'] = string_replace_keys_values($this->options['quote_template'], $parent_post);
        }

        // Если форма была отправлена
        if ($this->request->has('submit')) {

            // Получаем данные формы
            $post = $form->parse($this->request, true);

            // Проверяем полученные данные
            $errors = $form->validate($this, $post);

            // Пропускаем сообщение через типограф
            $content_html = $this->options['is_html_filter'] ? cmsEventsManager::hook('html_filter', [
                'text'                => $post['content'],
                'is_auto_br'          => empty($this->options['editor']) || $this->options['editor'] === 'markitup',
                'build_smiles'        => $this->options['editor'] === 'markitup', // пока что только так
                'build_redirect_link' => empty($this->options['build_redirect_link']) ? true : false
            ]) : $post['content'];

            if(!$content_html){
                $errors['content'] = LANG_VALIDATE_REQUIRED;
            }

            if(mb_strlen($content_html) > 65535){
                $errors['content'] = sprintf(ERR_VALIDATE_MAX_LENGTH, 65535);
            }

            // Если нет ошибок
            if (!$errors) {

                // Последнее сообщение темы
                $last_post_thread = [];

                // Если отмечена опция объединения с последним сообщением
                // если не указано, что "флуд" и нет прикреплённых файлов
                if (!empty($this->options['combine_post']) && empty($post['flood_type']) && empty($post['files'])) {
                    $last_post_thread = $this->model->getThreadLastPost($thread['id'], ['content', 'content_html', 'modified_count']);
                }

                // Объединяем с предыдущим сообщением
                if ($last_post_thread && ($last_post_thread['user']['id'] == $this->cms_user->id)) {

                    if (empty($this->options['combine_interval']) ||
                            ((time() - strtotime($last_post_thread['date_pub'])) < $this->options['combine_interval'] * 60)) {

                        $last_content = $last_post_thread['content'] . "\n\n" . $post['content'];

                        $last_content_html = $last_post_thread['content_html'];
                        $last_content_html .= '<div class="icms-forum__post-added_later">' . sprintf(LANG_FORUM_ADDED_LATER, string_date_age_max([date('Y-m-d H:i:s'), $last_post_thread['date_pub']])) . '</div>';
                        $last_content_html .= $content_html;

                        // Проверяем, чтобы длина объединенного сообщения
                        // не превышала длины ячейки в базе данных
                        if (mb_strlen($last_content_html) < 65535) {

                            $last_post_thread['content'] = $last_content;

                            $last_post_thread['content_html'] = $last_content_html;

                            $last_post_thread['date_pub'] = null; // текущая дата будет

                            $this->model->updatePost($last_post_thread['id'], $last_post_thread);

                            // Обновляем количество и последнее сообщение в теме
                            $this->model->updateLastPostAfterPostEdit($thread);

                            // Обновляем количество и последнее сообщение в родительских разделах
                            $this->model->updateLastPostAfterThreadEdit($category);

                            // Возвращаемся к добавленному сообщению в теме
                            return $this->runExternalAction('pfind', [$last_post_thread['id']]);
                        }
                    }
                }

                $post['content_html'] = $content_html;
                $post['category_id']  = $thread['category_id'];
                $post['thread_id']    = $thread['id'];
                $post['user_id']      = $this->cms_user->id;

                list($post, $thread, $category) = cmsEventsManager::hook('forum_before_add_post', [$post, $thread, $category]);

                $post['id'] = $this->model->addPost($post);

                list($post, $thread, $category) = cmsEventsManager::hook('forum_after_add_post', [$post, $thread, $category]);

                // Добавляем запись в ленту активности
                cmsCore::getController('activity')->addEntry('forum', 'add.post', array(
                    'user_id'       => $post['user_id'],
                    'subject_title' => $thread['title'],
                    'is_parent_hidden' => $this->cat_access->is_set_read_access ? 1 : null,
                    'subject_id'    => $post['id'],
                    'subject_url'   => href_to_rel('forum', 'pfind', [$post['id']])
                ));

                // Обновляем количество и последнее сообщение в теме
                $this->model->updateLastPostAfterPostEdit($thread);

                // Обновляем количество и последнее сообщение родительских разделах
                $this->model->updateLastPostAfterThreadEdit($category);

                // Возвращаем пользователя к новому сообщению
                return $this->runExternalAction('pfind', [$post['id']]);
            }

            // Если есть ошибки, сообщаем об этом
            if ($errors) {
                $this->cms_user->addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        $this->cms_template->addBreadcrumb($thread['title'], href_to('forum', $thread['slug'] . '.html'));

        return $this->cms_template->render('post_add', [
            'form'        => $form,
            'thread'      => $thread,
            'category'    => $category,
            'parent_post' => $parent_post,
            'post'        => $post,
            'options'     => $this->options,
            'errors'      => isset($errors) ? $errors : false
        ]);
    }

}
