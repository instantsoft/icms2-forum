<?php $this->addTplCSSNameFromContext('forum'); ?>

<div class="icms-forum__widget-posts">
    <?php foreach ($items as $post) { ?>

    <?php $author_url = href_to_profile($post['user']); ?>

    <div class="media mb-3 mb-md-4<?php if (!empty($post['is_closed'])) { ?> icms-forum__widget-posts-is_closed<?php } ?><?php if (!empty($post['is_pinned'])) { ?> icms-forum__widget-posts-is_pinned<?php } ?><?php if (!empty($post['is_fixed'])) { ?> icms-forum__widget-posts-is_fixed<?php } ?>">
        <div class="media-body">
            <h5 class="d-inline-block mb-2">
                <span class="mr-2">
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
            </h5>
            <?php if ($show_text) { ?>
                <div class="icms-forum__widget-posts-content text-break">
                    <?php echo string_short($post['content_html'], $length, '.', 'w', ['blockquote']); ?>
                </div>
            <?php } ?>
            <div class="text-muted d-flex justify-content-between align-items-center mt-2">
                <div class="small d-flex align-items-center">
                    <span>
                        <?php html_svg_icon('solid', 'history'); ?>
                        <span>
                            <?php echo string_date_age_max($post['date_pub'], true); ?>
                        </span>
                        <?php if ($post['modified_count']){ ?>
                            <span data-toggle="tooltip" data-placement="top" class="date_last_modified ml-2" title="<?php echo LANG_CONTENT_EDITED.' '.strip_tags(html_date_time($post['date_last_modified'])); ?>">
                                <?php html_svg_icon('solid', 'pen'); ?>
                            </span>
                        <?php } ?>
                    </span>
                    <?php if (!empty($post['badges'])) { ?>
                        <?php foreach ($post['badges'] as $type => $badge) { ?>
                            <span class="ml-2 d-inline-flex align-items-baseline">
                                <span class="badge-square badge-square-<?php echo $type; ?>"></span>
                                <span class="d-none d-md-inline-block"><?php echo mb_strtolower($badge); ?></span>
                            </span>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php if($show_rating && $post['rating']){ ?>
                <div>
                    <span class="<?php echo html_signed_class($post['rating']); ?>">
                        <?php if($post['rating'] > 0){ ?>
                            <?php html_svg_icon('solid', 'smile'); ?>
                        <?php } else { ?>
                            <?php html_svg_icon('solid', 'frown'); ?>
                        <?php } ?>
                        <?php echo html_signed_num($post['rating']); ?>
                    </span>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
</div>