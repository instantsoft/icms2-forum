<?php foreach ($posts as $post) { ?>
<?php $post_num = $post['is_first'] ? 1 : $num; ?>
<article class="row no-gutters mb-3 icms-forum__post<?php if (!empty($post['is_deleted'])) { ?> icms-forum__post-is_deleted<?php } ?><?php if ($post['rating'] < 0) { ?> icms-forum__post-is_bad<?php } ?>" id="post-<?php echo $post['id']; ?>" data-author="<?php html($post['user']['nickname']); ?>">
    <div class="col-sm-10 order-1 rounded shadow-sm position-relative d-flex flex-column icms-forum__post-data">
        <div class="icms-forum__post-header rounded-top px-3 py-2 d-flex align-items-center">
            <div class="icms-forum__post-header_info small icms-dot-between icms-links-inherit-color">
                <?php if ($post['is_pinned'] && $post_num > 1) { ?>
                    <span class="post-is-pinned" title="<?php echo LANG_FORUM_POST_IS_PINNED; ?>" data-toggle="tooltip" data-placement="top">
                        <?php html_svg_icon('solid', 'thumbtack'); ?>
                    </span>
                <?php } ?>
                <?php if($post['id']){ ?>
                    <span>
                        <a class="font-weight-bold text-decoration-none" href="<?php echo href_to('forum ', 'pfind', [$post['id']]); ?>" rel="nofollow">
                            #<?php echo $post_num; ?>
                        </a>
                    </span>
                <?php } ?>
                <?php if (!empty($post['title'])) { ?>
                    <span>
                        <a class="" href="<?php echo href_to('forum', 'pfind', $post['id']); ?>" rel="nofollow">
                            <?php html($post['title']); ?>
                        </a>
                    </span>
                <?php } ?>
                <span>
                    <?php html_svg_icon('solid', 'history'); ?>
                    <?php echo string_date_format($post['date_pub'], true); ?>
                </span>
                <?php if ($post['modified_count']) { ?>
                    <span data-toggle="tooltip" data-placement="top" title="<?php echo LANG_FORUM_EDITED; ?>: <?php echo html_spellcount($post['modified_count'], LANG_TIME1, LANG_TIME2, LANG_TIME10); ?>, <?php echo LANG_FORUM_LAST_EDIT; ?> <?php echo string_date_format($post['date_last_modified'], true); ?>">
                        <?php html_svg_icon('solid', 'pen'); ?>
                    </span>
                <?php } ?>
                <?php if (!empty($post['flood_time'])) { ?>
                    <span data-toggle="tooltip" data-placement="top" title="<?php echo LANG_FORUM_AUTODELETED; ?> <?php echo mb_strtolower(string_date_format($post['flood_time'], true)); ?>">
                        <b class="text-danger"><?php html_svg_icon('solid', 'pepper-hot'); ?></b>
                    </span>
                <?php } ?>
                <?php if (!empty($post['from_thread_id'])) { ?>
                    <span>
                        <a class="text-decoration-none" href="<?php echo href_to('forum', $post['from_thread_slug'] . '.html'); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo LANG_FORUM_POST_FROM_THREAD; ?> <?php html($post['from_thread_title']); ?>">
                            <?php html_svg_icon('solid', 'map-marker-alt'); ?>
                        </a>
                    </span>
                <?php } ?>
            </div>
            <?php if (!empty($post['actions'])){ ?>
            <div class="dropdown ml-auto">
                <button class="btn btn-sm my-n2" type="button" data-toggle="dropdown">
                    <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <?php foreach($post['actions'] as $action){ ?>
                        <a class="dropdown-item <?php echo $action['class']; ?>" href="<?php echo $action['href']; ?>" title="<?php html($action['title']); ?>" <?php if (!empty($action['confirm'])) { ?>onclick="return confirm('<?php html($action['confirm']); ?>');"<?php } ?>>
                            <?php if (!empty($action['icon'])){ ?>
                                <?php html_svg_icon('solid', $action['icon']); ?>
                            <?php } ?>
                            <?php echo $action['title']; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="p-2 p-sm-3">
            <div class="icms-forum__post-content text-break">
                <?php if (!$post['is_hidden']) { ?>
                    <?php echo $post['content_html']; ?>
                <?php } else { ?>
                    <?php echo LANG_FORUM_POST_IS_HIDDEN; ?>
                    <a onclick="return icms.forum.viewPost('<?php echo href_to('forum', 'post_view_ajax', $post['id']); ?>', <?php echo $post['id']; ?>);" href="#" class="btn btn-outline-secondary">
                        <span><?php echo LANG_FORUM_VIEW; ?></span>
                    </a>
                <?php } ?>
            </div>
            <?php if ($post['files']) { ?>
                <div class="icms-forum__post-files" id="icms-forum-post-attach-<?php echo $post['id']; ?>">
                    <div class="text-muted small">
                        <?php echo LANG_FORUM_ATTACHED_FILE; ?>
                    </div>
                    <div class="icms-forum__post-files-file icms-dot-between text-muted">
                        <span>
                            <a class="" href="<?php echo href_to('files', 'download', [$post['files']['id'], files_user_file_hash($post['files']['path'])]); ?>">
                                <?php html_svg_icon('solid', 'file-'.$post['files']['icon']); ?>
                                <?php html($post['files']['name']); ?>
                            </a>
                        </span>
                        <span>
                            <?php echo files_format_bytes($post['files']['size']); ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="icms-forum__post-footer d-flex align-items-center px-3 py-2 mt-auto">
            <?php if ($post['user']['forum_sign']) { ?>
                <div class="icms-forum__post-footer_signature text-muted small">
                    <?php echo $post['user']['forum_sign']; ?>
                </div>
            <?php } ?>
            <?php if (!empty($post['info_bar'])){ ?>
                <div class="icms-forum__post-footer_bar ml-auto flex-shrink-0 d-flex">
                    <?php foreach($post['info_bar'] as $bar){ ?>
                        <div class="icms-forum__post-footer_bar_item text-secondary position-relative <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                            <?php if (!empty($bar['icon'])){ ?>
                                <?php html_svg_icon('solid', $bar['icon']); ?>
                            <?php } ?>
                            <?php if (!empty($bar['href'])){ ?>
                                <a class="stretched-link" href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                            <?php } else { ?>
                                <?php echo $bar['html']; ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <aside class="col-sm-2 order-0 pb-3 pr-sm-3 text-center">
        <div class="mb-2">
            <?php if(!empty($post['user']['id'])){ ?>
                <a href="<?php echo href_to_profile($post['user']); ?>" class="icms-user-avatar small <?php if (!empty($post['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                    <?php if($post['user']['avatar']){ ?>
                        <?php echo html_avatar_image($post['user']['avatar'], $user_avatar_size, $post['user']['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($post['user']['nickname'], 'avatar__inlist'); ?>
                    <?php } ?>
                </a>
            <?php } else { ?>
                <span class="icms-user-avatar small peer_no_online">
                    <?php if($post['user']['avatar']){ ?>
                        <?php echo html_avatar_image($post['user']['avatar'], $user_avatar_size, $post['user']['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($post['user']['nickname'], 'avatar__inlist'); ?>
                    <?php } ?>
                </span>
            <?php } ?>
        </div>
        <h5 class="text-truncate">
            <?php if($post['id']){ ?>
                <a href="#" onclick="return icms.forum.addNickname(this);" title="<?php echo LANG_FORUM_ADD_NICKNAME; ?>">
                    <?php html($post['user']['nickname']); ?>
                </a>
            <?php } else { ?>
                <?php html($post['user']['nickname']); ?>
            <?php } ?>
        </h5>
        <ul class="list-unstyled text-muted small m-0 icms-forum__post-user_details">
            <?php foreach ($post['user_fields'] as $title => $user_field) { ?>
                <li class="mb-1">
                    <?php echo $user_field; ?>
                </li>
            <?php } ?>
            <?php if (!empty($users_groups)) { ?>
                <?php foreach ($post['user']['groups'] as $group_id) { ?>
                    <li class="mb-1 icms-forum__post-groups icms-forum__post-groups_<?php echo $users_groups[$group_id]['name']; ?>">
                        <?php echo $users_groups[$group_id]['title']; ?>
                    </li>
                <?php } ?>
            <?php } ?>
            <?php if (!empty($post['user']['forum_posts_count'])) { ?>
                <li>
                    <b><?php echo LANG_FORUM_MESSAGES; ?>:</b> <?php echo $post['user']['forum_posts_count']; ?>
                </li>
            <?php } ?>
        </ul>
    </aside>
</article>
<?php if((!$post['is_first'] || $page == 1) && $post['id']){ ++$num; } ?>
<?php } ?>