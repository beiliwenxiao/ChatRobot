<?php
/**
 * 机器人动作基类
 * @Author : beiliwenxiao <beiliwenxiao@qq.com>
 * @Blog： https://blog.csdn.net/beiliwenxiao https://www.cnblogs.com/codeaaa
 * @DateTime : 2024-09-16
 */
namespace Robot;

use Robot\RobotActionInterface;
use Workerman\Connection\AsyncTcpConnection;

abstract class RobotAction implements RobotActionInterface
{

    /**
     * 模拟的客户端webSocket对象.
     *
     * @var \Workerman\Connection\AsyncTcpConnection
     */
    public $client;

    /**
     * 用户ID.
     * @var integer
     */
    public $userId;

    /**
     * 用户名.
     * @var string
     */
    public $userName;

    /**
     * 昵称.
     * @var string
     */
    public $nickName;

    /**
     * 房间ID.
     * @var string
     */
    public $roomId;


    /**
     * 获得动作编号.
     *
     * @return string
     */
    public function getActionID()
    {
        return static::$actionID;
    }

    /**
     * 获得动作名称.
     *
     * @return string
     */
    public function getActionName()
    {
        return static::$actionName;
    }

    /**
     * 获取动作类型，如 live、playGame、playSomeGame、chat、sayHi.
     *
     * @return type
     */
    public function getActionType()
    {
        return static::$actionType;
    }

    /**
     * 获得动作描述.
     *
     * @return string
     */
    public function getActionContent()
    {
        return static::$actionContent;
    }

    /**
     * 预备动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function beforeAction()
    {
        // 如果未链接或没有链接，则进行链接初始化。
        $isNotConnect = empty($this->client);
        if ($isNotConnect) {

            /**
             * 以websocket协议连接远程websocket服务器
             */
            $this->client = new AsyncTcpConnection(\Config\Config::$chatRoomWebSocketAddress);

            // 连上后发送hello字符串
            $this->client->onConnect = function($connection) {
                echo "连接聊天室成功\n";
                echo static::$actionContent."开始\n";
            };

            // 远程websocket服务器发来消息时
            $this->client->onMessage = function($connection, $message) {
                echo "recv: $message\n";
                $msg = json_decode($message, true);

                // 这里的处理，是根据workerman-chat的代码确认的。
                if ($msg['type'] == 'init') {

                    $data = [];
                    $data["type"] = 'login';
                    $data["client_name"] = $this->nickName;
                    $data["room_id"] = $this->roomId;
                    $data["user_id"] = $this->userId;
                    $connection->send(json_encode($data, true));
                }

                // 收到服务端心跳时，返回心跳。
                if ($msg['type'] == 'ping') {

                    $data = [];
                    $data["type"] = 'pong';
                    $this->client->send(json_encode($data, true));
                }

            };
            // 连接上发生错误时，一般是连接远程websocket服务器失败错误
            $this->client->onError = function($connection, $code, $msg) {
                echo "error: $msg\n";
            };

            // 当连接远程websocket服务器的连接断开时
            $this->client->onClose = function($connection) {
                echo " connection closed\n";
            };

            // 设置好以上各种回调后，执行连接操作
            $this->client->connect();

        }
    }

    /**
     * 开始动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function beginAction()
    {

    }

    /**
     * 结束动作.
     *
     * @return array (code=>0, msg=>array())
     */
    public function endAction() {

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
     * 执行动作
     * @return array (code=>0, msg=>array())
     */
    public function doAction()
    {
        $this->beforeAction();
        $this->beginAction();
        $this->endAction();
        $this->afterAction();
    }

}
