<table class="category-table" cellspacing="0" cellpadding="0">

    <thead class="category-table-header">

        <tr>
            <td class="category-table-header-cats"><?php echo LANG_FORUM_CATS; ?></td>
            <td class="category-table-header-threads"><?php echo LANG_FORUM_THREADS; ?></td>
            <td class="category-table-header-posts"><?php echo LANG_FORUM_POSTS; ?></td>
            <td class="category-table-header-lastpost"><?php echo LANG_FORUM_LAST_POST; ?></td>
        </tr>

    </thead>

    <tbody>

        <?php $level = $cats_list[0]['ns_level']; $odd = 0; $is_odd = false;?>

        <?php foreach ($cats_list as $id => $cat) { ?>

            <?php // Временный костыль для старых "последних" сообщений
                if (empty($cat['last_post']['user']) && !empty($cat['last_post']['user_nickname'])){
                    $cat['last_post']['post_id'] = $cat['last_post']['id'];
                    $cat['last_post']['user'] = [
                        'id' => $cat['last_post']['user_id'],
                        'nickname' => $cat['last_post']['user_nickname'],
                        'avatar' => $cat['last_post']['user_avatar'],
                    ];
                }
            ?>

            <?php $odd++;
            $is_odd = $odd % 2 == 0 ? true : false; ?>

            <?php $is_parent = $level == 1 ? ' is_first_parent' : ''; ?>

    <?php if (!empty($cat['as_folder'])) {
        $odd = 0;
    } ?>

    <?php if ($cat['ns_level'] == $level) { ?>

                <tr id="category-<?php echo $cat['id']; ?>" class="category<?php if (!empty($cat['as_folder'])) { ?> category-as-folder<?php } ?><?php echo $is_parent; ?><?php if ($is_odd) { ?> odd<?php } ?>">

                                <?php if (!empty($cat['as_folder'])) { ?>

                        <td colspan="4" class="category-ceil">
                            <div class="category-icon<?php if (empty($cat['icon'])) { ?> default_icon<?php } ?>">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
            <?php if (!empty($cat['icon'])) { ?>
                <?php echo html_image($cat['icon'], 'micro'); ?>
            <?php } ?>
                                </a>
                            </div>
                            <div class="category-title">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>"><?php html($cat['title']); ?></a>
                            </div>
                        </td>

                                <?php } else { ?>

                        <td class="category-ceil">
                            <div class="category-icon<?php if (empty($cat['icon'])) { ?> default_icon<?php } ?>">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
            <?php if (!empty($cat['icon'])) { ?>
                                        <?php echo html_image($cat['icon'], 'micro'); ?>
                                    <?php } ?>
                                </a>
                            </div>
                            <div class="category-title">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
            <?php html($cat['title']); ?>
                                </a>
                            </div>
                            <div class="category-description">
            <?php html(string_short($cat['description'], 300)); ?>
                            </div>
                        </td>

                        <td class="threads-count"><?php echo $cat['threads_count']; ?></td>

                        <td class="posts-count"><?php echo $cat['posts_count']; ?></td>

                        <td class="last-post">
                                        <?php if (!empty($cat['last_post'])) { ?>

                                <div class="post-from-user">
                                    <span class="last-post-thread">
                                        <a href="<?php echo href_to('forum', 'pfind', array($cat['last_post']['id'])); ?>" title="<?php html($cat['last_post']['thread_title']); ?>" rel="nofollow">
                <?php html($cat['last_post']['thread_title']); ?>
                                        </a>
                                    </span>

                                    <span class="last-post-from"><?php echo LANG_FORUM_FROM; ?></span>
                                    <span class="last-post-user"><a href="<?php echo href_to('users', $cat['last_post']['user']['id']); ?>" title="<?php html($cat['last_post']['user']['nickname']); ?>"><?php html($cat['last_post']['user']['nickname']); ?></a></span>
                                    <span class="last-post-date"><?php echo string_date_format($cat['last_post']['date_pub'], true); ?></span>
                                </div>

                        <?php } else { ?>
                <?php echo LANG_FORUM_NOT_POSTS; ?>
            <?php } ?>
                        </td>

                <?php } ?>

                </tr>

                <?php } ?>

    <?php if (($cat['ns_level'] - 1) == $level) { ?>

                <tr id="category-<?php echo $cat['id']; ?>" class="category<?php if (!empty($cat['as_folder'])) { ?> category-as-folder<?php } ?><?php if ($is_odd) { ?> odd<?php } ?>">

                                <?php if (!empty($cat['as_folder'])) { ?>

                        <td colspan="4" class="category-ceil">

                            <div class="category-icon<?php if (empty($cat['icon'])) { ?> default_icon<?php } ?>">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
            <?php if (!empty($cat['icon'])) { ?>
                <?php echo html_image($cat['icon'], 'micro'); ?>
                                    <?php } ?>
                                </a>
                            </div>

                            <div class="category-title">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
                        <?php html($cat['title']); ?>
                                </a>
                            </div>
                        </td>

                                <?php } else { ?>

                        <td class="category-ceil">
                            <div class="category-icon<?php if (empty($cat['icon'])) { ?> default_icon<?php } ?>">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
            <?php if (!empty($cat['icon'])) { ?>
                <?php echo html_image($cat['icon'], 'micro'); ?>
                                    <?php } ?>
                                </a>
                            </div>

                            <div class="category-title">
                                <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>" title="<?php html($cat['title']); ?>">
                                <?php html($cat['title']); ?>
                                </a>
                            </div>

                            <div class="category-description">
            <?php html(string_short($cat['description'], 300)); ?>
                            </div>
                        </td>

                        <td class="threads-count"><?php echo $cat['threads_count']; ?></td>

                        <td class="posts-count"><?php echo $cat['posts_count']; ?></td>

                        <td class="last-post">
                                        <?php if (!empty($cat['last_post'])) { ?>

                                <div class="post-from-user">
                                    <span class="last-post-thread">
                                        <a href="<?php echo href_to('forum', 'pfind', array($cat['last_post']['id'])); ?>" title="<?php html($cat['last_post']['thread_title']); ?>" rel="nofollow">
                <?php html($cat['last_post']['thread_title']); ?>
                                        </a>
                                    </span>

                                    <span class="last-post-from"><?php echo LANG_FORUM_FROM; ?></span>
                                    <span class="last-post-user"><a href="<?php echo href_to('users', $cat['last_post']['user']['id']); ?>" title="<?php html($cat['last_post']['user']['nickname']); ?>"><?php html($cat['last_post']['user']['nickname']); ?></a></span>
                                    <span class="last-post-date"><?php echo string_date_format($cat['last_post']['date_pub'], true); ?></span>
                                </div>

                        <?php } else { ?>
                <?php echo LANG_FORUM_NOT_POSTS; ?>
            <?php } ?>
                        </td>

        <?php } ?>

                </tr>

        <?php if (!empty($cat['sub_cats'])) { ?>

                    <tr id="category-<?php echo $cat['id']; ?>" class="sub-category<?php if ($is_odd) { ?> odd<?php } ?>">

                        <td colspan="4" class="sub-category-ceil">

                            <div class="sub-category-header"><?php echo LANG_FORUM_SUBFORUMS; ?></div>

                            <div class="sub-category-titles">
                                        <?php foreach ($cat['sub_cats'] as $key => $sub_cat) { ?>

                                    <div class="sub-category-title">
                                        <span class="sub-category-icon<?php if (empty($sub_cat['icon'])) { ?> default_icon<?php } ?>">
                                    <?php if (!empty($sub_cat['icon'])) { ?>
                                        <?php echo html_image($sub_cat['icon'], 'micro'); ?>
                <?php } ?>
                                        </span>
                                        <a href="<?php echo href_to('forum', $sub_cat['slug']); ?>"><?php html($sub_cat['title']); ?></a><?php if (empty($sub_cat['as_folder'])) { ?> <span class="sub-category-title-counter">(<?php echo $sub_cat['threads_count']; ?>/<?php echo $sub_cat['posts_count']; ?>)</span><?php } ?>
                                    </div>
            <?php } ?>
                            </div>

                        </td>

                    </tr>

        <?php } ?>

    <?php } ?>

<?php } ?>
    </tbody>

</table>
