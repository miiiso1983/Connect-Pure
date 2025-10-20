@extends('layouts.app')

@section('title', __('hr.performance_reviews'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('hr.performance_reviews') }}</h1>
            <p class="text-gray-600">{{ __('hr.review_employee_performance') }}</p>
        </div>
        <a href="#" class="btn-primary disabled:opacity-50" aria-disabled="true">{{ __('common.create') }}</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">{{ __('hr.total_reviews') }}</p>
            <p class="text-2xl font-bold">{{ number_format($summary['total_reviews'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">{{ __('hr.pending_reviews') }}</p>
            <p class="text-2xl font-bold">{{ number_format($summary['pending_reviews'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">{{ __('hr.completed_reviews') }}</p>
            <p class="text-2xl font-bold">{{ number_format($summary['completed_reviews'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">{{ __('hr.avg_overall_rating') }}</p>
            <p class="text-2xl font-bold">{{ number_format($summary['avg_overall_rating'] ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.department') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.review_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('hr.status') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $review->employee->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $review->employee->department->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($review->review_date)->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($review->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">{{ __('hr.no_records_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection

