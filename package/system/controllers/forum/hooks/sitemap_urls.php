<?php

class onForumSitemapUrls extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($type) {

        $urls = [];

        if (!in_array($type, ['cats', 'threads'])) {
            return $urls;
        }

        $cats = $this->model->filterEqual('is_pub', 1)->
                filterGt('parent_id', 0)->
                limit(false)->getCategories($this->getChildsAccessCallback());

        if (!$cats) {
            return $urls;
        }

        if ($type == 'cats') {

            $datasets = $this->getDatasets();

            $is_set_index_url = false;

            foreach ($datasets as $dataset) {

                $urls[] = [
                    'last_modified' => date('Y-m-d'),
                    'title' => $dataset['title'],
                    'url' => $is_set_index_url ? href_to_abs($this->name, $dataset['name']) : href_to_abs($this->name)
                ];

                $is_set_index_url = true;
            }

            foreach ($cats as $cat) {
                $urls[] = [
                    'last_modified' => $cat['date_last_modified'],
                    'title' => $cat['title'],
                    'url' => href_to_abs($this->name, $cat['slug'])
                ];
            }
        }

        if ($type == 'threads') {

            $threads = $this->model->selectOnly('title')->select('date_last_modified')->
                    select('slug')->select('is_fixed')->select('is_closed')->
                    filterIsNull('is_deleted')->
                    filterIn('category_id', array_keys($cats))->limit(false)->
                    get('forum_threads', false, false) ?: [];

            foreach ($threads as $thread) {

                $urls[] = [
                    'last_modified' => $thread['date_last_modified'],
                    'title'         => $thread['title'],
                    'url'           => href_to_abs($this->name, $thread['slug'] . '.html')
                ];
            }
        }

        return $urls;
    }

}
