<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use App\Services\Admin\GroupService;
use App\Services\Admin\SalesmanService;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    protected $visit;
    protected $request;
    protected $customer;
    protected $salesman;
    protected $group;

    public function __construct(VisitService $visit,
                                Request $request,
                                CustomerService $customer,
                                SalesmanService $salesman,
                                GroupService $group)
    {
        $this->visit = $visit;
        $this->request = $request;
        $this->customer = $customer;
        $this->salesman = $salesman;
        $this->group = $group;
    }

    /**
     * 记录列表
     *
     * @param $page [页码]
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($keyword = null)
    {
        $customers = $this->customer->get();

        $salesmans = $this->salesman->get();

        $groups = $this->group->get();

        $num = config('site.list_num');

        $lists = $this->visit->get($num, $keyword);

        return view('admin.visit.list', [
            'lists' => $lists,
            'customers' => $customers,
            'salesmans' => $salesmans,
            'groups' => $groups,
        ]);
    }

    /**
     * 添加管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        $customers = $this->customer->get();

        $result = [];

        //添加回访记录只能添加自己的直属客户，这里过滤
        foreach ($customers as $customer) {
            if ($customer['salesman_id'] == Auth::id()) {
                $result[] = $customer;
            }
        }

        return view('admin.visit.add_or_update', [
            'old_input' => $this->request->session()->get('_old_input'),
            'url' => Route('visit_add'),
            'sign' => 'add',
            'customers' => $result,
        ]);
    }

    /**
     * 修改管理员视图
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateView($id)
    {
        try {
            $old_input = $this->request->session()->has('_old_input') ?
                session('_old_input') : $this->visit->first($id);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }

        return view('admin.visit.add_or_update', [
            'old_input' => $old_input,
            'url' => Route('visit_update', ['id' => $id]),
            'sign' => 'update'
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

        return redirect()->route('visit_list');
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        try {
            $this->visit->destroy($id);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return redirect()->route('visit_list');
    }
}