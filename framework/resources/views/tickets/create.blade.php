@extends('layouts.admin')
@section('body-title')
    @lang('Tickets')
@endsection
@section('title')
    | @lang('Tickets')
@endsection
@section('breadcrumb')
<li class="active">Tickets</li>
@endsection

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery (necesario para Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                <h4 class="box-title"> Tickets </h4>
            </div>
            <div class="box-body ">
                @include ('errors.list')
                <form class="form" method="post" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="title"> Titulo </label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="manager_id"> Encargado </label>
                            <select name="manager_id" class="form-control">
                                <option value="">Encargado</option>
                                @if(isset($managers))
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>					
                        <div class="form-group col-md-6">
                            <label for="equipment_id">Equipo Clínico</label>
                            <select id="equipment_id" name="equipment_id" class="form-control select2">
                                <option value="">Equipo clínico</option>
                                @if(isset($equipments))
                                    @foreach ($equipments as $equipment)
                                        <option value="{{ $equipment->id }}" data-model="{{ $equipment->models->name ?? 'Sin modelo' }}" data-hospital="{{ $equipment->hospital->name ?? 'Sin hospital' }}" {{ old('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                            {{ $equipment->sr_no }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="model">Modelo</label>
                            <input type="text" id="model" name="model" class="form-control" value="{{ old('model') }}" disabled/>
                            <input type="hidden" id="model" name="model" value="{{ old('model', $equipment->models->name) }}" />

                        </div>
                        <div class="form-group col-md-6">
                            <label for="hospital">Hospital</label>
                            <input type="text" id="hospital" name="hospital" class="form-control" value="{{ old('hospital') }}" disabled />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="priority"> Prioridad </label>
                            <select name="priority" class="form-control">
                                <option value="">Prioridad</option>
                                <option value="1" {{ old('priority') == "1" ? 'selected' : '' }}>BAJA</option>
                                <option value="2" {{ old('priority') == "2" ? 'selected' : '' }}>MEDIA</option>
                                <option value="3" {{ old('priority') == "3" ? 'selected' : '' }}>ALTA</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="category"> Categoria </label>
                            <select name="category" class="form-control">
                                <option value="">Categoria</option>
                                <option value="MANTENIMIENTO PREVENTIVO (M)" {{ old('category') == "MANTENIMIENTO PREVENTIVO (M)" ? 'selected' : '' }}>MANTENIMIENTO PREVENTIVO (M)</option>
                                <option value="INSTALACIÓN (M)" {{ old('category') == "INSTALACIÓN (M)" ? 'selected' : '' }}>INSTALACIÓN (M)</option>
                                <option value="DESINSTALACIÓN (M)" {{ old('category') == "DESINSTALACIÓN (M)" ? 'selected' : '' }}>DESINSTALACIÓN (M)</option>
                                <option value="REACONDICIONAMIENTO (M)" {{ old('category') == "REACONDICIONAMIENTO (M)" ? 'selected' : '' }}>REACONDICIONAMIENTO (M)</option>
                                <option value="CAPACITACIÓN (M)" {{ old('category') == "CAPACITACIÓN (M)" ? 'selected' : '' }}>CAPACITACIÓN (M)</option>
                                <option value="VERIFICACIÓN (M)" {{ old('category') == "VERIFICACIÓN (M)" ? 'selected' : '' }}>VERIFICACIÓN (M)</option>
                                <option value="INCIDENCIA CORRECTIVA (I)" {{ old('category') == "INCIDENCIA CORRECTIVA (I)" ? 'selected' : '' }}>INCIDENCIA CORRECTIVA (I)</option>
                                <option value="ASESORIA (I)" {{ old('category') == "ASESORIA (I)" ? 'selected' : '' }}>ASESORIA (I)</option>
                                <option value="ABASTECIMIENTO (I)" {{ old('category') == "ABASTECIMIENTO (I)" ? 'selected' : '' }}>ABASTECIMIENTO (I)</option>
                                <option value="SISTEMAS TI (I)" {{ old('category') == "SISTEMAS TI (I)" ? 'selected' : '' }}>SISTEMAS TI (I)</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="contact"> Contacto </label>
                            <input type="email" name="contact" class="form-control" value="{{ old('contact') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone"> Telefono </label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="extension"> Extension </label>
                            <input type="text" name="extension" class="form-control" value="{{ old('extension') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="adress"> Direccion </label>
                            <input type="text" name="adress" class="form-control" value="{{ old('adress') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="failure"> Actividad </label>
                            <input type="text" name="failure" class="form-control" value="{{ old('failure') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="description"> @lang('equicare.description') </label>
                            <input type="text" name="description" class="form-control" value="{{ old('description') }}" />
                        </div>
                        <div class="form-group col-md-6">
                            <label for="images"> Imagen </label>
                            <input type="file" name="images" class="form-control" value="{{ old('images') }}" />
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

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Seleccione un equipo"
        });

        function updateFields() {
            var selectedOption = $('#equipment_id').find('option:selected');
            var model = selectedOption.data('model') || ''; // Default to empty string if no model found
            var hospital = selectedOption.data('hospital') || ''; // Default to empty string if no hospital found
            $('#model').val(model);
            $('#hospital').val(hospital);
        }

        // Update fields on page load if a selection is already made
        updateFields();

        // Update fields when the selection changes
        $('#equipment_id').on('change', function() {
            updateFields();
        });
    });
</script>
@endsection
