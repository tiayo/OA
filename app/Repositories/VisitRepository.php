<?php

namespace App\Repositories;

use App\Visit;
use Illuminate\Support\Facades\Auth;

class VisitRepository
{
    protected $visit;

    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    public function create($data)
    {
        return $this->visit->create($data);
    }

    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->visit
                ->where('visits.salesman_id', Auth::id())
                ->where('visits.customer_id', $keyword)
                ->join('users', 'visits.salesman_id', 'users.id')
                ->join('customers', 'visits.customer_id', 'customers.id')
                ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
                ->skip(($page-1) * $num)
                ->take($num)
                ->orderBy('id', 'desc')
                ->get();
        }

        return $this->visit
            ->where('visits.salesman_id', Auth::id())
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->skip(($page-1) * $num)
            ->take($num)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function AdminGet($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->visit
                ->where('visits.customer_id', $keyword)
                ->join('users', 'visits.salesman_id', 'users.id')
                ->join('customers', 'visits.customer_id', 'customers.id')
                ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
                ->skip(($page-1) * $num)
                ->take($num)
                ->orderBy('id', 'desc')
                ->get();
        }

        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->skip(($page-1) * $num)
            ->take($num)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function countGet($keyword = null)
    {
        if (!empty($keyword)) {
            return $this->visit
                ->where('customer_id', $keyword)
                ->count();
        }

        return $this->visit
            ->where('salesman_id', Auth::id())
            ->count();
    }

    public function AdminCountGet($keyword = null)
    {

        if (!empty($keyword)) {
            return $this->visit
                ->where('customer_id', $keyword)
                ->count();
        }

        return $this->visit
            ->count();
    }

    public function first($id)
    {
        return $this->visit
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'customers.name as customer_name')
            ->find($id);
    }

    public function superId()
    {
        return $this->visit
            ->where('name', config('site.admin_name'))
            ->first();
    }

    public function updateOrCreate($post, $id)
    {
        if (empty($id) && $id !== 0) {
            return $this->visit->create($post);
        }

        return $this->visit
            ->where('id', $id)
            ->update($post);
    }

    public function destroy($id)
    {
        return $this->visit
            ->where('id', $id)
            ->delete();
    }

    public function unique($post)
    {
        return $this->visit
            ->where('name', 'like', '%'.$post['name'].'%')
            ->orWhere('phone', 'like', '%'.$post['phone'].'%')
            ->orWhere('company', 'like', '%'.$post['company'].'%')
            ->first();
    }

    public function reverseUnique($post)
    {
        $this->visit->chunk(50, function ($customers) use ($post) {
            foreach ($customers as $customer) {
                if (strpos($post['name'], $customer['name']) !== false) {
                    return $this->visit_chunk = $customer;
                }

                if (strpos($post['phone'], $customer['phone']) !== false) {
                    return $this->visit_chunk = $customer;
                }

                if (strpos($post['company'], $customer['company']) !== false) {
                    return $this->visit_chunk = $customer;
                }
            }
        });

        return $this->visit_chunk;
    }

    public function find($id)
    {
        return $this->visit->find($id);
    }
}