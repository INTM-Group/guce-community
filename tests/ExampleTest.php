<?php

use App\Models\Project;
use App\Models\User;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(),
            $this->response->getContent()
        );
    }

    public function testProject()
    {
        $user = User::find(1);
        $project = new Project([
            'title' => 'example',
        ]);
        $project->service()->associate($user);
        $project->creator()->associate($user);
        $project->addParticipants($user);
        $project->save();
    }
}
