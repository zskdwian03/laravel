@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <img src="{{ asset('images/mbjek_logo.png') }}" alt="MBJEK Logo" class="auth-logo">
        <h2>Sign Up</h2>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('signup.process') }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" class="form-control" name="username" placeholder="Username" value="{{ old('username') }}" required>
        </div>
        
        <div class="mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required>
        </div>
        
        <div class="mb-3">
            <input type="text" class="form-control" name="phone" placeholder="No. Handphone" value="{{ old('phone') }}" required>
        </div>
        
        <div class="mb-3">
    <select class="form-select" name="role" id="roleSelect" required>
        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih sebagai</option>
        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
        <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>Driver</option>
    </select>
</div>

<!-- Driver specific fields (hidden by default) -->
<div id="driverFields" style="display: none;">
    <div class="mb-3">
        <select class="form-select" name="vehicle_type">
            <option value="" disabled selected>Pilih tipe kendaraan</option>
            <option value="motor">Motor</option>
            <option value="mobil">Mobil</option>
        </select>
    </div>
    
    <div class="mb-3">
        <input type="text" class="form-control" name="vehicle_color" placeholder="Warna kendaraan">
    </div>
    
    <div class="mb-3">
        <input type="text" class="form-control" name="vehicle_plate" placeholder="No. Plat kendaraan">
    </div>
</div>

        
        <div class="mb-4">
            <div class="input-group">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>
        
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
        </div>
    </form>
    
    <div class="auth-footer">
        <p>Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </div>
</div>
@endsection

@section('scripts')
@section('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            this.innerHTML = '<i class="bi bi-eye-slash"></i>';
        } else {
            passwordInput.type = 'password';
            this.innerHTML = '<i class="bi bi-eye"></i>';
        }
    });

    // Show/hide driver-specific fields
    const roleSelect = document.getElementById('roleSelect');
    const driverFields = document.getElementById('driverFields');

    function toggleDriverFields() {
        if (roleSelect.value === 'driver') {
            driverFields.style.display = 'block';
        } else {
            driverFields.style.display = 'none';
        }
    }

    // Run on page load (in case old value is driver)
    window.addEventListener('DOMContentLoaded', toggleDriverFields);
    
    // Run on change
    roleSelect.addEventListener('change', toggleDriverFields);
</script>
@endsection

@endsection