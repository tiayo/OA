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
            'customers' => $customers,
            'page' => $page == 1 ? 2 : $page,
            'current' =>  $page,
            'num' => $num,
            'count' => ceil($this->customer->countGet($keyword) / $num),
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
            $old_input =   session('_old_input');
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function post($id = null)
    {
        $this->validate($this->request, [
            'salesman_id' => 'required|integer',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'company' => 'required',
        ]);

        if (empty($id)) {

            //验证唯一性
            $this->validate($this->request, [
                'phone' => 'unique:customers',
                'email' => 'unique:customers',
            ]);

            //执行添加操作
            $this->customer->updateOrCreate($this->request->all());

        } else {

            //执行更新操作
            $this->customer->updateOrCreate($this->request->all(), $id);
        }

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