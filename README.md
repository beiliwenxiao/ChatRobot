ChatRobot
=======
批量生成聊天机器人，并维持连接和动作。
可以访问ChatHall（forked by workerman-chat）聊天室。


机器人说明
=======

机器人动作包括：注册、登录、进出房间、发信息、发礼物、发弹幕、聊天、获取数据、删除注册等等，均需自行编写。当然，也可以直接接入ai接口等。
机器人动作的策略： 动作、数量、触发条件、执行周期、执行时间。可以有多条。

动作的触发条件、执行周期、执行时间，在Robot\RobotCreator.php里编写。

动作的具体内容在Robot\EnterRoom.php是可供参考的例子。

下载安装
=====
1、git clone https://github.com/beiliwenxiao/ChatRobot

2、composer install

启动停止(Linux系统)
=====
以debug方式启动
```php start.php start  ```

以daemon方式启动
```php start.php start -d ```


