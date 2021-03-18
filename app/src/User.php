<?php declare(strict_types=1);

class User
{
    public $name;
    public function __construct($name)
    {
        $this->name=$name;
    }
    public function Isempty()
    {
        try{
            if(empty($this->name))
            {
                throw new Exception('its null',0);
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
        return 'welcome '.$this->name;
    }
}

