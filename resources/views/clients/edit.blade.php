@extends('layout')
@section('title')
    Edit Client
@endsection
@section('main')
    <div class="page-content p-4 flex-grow-1 overflow-auto">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Add New Client</h4>
            <a href="{{ route('client.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('client.update', $client->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="clientName" placeholder="Client Name"
                                    value="{{ old('name', $client->name) }}" autofocus>
                                <label for="clientName">Client Name</label>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="clientEmail" placeholder="Email Address"
                                    value="{{ old('email', $client->email) }}">
                                <label for="clientEmail">Email Address</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                    name="phone" id="clientPhone" placeholder="Phone Number"
                                    value="{{ old('phone', $client->phone) }}">
                                <label for="clientPhone">Phone Number</label>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <textarea class="form-control @error('address') is-invalid @enderror" id="clientAddress" name="address"
                                    placeholder="Address" style="height: 90px">{{ old('address', $client->address) }}</textarea>
                                <label for="clientAddress">Address</label>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Credit Period</label>
                                <select class="form-select @error('credit_period') is-invalid @enderror"
                                    name="credit_period">
                                    @foreach ([15, 30, 45, 60] as $days)
                                        <option value="{{ $days }}"
                                            {{ old('credit_period', $client->credit_period) == $days ? 'selected' : '' }}>
                                            {{ $days }} days
                                        </option>
                                    @endforeach
                                </select>
                                @error('credit_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <a href="{{ route('client.index') }}" class="btn btn-light px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">Update Client</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
