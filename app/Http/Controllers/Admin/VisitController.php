<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use App\Services\VisitService;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    protected $visit;
    protected $request;
    protected $customer;

    public function __construct(VisitService $visit, Request $request, CustomerService $customer)
    {
        $this->middleware('visit_control');

        $this->visit = $visit;
        $this->request = $request;
        $this->customer = $customer;
    }

    /**
     * 记录列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($page)
    {
        $customers = $this->customer->get(1, 10000);

        $num = config('site.list_num');

        $lists = $this->visit->get($page, $num);

        return view('admin.visit.list', [
            'lists' => $lists,
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->visit->countGet() / $num),
            'customers' => $customers,
            'sign' => 'list',
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

        $lists = $this->visit->get($page, $num, $keyword);

        $customers = $this->customer->get(1, 10000);

        return view('admin.visit.list', [
            'lists' => $lists['data'],
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($lists['count'] / $num),
            'customers' => $customers,
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
        $customers = $this->customer->get(1, 10000);

        return view('admin.visit.add_or_update', [
            'old_input' => $this->request->session()->get('_old_input'),
            'url' => Route('visit_add'),
            'sign' => 'add',
            'customers' => $customers,
        ]);
    }

    /**
     * 修改管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateView($id)
    {
        //获取当前业务员所有客户
        $customers = $this->customer->get(1, 10000);

        //从session获取
        $old_input = session('_old_input');

        if (empty($old_input)) {
            //从数据库获取
            $old_input = $this->visit->first($id);
        }

        return view('admin.visit.add_or_update', [
            'old_input' => $old_input,
            'url' => Route('visit_update', ['id' => $id]),
            'sign' => 'update',
            'customers' => $customers,
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
        $this->validate($this->request, [
            'record' => 'required',
        ]);

        //添加记录判断
        if (empty($id)) {
            $this->validate($this->request, [
                'customer_id' => 'required|integer',
            ]);
        }

        //执行操作
        $this->visit->updateOrCreate($this->request->all(), $id);

        return redirect()->route('visit_list_simple');
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
            $this->visit->destroy($id);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return redirect()->route('visit_list_simple');
    }
}