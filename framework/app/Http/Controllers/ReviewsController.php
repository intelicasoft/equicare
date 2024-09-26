<?php

namespace App\Http\Controllers;

use App\Equipment;
use App\Models\Reviews;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\DistanceAlert;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //$this->availibility('View brands');
        $index['page'] = 'reviews';
    
    
        $userId = auth()->user()->id; 
            
        $equipos = Equipment::with(['hospital'])
            ->leftJoin('reviews', 'equipments.id', '=', 'reviews.equipment_id')
            ->join('hospital_user', 'equipments.hospital_id', '=', 'hospital_user.hospital_id') // Join con la tabla hospital_user
            ->where('hospital_user.user_id', $userId) // Filtrado por el hospital asignado al usuario
            ->select('equipments.*', 
                    DB::raw('MAX(reviews.created_at) as ultima_fecha_revision'),
                    DB::raw('(SELECT description FROM reviews WHERE reviews.equipment_id = equipments.id ORDER BY reviews.created_at DESC LIMIT 1) as description'))
            ->groupBy('equipments.id')
            ->get();

        return view('reviews.index', $index,['equipos' => $equipos]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    // public function create()
    // {
    //     $index['page'] = 'reviews';
    //     $index['user'] = Auth::user();

    //     return view('reviews.create',$index);
    // }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {

        // Obtener la distancia antes de la validación
        $distance = $request->input('distance');

        // Verificar si la distancia es mayor a 1 y enviar correo al administrador
        if ($distance > 10) {
            $adminEmail = 'lpipeavila1@gmail.com'; // Cambia esto al correo del administrador
            $user = Auth::user();
            
            Mail::to($adminEmail)->send(new DistanceAlert($user, $distance));
        }
        
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'equipment_id' => 'required|integer|exists:equipments,id', // Suponiendo que hay una tabla de equipos
            'images' => 'required|string', // Modificamos la validación a 'string'
            'status' => 'required|integer',
            'supplies' => 'required|integer',
            'distance' => 'required|numeric|max:9.99',
        ]);

        $data['user_id'] = Auth::id(); // Utiliza Auth::id() en lugar de Auth::user()->id

        $equipment = Equipment::find($data['equipment_id']);
        if ($equipment) {
            $equipment->status = $data['status'];
            $equipment->supplies = $data['supplies'];
            $equipment->save();
        } else {
            return back()->withErrors(['equipment_id' => 'El equipo no fue encontrado.']);
        }

        // Procesar la imagen si está presente
        if ($request->has('images')) {
            $imageData = $request->input('images');
            $imageName = time() . '.png'; // Por defecto, asumiremos que la imagen es en formato PNG

            // Guardar la imagen en el servidor
            $path = public_path('images/' . $imageName);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);
            file_put_contents($path, $imageData);

            $data['images'] = $imageName;
        } else {
            return back()->withErrors(['images' => 'La imagen es requerida.']);
        }

        Reviews::create($data);

        return redirect()->route('reviews.index')->with('success', 'Revisión creada exitosamente.');
    }


    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $index['page'] = 'reviews';
        $index['equipo'] = Equipment::findOrFail($id);
        $index['user'] = Auth::user();

        return view('reviews.create',$index);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       
    }

    public static function availibility($method)
	{
		// $r_p = \Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray();
		if (\Auth::user()->hasDirectPermission($method)) {
			return true;
		} else {
			abort('401');
		}
		// if (\Auth::user()->hasDirectPermission($method)) {
		// 	return true;
		// } elseif (!in_array($method, $r_p)) {
		// 	abort('401');
		// } else {
		// 	return true;
		// }
	}
}
