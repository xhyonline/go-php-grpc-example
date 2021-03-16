# go-php-grpc-example
Golang 服务端,PHP客户端 的 GRPC 示例代码

博客地址:https://www.xhyonline.com/?p=1416

## 一、安装环境

我们需要编写 `.proto` 文件,然后通过 protoc 命令编译,因此需要安装 protoc 

地址：https://github.com/protocolbuffers/protobuf/releases

![](http://picture.xhyonline.com/imgs/2021/03/606322cadf88fd7b.png)

我是 windows 环境，因此就下载了这个压缩包，然后将 bin 目录下的 exe 添加至环境变量

![](http://picture.xhyonline.com/imgs/2021/03/548f7c6bcd997f6f.png)

此时在命令行窗口执行 protoc --version，当你看到下面这个界面时，代表安装成功了

![](http://picture.xhyonline.com/imgs/2021/03/c6b9ed0b54b04387.png)

### (一)、Golang 相关插件

此时正式进入 Golang 相关插件的安装，涉及到 Golang 的其实只有一个，`protoc-gen-go` 。它是用来配合 protoc 命令来生成 go 文件的。

安装方式如下:

地址:github.com/golang/protobuf/

![](http://picture.xhyonline.com/imgs/2021/03/1e405edc5f625186.png)

下载整个项目,然后进入上图中 protoc-gen-go 目录中,自行使用 go build 进行编译,编译完成的 二进制文件 protoc-gen-go.exe 请一定放在 $GOPATH/bin 目录中,因为编译器`protoc`必须在`$PATH`中能找到它。

### (二)、PHP 相关扩展

PHP 必须安装 grpc 扩展。

地址如下:http://pecl.php.net/package/gRPC

由于我在 windows 环境下我就直接安装了 windows 下PHP的扩展,并且选择自己PHP对应的版本

![](http://picture.xhyonline.com/imgs/2021/03/18bbf923ace14a9f.png)

![](http://picture.xhyonline.com/imgs/2021/03/8106702699ac6a97.png)

下载完毕后,我们只需要 php_grpc.dll 文件,如下所示

![](http://picture.xhyonline.com/imgs/2021/03/efd68f002ed8885a.png)

我们将其拷贝到PHP的扩展目录  `D:\phpstudy_pro\Extensions\php\php7.3.4nts\ext`,然后在`php.ini`文件中添加`extension=grpc` ,然后重启 php-fpm 后使用命令行 php -m 查看扩展是否安装成功。

![](http://picture.xhyonline.com/imgs/2021/03/984d48c70e8af7e8.png)

其次我们必须安装 composer ,因为可以通过 composer 自动加载来发现类。

composer 的安装方式这里就不展示了。

## 二、正式开始

### (一)、编写 proto 文件

目录结构如下所示

![](http://picture.xhyonline.com/imgs/2021/03/6a184d3cee443267.png)

1. user.proto 文件内容如下

   ```
   syntax = "proto3";
   
   package user;
   
   option go_package="github.com/xhyonline/main/proto/user";
   
   import "basic/basic.proto";
   
   message User {
     string Name =1;
     int64 Age =2;
   }
   
   service UserService {
     // 调用 SayHello 方法,不需要传参,但是由于入参和返回必须是个 message 类型
     // 因此我们可以把它拆出来
     rpc SayHello(basic.Empty) returns (basic.Empty);
     rpc AddUser(User) returns (User);
   }
   ```

2.  basic.proto 文件如下

   ```
   syntax = "proto3";
   
   package basic;
   
   option go_package="github.com/xhyonline/main/proto/basic";
   
   message Empty {
   
   }
   ```

### (二)、编译服务端文件

然后在 proto 目录下执行如下命令,用于编译 user 模块下与 basic 模块下的 proto 文件 (**请注意:我非常建议你切换到 proto 目录下 ,否则会因为路径依赖问题导致编译失败 **,)

```
protoc --go_out=plugins=grpc,paths=source_relative:. ./user/*.proto
```

```
protoc --go_out=plugins=grpc,paths=source_relative:. ./basic/*.proto
```

经过上面两个命令,我们就能生成如下述所示的两个文件

![](http://picture.xhyonline.com/imgs/2021/03/4ffb87a64aabaee0.png)

### (三)、编写服务端 

文件:main.go

```
package main

import (
	"fmt"
	"github.com/xhyonline/main/proto/basic"
	"github.com/xhyonline/main/proto/user"

	"context"
	"google.golang.org/grpc"
	"log"
	"net"
)

type User struct {

}

// 实现 proto 文件中 SayHello 的接口
func (s *User) SayHello (ctx context.Context, in *basic.Empty) (*basic.Empty,error){
	fmt.Println("你好世界")
	return nil,nil
}

// 实现 proto 文件中 AddUser 的接口
func (s *User) AddUser(ctx context.Context,user *user.User)(*user.User ,error){
	fmt.Printf("新增一个用户的基本信息是 %+v\n",user)
	user.Name="强行修改了用户的名字"
	return user,nil
}

func main() {
	// 实例化一个 grpc 服务
	g := grpc.NewServer()
	// 绑定
	user.RegisterUserServiceServer(g,new(User))
	// grpc 监听在 8080 端口
	l, err := net.Listen("tcp", "0.0.0.0:8080")
	if err != nil {
		log.Fatal(err)
	}
	// 服务启动
	err=g.Serve(l)
	if err!=nil {
		panic(err)
	}
}
```

使用 go run main.go 启动服务端

### (四)、编译客户端文件

幸运的是 PHP 编译 proto 文件其实不用装类似于 protoc-gen-go 这种 Go扩展,他可以直接通过命令就可以编译。

先看 PHP 项目的目录结构(请注意 gen 目录此时还为空)

![](http://picture.xhyonline.com/imgs/2021/03/e9ada55ff92b4dfa.png)

执行命令:编译出 php 文件 (**与Go编译一样,请注意路径,我都是切换到 proto 目录下进行编译**)

先编译 user 模块

```
protoc --php_out=. ./user/*.proto
```

再编译 basic 模块

```
protoc --php_out=. ./basic/*.proto
```

编译后,生成的文件如下所示

![](http://picture.xhyonline.com/imgs/2021/03/5092b3bc7e122851.png)

为了养成编码规范,我比较喜欢将文件分开存放,因此上面的 gen 目录在此就派上了用场,迁移生成好的文件如下所示。

其实也就是将 `proto/user` 目录下编译的文件放到 `gen/user` 中,将 basic 目录下编译的文件,放到 `gen/basic` 中,最后一个公共目录 `GPBMetadata` 也将它迁移至 gen 目录下。

![](http://picture.xhyonline.com/imgs/2021/03/53ee1046966d9bad.png)

### (五)、编写客户端

首先,我们先需要使用 composer init 在根目录下生成 composer.json 文件,其次我们还需依赖几个库。

分别是: "grpc/grpc": "^v1.3.0" 与  "google/protobuf": "^v3.3.0"

其次我们要把刚生成的PHP文件添加到 composer 中,使其自动加载

```
{
    "name": "edz/php",
    "authors": [
        {
            "name": "兰陵美酒郁金香",
            "email": "383164014@qq.com"
        }
    ],
    "require": {
        "grpc/grpc": "^v1.3.0",
        "google/protobuf": "^v3.3.0"
    },
    "autoload":{
        "psr-4":{
            "GPBMetadata\\": "gen/GPBMetadata/",
            "User\\":"gen/user/",
            "Basic\\":"gen/basic/"
        }
    }
}

```

执行 composer install 安装扩展

autoload 解释:

psr-4 是一个自动加载的规范,我们拿上述文件中的 ` "User\\":"gen/user/"` 举例:`User` 代表它的命名空间, `gen/User/`代表该命名空间的真实路径。因此如果我们下次在index.php文件中引用了 `vendor/autoload.php` 文件后,就可以直接使用 \User\类名::静态方法() 的方式调用了。

*再插一嘴* : 在 composer 中的 autoload 区域更新类的配置信息,需要 `composer dump-autoload`,否则类加载不到。

其次我们要编写客户端文件,因此我们需要在 gen/user/目录下再新建一个 UserClient.php

![](http://picture.xhyonline.com/imgs/2021/03/42f6e66b8a7658ce.png)

编写内容如下

```
<?php

namespace User;

class UserClient extends \Grpc\BaseStub
{
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }
    // 编写 AddUser 方法
    public function AddUser(\User\User $argument,$metadata=[],$options=[]){
        // user.UserService/AddUser 是请求了服务端对应的方法
        // 格式: proto 对应的包名.service 服务名/服务中的方法名
        return $this->_simpleRequest('user.UserService/AddUser',
            $argument,
            // \User\User 是返回参数,proto文件编译后,自动生成了这个类型,由于 composer autoload 的存在,我们可			// 以直接加载到 \User\User 类,这个类有点类似于 Golang 的结构体(讲道理其实就是.....)
            ['\User\User', 'decode'],
            $metadata, $options);
    }
	// 编写 SayHello 方法
    public function SayHello(\Basic\PBEmpty $argument,$metadata=[],$options=[]){
        // user.UserService/SayHello 调用了proto 中 user包下的UserService 中的 SayHello 方法
        return $this->_simpleRequest('user.UserService/SayHello',
            $argument,
            // \User\User 是返回参数
            ['\Basic\PBEmpty', 'decode'],
            $metadata, $options);
    }
}
```

然后开始编写 index.php 入口代码

```
<?php

require_once 'vendor/autoload.php';

// 由于我们在 composer.json 中定义了 autoload  ==> "User\\":"gen/user/",
// 因此它在看到 User 这个命名空间后,会自动去 gen/user 目录下寻找 UserClient 类
$client=new \User\UserClient("127.0.0.1:8080",[
    'credentials' => \Grpc\ChannelCredentials::createInsecure()
]);

//实例化 TestRequest 请求类
$request = new \User\User();
$request->setAge(24);
$request->setName("小陈");
// 调用远程服务
$get = $client->AddUser($request)->wait();

list($reply, $status) = $get;

var_dump($reply->getName());

$request=new Basic\PBEmpty();
// 远程调用 SayHello 方法
$client->SayHello($request);

```

最后执行命令 php index.php 你就能看到这个效果

![](http://picture.xhyonline.com/imgs/2021/03/2f0b37baadf8e39d.png)



![](http://picture.xhyonline.com/imgs/2021/03/a1ea54066a20f75e.png)



