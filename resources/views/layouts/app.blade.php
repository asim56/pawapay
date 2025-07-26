<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'pawaPay Payment') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- jQuery -->


</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">PawaPay</a>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

{{--<footer class="text-center py-4 text-muted mt-5">--}}
{{--    &copy; {{ date('Y') }} pawaPay Integration Demo--}}
{{--</footer>--}}
<div class="container">
    @yield('scripts')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    purple: {
                        600: '#6B46C1',
                        700: '#553C9A',
                    }
                }
            }
        }
    }
</script>
</body>
</html>
