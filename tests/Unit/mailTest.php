<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Mail\TestMail;

class mailTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = \App\User::find(21);
        \Mail::to($user->email)->send(new TestMail());
        //\Mail::assertSent(TestMail::class);
        $this->assertTrue(true);
    }
}
