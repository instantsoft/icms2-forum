<?php

class onSubscriptionsForumAfterAddPost extends cmsAction {

    public function run($data){

        $forum_options = cmsController::loadOptions('forum');

        if (empty($forum_options['thread_enable_subscriptions'])) {
            return $data;
        }

        list($post, $thread, $category) = $data;

        $subscriptions_list = $this->model->filterEqual('controller', 'forum')->
                filterEqual('subject', 'thread')->
                filterGt('subscribers_count', 0)->
                getSubscriptionsList();

        if(!$subscriptions_list){
            return $data;
        }

        cmsQueue::pushOn('subscriptions', array(
            'controller' => $this->name,
            'hook'       => 'send_letters',
            'params'     => [
                'forum', 'thread', [$post['id']]
            ]
        ));

        return $data;
    }

}
