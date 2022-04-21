<?php $this->addCSS($this->getStylesFileName('forum')); ?>

<div class="icms-forum__widget-threads">
    <?php foreach ($items as $item) { ?>

    <div class="media mb-3">

        <?php $author_url = href_to_profile($item['user']); ?>

        <div class="media-body">
            <h3 class="h6 d-inline-block mb-2">
                <span class="mr-2">
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

            </h3>
            <p class="icms-dot-between small text-muted">
                <span>
                    <?php html_svg_icon('solid', 'history'); ?>
                    <span>
                        <?php echo LANG_FORUM_THREAD_CREATED; ?> <?php echo string_date_age_max($item['date_pub'], true); ?>
                    </span>
                </span>
                <span>
                    <?php html_svg_icon('solid', 'comments'); ?>
                    <span>
                        <?php echo html_spellcount($item['posts_count'], LANG_FORUM_SPELL_POST); ?>
                    </span>
                </span>
                <span>
                    <?php html_svg_icon('solid', 'eye'); ?>
                    <span>
                        <?php echo html_spellcount($item['hits'], LANG_HITS_SPELL); ?>
                    </span>
                </span>
            </p>
        </div>

    </div>

<?php } ?>
</div>