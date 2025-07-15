<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

describe('User Model', function () {
    it('hides password attribute in array conversion', function () {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ]);

        $userArray = $user->toArray();

        expect($userArray)->not->toHaveKey('password')
            ->and($user->password)->not->toBeNull();
    });

    it('hides remember_token in array conversion', function () {
        $user = new User([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'remember_token' => 'sample_token',
        ]);

        $userArray = $user->toArray();

        expect($userArray)->not->toHaveKey('remember_token');
    });

    it('casts email_verified_at to datetime', function () {
        $user = User::factory()->verified()->make();

        expect($user->email_verified_at)
            ->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('hashes password on assignment', function () {
        $user = new User();
        $plainPassword = 'secret123';

        $user->password = $plainPassword;

        expect($user->password)
            ->not->toBe($plainPassword)
            ->and(Hash::check($plainPassword, $user->password))
            ->toBeTrue();
    });

    it('has correct fillable attributes', function () {
        $user = new User();

        expect($user->getFillable())
            ->toBe(['name', 'email', 'password', 'email_verified_at']);
    });

    it('uses HasApiTokens trait', function () {
        $traits = class_uses_recursive(User::class);

        expect($traits)->toHaveKey('Laravel\Sanctum\HasApiTokens');
    });
});
