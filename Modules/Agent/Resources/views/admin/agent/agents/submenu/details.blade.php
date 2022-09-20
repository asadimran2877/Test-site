@extends('admin.layouts.master')

@section('title', __('View Agent'))

@section('head_style')
    <!-- sweetalert -->
    <link rel="stylesheet" href="{{ asset('public/backend/sweetalert/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('Modules/Agent/Resources/assets/css/details.min.css') }}">
@endsection

@section('page_content')
    <div class="box">
        <div class="panel-body ml-20">
            <ul class="nav nav-tabs cus" role="tablist">
                <li class="active">
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/details/' . $agent->id) }}">{{ __('Details') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/user/' . $agent->id) }}">{{ __('User List') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/deposit/' . $agent->id) }}">{{ __('Deposit List') }}</a>
                </li>
                <li>
                    <a href="{{ url(\Config::get('adminPrefix') . '/agents/payout/' . $agent->id) }}">{{ __('Withdrawal List') }}</a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="mt-5"></div>
        <div class="mt-5 col-md-12">
            <div class="row">
                <span class="sans-pro agent-pro-title ml-14">{{ __('Agents Profile') }}</span>
            </div>
            <div class="box mt-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex flex-row">
                            <div class="imgdiv mt-28 ml-28">
                                @if ($agent->picture)
                                    <img class="pic" src="{{ url('public/images/agents/profile/' . $agent->picture) }}">
                                @else
                                    <img class="pic" src="{{ asset('public/uploads/userPic/elipse.png') }}">
                                @endif
                                <div class="online"></div>
                            </div>
                            <div class="d-flex flex-column desmargin">
                                <span class="sans-pro p-16 twidth">{{ ($agent->first_name .' '.$agent->last_name) ?? '-' }}</span>
                                <span class="sans-pro p-12 mt-4">{{ $agent->email ?? '-'}}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-row ml-28 mt-12">
                            <div><span class="sans-pro p-12">{{ __('Phone') }}</span><span
                                    class="sans-pro p-12 dotpd">:</span></div>
                            <div> <span class="sans-pro p-12">{{ $agent->formattedPhone ?? '-' }}</span></div>
                        </div>
                        <div class="d-flex flex-row ml-28 mt-4">
                            <div>
                                <span class="sans-pro p-12">{{ __('Created') }}</span>
                                <span class="sans-pro p-12 dotmiddlepd">:</span>
                            </div>
                            <div><span class="sans-pro p-12">{{ dateFormat($agent->created_at) ?? '-' }}</span></div>
                        </div>
                        <div class="d-flex flex-row ml-28 mt-4">
                            <div><span class="sans-pro p-12">{{ __('Status') }}</span><span
                                    class="sans-pro p-12 dotpd">:</span></div>
                            <div class=""><span class="activebg sans-pro p-10">{{  $agent->status  }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-8 border-left mt-28 mb-28">
                        <div class="ml-60 boxsize t-bg d-flex flex-column">
                            <svg class="mt-19 ml-14" width="17" height="15" viewBox="0 0 17 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.81283 6.41667C5.66373 6.41667 6.47978 6.07865 7.08146 5.47697C7.68314 4.87529 8.02116 4.05924 8.02116 3.20833C8.02116 2.35743 7.68314 1.54138 7.08146 0.939699C6.47978 0.33802 5.66373 0 4.81283 0C3.96192 0 3.14587 0.33802 2.54419 0.939699C1.94251 1.54138 1.60449 2.35743 1.60449 3.20833C1.60449 4.05924 1.94251 4.87529 2.54419 5.47697C3.14587 6.07865 3.96192 6.41667 4.81283 6.41667ZM4.81283 4.8125C5.23828 4.8125 5.6463 4.64349 5.94714 4.34265C6.24798 4.04181 6.41699 3.63379 6.41699 3.20833C6.41699 2.78288 6.24798 2.37486 5.94714 2.07402C5.6463 1.77318 5.23828 1.60417 4.81283 1.60417C4.38737 1.60417 3.97935 1.77318 3.67851 2.07402C3.37767 2.37486 3.20866 2.78288 3.20866 3.20833C3.20866 3.63379 3.37767 4.04181 3.67851 4.34265C3.97935 4.64349 4.38737 4.8125 4.81283 4.8125Z"
                                    fill="#2C2D37" />
                                <path
                                    d="M7.21875 8.82292C7.43148 8.82292 7.63549 8.90742 7.78591 9.05784C7.93633 9.20826 8.02083 9.41227 8.02083 9.625V14.4375H9.625V9.625C9.625 8.98682 9.37148 8.37478 8.92023 7.92352C8.46897 7.47226 7.85693 7.21875 7.21875 7.21875H2.40625C1.76807 7.21875 1.15603 7.47226 0.704774 7.92352C0.253515 8.37478 0 8.98682 0 9.625V14.4375H1.60417V9.625C1.60417 9.41227 1.68867 9.20826 1.83909 9.05784C1.98951 8.90742 2.19352 8.82292 2.40625 8.82292H7.21875Z"
                                    fill="#2C2D37" />
                                <path
                                    d="M12.8337 3.20834H14.4378V4.81251H16.042V6.41668H14.4378V8.02084H12.8337V6.41668H11.2295V4.81251H12.8337V3.20834Z"
                                    fill="#2C2D37" />
                            </svg>
                            <span class="ml-14 mt-4 sans-pro pr-16 c-black">{{ __('Total Users') }}</span>
                            <span class="ml-14 mt-5 sans-pro p-24 c-blue">{{ $userCount }}</span>
                        </div>
                        <div class="ml-60 mt-12 d-flex flex-row">
                            <div class="boxsize a-bg d-flex flex-column">
                                <svg class="mt-19 ml-14" width="17" height="15" viewBox="0 0 17 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.81185 6.41667C5.66275 6.41667 6.4788 6.07865 7.08048 5.47697C7.68216 4.87529 8.02018 4.05924 8.02018 3.20833C8.02018 2.35743 7.68216 1.54138 7.08048 0.939699C6.4788 0.33802 5.66275 0 4.81185 0C3.96095 0 3.14489 0.33802 2.54321 0.939699C1.94154 1.54138 1.60352 2.35743 1.60352 3.20833C1.60352 4.05924 1.94154 4.87529 2.54321 5.47697C3.14489 6.07865 3.96095 6.41667 4.81185 6.41667V6.41667ZM4.81185 4.8125C5.2373 4.8125 5.64533 4.64349 5.94617 4.34265C6.24701 4.04181 6.41602 3.63378 6.41602 3.20833C6.41602 2.78288 6.24701 2.37486 5.94617 2.07402C5.64533 1.77318 5.2373 1.60417 4.81185 1.60417C4.3864 1.60417 3.97837 1.77318 3.67753 2.07402C3.37669 2.37486 3.20768 2.78288 3.20768 3.20833C3.20768 3.63378 3.37669 4.04181 3.67753 4.34265C3.97837 4.64349 4.3864 4.8125 4.81185 4.8125V4.8125Z"
                                        fill="#009651" />
                                    <path
                                        d="M7.21875 8.82292C7.43148 8.82292 7.63549 8.90742 7.78591 9.05784C7.93633 9.20826 8.02083 9.41227 8.02083 9.625V14.4375H9.625V9.625C9.625 8.98682 9.37149 8.37478 8.92023 7.92352C8.46897 7.47226 7.85693 7.21875 7.21875 7.21875H2.40625C1.76807 7.21875 1.15603 7.47226 0.704774 7.92352C0.253515 8.37478 0 8.98682 0 9.625V14.4375H1.60417V9.625C1.60417 9.41227 1.68867 9.20826 1.83909 9.05784C1.98951 8.90742 2.19352 8.82292 2.40625 8.82292H7.21875Z"
                                        fill="#009651" />
                                    <path
                                        d="M12.8327 3.20831H14.4368V4.81248H16.041V6.41665H14.4368V8.02081H12.8327V6.41665H11.2285V4.81248H12.8327V3.20831Z"
                                        fill="#009651" />
                                </svg>
                                <span class="ml-14 mt-4 sans-pro p-14 c-green">{{ __('Active Users') }}</span>
                                <span class="ml-14 mt-5 mblmt-20 sans-pro p-24 c-black">{{ $userActive }}</span>
                            </div>
                            <div class="ml-16 boxsize i-bg d-flex flex-column">
                                <svg class="mt-19 ml-14" width="17" height="15" viewBox="0 0 17 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.81185 6.41667C5.66275 6.41667 6.4788 6.07865 7.08048 5.47697C7.68216 4.87529 8.02018 4.05924 8.02018 3.20833C8.02018 2.35743 7.68216 1.54138 7.08048 0.939699C6.4788 0.33802 5.66275 0 4.81185 0C3.96095 0 3.14489 0.33802 2.54321 0.939699C1.94154 1.54138 1.60352 2.35743 1.60352 3.20833C1.60352 4.05924 1.94154 4.87529 2.54321 5.47697C3.14489 6.07865 3.96095 6.41667 4.81185 6.41667ZM4.81185 4.8125C5.2373 4.8125 5.64533 4.64349 5.94617 4.34265C6.24701 4.04181 6.41602 3.63379 6.41602 3.20833C6.41602 2.78288 6.24701 2.37486 5.94617 2.07402C5.64533 1.77318 5.2373 1.60417 4.81185 1.60417C4.3864 1.60417 3.97837 1.77318 3.67753 2.07402C3.37669 2.37486 3.20768 2.78288 3.20768 3.20833C3.20768 3.63379 3.37669 4.04181 3.67753 4.34265C3.97837 4.64349 4.3864 4.8125 4.81185 4.8125Z"
                                        fill="#DEA512" />
                                    <path
                                        d="M7.21875 8.82292C7.43148 8.82292 7.63549 8.90742 7.78591 9.05784C7.93633 9.20826 8.02083 9.41227 8.02083 9.625V14.4375H9.625V9.625C9.625 8.98682 9.37148 8.37478 8.92023 7.92352C8.46897 7.47226 7.85693 7.21875 7.21875 7.21875H2.40625C1.76807 7.21875 1.15603 7.47226 0.704774 7.92352C0.253515 8.37478 0 8.98682 0 9.625V14.4375H1.60417V9.625C1.60417 9.41227 1.68867 9.20826 1.83909 9.05784C1.98951 8.90742 2.19352 8.82292 2.40625 8.82292H7.21875Z"
                                        fill="#DEA512" />
                                    <path
                                        d="M12.8327 3.20831H14.4368V4.81248H16.041V6.41665H14.4368V8.02081H12.8327V6.41665H11.2285V4.81248H12.8327V3.20831Z"
                                        fill="#DEA512" />
                                </svg>

                                <span class="ml-14 mt-4 sans-pro p-14 c-yellow">{{ __('Inactive Users') }}</span>
                                <span class="ml-14 mt-5 sans-pro p-24 c-black">{{ $userInActive }}</span>
                            </div>
                            <div class="ml-16 boxsize s-bg d-flex flex-column">
                                <svg class="mt-19 ml-14" width="17" height="15" viewBox="0 0 17 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.81185 6.41667C5.66275 6.41667 6.4788 6.07865 7.08048 5.47697C7.68216 4.87529 8.02018 4.05924 8.02018 3.20833C8.02018 2.35743 7.68216 1.54138 7.08048 0.939699C6.4788 0.33802 5.66275 0 4.81185 0C3.96095 0 3.14489 0.33802 2.54321 0.939699C1.94154 1.54138 1.60352 2.35743 1.60352 3.20833C1.60352 4.05924 1.94154 4.87529 2.54321 5.47697C3.14489 6.07865 3.96095 6.41667 4.81185 6.41667ZM4.81185 4.8125C5.2373 4.8125 5.64533 4.64349 5.94617 4.34265C6.24701 4.04181 6.41602 3.63379 6.41602 3.20833C6.41602 2.78288 6.24701 2.37486 5.94617 2.07402C5.64533 1.77318 5.2373 1.60417 4.81185 1.60417C4.3864 1.60417 3.97837 1.77318 3.67753 2.07402C3.37669 2.37486 3.20768 2.78288 3.20768 3.20833C3.20768 3.63379 3.37669 4.04181 3.67753 4.34265C3.97837 4.64349 4.3864 4.8125 4.81185 4.8125Z"
                                        fill="#C8191C" />
                                    <path
                                        d="M7.21875 8.82292C7.43148 8.82292 7.63549 8.90742 7.78591 9.05784C7.93633 9.20826 8.02083 9.41227 8.02083 9.625V14.4375H9.625V9.625C9.625 8.98682 9.37148 8.37478 8.92023 7.92352C8.46897 7.47226 7.85693 7.21875 7.21875 7.21875H2.40625C1.76807 7.21875 1.15603 7.47226 0.704774 7.92352C0.253515 8.37478 0 8.98682 0 9.625V14.4375H1.60417V9.625C1.60417 9.41227 1.68867 9.20826 1.83909 9.05784C1.98951 8.90742 2.19352 8.82292 2.40625 8.82292H7.21875Z"
                                        fill="#C8191C" />
                                    <path
                                        d="M12.8327 3.20831H14.4368V4.81248H16.041V6.41665H14.4368V8.02081H12.8327V6.41665H11.2285V4.81248H12.8327V3.20831Z"
                                        fill="#C8191C" />
                                </svg>

                                <span class="ml-14 mt-4 sans-pro p-14 c-red">{{ __('Suspended Users') }}</span>
                                <span class="ml-14 mt-5 sans-pro p-24 c-black">{{ $userSuspended }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-12">
            <div class="box py-30">
                <div class="row">
                    <div class="col-xs-12">
                        <div> <span class="ml-28 sans-pro wallet-title">{{ __('Wallets') }}</span></div>
                    </div>
                </div>
                <div class="row mt-32">
                    <div class="col-md-12">
                        @foreach ($walletLists as $key => $wallet)
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 walletbox 
                        @php
                            $bg = array('e-bg', 'g-bg', 'a-bg');
                            $random_bg=array_rand($bg, 1);
                            echo $bg[$random_bg];
                        @endphp ml-28 mbmt-5">
                            <div class="ml1">
                                <div class="mt-19"><span>{{ $wallet['symbol'] }}</span><span class="sans-pro p-13 ml-4">{{ $key }}</span></div>

                                <div class="amount">
                                    <span class="sans-pro p-24 c-blue">{{ formatNumber($wallet['balance'], $wallet['currency_id']) }}</span>
                                </div>                                    
                                <div class="d-flex flex-row justify-content-between mt-11">
                                    <div class="amnt">
                                        <div class="dollar">
                                            <span class="sans-pro p-12-amount ">{{ formatNumber($wallet['deposit'], $wallet['currency_id']) }}</span>
                                        </div>
                                        <div class="dollarchild">
                                            <span
                                                class="wallets-amount-description p-10-amount sans-pro">{{ __('Deposit') }}</span>
                                            <svg width="5" height="8" viewBox="0 0 5 8" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M5 5.38752L4.24889 4.60262L3.03111 5.87519L3.03111 0.555007C3.03111 0.248485 2.79333 -2.63143e-08 2.5 -2.98122e-08C2.20668 -3.33101e-08 1.96889 0.248485 1.96889 0.555007L1.96889 5.87519L0.751105 4.60262L3.11535e-08 5.38752L2.5 8L5 5.38752Z"
                                                    fill="#009651" />
                                            </svg>
                                        </div>

                                    </div>
                                    <div class="amnt">
                                        <div class="dollar">
                                            <span class="sans-pro p-12-amount">{{ formatNumber($wallet['withdrawal'], $wallet['currency_id']) }}</span>
                                        </div>
                                        <div class="dollarchild">
                                            <span class="wallets-amount-description p-10-amount sans-pro">{{ __('Withdrawal') }}</span>
                                            <svg width="5" height="8" viewBox="0 0 5 8" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M-1.14195e-07 2.61248L0.751105 3.39738L1.96889 2.12481L1.96889 7.44499C1.96889 7.75151 2.20667 8 2.5 8C2.79332 8 3.03111 7.75152 3.03111 7.44499L3.03111 2.12481L4.2489 3.39738L5 2.61248L2.5 1.09278e-07L-1.14195e-07 2.61248Z"
                                                    fill="#C8191C" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="amnt">
                                        <div class="dollar">
                                            <span class="sans-pro p-12-amount">{{ formatNumber($wallet['revenue'], $wallet['currency_id']) }}</span>
                                        </div>
                                        <div class="dollarchild">
                                            <span class="wallets-amount-description p-10-amount sans-pro">{{ __('Revenue') }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 8"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" fill="blue"></path> </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
