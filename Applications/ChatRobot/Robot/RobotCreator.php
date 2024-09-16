<?php
/**
 * 机器人创建者
 *
 * 机器人的行为逻辑和时间限制，都在这里处理。
 *
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-17
 *
 */
namespace Robot;

use Robot\Action\EnterRoom;
use Robot\RedisBase;
use WebSocket\Exception;

class RobotCreator
{

    /**
     * 机器人触发器缓存KEY。
     * @var string
     */
    const CACHE_ROBOT_TRIGGER = 'robot_trigger_';

    /**
     * 机器人触发器缓存时间，单位 秒， 默认 60。
     * @var string
     */
    const CACHE_ROBOT_TRIGGER_TIME = 60;

    /**
     * 触发器触发类型，根据执行次数触发。
     * @var string
     */
    const TRIGGER_TYPE_TIMES = 'times';

    /**
     * 触发器触发类型，根据概率随机触发。
     * @var string
     */
    const TRIGGER_TYPE_RANDOM = 'random';

    /**
     * 获取一组机器人.
     *
     * 参数中，可能还会增加用户ID和用户昵称等。视后台数据情况调整。
     *
     * @param integer $robotId         机器人ID.
     * @param array   $userInfo        用户信息.
     * @param array   $triggerInfoList 触发器信息组.
     *
     * @return RobotTrigger
     */
    public static function createOne($robotId, $userInfo, $triggerInfoList)
    {
        // 初始化机器人动作触发器
        $robotTrigger = new RobotTrigger();

        // 设置第一个机器人ID
        $robotTrigger->setRobotID($robotId);

        // 创建client
        // $robotTrigger->createClient($userInfo);

        // 登录获取token
        // $token = $robotTrigger->getToken($userInfo);

        // 根据机器人ID，初始化触发器、用户ID、用户信息、token等等。

        /** 包装可执行的动作 开始。这部分可能会循环执行。*/
        $actionList = null;
        if(is_array($triggerInfoList) && !empty($triggerInfoList)) {
            foreach ($triggerInfoList as $triggerInfo) {

                // 机器人动作。
                $actionClassName = '\Robot\Action\\'.$triggerInfo['actionClassName'];
                $action = new $actionClassName;

                $action->userId = $userInfo['id'];
                $action->userName = $userInfo['username'];
                $action->nickName = $userInfo['nickname'];

                // workerman-chat聊天室的默认roomid为1。此处可自定义
                $action->roomId = '1';

                // 把触发器的连接对象，传递给动作。
                $action->client = $robotTrigger->getClientID();

                // 初始化一个可执行动作。把基础动作，扩展为可执行的动作。
                $act = new ExecutableAction();
                $act->robotAction = $action;
                $act->triggerActionType = $triggerInfo['triggerActionType'];
                $act->doActionProbability = $triggerInfo['doActionProbability'];
                $act->stepTime = $triggerInfo['stepTime'];
                $act->randBeginTime = $triggerInfo['randBeginTime'];
                $act->randEndTime = $triggerInfo['randEndTime'];
                $act->nowNeedActionTimes = $triggerInfo['nowNeedActionTimes'];
                $act->beginTime = $triggerInfo['beginTime'];
                $act->endTime = $triggerInfo['endTime'];

                // 初始化触发器需要的机器人可执行动作实例。
                $actionList[] = $act;

            }
        }

        /** 包装可执行的动作 结束。这部分可能会循环执行。*/

        $robotTrigger->setActionList($actionList);
        return $robotTrigger;
    }

    /**
     *
     * @param $robotWorkerNum   进程总量
     *
     * @param $businessWorkerId 当前进程号,0开始
     *
     * @return mixed
     */
    public static function getRobotList($robotWorkerNum, $businessWorkerId)
    {
        // 根据数据库数据，创建多个机器人。

        /** 创建多个机器人 开始。这部分可能会循环执行。直接一次获取多条数据，再进行foreach循环即可*/

        // 通过数据库调用获取用户数据。这里是测试用，直接构造
        $userInfoList = [
            [
                'id'=> 1,
                'username'=> 'test01',
                'nickname'=> '测试用户1',

            ],
            [
                'id'=> 2,
                'username'=> 'test02',
                'nickname'=> '测试用户2',

            ],
//            [
//                'id'=> 3,
//                'username'=> 'test03',
//                'nickname'=> '测试用户3',
//
//            ],
//            [
//                'id'=> 4,
//                'username'=> 'test04',
//                'nickname'=> '测试用户4',
//
//            ],
//            [
//                'id'=> 5,
//                'username'=> 'test05',
//                'nickname'=> '测试用户5',
//
//            ],

        ];

        /**
         * 事件执行说明。
         * 每一个动作执行完成后，才会执行下一个动作。
        **/

        // 登录聊天室，聊几句天后，退出聊天室。但重复执行
        $actionTest1['actionClassName'] = 'EnterRoom';
        $actionTest1['triggerActionType'] = 'random';// 随机执行
        $actionTest1['doActionProbability'] = 10000;// 随机率，100%
        $actionTest1['stepTime'] = 0;
        $actionTest1['randBeginTime'] = 0;
        $actionTest1['randEndTime'] = 0;
        $actionTest1['nowNeedActionTimes'] = 10000;
        $actionTest1['beginTime'] = 0;
        $actionTest1['endTime'] = 86400;

        $robotList = array();
        // 开始循环。
        if ( empty($userInfoList)|| !is_array($userInfoList) ) {
            return $robotList;
        }

        foreach ($userInfoList as $userInfo) {


            $robotId = $userInfo['id'];
            // 机器人ID取余。进入不同的进程。
            $inWorkerId = $robotId % $robotWorkerNum;

            // 如果进程号与分配的进程号不一致，则跳过。
            if ($businessWorkerId !== $inWorkerId) {
                continue;
            }

            // 触发器之后也需要foreach循环处理。
            $triggerInfoList[] = $actionTest1;

            /*** 可以将多个不同的动作，写入到触发器，参数格式与例子一样 ***/
            // 如: $triggerInfoList[] = $actionTest2;

            if (empty($robotTrigger)) {
                $robotTrigger = self::createOne($robotId, $userInfo, $triggerInfoList);
            }

            // 获取不同进程下的所有机器人。
            $robotList[$inWorkerId][] = $robotTrigger;

            // 必须重置
            $robotTrigger = null;
            $triggerInfoList = null;

        }

        /** 单个机器人，优先查看缓存，存在则读取缓存。。*/


        /** 创建多个机器人 结束。这部分可能会循环执行。*/

        return $robotList;
    }
}
