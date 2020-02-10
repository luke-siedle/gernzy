<?php

namespace Gernzy\Server\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Gernzy\Server\Exceptions\GernzyException;
use Gernzy\Server\Models\PasswordResets;
use Gernzy\Server\Models\User;

class ResetPassword
{
    public static function handle($args): User
    {

        // Check if the passwords match
        if ($args['password'] !== $args['password_confirmation']) {
            throw new GernzyException(
                'The provided passwords.',
                'Please resubmit passwords and make sure they match.'
            );
        }

        // Check if the passwords have min length
        if (strlen($args['password']) < 8) {
            throw new GernzyException(
                'The provided password is too short.',
                'Please resubmit password and make sure it is a minimum of 8 characters.'
            );
        }

        // Check if the reset record exists in the db
        $resetRecord =  PasswordResets::where('email', $args['email'])->first();
        if ($resetRecord === null) {
            throw new GernzyException(
                'The provided email does not exist.',
                'Please resubmit a password reset request.'
            );
        }

        $createdAtTime = Carbon::parse($resetRecord->created_at);
        $timeDiff = $createdAtTime->diffInHours(Carbon::now());

        // Check if reset request was created less than 24 hours ago
        if ($timeDiff > 24) {
            $resetRecord->delete();
            throw new GernzyException(
                'Token expired',
                'The token has expired, please resubmit a password reset request.'
            );
        }

        // Check the record and compare the token from the args to the one in the table and return email, then delete the record
        if (!Hash::check($args['token'], $resetRecord->token)) {
            throw new GernzyException(
                'Token mismatch',
                'The token does not match our records, please resubmit a password reset request.'
            );
        }

        // Update the User's record with the new ID
        $user = User::where('email', $resetRecord->email)->first();
        if ($user === null) {
            $resetRecord->delete();
            throw new GernzyException(
                'The provided email does not exist.',
                'Please resubmit a password reset request.'
            );
        }

        $user->password = Hash::make($args['password']);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Delete the reset record after the user password has been updated
        $resetRecord->delete();

        return $user;
    }
}
