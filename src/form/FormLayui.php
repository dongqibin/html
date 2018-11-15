<?php
/**
 * 兼容layui的表单生成类,带layui样式
 * Created by PhpStorm.
 * User: DJ
 * Date: 2018/8/24
 * Time: 8:48
 */

namespace dongqibin\html\form;

/**
 * Class FormLayui
 * @method FormLayui selectOriObj($name , $data , $ext=[] , $value=false)
 * @method FormLayui textObj($title , $name , $is_required=false , $verify='' , $value=false , $ext=[])
 * @method FormLayui textOriObj($name , $verify='' , $value=false , $ext=[])
 * @method FormLayui textBlockOriObj($name , $verify='' , $value=false , $ext=[])
 * @method FormLayui setHtmlObj($html)
 * @method FormLayui setShortLineObj()
 * @package common\html
 */
class FormLayui
{
    public $form = null; //核心表单类
    public $layuiForm = null;

    public $data = [];
    public $html = ''; //表单html
    public $clues = ''; //表单外提示语
	public $Placeholder = ''; //表单内提示语

    private $_newLine = "\r\n"; //换行符

    //option里面value与title的key
    public $optionKeyValue = '';
    public $optionKeyTitle = '';

    public function __construct($data=[]) {
        $this->form = new \dongqibin\html\form\Form($data);
        $this->layuiForm = new \dongqibin\html\form\layui\Form();
        $this->data = $data;
    }


    /*=================常规方法==================*/
    /**
     * 隐藏表单
     * @param $name
     * @param bool $value
     * @param array $ext
     * @return string
     */
    public function hidden($name , $value=false , $ext=[]) {
        return $this->getForm()->field($name,$value)->hidden($ext);
    }

	/**
     * 文件上传表单
     * @param $name
     * @param bool $value
     * @param array $ext
     * @return string
     */
    public function file($name, $ext=[]) {
        return $this->getForm()->field($name,null)->file($ext);
    }

    /**
     * text
     * @param $title
     * @param $name
     * @param bool $is_required
     * @param string $verify
     * @param bool $value
     * @param array $ext
     * @param $is_long
     * @return string
     */
    public function text($title , $name , $is_required=false , $verify='' , $value=false , $ext=[], $is_long=false) {
        //获取提示语
        $clues = $this->_getClues();

        //表单属性,合成表单
        $verify = empty($verify) ? 'lengthCheck30' : $verify . '|lengthCheck30';
        $ext = $this->_getconvExtMerge($ext, $verify, $title);
        $input = $this->getForm()->field($name,$value)->text($ext);

        if($is_long) $inputDiv = $this->getLayuiForm()->getLong($input);
        else $inputDiv = $this->getLayuiForm()->getShort($input);

        //组合一个表单及结构
        $html = $this->getLayuiForm()->item(function() use($inputDiv, $title, $is_required, $clues) {
            return $this->getLayuiForm()->getLabel($title,$is_required)
                . $this->getLayuiForm()->getHtml($inputDiv)
                . $this->getLayuiForm()->getHtml($clues);
        });
        return $html;
    }

    /**
     * 长text
     * @param $title
     * @param $name
     * @param bool $is_required
     * @param string $verify
     * @param bool $value
     * @param array $ext
     * @return string
     */
    public function textLong($title , $name , $is_required=false , $verify='' , $value=false , $ext=[]) {
        return $this->text($title , $name , $is_required , $verify , $value , $ext,true);
    }

