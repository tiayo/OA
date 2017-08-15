<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalesmanService;
use Illuminate\Http\Request;

class SalesmanController extends Controller
{
    protected $salesman;
    protected $request;

    public function __construct(SalesmanService $salesman, Request $request)
    {
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
        $old_input = $this->request->session()->has('_old_input') ? session('_old_input') : $this->salesman->first($id);

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
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'min:6',
        ]);

        if (empty($id) && $id !== 0) {
            //执行添加操作
            $this->salesman->updateOrCreate($this->request->all());
        } else {
            //执行更新操作
            $this->salesman->updateOrCreate($this->request->all(), $id);
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

    /**
     * 搜索记录
     * #因为姓名邮箱都唯一，所以这里只获取一条
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search()
    {
        $keyword = $this->request->get('keyword');

        $salesman = $this->salesman->get(1, 1, $keyword);

        return view('admin.salesman.list', [
            'salesman' => [$salesman],
        ]);
    }
}