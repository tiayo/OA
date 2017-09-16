<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\GroupService;
use App\Services\Admin\SalesmanService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesmanController extends Controller
{
    protected $salesman;
    protected $request;
    protected $group;

    public function __construct(SalesmanService $salesman, Request $request, GroupService $group)
    {
        $this->salesman = $salesman;
        $this->request = $request;
        $this->group = $group;
    }

    /**
     * 管理员列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($keyword = null)
    {
        $num = config('site.list_num');

        $salesman = $this->salesman->get($num, $keyword);

        return view('admin.salesman.list', [
            'salesman' => $salesman,
        ]);
    }

    /**
     * 添加管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        $groups = $this->group->get();

        return view('admin.salesman.add_or_update', [
            'groups' => $groups,
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
        $groups = $this->group->get();

        try {
            $old_input = $this->request->session()->has('_old_input') ?
                session('_old_input') : $this->salesman->first($id);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }

        return view('admin.salesman.add_or_update', [
            'groups' => $groups,
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
            'group' => 'integer',
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

        return redirect()->route('salesman_list');
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
            return response($e->getMessage(), 500);
        }

        return redirect()->route('salesman_list');
    }
}