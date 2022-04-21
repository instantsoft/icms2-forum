<div class="icms-forum__threads container-fluid">

    <div class="row py-3 bg-light icms-forum__cat font-weight-bold">
        <div class="col-sm-6 col-xs-12">
            <?php echo LANG_FORUM_THREADS; ?>
        </div>
        <div class="col-sm-3 d-none d-lg-block">
            <?php echo LANG_FORUM_STATS; ?>
        </div>
        <div class="col-sm-3 d-none d-lg-block">
            <?php echo LANG_FORUM_LAST_POST; ?>
        </div>
    </div>

    <?php foreach ($threads as $thread) { ?>

        <?php
        $icon = '';
        $title_icon = '';
        if ($thread['is_deleted']) {
            $icon = 'trash';
            $title_icon = LANG_FORUM_TOPIC_DELETED_PREFIX;
        } elseif ($thread['is_pinned']) {
            $icon = 'thumbtack';
            $title_icon = LANG_FORUM_ATTACHED_THREAD;
        } elseif ($thread['is_closed']) {
            $icon = 'lock';
            $title_icon = LANG_FORUM_THREAD_IS_CLOSE;
        } elseif ($thread['is_new']) {
            $icon = 'crosshairs';
            $title_icon = LANG_FORUM_HAVE_NEW_MESS;
        }
        ?>

        <div class="row align-items-center py-3 icms-forum__threads_list<?php if ($thread['is_vip']) { ?> icms-forum__threads_list_vip<?php } ?><?php if ($thread['is_new']) { ?> icms-forum__threads_list_is_new<?php } ?><?php if ($thread['is_deleted']) { ?> icms-forum__threads_list_is_deleted<?php } ?>" id="thread-<?php echo $thread['id']; ?>">

            <div class="col-sm-12 col-lg-6">
                <div class="d-flex align-items-center">
                    <div>
                        <a href="<?php echo href_to_profile($thread['user']); ?>" class="icms-user-avatar mr-2 mr-md-3 small <?php if (!empty($thread['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                            <?php if($thread['user']['avatar']){ ?>
                                <?php echo html_avatar_image($thread['user']['avatar'], 'micro', $thread['user']['nickname']); ?>
                            <?php } else { ?>
                                <?php echo html_avatar_image_empty($thread['user']['nickname'], 'avatar__mini'); ?>
                            <?php } ?>
                        </a>
                    </div>
                    <div>
                        <h3 class="h6 mb-1 icms-forum__threads_list-preview"<?php if ($options['preview_thread']) { ?> title="..." data-thread_id="<?php echo $thread['id']; ?>"<?php } ?>>
                            <?php if ($icon) { ?>
                                <a class="mr-2 text-decoration-none icms-thread-icon icms-thread-icon__<?php echo $icon; ?>" href="<?php echo $thread['is_new'] ? href_to('forum', 'newpost', [$thread['id']]) : href_to('forum', 'pfind', [$thread['last_post']['id']]); ?>" title="<?php html($title_icon); ?>" rel="nofollow" data-toggle="tooltip" data-placement="top">
                                    <?php html_svg_icon('solid', $icon); ?>
                                </a>
                            <?php } ?>
                            <a href="<?php echo href_to('forum', $thread['slug'] . '.html'); ?>" title="<?php html($thread['title']); ?>">
                                <?php html($thread['title']); ?>
                            </a>
                        </h3>
                        <div class="small text-muted">
                            <?php echo LANG_FORUM_THREAD_CREATED; ?>
                            <a href="<?php echo href_to_profile($thread['user']); ?>" class="text-secondary">
                                <?php echo $thread['user']['nickname']; ?>
                            </a>,
                            <?php echo string_date_format($thread['date_pub'], true); ?>
                        </div>
                    </div>
                </div>
                <?php if ($thread['description']) { ?>
                    <p class="icms-forum__threads_list_desc text-muted m-0 mt-2"><?php html($thread['description']); ?></p>
                <?php } ?>
            </div>
            <div class="col-sm-12 col-lg-3">
                <div class="py-2 py-lg-0 small">
                    <b class="d-inline-block d-lg-block"><?php echo html_spellcount($thread['hits'], LANG_HITS_SPELL); ?></b>
                    <b class="d-inline-block d-lg-none"> / </b>
                    <b class="d-inline-block d-lg-block"><?php echo html_spellcount($thread['answers'], LANG_FORUM_SPELL_ANSW); ?></b>
                </div>
            </div>
            <div class="col-sm-12 col-lg-3 small">
                <?php if (!empty($thread['last_post'])) { ?>
                    <span class="d-inline d-lg-none">
                        <?php echo LANG_FORUM_LAST_POST; ?>
                    </span>
                    <span>
                        <?php echo LANG_FORUM_FROM; ?>
                    </span>
                    <a href="<?php echo href_to_profile($thread['last_post']['user']); ?>" title="<?php html($thread['last_post']['user']['nickname']); ?>">
                        <?php html($thread['last_post']['user']['nickname']); ?>
                    </a>
                    <a href="<?php echo href_to('forum', 'pfind', [$thread['last_post']['id']]); ?>" title="<?php echo LANG_FORUM_GO_LAST_POST; ?>" rel="nofollow" data-toggle="tooltip" data-placement="top">
                        <?php echo string_date_age_max($thread['last_post']['date_pub'], true); ?>
                        <?php html_svg_icon('solid', 'arrow-right'); ?>
                    </a>
                <?php } else { ?>
                    <?php echo LANG_FORUM_NOT_POSTS; ?>
                <?php } ?>
            </div>

        </div>
    <?php } ?>

</div>

<?php if ($options['preview_thread']) { ob_start(); ?>
    <script>
        $(function(){
            $('.icms-forum__threads_list-preview').tooltip({
                 placement: "top",
                 delay: {show: 500, hide: 100},
                 html: true
            }).on('show.bs.tooltip', function () {
                var el = this;
                $.post('<?php echo href_to('forum', 'first-post'); ?>', {thread_id: $(this).data('thread_id')}, function (result) {
                    $(el).attr('title', result.content);
                    $(el).tooltip('dispose');
                    $(el).tooltip({
                        html: true
                    }).tooltip('show');
                }, 'json');

            });
        });
    </script>
    <?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>