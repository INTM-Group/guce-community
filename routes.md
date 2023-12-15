| Verb   | Path                                       | NamedRoute         | Controller                          | Action     | Middleware |
|--------|--------------------------------------------|--------------------|-------------------------------------|------------|------------|
| GET    | /                                          |                    | None                                | Closure    |            |
| GET    | /api                                       |                    | App\Http\Controllers\Controller     | info       |            |
| GET    | /api/v0                                    |                    | App\Http\Controllers\Controller     | version    |            |
| GET    | /api/v0/error                              |                    | App\Http\Controllers\Controller     | error      |            |
| POST   | /api/v0/is/auth                            |                    | App\Http\Controllers\Controller     | authTest   | auth       |
| POST   | /api/v0/auth/activation                    |                    | App\Http\Controllers\AuthController | activation |            |
| POST   | /api/v0/auth                               |                    | App\Http\Controllers\AuthController | validation |            |
| DELETE | /api/v0/auth                               |                    | App\Http\Controllers\AuthController | logout     | auth       |
| GET    | /api/v0/roles                              | roles.index        | App\Http\Controllers\Roles          | all        | auth       |
| POST   | /api/v0/roles                              | roles.store        | App\Http\Controllers\Roles          | add        | auth       |
| GET    | /api/v0/roles/{id}                         | roles.show         | App\Http\Controllers\Roles          | get        | auth       |
| PUT    | /api/v0/roles/{id}                         | roles.update       | App\Http\Controllers\Roles          | put        | auth       |
| PATCH  | /api/v0/roles/{id}                         | roles.update       | App\Http\Controllers\Roles          | put        | auth       |
| DELETE | /api/v0/roles/{id}                         | roles.destroy      | App\Http\Controllers\Roles          | remove     | auth       |
| GET    | /api/v0/services                           | services.index     | App\Http\Controllers\Services       | all        | auth       |
| POST   | /api/v0/services                           | services.store     | App\Http\Controllers\Services       | add        | auth       |
| GET    | /api/v0/services/{id}                      | services.show      | App\Http\Controllers\Services       | get        | auth       |
| PUT    | /api/v0/services/{id}                      | services.update    | App\Http\Controllers\Services       | put        | auth       |
| PATCH  | /api/v0/services/{id}                      | services.update    | App\Http\Controllers\Services       | put        | auth       |
| DELETE | /api/v0/services/{id}                      | services.destroy   | App\Http\Controllers\Services       | remove     | auth       |
| GET    | /api/v0/users                              | users.index        | App\Http\Controllers\Users          | all        | auth       |
| POST   | /api/v0/users                              | users.store        | App\Http\Controllers\Users          | add        | auth       |
| GET    | /api/v0/users/{id}                         | users.show         | App\Http\Controllers\Users          | get        | auth       |
| PUT    | /api/v0/users/{id}                         | users.update       | App\Http\Controllers\Users          | put        | auth       |
| PATCH  | /api/v0/users/{id}                         | users.update       | App\Http\Controllers\Users          | put        | auth       |
| DELETE | /api/v0/users/{id}                         | users.destroy      | App\Http\Controllers\Users          | remove     | auth       |
| GET    | /api/v0/tickets                            | tickets.index      | App\Http\Controllers\Tickets        | all        | auth       |
| POST   | /api/v0/tickets                            | tickets.store      | App\Http\Controllers\Tickets        | add        | auth       |
| GET    | /api/v0/tickets/{id}                       | tickets.show       | App\Http\Controllers\Tickets        | get        | auth       |
| PUT    | /api/v0/tickets/{id}                       | tickets.update     | App\Http\Controllers\Tickets        | put        | auth       |
| PATCH  | /api/v0/tickets/{id}                       | tickets.update     | App\Http\Controllers\Tickets        | put        | auth       |
| DELETE | /api/v0/tickets/{id}                       | tickets.destroy    | App\Http\Controllers\Tickets        | remove     | auth       |
| GET    | /api/v0/projects                           | projects.index     | App\Http\Controllers\Projects       | all        | auth       |
| POST   | /api/v0/projects                           | projects.store     | App\Http\Controllers\Projects       | add        | auth       |
| GET    | /api/v0/projects/{id}                      | projects.show      | App\Http\Controllers\Projects       | get        | auth       |
| PUT    | /api/v0/projects/{id}                      | projects.update    | App\Http\Controllers\Projects       | put        | auth       |
| PATCH  | /api/v0/projects/{id}                      | projects.update    | App\Http\Controllers\Projects       | put        | auth       |
| DELETE | /api/v0/projects/{id}                      | projects.destroy   | App\Http\Controllers\Projects       | remove     | auth       |
| GET    | /api/v0/{targetType}/{targetId}/activities | activities.index   | App\Http\Controllers\Activities     | allOf      | auth       |
| POST   | /api/v0/{targetType}/{targetId}/activities | activities.store   | App\Http\Controllers\Activities     | addTo      | auth       |
| GET    | /api/v0/activities/{id}                    | activities.show    | App\Http\Controllers\Activities     | get        | auth       |
| PUT    | /api/v0/activities/{id}                    | activities.update  | App\Http\Controllers\Activities     | put        | auth       |
| PATCH  | /api/v0/activities/{id}                    | activities.update  | App\Http\Controllers\Activities     | put        | auth       |
| DELETE | /api/v0/activities/{id}                    | activities.destroy | App\Http\Controllers\Activities     | remove     | auth       |
