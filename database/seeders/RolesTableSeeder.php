<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appName = env('APP_NAME', 'guce');
        $rootRole = new Role([
            'slug' => 'root',
            'type' => Role::TYPE_CLIENT +
                Role::TYPE_SUPPLIER +
                Role::TYPE_SERVICE +
                Role::TYPE_MANAGER,
            'description' => [
                'en' => [
                    'name' => 'Administrator',
                    'description' => 'Administrator'
                ],
                'fr' => [
                    'name' => 'Administrateur',
                    'description' => 'Administrateur'
                ]
            ],
            'permissions' => [
                'root' => true
            ]
        ]);
        User::whereEmail($appName . "@intm.fr")
            ->first()
            ->roles()
            ->save($rootRole);

        $rol = Role::create([
            'slug' => 'rtc',
            'type' => Role::TYPE_CLIENT,
            'description' => [
                'en' => [
                    'name' => 'Regular Ticket Customer',
                    'description' => 'Regular Ticket Customer'
                ],
                'fr' => [
                    'name' => 'Client Régulier de Ticket',
                    'description' => 'Client Régulier de Ticket'
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);

        User::whereType($rol->type + User::TYPE_MANAGER)
            ->first()
            ->roles()
            ->save($rol);

        $rol = Role::create([
            'slug' => 'rpc',
            'type' => Role::TYPE_CLIENT,
            'description' => [
                'en' => [
                    'name' => 'Regular Project Customer',
                    'description' => 'Regular Project Customer'
                ],
                'fr' => [
                    'name' => 'Client Régulier de Projet',
                    'description' => 'Client Régulier de Projet'
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);

        User::whereType($rol->type + User::TYPE_MANAGER)
            ->first()
            ->roles()
            ->save($rol);

        $rol = Role::create([
            'slug' => 'itm',
            'type' => Role::TYPE_SUPPLIER,
            'description' => [
                'en' => [
                    'name' => 'Incident Team Member',
                    'description' => 'Incident Team Member'
                ],
                'fr' => [
                    'name' => "Membre de l'équipe Incident",
                    'description' => "Membre de l'équipe Incident"
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);

        $rol = Role::create([
            'slug' => 'ptm',
            'type' => Role::TYPE_SUPPLIER,
            'description' => [
                'en' => [
                    'name' => 'Project Team Member',
                    'description' => 'Project Team Member'
                ],
                'fr' => [
                    'name' => "Membre de l'équipe Projet",
                    'description' => "Membre de l'équipe Projet"
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);

        $rol = Role::create([
            'slug' => 'pi',
            'type' => Role::TYPE_SUPPLIER +
                Role::TYPE_MANAGER,
            'description' => [
                'en' => [
                    'name' => 'Pilot Incident',
                    'description' => 'Pilot Incident'
                ],
                'fr' => [
                    'name' => "Pilote Incident",
                    'description' => "Pilote Incident"
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => true, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);

        $rol = Role::create([
            'slug' => 'pp',
            'type' => Role::TYPE_SUPPLIER +
                Role::TYPE_MANAGER,
            'description' => [
                'en' => [
                    'name' => 'Pilot Project',
                    'description' => 'Pilot Project'
                ],
                'fr' => [
                    'name' => 'Pilote Projet',
                    'description' => 'Pilote Projet'
                ]
            ],
            'permissions' => [
                'activities' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'projects' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => true, // delete
                ],
                'roles' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'tickets' => [
                    'index' => false, // read all
                    'store' => false, // create
                    'show' => false, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
                'services' => [
                    'index' => true, // read all
                    'store' => true, // create
                    'show' => true, // read one
                    'update' => true, // update
                    'destroy' => false, // delete
                ],
                'users' => [
                    'index' => true, // read all
                    'store' => false, // create
                    'show' => true, // read one
                    'update' => false, // update
                    'destroy' => false, // delete
                ],
            ]
        ]);
        User::whereType($rol->type)
            ->first()
            ->roles()
            ->save($rol);
    }
}
