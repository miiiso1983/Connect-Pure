<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class PerformanceReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_performance_reviews';

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'review_date',
        'technical_skills',
        'communication_skills',
        'teamwork',
        'leadership',
        'problem_solving',
        'initiative',
        'punctuality',
        'quality_of_work',
        'overall_rating',
        'achievements',
        'areas_for_improvement',
        'goals',
        'goals_next_period',
        'reviewer_comments',
        'employee_comments',
        'status',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'review_date' => 'date',
        'completed_at' => 'datetime',
        'goals' => 'array',
        'goals_next_period' => 'array',
        'technical_skills' => 'integer',
        'communication_skills' => 'integer',
        'teamwork' => 'integer',
        'leadership' => 'integer',
        'problem_solving' => 'integer',
        'initiative' => 'integer',
        'punctuality' => 'integer',
        'quality_of_work' => 'integer',
        'overall_rating' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('review_period', $period);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('review_date', $year);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByReviewer($query, $reviewerId)
    {
        return $query->where('reviewer_id', $reviewerId);
    }

    // Accessors
    public function getReviewPeriodTextAttribute()
    {
        $periods = [
            'quarterly' => 'Quarterly',
            'semi_annual' => 'Semi-Annual',
            'annual' => 'Annual',
        ];

        return $periods[$this->review_period] ?? ucfirst($this->review_period);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'green',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getOverallRatingTextAttribute()
    {
        if (!$this->overall_rating) {
            return 'Not Rated';
        }

        $rating = round($this->overall_rating, 1);
        
        if ($rating >= 4.5) {
            return 'Excellent';
        } elseif ($rating >= 3.5) {
            return 'Good';
        } elseif ($rating >= 2.5) {
            return 'Satisfactory';
        } elseif ($rating >= 1.5) {
            return 'Needs Improvement';
        } else {
            return 'Unsatisfactory';
        }
    }

    public function getProgressPercentageAttribute()
    {
        $totalFields = 8; // Number of rating fields
        $completedFields = 0;

        $ratingFields = [
            'technical_skills',
            'communication_skills',
            'teamwork',
            'leadership',
            'problem_solving',
            'initiative',
            'punctuality',
            'quality_of_work',
        ];

        foreach ($ratingFields as $field) {
            if (!is_null($this->$field)) {
                $completedFields++;
            }
        }

        return ($completedFields / $totalFields) * 100;
    }

    public function getAverageSkillRatingAttribute()
    {
        $ratings = [
            $this->technical_skills,
            $this->communication_skills,
            $this->teamwork,
            $this->leadership,
            $this->problem_solving,
            $this->initiative,
            $this->punctuality,
            $this->quality_of_work,
        ];

        $validRatings = array_filter($ratings, function($rating) {
            return !is_null($rating);
        });

        if (empty($validRatings)) {
            return null;
        }

        return array_sum($validRatings) / count($validRatings);
    }

    // Methods
    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function canBeCompleted()
    {
        return $this->status !== 'completed' && $this->overall_rating !== null;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function getRatingStars($rating)
    {
        if (is_null($rating)) {
            return str_repeat('☆', 5);
        }

        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;

        return str_repeat('★', $fullStars) . 
               str_repeat('☆', $halfStar) . 
               str_repeat('☆', $emptyStars);
    }

    public function getSkillRatings()
    {
        return [
            'technical_skills' => [
                'label' => 'Technical Skills',
                'value' => $this->technical_skills,
                'stars' => $this->getRatingStars($this->technical_skills),
            ],
            'communication_skills' => [
                'label' => 'Communication Skills',
                'value' => $this->communication_skills,
                'stars' => $this->getRatingStars($this->communication_skills),
            ],
            'teamwork' => [
                'label' => 'Teamwork',
                'value' => $this->teamwork,
                'stars' => $this->getRatingStars($this->teamwork),
            ],
            'leadership' => [
                'label' => 'Leadership',
                'value' => $this->leadership,
                'stars' => $this->getRatingStars($this->leadership),
            ],
            'problem_solving' => [
                'label' => 'Problem Solving',
                'value' => $this->problem_solving,
                'stars' => $this->getRatingStars($this->problem_solving),
            ],
            'initiative' => [
                'label' => 'Initiative',
                'value' => $this->initiative,
                'stars' => $this->getRatingStars($this->initiative),
            ],
            'punctuality' => [
                'label' => 'Punctuality',
                'value' => $this->punctuality,
                'stars' => $this->getRatingStars($this->punctuality),
            ],
            'quality_of_work' => [
                'label' => 'Quality of Work',
                'value' => $this->quality_of_work,
                'stars' => $this->getRatingStars($this->quality_of_work),
            ],
        ];
    }

    // Static methods
    public static function getReviewPeriods()
    {
        return [
            'quarterly' => 'Quarterly',
            'semi_annual' => 'Semi-Annual',
            'annual' => 'Annual',
        ];
    }

    public static function getStatuses()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    public static function getRatingScale()
    {
        return [
            1 => 'Poor',
            2 => 'Below Average',
            3 => 'Average',
            4 => 'Good',
            5 => 'Excellent',
        ];
    }

    public static function getAverageRatingByDepartment($departmentId, $year = null)
    {
        $query = static::whereHas('employee', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })->where('status', 'completed');

        if ($year) {
            $query->whereYear('review_date', $year);
        }

        return $query->avg('overall_rating');
    }

    public static function getTopPerformers($limit = 10, $year = null)
    {
        $query = static::with(['employee.department'])
            ->where('status', 'completed')
            ->orderByDesc('overall_rating');

        if ($year) {
            $query->whereYear('review_date', $year);
        }

        return $query->take($limit)->get();
    }

    public static function getPerformanceTrends($year = null)
    {
        $query = static::where('status', 'completed');

        if ($year) {
            $query->whereYear('review_date', $year);
        }

        return $query->selectRaw('
                YEAR(review_date) as year,
                MONTH(review_date) as month,
                AVG(overall_rating) as avg_rating,
                COUNT(*) as review_count
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }
}
