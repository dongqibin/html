<?php
/**
 * build the input
 * Created by PhpStorm.
 * User: DJ
 * Date: 2018/9/12
 * Time: 16:30
 */

namespace dongqibin\html\form\base;


class InputBuild
{

    const TYPE_TEXT = 'text';
    const TYPE_PASSWORD = 'password';
    const TYPE_HIDDEN = 'hidden';
    const TYPE_FILE = 'file';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';

    public $type = '';
    public $name = '';
    public $defaultValue = '';
    public $_newLine = "\r\n";
    public $data_son = []; //子元素数据,select,radio等
    public $title = ''; //checkbox的title
    public $html = ''; // input html

    public function __construct($name='', $defaultValue='', $type='') {
        $this->init($name, $defaultValue, $type);
    }

    public function init($name='', $defaultValue='', $type='') {
        $this->_clear(); //释放上次输入的变量
        if($name) $this->name = $name;
        if($defaultValue) $this->defaultValue = $defaultValue;
        if($type) $this->type = $type;

        return $this;
    }

    /**
     * text
     * @param array $ext
     * @return string
     */
    public function text($ext=[]) {
        $this->type = self::TYPE_TEXT;
        return $this->_getInput($this->type,$ext);
    }

    /**
     * password
     * @param array $ext
     * @return string
     */
    public function password($ext=[]) {
        $this->type = self::TYPE_PASSWORD;
        return $this->_getInput($this->type,$ext);
    }

    /**
     * hidden
     * @param array $ext
     * @return string
     */
    public function hidden($ext=[]) {
        $this->type = self::TYPE_HIDDEN;
        return $this->_getInput($this->type,$ext);
    }

	/**
     * hidden
     * @param array $ext
     * @return string
     */
    public function file($ext) {
        $this->type = self::TYPE_FILE;
        return $this->_getInput($this->type, $ext);
    }

    /**
     * textarea
     * @param array $ext
     * @return string
     */
    public function textarea($ext=[]) {
        $this->type = self::TYPE_TEXTAREA;
        $ext_str = $this->_getExt($ext);
        $this->html = '<textarea '. $ext_str .' name="'. $this->name .'">'. $this->defaultValue .'</textarea>'.$this->_newLine;
        return $this->html;
    }

    /**
     * checkbox
     * @param $title
     * @param string $id
     * @param array $ext
     * @return string
     */
    public function checkbox($title,$id='',$ext=[]) {
        $this->title = $title;
        $this->type = self::TYPE_CHECKBOX;
        $ext_str = $this->_getExt($ext);
        $this->html = '<input type="'. $this->type .'" name="'. $this->name .'['.$id.']" ' .$ext_str. ' title="'. $title .'">'.$this->_newLine;
        return $this->html;
    }

    /**
     * 一组checkbox
     * @param $data_checkbox
     * @return string
     */
    public function checkboxList($data_checkbox) {
        $this->data_son = $data_checkbox;
        $this->type = self::TYPE_CHECKBOX;
        $html = '';
        if(!$this->defaultValue) $this->defaultValue = [];
        foreach($data_checkbox as $k=>$v) {
            $ext_check = [];
            if(!empty($v['ext'])) {
                $ext_check = $v['ext'];
                $v = $v[0];
            }
            if(in_array($k,$this->defaultValue)) $ext_check['checked'] = 'checked';
            $html .= $this->checkbox($v,$k,$ext_check);
        }
        $this->html = $html;
        return $this->html;
    }

    /**
     * select
     * @param $data_option
     * @param array $ext
     * @return string
     */
    public function select($data_option,$ext=[]) {
        $this->data_son = $data_option;
        $this->type = self::TYPE_SELECT;
        $ext_str = $this->_getExt($ext);
        $html = '<select name="'. $this->name .'" '. $ext_str .'>'.$this->_newLine;
        foreach($data_option as $k=>$v) {
            $ext_option = [];
            if (isset($v['ext'])) {
                $ext_option = $v['ext'];
                $v = $v[0];
            }
            if ($k == $this->defaultValue) {
                $ext_option['selected'] = 'selected';
            }
            $html .= $this->_getOption($k, $v, $ext_option);
        }
        $html .= '</select>'.$this->_newLine;
        $this->html = $html;
        return $this->html;
    }

    /**
     * radio ['a'=>'this is aa','b'=>'this is bb']
     * @param $data_radio
     * @return string
     */
    public function radio($data_radio) {
        $this->data_son = $data_radio;
        $this->type = self::TYPE_RADIO;
        $html = '';
        foreach($data_radio as $k=>$v) {
            $ext_radio = [];
            if(!empty($v['ext'])) {
                $ext_radio = $v['ext'];
                $v = $v[0];
            }
            if($k == $this->defaultValue) $ext_radio['checked'] = 'checked';
            $html .= $this->_getRadio($k,$v,$ext_radio);
        }
        $this->html = $html;
        return $this->html;
    }

    /**
     * 获取表单信息
     * @return array
     */
    public function getInfo() {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'defaultValue'=> $this->defaultValue,
            'title'=> $this->title,
            'data_son' => $this->data_son,
            'html'  => $this->html,
        ];
    }

    /**
     * one radio
     * @param $value
     * @param $title
     * @param array $ext
     * @return string
     */
    private function _getRadio($value,$title,$ext=[]) {
        $ext_str = $this->_getExt($ext);
        return '<input type="'. $this->type .'" name="'. $this->name .'" value="'. $value .'" title="'. $title .'" '. $ext_str .'>'.$this->_newLine;
    }

    /**
     * select->option
     * @param $value
     * @param $title
     * @param array $ext
     * @return string
     */
    private function _getOption($value,$title,$ext=[]) {
        $ext_str = $this->_getExt($ext);
        return '<option '. $ext_str .' value="'. $value .'">'. $title .'</option>'.$this->_newLine;
    }

    /**
     * 获取常规表单
     * @param string $type
     * @param array $ext
     * @return string
     */
    private function _getInput($type='',$ext=[]) {
        if(!$type) $type = $this->type;
        else $this->type = $type;

        $ext = $this->_getExt($ext);
        $this->html = '<input type="'.$type.'" name="'.$this->name.'" value="'.$this->defaultValue.'" ' . $ext . '>'.$this->_newLine;
        return $this->html;
    }

    /**
     * 释放表单变量
     */
    private function _clear() {
        $this->type = '';
        $this->name = '';
        $this->defaultValue = '';
        $this->data_son = [];
        $this->title = '';
        $this->html = '';
    }

    /**
     * 将扩展数据转为字符串
     * @param $ext
     * @return string
     */
    private function _getExt($ext) {
        $ext_str = '';
        foreach($ext as $k=>$v) {
            $ext_str .= ' '.$k .'='.'"'.$v.'" ';
        }
        return $ext_str;
    }
}