<div class="icms-forum border-bottom">
<?php foreach ($cats_list as $cat) { ?>
    <div class="container-fluid icms-forum__list icms-forum__list-ns-<?php echo $cat['ns_level']; ?>" id="category-<?php echo $cat['id']; ?>">
        <?php if ($cat['as_folder']) { ?>

            <div class="row py-3 bg-primary text-white icms-forum__cat">
                <div class="col-sm-6 col-xs-12">
                    <?php if (!empty($cat['icon'])) { ?>
                        <?php echo html_image($cat['icon'], 'micro'); ?>
                    <?php } else { ?>
                        <?php html_svg_icon('solid', 'folder'); ?>
                    <?php } ?>
                    <a class="text-white ml-2" href="<?php echo href_to('forum', $cat['slug']); ?>" title="<?php html($cat['title']); ?>">
                        <?php html($cat['title']); ?>
                    </a>
                </div>
                <div class="col-sm-3 d-none d-md-block">Активность</div>
                <div class="col-sm-3 d-none d-md-block"><?php echo LANG_FORUM_LAST_POST; ?></div>
            </div>

        <?php } else { ?>

            <div class="row align-items-center py-3 icms-forum__section">
                <div class="col-sm-6 col-xs-12">
                    <h3 class="forum-link h5 d-inline-block">
                        <?php if (!empty($cat['icon'])) { ?>
                            <?php echo html_image($cat['icon'], 'micro'); ?>
                        <?php } else { ?>
                            <span class="text-primary mr-2">
                                <?php html_svg_icon('solid', 'comments'); ?>
                            </span>
                        <?php } ?>
                        <a href="<?php echo href_to('forum', $cat['slug']); ?>" title="<?php html($cat['title']); ?>">
                            <?php html($cat['title']); ?>
                        </a>
                    </h3>
                    <p class="text-muted small m-0">
                        <?php html(string_short($cat['description'], 300)); ?>
                    </p>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="small py-2 py-sm-0">
                        <strong><?php echo LANG_FORUM_THREADS; ?>:</strong> <?php echo $cat['threads_count']; ?><br>
                        <strong><?php echo LANG_FORUM_POSTS; ?>:</strong> <?php echo $cat['posts_count']; ?>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="forum-serve small">
                        <?php if (!empty($cat['last_post'])) { ?>
                            <div class="post-from-user">
                                <a href="<?php echo href_to('forum', 'pfind', [$cat['last_post']['id']]); ?>" title="<?php html($cat['last_post']['thread_title']); ?>" rel="nofollow" class="d-block">
                                    <?php html($cat['last_post']['thread_title']); ?>
                                </a>
                                <span class="last-post-from"><?php echo LANG_FORUM_FROM; ?></span>
                                <a href="<?php echo href_to_profile($cat['last_post']['user']); ?>" class="text-muted">
                                    <?php html($cat['last_post']['user']['nickname']); ?>
                                </a>
                                <span class="last-post-date">
                                    <?php echo string_date_format($cat['last_post']['date_pub'], true); ?>
                                </span>
                            </div>
                        <?php } else { ?>
                            <?php echo LANG_FORUM_NOT_POSTS; ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($cat['sub_cats'])) { ?>
                <div class="icms-forum__sub-cats pb-3 text-muted border-top">

                        <div class="font-weight-bold my-2">
                            <?php html_svg_icon('solid', 'level-down-alt'); ?>
                            <?php echo LANG_FORUM_SUBFORUMS; ?>
                        </div>

                        <div class="icms-forum__sub-cats-list row row-cols-1 row-cols-sm-2 row-cols-xl-3">
                            <?php foreach ($cat['sub_cats'] as $sub_cat) { ?>
                                <div class="col">
                                    <?php if (!empty($sub_cat['icon'])) { ?>
                                        <?php echo html_image($sub_cat['icon'], 'micro'); ?>
                                    <?php } else { ?>
                                        <?php html_svg_icon('solid', 'comment'); ?>
                                    <?php } ?>
                                    <a href="<?php echo href_to('forum', $sub_cat['slug']); ?>" class="text-muted" <?php if (empty($sub_cat['as_folder'])) { ?>title="<?php echo html_spellcount($sub_cat['threads_count'], LANG_FORUM_SPELL_THR); ?>, <?php echo html_spellcount($sub_cat['posts_count'], LANG_FORUM_SPELL_POST); ?>" data-toggle="tooltip" data-placement="top"<?php } ?>>
                                        <?php html($sub_cat['title']); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
</div>