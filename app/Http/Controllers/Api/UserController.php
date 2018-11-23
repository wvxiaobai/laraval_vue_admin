<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 登录借口
     */
    public function login(){
        header('Access-Control-Allow-Origin:*');//允许所有来源访问
        header('Access-Control-Allow-Method:POST,GET');//允许访问的方式 　　
        $data = [
            'code'=>20000,
            'message'=>'ok',


        ];
        return response()->json('1111');
    }
}
