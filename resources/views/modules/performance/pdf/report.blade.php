<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performance Report - {{ ucfirst($period) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1F2937;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        
        .header p {
            color: #6B7280;
            margin: 0;
            font-size: 14px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #E5E7EB;
            background-color: #F9FAFB;
        }
        
        .summary-cell h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #1F2937;
        }
        
        .summary-cell p {
            margin: 0;
            color: #6B7280;
            font-size: 11px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1F2937;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th,
        .table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #E5E7EB;
        }
        
        .table th {
            background-color: #F3F4F6;
            font-weight: bold;
            color: #374151;
        }
        
        .table tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-in-progress {
            background-color: #DBEAFE;
            color: #1E40AF;
        }
        
        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-overdue {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .progress-bar {
            width: 100px;
            height: 8px;
            background-color: #E5E7EB;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #3B82F6;
            border-radius: 4px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            color: #6B7280;
            font-size: 10px;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: top;
        }
        
        .column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
        
        .metric-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #F3F4F6;
        }
        
        .metric-label {
            color: #6B7280;
        }
        
        .metric-value {
            font-weight: bold;
            color: #1F2937;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Performance Report</h1>
        <p>Period: {{ ucfirst($period) }} | Generated on {{ date('F j, Y \a\t g:i A') }}</p>
        @if($employee)
            <p>Employee: {{ $employee }}</p>
        @endif
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <h3>{{ $data['stats']['total_tasks'] ?? 0 }}</h3>
                    <p>Total Tasks</p>
                </div>
                <div class="summary-cell">
                    <h3>{{ $data['stats']['completed_tasks'] ?? 0 }}</h3>
                    <p>Completed Tasks</p>
                </div>
                <div class="summary-cell">
                    <h3>{{ $data['stats']['completion_rate'] ?? 0 }}%</h3>
                    <p>Completion Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="section">
        <div class="section-title">Key Performance Metrics</div>
        <div class="two-column">
            <div class="column">
                <div class="metric-item">
                    <span class="metric-label">Active Tasks:</span>
                    <span class="metric-value">{{ $data['stats']['active_tasks'] ?? 0 }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Overdue Tasks:</span>
                    <span class="metric-value">{{ $data['stats']['overdue_tasks'] ?? 0 }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Total Assignments:</span>
                    <span class="metric-value">{{ $data['stats']['total_assignments'] ?? 0 }}</span>
                </div>
            </div>
            <div class="column">
                <div class="metric-item">
                    <span class="metric-label">Completed Assignments:</span>
                    <span class="metric-value">{{ $data['stats']['completed_assignments'] ?? 0 }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Assignment Completion Rate:</span>
                    <span class="metric-value">{{ $data['stats']['assignment_completion_rate'] ?? 0 }}%</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Average Efficiency:</span>
                    <span class="metric-value">{{ $data['stats']['avg_efficiency'] ?? 0 }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Details -->
    @if(isset($data['tasks']) && count($data['tasks']) > 0)
    <div class="section">
        <div class="section-title">Task Details</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['tasks']->take(20) as $task)
                <tr>
                    <td>{{ $task->title ?? 'N/A' }}</td>
                    <td>{{ $task->category ?? 'N/A' }}</td>
                    <td>{{ ucfirst($task->priority ?? 'normal') }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $task->status ?? 'pending')) }}">
                            {{ $task->status ?? 'Pending' }}
                        </span>
                    </td>
                    <td>{{ $task->assigned_to ?? 'Unassigned' }}</td>
                    <td>{{ $task->due_date ? date('M j, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $task->completion_percentage ?? 0 }}%"></div>
                        </div>
                        {{ $task->completion_percentage ?? 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($data['tasks']) > 20)
            <p><em>Showing first 20 tasks. Total tasks: {{ count($data['tasks']) }}</em></p>
        @endif
    </div>
    @endif

    <!-- Top Performers -->
    @if(isset($data['top_performers']) && count($data['top_performers']) > 0)
    <div class="section">
        <div class="section-title">Top Performers</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Tasks Completed</th>
                    <th>Completion Rate</th>
                    <th>Performance Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_performers'] as $performer)
                <tr>
                    <td>{{ $performer['name'] ?? 'N/A' }}</td>
                    <td>{{ $performer['completed_tasks'] ?? 0 }}</td>
                    <td>{{ $performer['completion_rate'] ?? 0 }}%</td>
                    <td>{{ $performer['performance_score'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the Performance Management System.</p>
        <p>For questions or support, please contact your system administrator.</p>
    </div>
</body>
</html>
