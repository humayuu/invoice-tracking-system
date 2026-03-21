@extends('layout')
@section('title')
    Create Supplier
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Add New Supplier</h4>
            <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('supplier.store') }}">
                            @csrf
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="name" id="supplierName"
                                    placeholder="Supplier Name" autofocus>
                                <label for="supplierName">Supplier Name</label>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="email" id="supplierEmail"
                                    placeholder="Email Address">
                                <label for="supplierEmail">Email Address</label>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" name="phone" id="supplierPhone"
                                    placeholder="Phone Number">
                                <label for="supplierPhone">Phone Number</label>
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="supplierAddress" name="address" placeholder="Address" style="height: 90px"></textarea>
                                <label for="supplierAddress">Address</label>
                                @error('address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Credit Period</label>
                                <select class="form-select" name="credit_period">
                                    <option value="15">15 days</option>
                                    <option value="30" selected>30 days</option>
                                    <option value="45">45 days</option>
                                    <option value="60">60 days</option>
                                </select>
                                @error('credit_period')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('supplier.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Create New Supplier</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
