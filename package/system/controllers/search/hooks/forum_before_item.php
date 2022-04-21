<?php

class onSearchForumBeforeItem extends cmsAction {

    public function run($data) {

        if (empty($this->options['is_hash_tag']) || empty($this->options['types'])) {
            return $data;
        }

        list($category, $thread, $thread_poll, $posts, $form) = $data;

        if (!empty($thread['title'])) {
            $thread['title'] = $this->parseHashTag($thread['title']);
        }
        if (!empty($thread['title'])) {
            $thread['description'] = $this->parseHashTag($thread['description']);
        }

        foreach ($posts as $key => $post) {
            $posts[$key]['content_html'] = $this->parseHashTag($post['content_html']);
        }

        return [$category, $thread, $thread_poll, $posts, $form];
    }

}
