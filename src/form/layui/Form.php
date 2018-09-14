<?php
/**
 * Created by PhpStorm.
 * User: DJ
 * Date: 2018/9/13
 * Time: 17:58
 */

namespace dongqibin\html\form\layui;


class Form
{
    public $label = '';
    public $input = '';

    private $_newLine = "\r\n";

    public function __construct() {

    }

    //外围item
    public function item($fun) {
        $html = '<div class="layui-form-item">' . $this->_newLine;
        $html .= $fun();
        $html .= '</div>' . $this->_newLine;
        return $html;
    }

    //外围inline
    public function inline($fun) {
        $html = '<div class="layui-form-inline">' . $this->_newLine;
        $html .= $fun();
        $html .= '</div>' . $this->_newLine;
        return $html;
    }

    //label
    public function getLabel($label, $isRequirePre=false) {
        $this->label = '<label class="layui-form-label">' . $this->getIsRequiredPre($isRequirePre) . $label . '</label>' . $this->_newLine;
        return $this->label;
    }

    //设置短连接线
    public function getShortLine() {
        return '<div class="layui-form-mid">-</div>' . $this->_newLine;
    }

    //设置提示语
    public function setClues($clues) {
        return '<div class="layui-form-mid layui-word-aux">'. $clues .'</div>' . $this->_newLine;
    }

    //获取必填项的标题前缀,前面的红色星号
    public function getIsRequiredPre($is_required) {
        if(!$is_required) return '';
        return '<span style="color:red ">*</span>';
    }

    //获取长表单
    public function getLong($input, $class='', $verify='') {
        $class = 'layui-input-block ' . $class;
        return $this->getInputDiv($input, $class, $verify);
    }

    //获取短表单
    public function getShort($input) {
        return $this->getInputDiv($input, 'layui-input-inline');
    }

    public function getHtml($html) {
        return $html . $this->_newLine;
    }

    //获取input+外围div
    public function getInputDiv($input, $class, $verify='') {
        $html = '<div class="' . $class . '" lay-verify="'. $verify .'">' . $this->_newLine;
        $html .= $input;
        $html .= '</div>' . $this->_newLine;
        return $html;
    }

    //获取表单
    public function getInput() {
        $this->input = '<input type="text" name="xx" value="0" />' . $this->_newLine;
        return $this->input;
    }
}