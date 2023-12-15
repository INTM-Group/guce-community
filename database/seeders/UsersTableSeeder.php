<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->guceAdmin();
        $this->guceMemberManager();
        $this->guceMember();
        $this->guceClientManager();
        $this->guceClient();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function guceAdmin()
    {
        $appName = env('APP_NAME', 'guce');
        $service = Service::first();
        $user = new User([
            'email' => $appName . "@intm.fr",
            'type' => User::TYPE_CLIENT +
                User::TYPE_SUPPLIER +
                User::TYPE_SERVICE +
                User::TYPE_MANAGER,
            'first_name' => $appName,
            'last_name' => "INTM",
            'department' => "INTM-test",
            'last_login' => Carbon::now(),
        ]);
        $user->password = $appName;
        $user->service()->associate($service);
        $user->save();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function guceMemberManager()
    {
        $appName = env('APP_NAME', 'guce');
        $service = Service::first();
        $user = new User([
            'email' => "member.manager@intm.fr",
            'type' => User::TYPE_SUPPLIER+
                User::TYPE_MANAGER,
            'first_name' => "Member manager",
            'last_name' => "INTM",
            'department' => "INTM-test",
            'last_login' => Carbon::now(),
        ]);
        $user->password = $appName;
        $user->service()->associate($service);
        $user->save();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function guceMember()
    {
        $appName = env('APP_NAME', 'guce');
        $service = Service::first();
        $user = new User([
            'email' => "member@intm.fr",
            'type' => User::TYPE_SUPPLIER,
            'first_name' => "member",
            'last_name' => "INTM",
            'department' => "INTM-test",
            'last_login' => Carbon::now(),
        ]);
        $user->password = $appName;
        $user->service()->associate($service);
        $user->save();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function guceClientManager()
    {
        $appName = env('APP_NAME', 'guce');
        $service = Service::first();
        $user = new User([
            'email' => "client.manager@intm.fr",
            'type' => User::TYPE_CLIENT +
                User::TYPE_MANAGER,
            'first_name' => "Client manager",
            'last_name' => "INTM",
            'department' => "INTM-test",
            'last_login' => Carbon::now(),
        ]);
        $user->password = $appName;
        $user->service()->associate($service);
        $user->save();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function guceClient()
    {
        $appName = env('APP_NAME', 'guce');
        $service = Service::first();
        $user = new User([
            'email' => "client@intm.fr",
            'type' => User::TYPE_CLIENT,
            'first_name' => "Client",
            'last_name' => "INTM",
            'department' => "INTM-test",
            'last_login' => Carbon::now(),
        ]);
        $user->password = $appName;
        $user->service()->associate($service);
        $user->save();
    }
}
