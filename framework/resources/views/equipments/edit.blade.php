@extends('layouts.admin')
@section('body-title')
	@lang('equicare.equipments')
@endsection
@section('title')
	| @lang('equicare.equipments')
@endsection
@section('breadcrumb')
<li><a href="{{ url('admin/equipments') }}">@lang('equicare.equipments') </a></li>
<li class="active">@lang('equicare.edit')</li>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary ">
			<div class="box-header with-border">
				<h4 class="box-title">@lang('equicare.edit_equipments')</h4>
				</div>
				<div class="box-body ">
					@include ('errors.list')
					<form class="form" method="post" action="{{ route('equipments.update',$equipment->id) }}">
						{{ csrf_field() }}
						{{ method_field('PATCH') }}
						<div class="row">
						<div class="form-group col-md-6">
							<label for="name"> @lang('equicare.name') </label>
							<input type="text" name="name" class="form-control"
							value="{{ $equipment->name }}" />
						</div>
						<div class="form-group col-md-6">
							<label for="short_name"> @lang('equicare.short_name_eq') </label>
							<input type="text" name="short_name" class="form-control"
							value="{{ $equipment->short_name }}" />
						</div>
						<div class="form-group col-md-6">
							<label for="company"> @lang('equicare.company') </label>
							<input type="text" name="company" class="form-control"
							value="{{ $equipment->company }}" />
						</div>
						<div class="form-group col-md-6">
							<label for="model"> @lang('equicare.model') </label>
							<input type="text" name="model" class="form-control"
							value="{{ $equipment->model }}" />
						</div>
						<div class="form-group col-md-6">
							<label for="sr_no"> @lang('equicare.serial_number') </label>
							<input type="text" name="sr_no" class="form-control"
							value="{{ old('sr_no')??$equipment->sr_no }}" />
						</div>
						<div class="form-group col-md-6">
							<label for="hospital_id"> @lang('equicare.hospital') </label>
							<select name="hospital_id" class="form-control">
								<option value="">---select---</option>
								@if(isset($hospitals))
									@foreach ($hospitals as $hospital)
										<option value="{{ $hospital->id }}"
											{{ $hospital->id==$equipment->hospital_id?'selected':''}}
											>{{ $hospital->name }}
										</option>
									@endforeach
								@endif
							</select>
						</div>

						<div class="form-group col-md-6">
							<label for="department"> @lang('equicare.department') </label>
							{!! Form::select('department',$departments??[],$equipment->department??null,['class'=>'form-control','placeholder'=>'--select--']) !!}
						</div>
						<div class="form-group col-md-6">
							<label for="date_of_purchase"> @lang('equicare.purchase_date') </label>
							
							<div class="input-group">
{{-- @dd($equipment->date_of_purchase,env('date_settings')) --}}
								<input type="text" id="date_of_purchase" name="date_of_purchase" class="form-control"			
								value="{{date_change($equipment->date_of_purchase)}}" />
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="order_date"> @lang('equicare.order_date') </label>
							<div class="input-group">

							<input type="text" id="order_date" name="order_date" class="form-control"
							value="{{ date_change($equipment->order_date) }}" />
							<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
							</span>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="date_of_installation"> @lang('equicare.installation_date') </label>
							<div class="input-group">

							<input type="text" id="date_of_installation" name="date_of_installation" class="form-control"
							value="{{ date_change($equipment->date_of_installation) }}" />
							<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
							</span>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="warranty_due_date"> @lang('equicare.warranty_due_date') </label>
							<div class="input-group">

							<input type="text" id="warranty_due_date" name="warranty_due_date" class="form-control"
							value="{{ date_change($equipment->warranty_due_date) }}" />
							<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
							</span>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label for="service_engineer_no"> @lang('equicare.service_engineer_number') </label>
							<input type="text" name="service_engineer_no" class="form-control"
							value="{{ $equipment->service_engineer_no }}" />
						</div>
						<div class="form-group col-md-6">
							<label> @lang('equicare.critical') </label><br/>
							<label>
							<input type="radio" value="1" name="is_critical" {{ $equipment->is_critical==1? 'checked':'' }}>
							@lang('equicare.yes')	</label> &nbsp;
							<label>
							<input type="radio" value="0" name="is_critical" {{ $equipment->is_critical==0? 'checked':''  }}>
							@lang('equicare.no')
							</label>
						</div>
						<div class="form-group col-md-6">
							<label for="notes"> @lang('equicare.notes') </label>
							<textarea rows="2" name="notes" class="form-control"
							>{{ $equipment->notes }}</textarea>
						</div>
						<div class="form-group col-md-12">
							<input type="submit" value="@lang('equicare.submit')" class="btn btn-primary btn-flat"/>
						</div>
						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#date_of_purchase').datepicker({
				format:"{{env('date_settings')=='' ? 'yyyy-mm-dd' : env('date_settings')}}",
				'todayHighlight' : true,
			});
			$('#order_date').datepicker({
				format:"{{env('date_settings')=='' ? 'yyyy-mm-dd' : env('date_settings')}}",
				'todayHighlight' : true,
			});
			$('#date_of_installation').datepicker({
				format:"{{env('date_settings')=='' ? 'yyyy-mm-dd' : env('date_settings')}}",
				'todayHighlight' : true,
			});
			$('#warranty_due_date').datepicker({
				format:"{{env('date_settings')=='' ? 'yyyy-mm-dd' : env('date_settings')}}",
				'todayHighlight' : true,
			});
		});
	</script>
@endsection