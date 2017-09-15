<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $message;
    protected $request;

    public function __construct(MessageService $message, Request $request)
    {

        $this->message = $message;
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

        $messages = $this->message->get($num, $keyword);

        return view('admin.message.list', [
            'messages' => $messages,
        ]);
    }

    /**
     * 更新
     *
     * @param $id
     * @return int
     */
    public function update($id)
    {
        if ($this->message->update($id)) {
            return redirect()->route('message_list');
        }

        return response('更新状态失败！', 403);
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        if ($this->message->destroy($id)) {
            return redirect()->route('message_list');
        }

        return response('删除失败，请确认分组存在或清空组下所有账户！');
    }
}