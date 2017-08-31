<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesmanControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $id = $request->route('id');

        //没有请求id的通过中间件
        if (empty($id)) {
            return $next($request);
        }

        //有id的进行鉴权
        if (!Auth::user()->can('control', User::find($id))) {
            return response('没有权限操作该业务员！');
        }

        return $next($request);
    }
}
