@extends('layouts.app')

@section('title', 'Verifikasi Kode')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <img src="{{ asset('images/logo.png') }}" alt="MBJEK Logo" class="auth-logo">
        <h2>Verifikasi Kode</h2>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    
    @if (session('warning'))
        <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
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
    
    <p class="text-center mb-4">
        Kami telah mengirim 4 digit kode ke<br>
        <strong>{{ $email }}</strong>
    </p>
    
    <form action="{{ route('verification.verify') }}" method="POST">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        
        <div class="verification-input mb-4">
            <input type="text" name="digit1" class="form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" required autofocus>
            <input type="text" name="digit2" class="form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <input type="text" name="digit3" class="form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
            <input type="text" name="digit4" class="form-control" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
        </div>
        
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-lg">Verifikasi</button>
        </div>
    </form>
    
    <div class="text-center mt-3">
        <p>Tidak menerima kode? 
            <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="btn btn-link p-0">Kirim Ulang</button>
            </form>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-focus to next input on verification code entry
    const codeInputs = document.querySelectorAll('.verification-input input');
    
    codeInputs.forEach((input, index) => {
        input.addEventListener('keyup', function(e) {
            if (e.key >= 0 && e.key <= 9) {
                if (index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            } else if (e.key === 'Backspace') {
                if (index > 0) {
                    if (this.value === '') {
                        codeInputs[index - 1].focus();
                    }
                }
            }
        });
        
        input.addEventListener('keydown', function(e) {
            // Allow only numbers and backspace
            if (e.key !== 'Backspace' && (e.key < '0' || e.key > '9')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto focus to first input on page load
    window.onload = function() {
        codeInputs[0].focus();
    };
</script>
@endsection