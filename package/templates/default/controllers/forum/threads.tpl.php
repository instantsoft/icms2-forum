
    <?php if (!empty($options['fix_threads_reads']) && !empty($category['options']['fix_threads_reads']) && !empty($threads)) { ?>
        <div class="all-threads-view">
            <a class="ajaxlink" href="<?php echo href_to('forum', 'all_threads_view', $category['id']); ?>"><?php echo LANG_FORUM_ALL_THREADS_VIEW; ?></a>
        </div>
    <?php } ?>

    <?php if ($is_can_add_thread) { ?>
        <div class="new_thread_button">
            <a href="<?php echo href_to('forum', 'thread_add', $category['id']); ?>"><?php echo LANG_FORUM_NEW_THREAD; ?></a>
        </div>
    <?php } ?>

    <div id="threads-list">

        <table class="threads-table" cellspacing="0" cellpadding="0">

            <thead class="threads-table-header">

                <tr>
                    <td class="header-thread"><?php echo LANG_FORUM_THREADS; ?></td>
                    <td class="header-stats"><?php echo LANG_FORUM_STATS; ?></td>
                    <td class="header-last-post"><?php echo LANG_FORUM_LAST_POST; ?></td>
                </tr>

            </thead>

            <tbody>

                <?php if (!empty($threads)) { ?>

                    <?php $odd = 0;
                    $is_odd = false; ?>

                    <?php foreach ($threads as $id => $thread) { ?>

            <?php $thread_is_deleted = !empty($thread['is_deleted']) ? true : false; ?>

            <?php $odd ++;
            $is_odd = $odd % 2 == 0 ? true : false; ?>

                        <tr id="<?php echo $id; ?>" class="thread_view<?php if ($is_odd) { ?> odd<?php } ?><?php if ($thread['is_vip']) { ?> vip-thread<?php } ?><?php if ($thread_is_deleted) { ?> thread-is-deleted<?php } ?>">

                            <td class="threads-table-links">

                                <div class="thread-icon">

                                    <?php

                                    $class_icon = 'old';
                                    $title_icon = LANG_FORUM_NOT_NEW_MESS;

                                    if ($thread['is_pinned']) {
                                        $class_icon = 'pinned';
                                        $title_icon = LANG_FORUM_ATTACHED_THREAD;
                                    } elseif ($thread['is_closed']) {
                                        $class_icon = 'closed';
                                        $title_icon = LANG_FORUM_THREAD_IS_CLOSE;
                                    }

                                    ?>

                                            <?php if ($thread['is_new']) { ?>
                                        <a class="thread-icon-new" href="<?php echo href_to('forum', 'newpost', array($thread['id'])); ?>" title="<?php echo LANG_FORUM_HAVE_NEW_MESS; ?>">
            <?php } else { ?>
                                            <a class="thread-icon-<?php echo $class_icon; ?>" href="<?php echo href_to('forum', 'pfind', array($thread['last_post']['id'])); ?>" title="<?php echo $title_icon; ?>" rel="nofollow">
            <?php } ?>
                                        </a>

                                </div>

                                <div class="thread-link<?php if ($thread['is_new']) { ?> is_new_thread<?php } ?>">

            <?php if ($thread['is_new']) { ?>
                                        <a class="thread-icon-gotonewpost" href="<?php echo href_to('forum', 'newpost', array($thread['id'])); ?>" title="<?php echo LANG_FORUM_HAVE_NEW_MESS; ?>"></a>
                                <?php } ?>
                                    <a href="<?php echo href_to('forum', $thread['slug'] . '.html'); ?>" rel="<?php echo $thread['id']; ?>" title="<?php html($thread['title']); ?>"><?php html($thread['title']); ?></a>

                                </div>

                                <?php if ($user->id && !empty($options['preview_thread'])) { ?>
                                    <div class="thread-preview" rel="<?php echo $thread['id']; ?>"></div>
                                    <div id="jbox_<?php echo $thread['id']; ?>" style="display:none"></div>
                                <?php } ?>

            <?php if ($thread['description']) { ?>
                                    <div class="thread-desc"><?php html($thread['description']); ?></div>
            <?php } ?>

                                <div class="thread-info">

                                    <div class="thread-info-avatar">
                                        <a href="<?php echo href_to('users', $thread['user']['id']); ?>" title="<?php html($thread['user']['nickname']); ?>">
            <?php echo html_avatar_image($thread['user']['avatar'], 'micro'); ?>
                                        </a>
                                    </div>

                                    <span class="thread-info-date"><?php echo LANG_FORUM_THREAD_CREATED; ?></span>
                                    <span class="thread-info-nickname"><a href="/users/<?php echo $thread['user']['id']; ?>"><?php html($thread['user']['nickname']); ?></a></span>,
                                    <span class="thread-info-date"><?php echo string_date_format($thread['date_pub'], true); ?></span>
                                </div>

                            </td>

                            <td class="threads-table-stats">
                                <span class="threads-table-stats-title"><?php echo LANG_FORUM_HITS; ?>:</span><span class="threads-table-stats-count"> <?php echo $thread['hits']; ?></span><br/>
                                <span class="threads-table-stats-title"><?php echo LANG_FORUM_REPLIES; ?>:</span><span class="threads-table-stats-count"> <?php html($thread['answers']); ?></span>
                            </td>

                            <td class="threads-table-last-post">

            <?php if (!empty($thread['last_post'])) { ?>

                                    <span class="last-post-date"><?php echo LANG_FORUM_FROM; ?></span>
                                    <span class="last-post-user"><a href="<?php echo href_to('users', $thread['last_post']['user']['id']); ?>" title="<?php html($thread['last_post']['user']['nickname']); ?>"><?php html($thread['last_post']['user']['nickname']); ?></a></span>
                                    <span class="last-post-date"><?php echo string_date_format($thread['last_post']['date_pub'], true); ?></span>
                                    <a class="go-last-post" href="<?php echo href_to('forum', 'pfind', array($thread['last_post']['id'])); ?>" title="<?php echo LANG_FORUM_GO_LAST_POST; ?>" rel="nofollow"></a>

            <?php } else { ?>
                <?php echo LANG_FORUM_NOT_POSTS; ?>
            <?php } ?>

                            </td>

                        </tr>

        <?php } ?>

                        <?php } else { ?>

                    <tr>
                        <td colspan="4" class="not-threads">
                    <?php echo LANG_FORUM_NOT_THREADS_IN_FORUM; ?>
                        </td>
                    </tr>

        <?php } ?>

            </tbody>
        </table>

    </div>

        <?php if ($is_can_add_thread) { ?>
        <div class="new_thread_button">
            <a href="<?php echo href_to('forum', 'thread_add', $category['id']); ?>"><?php echo LANG_FORUM_NEW_THREAD; ?></a>
        </div>
    <?php } ?>

    <?php if ($user->id && !empty($options['preview_thread'])) { ob_start(); ?>
        <script>
            $( document ).ready( function () {
                $( '.thread-preview' ).on( 'click, mouseover', function () {
                    let element = $( this );
                    let thread_id = element.attr( 'rel' );
                    element.jBox( 'Tooltip', {
                        id: 'jbox_' + thread_id,
                        ajax: {
                            method: 'POST',
                            url: '<?php echo href_to('forum', 'first-post'); ?>',
                            data: {
                                thread_id: thread_id
                            },
                            setContent: false,
                            dataType: 'json',
                            success: function (response) {
                                this.setContent(response.content);
                            }
                        },
                        position: {
                            x: 'right', y: 'center'
                        },
                        outside: 'x'
                    } );
                } );
            } );
        </script>
        <?php $this->addBottom(ob_get_clean()); ?>
    <?php } ?>