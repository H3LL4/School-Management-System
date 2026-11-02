@extends('layouts.school')
@section('title', 'My profile')
@section('content')
    @php $user = auth()->user(); @endphp

    <style>
        @keyframes saved-fade {

            0%,
            70% {
                opacity: 1
            }

            100% {
                opacity: 0
            }
        }

        .saved-note {
            color: var(--ok);
            font-size: .9rem;
            animation: saved-fade 3.5s forwards
        }

        .section-ico {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            flex: none;
            font-size: 1.05rem
        }

        .danger-zone {
            border-color: #f3c7c5
        }
    </style>

    <h1 class="h3 mb-1">My profile</h1>
    <p class="text-muted mb-4">Manage your name, email and password.</p>

    <div class="row g-3">
        {{-- summary --}}
        <div class="col-lg-4">
            <div class="card text-center">
                <div class="card-body p-4">
                    <span class="avatar mx-auto mb-3"
                        style="--h: {{ crc32($user->name) % 360 }}; width:84px; height:84px; font-size:2rem">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    <h2 class="h5 mb-1">{{ $user->name }}</h2>
                    <div class="text-muted small mb-3">{{ $user->email }}</div>
                    <span class="badge text-bg-primary px-3 py-2">{{ $user->role_label }}</span>
                    <hr class="my-4">
                    <div class="small text-muted">Member since {{ $user->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 d-grid gap-3 align-content-start">

            {{-- profile information --}}
            <div class="card">
                <div class="card-header bg-white d-flex align-items-center gap-3">
                    <span class="section-ico" style="background:#e6ecff;color:var(--brand)"><i
                            class="bi bi-person-fill"></i></span>
                    <div>
                        <div class="fw-semibold">Profile information</div>
                        <div class="small text-muted">Update your name and email address.</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PATCH')
                        <div class="row">
                            <x-field class="col-md-6" name="name" label="Name" :value="$user->name" required
                                autocomplete="name" />
                            <x-field class="col-md-6" name="email" label="Email address" type="email" :value="$user->email"
                                required autocomplete="username" />
                        </div>
                        <button class="btn btn-primary">Save changes</button>
                        @if (session('status') === 'profile-updated')
                            <span class="saved-note ms-2"><i class="bi bi-check-circle-fill me-1"></i>Saved</span>
                        @endif
                    </form>
                </div>
            </div>

            {{-- password --}}
            <div class="card">
                <div class="card-header bg-white d-flex align-items-center gap-3">
                    <span class="section-ico" style="background:#fff3cf;color:#a87500"><i class="bi bi-key-fill"></i></span>
                    <div>
                        <div class="fw-semibold">Update password</div>
                        <div class="small text-muted">Use a long, random password to stay secure.</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current password</label>
                            <input type="password" id="current_password" name="current_password"
                                autocomplete="current-password"
                                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">
                            @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                    class="form-control @error('password', 'updatePassword') is-invalid @enderror">
                                @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    autocomplete="new-password" class="form-control">
                            </div>
                        </div>
                        <button class="btn btn-primary">Update password</button>
                        @if (session('status') === 'password-updated')
                            <span class="saved-note ms-2"><i class="bi bi-check-circle-fill me-1"></i>Saved</span>
                        @endif
                    </form>
                </div>
            </div>

            {{-- delete account (staff only: an admin should not delete the last admin) --}}
            @unless ($user->isAdmin())
                <div class="card danger-zone">
                    <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="section-ico" style="background:#ffeceb;color:var(--bad)"><i
                                    class="bi bi-exclamation-triangle-fill"></i></span>
                            <div>
                                <div class="fw-semibold">Delete account</div>
                                <div class="small text-muted">This permanently removes your account.</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal">Delete my account</button>
                    </div>
                </div>
            @endunless
        </div>
    </div>

    @unless ($user->isAdmin())
        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
                <div class="modal-content">
                    <form method="POST" action="{{ route('profile.destroy') }}" class="modal-body p-4">
                        @csrf @method('DELETE')
                        <div class="confirm-icon"><i class="bi bi-trash3"></i></div>
                        <h5 class="text-center mb-1">Delete your account?</h5>
                        <p class="text-muted text-center small mb-3">This cannot be undone. Enter your password to confirm.</p>
                        <input type="password" name="password" placeholder="Password" autocomplete="current-password"
                            class="form-control mb-1 @error('password', 'userDeletion') is-invalid @enderror">
                        @error('password', 'userDeletion')
                        <div class="invalid-feedback d-block mb-2">{{ $message }}</div>@enderror
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-danger px-4">Delete account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if ($errors->userDeletion->isNotEmpty())
            <script>
                // re-open the modal when the password was wrong
                addEventListener('DOMContentLoaded', () => new bootstrap.Modal('#deleteAccountModal').show());
            </script>
        @endif
    @endunless
@endsection