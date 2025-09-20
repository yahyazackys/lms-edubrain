@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="font-heading text-xl font-semibold mb-2">Dashboard</h2>
    <p class="text-sm">Selamat datang di sistem LMS, {{ Auth::user()->nama }}.</p>
@endsection
