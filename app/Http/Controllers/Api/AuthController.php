<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Fractal\Facades\Fractal;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => Fractal::create($user, new UserTransformer())->toArray(),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return $this->respondWithFormat(
            Fractal::create($user, new UserTransformer()),
            $request
        );
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => Fractal::create($user, new UserTransformer())->toArray(),
            'token' => $token,
        ]);
    }

    private function respondWithFormat($fractalData, Request $request)
    {
        $format = $this->getResponseFormat($request);

        switch ($format) {
            case 'xml':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\XmlSerializer())->toArray();

                return response(\App\Http\Serializers\XmlSerializer::toXml($data))
                    ->header('Content-Type', 'application/xml');

            case 'yaml':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\YamlSerializer())->toArray();

                return response(yaml_emit($data))
                    ->header('Content-Type', 'application/x-yaml');

            case 'csv':
                $data = $fractalData->serializeWith(new \App\Http\Serializers\CsvSerializer())->toArray();

                return response(\App\Http\Serializers\CsvSerializer::toCsv($data))
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="user.csv"');

            default:
                return response()->json(
                    $fractalData->serializeWith(new \App\Http\Serializers\DefaultSerializer())->toArray()
                );
        }
    }

    private function getResponseFormat(Request $request): string
    {
        // Check query parameter first
        if ($request->has('format')) {
            return strtolower($request->query('format'));
        }

        // Check Accept header
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
