<?php
/**
 * 机器人动作接口
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-16
 */
namespace Robot;

interface RobotActionInterface
{

    /**
     * 获得动作编号.
     *
     * @return string
     */
    public function getActionID();

    /**
     * 获得动作名称.
     *
     * @return string
     */
    public function getActionName();

    /**
     * 获得动作描述.
     *
     * @return string
     */
    public function getActionContent();


    /**
     * 预备动作.
     *
     * @return mixed
     */
    public function beforeAction();

    /**
     * 开始动作.
     *
     * @return mixed
     */
    public function beginAction();

    /**
     * 结束动作.
     *
     * @return mixed
     */
    public function endAction();

    /**
     * 收尾动作.
     *
     * @return mixed
     */
    public function afterAction();

}
