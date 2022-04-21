<?php

// Получение дерева разделов в виде списка
class actionForumCategoryTreeAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id', 1);

        if (!$id || !preg_match('/^([0-9]+)$/i', $id)){ cmsCore::error404(); }

        $tree_nodes = array();

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($id);
        if (!$category){ $this->cms_template->renderJSON($tree_nodes); }

        // Получаем данные о подразделах
        $subcats = $this->model->getCategoryChilds($category, 1);

        if ($subcats){

            // Если пользователь - администратор, выводим все подразделы
            if ($this->cms_user->is_admin) {

                foreach($subcats as $subcat){

                    if ($subcat['parent_id'] > $id){continue;}

                    $tree_nodes[] = array(
                        'title' => $subcat['title'],
                        'slug' => $subcat['slug'],
                        'key' => $subcat['id'],
                        'isLazy' => ($subcat['ns_right'] - $subcat['ns_left'] > 1),
                        'isFolder' => !empty($subcat['as_folder']) ? true : false
                    );

                }

                return $this->cms_template->renderJSON($tree_nodes);

            }

            // Пользователь в списке модераторов раздела
            $is_moder = $this->isModerator($category['moderators']);

            // Может просматривать текущий раздел
            $is_can_read = $this->cms_user->isInGroups($category['groups_read']) ? true : false;

            // Создаем список доступных разделов
            foreach ($subcats as $subcat){

                if ($subcat['parent_id'] > $id){continue;}

                // Скрываем неопубликованные разделы от немодераторов
                if (!$is_moder && empty($subcat['is_pub'])){continue;}

                $is_can_read = $is_moder || $this->cms_user->isInGroups($category['groups_read']) ? true : false;

                // Скрываем подразделы у недоступных разделов
                if(isset($ns_left) && isset($ns_right) && $subcat['ns_left'] > $ns_left && $subcat['ns_right'] < $ns_right){ continue; }

                if (!$is_can_read){
                    $ns_left = $subcat['ns_left'];
                    $ns_right = $subcat['ns_right'];
                    continue;
                }

                $tree_nodes[] = array(
                    'title' => $subcat['title'],
                    'slug' => $subcat['slug'],
                    'key' => $subcat['id'],
                    'isLazy' => ($subcat['ns_right'] - $subcat['ns_left'] > 1),
                    'isFolder' => !empty($subcat['as_folder']) ? true : false
                );

            }

        }

        return $this->cms_template->renderJSON($tree_nodes);
    }

}
