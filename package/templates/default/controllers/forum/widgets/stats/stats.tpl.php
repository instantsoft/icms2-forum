<?php $this->addCSS($this->getStylesFileName('forum')); ?>

<div class="widget-stats">

    <?php foreach ($counter as $data) { ?>
        <?php if(!$data['show']){ continue; } ?>

        <h4><?php echo $data['title']; ?></h4>
        <ul class="list-unstyled">
        <?php foreach ($data['counters'] as $counter) { ?>
            <li class="">
                <?php echo $counter['title']; ?>
                <span class="badge badge-primary badge-pill"><?php echo $counter['count']; ?></span>
            </li>
        <?php } ?>
        </ul>
    <?php } ?>

    <?php if (!empty($moderators)) { ?>
        <h4><?php echo LANG_WD_FORUM_STATS_MODERATORS; ?></h4>
        <div class="widget-stats-moderators">
            <?php foreach ($moderators as $moderator) { ?>
                <div class="widget-stats-moderator<?php if (!empty($moderator['is_admin'])) { ?> moderator-admin<?php } ?>">
                    <a href="<?php echo href_to('users', $moderator['id']); ?>">
                        <?php echo html_avatar_image($moderator['avatar'], 'micro', $moderator['nickname'], true); ?><span class="widget-stats-moderator-nickname"><?php html($moderator['nickname']); ?></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>