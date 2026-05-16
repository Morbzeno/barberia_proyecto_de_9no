<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\AppointmentDetail;
use App\Models\Service;
use App\Models\Client;
use Psy\Util\Json;

class AppointmentController extends Controller
{
    public function index(){
        $appointments = Appointment::with('client', 'appointmentDetails.service')->paginate(10);
        
        if ($appointments->isEmpty()){
            return response()->json([
                'message' => 'no se encontraron citas'
            ], 400);
        }

        return response()->json([
            'message' => 'aqui estan las citas',
            'data' => $appointments
        ], 200);
    }

    public function show($id){
        $appointment = Appointment::with('client', 'appointmentDetails.service')->paginate(10);
        if ($appointment->isEmpty()){
            return response()->json([
                'message' => 'no se encontraron citas'
            ], 400);
        }

        return response()->json([
            'message' => 'aqui esta la cita',
            'data' => $appointment
        ], 200);
    }
}
