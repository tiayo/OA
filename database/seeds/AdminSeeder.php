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
        $admin = $this->user
            ->where('name', 'admin')
            ->first();

        if (empty($admin)) {
            return $this->create();
        }

        return $this->update($admin->id);
    }

    public function create()
    {
        return $this->user->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'type' => 1
        ]);
    }

    public function update($id)
    {
        return $this->user
            ->where('id', $id)
            ->update([
                'password' => bcrypt('123456'),
            ]);
    }
}
