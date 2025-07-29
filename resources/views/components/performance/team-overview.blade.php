@props(['teamData' => [], 'title' => 'Team Performance Overview'])

<div class="bg-white rounded-lg shadow-md border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        
        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <button onclick="exportTeamData()" class="text-gray-500 hover:text-gray-700 p-1 rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </button>
        </div>
    </div>
    
    @if(count($teamData) > 0)
        <div class="space-y-4">
            @foreach($teamData as $member)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <!-- Member Info -->
                    <div class="flex items-center space-x-4 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <div class="flex-shrink-0">
                            @if(isset($member['avatar']) && $member['avatar'])
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $member['avatar'] }}" alt="{{ $member['name'] }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        {{ substr($member['name'], 0, 2) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $member['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $member['role'] ?? __('erp.team_member') }}</p>
                            @if(isset($member['department']))
                                <p class="text-xs text-gray-400">{{ $member['department'] }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Performance Metrics -->
                    <div class="flex items-center space-x-6 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                        <!-- Tasks Completed -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">{{ __('erp.tasks') }}</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $member['completed_tasks'] ?? 0 }}</p>
                            <p class="text-xs text-gray-400">{{ __('erp.completed') }}</p>
                        </div>
                        
                        <!-- Productivity Score -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">{{ __('erp.productivity') }}</p>
                            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="w-12 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-blue-600 transition-all duration-300" 
                                         style="width: {{ $member['productivity_score'] ?? 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($member['productivity_score'] ?? 0, 0) }}%</span>
                            </div>
                        </div>
                        
                        <!-- Efficiency Rate -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">{{ __('erp.efficiency') }}</p>
                            <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                <div class="w-12 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-green-600 transition-all duration-300" 
                                         style="width: {{ min($member['efficiency_rate'] ?? 0, 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($member['efficiency_rate'] ?? 0, 0) }}%</span>
                            </div>
                        </div>
                        
                        <!-- Overall Grade -->
                        <div class="text-center">
                            <p class="text-xs text-gray-500 mb-1">{{ __('erp.grade') }}</p>
                            @php
                                $grade = $member['grade'] ?? 'C';
                                $gradeColors = [
                                    'A+' => 'bg-green-100 text-green-800',
                                    'A' => 'bg-green-100 text-green-800',
                                    'B+' => 'bg-blue-100 text-blue-800',
                                    'B' => 'bg-blue-100 text-blue-800',
                                    'C+' => 'bg-yellow-100 text-yellow-800',
                                    'C' => 'bg-yellow-100 text-yellow-800',
                                    'D' => 'bg-red-100 text-red-800',
                                    'F' => 'bg-red-100 text-red-800',
                                ];
                                $gradeColor = $gradeColors[$grade] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gradeColor }}">
                                {{ $grade }}
                            </span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                            @if(isset($member['id']))
                                <a href="{{ route('modules.performance.employees.show', $member['id']) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <button onclick="assignTask({{ $member['id'] }})" 
                                        class="text-green-600 hover:text-green-800 p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Team Summary -->
        @if(isset($teamSummary))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $teamSummary['total_members'] ?? count($teamData) }}</p>
                        <p class="text-sm text-gray-600">{{ __('erp.team_members') }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ number_format($teamSummary['avg_productivity'] ?? 0, 1) }}%</p>
                        <p class="text-sm text-gray-600">{{ __('erp.avg_productivity') }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">{{ $teamSummary['total_tasks'] ?? 0 }}</p>
                        <p class="text-sm text-gray-600">{{ __('erp.total_tasks') }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($teamSummary['avg_efficiency'] ?? 0, 1) }}%</p>
                        <p class="text-sm text-gray-600">{{ __('erp.avg_efficiency') }}</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('erp.no_team_data') }}</h3>
            <p class="text-gray-500 mb-4">{{ __('erp.no_team_data_description') }}</p>
            <a href="{{ route('modules.hr.employees.index') }}" class="btn-primary">
                {{ __('erp.manage_employees') }}
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function exportTeamData() {
    // In a real application, this would trigger a download
    console.log('Exporting team performance data...');
    
    // Show notification
    if (typeof showNotification === 'function') {
        showNotification('{{ __("erp.export_started") }}', 'info');
    }
}

function assignTask(employeeId) {
    // In a real application, this would open a task assignment modal
    console.log('Assigning task to employee:', employeeId);
    
    // Show notification
    if (typeof showNotification === 'function') {
        showNotification('{{ __("erp.task_assignment_modal_would_open") }}', 'info');
    }
}
</script>
@endpush
