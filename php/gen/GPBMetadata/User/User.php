<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: user/user.proto

namespace GPBMetadata\User;

class User
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Basic\Basic::initOnce();
        $pool->internalAddGeneratedFile(
            '
?
user/user.protouser"!
User
Name (	
Age (2X
UserService&
SayHello.basic.Empty.basic.Empty!
AddUser
.user.User
.user.UserB&Z$github.com/xhyonline/main/proto/userbproto3'
        , true);

        static::$is_initialized = true;
    }
}

