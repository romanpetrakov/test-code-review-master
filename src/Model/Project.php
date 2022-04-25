<?php

namespace App\Model;
// я правильно понимаю что модель не умеет работать со всеми полями таблицы projects()
// но может работать с произвольным массивом data в котором могу быть данные не совпадающие по структуре с базой данных?
class Project
{
    /**
     * @var array
     */
    public $_data;
    
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->_data['id'];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->_data);
    }
}
