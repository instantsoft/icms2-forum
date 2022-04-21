<?php

class onForumFavoritesSubjects extends cmsAction {

    public function run($targets){

        $menu_items = [];

        if(empty($targets[$this->name])){
            return $menu_items;
        }

        $key = $this->name.'-1';

        $menu_items[$key] = [
            'title' => LANG_FORUM_FORUMS,
            'url'   => href_to('favorites', $key)
        ];

        return $menu_items;
    }

}
