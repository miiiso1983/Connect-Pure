@extends('layouts.app')

@section('title', __('hr.departments'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('hr.departments') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('hr.manage_departments') }}</p>
        </div>
        <div class="flex space-x-3 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
            <a href="{{ route('modules.hr.index') }}" class="btn-secondary">
                {{ __('hr.back_to_hr') }}
            </a>
            <a href="{{ route('modules.hr.departments.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('hr.add_department') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <x-stat-card
            title="{{ __('hr.total_departments') }}"
            :value="$departments->total()"
            color="blue"
            :icon="'<svg class=\'w-6 h-6 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.active_departments') }}"
            :value="$departments->where('is_active', true)->count()"
            color="green"
            :icon="'<svg class=\'w-6 h-6 text-green-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.total_employees') }}"
            :value="$departments->sum('employees_count')"
            color="purple"
            :icon="'<svg class=\'w-6 h-6 text-purple-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg>'"
        />

        <x-stat-card
            title="{{ __('hr.avg_employees_per_dept') }}"
            :value="$departments->count() > 0 ? round($departments->sum('employees_count') / $departments->count(), 1) : 0"
            color="orange"
            :icon="'<svg class=\'w-6 h-6 text-orange-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'></path></svg>'"
        />
    </div>

    <!-- Filters -->
    <x-card title="{{ __('hr.filters') }}">
        <form method="GET" action="{{ route('modules.hr.departments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.search') }}</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('hr.search_departments') }}" class="form-input">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.status') }}</label>
                <select id="status" name="status" class="form-select">
                    <option value="">{{ __('hr.all_statuses') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('hr.active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('hr.inactive') }}</option>
                </select>
            </div>

            <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">{{ __('hr.sort_by') }}</label>
                <select id="sort_by" name="sort_by" class="form-select">
                    <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>{{ __('hr.name') }}</option>
                    <option value="employees_count" {{ request('sort_by') === 'employees_count' ? 'selected' : '' }}>{{ __('hr.employee_count') }}</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>{{ __('hr.created_at') }}</option>
                </select>
            </div>

            <div class="flex items-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ __('hr.search') }}
                </button>
                <a href="{{ route('modules.hr.departments.index') }}" class="btn-secondary">
                    {{ __('hr.clear') }}
                </a>
            </div>
        </form>
    </x-card>

    <!-- Departments List -->
    <x-card title="{{ __('hr.departments_list') }}">
        @if($departments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.department') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.department_head') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.employees') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.budget') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($departments as $department)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $department->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $department->code ?? 'N/A' }}</div>
                                            @if($department->description)
                                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($department->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($department->manager)
                                        <div class="text-sm text-gray-900">{{ $department->manager->first_name }} {{ $department->manager->last_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $department->manager->email }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">{{ __('hr.no_manager_assigned') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900">{{ $department->employees_count ?? 0 }}</span>
                                        <svg class="w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($department->budget)
                                        <div class="text-sm text-gray-900">${{ number_format($department->budget, 0) }}</div>
                                    @else
                                        <span class="text-sm text-gray-400">{{ __('hr.no_budget_set') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $department->is_active ? __('hr.active') : __('hr.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 {{ app()->getLocale() === 'ar' ? 'space-x-reverse' : '' }}">
                                        <a href="{{ route('modules.hr.departments.show', $department) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ __('hr.view') }}
                                        </a>
                                        <a href="{{ route('modules.hr.departments.edit', $department) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('hr.edit') }}
                                        </a>
                                        <form action="{{ route('modules.hr.departments.destroy', $department) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('{{ __('hr.confirm_delete_department') }}')">
                                                {{ __('hr.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $departments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('hr.no_departments_found') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('hr.no_departments_match_criteria') }}</p>
                <a href="{{ route('modules.hr.departments.create') }}" class="btn-primary">
                    {{ __('hr.add_first_department') }}
                </a>
            </div>
        @endif
    </x-card>
</div>
@endsection
