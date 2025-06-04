<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationMemberLog;
use App\Models\NotificationMitraLog;
use Twilio\Rest\Client;

class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        Log::debug('Job constructed with data:', $data);
    }

    public function handle(): void
    {
        Log::info('📨 Processing Notification Payload: ' . json_encode($this->data));

        $isMember = isset($this->data['member_name']);
        $name = $isMember ? $this->data['member_name'] : ($this->data['mitra_name'] ?? 'Pengguna');

        // Simpan ke log table yang sesuai
        if ($isMember) {
            NotificationMemberLog::create([
                'type' => $this->data['type'] ?? null,
                'transaction_id' => $this->data['transaction_id'] ?? null,
                'member_id' => $this->data['member_id'] ?? null,
                'member_name' => $name,
                'phone' => $this->data['phone'] ?? null,
                'products' => json_encode($this->data['products'] ?? []),
                'status' => $this->data['status'] ?? null,
                'message' => $this->data['message'] ?? null,
            ]);
        } else {
            NotificationMitraLog::create([
                'type' => $this->data['type'] ?? null,
                'transaction_id' => $this->data['transaction_id'] ?? null,
                'mitra_id' => $this->data['mitra_id'] ?? null,
                'mitra_name' => $name,
                'phone' => $this->data['phone'] ?? null,
                'products' => json_encode($this->data['products'] ?? []),
                'status' => $this->data['status'] ?? null,
                'message' => $this->data['message'] ?? null,
            ]);
        }

        // Buat pesan WhatsApp
        $body = $isMember ? "🌾 *Koperasi Tani - Member*\n\n" : "🧺 *Koperasi Tani - Mitra*\n\n";
        $body .= "Halo, {$name}!\n";

        switch ($this->data['type']) {
            case 'transaction_created':
                $body .= "Pesanan dengan kode *{$this->data['transaction_code']}* telah berhasil dibuat.\n\n";
                break;
            case 'transaction_updated':
                $body .= "Pesanan dengan kode *{$this->data['transaction_code']}* telah diperbarui.\n\n";
                break;
            case 'transaction_deleted':
                $body .= "Pesanan dengan kode *{$this->data['transaction_code']}* telah dibatalkan.\n\n";
                break;
            case 'transaction_status_updated':
                $body .= "Status pesanan *{$this->data['transaction_code']}* telah diperbarui.\n\n";
                break;
            default:
                $body .= "Pesanan telah diperbarui.\n\n";
                break;
        }

        if (!empty($this->data['products'])) {
            $body .= "*Detail Produk:*\n";
            foreach ($this->data['products'] as $product) {
                $body .= "- {$product['product_name']} x{$product['quantity']} (Rp " . number_format($product['subtotal'], 0, ',', '.') . ")\n";
            }
            $body .= "\n";
        }

        if (!empty($this->data['status'])) {
            $body .= "Status: *" . strtoupper($this->data['status']) . "*\n\n";
        }

        $body .= $isMember
            ? "Terima kasih sudah berbelanja di Koperasi Tani! 🙏"
            : "Terima kasih telah bertransaksi sebagai Mitra Koperasi Tani! 🙌";

        try {
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

            $message = $twilio->messages->create(
                "whatsapp:+628978619504", // WA dev tujuan sementara
                [
                    "from" => "whatsapp:+14155238886",
                    "body" => $body
                ]
            );

            Log::info('✅ WhatsApp message sent', [
                'to' => '+628978619504',
                'sid' => $message->sid,
                'target' => $isMember ? 'Member' : 'Mitra'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Gagal mengirim WA: ' . $e->getMessage());
        }
    }
}
