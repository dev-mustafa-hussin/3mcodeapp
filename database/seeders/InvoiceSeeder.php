<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 invoices using Service would be best to test logic, but Seeder usually just inserts data.
        // Let's use the Service manually if I could inject it, or just create raw data.
        // For consistency and logic (stock deduction), using Service is better.
        // But in Seeder we can just simulate.
        
        $customer = Customer::first();
        $product = Product::first();
        
        if (!$customer || !$product) return;

        // 5 Invoices
        for ($i = 1; $i <= 5; $i++) {
             // Create Invoice
             $invoice = Invoice::create([
                 'invoice_number' => "INV-2024-" . str_pad($i, 4, '0', STR_PAD_LEFT),
                 'customer_id' => $customer->id,
                 'date' => now(),
                 'due_date' => now()->addDays(7),
                 'status' => 'paid',
                 'subtotal' => 0,
                 'tax' => 0,
                 'discount' => 0,
                 'total' => 0
             ]);
             
             // Create Item
             $qty = 2;
             $price = $product->price;
             $total = $qty * $price;
             
             $invoice->items()->create([
                 'product_id' => $product->id,
                 'quantity' => $qty,
                 'price' => $price,
                 'total' => $total
             ]);
             
             $invoice->update([
                 'subtotal' => $total,
                 'total' => $total
             ]);
             
             // Create Payment
             $invoice->payments()->create([
                 'amount' => $total,
                 'payment_method' => 'cash',
                 'payment_date' => now(),
                 'reference' => 'REF-' . $i
             ]);
        }
    }
}
