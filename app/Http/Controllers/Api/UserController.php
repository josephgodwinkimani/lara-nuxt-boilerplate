<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Fractal\Facades\Fractal;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $users = User::paginate($perPage);

        return $this->respondWithFormat(
            Fractal::create($users, new UserTransformer()),
            $request
        );
    }

    public function show(Request $request, User $user)
    {
        return $this->respondWithFormat(
            Fractal::create($user, new UserTransformer()),
            $request
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return $this->respondWithFormat(
            Fractal::create($user, new UserTransformer()),
            $request,
            201
        );
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return $this->respondWithFormat(
            Fractal::create($user->fresh(), new UserTransformer()),
            $request
        );
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 204);
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
        ]);
    }

    public function generatePassword(User $user)
    {
        $password = Str::random(12);

        $user->update([
            'password' => Hash::make($password),
        ]);

        return response()->json([
            'message' => 'Password generated successfully',
            'password' => $password,
        ]);
    }

    private function respondWithFormat($fractalData, Request $request, int $statusCode = 200)
    {
        $format = $this->getResponseFormat($request);

        switch ($format) {
            case 'xml':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\XmlSerializer())->toArray();

                return response(\App\Http\Serializers\XmlSerializer::toXml($data), $statusCode)
                    ->header('Content-Type', 'application/xml');

            case 'yaml':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\YamlSerializer())->toArray();

                return response(yaml_emit($data), $statusCode)
                    ->header('Content-Type', 'application/x-yaml');

            case 'csv':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\CsvSerializer())->toArray();

                return response(\App\Http\Serializers\CsvSerializer::toCsv($data), $statusCode)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="users.csv"');

            default:
                return response()->json(
                    $fractalData->serializeWith(new \App\Http\Serializers\DefaultSerializer())->toArray(),
                    $statusCode
                );
        }
    }

    private function getResponseFormat(Request $request): string
    {
        if ($request->has('format')) {
            return strtolower($request->query('format'));
        }

        $acceptHeader = $request->header('Accept', 'application/json');

        if (str_contains($acceptHeader, 'application/xml')) {
            return 'xml';
        }

        if (str_contains($acceptHeader, 'application/x-yaml') || str_contains($acceptHeader, 'text/yaml')) {
            return 'yaml';
        }

        if (str_contains($acceptHeader, 'text/csv')) {
            return 'csv';
        }

        return 'json';
    }
}
