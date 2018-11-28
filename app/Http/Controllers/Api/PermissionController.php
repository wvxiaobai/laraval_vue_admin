<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 权限用户列表
     */
    public function userList(Request $request){
        $limit = $request->input('limit');
        $page  = $request->input('page');
        $items = DB::connection('mysql_crm')->table('admin_users')
            ->offset($page)->limit($limit)
            ->get();
        $users['items'] = [];
        foreach ($items as &$v){
            $v = (array)$v;
            $v['create_dateline'] = $v['create_dateline']?date('Y-m-d H:i:s',$v['create_dateline']):'';
            $v['last_login_dateline'] = $v['last_login_dateline']?date('Y-m-d H:i:s',$v['last_login_dateline']):'';
            $users['items'][] =$v;
        }
        unset($v);

        $users['total'] = DB::connection('mysql_crm')->table('admin_users')->count();

        $data = [
            'code'=>20000,
            'data'=>$users,
        ];
        return response()->json($data);
    }
}
