<?php
/**
 * 机器人触发器
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-16
 */
namespace Robot;

class RobotTrigger
{

    /**
     * 触发器ID.
     * @var integer
     */
    protected $triggerID = 0;

    /**
     * 机器人ID.
     * @var integer
     */
    protected $robotID;

    /**
     * 模拟的客户端webSocket对象.
     *
     * @var \Workerman\Connection\AsyncTcpConnection
     */
    protected $client;

    /**
     * 可执行动作对象实例的列表， 必须是 ExecutableAction 对象的实例 .
     * @var string
     */
    protected $actionList = array();

    /**
     * 当前执行到的动作编号.
     * @var integer
     */
    protected $nowActionNum = 0;

    /**
     * 当前动作/动作组状态. 准备 ready, 执行中 doing, 执行成功 success, 执行失败 failed
     *
     * @var string
     */
    protected $nowActionState = '';

    /**
     * 当前动作执行次数。
     *
     * @var string
     */
    protected $nowDoActionTimes = '';

    /**
     * 当前动作需要执行的次数，默认1.
     *
     * @var string
     */
    protected $nowNeedActionTimes = '1';

    /**
     * 设置触发器编号，一个触发器拥有唯一ID.
     * @param integer $id 触发器编号.
     *
     * @return array (code=>0, msg=>array())
     */
    public function setTriggerID($id)
    {
        $this->triggerID = $id;
    }

    /**
     * 获得触发器编号.
     *
     * @return array (code=>0, msg=>array())
     */
    public function getTriggerID()
    {
        return $this->triggerID;
    }


    /**
     * 设置机器人ID，一个触发器拥有唯一ID.
     * @param integer $id 机器人ID.
     *
     * @return array
     */
    public function setRobotID($id)
    {
        $this->robotID = $id;
    }

    /**
     * 获取机器人ID.
     *
     * @return string
     */
    public function getRobotID()
    {
        return $this->robotID;
    }

    /**
     * 获取客户端连接对象.
     *
     * @return \Workerman\Connection\AsyncTcpConnection
     */
    public function getClientID()
    {
         return $this->client;
    }

    /**
     * 设置需要执行的动作列表，动作或动作组.
     * @param array $actionList 待执行动作列表.
     *
     * @return array
     */
    public function setActionList($actionList)
    {
        $this->actionList = $actionList;
    }

    /**
     * 获得需要执行的动作列表.
     *
     * @return array
     */
    public function getActionList()
    {
        return $this->actionList;
    }

    /**
     * 执行触发器
     * @return array
     */
    public function doTrigger()
    {
        /**
         * 暂时只根据时间作为条件。
         * beginTime 和 endTime 都作为秒数处理。范围在0-86400内。
         *
         * beginDate 和 endDate 的在指定时间段执行。
         *
         * 根据逻辑条件，进行动作执行的功能。三期再考虑。
         *
         * */
        $nowTime = time() - strtotime(date("Y-m-d"));
        $nowDate = date("Y-m-d H:i:s");
        if (is_array($this->actionList)) {
            foreach ($this->actionList as $executableAction)
            {
                $act = $executableAction;

                // 同一动作在执行不同次数的时候，进行时间随机偏移调整。
                $adjustTime = $act->stepTime
                + rand($act->randBeginTime, $act->randEndTime);

                // 是否符合上次执行后的时间间隔。
                $isRightTime = $nowTime > ( $act->lastDoActionTime + $adjustTime );

                // 是否在可执行的时间范围内。
                $isInTime = ( $act->beginTime <= $nowTime )
                    && ( $nowTime <= $act->endTime );

                // 如果有指定日期，则在指定日期内执行。
                $isInDate = true;
                if (!empty($act->beginDate)) {
                    $isInDate = ($act->beginDate <= $nowDate)
                        && ($nowDate <= $act->endDate);
                }

                // 根据执行类型，判断概率或次数。
                $isNeedDoAction = false;
                if ($act->triggerActionType === RobotCreator::TRIGGER_TYPE_TIMES) {
                    // 根据执行次数判断是否需要继续执行。
                    $isNeedDoAction = $act->nowNeedActionTimes
                        > $act->nowDoActionTimes;
                } else if ($act->triggerActionType === RobotCreator::TRIGGER_TYPE_RANDOM)  {
                    if ($act->doActionProbability > mt_rand(0, 10000)) {
                        $isNeedDoAction = true;
                    } else {
                        $isNeedDoAction = false;
                    }
                }

                // 满足条件则开始执行
                if ($isRightTime && $isInTime && $isNeedDoAction && $isInDate)
                {

                    $act->robotAction->doAction();
                    $act->nowDoActionTimes += 1;
                    $act->lastDoActionTime = time() - strtotime(date("Y-m-d"));
                // 不满足条件，则继续下一个动作
                } else {
                    continue;
                }


            }
        }

    }

}
