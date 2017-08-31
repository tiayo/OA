<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customer;
    protected $request;

    public function __construct(CustomerService $customer, Request $request)
    {
        $this->customer = $customer;
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

        $customers = $this->customer->get($page, $num);

        return view('admin.customer.list', [
            'customers' => $customers,
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->customer->countGet() / $num),
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

        $customers = $this->customer->get($page, $num, $keyword);

        return view('admin.customer.list', [
            'customers' => $customers['data'],
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($customers['count'] / $num),
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
        if ($this->request->session()->has('_old_input')) {
            //从session获取
            $old_input = session('_old_input');
        } else {
            //从数据库获取
            try {
                $old_input = $this->customer->first($id);
            } catch (\Exception $e) {
               return response($e->getMessage(), 403);
            }
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

        return redirect()->route('customer_list_simple');
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
            $this->customer->destroy($id);
        } catch (\Exception $e) {
            return response($e->getMessage());
        }

        return redirect()->route('customer_list_simple');
    }
}