<?php

namespace App\Repositories;

use App\Message;

class MessageRepository
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function create($data)
    {
        return $this->message->create($data);
    }

    public function update($id, $data)
    {
        return $this->message
            ->where('id', $id)
            ->update($data);
    }

    public function destroy($id)
    {
        return $this->message
            ->where('id', $id)
            ->delete();
    }

    public function find($id, ...$select)
    {
        return $this->message
            ->select($select)
            ->find($id);
    }

    public function search($num, $keyword)
    {
        return $this->message
            ->where('messages.name', 'like', "%$keyword%")
            ->join('users', 'messages.salesman_id', 'users.id')
            ->select('messages.*', 'users.name as salesman_name')
            ->paginate($num);
    }

    public function get($num)
    {
        return $this->message
            ->join('users', 'messages.salesman_id', 'users.id')
            ->select('messages.*', 'users.name as salesman_name')
            ->orderBy('updated_at', 'desc')
            ->paginate($num);
    }

    public function selectFirst($where, ...$select)
    {
        return $this->message
            ->select($select)
            ->where($where)
            ->first();
    }

    public function getRemind($num)
    {
        return $this->message
            ->join('users', 'messages.salesman_id', 'users.id')
            ->select('messages.*', 'users.name as salesman_name')
            ->where('messages.status', 0)
            ->where('messages.type', 'customer')
            ->orderBy('updated_at', 'desc')
            ->paginate($num);
    }
}