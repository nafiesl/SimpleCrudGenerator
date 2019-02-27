<?php

namespace Tests;

use App\User;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class BrowserKitTest extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUrl = 'http://localhost';

    protected function loginAsUser()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        return $user;
    }

    protected function createUser()
    {
        return factory(User::class)->create();
    }
}
