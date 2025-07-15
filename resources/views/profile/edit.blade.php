@extends('layouts.admin')

@section('title')
    Dashboard
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Account Settings</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Setting</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-lg-6">
                                        <form action="{{ route('profile.update', auth()->user()->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <p class="m-0"><strong>Profile Information</strong></p>
                                            <small>Update your accounts profile information.</small><br>
                                            <label class="mt-4 mb-0 form-label col-form-label-sm"
                                                for="name">Name</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="name" name="name"
                                                    aria-describedby="name" value="{{ $user->name }}" disabled>
                                            </div>
                                            <label class="mb-0 form-label col-form-label-sm" for="email">Email</label>
                                            <div class="input-group mb-3">
                                                <input type="email" class="form-control" id="email" name="email"
                                                    aria-describedby="email" value=" {{ $user->email }}" disabled>
                                            </div>
                                            <label class="mb-0 form-label col-form-label-sm" for="username">Username</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="username" name="username"
                                                    aria-describedby="username" value="{{ $user->username }}" disabled>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-dark text-xs">SAVE</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-lg-6">
                                        <form action="{{ route('profile.password', auth()->user()->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <p class="m-0"><strong>Update Password</strong></p>
                                            <small>Ensure your account is using a long, random password to stay
                                                secure.</small><br>
                                            <label class="mt-4 mb-0 form-label col-form-label-sm" for="old_password">Current
                                                Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="old_password"
                                                    name="old_password" aria-describedby="old_password">
                                            </div>
                                            <label class="mb-0 form-label col-form-label-sm" for="new_password">New
                                                Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="new_password"
                                                    name="new_password" aria-describedby="new_password">
                                            </div>
                                            <label class="mb-0 form-label col-form-label-sm" for="confirm_password">Confirm
                                                Password</label>
                                            <div class="input-group mb-3">
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="confirm_password" aria-describedby="confirm_password">
                                            </div>

                                            <button type="submit" class="btn btn-sm btn-dark text-xs">SAVE</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-lg-6">
                                        <p class="m-0"><strong>Delete Account</strong></p>
                                        <small>Once your account is deleted, all of its resources and data will be
                                            permanently deleted.</small><br>
                                        <small>Before deleting your account, please download any data or information that
                                            you wish to retain.</small><br>
                                        <button class="btn btn-xs btn-danger mt-4 text-xs"
                                            onclick="deleteAccount({{ $user->id }})">
                                            DELETE ACCOUNT</button>
                                        <form id="delete-form-{{ $user->id }}"
                                            action="{{ route('profile.destroy', $user->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function deleteAccount(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'error',
                showCancelButton: false,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }
    </script>
@endpush
