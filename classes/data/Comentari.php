<?php
class Comentari {
    private $id;
    private $text;
    private $usuariId;
    private $llocId;

    function __construct()
    {
        $this->text = func_get_arg(0);
        $this->usuariId = func_get_arg(1);
        $this->llocId = func_get_arg(2);

        if (func_num_args()===4) {
            $this->id = func_get_arg(3);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLlocId()
    {
        return $this->llocId;
    }

    public function getUsuariId()
    {
        return $this->usuariId;
    }

    public function getText()
    {
        return $this->text;
    }
}
