<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */

use \Robot\RobotCreator;

class Events
{

    public static function onWorkerStart($businessWorker)
    {
        //重新播种
        mt_srand();

        // 获取当前进程总量。
        $robotWorkerNum = $businessWorker->count;
        $robotTriggerList = RobotCreator::getRobotList($robotWorkerNum, $businessWorker->id);


        $num = isset($robotTriggerList[$businessWorker->id]) ?
            count($robotTriggerList[$businessWorker->id]) : 0;
        echo '进程'.$businessWorker->id.'创建机器人数量：'.$num."\n";

        // 获得当前进程下需要处理的机器人触发器列表。
        if (isset($robotTriggerList[$businessWorker->id])) {
            $robotTriggerByWorker = [];
            $robotTriggerByWorker = $robotTriggerList[$businessWorker->id];
            \workerman\Lib\Timer::add(1, function() use ($robotTriggerByWorker) {
                if (!empty($robotTriggerByWorker) && is_array($robotTriggerByWorker) ) {
                    foreach ($robotTriggerByWorker as $robotTrigger) {
                        $robotTrigger->doTrigger();
                    }
                }
            });
        }
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        // 向当前client_id发送数据 
        Gateway::sendToClient($client_id, "Hello $client_id\n");
        // 向所有人发送
        Gateway::sendToAll("$client_id login\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message) {
        // 向所有人发送 
        Gateway::sendToAll("$client_id said $message");
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       // 向所有人发送 
       GateWay::sendToAll("$client_id logout");
   }

}
