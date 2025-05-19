<?php

namespace Adldap\Laravel\Tests\Listeners;

use Adldap\Laravel\Events\Authenticating;
use Adldap\Laravel\Listeners\LogAuthentication;
use Adldap\Laravel\Tests\TestCase;
use Adldap\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery as m;

class LogAuthenticationTest extends TestCase
{
    /** @test */
    public function logged()
    {

        $user = m::mock(User::class);

        $name = 'John Doe';

        $user->shouldReceive('getCommonName')->andReturn($name);

        $username = 'jdoe';
        $prefix = 'prefix.';
        $suffix = '.suffix';

        Config::set('ldap_auth.connection', 'default');
        Config::set('ldap.connections.default.settings.account_prefix', $prefix);
        Config::set('ldap.connections.default.settings.account_suffix', $suffix);

        $authUsername = $prefix.$username.$suffix;

        Log::shouldReceive('info')->once()->with("User '{$name}' is authenticating with username: '{$authUsername}'");

        $e = new Authenticating($user, $username);

        $l = new LogAuthentication();
        $l->handle($e);
    }
}