<?php $this->addCSS($this->getStylesFileName('forum')); ?>

<div class="icms-forum__widget-stats">

    <?php foreach ($counter as $data) { ?>
        <?php if(!$data['show']){ continue; } ?>

        <h5><?php echo $data['title']; ?></h5>
        <ul class="list-unstyled">
        <?php foreach ($data['counters'] as $counter) { ?>
            <li class="">
                <?php echo $counter['title']; ?>
                <span class="badge badge-primary badge-pill"><?php echo $counter['count']; ?></span>
            </li>
        <?php } ?>
        </ul>
    <?php } ?>

    <?php if ($moderators) { ?>
        <?php if ($show_moder_caption) { ?>
            <h5><?php echo LANG_WD_FORUM_STATS_MODERATORS; ?></h5>
        <?php } ?>
        <div class="icms-forum__widget-stats-moderators d-flex flex-wrap mr-n2 mb-n2<?php if ($show_moder_caption) { ?> mt-3<?php } ?>">
            <?php foreach ($moderators as $moderator) { ?>
                <a href="<?php echo href_to_profile($moderator); ?>" class="icms-user-avatar mb-2 mr-2 small <?php if (!empty($post['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>" title="<?php html($moderator['nickname']); ?>" data-toggle="tooltip" data-placement="top">
                    <?php if($moderator['avatar']){ ?>
                        <?php echo html_avatar_image($moderator['avatar'], 'micro', $moderator['nickname']); ?>
                    <?php } else { ?>
                        <?php echo html_avatar_image_empty($moderator['nickname'], 'avatar__mini'); ?>
                    <?php } ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

</div>