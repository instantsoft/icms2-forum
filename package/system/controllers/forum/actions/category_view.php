<?php
/**
 * Просмотр тем раздела форума
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumCategoryView extends cmsAction {

    public function run() {

        $slug = $this->request->get('slug', '');
        if (!$slug) {
            return cmsCore::error404();
        }

        // Получаем данные раздела
        $category = $this->model->getCategoryByField($slug, 'slug');
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и доступы для темы
        $this->loadCatAccess($category['path'])->loadThreadAccess([
            'user_id' => $this->cms_user->id
        ]);

        if (!$this->cat_access->is_can_read) {
            return cmsCore::error404();
        }

        // добавляем Last-Modified
        if(!$this->cms_user->is_logged){
            cmsCore::respondIfModifiedSince($category['date_last_modified']);
        }

        // Получаем список разделов отсортированных по уровням вложенности
        // и проверяем пользователя на доступ к просмотру их
        $category['childs_path'] = $this->model->getCategoryChilds($category, $this->options['cats_level_view'], $this->getChildsAccessCallback());

        // Разбиваем подразделы на уровни
        $category['childs_path'] = $this->getChildsLevels($category['childs_path']);

        $threads = [];

        // Номер текущей страницы раздела
        $page = $this->request->get('page', 1);

        // Количество тем на странице
        $perpage = $this->options['perpage_threads'];

        // Если раздел не как категория,
        // получаем темы раздела и подразделов
        if (empty($category['as_folder'])) {

            // Скрываем удаленные темы
            if (!$this->cms_user->is_admin) {
                $this->model->filterIsNull('is_deleted');
            }

            // Показываем темы только из данного раздела
            $this->model->filterEqual('category_id', $category['id']);

            $total = $this->model->getThreadsCount();

            // Ограничиваем количество тем для вывода на текущей странице
            $this->model->limitPage($page, $perpage);

            $this->model->orderByList($this->options['threads_sorting']);

            // Получаем данные о темах категории
            $threads = $this->model->getThreads($this->options['fix_threads_reads']);

            // Бесконечное кол-во страниц нам не нужно
            if(!$threads && $page > 1){ cmsCore::error404(); }

            // Предупреждаем, если пользователю запрещено создавать темы
            if ($this->cms_user->is_logged && !$this->thread_access->is_can_thread_add) {
                cmsUser::addSessionMessage(LANG_FORUM_NOT_CREATED_THREAD_ON_THIS_FORUM, 'info');
            }
        }

        list($category, $threads) = cmsEventsManager::hook('forum_before_list', array($category, $threads));

        // Кэшируем данные категории
        cmsModel::cacheResult('current_forum_category', $category);

        // Используем rss
        $is_rss = false;

        if (!empty($this->options['is_rss']) && empty($category['as_folder'])) {
            $is_rss = cmsController::enabled('rss') && $this->model->db->getField('rss_feeds', "ctype_name = 'forum'", 'is_enabled');
        }

        // Файл шаблона раздела, выбранный в настройках форума
        $tpl = $this->options['tpl_cats'];

        // Файл шаблона раздела, выбранный в настройках раздела
        if (!empty($category['options']['tpl_cats'])) {
            $tpl = $category['options']['tpl_cats'];
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        return $this->cms_template->render($tpl, [
            'user'              => $this->cms_user,
            'fix_threads_reads' => $this->options['fix_threads_reads'],
            'category'          => $category,
            'threads'           => $threads,
            'page'              => $page,
            'perpage'           => $perpage,
            'total'             => isset($total) ? $total : 0,
            'is_can_add_thread' => $this->thread_access->is_can_thread_add,
            'is_moder'          => $this->cat_access->is_moder,
            'is_rss'            => $is_rss,
            'options'           => $this->options
        ]);
    }

}
