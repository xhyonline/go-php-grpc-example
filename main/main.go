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