<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Lab19\Cart\Models\PasswordResets;
use Lab19\Cart\Models\User;
use Lab19\Cart\Notifications\GernzyResetPassword;
use Lab19\Cart\Testing\TestCase;
use Notification;

class PasswordResetFeatureTest extends TestCase
{
    use WithFaker;
    const USER_ORIGINAL_PASSWORD = 'secret';

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetEmailAndTokenNotificationLaravel()
    {
        // Create user
        $user = factory(User::class)->create();

        // $user = User::where('email', request()->input('email'))->first();
        $token = Password::broker()->createToken($user);
        $tokenHash = Hash::make($token);
        PasswordResets::create([
            'email' => $user->email,
            'token' => $tokenHash,
            'created_at' => Carbon::now(),
        ]);


        $this->assertDatabaseHas('cart_password_resets', [
            'email' => $user->email,
            'token' => $tokenHash,
        ]);


        $user->notify(new GernzyResetPassword($token));

        Notification::assertSentTo(
            $user,
            GernzyResetPassword::class,
            function ($notification, $channels) use ($token, $user) {
                // retrive the mail content
                $mailData = $notification->toMail($user)->toArray();
                $this->assertEquals(route('password.reset.token', ['token' => $notification->getToken()]), $mailData['actionUrl']);
                return $token == $notification->getToken();
            }
        );
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetEmailLinkNotificationGraphQl()
    {
        // Create user
        $user = factory(User::class)->create();
        $token = Password::broker()->createToken($user);

        // This is to create a record in the resets table and thus should be deleted by the resetUserPasswordLink funtion
        // it caters for multiple reset requests
        $testResetRecord = PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetUserPasswordLink(email: "' . $user->email . '") {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'resetUserPasswordLink' => [
                    'success'
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['resetUserPasswordLink']['success'], true);

        $this->assertNotEquals(PasswordResets::where('email', '=', $user->email)->first()->token, $testResetRecord->token);

        $this->assertDatabaseHas('cart_password_resets', [
            'email' => $user->email
        ]);

        Notification::assertSentTo(
            $user,
            GernzyResetPassword::class
        );

        $this->assertDatabaseMissing('cart_password_resets', [
            'token' => $testResetRecord->token
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetEmailLinkNotificationGraphQlNonExistentEmail()
    {
        //The endpoint is made not to return a reponse whether the submitted email actaully exists in the DB


        // Create user
        $user = factory(User::class)->create();
        $token = Password::broker()->createToken($user);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetUserPasswordLink(email: "' . str_random() . '") {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertSee('errors');

        $this->assertDatabaseMissing('cart_password_resets', [
            'email' => $user->email
        ]);

        Notification::assertNotSentTo(
            $user,
            GernzyResetPassword::class
        );
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordGraphQl()
    {
        // Create user
        $user = factory(User::class)->create();
        $token = Password::broker()->createToken($user);
        $password = str_random();

        PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->addHours(random_int(0, 24)),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetPassword(input:{ 
                email: "' . $user->email . '",
                token: "' . $token . '",
                password: "' . $password . '"
                password_confirmation: "' . $password . '"
                }) {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertDontSee('errors');

        $response->assertJsonStructure([
            'data' => [
                'resetPassword' => [
                    'success'
                ]
            ]
        ]);

        $result = $response->decodeResponseJson();

        $this->assertEquals($result['data']['resetPassword']['success'], true);

        $user->refresh();

        $this->assertFalse(Hash::check(
            self::USER_ORIGINAL_PASSWORD,
            $user->password
        ));

        $this->assertTrue(Hash::check($password, $user->password));

        $this->assertDatabaseMissing('cart_password_resets', [
            'email' => $user->email
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordGraphQlWrongEmail()
    {
        // Create user
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);
        $password = str_random();

        PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->addHours(random_int(0, 24)),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetPassword(input:{ 
                email: "' . str_random() . '",
                token: "' . $token . '",
                password: "' . $password . '"
                password_confirmation: "' . $password . '"
                }) {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertSee('errors');

        $this->assertEquals($result['errors'][0]['message'], 'The provided email does not exist.');

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD, $user->password));

        $this->assertDatabaseHas('cart_password_resets', [
            'email' => $user->email
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordGraphQlTokenExpired()
    {
        // Create user
        $user = factory(User::class)->create();
        $token = Password::broker()->createToken($user);
        $password = str_random();

        PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->addHours(random_int(25, 1000)),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetPassword(input:{ 
                email: "' . $user->email . '",
                token: "' . $token . '",
                password: "' . $password . '"
                password_confirmation: "' . $password . '"
                }) {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertSee('errors');

        $this->assertEquals($result['errors'][0]['message'], 'Token expired');

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertDatabaseMissing('cart_password_resets', [
            'email' => $user->email
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordGraphQlPasswordMismatch()
    {
        // Create user
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random();
        $password_confirmation = str_random();

        PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->addHours(random_int(0, 24)),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetPassword(input:{ 
                email: "' . $user->email . '",
                token: "' . $token . '",
                password: "' . $password . '"
                password_confirmation: "' . $password_confirmation . '"
                }) {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertSee('errors');

        $this->assertEquals($result['errors'][0]['message'], 'The provided passwords.');

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertDatabaseHas('cart_password_resets', [
            'email' => $user->email
        ]);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordGraphQlPasswordInvalidLength()
    {
        // Create user
        $user = factory(User::class)->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD),
        ]);

        $token = Password::broker()->createToken($user);

        $password = str_random(random_int(0, 7));
        $password_confirmation = $password;

        PasswordResets::create([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->addHours(random_int(0, 24)),
        ]);

        /** @var \Illuminate\Foundation\Testing\TestResponse $response */
        $response = $this->graphQLWithSession('
        mutation {
            resetPassword(input:{ 
                email: "' . $user->email . '",
                token: "' . $token . '",
                password: "' . $password . '"
                password_confirmation: "' . $password_confirmation . '"
                }) {
                success
            }
        }
        ');

        $result = $response->decodeResponseJson();

        $response->assertSee('errors');

        $this->assertEquals($result['errors'][0]['message'], 'The provided password is too short.');

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertDatabaseHas('cart_password_resets', [
            'email' => $user->email
        ]);
    }
}
