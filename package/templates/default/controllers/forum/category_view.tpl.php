<?php

$user = cmsUser::getInstance();

if ($is_rss) {
    $this->addHead('<link rel="alternate" type="application/rss+xml" title="' . LANG_FORUM_RSS_THREADS . '" href="/rss/feed/forum?view=threads&category=' . $category['id'] . '" />');
    $this->addHead('<link rel="alternate" type="application/rss+xml" title="' . LANG_FORUM_RSS_POSTS . '" href="/rss/feed/forum?view=posts&category=' . $category['id'] . '" />');
}

if ($user->id && !empty($options['preview_thread'])) {
    $this->addTplCSSNameFromContext('jBox');
    $this->addTplJSNameFromContext('jBox');
}

$this->setPageTitle(($category['seo_title'] ? $category['seo_title'] : $category['title']));
$this->setPageKeywords(($category['seo_keys'] ? $category['seo_keys'] : string_get_meta_keywords($category['title'])));
$this->setPageDescription(($category['seo_desc'] ? $category['seo_desc'] : (!empty($category['description']) ? string_get_meta_description($category['description']) : $category['title'])));

if ($is_can_add_thread) {

    $this->addToolButton(array(
        'class' => 'add',
        'icon' => 'plus-circle',
        'title' => LANG_FORUM_NEW_THREAD,
        'href'  => href_to('forum', 'thread_add', $category['id']),
    ));
}

if ($user->id) {

    $this->addToolButton(array(
        'class' => 'my-threads',
        'icon'  => 'address-book',
        'title' => LANG_FORUM_MY_THREADS,
        'href'  => href_to('forum', 'my_threads'),
    ));

    $this->addToolButton(array(
        'class' => 'my-posts',
        'icon'  => 'address-card',
        'title' => LANG_FORUM_MY_POSTS,
        'href'  => href_to('forum', 'my_posts'),
    ));

    $this->addToolButton(array(
        'class' => 'threads',
        'icon'  => 'newspaper',
        'title' => LANG_FORUM_NEW_THREADS,
        'href'  => href_to('forum', 'latest_threads'),
    ));

    $this->addToolButton(array(
        'class' => 'posts',
        'icon'  => 'file-alt',
        'title' => LANG_FORUM_LATEST_POSTS,
        'href'  => href_to('forum', 'latest_posts'),
    ));
}

if ($user->is_admin) {

    $this->addToolButton(array(
        'class' => 'folder_edit',
        'icon'  => 'pen-square',
        'title' => LANG_FORUM_CAT_EDIT,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum', 'category_edit', $category['id'])),
    ));

    $this->addToolButton(array(
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum')),
    ));
}

$odd = 0;
$is_odd = false;

?>

<?php if ($is_rss && !empty($options['is_rss']) && empty($category['as_folder'])) { ?>
    <div class="forum_rss_icon">
        <a class="rss_icon" href="/rss/feed/forum?view=threads&category=1" title="<?php echo LANG_FORUM_RSS_THREADS; ?>"><?php echo LANG_FORUM_RSS_THREADS; ?></a>
        <a class="rss_icon" href="/rss/feed/forum?view=posts&category=1" title="<?php echo LANG_FORUM_RSS_POSTS; ?>"><?php echo LANG_FORUM_RSS_POSTS; ?></a>
    </div>
<?php } ?>

<h1><?php html($category['title']); ?></h1>

<?php if (!empty($category['description']) || !empty($category['icon'])) { ?>

    <div class="category-view-description">
            <?php if (!empty($category['icon'])) { ?>
            <div class="category-view-icon">
            <?php echo html_image($category['icon'], 'small'); ?>
            </div>
        <?php } ?>

            <?php if (!empty($category['description'])) { ?>
            <div class="category-view-desc">
            <?php html($category['description']); ?>
            </div>
    <?php } ?>
    </div>

<?php } ?>

<?php if (!empty($category['childs_path'])) { ?>

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

            <?php $level = $category['childs_path'][0]['ns_level']; ?>

            <?php foreach ($category['childs_path'] as $id => $cat) { ?>

                <?php $odd ++;
                $is_odd = $odd % 2 == 0 ? true : false; ?>

                <?php $is_parent = $level == 1 ? ' is_first_parent' : ''; ?>

                <?php

                if (!empty($cat['as_folder'])) {
                    $odd = 0;
                }

                ?>

        <?php if ($cat['ns_level'] == $level) { ?>

                    <tr id="category-<?php echo $cat['id']; ?>" class="category_view<?php if (!empty($cat['as_folder'])) { ?> category-as-folder<?php } ?><?php echo $is_parent; ?><?php if ($is_odd) { ?> odd<?php } ?>">

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
                                    <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>">
                <?php html($cat['title']); ?>
                                    </a>
                                </div>
                            </td>

            <?php } else { ?>

                            <td class="category-ceil">
                                <div class="category-icon<?php if (empty($cat['icon'])) { ?> default_icon<?php } ?>">
                                    <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>">
                                        <?php if (!empty($cat['icon'])) { ?>
                    <?php echo html_image($cat['icon'], 'micro'); ?>
                <?php } ?>
                                    </a>
                                </div>
                                <div class="category-title">
                                    <a href="<?php echo href_to('forum', (!empty($cat['slug_key']) ? $cat['slug_key'] : $cat['slug'])); ?>">
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

                                        <span class="last-post-date"><?php echo LANG_FORUM_FROM; ?></span>
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

            <?php if (!empty($cat['sub_forums_cats'])) { ?>

                        <tr id="category-<?php echo $cat['id']; ?>" class="sub-category<?php if ($is_odd) { ?> odd<?php } ?>">

                            <td colspan="4" class="sub-category-ceil">

                                <div class="sub-category-header"><?php echo LANG_FORUM_SUBFORUMS_CATS; ?></div>

                                <div class="sub-category-titles">
                <?php foreach ($cat['sub_forums_cats'] as $key => $sub_cat) { ?>

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
    <br />
<?php } ?>

<?php if (empty($category['as_folder'])) { ?>

    <?php if (!$threads) { ?>
        <div class="alert alert-info mt-4 alert-list-empty">
            <?php echo LANG_FORUM_NOT_THREADS_IN_FORUM; ?>
        </div>
    <?php } else { ?>

        <?php echo $this->renderChild('threads', [
            'user'    => $user,
            'category' => $category,
            'threads' => $threads,
            'options' => $options,
            'is_can_add_thread' => $is_can_add_thread
        ]); ?>

        <?php echo html_pagebar($page, $perpage, $total, href_to('forum', $category['slug'])); ?>

    <?php } ?>

<?php } ?>
