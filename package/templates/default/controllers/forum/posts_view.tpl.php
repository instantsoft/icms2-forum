<?php $is_mobile = (bool) cmsRequest::getDeviceType() === 'mobile'; ?>

<table class="posts-table" cellspacing="0" cellpadding="0">

    <?php foreach ($posts as $id => $post) { ?>

        <?php $post_num = $post['is_first'] ? 1 : $num; ?>

        <?php $post_is_deleted = !empty($post['is_deleted']) ? true : false; ?>

        <tr class="posts-header">

            <td colspan="2" class="posts-table-header<?php if (!empty($thread['is_vip'])) { ?> vip-thread-table-header<?php } ?><?php if ($post_is_deleted) { ?> posts-table-header-is-deleted<?php } ?>">

                <div id="post_<?php echo $post['id']; ?>" class="post-date">

                    <?php if ($post['is_pinned'] && $post_num > 1) { ?>

                        <span class="post-is-pinned" title="<?php echo LANG_FORUM_ATTACHED_MESSAGE; ?>"></span>

                    <?php } ?>

                    <strong>
                        <a class="post-finder" href="<?php echo href_to('forum ', 'pfind', array($post['id'])); ?>" rel="nofollow">#<?php echo $post_num; ?></a>
                    </strong> - <?php echo string_date_format($post['date_pub'], true); ?>

                </div>

                <?php if (empty($thread['is_closed'])) { ?>

                    <?php if (!empty($post['actions'])){ ?>
                    <div class="post-links">
                        <?php foreach($post['actions'] as $action){ ?>
                            &nbsp| <a <?php echo $action['class']; ?>" href="<?php echo $action['href']; ?>" title="<?php html($action['title']); ?>" <?php if (!empty($action['confirm'])) { ?>onclick="return confirm('<?php html($action['confirm']); ?>');"<?php } ?>><?php echo $action['title']; ?></a>
                        <?php } ?>
                    </div>
                    <?php } ?>

                <?php } ?>

            </td>

        </tr>

        <tr class="posts-contents<?php if ($post_is_deleted) { ?> posts-contents-is-deleted<?php } ?>">

            <?php if (!$is_mobile) { ?>

                <td class="post-user-cell">

                    <div class="post-user-link">

                        <a href="#" onclick="icms.forum.addNickname( this ); return false;" title="<?php echo LANG_FORUM_ADD_NICKNAME; ?>" rel="<?php html($post['user']['nickname']); ?>"><?php html($post['user']['nickname']); ?></a>

                    </div>

                    <div class="post-user-avatar">

                        <a href="<?php echo href_to_profile($post['user']); ?>" title="<?php echo LANG_FORUM_GOTO_PROFILE; ?>">
                            <?php echo html_avatar_image($post['user']['avatar'], $user_avatar_size, $post['user']['nickname'], true); ?>
                        </a>

                    </div>

                    <?php if (!empty($options['show_users_groups']) && !empty($post['user_groups']) && !empty($users_groups)) { ?>

                        <div class="post-user-groups">

                            <div class="post-user-groups-title"><?php echo LANG_FORUM_USER_GROUPS; ?></div>

                            <?php foreach ($post['user_groups'] as $group_id) { ?>
                                <div class="post-user-group post-user-group-<?php echo $users_groups[$group_id]['name']; ?>"><?php echo $users_groups[$group_id]['title']; ?></div>
                            <?php } ?>

                        </div>

                    <?php } ?>

                    <ul class="details">

                        <li><strong><?php echo LANG_FORUM_MESSAGES; ?>:</strong> <?php echo $post['user']['forum_posts_count']; ?></li>

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
                        <li>
                            <b><?php echo LANG_FORUM_MESSAGES; ?>:</b> <?php echo $post['user']['forum_posts_count']; ?>
                        </li>

                        <?php if (!$post['is_online']) { ?>
                            <strong><?php echo LANG_FORUM_AUTHOR_LOGDATE; ?>:</strong><br /> <?php echo string_date_format($post['user_date_log'], true); ?>
                        <?php } else { ?>
                            <span class="online"><?php echo LANG_ONLINE; ?></span>
                        <?php } ?>

                    </ul>
                </td>

                <td class="post-content-cell<?php if ($post['modified_count']) { ?> edited<?php } ?><?php if ($post['rating'] < 0) { ?> is_bad<?php } ?><?php if ($post_is_deleted) { ?> posts-contents-is-deleted<?php } ?>">

                <?php } else { ?>

                <td class="post-user-cell-mobile">

                    <div class="post-user-avatar-mobile">

                        <a href="/users/<?php echo $post['user_id']; ?>" title="<?php echo LANG_FORUM_GOTO_PROFILE; ?>">

                            <?php echo html_avatar_image($post['user_avatar'], 'small', LANG_FORUM_GOTO_PROFILE); ?>

                        </a>

                    </div>

                </td>

                <td class="post-user-cell-mobile<?php if ($post_is_deleted) { ?> posts-contents-is-deleted<?php } ?>">

                    <div class="post-user-link-mobile">

                        <a href="#" onclick="icms.forum.addNickname( this ); return false;" title="<?php echo LANG_FORUM_ADD_NICKNAME; ?>" rel="<?php html($post['user_nickname']); ?>"><?php html($post['user_nickname']); ?></a>

                    </div>

                    <ul class="details_mobile">

                        <?php if ($user_fields) { ?>

                            <?php foreach ($user_fields as $key => $field) { ?>

                                <?php $field['name'] = 'user_' . $field['name']; ?>

                                <?php if (empty($post[$field['name']])) { continue; } ?>

                                <?php $field['handler']->name = $field['name']; ?>

                                <li><strong><?php echo $field['title']; ?>:</strong> <?php echo $field['handler']->setItem($post)->getStringValue($post[$field['name']]); ?></li>

                            <?php } ?>

                        <?php } ?>

                        <?php if (!$post['is_online']) { ?>
                            <strong><?php echo LANG_FORUM_AUTHOR_LOGDATE; ?>:</strong> <?php echo string_date_format($post['user_date_log'], true); ?>
                        <?php } else { ?>
                            <span class="online"><?php echo LANG_ONLINE; ?></span>
                        <?php } ?>

                    </ul>
                </td>

            </tr>
            <tr>
                <td colspan="2" class="post-content-cell-mobile<?php if ($post['rating'] < 0) { ?> is_bad<?php } ?>">
                <?php } ?>
                <?php if (!empty($post['rating_widget'])) { ?>

                    <div class="votes-links"><?php echo $post['rating_widget']; ?></div>

                <?php } ?>

                <div id="post_content_<?php echo $post['id']; ?>" class="post-content">

                    <?php if (!$post['is_hidden']) { ?>

                        <?php echo $post['content_html']; ?>

                    <?php } else { ?>

                        <a onclick="icms.forum.viewPost( '<?php echo href_to('forum', 'post_view_ajax', $post['id']); ?>', <?php echo $post['id']; ?> ); return false;" href="#" class="ajaxlink" title="<?php echo LANG_FORUM_VIEW; ?>"><?php echo LANG_FORUM_VIEW; ?></a>

                    <?php } ?>

                </div>

                <?php if ($post['files']) { ?>

                    <div id="attached_files_<?php echo $post['id']; ?>">

                        <div class="fa_attach" id="fa_attach_<?php echo $post['id']; ?>">

                            <div class="fa_attach_title"><?php echo LANG_FORUM_ATTACHED_FILE; ?>:</div>

                            <div class="fa_filebox" id="filebox<?php echo $post['files']['id']; ?>">

                                <div class="fa_file">

                                    <a class="fa_file_link" href="<?php echo href_to('files', 'download', array($post['files']['id'], files_user_file_hash($post['files']['path']))); ?>"><?php html($post['files']['name']); ?></a>

                                    <span class="fa_file_desc"> |
                                        <?php echo files_format_bytes($post['files']['size']); ?>
                                    </span>

                                    <?php if ((!empty($post['is_mypost']) && $is_can_attach)) { ?>

                                        <a class="fa_file_delete" href="#" title="<?php echo LANG_FORUM_DELETE_FILE; ?>" onclick="if ( confirm( '<?php echo LANG_FORUM_DELETE_FILE; ?>' ) ) { icms.forum.deleteFile( '<?php echo href_to('forum', 'attach_delete', array($post['id'], $post['files']['id'])); ?>', <?php echo $post['id']; ?> ); return false; }"></a>

                                    <?php } ?>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php } ?>

                <?php if ($post['modified_count']) { ?>

                    <div class="post-edit-date">

                        <?php echo LANG_FORUM_EDITED; ?>: <?php echo html_spellcount($post['modified_count'], LANG_FORUM_COUNT1, LANG_FORUM_COUNT2, LANG_FORUM_COUNT10); ?> (<?php echo LANG_FORUM_LAST_EDIT; ?>: <?php echo string_date_format($post['date_last_modified'], true); ?>)

                    </div>

                <?php } ?>

                <?php if (!empty($post['flood_time'])) { ?>

                    <div class="post-auto-deleted">

                        <?php echo LANG_FORUM_AUTODELETED; ?>: <?php echo html_date_time($post['flood_time']); ?>

                    </div>

                <?php } ?>

                <?php if (!empty($post['from_thread_id'])) { ?>

                    <div class="post-from-thread"><?php echo LANG_FORUM_POST_FROM_THREAD; ?> <?php echo html_link($post['from_thread_title'], href_to('forum', $post['from_thread_slug'] . '.html')); ?></div>

                <?php } ?>

                <?php if ($post['user']['forum_sign']) { ?>

                    <div class="post-user-signature"><?php echo $post['user']['forum_sign']; ?></div>

                <?php } ?>

            </td>

        </tr>

        <?php

        $num = ($post['is_first'] && $page > 1) ? $num : $num + 1;
    } ?>

</table>
