<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta https-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Vuejs</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

   <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}">
</head>
<body>
    <section id="app" class="container">
        <app></app>
    </section>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
