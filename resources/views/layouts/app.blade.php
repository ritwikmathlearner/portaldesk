<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://use.fontawesome.com/releases/v5.13.1/css/all.css"
        integrity="sha384-xxzQGERXS00kBmZW/6qxqJPyxW3UR0BPsL4c8ILaIWXva5kFi7TxkIIaMiKtqV1Q"
        crossorigin="anonymous">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Notable&display=swap');

        #brand-name {
            font-family: 'Notable', sans-serif;
            letter-spacing: 0.10rem;
        }
    </style>
    @livewireStyles
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" id="brand-name" href="{{ url('/') }}">
                Portal Desk
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <form action="{{ route('tags.search') }}" method="GET" class="form-inline my-2 my-lg-0">
                            <livewire:task-search-bar/>
                        </form>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Dashboard</a>
                        </li>
                        @if(\Illuminate\Support\Facades\Auth::user()->isATutor())
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Task <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a href="{{ route('tasks.index') }}" class="dropdown-item" >Allocated tasks</a>
                                    <a href="{{ route('tasks.index').'?invited=true' }}" class="dropdown-item" >Invited tasks</a>
                                    <a href="{{ route('tasks.index').'?missed=true' }}" class="dropdown-item" >Missed deadlines</a>
                                    <a href="{{ route('tasks.index').'?escalated=true' }}" class="dropdown-item" >Escalated</a>
                                    <a href="{{ route('tasks.index').'?completed=true' }}" class="dropdown-item" >Completed</a>
                                    <a href="{{ route('tasks.index').'?failed=true' }}" class="dropdown-item" >Failed</a>
                                </div>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Task <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('tasks.index') }}">All Tasks</a>
                                    <a class="dropdown-item" href="{{ route('tasks.create') }}">Create</a>
                                </div>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="{{ route('tasks.index') }}"
                               role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-3">
        @if(session()->has('error'))
            <p class="bg-danger text-light p-1" id="flash-message">{{ session()->get('error') }}</p>
        @elseif(session()->has('success'))
            <p class="bg-success text-light p-1" id="flash-message">{{ session()->get('success') }}</p>
        @endif
    </div>
    <main class="py-4">
        @yield('content')
    </main>
</div>
@livewireScripts
<script>
    if (document.getElementById('flash-message')) {
        setTimeout(function () {
            document.getElementById('flash-message').style.display = 'none';
        }, 3000)
    }

    function deleteTask(e) {
        $delete = confirm("Delete this task?");
        if ($delete) {
            return true;
        }
        return false;
    }
</script>
</body>
</html>
