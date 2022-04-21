<?php $user = cmsUser::getInstance(); ?>

<div id="thread_poll" class="thread-poll">

    <div class="thread-poll-title odd">
        <?php html($thread_poll['title']); ?>
    </div>

    <?php if (!empty($thread_poll['description'])) { ?>
        <div class="thread-poll-description odd">
            <?php html($thread_poll['description']); ?>
        </div>
    <?php } ?>

    <?php if (!$thread_poll['is_can_vote']) { ?>

        <div class="odd"><?php echo sprintf(LANG_FORUM_GUESTS_NOT_VOTE, href_to('auth', 'register')); ?></div>

    <?php } else { ?>

        <?php if ($thread_poll['allow_show_result'] && $show_result == 'result' || !$thread_access->is_can_write) { ?>

            <div class="thread-poll-results">

                <div class="thread-poll-voters-count">

                    <span><?php echo $thread_poll['results']['total'] ? LANG_FORUM_TOTAL_VOTES : ''; ?> <?php echo html_spellcount($thread_poll['results']['total'], LANG_FORUM_POLL_VOICE_SPELL); ?></span>

                    <?php if ($thread_access->is_can_write && !$thread['is_closed'] && !$thread_poll['is_closed']) { ?>
                        <a class="thread-poll-comment" href="<?php echo href_to('forum', 'post_add', $thread['id']); ?>"><?php echo LANG_FORUM_COMMENT_POLL; ?></a>
                    <?php } ?>

                    <?php if ($thread_access->is_can_write) { ?>
                        <a class="button-poll-result" onclick="loadPoll('form'); return false;" href="#"><?php echo LANG_FORUM_POLL_GOTO_VOTE; ?></a>
                    <?php } ?>

                </div>

                <table class="thread-poll-results-data" cellspacing="0" cellpadding="0">

                    <tbody>
                        <?php $option_key = 1; ?>
                        <?php foreach ($thread_poll['answers'] as $num => $answer) { ?>

                            <?php if ($num == '0') {
                                continue;
                            } ?>

                            <?php
                            $is_my_answer = !empty($thread_poll['is_user_vote']) ? in_array($option_key, $thread_poll['user_answer_ids']) : false;
                            ?>

                            <tr>

                                <td class="thread-poll-option-cell">

                                    <div class="title<?php if ($is_my_answer) { ?> is_my_answer<?php } ?>"><?php echo $answer; ?></div>

                                    <div class="thread-poll-bar-container">
                                        <div class="thread-poll-bar option-<?php echo $option_key; ?><?php if ($is_my_answer) { ?> is_my_answer_bar<?php } ?>" style="width: <?php echo $thread_poll['results']['percents'][$num]; ?>%"></div>
                                    </div>

                                </td>

                                <td class="thread-poll-percent-cell">
                                    <div class="thread-poll-percentage"><?php echo $thread_poll['results']['percents'][$num]; ?>%</div>
                                </td>

                                    <?php if (!empty($thread_poll['options']['answers_is_pub']) && !empty($thread_poll['voters'])) { ?>
                                    <td>

                                        <div class="thread-poll-voters-block" title="<?php echo LANG_FORUM_POLL_PRESS_FOR_DETAIL; ?>">

                                            <div class="thread-poll-voters" style="display: none;">

                                                <?php if (!empty($thread_poll['voters'][$option_key])) { ?>

                                                    <?php foreach ($thread_poll['voters'][$option_key] as $voters) { ?>

                                                        <?php echo html_date_time($voters['date_pub']); ?>
                                                        <a href="<?php echo href_to('users', $voters['user_id']); ?>"><?php html($voters['nickname']); ?></a><br>

                                                    <?php } ?>

                                                <?php } else { ?>

                                                    <?php echo LANG_FORUM_POLL_NO_VOICE; ?>

                                                <?php } ?>

                                            </div>

                                        </div>

                                    </td>
                                <?php } ?>

                            </tr>

                            <?php $option_key++; ?>
                        <?php } ?>

                    </tbody>

                </table>

                    <div class="mt-3">
                        <span class="mr-3 small text-muted">
                            <?php echo $thread_poll['results']['total'] ? LANG_FORUM_TOTAL_VOTES : ''; ?> <?php echo html_spellcount($thread_poll['results']['total'], LANG_FORUM_POLL_VOICE_SPELL); ?>
                        </span>

                        <?php if($thread_poll['is_can_vote'] && !$thread_poll['is_closed'] && !$thread_poll['user_answer_ids']) { ?>
                            <a class="button-poll-result" onclick="loadPoll('form'); return false;" href="#"><?php echo LANG_FORUM_POLL_GOTO_VOTE; ?></a>
                        <?php } ?>

                        <?php if($thread_poll['is_closed']) { ?>
                            <span class="small text-muted">
                                <?php echo LANG_FORUM_POLL_FINISHED; ?>
                            </span>
                        <?php } ?>
                    </div>
            </div>

        <?php } else { ?>

            <div class="thread-poll-results">

                <div class="thread-poll-voters-count">

                    <span><?php echo $thread_poll['results']['total'] ? LANG_FORUM_TOTAL_VOTES : ''; ?> <?php echo html_spellcount($thread_poll['results']['total'], LANG_FORUM_POLL_VOICE_SPELL); ?></span>

                    <?php if ($thread_access->is_can_write && !$thread['is_closed'] && !$thread_poll['is_closed']) { ?>
                        <a class="thread-poll-comment" href="<?php echo href_to('forum', 'post_add', $thread['id']); ?>"><?php echo LANG_FORUM_COMMENT_POLL; ?></a>
                    <?php } ?>

                    <?php if ($thread_poll['allow_show_result']) { ?>
                        <a class="button-poll-result" onclick="loadPoll('result'); return false;" href="#"><?php echo LANG_FORUM_POLL_GOTO_RESULT; ?></a>
                    <?php } ?>

                </div>

                <table class="thread-poll-results-table" cellspacing="0" cellpadding="0">

                    <tr>

                        <td class="thread-poll-data">

                            <?php if ($thread['is_closed'] && $thread_poll['options']['result'] < 2 && empty($thread_poll['is_user_vote'])) { ?>

                                <?php echo LANG_FORUM_YOU_IS_NOT_VOTE_IN_CLOSED; ?>

                            <?php } elseif (!empty($thread_poll['is_user_vote']) && empty($thread['is_closed']) && empty($thread_poll['is_closed'])) { ?>

                                <?php echo LANG_FORUM_YOU_IS_VOTE; ?>

                            <?php } else { ?>

                                <form id="thread-poll-form" method="post" action="">
                                    <?php echo html_input('hidden', 'csrf_token', cmsForm::getCSRFToken()); ?>
                                    <?php echo html_input('hidden', 'poll_id', $thread_poll['id']); ?>

                                    <div class="form-group">
                                        <?php foreach ($thread_poll['answers'] as $answer_id => $questions) { ?>

                                            <?php $aid = 'answer-'.$answer_id; ?>
                                            <?php $type = !empty($thread_poll['options']['multi_answer']) ? 'checkbox' : 'radio'; ?>

                                            <div class="custom-control custom-<?php echo $type; ?>">
                                                <input type="<?php echo $type; ?>" id="<?php echo $aid; ?>" value="<?php html($answer_id); ?>" name="answer" class="custom-control-input">
                                                <label class="custom-control-label" for="<?php echo $aid; ?>">
                                                    <?php echo $questions; ?>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div>
                                        <?php echo html_button(LANG_FORUM_VOTING, 'submit', 'pollSubmit();', ['class' => 'btn-primary']); ?>
                                    </div>
                                </form>

                            <?php } ?>

                        </td>
                        <td class="thread-poll-info">

                            <?php if ($thread['is_closed'] && empty($thread_poll['user_answer_ids'])) { ?>
                                <div class="alert alert-info m-0">
                                    <?php echo LANG_FORUM_YOU_IS_NOT_VOTE_IN_CLOSED; ?>
                                </div>
                            <?php } elseif ($thread_poll['is_closed']) { ?>
                                <div class="alert alert-info m-0">
                                    <?php echo LANG_FORUM_POLL_FINISHED; ?>
                                </div>
                            <?php } elseif (!empty($thread_poll['user_answer_ids'])) { ?>
                                <div class="alert alert-info m-0">
                                    <?php echo LANG_FORUM_YOU_IS_VOTE; ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($thread_poll['is_user_vote'])) { ?>

                                <div class="thread-poll-param"><strong><?php echo LANG_FORUM_YOUR_ANSWER; ?>:</strong> <?php echo implode(', ', $thread_poll['is_user_vote']); ?></div>

                            <?php } ?>

                            <?php if (!$thread_poll['is_closed'] && !$thread['is_closed']) { ?>

                                <?php if (!empty($thread_poll['options']['days'])) { ?>
                                    <div class="thread-poll-param"><strong><?php echo LANG_FORUM_END_DATE_POLL; ?>:</strong> <?php echo html_date($thread_poll['date_pub_end']); ?></div>
                                <?php } ?>

                                <?php if (!empty($thread_poll['is_user_vote']) && $thread_poll['options']['change']) { ?>
                                    <div class="thread-poll-param"><a class="ajaxlink" href="#" onclick="loadPoll('revote'); return false;"><?php echo LANG_FORUM_CHANGE_VOTE; ?></a></div>
                                <?php } ?>

                                <?php if ($user->is_admin || $is_moder || $thread['is_mythread']) { ?>
                                    <div class="thread-poll-param"><a class="ajaxlink" href="#" onclick="if (confirm('<?php echo LANG_FORUM_CONFIRM_DELETE_POLL; ?>')) { deletePoll(); return false; }"><?php echo LANG_FORUM_DELETE_POLL; ?></a></div>
                                <?php } ?>

                            <?php } else { ?>
                                <div class="thread-poll-param thread-poll-param-finished"><strong><?php echo LANG_FORUM_POLL_FINISHED; ?></strong></div>
                            <?php } ?>

                        </td>

                    </tr>

                </table>

            </div>

        <?php } ?>

    <?php } ?>

</div>

<?php ob_start(); ?>
<script>

    function pollSubmit() {

        var form = $('#thread-poll-form');

        var form_data = icms.forms.toJSON(form);

        var url = '<?php echo href_to('forum', 'poll_vote'); ?>';

        $.post(url, form_data, function (result) {

            if (result.error === false) {

                loadPoll('result');

                return;
            } else {
                alert(result.text);
            }

        }, 'json');

    }

    function loadPoll(result) {

        $('#thread_poll').css({
            opacity: 0.4, filter: 'alpha(opacity=40)'
        });
        $.post('<?php echo href_to('forum', 'poll', $thread['id']); ?>/' + result, {}, function (data) {
            $('#thread_poll').html(data);
            $('#thread_poll').css({
                opacity: 1.0, filter: 'alpha(opacity=100)'
            });
        });

    }

    function deletePoll() {
        $.ajax({
            method: 'POST',
            url: '<?php echo href_to('forum', 'poll_delete', $thread['id']); ?>',
            data: {
                csrf_token: '<?php echo cmsForm::getCSRFToken(); ?>'
            },
            dataType: 'json'
        }).done(function (msg) {
            $('#thread_poll').html('');
            alert(msg);
        });
    }
    ;
    $('.thread-poll-voters-block').on('click', function () {
        $(this).children('.thread-poll-voters').toggle();
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>