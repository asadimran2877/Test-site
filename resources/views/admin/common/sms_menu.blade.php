<div class="box box-primary">

  {{-- normal template --}}
  <div class="box-header with-border">
    <h3 class="box-title underline">SMS Templates</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-21' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/21")}}">Identity/Address Verification</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-1' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/1")}}">Transferred Payments</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-2' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/2")}}">Received Payments</a>
      </li>


      <li {{ isset($list_menu) &&  $list_menu == 'menu-4' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/4")}}">Request Payment Creation</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-5' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/5")}}">Request Payment Acceptance</a>
      </li>
      @if(config('referral.is_active'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-33' ? 'class=active' : ''}} >
          <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/33")}}">Referral Award</a>
        </li>
      @endif

    </ul>
  </div>
</div>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title underline">SMS Templates of Admin actions</h3>
  </div>
  <div class="box-body no-padding" style="display: block;">
    <ul class="nav nav-pills nav-stacked">

      <li {{ isset($list_menu) &&  $list_menu == 'menu-14' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/14")}}">Merchant Payment</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-10' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/10")}}">Payout</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-6' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/6")}}">Transfers</a>
      </li>

      <li {{ isset($list_menu) &&  $list_menu == 'menu-8' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/8")}}">Request Payments (Success/Refund)</a>
      </li>


      <li {{ isset($list_menu) &&  $list_menu == 'menu-16' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/16")}}">Request Payments (Cancel/Pending)</a>
      </li>

      @if(isActive('CryptoExchange'))
        <li {{ isset($list_menu) &&  $list_menu == 'menu-36' ? 'class=active' : ''}} >
        <a href="{{ URL::to(\Config::get('adminPrefix')."/sms-template/36") }}">{{ __('Crypto Exhange Notification (Success/
          Cancel)') }}</a>
      </li>
      @endif

    </ul>
  </div>
  </div>
@if (isActive('Agent'))
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title underline">{{ __('Agent Notifications') }}</h3>
        </div>
        <div class="box-body no-padding" style="display: block;">
            <ul class="nav nav-pills nav-stacked">
                <li {{ isset($list_menu) && $list_menu == 'menu-23' ? 'class=active' : '' }}>
                    <a href="{{ URL::to(\Config::get('adminPrefix') . '/sms-template/23') }}">{{ __('User Deposit by Agent') }}</a>
                </li>
                <li {{ isset($list_menu) && $list_menu == 'menu-24' ? 'class=active' : '' }}>
                    <a href="{{ URL::to(\Config::get('adminPrefix') . '/sms-template/24') }}">{{ __('User Withdrawal by Agent') }}</a>
                </li>
            </ul>
        </div>
    </div>
@endif