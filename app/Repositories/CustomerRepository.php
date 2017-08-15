<?php

namespace App\Repositories;

use App\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerRepository
{
    protected $customer;
    protected $customer_chunk;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function create($data)
    {
        return $this->customer->create($data);
    }
    
    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->customer
                ->where('salesman_id', Auth::id())
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orwhere('email', 'like', "%$keyword%")
                        ->orwhere('phone', 'like', "%$keyword%")
                        ->orwhere('company', 'like', "%$keyword%");
                })
                ->skip(($page-1) * $num)
                ->take($num)
                ->orderBy('id', 'desc')
                ->get();
        }

        return $this->customer
            ->where('salesman_id', Auth::id())
            ->skip(($page-1) * $num)
            ->take($num)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function AdminGet($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->customer
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orwhere('email', 'like', "%$keyword%")
                        ->orwhere('phone', 'like', "%$keyword%")
                        ->orwhere('company', 'like', "%$keyword%");
                })
                ->skip(($page-1) * $num)
                ->take($num)
                ->orderBy('id', 'desc')
                ->get();
        }

        return $this->customer
            ->skip(($page-1) * $num)
            ->take($num)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function countGet($keyword = null)
    {
        if (!empty($keyword)) {
            return $this->customer
                ->where('salesman_id', Auth::id())
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orwhere('email', 'like', "%$keyword%")
                        ->orwhere('phone', 'like', "%$keyword%")
                        ->orwhere('company', 'like', "%$keyword%");
                })
                ->count();
        }

        return $this->customer
            ->where('salesman_id', Auth::id())
            ->count();
    }

    public function AdminCountGet($keyword = null)
    {

        if (!empty($keyword)) {
            return $this->customer
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orwhere('email', 'like', "%$keyword%")
                        ->orwhere('phone', 'like', "%$keyword%")
                        ->orwhere('company', 'like', "%$keyword%");
                })
                ->count();
        }

        return $this->customer
            ->count();
    }
    
    public function first($id)
    {
        return $this->customer->find($id);
    }

    public function superId()
    {
        return $this->customer
            ->where('name', config('site.admin_name'))
            ->first();
    }

    public function updateOrCreate($post, $id)
    {
        if (empty($id) && $id !== 0) {
            return $this->customer->create($post);
        }

        return $this->customer
            ->where('id', $id)
            ->update($post);
    }

    public function destroy($id)
    {
        return $this->customer
            ->where('id', $id)
            ->delete();
    }

    public function unique($post)
    {
        return $this->customer
            ->where('name', 'like', '%'.$post['name'].'%')
            ->orWhere('phone', 'like', '%'.$post['phone'].'%')
            ->orWhere('company', 'like', '%'.$post['company'].'%')
            ->first();
    }

    public function reverseUnique($post)
    {
        $this->customer->chunk(50, function ($customers) use ($post) {
            foreach ($customers as $customer) {
                if (strpos($post['name'], $customer['name']) !== false) {
                    return $this->customer_chunk = $customer;
                }

                if (strpos($post['phone'], $customer['phone']) !== false) {
                    return $this->customer_chunk = $customer;
                }

                if (strpos($post['company'], $customer['company']) !== false) {
                    return $this->customer_chunk = $customer;
                }
            }
        });

        return $this->customer_chunk;
    }
}