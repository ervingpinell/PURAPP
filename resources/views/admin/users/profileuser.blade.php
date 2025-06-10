<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Green Vacations</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logoCompanyWhite.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c3e50;
            color: #ecf0f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .edit-container {
            max-width: 500px;
            margin: 80px auto;
            background: #34495e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .form-control::placeholder {
            color: #95a5a6;
        }

        .form-label {
            margin-top: 10px;
        }

        .password-requirements li {
            font-size: 0.9rem;
            list-style: none;
        }

        .btn-success {
            background-color: #60A862;
            border: none;
        }

        .btn-success:hover {
            background-color: #4a894e;
        }

        .text-success {
            color: #2ecc71 !important;
        }

        .text-muted {
            color: #bdc3c7 !important;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: rgba(10, 25, 15, 0.9);">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logoCompanyWhite.png') }}" alt="Green Vacations" height="40" class="me-2">
            Green Vacations
        </a>
    </div>
</nav>

<div class="edit-container">
    <h3 class="text-center mb-4">Edit My Profile</h3>
    <form action="{{ route('user.profile.update') }}" method="POST">
        @csrf

        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                   value="{{ old('full_name', auth()->user()->full_name) }}" required>
            @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', auth()->user()->email) }}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', auth()->user()->phone) }}" required>
            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">New Password <small class="text-muted">(Optional)</small></label>
            <div class="input-group">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                <span class="input-group-text bg-transparent">
                    <i class="fas fa-eye toggle-password" data-target="password" style="cursor: pointer;"></i>
                </span>
            </div>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror

            <ul class="password-requirements mt-2 ps-3">
                <li id="req-length" class="text-muted">Minimum 8 characters</li>
                <li id="req-special" class="text-muted">At least one special character (!@#$%^&*)</li>
                <li id="req-number" class="text-muted">At least one number</li>
            </ul>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                <span class="input-group-text bg-transparent">
                    <i class="fas fa-eye toggle-password" data-target="password_confirmation" style="cursor: pointer;"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Save Changes</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const reqLength = document.getElementById('req-length');
        const reqSpecial = document.getElementById('req-special');
        const reqNumber = document.getElementById('req-number');

        passwordInput.addEventListener('input', function () {
            const value = passwordInput.value;

            reqLength.className = value.length >= 8 ? 'text-success' : 'text-muted';
            reqSpecial.className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? 'text-success' : 'text-muted';
            reqNumber.className = /\d/.test(value) ? 'text-success' : 'text-muted';
        });

        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.target);
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });
    });
</script>
</body>
</html>
