@extends('layouts.admin')

@section('title')
    Home
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Home</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <a href="{{ route('project.index') }}">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $projects }}</h3>

                                <p>Project</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-solid fa-paste"></i>
                            </div>
                            <span class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></span>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="{{ route('application.index') }}">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $applications }}</h3>

                                <p>Application</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-solid fa-file-invoice"></i>
                            </div>
                            <span class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></span>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-6">
                    <a href="{{ route('document.index') }}">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $documents }}</h3>

                                <p>Document</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-regular fa-folder-open"></i>
                            </div>
                            <span class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Modal -->
    <div id="extensionModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Your Personal Data</h5>
                </div>
                <form action="{{ route('user-data.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row w-100">
                            <div class="col-12 col-md-6">
                                <label for="nik" class="mb-0 form-label col-form-label-sm">NIK</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                    name="nik" value="{{ old('nik') }}" placeholder="Enter NIK" required>

                                @error('nik')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="npwp" class="mb-0 form-label col-form-label-sm">NPWP</label>
                                <input type="text" class="form-control @error('npwp') is-invalid @enderror"
                                    id="npwp" name="npwp" value="{{ old('npwp') }}" placeholder="Enter NPWP"
                                    required>

                                @error('npwp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="mb-0 form-label col-form-label-sm">Phone</label>
                                <input type="number" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="Enter phone number" required>

                                @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="address" class="mb-0 form-label col-form-label-sm">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                    id="address" name="address" value="{{ old('address') }}" placeholder="Enter address"
                                    required>

                                @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="religion" class="mb-0 form-label col-form-label-sm">Religion</label>
                                <select class="form-control religion" style="width: 100%;" id="religion" name="religion"
                                    required>
                                    <option></option>
                                    <option value="Protestant" {{ old('religion') == 'Protestant' ? 'selected' : '' }}>
                                        Protestant
                                    </option>
                                    <option value="Catholic" {{ old('religion') == 'Catholic' ? 'selected' : '' }}>
                                        Catholic
                                    </option>
                                    <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>
                                        Islam
                                    </option>
                                    <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>
                                        Buddha
                                    </option>
                                    <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>
                                        Hindu
                                    </option>
                                    <option value="Confucianism"
                                        {{ old('religion') == 'Confucianism' ? 'selected' : '' }}>
                                        Confucianism
                                    </option>
                                </select>
                                @error('religion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="hobby" class="mb-0 form-label col-form-label-sm">Hobby</label>
                                <input type="text" class="form-control @error('hobby') is-invalid @enderror"
                                    id="hobby" name="hobby" value="{{ old('hobby') }}" placeholder="Enter hobby"
                                    required>

                                @error('hobby')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="pob" class="mb-0 form-label col-form-label-sm">Place of Birth</label>
                                <input type="text" class="form-control @error('pob') is-invalid @enderror"
                                    id="pob" name="pob" value="{{ old('pob') }}"
                                    placeholder="Enter place of birth" required>

                                @error('pob')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="dob" class="mb-0 form-label col-form-label-sm">Date of Birth</label>
                                <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                    id="dob" name="dob" value="{{ old('dob') }}" required>
                                @error('dob')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="disease" class="mb-0 form-label col-form-label-sm">Congenital Disease</label>
                                <input type="text" class="form-control @error('disease') is-invalid @enderror"
                                    id="disease" name="disease" value="{{ old('disease') }}"
                                    placeholder="Enter your congenital disease" required>

                                @error('disease')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="marriage" class="mb-0 form-label col-form-label-sm">Marriage Status</label>
                                <select class="form-control marriage" style="width: 100%;" id="marriage"
                                    name="marriage" required>
                                    <option></option>
                                    <option value="married" {{ old('marriage') == 'married' ? 'selected' : '' }}>
                                        Married
                                    </option>
                                    <option value="not married yet"
                                        {{ old('marriage') == 'not married yet' ? 'selected' : '' }}>
                                        Not married yet
                                    </option>
                                    <option value="widow" {{ old('marriage') == 'widow' ? 'selected' : '' }}>
                                        Widow
                                    </option>
                                </select>
                                @error('marriage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="gender" class="mb-0 form-label col-form-label-sm">Gender</label>
                                <div class="row pl-2">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gender1"
                                                value="female" required>
                                            <label class="form-check-label ml-3" for="gender1">
                                                Female
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gender2"
                                                value="male">
                                            <label class="form-check-label ml-3" for="gender2">
                                                Male
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="language" class="mb-0 form-label col-form-label-sm">Language Skills</label>
                                <select class="form-control language" style="width: 100%;" id="language"
                                    name="language" required>
                                    <option></option>
                                    <option value="active" {{ old('language') == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="passive" {{ old('language') == 'passive' ? 'selected' : '' }}>
                                        Passive
                                    </option>
                                </select>
                                @error('language')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 text-center">
                                <hr>
                                <p class="m-0 p-0"><strong>Education</strong></p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="elementary" class="mb-0 form-label col-form-label-sm">Elementary
                                    School</label>
                                <input type="text" class="form-control @error('elementary') is-invalid @enderror"
                                    id="elementary" name="elementary" value="{{ old('elementary') }}"
                                    placeholder="Enter your elementary school" required>

                                @error('elementary')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="junior_high" class="mb-0 form-label col-form-label-sm">Junior High
                                    School</label>
                                <input type="text" class="form-control @error('junior_high') is-invalid @enderror"
                                    id="junior_high" name="junior_high" value="{{ old('junior_high') }}"
                                    placeholder="Enter your junior high school" required>

                                @error('junior_high')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="senior_high" class="mb-0 form-label col-form-label-sm">Senior High
                                    School</label>
                                <input type="text" class="form-control @error('senior_high') is-invalid @enderror"
                                    id="senior_high" name="senior_high" value="{{ old('senior_high') }}"
                                    placeholder="Enter your senior high school" required>

                                @error('senior_high')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="college" class="mb-0 form-label col-form-label-sm">College</label>
                                <input type="text" class="form-control @error('college') is-invalid @enderror"
                                    id="college" name="college" value="{{ old('college') }}"
                                    placeholder="Enter your college university" required>

                                @error('college')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 text-center">
                                <hr>
                                <p><strong>Account</strong></p>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="bank" class="mb-0 form-label col-form-label-sm">Bank</label>
                                <input type="text" class="form-control @error('bank') is-invalid @enderror"
                                    id="bank" name="bank" value="{{ old('bank') }}"
                                    placeholder="Enter bank name" required
                                    oninput="this.value = this.value.toUpperCase()">

                                @error('bank')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="account" class="mb-0 form-label col-form-label-sm">Account Number</label>
                                <input type="text" class="form-control @error('account') is-invalid @enderror"
                                    id="account" name="account" value="{{ old('account') }}"
                                    placeholder="Enter account number" required>

                                @error('account')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.religion').select2({
                placeholder: "Select religion",
                allowClear: true,
            })
            $('.marriage').select2({
                placeholder: "Select marriage status",
                allowClear: true,
            })
            $('.language').select2({
                placeholder: "Select language skill",
                allowClear: true,
            })
        })

        // Fungsi untuk mengecek extension
        function checkUserExtension(userId) {
            fetch(`/check-user-extension/${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.hasExtension) {
                        // Tampilkan modal dan buat tidak bisa ditutup
                        const modal = new bootstrap.Modal(document.getElementById('extensionModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Panggil fungsi ketika halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const userId = 1; // ID user yang ingin dicek
            checkUserExtension(userId);
        });
    </script>
@endpush
