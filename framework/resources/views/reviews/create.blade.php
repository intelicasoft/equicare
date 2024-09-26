@extends('layouts.admin')
@section('body-title')
    Revisiones de Equipos
@endsection
@section('title')
	| Revisiones de Equipos
@endsection
@section('breadcrumb')
<li class="active">Revisiones de Equipos</li>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
			<div class="box-header">
				<h4 class="box-title"> Realizar Revisiones </h4>
				</div>

				<div class="box-body ">
					@include ('errors.list')
					<form class="form" method="post" action="{{ route('reviews.store') }}" enctype="multipart/form-data">
						{{ csrf_field() }}
						{{ method_field('POST') }}
						<div class="row">
							<div class="col-md-12">
								<h4>Distancia al equipo desde tu ubicación: <span id="distancia"></span> km</h4>
                                <input type="hidden" name="distance" id="distance">
							</div>
							<div class="form-group col-md-6">
								<label for="description"> @lang('equicare.description') </label>
								<input type="text" name="description" class="form-control" value="{{ old('description') }}" />
							</div>

							<div class="form-group col-md-6">
								<label for="equipment_id"> Numero de Serie del Equipo Clinico</label>
								<select name="equipment_id" class="form-control">
									<option value="{{ $equipo->id }}" selected>{{ $equipo->sr_no }} </option>
								</select>
							</div>

                            <div class="form-group col-md-6">
                                <label>Seleccione el estado en el que se encuentra el equipo</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status1" value="1" required>
                                        <label class="form-check-label" for="status1">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightblue; color: black;">Disponible, en uso</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status2" value="2" required>
                                        <label class="form-check-label" for="status2">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgray; color: black;">Disponible, sin uso</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status3" value="3" required>
                                        <label class="form-check-label" for="status3">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgreen; color: black;">Fuera de Servicio, reportado</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status4" value="4" required>
                                        <label class="form-check-label" for="status4">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: pink; color: black;">Fuera de Servicio, no reportado</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Seleccione la disponibilidad de insumos</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="supplies" id="supplies1" value="1" required>
                                        <label class="form-check-label" for="supplies1">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightblue; color: black;">No dispone de insumos</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="supplies" id="supplies2" value="2" required>
                                        <label class="form-check-label" for="supplies2">
                                            <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgray; color: black;">Disponible de insumos</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            
							{{-- Botón para abrir la cámara --}}
							<div class="form-group col-md-12">
								<button type="button" id="open-camera" class="btn btn-secondary btn-flat">Abrir Cámara</button>
							</div>

							{{-- Capturar imagen con la cámara (oculto inicialmente) --}}
							
							<div id="camera-section" class="form-group col-md-12" style="display:none;">
								<label for="images"> Capturar Imagen </label>
								<div>
									<video id="video" width="320" height="240" autoplay></video>
									<button type="button" id="snap" class="btn btn-primary btn-flat">Tomar Foto</button>
									<canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
									<input type="hidden" name="images" id="images">
								</div>
								
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
<script>

document.addEventListener('DOMContentLoaded', function () {
    const openCameraButton = document.getElementById('open-camera');
    const cameraSection = document.getElementById('camera-section');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snap = document.getElementById('snap');
    const imagenInput = document.getElementById('images');
    const distanceInput = document.getElementById('distance');
    const context = canvas.getContext('2d');

    // Mostrar la sección de la cámara al hacer clic en el botón
    openCameraButton.addEventListener('click', function () {
        cameraSection.style.display = 'block';
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(stream) {
                video.srcObject = stream;
                video.play(); // Asegurar que el video se reproduzca
            })
            .catch(function(error) {
                console.error('Error accediendo a la cámara', error);
            });
    });

    // Capturar la imagen cuando se hace clic en el botón
    snap.addEventListener('click', function () {
        context.drawImage(video, 0, 0, 320, 240);
        imagenInput.value = canvas.toDataURL('image/png');
        canvas.style.display = 'block';
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const equipmentLat = {{$equipo->latitude}}
            const equipmentLng = {{$equipo->longitude}}

            // Verificación de las coordenadas
            console.log(`User Coordinates: ${userLat}, ${userLng}`);
            console.log(`Equipment Coordinates: ${equipmentLat}, ${equipmentLng}`);

            const distance = haversine(userLat, userLng, equipmentLat, equipmentLng);
            distanceInput.value = distance.toFixed(2);
            document.getElementById('distancia').innerText = distance.toFixed(2);

            if (distance > 10) {
                alert('Te encuentras fuera de la zona permitida, la revisión no es válida y se le notificará al administrador.');
                document.querySelector('form').addEventListener('submit', function(event) {
                    event.preventDefault();
                });
            }
        });
    } else {
        alert('La geolocalización no es compatible con este navegador.');
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
        });
    }
});

function haversine(lat1, lon1, lat2, lon2) {
    const toRad = angle => angle * Math.PI / 180;
    const R = 6371; // Radio de la Tierra en km
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c;

    console.log(`Calculated Distance: ${distance}`);
    
    return distance;
}



</script>
@endsection
