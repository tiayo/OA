<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminSeeder extends Seeder
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Run the database seeds.
     *
     * @return mixed
     */
    public function run()
    {
        $admins = config('site.admin_name');

        foreach ($admins as $admin) {
            empty($user = $this->user->where('name', $admin)->first()) ?
                $this->create($admin) : $this->update($user->id);
        }
    }

    public function create($admin)
    {
        return $this->user->create([
            'name' => $admin,
            'email' => $admin.'@startce.com',
            'parent_id' => 0,
            'password' => bcrypt(env('ADMIN_PASSWORD', '123456')),
            'type' => 1
        ]);
    }

    public function update($id)
    {
        return $this->user
            ->where('id', $id)
            ->update([
                'password' => bcrypt(env('ADMIN_PASSWORD', '123456')),
            ]);
    }
}
