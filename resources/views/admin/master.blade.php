@include('admin.master.head')
@include('admin.master.body.header')
@include('admin.master.body.main')
@include('admin.master.body.footer')

@section('admin.master')
<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('document-head')
    </head>
    <body>
        @yield('document-body-header')

        @yield('document-body-main')

        @yield('document-body-footer')
    </body>
</html>
@show
