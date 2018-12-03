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
    public function userList(Request $request)
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 0);

        $where = [];
        $rid = $request->input('rid', 0);
        $rid ? ($where['rid'] = $rid) : '';

        $account = $request->input('account', '');

        DB::connection('mysql_crm')->enableQueryLog();  // 开启QueryLog
        $query = DB::connection('mysql_crm')
            ->table('admin_users')
            ->select('admin_users.*', 'admin_role.name')
            ->join('admin_role', 'admin_users.rid', '=', 'admin_role.id')
            ->where($where);
        if ($account) {
            $query->where('admin_users.account', 'like', '%' . $account . '%');
        }

        $sort = $request->input('sort', '-id');
        if (stripos($sort, '+') !== false) {
            $sort_method = 'asc';
        } else {
            $sort_method = 'desc';
        }
        $sort_key = substr($sort, 1);
        if ($sort_key == 'name') {
            $sort_key = 'admin_role.' . $sort_key;
        }

        $users['total'] = $query->count();
        $query = $query->orderBy($sort_key, $sort_method);

        $page = $page ? $page - 1 : 0;
        $items = $query->offset($page)->limit($limit)->get();
        $log[] = DB::connection('mysql_crm')->getQueryLog();

        $users['roles'] = DB::connection('mysql_crm')->table('admin_role')->get();
        //$users['roles']['all'] = '所有角色';

        $users['items'] = [];
        foreach ($items as &$v) {
            $v = (array)$v;
            $v['create_dateline'] = $v['create_dateline'] ? date('Y-m-d H:i:s', $v['create_dateline']) : '';
            $v['last_login_dateline'] = $v['last_login_dateline'] ? date('Y-m-d H:i:s', $v['last_login_dateline']) : '';
            $v['disabled_desc'] = $v['disabled'] ? '禁用' : '';
            $users['items'][] = $v;
        }
        unset($v);

        $users['log'] = $log;

        $data = [
            'code' => 20000,
            'data' => $users,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 更新用户列表
     */
    public function updateUser(Request $request)
    {
        $id = $request->input('id');
        $code = 20000;
        if (!$id) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $password = $request->input('password');
        if ($password && $password != '********') {
            $data['password'] = $password;
        }

        $data['disabled'] = $request->input('disabled');
        $data['email'] = $request->input('email');
        $data['account'] = $request->input('account');
        $data['rid'] = $request->input('rid');
        $data['create_dateline'] = time();
        DB::connection('mysql_crm')->table('admin_users')->where('id', $id)->update($data);

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 创建用户列表
     */
    public function createUser(Request $request)
    {
        $code = 20000;

        $data['account'] = $request->input('account');
        if (!$data['account']) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $password = $request->input('password');
        if ($password && $password != '********') {
            $data['password'] = $password;
        }

        $data['disabled'] = $request->input('disabled');
        $data['email'] = $request->input('email');
        $data['rid'] = $request->input('rid');
        $data['create_dateline'] = time();

        $data['id'] = DB::connection('mysql_crm')->table('admin_users')->insertGetId($data);
        if (!$data['id']) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => $data,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除用户列表
     */
    public function deleteUser(Request $request)
    {
        $code = 20000;
        $id = $request->input('id');
        if (!$id) {
            $data = [
                'code' => 500,
                'data' => [],
            ];
            return response()->json($data);
        }

        $res = DB::connection('mysql_crm')->table('admin_users')->delete($id);
        if (!$res) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 权限角色列表
     */
    public function roleList(Request $request)
    {
        DB::connection('mysql_crm')->enableQueryLog();  // 开启QueryLog
        $items = DB::
        connection('mysql_crm')->table('admin_role')->get();

        $roles['items'] = $items;
        $data = [
            'code' => 20000,
            'data' => $roles,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 更新角色列表
     */
    public function updateRole(Request $request)
    {
        $id = $request->input('id');
        $data['name'] = $request->input('name');
        $code = 20000;
        if (!$id || !$data['name']) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $data['desc'] = $request->input('desc', '');

        DB::connection('mysql_crm')->table('admin_role')->where('id', $id)->update($data);

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 创建角色列表
     */
    public function createRole(Request $request)
    {
        $code = 20000;
        $data['name'] = $request->input('name');
        if (!$data['name']) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $data['desc'] = $request->input('desc', '');
        $data['menu'] = $data['catalog'] = '';

        $data['id'] = DB::connection('mysql_crm')->table('admin_role')->insertGetId($data);
        if (!$data['id']) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => $data,
        ];
        return response()->json($data);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除角色列表
     */
    public function deleteRole(Request $request)
    {
        $code = 20000;
        $id = $request->input('id');
        if (!$id) {
            $data = [
                'code' => 500,
                'data' => [],
            ];
            return response()->json($data);
        }

        $res = DB::connection('mysql_crm')->table('admin_role')->delete($id);
        if (!$res) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 权限菜单列表
     */
    public function menuList(Request $request)
    {
        DB::connection('mysql_crm')->enableQueryLog();  // 开启QueryLog
        $items = DB::connection('mysql_crm')->table('admin_menu')->orderBy('pid','asc')->get();

//        data:
//        {
//          id:1,
//          event: '事件1',
//          timeLine: 100,
//          comment: '无',
//          children: [
//            {
//              id:2,
//              event: '事件2',
//              timeLine: 10,
//              comment: '无'
//            },
//          ]
//        }
        $tmp = [];
        //无限极分类
        //构造函数
        foreach ($items as $v) {
            $v = (array)$v;
            $tmp[$v['id']] = $v;
        }

        //遍历数据生成tree
        $tree = $indexs = [];
        foreach ($tmp as $k=>$v) {
            $tmp[$k]['event'] = $v['name'];
            if(isset($tmp[$v['pid']])){
                $tmp[$v['pid']]['children'][] = &$tmp[$k];
            }else{
                $tree[] = &$tmp[$k];
            }

            $indexs[$v['id']] = $v['index'];

//            $tmp[$v['id']] = [
//                'id' => $v['id'],
//                'pid' => $v['pid'],
//                'event' => $v['name'],
//                'timeLine' => 100,
//                'comment' => '无'
//            ];
        }
        unset($v);
        $users['items']   = $tree;
        $users['indexs'] = $indexs;

        $data = [
            'code' => 20000,
            'data' => $users,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 更新菜单列表
     */
    public function updateMenuSort(Request $request)
    {
        $ids = $request->input('id');
        $code = 20000;
        if (!$ids) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        foreach ($ids as $k=>$v){
            $data['index'] = $v;
            $ids[$k] = DB::connection('mysql_crm')->table('admin_menu')->where('id', $k)->update($data);
        }

        $data = [
            'code' => $code,
            'data' => $ids,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 更新菜单列表
     */
    public function updateMenu(Request $request)
    {
        $id = $request->input('id');
        $code = 20000;
        if (!$id) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $password = $request->input('password');
        if ($password && $password != '********') {
            $data['password'] = $password;
        }

        $data['disabled'] = $request->input('disabled');
        $data['email'] = $request->input('email');
        $data['account'] = $request->input('account');
        $data['rid'] = $request->input('rid');
        $data['create_dateline'] = time();
        DB::connection('mysql_crm')->table('admin_users')->where('id', $id)->update($data);

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 创建菜单列表
     */
    public function createMenu(Request $request)
    {
        $code = 20000;

        $data['account'] = $request->input('account');
        if (!$data['account']) {
            $code = 500;
            $data = [
                'code' => $code,
                'data' => [],
            ];
            return response()->json($data);
        }

        $password = $request->input('password');
        if ($password && $password != '********') {
            $data['password'] = $password;
        }

        $data['disabled'] = $request->input('disabled');
        $data['email'] = $request->input('email');
        $data['rid'] = $request->input('rid');
        $data['create_dateline'] = time();

        $data['id'] = DB::connection('mysql_crm')->table('admin_users')->insertGetId($data);
        if (!$data['id']) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => $data,
        ];
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除菜单列表
     */
    public function deleteMenu(Request $request)
    {
        $code = 20000;
        $id = $request->input('id');
        if (!$id) {
            $data = [
                'code' => 500,
                'data' => [],
            ];
            return response()->json($data);
        }

        $res = DB::connection('mysql_crm')->table('admin_users')->delete($id);
        if (!$res) {
            $code = 500;
        }

        $data = [
            'code' => $code,
            'data' => [],
        ];
        return response()->json($data);
    }
}
