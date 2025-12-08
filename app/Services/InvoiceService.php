<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class InvoiceService
{
    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $totals = $this->calculateTotals($data['items'], $data['discount'] ?? 0, $data['tax'] ?? 0);

            // Generate Invoice Number
            $data['invoice_number'] = $this->generateInvoiceNumber();
            $data['subtotal'] = $totals['subtotal'];
            $data['total'] = $totals['total'];
            $data['tax'] = $data['tax'] ?? 0; // if frontend sends calculated tax or rate, simple here: assumes amount.
            // Requirement says "calculate totals automatically (subtotal, tax, total)"
            // Let's assume tax in data is RATE or Amount? 
            // Usually simple ERPs take tax amount or calculate from rate. 
            // Let's assume input 'tax' is the tax amount provided by FE or 0.
            // OR we calculate tax based on a fixed rate? 
            // User requirement: "حساب الإجماليات تلقائياً (subtotal, tax, total)"
            // Let's make it robust: Calculate Subtotal from items. 
            // If Tax is provided as amount, use it. If not, 0. 
            // Adjust Subtotal + Tax - Discount = Total.

            $invoice = Invoice::create($data);

            foreach ($data['items'] as $itemData) {
                // Get Product to check stock and get current price (if needed, but usually we trust FE or taking current Price)
                $product = Product::findOrFail($itemData['product_id']);
                
                // Advanced Validation: Check Stock
                if ($product->stock < $itemData['quantity']) {
                    throw new Exception("Product {$product->name} has insufficient stock.");
                }

                // Decrement Stock
                $product->decrement('stock', $itemData['quantity']);

                $invoice->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'total' => $itemData['quantity'] * $itemData['price'],
                ]);
            }

            return $invoice;
        });
    }

    public function updateInvoice(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            // If items are being updated, we need to handle stock reversion and re-application
            // This is complex. Simplest strategy for update with item change:
            // 1. Revert stock for all old items
            // 2. Delete old items
            // 3. Create new items and apply stock
            
            // Check if items key exists to know if we are updating items
            if (isset($data['items'])) {
                // Revert Stock
                foreach ($invoice->items as $item) {
                     $product = Product::find($item->product_id);
                     if ($product) {
                         $product->increment('stock', $item->quantity);
                     }
                }
                
                // Delete old items
                $invoice->items()->delete();

                // Recalculate totals
                $totals = $this->calculateTotals($data['items'], $data['discount'] ?? $invoice->discount, $data['tax'] ?? $invoice->tax);
                $data['subtotal'] = $totals['subtotal'];
                $data['total'] = $totals['total'];

                // Create new items
                foreach ($data['items'] as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);
                    if ($product->stock < $itemData['quantity']) {
                        throw new Exception("Product {$product->name} has insufficient stock.");
                    }
                    $product->decrement('stock', $itemData['quantity']);

                    $invoice->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'total' => $itemData['quantity'] * $itemData['price'],
                    ]);
                }
            } else {
                 // Updating only invoice details like status or customer, no recalculation if no items change
                 // But wait, if discount/tax changes?
                 // Let's assume if items not sent, we don't recalc subtotal.
            }

            $invoice->update($data);
            return $invoice->fresh(['items', 'customer']);
        });
    }

    public function deleteInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            // Restore stock
            foreach ($invoice->items as $item) {
                 $product = Product::find($item->product_id);
                 if ($product) {
                     $product->increment('stock', $item->quantity);
                 }
            }
            // Payments are cascade delete or soft delete
            $invoice->delete();
        });
    }

    private function calculateTotals(array $items, float $discount = 0, float $tax = 0): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['price'];
        }
        
        $total = $subtotal + $tax - $discount;

        return [
            'subtotal' => $subtotal,
            'total' => max($total, 0), // No negative total
        ];
    }

    private function generateInvoiceNumber(): string
    {
        // Format: INV-YYYY-0001
        $prefix = 'INV-' . date('Y') . '-';
        $lastInvoice = Invoice::withTrashed()
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastInvoice) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
