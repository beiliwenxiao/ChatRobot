<?php
/**
 * 可以执行的动作对象.
 *
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-16
 */

namespace Robot;


class ExecutableAction
{
    /**
     * 机器人的动作对象实例.
     * @var RobotAction
     */
    public $robotAction;

    /**
     * 执行动作的开始时间，单位 秒 ，范围 0-86400.
     *
     * @var integer
     */
    public $beginTime;

    /**
     * 执行动作的结束时间，单位 秒 ，范围 0-86400.
     *
     * @var integer
     */
    public $endTime;

    /**
     * 执行每一次动作的间隔时间，单位 秒 ，范围 0-3600 默认 1.
     *
     * @var integer
     */
    public $stepTime = 1;

    /**
     * 执行每一次动作的随机间隔开始时间，单位 秒 ，范围 0-3600 默认 0.
     *
     * @var integer
     */
    public $randBeginTime = 0;

    /**
     * 执行每一次动作的随机间隔结束时间，单位 秒 ，范围 0-3600 默认 10.
     *
     * @var integer
     */
    public $randEndTime = 10;

    /**
     * 执行动作的开始时间，如：2018-01-01 00:00:00 .
     *
     * @var string
     */
    public $beginDate;

    /**
     * 执行动作的结束时间，如：2018-01-07 23:59:59 .
     *
     * @var integer
     */
    public $endDate;

    /**
     * 上次执行时间，单位 秒.
     *
     * @var integer
     */
    public $lastDoActionTime = 0;

    /**
     * 当前动作执行次数。
     *
     * @var integer
     */
    public $nowDoActionTimes = 0;

    /**
     * 当前动作需要执行的次数，默认1.
     *
     * @var string
     */
    public $nowNeedActionTimes = 1;

    /**
     * 执行动作的类型.按设定的次数执行 times, 随机执行 random,
     *
     * @var string
     */
    public $triggerActionType = 'times';

    /**
     * 执行动作的概率.万分比。默认5000 ，即50%。当选择随机执行类型时会启用该参数.
     *
     * @var string
     */
    public $doActionProbability = 5000;

}
