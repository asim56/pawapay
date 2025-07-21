@extends('layouts.app') {{-- or 'layouts.master' based on your setup --}}

@section('title', 'Thank You')

@section('content')
    <div class="max-w-xl mx-auto mt-20 p-6 bg-white shadow-md rounded-xl text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">🎉 Thank You!</h1>
        <p class="text-lg text-gray-700 mb-6">
            Your submission was successful. We appreciate your response!
        </p>

        {{-- Optional: Show some dynamic data --}}
        @if(session('reference_id'))
            <p class="text-gray-600 mb-2">Transaction ID: <strong>{{ session('reference_id') }}</strong></p>
        @endif

{{--        <a href="{{ route('home') }}" class="inline-block mt-6 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">--}}
{{--            Go to Homepage--}}
{{--        </a>--}}
    </div>
@endsection