    /**
     * password
     * @param $title
     * @param $name
     * @param bool $is_required
     * @param $verify
     * @param bool $value
     * @param array $ext
     * @return string
     */
    public function password($title , $name , $is_required=false , $verify , $value=false , $ext=[]) {
        $ext = $this->_getconvExtMerge($ext, $verify, $title);
        $input = $this->getForm()->field($name,$value)->password($ext);

        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required) {
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getShort($input);
        });
        return $html;
    }

    /**
     * 一组text表单
     * @param $title
     * @param bool $is_required
     * @return string
     */
    public function textSome($title , $is_required=false) {
        $html = $this->getLayuiForm()->item(function() use($title, $is_required) {
            return $this->getLayuiForm()->inline(function() use($title, $is_required) {
                return $this->getLayuiForm()->getLabel($title, $is_required)
                    . $this->_getHtml()
                    . $this->_getClues();
            });
        });
        $this->_clearHtml();
        return $html;
    }

    /**
     * text表单原始数据
     * @param $name
     * @param string $verify
     * @param bool $value
     * @param array $ext
     * @param bool $is_long
     * @return mixed
     */
    public function textOri($name , $verify='' , $value=false , $ext=[] , $is_long=false) {
        //表单属性,合成表单
        $ext = $this->_getconvExtMerge($ext, $verify);

        //获取表单
        $input = $this->getForm()->field($name,$value)->text($ext);

        if($is_long) $method = 'getLong';
        else $method = 'getShort';

        $html = $this->getLayuiForm()->$method($input);
        return $html;
    }

    /**
     * 长text表单原始数据
     * @param $name
     * @param string $verify
     * @param bool $value
     * @param array $ext
     * @return mixed
     */
    public function textBlockOri($name , $verify='' , $value=false , $ext=[]) {
        return $this->textOri($name , $verify , $value , $ext,true);
    }

    /**
     * 输入框
     * @param $title
     * @param $name
     * @param bool $is_required
     * @param string $verify
     * @param bool $value
     * @param array $ext
     * @return string
     */
    public function textarea($title,$name,$is_required=false,$verify='',$value=false,$ext=[]) {
        $ext['placeholder'] = $this->_getPlaceholder($title) .', '. $this->clues;
        $ext['cols'] = 80;
        $ext['rows'] = 5;
        $ext['lay-verify'] = empty($verify) ? 'lengthCheck120' : $verify;
        if(empty($ext['class'])) $ext['class']="layui-textarea";

        $input = $this->getForm()->field($name, $value)->textarea($ext);

        $html = $this->getLayuiForm()->item(function() use($title, $is_required, $input) {
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getLong($input);
        });
        return $html;
    }

    /**
     * check
     * @param $title
     * @param $name
     * @param $data_all
     * @param $data_selected
     * @param string $verify
     * @param bool $is_required
     * @return string
     */
    public function check($title,$name,$data_all,$data_selected,$verify='',$is_required=false) {
        $data_all = $this->_getDataWithKey($data_all);
        $data = [];
        foreach($data_all as $k=>$v) {
            $ext = ['lay-skin' => 'primary'];
            $v = [$v,'ext'=>$ext];
            $data[$k] = $v;
        }
        $data_all = $data;
        $input = $this->getForm()->field($name, $data_selected)->checkboxList($data_all);
        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required, $verify) {
            $class = 'equipment_body';
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getLong($input, $class, $verify)
                . $this->_getClues();
        });
        return $html;
    }

    /**
     * checkOri
     * @param $title
     * @param $name
     * @param $data
     * @param string $verify
     * @param bool $is_required
     * @return string
     */
    public function checkOri($title,$name,$data=[],$verify='',$is_required=false) {
        $input = '';
        foreach($data as $k=>$v) {
            if(empty($v['ext'])) {
                $ext = [];
            } else {
                $ext = $v['ext'];
                $v = $v[0];
            }
            $ext['lay-skin'] = 'primary';
            $input .= $this->getForm()->field($name)->checkbox($v,$k,$ext);
        }

        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required, $verify) {
            $class = 'equipment_body';
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getLong($input, $class, $verify)
                . $this->_getClues();
        });
        return $html;
    }

    /**
     * radio
     * @param $title
     * @param $name
     * @param $data
     * @param bool $is_required
     * @param bool $value
     * @return string
     */
    public function radio($title,$name,$data,$is_required=false,$value=false) {
        $input = $this->getForm()->field($name, $value)->radio($data);

        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required) {
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getLong($input)
                . $this->_getClues();
        });

        return $html;
    }

    /**
     * select
     * @param $title
     * @param $name
     * @param $data
     * @param bool $is_required
     * @param array $ext
     * @param bool $value
     * @return string
     */
    public function select($title , $name ,$data, $is_required=false,$ext=[],$value=false) {
        $input = $this->selectOri($name, $data, $ext, $value);

        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required) {
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getHtml($input)
                . $this->_getClues();
        });
        return $html;
    }

    /**
     * 一组select
     * @param $title
     * @param bool $is_required
     * @return string
     */
    public function selectSome($title,$is_required=false) {
        $input = $this->_getHtml();

        $html = $this->getLayuiForm()->item(function() use($input, $title, $is_required) {
            return $this->getLayuiForm()->getLabel($title, $is_required)
                . $this->getLayuiForm()->getHtml($input)
                . $this->_getClues();
        });

        $this->_clearHtml();
        return $html;
    }

    /**
     * 原始select
     * @param $name
     * @param $data
     * @param array $ext
     * @param bool $value
     * @return string
     */
    public function selectOri($name , $data , $ext=[] , $value=false) {
        $data = $this->_getDataWithKey($data);
        $input = $this->getForm()->field($name, $value)->select($data, $ext);

        $html = $this->getLayuiForm()->getShort($input);
        return $html;
    }


    /*=====================小方法,负责补充===============*/
    /**
     * 设置option里面value与title的key
     * @param string $valueKey
     * @param string $titleKey
     * @return $this
     */
    public function setOptionDataKey($valueKey='' , $titleKey='') {
        if($valueKey) $this->optionKeyValue = $valueKey;
        if($titleKey) $this->optionKeyTitle = $titleKey;
        return $this;
    }

    /**
     * 设置html
     * @param string $html
     * @return string
     */
    public function setHtml($html='') {
        return $html;
    }

    /**
     * 设置短连接线
     * @return string
     */
    public function setShortLine() {
        return '<div class="layui-form-mid">-</div>';
    }

    /**
     * 获取基本表单实例
     * @return \dongqibin\html\form\Form|null
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * 获取layui结构实例
     * @return LayuiForm|null
     */
    public function getLayuiForm() {
        return $this->layuiForm;
    }

    /**
     * 设置表单默认值
     * @param $data
     */
    public function setData($data) {
        $this->data = $data;
        $this->getForm()->setData($data);
    }

    /**
     * 设置提示语
     * @param $clues
     * @return $this
     */
    public function setClues($clues) {
        $this->clues = $clues;
        return $this;
    }

    /**
     * 设置长度限制提示语
     * @param $num
     * @return FormLayui
     */
    public function setCluesLength($num) {
        $clues = '长度不能超过' . $num . '字符';
        return $this->setClues($clues);
    }


    /*======================自己使用的私有方法==========================*/
    /**
     * 获取提示语
     * @return string
     */
    private function _getClues() {
        if(!$this->clues) return $this->clues;
        $clues = $this->getLayuiForm()->setClues($this->clues);
        $this->clues = '';
        return $clues;
    }

    /**
     * 将一组数据整理成value=>key的键值对
     * @param $data
     * @return array
     */
    private function _getDataWithKey($data) {
        if($this->optionKeyTitle) {
            $data_option = [];
            foreach($data as $v) {
                $title = '';
                if(is_array($this->optionKeyTitle)) {
                    foreach($this->optionKeyTitle as $title_line) {
                        $title .= $v[$title_line] . ' &nbsp; ';
                    }
                }
                $data_option[$v[$this->optionKeyValue]] = empty($title) ? $v[$this->optionKeyTitle] : $title;
            }
            $data = $data_option;
            $this->_clearOptionKey();
        }
        return $data;
    }

    /**
     * 追加一段html
     * @param $html
     */
    private function _addHtml($html) {
        $this->html .= $html . $this->_newLine;
    }

    /**
     * 获取html
     * @return string
     */
    private function _getHtml() {
        return $this->html;
    }

    /**
     * 根据title获取placeholder属性
     * @param $title
     * @return string
     */
    private function _getPlaceholder($title) {
        if($this->Placeholder) {
            $Placeholder = $this->Placeholder;
            $this->Placeholder = '';
            return $Placeholder;
        }
        if(!$title) return '';
        return '请输入'.$title;
    }

    public function setPlaceholder($Placeholder) {
        $this->Placeholder = $Placeholder;
        return $this;
    }

    /**
     * 获取常规ext
     * @param string $verify
     * @param string $title
     * @return array
     */
    private function _getConvExt($verify='',$title='') {
        $placeholder = $this->_getPlaceholder($title);
        return [
            'lay-verify'    => $verify,
            'placeholder'   => $placeholder,
            'autocomplete'  => 'off',
            'class'         => 'layui-input',
        ];
    }

    /**
     * 合并常规ext
     * @param $ext
     * @param string $verify
     * @param string $title
     * @return array
     */
    private function _getconvExtMerge($ext, $verify='', $title='') {
        $convExt = $this->_getConvExt($verify, $title);
        if(!empty($ext['class'])) $ext['class'] .= ' ' . $convExt['class'];
        $ext = array_merge($convExt,$ext);
        return $ext;
    }

    /**
     * 清除html,方便下次追加
     */
    private function _clearHtml() {
        $this->html = '';
    }

    /**
     * 清空key和value
     */
    private function _clearOptionKey() {
        $this->optionKeyValue = '';
        $this->optionKeyTitle = '';
    }

    /**
     * 作者也不记得这是干嘛的了~~~
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments) {
        //methodObj
        $fun_name = substr($name,0,-3);
        $result = call_user_func_array([$this, $fun_name], $arguments);
        if ($result) {
            $this->_addHtml($result);
        }
        return $this;
    }
}