<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthenticationTest extends DuskTestCase
{
    /**
     * @test
     */
    public function ItAssertThatUserCanLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertSee('Entrar')
                    ->type('email', 'juanfarias580@gmail.com')
                    ->type('password', 'password')
                    ->press('Entrar')
                    ->assertPathIs('/')
                    ->assertAuthenticated();
        });
    }
}
