<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Platform</title>
    <link href="{{ mix('scss/app.css') }}" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Savanoriauk</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#">{{ __('app.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#">{{ __('app.events') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#">{{ __('app.profile') }}</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('app.language') }}
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">{{ __('app.english') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('lang.switch', 'lt') }}">{{ __('app.lithuanian') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div>
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
