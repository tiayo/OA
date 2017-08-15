<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
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
        factory(User::class, 10)->make();
    }
}
