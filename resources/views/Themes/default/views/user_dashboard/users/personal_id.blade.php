@extends('user_dashboard.layouts.app')

@section('css')
<style>
    @media only screen and (max-width: 508px) {
        .chart-list ul li.active a {
            padding-bottom: 0px !important;
        }

    }
</style>
@endsection

@section('content')
    <!-- personal_id -->
    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    @include('user_dashboard.layouts.common.alert')

                    <div class="card">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li><a href="{{url('/profile')}}">@lang('message.dashboard.setting.title')</a></li>
                                    @if ($two_step_verification != 'disabled')
                                        <li><a href="{{url('/profile/2fa')}}">@lang('message.2sa.title-short-text')</a></li>
                                    @endif
                                    <li class="active"><a href="{{url('/profile/personal-id')}}">@lang('message.personal-id.title')
                                        @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>{{ __('Verified') }}</span>) @endif
                                        </a>
                                    </li>
                                    <li><a href="{{url('/profile/personal-address')}}">@lang('message.personal-address.title')
                                        @if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>{{ __('Verified') }}</span>) @endif
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <!-- form -->
                                    <form action="{{ url('profile/personal-id-update') }}" method="POST" class="form-horizontal" id="personal_id" enctype="multipart/form-data">
                                        {{ csrf_field() }}

                                        <input type="hidden" value="{{$user->id}}" name="user_id" id="user_id" />

                                        <input type="hidden" value="{{ isset($documentVerification->file_id) ? $documentVerification->file_id : '' }}" name="existingIdentityFileID" id="existingIdentityFileID" />

                                        <!-- Identity Type -->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="identity_type">@lang('message.personal-id.identity-type')</label>
                                                <select name="identity_type" id="identity_type" class="form-control">
                                                    <option value="">@lang('message.personal-id.select-type')</option>
                                                    <option value="driving_license"
                                                    {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'driving_license' ? 'selected' : '' }}>
                                                        @lang('message.personal-id.driving-license')
                                                    </option>
                                                    <option value="passport" {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'passport' ? 'selected' : '' }}>@lang('message.personal-id.passport')</option>
                                                    <option value="national_id" {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'national_id' ? 'selected' : '' }}>@lang('message.personal-id.national-id')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <!-- Identity Number -->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="identity_number">@lang('message.personal-id.identity-number')</label>
                                                <input type="text" name="identity_number" class="form-control" value="{{ !empty($documentVerification->identity_number) ? $documentVerification->identity_number : '' }}">
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <!-- Upload Attached file -->
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label for="identity_file">@lang('message.personal-id.upload-identity-proof')</label>
                                                <input type="file" name="identity_file" class="form-control input-file-field">
                                            </div>
                                        </div>

                                        <!-- Attached file -->
                                        @if (!empty($documentVerification->file))
                                            <h5>
                                                <a class="text-info" href="{{ url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename }}"><i class="fa fa-download"></i>
                                                    {{ $documentVerification->file->originalname }}
                                                </a>
                                            </h5>
                                            <br>
                                        @endif

                                        <div class="row">
                                            <div class="form-group col-md-12 mt-4">
                                                <button type="submit" class="btn btn-grad col-12" id="personal_id_submit">
                                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="personal_id_submit_text">@lang('message.dashboard.button.submit')</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- /form -->
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
        number: "{{ __('Please enter a valid number.') }}",
    })

    $('#personal_id').validate({
        rules: {
            identity_type: {
                required: true,
            },
            identity_number: {
                required: true,
            },
            identity_file: {
                required: true,
                extension: "pdf|png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
            identity_file: {
                extension: "{{ __('Please select (pdf, png, jpg, jpeg, gif or bmp) file!') }}"
            }
        },
        submitHandler: function(form)
        {
            $("#personal_id_submit").attr("disabled", true);
            $(".spinner").show();
            $("#personal_id_submit_text").text("{{ __('Submitting...') }}");
            form.submit();
        }
    });
</script>
@endsection
