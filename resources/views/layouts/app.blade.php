<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Partnership Support</title>

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="{{ asset('favicons/favicon-48x48.png') }}" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
    </script>

    <!-- Sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Our style -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body>
    <main>
        @yield('content')
    </main>

    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

    <!-- Sweetalert2 -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast'
            },
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        })

        @if (session('pesan'))
            @switch(session('level-alert'))
                @case('alert-success')
                Toast.fire({
                    icon: 'success',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-danger')
                Toast.fire({
                    icon: 'error',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-warning')
                Toast.fire({
                    icon: 'warning',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-question')
                Toast.fire({
                    icon: 'question',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @default
                Toast.fire({
                    icon: 'info',
                    title: '{{ Session::get('pesan') }}'
                })
            @endswitch
        @endif
        @if (count($errors) > 0)
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: '{{ $error }}'
                })
            @endforeach
        @endif
    </script>

    @stack('scripts')
</body>

</html>
