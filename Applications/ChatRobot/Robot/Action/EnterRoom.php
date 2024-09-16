<?php
/**
 * 机器人动作例子：进入房间.
 *
 * 说明：此处为动作例子，类名以及类属性的值，均为自定义。
 * 方便用于RobotTrigger中的调用。
 * 本例简单的执行了进入聊天室，以及随机发送几句聊天信息。
 *
 * 实际上可以处理各种复杂操作。
 *
 * 机器人逻辑:
 * 机器人动作包括：注册、登录、进出房间、发信息、发礼物、发弹幕、聊天、获取数据、删除注册等等。
 * 机器人的策略： 动作、数量、触发条件、执行周期、执行时间。可以有多条。
 *
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-16
 */
namespace Robot\Action;

use Robot\RobotAction;
use Workerman\Lib\Timer;

class EnterRoom extends RobotAction
{
    /**
     * 进入房间的ID.
     * @var string
     */
    public static $actionID = '1001';

    /**
     * 服务端动作名称.
     * @var string
     */
    public static $actionName = 'login';

    /**
     * 服务端动作类型.
     * @var string
     */
    public static $actionType = 'ChatHall';// 可以理解为动作名称的父名称。自定义

    /**
     * 动作描述.
     * @var string
     */
    public static $actionContent = '登录并进入房间';

    // 用户属性，后期需要整理后移动到父类。

    /**
     * 用户访问接口的token，可能会增减.
     * @var integer
     */
    public $token;

    /**
     * 用户类型,也许用不到.
     * @var integer
     */
    public $userType = 4;

    /**
     * 开始时定时器.
     *
     * @var string
     */
    public $beginTimerId;

    /**
     * 结束时定时器.
     *
     * @var string
     */
    public $endTimerId;

    /**
     * 发送消息定时器.
     *
     * @var string
     */
    public $sendMsgTimerId;


    /**
     * 预备动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function beforeAction()
    {
        parent::beforeAction();
    }

    /**
     * 开始动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function beginAction()
    {
        if (!isset($this->sendMsgTimerId)) {
            // 每1秒发一句消息
            $this->sendMsgTimerId = Timer::add(1, function() {
                // 发送一句随机聊天的话。
                try{
                    $contentList = [
                        '恭喜恭喜！红包拿来！',
                        '周末,你都做些什么？',
                        '哦。',
                        '奥',
                        '再见！',
                        '你好！',
                        '人很多嘛！',
                        '我喜欢！',
                        '你喜欢我吗？',
                        '啥？',
                        '干嘛？',
                        '我先走了,你自己慢慢玩。',
                        '好的,再见！',
                        '恭喜发财！',
                        '一路顺风！',
                        '你在做什么？',
                        '我在压测呢。',
                        '哎呀！',
                        '你是谁？',
                    ];

                    $data = [
                        "type" => 'say',
                        "content" => $contentList[array_rand($contentList)],
                        'to_user_id' => 'all',
                    ];

                    $this->client->send(json_encode($data, true));

                    var_dump(json_encode($data, true));
                    echo "\n发送消息\n";
                } catch (Exception $e) {
                    echo '获取信息失败';
                    var_dump($e->getMessage());
                    //...
                }
            });
        }

    }

    /**
     * 结束动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function endAction()
    {
        if (!isset($this->endTimerId)) {
            // 3秒后退出房间
            $this->endTimerId = Timer::add(3, function() {
                try {
//                    $data = [
//                        "type" => 'logout',
//                    ];
//                    $this->client->send(json_encode($data, true));
                    $this->client->close();

                    Timer::del($this->beginTimerId);
                    Timer::del($this->sendMsgTimerId);
                    Timer::del($this->endTimerId);

                    echo "退出聊天室,中断连接\n";
                    echo self::$actionContent."结束\n";

                } catch (Exception $e) {
                    echo '获取信息失败';
                    var_dump($e->getMessage());
                }
            });
        }


    }

    /**
     * 收尾动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function afterAction()
    {

    }

    /**
     * 动作类型.
     *
     * @return array (code=>0, msg=>array())
     */
    public function getActionType()
    {

    }
}
