@php
    $logo = settings('logo');
    $companyName = settings('name');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ !isset($exception) ? meta(Route::current()->uri(), 'description') : $exception->description }}">
    <meta name="keywords" content="{{ !isset($exception) ? meta(Route::current()->uri(), 'keyword') : $exception->keyword }}">
    <title>{{ !isset($exception) ? meta(Route::current()->uri(), 'title') : $exception->title }} <?= isset($additionalTitle) ? '| ' . $additionalTitle : '' ?></title>

    <!--css styles-->
    @include('agent::agent.agent_dashboard.layouts.common.style')
    <!---title logo icon-->
    <link rel="javascript" href="{{ theme_asset('public/js/respond.js') }}">

    <!---favicon-->
    @if (!empty(settings('favicon')))
        <link rel="shortcut icon" href="{{ theme_asset('public/images/logos/' . settings('favicon')) }}" />
    @endif

    <script type="text/javascript">
        const themeMode = localStorage.getItem('theme');
        if (themeMode === "dark") {
            document.documentElement.setAttribute('class', 'dark');
        }
        var SITE_URL = "{{ url('/') }}";
    </script>
</head>

<body>
    <div id="scroll-top-area">
        <a href="{{ url()->current() }}#top-header"><i class="ti-angle-double-up" aria-hidden="true"></i></a>
    </div>
    <!-- Navbar section start -->
    <div>
        <nav class="navbar border-bottom py-2 nav-left navbar-toggleable-sm navbar-light bg-faded fixed-top">
            @include('agent::agent.agent_dashboard.layouts.common.navbar')
        </nav>
    </div>
    <!-- Navbar section end -->
    <div class="container-fluid">
        <!-- Sidebar section start-->
        <div class="sidebar pl-1 min-vh-100 d-none  d-lg-block" id="sidecol">
            @include('agent::agent.agent_dashboard.layouts.common.sidebar')
        </div>
        <!-- Sidebar section end -->

        <!-- Main content section start-->
        <div class="main-content">
            @yield('content')
            <!--Footer start-->
            <footer>
                <div class="p-3 text-center border-top">
                    <p class="copyright">{{ __('Copyright') }}&nbsp;© {{ date('Y') }} &nbsp;&nbsp;{{ $companyName }} | {{ __('All Rights Reserved') }}</p>
                </div>
            </footer>
            <!--Footer end-->
        </div>
        <!-- Main content section end -->

    </div>
    @include('agent::agent.agent_dashboard.layouts.common.script')
    @yield('js')
</body>

</html>
