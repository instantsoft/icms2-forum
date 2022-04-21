<?php

class formWidgetForumStatsOptions extends cmsForm {

    public function init() {

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:category_id', array(
                        'title'   => LANG_WD_FORUM_STATS_TYPE,
                        'default' => 1,
                        'items'   => array(
                            0 => LANG_WD_FORUM_STATS_CATEGORY_DETECT,
                            1 => LANG_WD_FORUM_STATS_ALL_FORUM,
                        )
                    )),
                    new fieldCheckbox('options:show_all_stat', array(
                        'title'   => LANG_WD_FORUM_STATS_SHOW_ALL,
                        'default' => true
                    )),
                    new fieldCheckbox('options:show_month_stat', array(
                        'title'   => LANG_WD_FORUM_STATS_SHOW_MONTH,
                        'default' => true
                    )),
                    new fieldCheckbox('options:show_moderators', array(
                        'title'   => LANG_WD_FORUM_STATS_SHOW_MODERATORS,
                        'default' => true
                    )),
                    new fieldCheckbox('options:show_all_moderators', array(
                        'title'   => LANG_WD_FORUM_STATS_SHOW_PERMS_MODERATORS,
                        'default' => true,
                        'visible_depend' => ['options:show_moderators' => ['show' => ['1']]]
                    ))
                )
            ),
        );
    }

}
