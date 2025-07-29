<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modules\Performance\Models\Task;
use App\Models\Modules\Performance\Models\TaskAssignment;
use App\Models\Modules\Performance\Models\PerformanceMetric;

class PerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample tasks
        $tasks = [
            [
                'title' => 'Implement User Authentication System',
                'description' => 'Develop a secure user authentication system with login, registration, and password reset functionality.',
                'status' => 'completed',
                'priority' => 'high',
                'category' => 'development',
                'created_by' => 'Project Manager',
                'project_name' => 'ERP System',
                'start_date' => now()->subDays(10),
                'due_date' => now()->subDays(2),
                'completed_at' => now()->subDays(1),
                'estimated_hours' => 40,
                'actual_hours' => 35,
                'completion_percentage' => 100,
                'tags' => ['authentication', 'security', 'backend'],
                'notes' => 'Completed ahead of schedule with excellent code quality.',
            ],
            [
                'title' => 'Design Dashboard UI/UX',
                'description' => 'Create modern and intuitive dashboard interface with responsive design and accessibility features.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'category' => 'design',
                'created_by' => 'Design Lead',
                'project_name' => 'ERP System',
                'start_date' => now()->subDays(5),
                'due_date' => now()->addDays(3),
                'estimated_hours' => 30,
                'actual_hours' => 20,
                'completion_percentage' => 75,
                'tags' => ['ui', 'ux', 'dashboard', 'responsive'],
                'notes' => 'Making good progress, on track for completion.',
            ],
            [
                'title' => 'API Integration Testing',
                'description' => 'Comprehensive testing of all API endpoints including unit tests, integration tests, and performance tests.',
                'status' => 'pending',
                'priority' => 'high',
                'category' => 'testing',
                'created_by' => 'QA Lead',
                'project_name' => 'ERP System',
                'start_date' => now()->addDays(1),
                'due_date' => now()->addDays(7),
                'estimated_hours' => 25,
                'tags' => ['testing', 'api', 'quality-assurance'],
                'notes' => 'Waiting for development completion before starting.',
            ],
            [
                'title' => 'Database Performance Optimization',
                'description' => 'Optimize database queries, add proper indexing, and implement caching strategies.',
                'status' => 'on_hold',
                'priority' => 'medium',
                'category' => 'development',
                'created_by' => 'Tech Lead',
                'project_name' => 'Performance Improvement',
                'start_date' => now()->subDays(3),
                'due_date' => now()->addDays(10),
                'estimated_hours' => 20,
                'actual_hours' => 5,
                'completion_percentage' => 25,
                'tags' => ['database', 'optimization', 'performance'],
                'notes' => 'On hold pending infrastructure decisions.',
            ],
            [
                'title' => 'User Documentation',
                'description' => 'Create comprehensive user documentation including user guides, API documentation, and troubleshooting guides.',
                'status' => 'in_progress',
                'priority' => 'low',
                'category' => 'documentation',
                'created_by' => 'Technical Writer',
                'project_name' => 'Documentation',
                'start_date' => now()->subDays(15),
                'due_date' => now()->subDays(5),
                'estimated_hours' => 15,
                'actual_hours' => 8,
                'completion_percentage' => 60,
                'tags' => ['documentation', 'user-guide', 'api-docs'],
                'notes' => 'Delayed due to changing requirements.',
            ],
            [
                'title' => 'Weekly Team Meeting',
                'description' => 'Regular team sync meeting to discuss progress, blockers, and upcoming priorities.',
                'status' => 'completed',
                'priority' => 'medium',
                'category' => 'meeting',
                'created_by' => 'Scrum Master',
                'project_name' => 'Team Management',
                'start_date' => now()->subDays(1),
                'due_date' => now()->subDays(1),
                'completed_at' => now()->subDays(1),
                'estimated_hours' => 2,
                'actual_hours' => 2,
                'completion_percentage' => 100,
                'tags' => ['meeting', 'sync', 'team'],
                'notes' => 'Productive meeting with clear action items.',
            ],
        ];

        $employees = [
            ['name' => 'Ahmed Al-Rashid', 'email' => 'ahmed@company.com', 'role' => 'Senior Developer'],
            ['name' => 'Sara Mohammed', 'email' => 'sara@company.com', 'role' => 'UI/UX Designer'],
            ['name' => 'Omar Hassan', 'email' => 'omar@company.com', 'role' => 'QA Engineer'],
            ['name' => 'Fatima Al-Zahra', 'email' => 'fatima@company.com', 'role' => 'Backend Developer'],
            ['name' => 'Khalid Al-Mansoori', 'email' => 'khalid@company.com', 'role' => 'Technical Writer'],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::create($taskData);

            // Assign tasks to employees
            $this->assignTaskToEmployees($task, $employees);
        }

        // Create performance metrics
        $this->createPerformanceMetrics($employees);
    }

    private function assignTaskToEmployees(Task $task, array $employees): void
    {
        // Assign based on task category and employee role
        $assignments = [];

        switch ($task->category) {
            case 'development':
                $assignments = [
                    ['name' => 'Ahmed Al-Rashid', 'email' => 'ahmed@company.com', 'role' => 'Senior Developer'],
                    ['name' => 'Fatima Al-Zahra', 'email' => 'fatima@company.com', 'role' => 'Backend Developer'],
                ];
                break;
            case 'design':
                $assignments = [
                    ['name' => 'Sara Mohammed', 'email' => 'sara@company.com', 'role' => 'UI/UX Designer'],
                ];
                break;
            case 'testing':
                $assignments = [
                    ['name' => 'Omar Hassan', 'email' => 'omar@company.com', 'role' => 'QA Engineer'],
                ];
                break;
            case 'documentation':
                $assignments = [
                    ['name' => 'Khalid Al-Mansoori', 'email' => 'khalid@company.com', 'role' => 'Technical Writer'],
                ];
                break;
            default:
                $assignments = [$employees[array_rand($employees)]];
                break;
        }

        foreach ($assignments as $employee) {
            $status = match($task->status) {
                'completed' => 'completed',
                'in_progress' => 'in_progress',
                'pending' => 'assigned',
                'on_hold' => 'accepted',
                default => 'assigned'
            };

            TaskAssignment::create([
                'task_id' => $task->id,
                'employee_name' => $employee['name'],
                'employee_email' => $employee['email'],
                'employee_role' => $employee['role'],
                'assigned_by' => $task->created_by,
                'assigned_at' => $task->created_at,
                'started_at' => $task->start_date,
                'completed_at' => $task->completed_at,
                'assignment_status' => $status,
                'assignment_notes' => 'Assigned based on expertise and availability.',
            ]);
        }
    }

    private function createPerformanceMetrics(array $employees): void
    {
        foreach ($employees as $employee) {
            // Create weekly metrics for the past 4 weeks
            for ($week = 3; $week >= 0; $week--) {
                $date = now()->subWeeks($week)->startOfWeek();

                $tasksAssigned = rand(3, 8);
                $tasksCompleted = rand(2, $tasksAssigned);
                $tasksOverdue = rand(0, 2);
                $estimatedHours = rand(20, 40);
                $actualHours = rand(18, 45);

                PerformanceMetric::create([
                    'employee_name' => $employee['name'],
                    'employee_email' => $employee['email'],
                    'metric_date' => $date,
                    'metric_type' => 'weekly',
                    'tasks_assigned' => $tasksAssigned,
                    'tasks_completed' => $tasksCompleted,
                    'tasks_overdue' => $tasksOverdue,
                    'completion_rate' => PerformanceMetric::calculateCompletionRate($tasksCompleted, $tasksAssigned),
                    'total_hours_worked' => $actualHours,
                    'estimated_hours' => $estimatedHours,
                    'actual_hours' => $actualHours,
                    'efficiency_rate' => PerformanceMetric::calculateEfficiencyRate($estimatedHours, $actualHours),
                    'tasks_on_time' => $tasksCompleted - $tasksOverdue,
                    'tasks_delayed' => $tasksOverdue,
                    'on_time_delivery_rate' => PerformanceMetric::calculateOnTimeRate($tasksCompleted - $tasksOverdue, $tasksCompleted),
                    'productivity_score' => rand(70, 95),
                    'quality_score' => rand(75, 98),
                    'overall_score' => 0, // Will be calculated below
                ]);
            }

            // Create monthly metrics for the past 3 months
            for ($month = 2; $month >= 0; $month--) {
                $date = now()->subMonths($month)->startOfMonth();

                $tasksAssigned = rand(12, 25);
                $tasksCompleted = rand(10, $tasksAssigned);
                $tasksOverdue = rand(1, 5);
                $estimatedHours = rand(80, 160);
                $actualHours = rand(75, 180);

                $productivityScore = rand(70, 95);
                $qualityScore = rand(75, 98);
                $efficiencyRate = PerformanceMetric::calculateEfficiencyRate($estimatedHours, $actualHours);

                PerformanceMetric::create([
                    'employee_name' => $employee['name'],
                    'employee_email' => $employee['email'],
                    'metric_date' => $date,
                    'metric_type' => 'monthly',
                    'tasks_assigned' => $tasksAssigned,
                    'tasks_completed' => $tasksCompleted,
                    'tasks_overdue' => $tasksOverdue,
                    'completion_rate' => PerformanceMetric::calculateCompletionRate($tasksCompleted, $tasksAssigned),
                    'total_hours_worked' => $actualHours,
                    'estimated_hours' => $estimatedHours,
                    'actual_hours' => $actualHours,
                    'efficiency_rate' => $efficiencyRate,
                    'tasks_on_time' => $tasksCompleted - $tasksOverdue,
                    'tasks_delayed' => $tasksOverdue,
                    'on_time_delivery_rate' => PerformanceMetric::calculateOnTimeRate($tasksCompleted - $tasksOverdue, $tasksCompleted),
                    'productivity_score' => $productivityScore,
                    'quality_score' => $qualityScore,
                    'overall_score' => PerformanceMetric::calculateOverallScore($productivityScore, $qualityScore, $efficiencyRate),
                ]);
            }
        }

        // Update weekly metrics with calculated overall scores
        $weeklyMetrics = PerformanceMetric::where('metric_type', 'weekly')->get();
        foreach ($weeklyMetrics as $metric) {
            $metric->update([
                'overall_score' => PerformanceMetric::calculateOverallScore(
                    $metric->productivity_score,
                    $metric->quality_score,
                    $metric->efficiency_rate
                )
            ]);
        }
    }
}
