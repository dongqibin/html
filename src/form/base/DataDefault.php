<?php
/**
 * default values for the form
 * Created by PhpStorm.
 * User: DJ
 * Date: 2018/9/12
 * Time: 16:11
 */

namespace dongqibin\html\form\base;


class DataDefault
{
    public $data = []; //默认值集合

    public function __construct($data) {
        $this->setData($data);
    }

    /**
     * set default data
     * @param $data
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * return default data
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * return the default value for the field
     * @param $field
     * @param mixed $defaultValue
     * @return bool|mixed|string
     */
    public function getValue($field,$defaultValue=false) {
        //有value,直接返回value
        if($defaultValue) return $defaultValue;

        //未提交name,返回空字符串
        if($field === '') return '';

        //没传value,获取data里面的数据
        if($defaultValue === false) return empty($this->data[$field]) ? '' : $this->data[$field];
    }
}