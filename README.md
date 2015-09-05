#SwiftQuiz

SwiftQuiz 是一套基于ThinkPHP框架开发的在线答题系统，开放源代码。

##特点

* 支持图片、数学公式、文本格式
* 查看每个人的成绩，答题情况
* 按标签分类用户、试卷
* 对手机、平板等触屏设备的良好支持

##版本信息

SwiftQuiz 1.0 Beta

##关于开发者

DavidZYC2000

##许可

使用本软件需要遵守GPL协议

##反馈

如有任何问题请直接在GitHub上发送Issue

##致谢以下开源项目

* Bootstrap (前端框架)
* Bootswatch定制版Bootstrap主题：Paper (Material Design风格主题)
* jQuery (JavaScript库)
* CKEditor (HTML5富文本编辑器)
* ThinkPHP (MVC PHP框架)
* Chart.js (图表生成库)

##下一步

* 加入人工阅卷系统
* Excel CSV文件批量导入用户、题目
* 用户头像、积分系统
* 评论、互动系统

-----------------------------

# SwiftQuiz安装说明

##系统要求

* Windows Server / Linux 服务器操作系统
* Apache WEB服务器软件
* MySQL 数据库
* PHP5.3以上版本（注意：PHP5.3dev版本和PHP6均不支持）

##安装步骤

1. Apache 请在 httpd.conf 配置文件中启用 `mod_rewrite.so` 模块
2. 把压缩包内所有内容拷贝至 `www` 目录
3. 打开 `www/SwiftQuiz/Common/Conf/config.php`,修改以下内容

	'DB_TYPE' => 'mysql',  
	'DB_HOST' => '这里填数据库服务器地址',  
	'DB_NAME' => '这里填填数据库名',  
	'DB_PORT' => 这里填填数据库服务器端口,  
	'DB_PREFIX' => '这里填数据库表前缀',  
	'DB_PWD' => '这里填数据库密码',  
	'DB_USER' => '这里填数据库用户名',

4. 将根目录的 `SwiftQuiz.sql` 导入数据库
5. OK，可以开始使用了。

##第一次使用

安装完成后，可以删除`SwiftQuiz.sql`及`README.md`(本文档)

默认管理员账号
 
账号：admin
 
密码：12345678

**安装完成后，请立即更改默认管理员密码**
 
 