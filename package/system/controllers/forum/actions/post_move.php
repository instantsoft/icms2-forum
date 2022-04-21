<?php

class actionForumPostMove extends cmsAction {

    public function run($post_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем данные по сообщению
        $post = $this->model->getPost($post_id);
        if (!$post || !empty($post['is_first'])) {
            return cmsCore::error404();
        }

        // Получаем данные по старой теме
        $thread_old = $this->model->getThreadByField($post['thread_id']);
        if (!$thread_old || $thread_old['is_closed']) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы и сообщения, доступны только администраторам сайта
        if (!$this->cms_user->is_admin && (!empty($post['is_deleted']) || !empty($thread_old['is_deleted']))) {
            return cmsCore::error404();
        }

        // Получаем данные по старому разделу
        $category_old = $this->model->getCategoryByField($thread_old['category_id']);
        if (!$category_old) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        $this->loadCatAccess($category_old['path']);

        // Переносить сообщение может только модератор темы
        if (!$this->cat_access->is_moder) {
            return cmsCore::error404();
        }

        $form = $this->getForm('post_move');

        // Если форма отправлена
        if ($this->request->has('thread_id')) {

            $request = $form->parse($this->request, true);

            $errors = $form->validate($this, $request);

            if ($errors){
                return $this->cms_template->renderJSON([
                    'errors' => $errors
                ]);
            }

            // Если выбрана старая тема
            if ($request['thread_id'] == $thread_old['id']) {
                return $this->cms_template->renderJSON([
                    'errors' => [
                        'thread_id' => ERR_VALIDATE_INVALID
                    ]
                ]);
            }

            // Получаем данные по новой теме
            $thread_new = $this->model->getThreadByField($request['thread_id']);
            if (!$thread_new) {
                return $this->cms_template->renderJSON([
                    'errors' => [
                        'thread_id' => ERR_VALIDATE_INVALID
                    ]
                ]);
            }

            // Получаем данные по разделу новой темы
            $category_new = $this->model->getCategoryByField($thread_new['category_id']);
            if (!$category_new) {
                return $this->cms_template->renderJSON([
                    'errors' => [
                        'thread_id' => ERR_VALIDATE_INVALID
                    ]
                ]);
            }

            // Обновляем время публикации сообщения,
            // чтобы оно не потерялось в новой теме
            $this->model->filterEqual('id', $post['id']);
            $this->model->updateFiltered('forum_posts', [
                'date_pub'       => null,
                'category_id'    => $category_new['id'],
                'thread_id'      => $thread_new['id'],
                'from_thread_id' => $thread_old['id']
            ]);

            // Обновляем запись в ленте активности
            cmsCore::getController('activity')->updateEntry('forum', 'add.post', $post['id'], array(
                'user_id'       => $post['user_id'],
                'subject_title' => $thread_new['title'],
                'subject_id'    => $post['id'],
                'subject_url'   => href_to_rel('forum', 'pfind', array($post['id']))
            ));

            // Обновляем количество и последнее сообщение в старой форуме
            $this->model->updateLastPostAfterPostEdit($thread_old);

            // Обновляем количество и последнее сообщение в старых родительских разделах
            $this->model->updateLastPostAfterThreadEdit($category_old);

            // Обновляем количество и последнее сообщение в новой ветке темы
            $this->model->updateLastPostAfterPostEdit($thread_new);

            // Обновляем количество и последнее сообщение в новых родительских разделах
            $this->model->updateLastPostAfterThreadEdit($category_new);

            // очищаем кэш
            cmsCache::getInstance()->clean("forum.categories.{$category_old['id']}");
            cmsCache::getInstance()->clean("forum.categories.{$category_new['id']}");
            cmsCache::getInstance()->clean("forum.threads.{$thread_old['id']}");
            cmsCache::getInstance()->clean("forum.threads.{$thread_new['id']}");

            cmsUser::addSessionMessage(LANG_FORUM_POST_IS_MOVED, 'success');

            return $this->cms_template->renderJSON(array(
                'errors' => false,
                'redirect_uri' => href_to('forum', 'pfind', [$post['id']])
            ));
        }

        return $this->cms_template->render('post_move', [
            'post'   => $post,
            'thread' => $thread_old,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
