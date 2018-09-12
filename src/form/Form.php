<?php
/**
 * 表单生成类,单纯的生成表单
 * Created by PhpStorm.
 * User: DJ
 * Date: 2018/8/28
 * Time: 15:27
 */

namespace dongqibin\html\form;

use dongqibin\html\form\DataDefault;
use dongqibin\html\form\InputBuild;

class Form
{

    private $inputBuild = null; //表单input构建对象
    private $dataDefault = null; //默认值处理对象

    public function __construct($data=[]) {
        $this->dataDefault = new DataDefault($data);
        $this->inputBuild = new InputBuild();
    }

    /**
     * set name and default value for the input
     * @param $name
     * @param mixed $defaultValue
     * @return $this
     */
    public function field($name, $defaultValue=false) {
        $defaultValue = $this->dataDefault->getValue($name, $defaultValue);
        $this->inputBuild->init($name,$defaultValue);
        return $this;
    }

    /**
     * text
     * @param array $ext
     * @return string
     */
    public function text($ext=[]) {
        return $this->inputBuild->text($ext);
    }

    /**
     * password
     * @param array $ext
     * @return string
     */
    public function password($ext=[]) {
        return $this->inputBuild->password($ext);
    }

    /**
     * hidden
     * @param array $ext
     * @return string
     */
    public function hidden($ext=[]) {
        return $this->inputBuild->hidden($ext);
    }

    /**
     * textarea
     * @param array $ext
     * @return string
     */
    public function textarea($ext=[]) {
        return $this->inputBuild->textarea($ext);
    }

    /**
     * checkbox
     * @param $title
     * @param string $id
     * @param array $ext
     * @return string
     */
    public function checkbox($title, $id='',$ext=[]) {
        return $this->inputBuild->checkbox($title, $id, $ext);
    }

    /**
     * 一组checkbox
     * @param $data_checkbox
     * @return string
     */
    public function checkboxList($data_checkbox) {
        return $this->inputBuild->checkboxList($data_checkbox);
    }

    /**
     * select
     * @param $data_option
     * @param array $ext
     * @return string
     */
    public function select($data_option, $ext=[]) {
        return $this->inputBuild->select($data_option, $ext);
    }

    /**
     * radio ['a'=>'this is aa','b'=>'this is bb']
     * @param $data_radio
     * @return string
     */
    public function radio($data_radio) {
        return $this->inputBuild->radio($data_radio);
    }

    //获取结构化的数据,用户拼装表单html
    public function getInfo() {
        $inputBuildInfo = $this->inputBuild->getInfo();
        $data = [
            'data' => $this->dataDefault->getData(),
        ];
        return $inputBuildInfo + $data;
    }

    public function setData($data) {
        $this->dataDefault->setData($data);
    }
}

/**
 * 使用示例
 * $formClass = new Form($get);
 *
 * //<input type="text" name="name" value="get[name]">
 * echo $formClass->field('name')->text();
 *
 * //<input type="password" name="password" value="get[password]">
 * echo $formClass->field('password')->password();
 *
 * //<input type="hidden" name="id" value="get[id]">
 * echo $formClass->field('id')->hidden();
 *
 * //<textarea name="content">$get[content]</textarea>
 * echo $formClass->field('content')->textarea();
 *
 * //<input type="checkbox" name="aa" title="写作" checked>
 * //<input type="checkbox" name="aa" title="发呆">
 * //<input type="checkbox" name="aa" title="禁用" disabled>
 * $data = [
 *      '1' => '写作',
 *      '2' => '发呆',
 *      '3' => ['禁用','ext'=>['disabled'=>'disabled']],
 * ];
 * echo $formClass->field('aa')->checkboxList($data);
 *
 * //<select name="city" lay-verify="required">
 * //  <option value="0">北京</option>
 * //  <option value="1" disabled="disabled">上海</option>
 * //</select>
 * $data = [
 *      0 => '北京',
 *      1 => ['上海','ext'=>['disabled'=>'disabled']],
 * ];
 * $ext=[
 *      'lay-verify' => 'required',
 * ];
 * echo $formClass->field('city')->select($data,$ext);
 *
 * <input type="radio" name="sex" value="1" title="男">
 * <input type="radio" name="sex" value="2" title="女" disabled>
 * $data = [
 *      1 => '男',
 *      2 => ['女','ext'=>['disabled'=>'disabled']],
 * ];
 * echo $formClass->field('sex')->radio($data);
 *
 *
 */