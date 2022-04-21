<?php $this->addTplCSSNameFromContext('forum'); ?>

<?php if (!empty($items)) { ?>

    <div class="widget-posts">

        <?php $odd = 0; $is_odd = false; ?>

        <?php foreach ($items as $post) { ?>

            <?php $author_url = href_to_profile($post['user']); ?>

            <?php $odd++; $is_odd = $odd % 2 == 0 ? true : false; ?>

            <div class="widget-posts-item<?php if ($is_odd) { ?> odd<?php } ?><?php if (!empty($post['thread_is_vip'])) { ?> vip-thread-post<?php } ?>">

                <div class="widget-posts-info">

                <span class="widget-posts-avatar">
                    <a href="<?php echo $author_url; ?>" class="icms-user-avatar <?php if (!empty($post['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                        <?php echo html_avatar_image($post['user']['avatar'], 'micro', $post['user']['nickname']); ?>
                    </a>
                </span>
                <a href="<?php echo $author_url; ?>"><?php echo $post['user']['nickname']; ?></a>
                <span>
                    <?php if ($post['is_first']){ ?>
                        <?php echo LANG_WD_FORUM_TR_START; ?>
                    <?php } else { ?>
                        <?php echo LANG_WD_FORUM_TR_GO; ?>
                    <?php } ?>
                </span>
                <a href="<?php echo href_to('forum', 'pfind', $post['id']); ?>" rel="nofollow">
                    <?php echo $post['title']; ?>
                </a>

                <?php if ($show_text) { ?>
                    <div class="icms-forum__widget-posts-content text-break">
                        <?php echo string_short($post['content_html'], $length, '.', 'w', ['blockquote']); ?>
                    </div>
                <?php } ?>

                <span class="widget-posts-date">
                    <?php echo string_date_age_max($post['date_pub'], true); ?>
                </span>

                <?php if (!empty($post['badges'])) { ?>
                    <?php foreach ($post['badges'] as $type => $badge) { ?>
                        <span class="ml-2 d-inline-flex align-items-baseline">
                            <span class="badge-square badge-square-<?php echo $type; ?>"></span>
                            <span class="d-none d-md-inline-block"><?php echo mb_strtolower($badge); ?></span>
                        </span>
                    <?php } ?>
                <?php } ?>

                <?php if($show_rating && $post['rating']){ ?>
                    <div>
                        <span class="widget-posts-rating <?php echo html_signed_class($post['rating']); ?>">
                            <?php echo LANG_RATING; ?>: <?php echo html_signed_num($post['rating']); ?>
                        </span>
                    </div>
                <?php } ?>

                    </div>


            </div>

    <?php } ?>

    </div>

<?php } ?>