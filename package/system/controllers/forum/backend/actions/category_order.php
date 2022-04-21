<?php
/**
 * Упорядочивание разделов форума
 */
class actionForumCategoryOrder extends cmsAction {

    private $total_nodes = 0;

    public function run(){

        $categories = $this->model->getCategoriesTree('forum', false);

        if ($this->request->has('submit')){

            $hash = $this->request->get('hash', '');

            cmsUser::setCookiePublic('forum_tree_path', 1);

            $this->reorderCategoriesTree($categories, $hash);

            cmsUser::addSessionMessage(LANG_CP_ORDER_SUCCESS, 'success');

            $this->redirectBack();
        }

        return $this->cms_template->render('backend/category_order', [
            'categories' => $categories,
        ]);
    }

    private function reorderCategoriesTree($categories, $hash){

        $hash = json_decode($hash, true);

        $this->total_nodes = 0;

        $tree = $this->prepareTree($hash);

        $tree = $this->buildNestedSet($tree);

        $this->model->updateCategoryTree('forum', $tree, $this->total_nodes);
    }

    private function prepareTree($tree, $parent_key=1){

        foreach($tree as $idx => $node){

            unset($node['expand']);
            unset($node['activate']);
            unset($node['isFolder']);

            $node['parent_key'] = $parent_key;

            if (!empty($node['children'])){
                $count = sizeof($node['children']);
                $node['children'] = $this->prepareTree($node['children'], $node['key']);
                $node['children_count'] = $this->countChilds($node['children'], $count);
            } else {
                $node['children_count'] = 0;
            }

            $tree[$idx] = $node;
            $this->total_nodes++;

        }

        return $tree;
    }

    private function countChilds($tree, $count){

        foreach($tree as $idx => $node){

            if (!empty($node['children'])){
                $my_count = sizeof($node['children']);
                $count = $my_count + $this->countChilds($node['children'], $count);
            }

        }

        return $count;
    }

    private function buildNestedSet($tree, $left=1, $level=1){

        foreach($tree as $idx => $node){

            $left++;

            $node['left'] = $left;
            $node['level'] = $level;

            if (!empty($node['children'])){

                $node['right'] = $left + ($node['children_count']*2) + 1;

                $child_level = $level+1;

                $node['children'] = $this->buildNestedSet($node['children'], $left, $child_level);

            } else {

                $node['right'] = $node['left'] + 1;

            }

            $left = $node['right'];

            $tree[$idx] = $node;

        }

        return $tree;
    }

}
