<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('appointment.client.person')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('appointment.client.person', 'appointment.employee.person', 'appointment.chair');

        return view('admin.payments.show', compact('payment'));
    }
}
