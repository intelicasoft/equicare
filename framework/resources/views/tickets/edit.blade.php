@extends('layouts.admin')
@section('body-title')
    Tickets
@endsection
@section('title')
    | Tickets
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

            <div class="box-header with-border">
                <h4 class="box-title">
                    Editar tickets
                </h4>
            </div>
            <div class="box-body">
                @include ('errors.list')
                <form class="form" method="post" action="{{ route('tickets.update', $ticket->id) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('PATCH') }}
                    <div class="row">

                    <div class="form-group col-md-6">
                        <label for="number_id"> Numero de ticket </label>
                        <input type="number" name="number_id" class="form-control"
                        value="{{ $ticket->number_id }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="title"> Titulo </label>
                        <input type="text" name="title" class="form-control"
                        value="{{ $ticket->title }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="manager_id"> Encargado </label>
                        <select name="manager_id" class="form-control">
                            <option value="">Encargado</option>
                            @if(isset($managers))
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager->id }}"
                                        {{ $ticket->manager_id == $manager->id ? 'selected' : '' }}
                                        >{{ $manager->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

					<div class="form-group col-md-6">
                        <label for="priority"> Prioridad </label>
                        <select name="priority" class="form-control">
                            <option value="">Prioridad</option>
                            <option value="1" {{ $ticket->priority == 1 ? 'selected' : '' }}>Baja</option>
                            <option value="2" {{ $ticket->priority == 2 ? 'selected' : '' }}>Media</option>
                            <option value="3" {{ $ticket->priority == 3 ? 'selected' : '' }}>Alta</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="equipment_id"> Equipo Clinico </label>
                        <select id="equipment_id" name="equipment_id" class="form-control select2">
                            <option value="">Equipo clinico</option>
                            @if(isset($equipments))
                                @foreach ($equipments as $equipment)
                                    <option value="{{ $equipment->id }}"
                                        data-model="{{ $equipment->models->name ?? "Sin modelo"}}"
                                        data-hospital="{{ $equipment->hospital->name ?? 'Sin hospital' }}" 
                                        {{ $ticket->equipment_id == $equipment->id ? 'selected' : '' }}
                                        >{{ $equipment->sr_no }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="model">Modelo</label>
                        <input type="text" id="model" name="model" class="form-control" value="{{ old('model', $equipment->models->name) }}" disabled />
                        <input type="hidden" id="model" name="model" value="{{ old('model', $equipment->models->name) }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="hospital">Hospital</label>
                        <input type="text" id="hospital" name="hospital" class="form-control" value="{{ old('hospital') }}" disabled />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="category"> Categoria </label>
                        <select name="category" class="form-control">
                            <option value="">Categoria</option>
                            <option value="MANTENIMIENTO PREVENTIVO (M)" {{ $ticket->category == "MANTENIMIENTO PREVENTIVO (M)" ? 'selected' : '' }}>MANTENIMIENTO PREVENTIVO (M)</option>
                            <option value="INSTALACIÓN (M)" {{ $ticket->category == "INSTALACIÓN (M)" ? 'selected' : '' }}>INSTALACIÓN (M)</option>
                            <option value="DESINSTALACIÓN (M)" {{ $ticket->category == "DESINSTALACIÓN (M)" ? 'selected' : '' }}>DESINSTALACIÓN (M)</option>
                            <option value="REACONDICIONAMIENTO (M)" {{ $ticket->category == "REACONDICIONAMIENTO (M)" ? 'selected' : '' }}>REACONDICIONAMIENTO (M)</option>
                            <option value="CAPACITACIÓN (M)" {{ $ticket->category == "CAPACITACIÓN (M)" ? 'selected' : '' }}>CAPACITACIÓN (M)</option>
                            <option value="VERIFICACIÓN (M)" {{ $ticket->category == "VERIFICACIÓN (M)" ? 'selected' : '' }}>VERIFICACIÓN (M)</option>
                            <option value="INCIDENCIA CORRECTIVA (I)" {{ $ticket->category == "INCIDENCIA CORRECTIVA (I)" ? 'selected' : '' }}>INCIDENCIA CORRECTIVA (I)</option>
                            <option value="ASESORIA (I)" {{ $ticket->category == "ASESORIA (I)" ? 'selected' : '' }}>ASESORIA (I)</option>
                            <option value="ABASTECIMIENTO (I)" {{ $ticket->category == "ABASTECIMIENTO (I)" ? 'selected' : '' }}>ABASTECIMIENTO (I)</option>
                            <option value="SISTEMAS TI (I)" {{ $ticket->category == "SISTEMAS TI (I)" ? 'selected' : '' }}>SISTEMAS TI (I)</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="contact"> Contacto </label>
                        <input type="email" name="contact" class="form-control"
                        value="{{ $ticket->contact }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="phone"> Telefono </label>
                        <input type="text" name="phone" class="form-control"
                        value="{{ $ticket->phone }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="extension"> Extension </label>
                        <input type="text" name="extension" class="form-control"
                        value="{{ $ticket->extension }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="adress"> Direccion </label>
                        <input type="text" name="adress" class="form-control"
                        value="{{ $ticket->adress }}" />
                    </div>

                    <div class="form-group col-md-6">
                        <label for="failure"> Actividad </label>
                        <input type="text" name="failure" class="form-control"
                        value="{{ $ticket->failure }}" />
                    </div>

                    <div class="form-group col-md-12">
                        <label for="description"> Descripcion </label>
                        <textarea name="description" class="form-control">{{ $ticket->description }}</textarea>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="images"> Imagen </label>
                        <input type="file" name="images" class="form-control" />
                        @if($ticket->images)
                            <img src="{{ asset('framework/public/images/' . $ticket->images) }}" alt="Current Image" style="max-width: 100px; max-height: 100px;">
                        @endif
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

        function updateModel() {
            var selectedOption = $('#equipment_id').find('option:selected');
            var model = selectedOption.data('model') || ''; // Default to empty string if no model found
            var hospital = selectedOption.data('hospital') || ''; // Default to empty string if no hospital found
            $('#model').val(model);
            $('#hospital').val(hospital);
        }

        // Update model on page load if a selection is already made
        updateModel();

        // Update model when the selection changes
        $('#equipment_id').on('change', function() {
            updateModel();
        });
    });
</script>
@endsection
