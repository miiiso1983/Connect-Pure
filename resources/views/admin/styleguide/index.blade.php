@extends('layouts.app')

@section('title', 'UI Styleguide')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-900">UI Styleguide</h1>
            <p class="text-gray-600">Preview of core UI components in light/dark modes</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        <!-- Buttons -->
        <x-ui.card class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Buttons</h2>
            <div class="flex flex-wrap gap-3">
                <x-ui.button>Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="success">Success</x-ui.button>
                <x-ui.button variant="warning">Warning</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="outline">Outline</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
            </div>
        </x-ui.card>

        <!-- Form Controls -->
        <x-ui.card class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Form Controls</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.input name="name" label="Text Input" placeholder="Enter text" />
                <x-ui.select name="country" label="Select">
                    <option value="">Choose</option>
                    <option>Option A</option>
                    <option>Option B</option>
                </x-ui.select>
                <x-ui.textarea name="desc" label="Textarea" placeholder="Write something..." />
                <div class="space-y-4">
                    <x-ui.checkbox name="agree" label="I agree to terms" />
                    <x-ui.toggle name="enabled" label="Enabled" />
                </div>
            </div>
        </x-ui.card>

        <!-- Cards & Tables -->
        <x-ui.card class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Cards & Tables</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-ui.card class="p-6">
                    <div class="text-sm text-gray-500">Stat Card</div>
                    <div class="text-3xl font-bold text-gray-900 mt-2">12,480</div>
                    <div class="text-sm text-green-600 mt-1">+8.2% vs last week</div>
                </x-ui.card>
                <div class="modern-card overflow-hidden">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sample Record</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>$1,200.00</td>
                            </tr>
                            <tr>
                                <td>Another Record</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>$460.50</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
