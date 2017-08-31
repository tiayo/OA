<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalesmanService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesmanController extends Controller
{
    protected $salesman;
    protected $request;

    public function __construct(SalesmanService $salesman, Request $request)
    {
        $this->middleware('salesman_control');

        $this->salesman = $salesman;
        $this->request = $request;
    }

    /**
     * 管理员列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($page)
    {
        $num = config('site.list_num');

        $salesman = $this->salesman->get($page, $num);

        return view('admin.salesman.list', [
            'salesman' => $salesman,
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->salesman->countGet() / $num),
            'sign' => 'list',
        ]);
    }

    /**
     * 搜索记录
     * #因为姓名邮箱都唯一，所以这里只获取一条
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($page, $keyword)
    {
        $num = config('site.list_num');

        $salesman = $this->salesman->get($page, $num, $keyword);

        return view('admin.salesman.list', [
            'salesman' => $salesman['data'],
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($salesman['count'] / $num),
            'sign' => 'search',
        ]);
    }

    /**
     * 添加管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin.salesman.add_or_update', [
            'old_input' => $this->request->session()->get('_old_input'),
            'url' => Route('salesman_add'),
            'sign' => 'add',
        ]);
    }

    /**
     * 修改管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateView($id)
    {
        try {
            $old_input = $this->request->session()->has('_old_input') ? session('_old_input') : $this->salesman->first($id);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return view('admin.salesman.add_or_update', [
            'old_input' => $old_input,
            'url' => Route('salesman_update', ['id' => $id]),
            'sign' => 'update',
        ]);
    }

    /**
     * 添加/更新提交
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function post($id = null)
    {
        $this->validate($this->request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'min:6',
            'type' => 'required',
        ]);

        //唯一性验证
        if (empty($id)) {
            $this->validate($this->request, [
                'name' => 'unique:users',
                'email' => 'unique:users',
            ]);
        }

        try {
            $this->salesman->updateOrCreate($this->request->all(), $id);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return redirect()->route('salesman_list_simple');
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        try {
            $this->salesman->destroy($id);
        } catch (\Exception $e) {

        }

        return redirect()->route('salesman_list');
    }
}