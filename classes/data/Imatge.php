<?php
class Imatge {
    private $id;
    private $titol;
    private $url;
    private $llocId;

    function __construct()
    {
        $this->titol = func_get_arg(0);
        $this->url = func_get_arg(1);
        $this->llocId= func_get_arg(2);

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

    public function getTitol()
    {
        return $this->titol;
    }

    public function getUrl()
    {
        return $this->url;
    }
}

