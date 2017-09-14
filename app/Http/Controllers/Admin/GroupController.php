<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\GroupService;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $group;
    protected $request;

    public function __construct(GroupService $group, Request $request)
    {

        $this->group = $group;
        $this->request = $request;
    }

    /**
     * 记录列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($page)
    {
        $num = config('site.list_num');

        $groups = $this->group->get($page, $num);

        return view('admin.group.list', [
            'groups' => $groups,
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->group->countGet() / $num),
            'sign' => 'list'
        ]);
    }

    /**
     * 搜索记录
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search($page, $keyword)
    {
        $num = config('site.list_num');

        $groups = $this->group->get($page, $num, $keyword);

        return view('admin.group.list', [
            'groups' => $groups['data'],
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->group->countGet() / $num),
            'sign' => 'search'
        ]);
    }

    /**
     * 添加管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin.group.add_or_update', [
            'old_input' => $this->request->session()->get('_old_input'),
            'url' => Route('group_add'),
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
        if ($this->request->session()->has('_old_input')) {
            //从session获取
            $old_input = session('_old_input');
        } else {
            //从数据库获取
            $old_input = $this->group->first($id);
        }

        return view('admin.group.add_or_update', [
            'old_input' => $old_input,
            'url' => Route('group_update', ['id' => $id]),
            'sign' => 'update',
        ]);
    }

    /**
     * 添加/更新提交
     *
     * @param null $id
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function post($id = null)
    {
        //初步验证
        $this->validate($this->request, [
            'salesman_id' => 'required|integer',
            'name' => 'required',
        ]);

        //执行更新操作
        $this->group->updateOrCreate($this->request->all(), $id);

        return redirect()->route('group_list_simple');
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        if (!$this->group->destroy($id)) {
            return response('删除失败！');
        }

        return redirect()->route('group_list_simple');
    }
}