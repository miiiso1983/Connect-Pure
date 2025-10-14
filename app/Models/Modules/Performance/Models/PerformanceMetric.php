<?php

namespace App\Models\Modules\Performance\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'employee_name',
        'employee_email',
        'metric_date',
        'metric_type',
        'tasks_assigned',
        'tasks_completed',
        'tasks_overdue',
        'completion_rate',
        'total_hours_worked',
        'estimated_hours',
        'actual_hours',
        'efficiency_rate',
        'tasks_on_time',
        'tasks_delayed',
        'on_time_delivery_rate',
        'productivity_score',
        'quality_score',
        'overall_score',
        'additional_metrics',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'completion_rate' => 'decimal:2',
        'efficiency_rate' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'productivity_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'additional_metrics' => 'array',
    ];

    public function getPerformanceGradeAttribute(): string
    {
        $score = $this->overall_score;

        return match (true) {
            $score >= 90 => 'A+',
            $score >= 85 => 'A',
            $score >= 80 => 'B+',
            $score >= 75 => 'B',
            $score >= 70 => 'C+',
            $score >= 65 => 'C',
            $score >= 60 => 'D',
            default => 'F'
        };
    }

    public function getPerformanceColorAttribute(): string
    {
        $score = $this->overall_score;

        return match (true) {
            $score >= 85 => 'green',
            $score >= 75 => 'blue',
            $score >= 65 => 'yellow',
            $score >= 50 => 'orange',
            default => 'red'
        };
    }

    public function getProductivityLevelAttribute(): string
    {
        $score = $this->productivity_score;

        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 80 => 'Very Good',
            $score >= 70 => 'Good',
            $score >= 60 => 'Average',
            $score >= 50 => 'Below Average',
            default => 'Poor'
        };
    }

    public function getEfficiencyLevelAttribute(): string
    {
        $rate = $this->efficiency_rate;

        return match (true) {
            $rate >= 95 => 'Highly Efficient',
            $rate >= 85 => 'Efficient',
            $rate >= 75 => 'Moderately Efficient',
            $rate >= 65 => 'Average',
            default => 'Needs Improvement'
        };
    }

    // Scopes
    public function scopeByEmployee($query, $employeeName)
    {
        return $query->where('employee_name', $employeeName);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderBy('overall_score', 'desc')->limit($limit);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('metric_date', 'desc');
    }

    // Static methods for calculations
    public static function calculateCompletionRate($completed, $assigned): float
    {
        return $assigned > 0 ? ($completed / $assigned) * 100 : 0;
    }

    public static function calculateEfficiencyRate($estimated, $actual): float
    {
        return $actual > 0 ? ($estimated / $actual) * 100 : 0;
    }

    public static function calculateOnTimeRate($onTime, $total): float
    {
        return $total > 0 ? ($onTime / $total) * 100 : 0;
    }

    public static function calculateOverallScore($productivity, $quality, $efficiency): float
    {
        return $productivity * 0.4 + $quality * 0.3 + $efficiency * 0.3;
    }
}
