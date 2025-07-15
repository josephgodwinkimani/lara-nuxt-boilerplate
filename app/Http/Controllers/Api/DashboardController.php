<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'users_created_today' => User::whereDate('created_at', today())->count(),
            'users_created_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'users_created_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        return $this->respondWithFormat($stats, $request);
    }

    public function userGrowth(Request $request)
    {
        $period = $request->query('period', '30'); // days

        $userGrowth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays($period))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => (int) $item->count,
                    'cumulative' => 0, // Will be calculated
                ];
            });

        // Calculate cumulative count
        $cumulative = 0;
        $userGrowth = $userGrowth->map(function ($item) use (&$cumulative) {
            $cumulative += $item['count'];
            $item['cumulative'] = $cumulative;

            return $item;
        });

        $data = [
            'period_days' => (int) $period,
            'growth_data' => $userGrowth->values()->toArray(),
            'total_new_users' => $userGrowth->sum('count'),
        ];

        return $this->respondWithFormat($data, $request);
    }

    public function usersByStatus(Request $request)
    {
        $data = [
            [
                'status' => 'Verified',
                'count' => User::whereNotNull('email_verified_at')->count(),
                'percentage' => 0,
            ],
            [
                'status' => 'Unverified',
                'count' => User::whereNull('email_verified_at')->count(),
                'percentage' => 0,
            ],
        ];

        $total = array_sum(array_column($data, 'count'));

        if ($total > 0) {
            foreach ($data as &$item) {
                $item['percentage'] = round(($item['count'] / $total) * 100, 2);
            }
        }

        $response = [
            'total_users' => $total,
            'status_breakdown' => $data,
        ];

        return $this->respondWithFormat($response, $request);
    }

    public function recentUsers(Request $request)
    {
        $limit = $request->query('limit', 10);

        $recentUsers = User::select('id', 'name', 'email', 'email_verified_at', 'created_at')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_verified' => !is_null($user->email_verified_at),
                    'created_at' => $user->created_at->toISOString(),
                    'created_at_human' => $user->created_at->diffForHumans(),
                ];
            });

        $data = [
            'limit' => (int) $limit,
            'users' => $recentUsers->toArray(),
        ];

        return $this->respondWithFormat($data, $request);
    }

    public function activityMetrics(Request $request)
    {
        $days = $request->query('days', 7);

        // Generate sample activity data (in real app, this would come from activity logs)
        $metrics = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $metrics[] = [
                'date' => $date->format('Y-m-d'),
                'logins' => rand(10, 100),
                'page_views' => rand(100, 1000),
                'api_requests' => rand(50, 500),
                'active_users' => rand(5, 50),
            ];
        }

        $data = [
            'period_days' => (int) $days,
            'metrics' => $metrics,
            'totals' => [
                'total_logins' => array_sum(array_column($metrics, 'logins')),
                'total_page_views' => array_sum(array_column($metrics, 'page_views')),
                'total_api_requests' => array_sum(array_column($metrics, 'api_requests')),
                'avg_daily_active_users' => round(array_sum(array_column($metrics, 'active_users')) / $days, 2),
            ],
        ];

        return $this->respondWithFormat($data, $request);
    }

    private function respondWithFormat($data, Request $request, int $statusCode = 200)
    {
        $format = $this->getResponseFormat($request);

        switch ($format) {
            case 'xml':
                return response(\App\Http\Serializers\XmlSerializer::toXml($data), $statusCode)
                    ->header('Content-Type', 'application/xml');

            case 'yaml':
                return response(yaml_emit($data), $statusCode)
                    ->header('Content-Type', 'application/x-yaml');

            case 'csv':
                return response(\App\Http\Serializers\CsvSerializer::toCsv($this->flattenForCsv($data)), $statusCode)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="dashboard-data.csv"');

            default:
                return response()->json($data, $statusCode);
        }
    }

    private function flattenForCsv($data): array
    {
        // Flatten complex data structures for CSV export
        if (isset($data['growth_data'])) {
            return $data['growth_data'];
        } elseif (isset($data['status_breakdown'])) {
            return $data['status_breakdown'];
        } elseif (isset($data['users'])) {
            return $data['users'];
        } elseif (isset($data['metrics'])) {
            return $data['metrics'];
        } else {
            // Convert single-level array to row format
            return [array_keys($data), array_values($data)];
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
