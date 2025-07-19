<?php

namespace App\Repositories;

use App\Models\Memory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MemoryRepository
{
    protected Memory $model;
    
    public function __construct(Memory $model)
    {
        $this->model = $model;
    }

    /**
     * Get all memories for a user with optional filters
     */
    public function getUserMemories(
        int $userId, 
        array $filters = [], 
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->where('user_id', $userId);
        
        // Apply filters
        $this->applyFilters($query, $filters);
        
        return $query->orderBy('memory_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Get shared memories (public memories from all users)
     */
    public function getSharedMemories(
        array $filters = [], 
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = $this->model->where('is_private', false);
        
        $this->applyFilters($query, $filters);
        
        return $query->with('user:id,name')
                    ->orderBy('memory_date', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Get memory by ID with user check
     */
    public function findByIdForUser(int $memoryId, int $userId): ?Memory
    {
        return $this->model->where('id', $memoryId)
                          ->where(function($query) use ($userId) {
                              $query->where('user_id', $userId)
                                    ->orWhere('is_private', false);
                          })
                          ->first();
    }

    /**
     * Create new memory
     */
    public function create(array $data): Memory
    {
        return DB::transaction(function () use ($data) {
            $memory = $this->model->create($data);
            
            // Clear cache
            $this->clearUserMemoriesCache($data['user_id']);
            
            return $memory;
        });
    }

    /**
     * Update memory
     */
    public function update(Memory $memory, array $data): bool
    {
        return DB::transaction(function () use ($memory, $data) {
            $updated = $memory->update($data);
            
            // Clear cache
            $this->clearUserMemoriesCache($memory->user_id);
            
            return $updated;
        });
    }

    /**
     * Delete memory
     */
    public function delete(Memory $memory): bool
    {
        return DB::transaction(function () use ($memory) {
            $userId = $memory->user_id;
            $deleted = $memory->delete();
            
            // Clear cache
            $this->clearUserMemoriesCache($userId);
            
            return $deleted;
        });
    }

    /**
     * Get memories by date range
     */
    public function getMemoriesByDateRange(
        int $userId, 
        string $startDate, 
        string $endDate
    ): Collection {
        return $this->model->where('user_id', $userId)
                          ->whereBetween('memory_date', [$startDate, $endDate])
                          ->orderBy('memory_date', 'asc')
                          ->get();
    }

    /**
     * Get memories for calendar view (cached)
     */
    public function getCalendarMemories(int $userId, int $year, int $month): Collection
    {
        $cacheKey = "user_memories_calendar_{$userId}_{$year}_{$month}";
        
        return Cache::remember($cacheKey, 3600, function () use ($userId, $year, $month) {
            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            return $this->getMemoriesByDateRange($userId, $startDate, $endDate);
        });
    }

    /**
     * Search memories
     */
    public function search(int $userId, string $query, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
                          ->where(function($q) use ($query) {
                              $q->where('title', 'LIKE', "%{$query}%")
                                ->orWhere('description', 'LIKE', "%{$query}%")
                                ->orWhere('location', 'LIKE', "%{$query}%");
                          })
                          ->orderBy('memory_date', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Get memory statistics for user
     */
    public function getUserStatistics(int $userId): array
    {
        $cacheKey = "user_memory_stats_{$userId}";
        
        return Cache::remember($cacheKey, 1800, function () use ($userId) {
            $baseQuery = $this->model->where('user_id', $userId);
            
            return [
                'total_memories' => $baseQuery->count(),
                'this_year' => $baseQuery->whereYear('memory_date', date('Y'))->count(),
                'this_month' => $baseQuery->whereYear('memory_date', date('Y'))
                                         ->whereMonth('memory_date', date('m'))->count(),
                'private_memories' => $baseQuery->where('is_private', true)->count(),
                'public_memories' => $baseQuery->where('is_private', false)->count(),
                'oldest_memory' => $baseQuery->orderBy('memory_date', 'asc')->first()?->memory_date,
                'newest_memory' => $baseQuery->orderBy('memory_date', 'desc')->first()?->memory_date,
            ];
        });
    }

    /**
     * Get recent memories for dashboard
     */
    public function getRecentMemories(int $userId, int $limit = 5): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['year'])) {
            $query->whereYear('memory_date', $filters['year']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('memory_date', $filters['month']);
        }

        if (isset($filters['is_private'])) {
            $query->where('is_private', $filters['is_private']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'LIKE', "%{$filters['location']}%");
        }
    }

    /**
     * Clear user memories cache
     */
    private function clearUserMemoriesCache(int $userId): void
    {
        Cache::forget("user_memory_stats_{$userId}");
        
        // Clear calendar cache for current year
        $currentYear = date('Y');
        for ($month = 1; $month <= 12; $month++) {
            Cache::forget("user_memories_calendar_{$userId}_{$currentYear}_{$month}");
        }
    }
}