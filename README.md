# wxsdk
### 框架名称：wxsdk

框架简介：这是一个为快速开发微信应用而生的PHP框架。将微信的开发者功能，微信支付根据文档进行了封装。为了快速开发的目的，开发者完全不需要要知道具体是如何实现的，只需要简单的调用方法即可

开发语言：PHP

版本要求：原则PHP7.1以上

### 文档目录：
```
1、Access Token(初版完成)
2、自定义菜单(初版完成)
3、基础消息能力/事件推送/消息回复(初版完成)
4、模板消息
5、消息群发
6、微信网页开发
7、素材管理
8、用户管理
9、账号管理
10、微信发票
11、扫服务号二维码打开小程序
```

#以下为调用示例

## 1、Access Token
#### 1.1 获取Access Token
```phpregexp
$config = [
    'appid' => '',
    'secret' => '',
    'grant_type' => 'client_credential'
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->AccessToken()->getAccessToken();
```

#### 返回示例
```
跟官方返回格式一致
```

<br><br>

#### 1.2 获取Stable Access token
```phpregexp
$config = [
    'appid' => '',
    'secret' => '',
    'grant_type' => 'client_credential',
    'force_refresh' => false
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->AccessToken()->getStableAccessToken();
```

#### 返回示例
```
跟官方返回格式一致
```


## 2、自定义菜单
#### 2.1 获取菜单栏
```phpregexp
$config = [
    'appid' => '',
    'secret' => '',
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->getMenu(['access_token'=>'传入Access Token']);
```

#### 返回示例
```
跟官方返回格式一致
```

#### 2.2 创建菜单栏
```phpregexp
$config = [
    'appid' => '',
    'secret' => '',
];

//button格式和官方一致
$params = [
    'access_token'=>'传入Access Token',
    'button' => [
        ['name'=>'菜单1','sub_button'=>[
            ['type'=>'click','name'=>'点击','key'=>'key_111'],
            ['type'=>'view','name'=>'跳转','url'=>'http://www.baidu.com/'],
            ['type'=>'miniprogram','name'=>'小程序','url'=>'http://www.baidu.com/','appid'=>'wx9c1bffcf8ad42fe0','pagepath'=>'pages/homepage/homepage'],
            ['type'=>'scancode_push','name'=>'扫码推事件','key'=>'key_222','sub_button'=>[]],
            ['type'=>'scancode_waitmsg','name'=>'扫码带提示','key'=>'key_333','sub_button'=>[]],
        ]],
        ['name'=>'菜单2','sub_button'=>[
            ['type'=>'pic_sysphoto','name'=>'系统拍照发图','key'=>'key_444','sub_button'=>[]],
        ]]
    ]
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->createMenu($params);
```

#### 返回示例
```
跟官方返回格式一致
```

#### 2.3 删除菜单栏
```phpregexp
$config = [
    'appid' => '',
    'secret' => '',
];
$params = [
    'access_token'=>'传入Access Token',
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->deleteMenu($params);
```
#### 返回示例
```
跟官方返回格式一致
```

## 3、基础消息能力/事件推送/消息回复
#### 3.1 自动回复
PS：在公众号配置服务器地址(URL)，
比如服务器地址(URL)配置的是：https://baidu.com/demo.php，
那么在demo.php文件执行下面代码即可
```phpregexp
$token = ''; //对应公众号配置的令牌(Token)
$EncodingAESKey = ''; //对应公众号配置的消息加解密密钥(EncodingAESKey)

#接收事件推送的自动回复配置config（具体回复模板在下面）
$push_config = [
    //用户关注公众号
    'event.subscribe' => [
        #下面都是属于回复，具体看下面回复模板
        'msgTpl' => 'text',
        'Content' => '感谢关注公众号'
    ],

    //用户取消关注公众号
    'event.unsubscribe' => [
        'msgTpl' => 'text',
        'Content' => '很遗憾您取消关注了，我们下回再见'
    ],

    //点击菜单跳转链接时的事件推送
    'event.VIEW' => [
        'msgTpl' => 'text',
        'Content' => '即将跳转。。。'
    ],

    //点击事件推送(示例1)，key_111对应设置的菜单key
    'event.CLICK.key_111' => [
        'msgTpl' => 'text',
        'Content' => '你触发了设置的菜单栏目1。。。'

    ],
]

#接收用户发送普通消息到公众号的配置config
$text_config = [
]

\zhouzeken\wxsdk\Init::getInstance([
    'token' => $token,
    'EncodingAESKey' => $EncodingAESKey
])->receive()->start($push_config,$text_config);
```

<br>

#### 回复模板 如下
#### 3.1.1 文本消息
```phpregexp
[
    'msgTpl' => 'text',
    'Content' => '感谢关注公众号啊老铁。。。'
]
```
#### 3.1.2 图片消息
```phpregexp
[
    'msgTpl' => 'image',
    'MediaId' => '图片素材ID'
]
```
#### 3.1.3 语音消息
```phpregexp
[
    'msgTpl' => 'voice',
    'MediaId' => '语音素材ID'
]
```
#### 3.1.4 视频消息
```phpregexp
[
    'msgTpl' => 'video',
    'MediaId' => '视频素材ID',
    'Title' => '视频标题',
    'Description' => '视频描述'
]
```
#### 3.1.5 音乐消息
```phpregexp
[
    'msgTpl' => 'music',
    'Title' => '音乐标题',
    'Description' => '音乐链接',
    'MusicUrl' => '高质量音乐链接，WIFI环境优先使用该链接播放音乐',
    'ThumbMediaId' => '缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id'
]
```
#### 3.1.6 图文消息
```phpregexp
[
    'msgTpl' => 'news',
    'Content' => '感谢关注公众号啊老铁。。。'
]
```

#### 返回示例
```
该调用由微信触发，直接输出xml/text给到微信接口响应，再由微信发送到对应公众号用户
```