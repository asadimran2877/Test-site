@extends('admin.layouts.master')
@section('title', 'Woocommerce Configure')

@section('page_content')
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-header with-border">
				<h3 class="box-title">{{ __('Generate Woocommerce Brand') }}</h3>
				@if (!empty($pluginName))
					<a href="{{ url('public/uploads/woocommerce').'/'.$pluginName }}" class="btn btn-primary btn-flat pull-right"><i class="fa fa-download"></i> {{ $pluginName }}</a>
				@endif
				</div>
				<form action="{{ route('addon.woocommerce.store') }}" method="post" id="WoocommerceConfigureForm" class="form-horizontal" enctype="multipart/form-data">
				@csrf
					<div class="box-body">

						<!-- Brand -->
						<div class="form-group">
							<label for="plugin_brand" class="col-sm-3 control-label">{{ __('Plugin Brand') }}</label>
							<div class="col-sm-6">
								<input type="text" name="plugin_brand" class="form-control" id="plugin_brand" value="{{ $pluginInfo->plugin_brand ?? '' }}" placeholder="Ex: Paymoney" maxlength="50" required>
								<span class="text-danger">{{ $errors->first('plugin_brand') }}</span>
							</div>
						</div> 

						<!-- Name -->
						<div class="form-group">
							<label for="plugin_name" class="col-sm-3 control-label">{{ __('Plugin Name') }}</label>
							<div class="col-sm-6">
								<input type="text" name="plugin_name" class="form-control" id="plugin_name" value="{{ $pluginInfo->plugin_name ?? '' }}" placeholder="Ex: PayMoney - WooCommerce Addon" maxlength="91" required>
								<span class="text-danger">{{ $errors->first('plugin_name') }}</span>
							</div>
						</div>

						<!-- Plugin URI -->
						<div class="form-group">
							<label for="plugin_uri" class="col-sm-3 control-label">{{ __('Plugin URI') }}</label>
							<div class="col-sm-6">
								<input type="url" name="plugin_uri" class="form-control" id="plugin_uri" value="{{ $pluginInfo->plugin_uri ?? '' }}" placeholder="Ex: https://plugin-uri.com" maxlength="191" required>
								<span class="text-danger">{{ $errors->first('plugin_uri') }}</span>
							</div>
						</div>
						
						<!-- Author -->
						<div class="form-group">
							<label for="plugin_author" class="col-sm-3 control-label">{{ __('Plugin Author') }}</label>
							<div class="col-sm-6">
								<input type="text" name="plugin_author" class="form-control" id="plugin_author" value="{{ $pluginInfo->plugin_author ?? '' }}" placeholder="Ex: Techvillage" maxlength="50" required>
								<span class="text-danger">{{ $errors->first('plugin_author') }}</span>
							</div>
						</div>
						
						<!-- Author URI -->
						<div class="form-group">
							<label for="plugin_author_uri" class="col-sm-3 control-label">{{ __('Plugin Author URI') }}</label>
							<div class="col-sm-6">
								<input type="url" name="plugin_author_uri" class="form-control" id="plugin_author_uri" value="{{ $pluginInfo->plugin_author_uri ?? '' }}" placeholder="Ex: https://author-uri.com" required>
								<span class="text-danger">{{ $errors->first('plugin_author_uri') }}</span>
							</div>
						</div>
						
						<!-- Description -->
						<div class="form-group">
							<label for="plugin_description" class="col-sm-3 control-label">{{ __('Plugin Description') }}</label>
							<div class="col-sm-6">
							<textarea name="plugin_description" rows="2" class="form-control" id="plugin_description">{{ isset($pluginInfo) ? $pluginInfo->plugin_description : '' }}</textarea>
							<span class="text-danger">{{ $errors->first('plugin_description') }}</span>
							</div>
						</div>

						<!-- Status -->
						<div class="form-group">
							<label for="inputEmail3" class="col-sm-3 control-label">{{ __('Publication status') }}</label>
							<div class="col-sm-6">
							<select class="form-control" name="publication_status" id="publication_status" required>
								<option value="" selected>{{ __('Select status') }}</option>
								<option value="Active" {{ !empty($publicationStatus) && $publicationStatus == 'Active' ? 'selected':"" }}>{{ __('Active') }}</option>
								<option value="Inactive" {{ !empty($publicationStatus) && $publicationStatus == 'Inactive' ? 'selected':"" }}>{{ __('Inactive') }}</option>
							</select>
							<span class="text-danger">{{ $errors->first('publication_status') }}</span>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<a class="btn btn-theme-danger" href="{{ url(\Config::get('adminPrefix').'/module-manager/addons') }}" id="users_cancel">
									{{ __('Cancel') }}
								</a>
								<button type="submit" class="btn btn-theme pull-right">
									<span id="users_edit_text">{{ __('Submit') }}</span>
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
