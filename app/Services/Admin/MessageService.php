<?php

namespace App\Services\Admin;

use App\Repositories\MessageRepository;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    protected $message;
    protected $salesman;
    protected $user;

    public function __construct(MessageRepository $message, SalesmanService $salesman, UsersRepository $user)
    {
        $this->message = $message;
        $this->salesman = $salesman;
        $this->user = $user;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->message->search($num, $keyword);
        }

        return $this->message->get($num);
    }

    public function getRemind($num)
    {
        return $this->message->getRemind($num);
    }

    /**
     * 更新或编辑
     *
     * @param $post
     * @param null $id
     * @return mixed
     */
    public function create($post)
    {
        $data['type'] = $post->type;
        $data['option'] = $post->option;
        $data['salesman_id'] = Auth::id();
        $data['content'] = serialize($post->data);
        $data['origin'] = serialize($post->origin);

        return $this->message->create($data);
    }

    public function update($id)
    {
        $status = $this->message->find($id, 'status')['status'];

        $status = $status == 1 ? 0 : 1;

        return $this->message->update($id, [
            'status' => $status,
        ]);
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        //如果分组下有用户，则不可以删除
        if ($this->salesman->countGroup($id) > 0) {
            return false;
        }

        return $this->message->destroy($id);
    }

    /**
     * 返回所有业务员
     * 限制上限10000条，可以修改
     *
     * @return mixed
     */
    public function getAllSalesman()
    {
        return $this->salesman->get(10000);
    }
}