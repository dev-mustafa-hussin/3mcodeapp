<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('invoice.customer');

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $payments = $query->latest()->paginate(20);
        return PaymentResource::collection($payments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'reference' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($invoice->status === 'cancelled') {
             throw ValidationException::withMessages([
                'invoice_id' => ['Cannot add payment to a cancelled invoice.'],
            ]);
        }

        $payment = Payment::create([
            'invoice_id' => $request->invoice_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'reference' => $request->reference,
        ]);

        // Update Invoice Status
        $totalPaid = $invoice->payments()->sum('amount') + $payment->amount;
        
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
           // Maybe partial? but requirements said draft, sent, paid, overdue, cancelled. 
           // Paid usually means fully paid. 
           // If we wanted partial, we should enable 'partial' status.
           // For now, let's keep it simply paid if full, or just sent/overdue if not.
        }

        return new PaymentResource($payment);
    }

    public function show(Payment $payment)
    {
        return new PaymentResource($payment->load('invoice'));
    }

    /*
    public function update(Request $request, string $id)
    {
        // Payments are usually immutable for audit trails. 
        // If modification is needed, better to delete/refund and create new one.
    }
    */

    public function destroy(Payment $payment)
    {
         // Recalculate invoice status logic could be here if needed 
         // But for simplicity, just delete.
        $payment->delete();
        return response()->json(null, 204);
    }
}
