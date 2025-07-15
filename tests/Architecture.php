<?php

// Code structure and standards enforcement
// Probaly needs adjustment

// https://pestphp.com/docs/arch-testing

declare(strict_types=1);


describe('Architecture Rules', function () {
    test('controllers have Controller suffix')
        ->expect('App\Http\Controllers')
        ->toHaveSuffix('Controller'); // The toHaveSuffix() method may be used to ensure that all files within a given namespace have a specific suffix.

    test('no debugging functions left in codebase')
        ->expect(['dd', 'dump', 'ray'])
        ->not->toBeUsed(); // The not modifier, when combined with the toBeUsed() method, enables you to verify that certain classes or functions are not being utilized by your application.

    test('API controllers extend base controller')
        ->expect('App\Http\Controllers\Api')
        ->toExtend('App\Http\Controllers\Controller'); // The toExtend() method may be used to ensure that all classes within a given namespace extend a specific class.

    test('models are in correct namespace')
        ->expect('App\Models')
        ->toBeClasses()
        ->toExtend('Illuminate\Database\Eloquent\Model');

    test('transformers have Transformer suffix')
        ->expect('App\Transformers')
        ->toHaveSuffix('Transformer');

    test('transformers extend abstract transformer')
        ->expect('App\Transformers')
        ->toExtend('League\Fractal\TransformerAbstract');

    test('middleware are in correct namespace')
        ->expect('App\Http\Middleware')
        ->toBeClasses();

    test('controllers do not use facades directly')
        ->expect('App\Http\Controllers')
        ->not->toUse([
            'Illuminate\Support\Facades\DB',
            'Illuminate\Support\Facades\Cache',
            'Illuminate\Support\Facades\Log',
        ]);

    test('models use proper traits')
        ->expect('App\Models\User')
        ->toUse([
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            'Illuminate\Notifications\Notifiable',
            'Laravel\Sanctum\HasApiTokens',
        ]);

    test('controllers use dependency injection')
        ->expect('App\Http\Controllers')
        ->not->toUse('new');

    test('serializers extend correct base class')
        ->expect('App\Http\Serializers')
        ->toExtend('League\Fractal\Serializer\SerializerAbstract')
        ->ignoring('App\Http\Serializers\DefaultSerializer');

    test('test files are properly named')
        ->expect('Tests')
        ->toHaveSuffix('Test')
        ->ignoring([
            'Tests\TestCase',
            'Tests\CreatesApplication',
        ]);
});
