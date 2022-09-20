@extends('agent::agent.agent_dashboard.layouts.app')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('Modules/Agent/Resources/assets/css/agent.css') }}">
@endsection

@section('content')
    <section class="min-vh-100">
        <div class="my-30">
            <div class="container-fluid">
                <div>
                    <h3 class="page-title">{{ __('Users') }}</h3>
                </div>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <!-- Sub title start -->
                        <div class="mt-5">
                            <h3 class="sub-title">{{ __('User Details') }}</h3>
                            <p class="text-gray-500 text-16">{{ __('See the details of this User.') }}</p>
                        </div>
                        <!-- Sub title end-->
                    </div>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-xl-10">
                                <div class="bg-secondary rounded mt-5 shadow p-35">
                                    <div>
                                        <div class="mt-2">
                                            <p class="sub-title">{{ __('User Details') }}</p>
                                        </div>

                                        <div>
                                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                                <div>
                                                    <p>{{ __('First Name') }}</p>
                                                </div>

                                                <div class="pl-2">
                                                    <p>{{ $user->first_name }}</p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                                <div>
                                                    <p>{{ __('Last Name') }}</p>
                                                </div>

                                                <div class="pl-2">
                                                    <p>{{ $user->last_name }}</p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                                <div>
                                                    <p>{{ __('Phone') }}</p>
                                                </div>

                                                <div class="pl-2">
                                                    <p>{{ !empty($user->formattedPhone) ? $user->formattedPhone : '-' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                                <div>
                                                    <p>{{ __('Email') }}</p>
                                                </div>

                                                <div class="pl-2">
                                                    <p>{{ $user->email }}</p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                                <div>
                                                    <p>{{ __('Status') }}</p>
                                                </div>

                                                <div class="pl-2">
                                                    <p>{!! getStatusBadge($user->status) !!}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-0 row justify-content-between mt-2 p-3">
                                            <div>
                                                <a href="{{ url('agent/user') }}" class="btn btn-danger px-4 py-2 agent-color-white">{{ __('Cancel') }}</a>
                                            </div>
                                            <div>
                                                <a href="{{ url('agent/user/edit/' . $user->id) }}" class="btn btn-primary px-4 py-2" id="user_edit">
                                                    <i class="spinner fa fa-spinner fa-spin display-none"></i>
                                                    <span id="user_edit_text">{{ __('Edit') }}</span>
                                                </a>
                                            </div>
                                        </div>
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
