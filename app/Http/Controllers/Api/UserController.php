<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 登录接口
     */
    public function login(){
        $data = [
            'code'=>20000,
            'data'=>[
                'token'=>'admin'
            ],


        ];
        return response()->json($data);
    }

    /**
     * 信息接口
     */
    public function info(){
        $data = [
            'code'=>20000,
            'data'=>[
                'roles'=>[
                    'admin'
                ],
                'name'=>'admin',
                'avatar'=>'http://img1.duimian.cn/MediaServerMblove/servlet/Proxy/PhotoServlet/FhZfAO2EX2Q-uidKxnqZp_fNLy5f.jpg'
            ],


        ];
        return response()->json($data);
    }
}
