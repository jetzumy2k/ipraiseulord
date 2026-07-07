<?php

namespace App\Services;

use App\Models\PageVisit;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AnalyticsService
{
    public function track(array $data): PageVisit
    {
        return PageVisit::create([
            'visitor_id' => $this->clip($data['visitor_id'] ?? null, 255),
            'page_type' => $this->clip($data['page_type'], 255),
            'page_id' => $data['page_id'] ?? null,
            'page_slug' => $this->clip($data['page_slug'] ?? null, 255),
            'page_title' => $this->clip($data['page_title'] ?? null, 255),
            'ip_address' => $this->clip($data['ip_address'] ?? null, 45),
            'user_agent' => $this->clip($data['user_agent'] ?? null, 1024),
            'visited_at' => $data['visited_at'] ?? now(),
        ]);
    }

    private function clip(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::substr($value, 0, $max);
    }

    public function recentVisits(int $limit = 10): Collection
    {
        return PageVisit::query()
            ->orderByDesc('visited_at')
            ->limit($limit)
            ->get();
    }

    public function mostVisited(int $limit = 10): Collection
    {
        return PageVisit::query()
            ->select('page_type', 'page_id', 'page_slug', 'page_title')
            ->selectRaw('COUNT(*) as visit_count')
            ->groupBy('page_type', 'page_id', 'page_slug', 'page_title')
            ->orderByDesc('visit_count')
            ->limit($limit)
            ->get();
    }

    public function totalVisitors(): int
    {
        return (int) PageVisit::query()
            ->whereNotNull('visitor_id')
            ->distinct('visitor_id')
            ->count('visitor_id');
    }

    public function totalVisits(): int
    {
        return PageVisit::count();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboardStats(): array
    {
        return [
            'total_visits' => $this->totalVisits(),
            'total_visitors' => $this->totalVisitors(),
            'recent_visits' => $this->recentVisits(),
            'most_visited' => $this->mostVisited(),
        ];
    }
}
