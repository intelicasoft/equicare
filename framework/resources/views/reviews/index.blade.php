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
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-hover dataTable bottom-padding" id="data_table">
                        <thead class="thead-inverse">
                            <tr>
                                <th> # </th>
                                <th> No. Serie</th>
                                <th> Hospital </th>
                                <th> Status </th>
                                <th> Insumos </th>
                                <th> @lang('equicare.descripcion') </th>
                                <th> Última Fecha de Revisión </th>
                                @if(Auth::user()->can('Edit brands') || Auth::user()->can('Delete brands'))
                                <th> @lang('equicare.action')</th>
                                @endif 
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($equipos))
                            @php
                                $count = 0;
                            @endphp
                            @foreach ($equipos as $equipo)
                            @php
                                $count++;
                                $ultimaRevision = \Carbon\Carbon::parse($equipo->ultima_fecha_revision);
                                $hoy = \Carbon\Carbon::now();
                                $diasDiferencia = $hoy->diffInDays($ultimaRevision);
                            @endphp
                            <tr>
                            <td> {{ $count }} </td>
                            <td> {{ $equipo->sr_no ?? '-' }}</td>
                            <td> {{ $equipo->hospital->name ?? '-' }}</td>
                            <td>
                                @switch($equipo->status)
                                    @case(1)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightblue; color: black;">Disponible, en uso</span>
                                        @break
                            
                                    @case(2)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgray; color: black;">Disponible, sin uso</span>
                                        @break
                            
                                    @case(3)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgreen; color: black;">Fuera de Servicio, reportado</span>
                                        @break
                            
                                    @case(4)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: pink; color: black;">Fuera de Servicio, no reportado</span>
                                        @break
                            
                                    @default
                                        <span>-</span>
                                @endswitch
                            </td>    
                            
                            <td>    
                                @switch($equipo->supplies)
                                    @case(1)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightblue; color: black;">No dispone de insumos</span>
                                        @break
                            
                                    @case(2)
                                        <span class="badge badge-pill" style="font-size: 1.5rem; background-color: lightgray; color: black;">Disponible de insumos</span>
                                        @break
                            
                                    @case(Null)
                                        <span>-</span>
                                @endswitch
                            </td>
                            
                            <td> {{ $equipo->description ?? '-' }}</td>                       
                            <td> {{ $equipo->ultima_fecha_revision ? \Carbon\Carbon::parse($equipo->ultima_fecha_revision)->format('d/m/Y') : '-' }}</td>

                            <td>
                                {!! Form::open(['url' => 'admin/reviews/'.$equipo->id,'method'=>'DELETE','class'=>'form-inline']) !!}
                                    @if ($hoy)
                                    <a href="{{ route('reviews.edit',$equipo->id) }}" class="btn bg-purple btn-sm btn-flat" title="@lang('equicare.edit')"><i class="fa fa-check"></i> Revisar </a>
                                    @endif
                                    
                                {!! Form::close() !!}
                            </td>
    
                        </tr>
                        @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th> # </th>
                                <th> No. Serie</th>
                                <th> Hospital</th>
                                <th> Status</th>
                                <th> Insumos </th>
                                <th> Descripcion </th>
                                <th> Última Fecha de Revisión </th>
                                @if(Auth::user()->can('Edit brands') || Auth::user()->can('Delete brands'))
                                <th> @lang('equicare.action')</th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
