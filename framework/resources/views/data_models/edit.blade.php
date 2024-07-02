@extends('layouts.admin')
@section('body-title')
    @lang('equicare.models')
@endsection
@section('title')
	| @lang('equicare.models')
@endsection
@section('breadcrumb')
<li class="active">Modelos</li>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">

                <div class="box-header with-border">
                    <h4 class="box-title">
                        @lang('equicare.edit_datamodel')
                    </h4>
                </div>
                <div class="box-body">
					@include ('errors.list')
                    <form class="form" method="post" action="{{ route('models.update',$model->id) }}">
						{{ csrf_field() }}
						<input type="hidden" name="_method" value="PATCH"/>
						<div class="row">
						<div class="form-group col-md-6">
							<label for="name"> @lang('equicare.name') </label>
							<input type="text" name="name" class="form-control"
							value="{{ $model->name }}" />
						</div>
						
						<div class="form-group col-md-6">
							<label for="brand_id"> @lang('equicare.brand_id') </label>
							<select name="brand_id" class="form-control">
								<option value="" {{ old('marca_id') ? 'Selecciona un marca' : 'selected' }}></option>
								@if(isset($marcas))
									@foreach ($marcas as $marca)
										<option value="{{ $marca->id }}"
											{{ $model->brand_id == $marca->id ? 'selected' : '' }}
											>{{ $marca->name }}</option>
									@endforeach
								@endif
							
							</select>
						</div>
							
						<div class="form-group col-md-6">
							<label for="description"> @lang('equicare.description') </label>
							<input type="text" name="description" class="form-control"
							value="{{ $model->description }}" />
						</div>
						<br/>
						<div class="form-group col-md-12">
							<input type="submit" value="@lang('equicare.submit')" class="btn btn-primary btn-flat"/>
						</div>
						</div>
					</form>
        </div>
    </div>
@endsection