<?php

namespace App\Services;

use App\Models\PageVisit;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function track(array $data): PageVisit
    {
        return PageVisit::create([
            'visitor_id' => $data['visitor_id'] ?? null,
            'page_type' => $data['page_type'],
            'page_id' => $data['page_id'] ?? null,
            'page_slug' => $data['page_slug'] ?? null,
            'page_title' => $data['page_title'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'visited_at' => $data['visited_at'] ?? now(),
        ]);
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
