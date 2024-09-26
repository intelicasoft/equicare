<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Excel;
use QrCode;
use App\Hospital;
use App\CallEntry;
use App\Equipment;
use App\Department;
use App\Calibration;
use App\Models\DataBrand;
use App\Models\DataAccesories;
use App\Models\DataModels;
use App\Models\Tickets;
use Illuminate\Http\Request;
// use App\Role;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Spaite\Permission\Role;
use App\QrGenerate;
use Illuminate\Support\Collection;
use App\Http\Requests\EquipmentRequest;
// use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// use Maatwebsite\Excel\Facades\Excel;
class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Artisan::call('migrate:fresh',['--force' => true]);
        // Artisan::call('db:seed',['--force' => true]);
        set_time_limit(0);
        $this->availibility('View Equipments');
        $index['page'] = 'equipments';
        //$index['equipments'] = Equipment::latest()->get();
        $index['hospitals'] = Hospital::query()->Hospital()->get();
        $index['companies'] = Equipment::query()->Hospital()->distinct()->get(['company']);
        $index['hospital_id'] = isset($request->hospital_id) ? $request->hospital_id : "";
        $index['companyy'] = isset($request->company) ? $request->company : "";

        $equipments = Equipment::select('*')->Hospital();
        if (isset($index['hospital_id']) && $index['hospital_id'] != "") {
            $equipments->where('hospital_id', $index['hospital_id']);
        }
        if (isset($index['companyy']) && $index['companyy'] != "") {
            $equipments->where('company', $index['companyy']);
        }
        if (isset($request->excel_hidden)) {
            $equipments = $equipments->latest()->get();
            $updatedEquipments = [];
            foreach ($equipments as $equipment) {
                $equipment->date_of_purchase = date_change($equipment->date_of_purchase);
                $equipment->order_date = date_change($equipment->order_date);
                $equipment->date_of_installation = date_change($equipment->date_of_installation);
                $equipment->warranty_due_date = date_change($equipment->warranty_due_date);
                $updatedEquipments[] = $equipment;
            }
            $equipments = collect($updatedEquipments);
            return Excel::download(new class($equipments) implements FromView
            {
                public function __construct($collection)
                {
                    $this->collection = $collection;
                }

                public function view(): View
                {
                    return view('equipments.export_excel')->with('equipments', $this->collection);
                }
            }, time() . '_equipment.xlsx');
        } elseif (isset($request->pdf_hidden)) {

            $equipments = $equipments->latest()->get();
            //dd($equipments);
            $pdf = PDF::loadView('equipments.export_pdf', ['equipments' => $equipments])->setPaper('a4', 'landscape');
            return $pdf->download(time() . '_equipment.pdf');
        } else {
            $index['equipments'] = $equipments->latest()->get();
        }
        return view('equipments.index', $index);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $this->availibility('Create Equipments');
        $index['page'] = 'equipments';
        $index['hospitals'] = Hospital::query()->Hospital()->get();
        $index['brands'] = DataBrand::all();
        $index['accesories'] = DataAccesories::all();
        $index['models'] = DataModels::all();
        // $index['departments'] =
        //     Department::select('id', DB::raw('CONCAT(short_name," (" , name ,")") as full_name'))
        //     ->pluck('full_name', 'id')
        //     ->toArray();
        return view('equipments.create', $index);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_equipments_common(Request $request, $api = 0)
    {
        $equipment = new Equipment;
        //$equipment->name = trim($request->name);
        //$equipment->short_name = $request->short_name;
        if ($api == 1) {
            $equipment->user_id = auth('sanctum')->user()->id;
        } else {
            $equipment->user_id = \Auth::user()->id;
        }
        $equipment->company = $request->company;
        $equipment->sr_no = $request->sr_no;
        $equipment->hospital_id = $request->hospital_id;
        //$equipment->department = $request->department;
        $equipment->model = $request->model;
        $equipment->qr_id = $request->qr_id;
        $equipment->brand_id = $request->brand_id;
        $equipment->accesory_id = $request->accesory_id;
        $equipment->model_id = $request->model_id;


        $equipment->latitude = $request->latitude;
        $equipment->longitude = $request->longitude;

        $equipment->last_id = $request->last_id;
        $equipment->status = $request->status;
        $equipment->supplies = $request->supplies;

        $dateFormat = env('date_convert', 'Y-m-d');
        $date_of_purchase = !empty($request->date_of_purchase) ? DateTime::createFromFormat($dateFormat, $request->date_of_purchase)->format('Y-m-d') : null;
        $order_date = !empty($request->order_date) ? DateTime::createFromFormat($dateFormat, $request->order_date)->format('Y-m-d') : null;
        $date_of_installation = !empty($request->date_of_installation) ? DateTime::createFromFormat($dateFormat, $request->date_of_installation)->format('Y-m-d') : null;
        $warranty_due_date = !empty($request->warranty_due_date) ? DateTime::createFromFormat($dateFormat, $request->warranty_due_date)->format('Y-m-d') : null;

        // $date_of_purchase = !empty($request->date_of_purchase) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->date_of_purchase) : null;
        // $order_date = !empty($request->order_date) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->order_date) : null;
        // $date_of_installation = !empty($request->date_of_installation) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->date_of_installation) : null;
        // $warranty_due_date = !empty($request->warranty_due_date) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->warranty_due_date) : null;

        $equipment->date_of_purchase = $date_of_purchase;
        $equipment->order_date = $order_date;
        $equipment->date_of_installation = $date_of_installation;
        $equipment->warranty_due_date = $warranty_due_date;
        $equipment->service_engineer_no = $request->service_engineer_no;
        $equipment->is_critical = $request->is_critical;
        $equipment->notes = $request->notes;
        $equipment_number = Equipment::where('hospital_id', $request->hospital_id)
            ->where('name', trim($request->name))
            ->where('short_name', $request->short_name)
            // ->where('department', $request->department)
            ->count();
        $equipment_number = sprintf("%02d", $equipment_number + 1);
        $equipment->unique_id = "";
        $hospital = Hospital::where('id', $request->hospital_id)->first();
        if ($hospital != "") {
            // $unique_id = $hospital->slug . '/' . $equipment->department . '/' . $equipment->short_name . '/' . $equipment_number;
            // $label_name = $hospital->slug . '/' . $equipment->department . '/' . $equipment->short_name . '/';
            $unique_id = $hospital->slug . '/' . $equipment->department . '/' . $equipment->short_name . '/' . $equipment_number;
            $label_name = $hospital->slug . '/' . $equipment->department . '/' . $equipment->short_name . '/';
            $equipment_last = Equipment::where('unique_id', 'like', $label_name . '%')->orderBy('unique_id', 'desc')->first();
            if ($equipment_last) {
                $last_label_no = explode('/', $equipment_last->unique_id);
                $last_label_no = end($last_label_no);
                $equipment_number = sprintf("%02d", ((int)$last_label_no) + 1);
                $unique_id = $label_name . $equipment_number;
            } else {
                $unique_id = $label_name . "01";
            }
            $equipment->unique_id = $unique_id;
        }
        $equipment->save();

        $id = $equipment->id;
        $srno = $equipment->sr_no;
        //for generating qr 
        $equipment = Equipment::find($id);
        //update equipment qr_id if it not coming from request
        // dd($request->qr_id); 
        if (request('qr_id') != null) {
            $qr = QrGenerate::where('uid', request('qr_id'))->first();
            $qr->assign_to = $equipment->id;
            $qr->save();
            $equipment->qr_id = $qr->id;
            $equipment->save();
        } else {
            $qr = new QrGenerate;
            $qr->assign_to = $equipment->id;
            $qr->uid = Str::random(11);
            $qr->save();
            $url = 'http://34.133.51.29/equicare' . "/scan/qr/" . $qr->uid;
            // $url = url('/') . "/scan/qr/" . $qr->uid;
            Log::info('El valor de la variable es: ' . $url);

            if (extension_loaded('imagick')) {
                QrCode::format('png')->size(300)->generate($url, 'uploads/qrcodes/qr_assign/' . $qr->uid . '.png');
            }
            $equipment->qr_id = $qr->id;
            $equipment->save();
        }
        // dd('test');
        return $equipment;
    }
    public function store(EquipmentRequest $request)
    {
        $equipment = $this->store_equipments_common($request, 0);
        return redirect('admin/equipments')->with('flash_message', 'Equipment "' . $equipment->name . '" created');
    }

    public function regenerate_all_qr()
    {
        set_time_limit(0);
        $equipments = Equipment::all();
        foreach ($equipments as $key => $equipment) {
            $id = $equipment->id;
            if (extension_loaded('imagick')) {
                // Generate QR Code
                $url = url('/') . "/equipments/history/" . $id;
                $image = QrCode::format('png')->size(300)->generate($url, 'uploads/qrcodes/' . $id . '.png');
            }
        }
        echo "QRs regenerated!";
    }

    public function qr($id)
    {
        $equipment = Equipment::findOrFail($id);
        $url = url('/') . "/admin/equipments/history/" . $equipment->id;
        $qr_content = __('equicare.equipment_id') . ": " . $equipment->unique_id . " \n\n" .
            __('equicare.details') . ": " . $url;
        return '<div style="text-align:center;"><img src="data:image/png;base64, ' . base64_encode(QrCode::format('png')->size(150)->generate($qr_content)) . '"></div>';
    }

    public function qr_image($id)
    {
        $equipment = Equipment::findOrFail($id);
        $url = url('/') . "/admin/equipments/history/" . $equipment->id;
        $qr_content = __('equicare.equipment_id') . ": " . $equipment->unique_id . " \n\n" .
            __('equicare.details') . ": " . $url;
        $image = QrCode::format('png')->size(150)->generate($qr_content);
        return response($image)->header('Content-type', 'image/png');
    }

    public function edit($id)
    {
        $this->availibility('Edit Equipments');
        $index['page'] = 'equipments';
        $index['equipment'] = Equipment::findOrFail($id);
        $index['hospitals'] = Hospital::query()->Hospital()->get();
        $index['brands'] = DataBrand::all();
        $index['accesories'] = DataAccesories::all();
        $index['models'] = DataModels::all();
        // $index['departments'] =
        //     Department::select('id', DB::raw('CONCAT(short_name," (" , name ,")") as full_name'))
        //     ->pluck('full_name', 'id')
        //     ->toArray();
        return view('equipments.edit', $index);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\quipments  $quipments
     * @return \Illuminate\Http\Response
     */
    public function update(EquipmentRequest $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        //$equipment->name = trim($request->name);
        //$equipment->short_name = $request->short_name;
        $equipment->user_id = \Auth::user()->id;
        $equipment->company = $request->company;
        $equipment->sr_no = $request->sr_no;
        $equipment->hospital_id = $request->hospital_id;
        //$equipment->department = $request->department;
        $equipment->model = $request->model;
        $equipment->brand_id = $request->brand_id;
        $equipment->accesory_id = $request->accesory_id;
        $equipment->model_id = $request->model_id;

        $equipment->latitude = $request->latitude;
        $equipment->longitude = $request->longitude;

        $equipment->last_id = $request->last_id;
        $equipment->status = $request->status;
        $equipment->supplies = $request->supplies;

        // $date_of_purchase = !empty($request->date_of_purchase) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->date_of_purchase) : null;
        // $order_date = !empty($request->order_date) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->order_date) : null;
        // $date_of_installation = !empty($request->date_of_installation) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->date_of_installation) : null;
        // $warranty_due_date = !empty($request->warranty_due_date) ?\Carbon\Carbon::createFromFormat('m-d-Y',$request->warranty_due_date) : null;
        $dateFormat = env('date_convert', 'Y-m-d');
        $date_of_purchase = !empty($request->date_of_purchase) ? DateTime::createFromFormat($dateFormat, $request->date_of_purchase)->format('Y-m-d') : null;
        $order_date = !empty($request->order_date) ? DateTime::createFromFormat($dateFormat, $request->order_date)->format('Y-m-d') : null;
        $date_of_installation = !empty($request->date_of_installation) ? DateTime::createFromFormat($dateFormat, $request->date_of_installation)->format('Y-m-d') : null;
        $warranty_due_date = !empty($request->warranty_due_date) ? DateTime::createFromFormat($dateFormat, $request->warranty_due_date)->format('Y-m-d') : null;

        // $date_of_purchase = !empty($request->date_of_purchase) ? date('Y-m-d', strtotime($request->date_of_purchase)) : null;
        // $order_date = !empty($request->order_date) ? date('Y-m-d', strtotime($request->order_date)) : null;
        // $date_of_installation = !empty($request->date_of_installation) ? date('Y-m-d', strtotime($request->date_of_installation)) : null;
        // $warranty_due_date = !empty($request->warranty_due_date) ? date('Y-m-d', strtotime($request->warranty_due_date)) : null;

        $equipment->date_of_purchase = $date_of_purchase;
        $equipment->order_date = $order_date;
        $equipment->date_of_installation = $date_of_installation;
        $equipment->warranty_due_date = $warranty_due_date;
        $equipment->service_engineer_no = $request->service_engineer_no;
        $equipment->is_critical = $request->is_critical;
        $equipment->notes = $request->notes;

        $equipment->save();
        // if (extension_loaded('imagick')) {
        //     // Generate QR Code
        //     $url = url('/') . "/equipments/history/" . $id;
        //     $image = QrCode::format('png')->size(300)->generate($url, 'uploads/qrcodes/' . $id . '.png');
        // }

        return redirect('admin/equipments')->with('flash_message', 'Equipo "' . $equipment->name . '" actualizado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\quipments  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->availibility('Delete Equipments');
        $equipment = Equipment::findOrFail($id);
        $qr = QrGenerate::find($equipment->qr_id);
        $qr->delete();
        $equipment->delete();

        return redirect('admin/equipments')->with('flash_message', 'Equipment "' . $equipment->name . '" deleted');
    }

    public static function availibility($method)
    {
        if (\Auth::user()->hasDirectPermission($method)) {
            return true;
        } else {
            abort('401');
        }

        // $r_p = \Auth::user()->getPermissionsViaRoles()->pluck('name')->toArray();

        // if (\Auth::user()->hasDirectPermission($method)) {

        //     return true;
        // } elseif (!in_array($method, $r_p)) {
        //     abort('401');
        // } else {
        //     return true;
        // }
    }

    public function history($id)
    {

        $index['page'] = 'equipments history';
        $index['equipment'] = Equipment::find($id);

        // Obtener historial de Tickets

        $tickets = Tickets::where('equipment_id', $id)
            ->with('user', 'manager', 'equipment')
            ->get()
            ->map(function ($item) {
                return collect($item)->put('type', 'Ticket');
            });


        //ultimo ticket abierto que no sea de mantenimiento ni instalacion
        $lastOpenedTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', '!=', 'INSTALACIÓN (M)')
            ->where('category', '!=', 'MANTENIMIENTO PREVENTIVO (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastOpenedTicket'] = $lastOpenedTicket;

        //ultimo ticket de manteniento abierto
        $lastMaintenanceTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', 'MANTENIMIENTO PREVENTIVO (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastMaintenanceTicket'] = $lastMaintenanceTicket;

        //ultimo ticket de instalacion abierto
        $lastInstallationTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', 'INSTALACIÓN (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastInstallationTicket'] = $lastInstallationTicket;

        // Ordenar por fecha descendente
        $index['data'] = $tickets->sortByDesc('created_at');


        //Log::info('El valor de la variable tickets es: ' . $tickets->toJson());

        return view('equipments.history', $index);
    }


    public function history_qr($uid)
    {
        Log::info('El valor de la variable uid es: ' . $uid);
        $index['page'] = 'equipments history';
        $qr = QrGenerate::where('uid', $uid)->first();
        if ($qr->assign_to != 0) {
            $index['equipment'] = Equipment::find($qr->assign_to);
            // $index['model'] = DataModels::find($index['equipment']->model_id);

            $id = $index['equipment']->id;

            // Obtener historial de Tickets
            $tickets = Tickets::where('equipment_id', $id)
                ->with('user', 'manager', 'equipment')
                ->get()
                ->map(function ($item) {
                    return collect($item)->put('type', 'Ticket');
                });

            //ultimo ticket abierto que no sea de mantenimiento ni instalacion
            $lastOpenedTicket = Tickets::where('equipment_id', $id)
                ->where('status', '1')
                ->where('category', '!=', 'INSTALACIÓN (M)')
                ->where('category', '!=', 'MANTENIMIENTO PREVENTIVO (M)')
                ->with('user', 'manager', 'equipment')
                ->orderBy('created_at', 'desc')
                ->first();

            $index['lastOpenedTicket'] = $lastOpenedTicket;

            //ultimo ticket de manteniento abierto
            $lastMaintenanceTicket = Tickets::where('equipment_id', $id)
                ->where('status', '1')
                ->where('category', 'MANTENIMIENTO PREVENTIVO (M)')
                ->with('user', 'manager', 'equipment')
                ->orderBy('created_at', 'desc')
                ->first();

            $index['lastMaintenanceTicket'] = $lastMaintenanceTicket;

            //ultimo ticket de instalacion abierto
            $lastInstallationTicket = Tickets::where('equipment_id', $id)
                ->where('status', '1')
                ->where('category', 'INSTALACIÓN (M)')
                ->with('user', 'manager', 'equipment')
                ->orderBy('created_at', 'desc')
                ->first();

            $index['lastInstallationTicket'] = $lastInstallationTicket;

            // Ordenar por fecha descendente
            $index['data'] = $tickets->sortByDesc('created_at');

            //Log::info('El valor de la variable tickets es: ' . $tickets->toJson());

            foreach ($index as $key => $value) {
                Log::info('El valor de la variable ' . $key . ' es: ' . $value);
            }



            return view('equipments.history', $index);
        } else {
            return redirect('admin/equipments/create' . '?qr_id=' . $qr->uid);
        }
    }

    public function hq_history_qr($id)
    {
        Log::info('El valor de la variable id es: ' . $id);

        $index['page'] = 'equipments history';

        // Encuentra el equipo usando el ID
        $equipment = Equipment::where('last_id', $id)->first();

        // Verifica si se encontró el equipo antes de intentar acceder a sus propiedades
        if (!$equipment) {
            Log::error('No se encontró el equipo con last_id: ' . $id);
            return redirect('admin/equipments')->with('flash_message', 'Equipo con ID antiguo "' . $id . '" No encontrado');
        }

        $index['equipment'] = $equipment;

        // Ahora puedes acceder al id sin problemas
        $id = $equipment->id;
        // Obtener historial de Tickets
        $tickets = Tickets::where('equipment_id', $id)
            ->with('user', 'manager', 'equipment')
            ->get()
            ->map(function ($item) {
                return collect($item)->put('type', 'Ticket');
            });

        //ultimo ticket abierto que no sea de mantenimiento ni instalacion
        $lastOpenedTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', '!=', 'INSTALACIÓN (M)')
            ->where('category', '!=', 'MANTENIMIENTO PREVENTIVO (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastOpenedTicket'] = $lastOpenedTicket;

        //ultimo ticket de manteniento abierto
        $lastMaintenanceTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', 'MANTENIMIENTO PREVENTIVO (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastMaintenanceTicket'] = $lastMaintenanceTicket;

        //ultimo ticket de instalacion abierto
        $lastInstallationTicket = Tickets::where('equipment_id', $id)
            ->where('status', '1')
            ->where('category', 'INSTALACIÓN (M)')
            ->with('user', 'manager', 'equipment')
            ->orderBy('created_at', 'desc')
            ->first();

        $index['lastInstallationTicket'] = $lastInstallationTicket;

        // Ordenar por fecha descendente
        $index['data'] = $tickets->sortByDesc('created_at');

        //Log::info('El valor de la variable tickets es: ' . $tickets->toJson());

        foreach ($index as $key => $value) {
            Log::info('El valor de la variable ' . $key . ' es: ' . $value);
        }

        return view('equipments.history', $index);
    }

    // public function etiqueta($id)
    // {
    //     // $this->availibility('View orders');
    //     $pdf['page'] = 'Etiqueta';
    //     $pdf['equipment'] = Equipment::find($id);
    //     $pdf = PDF::loadView('equipments.etiqueta', $pdf);

    //     return $pdf->download('etiqueta' . $id . '.pdf');
    //     // return view('equipments.etiqueta', $pdf);
    // }

    // public function etiqueta($id)
    // {
    //     // Cargar los datos del equipo
    //     $data['page'] = 'Etiqueta';
    //     $data['equipment'] = Equipment::find($id);

    //     // Retornar la vista en lugar del PDF
    //     return view('equipments.etiqueta', $data);
    // }

    public function etiqueta($id)
    {
        $data['page'] = 'Etiqueta';
        $data['equipment'] = Equipment::find($id);

        // Generar PDF con tamaño de página A4 y márgenes ajustados
        $pdf = PDF::loadView('equipments.etiqueta', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm');

        return $pdf->stream();  // Para ver el PDF en el navegador
        // return $pdf->download('etiqueta' . $id . '.pdf');  // Para descargarlo
    }



    // public function render_etiqueta($id)
    // {
    //     $pdf['page'] = 'Etiqueta';
    //     $pdf['equipment'] = Equipment::find($id);
    //     $pdf = PDF::loadView('equipments.etiqueta', $pdf);
    //     return $pdf->download('etiqueta' . $id . '.pdf');
    // }
}
