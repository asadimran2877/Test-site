@extends('frontend.layouts.app')
@section('content')
<section>
    <div class="banner">
      <div class="container py-5">
          <div class="row py-5">
              <div class="col-md-12 py-5">
                  <div>
                      <h2 class="text-36">{{ __('Who is an Agent?') }}</h2>
                      <p class="text-20 text-gray-300">{{ __('Agent is a third party user, Who can deposit and withdraw money to user,
                      it enables a broader and more efficient ways to transact and maintain transactions.') }}</p>
                  </div>

                  <div class="mt-4">
                      <p class="head-sub-title">{{ __('Agent has an individual panel and can login into users  panel. After successfully login, Agent can see all the transaction that was made. 
                      Also can see the User list, Wallet Balance and manage their information settigns.
                      Agent platform – sitting in between different users, and merchants  acting as an intermediary to facilitate payments and 
                      Simplify the processes for transactions and  cash collection.') }}</p>
                  </div>

                    @if( !Auth::check() )
                        <div class="mt-5">
                            <a href="{{ url('/agent/login') }}">
                                <button class="btn btn-primary rounded">{{ __('Login as Agent') }}</button>
                            </a>
                        </div>
                    @endif
              </div>
          </div>
      </div>
  </div>
</section>
<!-- How it work section -->

<section class="mt-60 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="p-4">
                    <img src="{{ theme_asset('public/images/banner/bannerfour.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="text-28 title">{{ __('How does the Agent platform work?') }}</h2>
                <hr class="p-2" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Create Account') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Provide your credentials, create your own account and explore. Creating account is so easy.') }} </p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Send/Request Amount') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Send or request any amount to your preferred one within seconds. Just search the desired one and send or request for money.') }} </p>
                    </div>
                </div>


                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Select Payment Method') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Providing you multiple options to pay according to your desired payment method such as PayPal, Paystack, Stripe, CoinPayments and many more.') }}</p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Confirmation') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('After all the steps done above just confirm with your preference and that\'s it. Welcome to successfull transaction.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
