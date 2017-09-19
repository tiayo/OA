<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use App\Services\Admin\GroupService;
use App\Services\Admin\SalesmanService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customer;
    protected $group;
    protected $salesman;
    protected $request;

    public function __construct(CustomerService $customer,
                                GroupService $group,
                                SalesmanService $salesman,
                                Request $request)
    {
        $this->customer = $customer;
        $this->group = $group;
        $this->salesman = $salesman;
        $this->request = $request;
    }

    /**
     * 记录列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listView($keyword = null)
    {
        $num = config('site.list_num');

        $customers = $this->customer->get($num, $keyword);

        $group = $this->group->get();

        $salesmans = $this->salesman->get();

        return view('admin.customer.list', [
            'customers' => $customers,
            'groups' => $group,
            'salesmans' => $salesmans,
        ]);
    }

    /**
     * 根据分组查看客户
     *
     * @param $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listByGroup($group)
    {
        $num = config('site.list_num');

        $customers = $this->customer->getByGroup($num, $group);

        $group = $this->group->get();

        $salesmans = $this->salesman->get();

        return view('admin.customer.list', [
            'customers' => $customers,
            'groups' => $group,
            'salesmans' => $salesmans,
        ]);
    }

    /**
     * 根据业务员查看客户
     *
     * @param $group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listBySalesman($salesman)
    {
        $num = config('site.list_num');

        $customers = $this->customer->getBySalesman($num, $salesman);

        $group = $this->group->get();

        $salesmans = $this->salesman->get();

        return view('admin.customer.list', [
            'customers' => $customers,
            'groups' => $group,
            'salesmans' => $salesmans,
        ]);
    }

    /**
     * 添加管理员视图
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin.customer.add_or_update', [
            'old_input' => $this->request->session()->get('_old_input'),
            'url' => Route('customer_add'),
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
            $old_input = $this->request->session()->has('_old_input') ?
                session('_old_input') : $this->customer->first($id);
        } catch (\Exception $e) {
            return response($e->getMessage(), $e->getCode());
        }

        return view('admin.customer.add_or_update', [
            'old_input' => $old_input,
            'url' => Route('customer_update', ['id' => $id]),
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
            'phone' => 'required',
            'company' => 'required',
        ]);

        //验证唯一性
        try {
            $this->customer->unique($this->request->all(), $id);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput($this->request->all())
                ->withErrors($e->getMessage());
        }

        //执行更新操作
        $this->customer->updateOrCreate($this->request->all(), $id);

        return redirect()->route('customer_list');
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        if ($this->customer->destroy($id)) {
            return redirect()->route('salesman_list');
        }

        return response('删除失败！', 500);
    }
}