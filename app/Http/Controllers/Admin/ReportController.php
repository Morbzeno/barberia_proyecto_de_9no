<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sell;

class ReportController extends Controller
{
    public function generalPdf(Request $request)
{
    // Rango de fechas seleccionado
    $startDate = $request->input(
        'start_date',
        now()->startOfMonth()->format('Y-m-d')
    );

    $endDate = $request->input(
        'end_date',
        now()->format('Y-m-d')
    );

    // Citas del periodo con toda la información necesaria
    $appointments = Appointment::with([
        'client',
        'employee.person',
        'appointmentDetails.service',
        'payment',
    ])
        ->whereBetween('startHour', [
            $startDate . ' 00:00:00',
            $endDate . ' 23:59:59',
        ])
        ->orderBy('startHour')
        ->get();

    // AQUÍ SIGUE TODO EL RESTO DE TU CÓDIGO ACTUAL

        // =========================
        // MÉTRICAS PRINCIPALES
        // =========================

        $totalAppointments = $appointments->count();

        $totalServices = $appointments
            ->flatMap(function ($appointment) {
                return $appointment->appointmentDetails;
            })
            ->count();

        // Tiempo total estimado en minutos
        $totalMinutes = $appointments->sum(function ($appointment) {
            if (!$appointment->startHour || !$appointment->finishHour) {
                return 0;
            }

            return $appointment->startHour
                ->diffInMinutes($appointment->finishHour);
        });

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        $estimatedTime = $hours > 0
            ? $hours . 'h ' . $minutes . 'm'
            : $minutes . 'm';

        // Ingresos generados
        $totalIncome = $appointments->sum(function ($appointment) {
            return $appointment->payment?->subtotal
                ?? $appointment->appointmentDetails->sum('totalPrice');
        });

        // =========================
        // APP VS PRESENCIAL
        // =========================
        // Por ahora usamos clientID como citas hechas desde la app.
        // Ajustaremos esto si tu BD tiene un campo específico
        // para distinguir el origen de la cita.

        $appAppointments = $appointments
            ->filter(fn ($appointment) => !empty($appointment->clientID))
            ->count();

        $inPersonAppointments = $totalAppointments - $appAppointments;

        $appPercentage = $totalAppointments > 0
            ? round(($appAppointments / $totalAppointments) * 100)
            : 0;

        $inPersonPercentage = $totalAppointments > 0
            ? round(($inPersonAppointments / $totalAppointments) * 100)
            : 0;

        $appIncome = $appointments
            ->filter(fn ($appointment) => !empty($appointment->clientID))
            ->sum(function ($appointment) {
                return $appointment->payment?->subtotal
                    ?? $appointment->appointmentDetails->sum('totalPrice');
            });

        $inPersonIncome = $totalIncome - $appIncome;

        // =========================
        // DESGLOSE POR SERVICIO
        // =========================

        $servicesBreakdown = $appointments
            ->flatMap(function ($appointment) {
                return $appointment->appointmentDetails;
            })
            ->groupBy('serviceID')
            ->map(function ($details) {
                $firstDetail = $details->first();

                return [
                    'name' => $firstDetail->service?->name ?? 'Servicio',
                    'quantity' => $details->count(),
                    'subtotal' => $details->sum('totalPrice'),
                ];
            })
            ->sortByDesc('quantity')
            ->values();

        // =========================
        // RENDIMIENTO POR EMPLEADO
        // =========================

        $employeePerformance = $appointments
            ->groupBy('employeeID')
            ->map(function ($employeeAppointments) {

                $firstAppointment = $employeeAppointments->first();

                $employeeName = 'Sin empleado';

                if ($firstAppointment->employee?->person) {
                    $person = $firstAppointment->employee->person;

                    // Ajustaremos estos campos si tu modelo Person
                    // usa nombres diferentes.
                    $employeeName = trim(
                        ($person->name ?? '') . ' ' .
                        ($person->lastName ?? '')
                    );
                }

                if (empty($employeeName)) {
                    $employeeName = 'Empleado #' .
                        $firstAppointment->employeeID;
                }

                return [
                    'name' => $employeeName,
                    'appointments' => $employeeAppointments->count(),
                    'total' => $employeeAppointments->sum(function ($appointment) {
                        return $appointment->payment?->subtotal
                            ?? $appointment->appointmentDetails->sum('totalPrice');
                    }),
                ];
            })
            ->sortByDesc('total')
            ->values();

        // =========================
        // GENERAR PDF
        // =========================
$sales = Sell::query()
    ->whereBetween('created_at', [
        $startDate . ' 00:00:00',
        $endDate . ' 23:59:59',
    ])
    ->orderBy('created_at')
    ->get();

$totalSales = $sales->count();

$totalSalesIncome = $sales->sum('total');


$pdf = Pdf::loadView(
    'admin.reports.general-pdf',
    compact(
        'appointments',
        'sales',
        'startDate',
        'endDate',
        'totalAppointments',
        'totalServices',
        'estimatedTime',
        'totalIncome',
        'appAppointments',
        'inPersonAppointments',
        'appPercentage',
        'inPersonPercentage',
        'appIncome',
        'inPersonIncome',
        'servicesBreakdown',
        'employeePerformance',
        'totalSales',
        'totalSalesIncome'
    )
)->setPaper('a4', 'portrait');


return $pdf->download(
    'reporte-general-' .
    $startDate . '-al-' .
    $endDate .
    '.pdf'
);
    }
}