<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Activation;
use Carbon\Carbon;
use App\User;
class ActivateDateTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = User::find(50);
        $activations = Activation::where('user_id', $user->id)
        ->where('updated_at', '>', Carbon::now()->addMinutes(-60));

        $t = Carbon::now()->addMinutes(-10);

        dd($activations->first());
        
        $this->assertTrue(true);
    }
}
