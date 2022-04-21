<?php foreach ($posts as $post) { ?>

    <?php $is_new = !empty($user->date_log) ? (int) strtotime($post['date_pub']) > (int) strtotime($user->date_log) : false; ?>

    <div id="post_<?php echo $post['id']; ?>" class="post<?php if ($is_new) { ?> post-is-new<?php } ?><?php if ($post['is_deleted']) { ?> post-is-deleted<?php } ?>">

        <div class="info">

            <div class="name">
                <a class="user" href="<?php echo href_to('users', $post['user_id']); ?>"><?php html($post['user_nickname']); ?></a>
                &rarr;
                <a class="subject" href="<?php echo href_to('forum', 'pfind', $post['id']); ?>" rel="nofollow"><?php html($post['thread_title']); ?></a>
            </div>

            <div class="date">
                <span><?php echo html_date_time($post['date_pub']); ?></span>
            </div>

        </div>

        <div class="body">

            <div <?php if (!empty($post['is_online'])) { ?>class="avatar post-user-online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="avatar"<?php } ?>>
                <a href="<?php echo href_to('users', $post['user_id']); ?>">
                    <?php echo html_avatar_image($post['user_avatar'], 'micro', $post['user_nickname'], true); ?>
                </a>
            </div>

            <div class="content">

                <div class="text">
                    <?php echo $post['content_html']; ?>
                </div>

            </div>

        </div>

    </div>

<?php } ?>
