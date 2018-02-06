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
        $users = \App\User::all();
        \Mail::to($users->first())->send(new TestMail(['user' => $users->first()]));
        //\Mail::assertSent(TestMail::class);
        $this->assertTrue(true);
    }
}
