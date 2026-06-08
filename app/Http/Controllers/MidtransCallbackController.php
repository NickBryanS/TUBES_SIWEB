<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            Log::warning('Midtrans Webhook: Invalid Signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // order_id is in format 'GK-XXXX-timestamp'
        $orderIdParts = explode('-', $request->order_id);
        // The transaction ID is the second element (index 1)
        $transactionId = (int) ($orderIdParts[1] ?? null);

        if (!$transactionId) {
            Log::error('Midtrans Webhook: Transaction ID not found in order_id: ' . $request->order_id);
            return response()->json(['message' => 'Transaction ID not found'], 404);
        }

        $transaction = Transaction::with(['payment', 'details.product'])->find($transactionId);
        if (!$transaction) {
            Log::error('Midtrans Webhook: Transaction not found: ' . $transactionId);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $paymentStatus = 'menunggu';
        $orderStatus = 'menunggu';
        $statusJaminan = $transaction->status_jaminan;

        if ($transactionStatus == 'capture') {
            if ($request->payment_type == 'credit_card') {
                if ($request->fraud_status == 'challenge') {
                    $paymentStatus = 'menunggu';
                    $orderStatus = 'menunggu';
                } else {
                    $paymentStatus = 'terverifikasi';
                    $orderStatus = 'diproses';
                    $statusJaminan = 'verified';
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $paymentStatus = 'terverifikasi';
            $orderStatus = 'diproses';
            $statusJaminan = 'verified';
        } else if ($transactionStatus == 'pending') {
            $paymentStatus = 'menunggu';
            $orderStatus = 'menunggu';
        } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $paymentStatus = 'ditolak';
            $orderStatus = 'dibatalkan';
            $statusJaminan = 'rejected';
            
            // Revert stock if canceled or expired
            if ($transaction->status_transaksi !== 'dibatalkan') {
                foreach ($transaction->details as $detail) {
                    if ($detail->product) {
                        $detail->product->increment('stok_tersedia', $detail->jumlah);
                    }
                }
            }
        }

        // Update Payment
        if ($transaction->payment) {
            $transaction->payment->update([
                'status_pembayaran' => $paymentStatus
            ]);
        }

        // Update Transaction
        $transaction->update([
            'status_transaksi' => $orderStatus,
            'status_jaminan' => $statusJaminan,
        ]);

        // Trigger notification
        try {
            if (class_exists(\App\Notifications\OrderStatusUpdated::class)) {
                $transaction->user->notify(new \App\Notifications\OrderStatusUpdated($transaction));
            } elseif (class_exists(\App\Notifications\OrderStatusNotification::class)) {
                $transaction->user->notify(new \App\Notifications\OrderStatusNotification($transaction, 'Status pesanan Anda #WB-' . str_pad($transaction->id, 8, '0', STR_PAD_LEFT) . ' telah diperbarui.'));
            }
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook: Notification failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}
