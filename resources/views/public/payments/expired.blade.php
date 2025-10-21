@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12">
  <div class="bg-white rounded-lg shadow p-6 text-center">
    <h1 class="text-2xl font-bold mb-4">{{ __('accounting.payment_link_expired') }}</h1>
    <p class="text-gray-600">{{ __('accounting.payment_link_no_longer_valid') }}</p>
  </div>
</div>
@endsection

