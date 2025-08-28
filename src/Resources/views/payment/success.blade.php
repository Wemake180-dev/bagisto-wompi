@extends('shop::layouts.master')

@section('page_title')
    {{ __('wompi::app.shop.messages.payment-success') }}
@endsection

@section('content')
    <div class="container px-4 lg:px-8 mx-auto">
        <div class="max-w-md mx-auto mt-10 bg-white rounded-lg shadow-lg p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="icon-done text-2xl text-green-600"></i>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    {{ __('wompi::app.shop.messages.payment-success') }}
                </h2>
                
                <p class="text-gray-600 mb-6">
                    {{ __('wompi::app.shop.messages.payment-pending') }}
                </p>
                
                <div class="space-y-3">
                    <a href="{{ route('shop.home.index') }}" 
                       class="block w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200">
                        {{ __('shop::app.home.index.title') }}
                    </a>
                    
                    <a href="{{ route('shop.customers.account.orders.index') }}" 
                       class="block w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition duration-200">
                        {{ __('shop::app.customers.account.orders.index.title') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection