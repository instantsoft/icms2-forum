<?php $this->addCSS($this->getStylesFileName('forum')); ?>

<?php if (!empty($items)) { ?>

    <div class="widget-threads">

        <?php $odd = 0; $is_odd = false; ?>

        <?php foreach ($items as $item) { ?>

            <?php $author_url = href_to_profile($item['user']); ?>

            <?php $odd++; $is_odd = $odd % 2 == 0 ? true : false; ?>

            <div class="widget-threads-item<?php if ($is_odd) { ?> odd<?php } ?><?php if ($item['is_vip']) { ?> vip-thread<?php } ?>">

                <div class="widget-threads-info">

                    <span class="widget-threads-avatar">
                        <a href="<?php echo $author_url; ?>" class="icms-user-avatar <?php if (!empty($item['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                            <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                        </a>
                    </span>
                    <a href="<?php echo $author_url; ?>">
                        <?php echo $item['user']['nickname']; ?>
                    </a>
                    <span>
                        <?php echo LANG_WD_FORUM_TR_START; ?>
                    </span>
                    <a href="<?php echo href_to('forum', $item['slug'] . '.html'); ?>">
                        <?php echo $item['title']; ?>
                    </a>
                    <span><?php echo LANG_WD_FORUM_THREADS_CATEGORY; ?></span>
                    <a href="<?php echo href_to('forum', $item['category_slug']); ?>">
                        <?php echo $item['category_title']; ?>
                    </a>

                </div>

            </div>

    <?php } ?>

    </div>

<?php } ?>