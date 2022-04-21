<?php

class onForumAwardsRulesCheck extends cmsAction {

    /**
     * Флаг, что в изначальном комплекте контроллера
     * этого хука не было
     *
     * @var boolean
     */
    public $external = 'awards';

    /**
     * Не надо регистрировать в таблице events
     * @var boolean
     */
    public $disallow_event_db_register = true;

    /**
     * Подключаем ланг наград
     * @var array
     */
    protected $extended_langs = ['awards'];

    /**
     * Проверяет условие выдачи награды
     *
     * @param string $subject Субъект правила
     * @param integer $user_id ID пользователя, для которого проверяем
     * @param mixed $check_value Пороговое значение проверки
     * @param array $award Массив авто награды
     *
     * @return boolean true если условие выполнено, false если нет
     */
    public function run($subject, $user_id, $check_value, $award){
        return $this->{'get'. string_to_camel('_', $subject)}($user_id, $check_value, $award);
    }

    private function getPostCount($user_id, $check_value, $award) {

        $count = $this->model->getUserPostsCount($user_id);

        return $count >= $check_value;
    }

}
