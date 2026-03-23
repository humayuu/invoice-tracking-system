@extends('layout')
@section('title', '403 - Forbidden')
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto d-flex align-items-center justify-content-center">
        <div class="text-center py-5">
            <h1 class="fw-bold mb-0" style="font-size: 96px; line-height: 1; color: #dee2e6;">403</h1>
            <h4 class="fw-bold mt-3 mb-2">Access Forbidden</h4>
            <p class="text-muted mb-4">You don't have permission to access this page.</p>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">
                <i class="fa-solid fa-arrow-left me-2"></i>Go Back
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fa-solid fa-house me-2"></i>Dashboard
            </a>
        </div>
    </div>
@endsection
