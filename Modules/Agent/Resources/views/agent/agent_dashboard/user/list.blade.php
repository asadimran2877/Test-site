@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('content')
    <section class="min-vh-100" id="deleteUser">
        <div class="my-30">
            <div class="container-fluid">
                <!-- Page title start -->
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="page-title">{{ __('Users') }}</h3>
                    </div>

                    <div>
                        <a href="{{ url('agent/user/add') }}">
                            <button class="btn btn-primary px-4 py-2"><i class="fa fa-arrow-up"></i>&nbsp;{{ __('Users') }}</button>
                        </a>
                    </div>
                </div>
                <!-- Page title end-->

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                @include('user_dashboard.layouts.common.alert')
                                <div class="bg-secondary mt-3 shadow">
                                    <div class="table-responsive">
                                        @if ($list->count() > 0)
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Name') }}</th>
                                                        <th>{{ __('Phone') }}</th>
                                                        <th>{{ __('Email') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th>{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($list as $result)
                                                        <tr>
                                                            <td>
                                                                <p class="font-weight-600 text-16 mb-0">{{ $result->created_at->format('jS F') }}</p>
                                                                <p class="td-text">{{ $result->created_at->format('Y') }}</p>
                                                            </td>
                                                            <td>{{ $result->first_name .' '. $result->last_name }}</td>
                                                            <td>{{ !empty($result->phone) ? $result->formattedPhone : '-' }}</td>
                                                            <td>{{ $result->email }}</td>
                                                            <td>{!! getStatusBadge($result->status) !!}</td>
                                                            <td>
                                                                <a href="{{ url('agent/user/view/' . $result->id) }}" class="btn btn-light btn-sm"><i class="fa fa-eye"></i></a>
                                                                <a href="{{ url('agent/user/edit/' . $result->id) }}" class="btn btn-light btn-sm"><i class="far fa-edit"></i></a>
                                                                @if ($result->status == 'Inactive')
                                                                    <a class="btn btn-light btn-sm disabled"><i class="fa fa-trash"></i></a>
                                                                @else
                                                                    <a href="{{ url('agent/user/delete/' . $result->id) }}" class="btn btn-light btn-sm"><i class="fa fa-trash"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="p-5 text-center">
                                                <img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
                                                <p class="mt-4">{{ __('Sorry! Data Not Found !') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-4">{{ $list->links('vendor.pagination.bootstrap-4') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<script type="text/javascript" src="{{ asset('Modules/Agent/Resources/assets/js/agent/agent.min.js') }}"></script>
    
<script type="text/javascript">
    "use strict";
    var ajaxUrl = $('.delete-warning').attr('href');
</script>
@endsection
