@extends('layouts.app')

@section('title', 'Detail Pesanan - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pesanan-detail.css') }}">
@endsection

@php
    $isPickup = $transaction->metode_pengambilan === 'pickup';

    if ($isPickup) {
        // 4-step stepper untuk Ambil di Toko
        $statusMap = [
            'menunggu'       => 0,
            'menunggu_admin' => 1,
            'diproses'       => 2,
            'dikirim'        => 2,
            'selesai'        => 3,
            'dibatalkan'     => -1,
        ];
        $steps = [
            ['title' => 'Dipesan',        'icon' => 'fas fa-check'],
            ['title' => 'Dibayar',        'icon' => 'fas fa-credit-card'],
            ['title' => 'Dipinjam',       'icon' => 'fas fa-box-open'],
            ['title' => 'Dikembalikan',   'icon' => 'fas fa-undo-alt'],
        ];
        $currentStep = $statusMap[$transaction->status_transaksi] ?? 0;
    } else {
        // 4-step stepper untuk Diantar
        $statusMap = [
            'menunggu'       => 0,
            'menunggu_admin' => 0,
            'diproses'       => 1,
            'dikirim'        => 2,
            'selesai'        => 4,
            'dibatalkan'     => -1,
        ];
        $steps = [
            ['title' => 'Pesanan Dibuat',       'icon' => 'fas fa-check'],
            ['title' => 'Disiapkan',             'icon' => 'fas fa-box'],
            ['title' => 'Sedang Diantar',        'icon' => 'fas fa-truck'],
            ['title' => 'Barang Diterima',       'icon' => 'fas fa-box-open'],
            ['title' => 'Pesanan Selesai',       'icon' => 'fas fa-flag-checkered'],
        ];
        $currentStep = $statusMap[$transaction->status_transaksi] ?? 0;

        if ($transaction->status_transaksi === 'dikirim' && $transaction->barang_diterima) {
            $currentStep = 3;
        }
        
        // Logika tambahan jika barang sudah disiapkan dan tinggal dikirim
        if ($transaction->status_transaksi === 'diproses' && $transaction->siap_kirim) {
            $steps[1]['title'] = 'Siap Dikirim';
        }
    }
@endphp

@section('content')
<div class="pesanan-page">
    <div class="pesanan-container">
        <!-- BREADCRUMB -->
        <div class="pesanan-breadcrumb">
            <a href="/riwayat">Pesanan Saya</a>
            <i class="fas fa-chevron-right"></i>
            <span>Pelacakan</span>
        </div>

        <div class="pesanan-header-row">
            <h1 class="pesanan-title">Detail Pesanan #GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</h1>
        </div>

        {{-- ALERT MESSAGES --}}
        @if(session('success'))
            <div style="background: rgba(46,204,113,0.15); border: 1px solid rgba(46,204,113,0.3); color: #2ecc71; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 0.95rem;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.3); color: #e74c3c; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 0.95rem;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('info'))
            <div style="background: rgba(52,152,219,0.15); border: 1px solid rgba(52,152,219,0.3); color: #3498db; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 0.95rem;">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
        @endif

        {{-- STATUS DIBATALKAN --}}
        @if($transaction->status_transaksi === 'dibatalkan')
            <div style="background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); padding: 20px; border-radius: 12px; margin-bottom: 24px; text-align: center;">
                <i class="fas fa-ban" style="font-size: 2rem; color: #e74c3c;"></i>
                <h3 style="color: #e74c3c; margin-top: 8px;">Pesanan Dibatalkan</h3>
            </div>
        @else
            @if($isPickup)
                {{-- ===== 4-STEP STEPPER (PICKUP / Ambil di Toko) ===== --}}
                <div class="tracking-stepper tracking-stepper--pickup" id="tracking-stepper">
                    @foreach($steps as $index => $step)
                        @if($index > 0)
                            <div class="track-line {{ $index <= $currentStep ? 'completed-line' : '' }}"></div>
                        @endif
                        <div class="track-step {{ $index < $currentStep ? 'completed' : ($index === $currentStep ? 'active' : '') }}">
                            <div class="track-circle">
                                @if($index < $currentStep)
                                    <i class="fas fa-check"></i>
                                @else
                                    <span class="track-number">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <div class="track-info">
                                <span class="track-step-title">{{ $step['title'] }}</span>
                                @if($index === 0)
                                    <span class="track-step-date">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                                @elseif($index === $currentStep && $index > 0)
                                    <span class="track-step-date">Saat ini</span>
                                @elseif($index === 3 && $currentStep >= 3 && $transaction->tanggal_kembali_aktual)
                                    <span class="track-step-date">{{ $transaction->tanggal_kembali_aktual->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- ===== 5-STEP STEPPER (DELIVERY / Diantar) ===== --}}
                <div class="tracking-stepper" id="tracking-stepper">
                    @foreach($steps as $index => $step)
                        @if($index > 0)
                            <div class="track-line {{ $index <= $currentStep ? 'completed-line' : '' }}"></div>
                        @endif
                        <div class="track-step {{ $index < $currentStep ? 'completed' : ($index === $currentStep ? 'active' : '') }}">
                            <div class="track-circle">
                                @if($index < $currentStep)
                                    <i class="fas fa-check"></i>
                                @elseif($index === $currentStep)
                                    <i class="{{ $step['icon'] }}"></i>
                                @endif
                            </div>
                            <div class="track-info">
                                <span class="track-step-title">{{ $step['title'] }}</span>
                                @if($index === 0)
                                    <span class="track-step-date">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                                @elseif($index === $currentStep && $index > 0)
                                    <span class="track-step-date">Saat ini</span>
                                @elseif($index === 4 && $currentStep >= 4 && $transaction->tanggal_kembali_aktual)
                                    <span class="track-step-date">{{ $transaction->tanggal_kembali_aktual->format('d M Y') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <!-- MAIN GRID -->
        <div class="pesanan-grid">
            <!-- LEFT -->
            <div class="pesanan-left">
                {{-- LIVE TRACKING CARD (KHUSUS STATUS DIKIRIM) --}}
                @if($transaction->status_transaksi === 'dikirim' && $transaction->metode_pengambilan === 'deliver')
                    @if(!$transaction->barang_diterima)
                    <div class="delivery-tracking-card">
                        <div class="delivery-tracking-header">
                            <div class="delivery-pulse-indicator">
                                <div class="pulse-ring"></div>
                                <div class="pulse-dot"></div>
                            </div>
                            <h3>Pesanan Sedang Diantar</h3>
                        </div>
                        <div class="delivery-tracking-body">
                            <div class="delivery-illustration">
                                <i class="fas fa-truck-fast" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                            </div>
                            <div class="delivery-info">
                                <h4>Pengantaran Langsung Gardakala</h4>
                                <p>Barang Anda sedang dalam perjalanan menuju lokasi Anda dan diantar langsung oleh tim internal kami.</p>
                                
                                @php
                                    $waMessage = urlencode("Halo Gardakala, saya ingin menanyakan status pengantaran untuk pesanan #GK-" . str_pad($transaction->id, 4, '0', STR_PAD_LEFT) . ". Apakah kurir sudah dekat?");
                                    $waNumber = "6287715778007"; // Nomor WhatsApp Gardakala (sama dengan Hubungi Kami)
                                @endphp
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank" class="btn-hubungi-kurir" style="flex: 1; text-align: center;">
                                        <i class="fab fa-whatsapp"></i> Hubungi Tim Pengantar
                                    </a>
                                    <form action="{{ route('pesanan.terima', $transaction->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah menerima semua barang pesanan dengan baik?');" style="flex: 1; margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-action-primary" style="width: 100%; border: none; padding: 12px; border-radius: 8px; cursor: pointer;">
                                            <i class="fas fa-box-open"></i> Pesanan Diterima
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="delivery-tracking-card" style="background: rgba(46, 204, 113, 0.05); border-color: rgba(46, 204, 113, 0.2);">
                        <div class="delivery-tracking-header" style="background: rgba(46, 204, 113, 0.1);">
                            <h3><i class="fas fa-box-open" style="color: #2ecc71;"></i> Barang Telah Diterima</h3>
                        </div>
                        <div class="delivery-tracking-body">
                            <div class="delivery-info">
                                <h4>Masa Sewa Sedang Berjalan</h4>
                                <p>Terima kasih telah mengonfirmasi penerimaan barang. Selamat menikmati perlengkapan dari Gardakala Outdoor! Jangan lupa mengembalikan barang pada tanggal selesai yang tertera.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                @endif

                {{-- STATUS PERPANJANGAN BADGE --}}
                @if($transaction->status_perpanjangan === 'pending')
                    <div style="background: rgba(241,196,15,0.1); border: 1px solid rgba(241,196,15,0.3); padding: 14px 20px; border-radius: 10px; margin-bottom: 16px; color: #f1c40f; font-size: 0.9rem;">
                        <i class="fas fa-clock"></i> Pengajuan perpanjangan <strong>{{ $transaction->perpanjangan_hari }} hari</strong> sedang menunggu persetujuan admin.
                    </div>
                @elseif($transaction->status_perpanjangan === 'approved')
                    <div style="background: rgba(46,204,113,0.1); border: 1px solid rgba(46,204,113,0.3); padding: 14px 20px; border-radius: 10px; margin-bottom: 16px; color: #2ecc71; font-size: 0.9rem;">
                        <i class="fas fa-check-circle"></i> Perpanjangan <strong>{{ $transaction->perpanjangan_hari }} hari</strong> telah disetujui.
                    </div>
                @elseif($transaction->status_perpanjangan === 'rejected')
                    <div style="background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); padding: 14px 20px; border-radius: 10px; margin-bottom: 16px; color: #e74c3c; font-size: 0.9rem;">
                        <i class="fas fa-times-circle"></i> Pengajuan perpanjangan ditolak.
                    </div>
                @endif

                <!-- RENTAL ITEMS (Dinamis dari Database) -->
                <div class="pesanan-section">
                    <h3><i class="fas fa-box"></i> Rincian Alat Sewa</h3>
                    <div class="pesanan-items-list">
                        @foreach($transaction->details as $detail)
                        <div class="pesanan-item">
                            <div class="pesanan-item-img">
                                <img src="{{ asset($detail->product->url_gambar ?? 'images/placeholder.png') }}" alt="{{ $detail->product->nama_produk }}">
                            </div>
                            <div class="pesanan-item-info">
                                <h4>{{ $detail->product->nama_produk }}</h4>
                                <p>{{ $detail->jumlah }} unit &middot; {{ $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai) }} Hari</p>
                            </div>
                            <span class="pesanan-item-price">
                                Rp {{ number_format($detail->product->harga_sewa * $detail->jumlah * $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai), 0, ',', '.') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- PAYMENT DETAILS (Dinamis dari Database) -->
                <div class="pesanan-section">
                    <h3><i class="fas fa-receipt"></i> Rincian Pembayaran</h3>
                    <div class="pesanan-payment-details">
                        @php
                            $subtotal = 0;
                            foreach($transaction->details as $detail) {
                                $subtotal += $detail->product->harga_sewa * $detail->jumlah * $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai);
                            }
                            $biayaAdmin = 0;
                        @endphp
                        <div class="payment-line">
                            <span>Subtotal Sewa</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->ongkos_kirim > 0)
                        <div class="payment-line">
                            <span>Ongkos Kirim ({{ $transaction->jarak_tempuh }} km)</span>
                            <span>Rp {{ number_format($transaction->ongkos_kirim, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($transaction->denda > 0)
                        <div class="payment-line" style="color: #e74c3c;">
                            <span><i class="fas fa-exclamation-triangle"></i> Denda Keterlambatan</span>
                            <span>Rp {{ number_format($transaction->denda, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="payment-line total">
                            <span>Total Bayar</span>
                            <span>Rp {{ number_format($transaction->total_biaya + $transaction->denda, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- DENDA INFO (FR-USR-034) --}}
                @if($transaction->denda > 0)
                <div class="pesanan-section" style="border: 1px solid rgba(231,76,60,0.3); background: rgba(231,76,60,0.05);">
                    <h3 style="color: #e74c3c;"><i class="fas fa-exclamation-triangle"></i> Informasi Denda</h3>
                    <div class="pesanan-payment-details">
                        <div class="payment-line">
                            <span>Tanggal Seharusnya Kembali</span>
                            <span>{{ $transaction->tanggal_selesai->format('d M Y') }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Tanggal Kembali Aktual</span>
                            <span>{{ $transaction->tanggal_kembali_aktual ? $transaction->tanggal_kembali_aktual->format('d M Y') : '-' }}</span>
                        </div>
                        @if($transaction->tanggal_kembali_aktual)
                        <div class="payment-line" style="color: #e74c3c;">
                            <span>Keterlambatan</span>
                            <span>{{ $transaction->tanggal_kembali_aktual->diffInDays($transaction->tanggal_selesai) }} hari</span>
                        </div>
                        @endif
                        <div class="payment-line total" style="color: #e74c3c;">
                            <span>Total Denda (50% x harga/hari)</span>
                            <span>Rp {{ number_format($transaction->denda, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- RIGHT -->
            <div class="pesanan-right">
                <!-- INFO PESANAN -->
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-info-circle"></i> Info Pesanan</h3>
                    <div class="address-info">
                        <div class="payment-line">
                            <span>Status</span>
                            <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $transaction->status_transaksi) }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Metode</span>
                            <span>{{ $transaction->metode_pengambilan === 'pickup' ? 'Ambil di Toko' : 'Diantar' }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Tanggal Mulai</span>
                            <span>{{ $transaction->tanggal_mulai->format('d M Y') }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Tanggal Selesai</span>
                            <span>{{ $transaction->tanggal_selesai->format('d M Y') }}</span>
                        </div>
                        @if($transaction->payment)
                        <div class="payment-line">
                            <span>Pembayaran</span>
                            <span>{{ str_replace('_', ' ', ucfirst($transaction->payment->metode_pembayaran)) }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Status Bayar</span>
                            <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $transaction->payment->status_pembayaran) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ALAMAT PENGIRIMAN -->
                @if($transaction->metode_pengambilan === 'deliver' && $transaction->alamat_pengiriman)
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
                    <div class="address-info">
                        <p>{{ $transaction->alamat_pengiriman }}</p>
                    </div>
                </div>
                @endif

                <!-- JAMINAN INFO (FR-USR-030/032) -->
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-shield-alt"></i> Info Jaminan</h3>
                    <div class="address-info">
                        <div class="payment-line">
                            <span>Jenis Jaminan</span>
                            <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $transaction->jenis_jaminan ?? 'ktp') }}</span>
                        </div>
                        <div class="payment-line">
                            <span>Status</span>
                            <span>
                                @if($transaction->status_jaminan === 'verified')
                                    <span style="color: #2ecc71;"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                                @elseif($transaction->status_jaminan === 'rejected')
                                    <span style="color: #e74c3c;"><i class="fas fa-times-circle"></i> Ditolak</span>
                                @else
                                    <span style="color: #f1c40f;"><i class="fas fa-clock"></i> Menunggu</span>
                                @endif
                            </span>
                        </div>
                        @if($transaction->foto_ktp)
                        <div class="payment-line">
                            <span>Foto KTP</span>
                            <span style="color: #2ecc71;"><i class="fas fa-check"></i> Sudah diunggah</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-cogs"></i> Aksi</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
                        {{-- Tombol Bayar Sekarang (Midtrans QRIS) --}}
                        @if($transaction->status_transaksi === 'menunggu' && $transaction->payment && $transaction->payment->metode_pembayaran === 'qris')
                            @if(isset($snapToken) && $snapToken)
                            <button id="pay-button"
                                    style="display: block; width: 100%; border: none; text-align: center; padding: 12px; background: linear-gradient(135deg, #2d5a27, #3a7d32); color: #fff; border-radius: 10px; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
                                <i class="fas fa-wallet"></i> Bayar Sekarang (QRIS)
                            </button>
                            @else
                            <div style="padding: 10px; background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); border-radius: 8px; color: #e74c3c; font-size: 0.85rem; text-align: center;">
                                <i class="fas fa-exclamation-circle"></i> Gagal memuat token pembayaran.
                            </div>
                            @endif
                        @endif

                        {{-- Tombol Perpanjangan (FR-USR-033) --}}
                        @if(in_array($transaction->status_transaksi, ['diproses', 'dikirim']) && $transaction->status_perpanjangan !== 'pending')
                        <a href="{{ route('perpanjangan.form', $transaction->id) }}"
                           style="display: block; text-align: center; padding: 12px; background: linear-gradient(135deg, #e8a838, #d4943a); color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600; transition: transform 0.2s;">
                            <i class="fas fa-calendar-plus"></i> Ajukan Perpanjangan
                        </a>
                        @endif

                        {{-- Tombol Konfirmasi Pengembalian (FR-USR-034) --}}
                        @if((in_array($transaction->status_transaksi, ['diproses', 'dikirim']) && $transaction->metode_pengambilan === 'pickup') || ($transaction->status_transaksi === 'dikirim' && $transaction->metode_pengambilan === 'deliver' && $transaction->barang_diterima))
                        <form action="{{ route('pesanan.pengembalian', $transaction->id) }}" method="POST"
                              onsubmit="return confirm('Konfirmasi bahwa barang telah dikembalikan?');"
                              style="margin: 0;">
                            @csrf
                            <input type="hidden" name="tanggal_kembali_aktual" value="{{ now()->format('Y-m-d') }}">
                            <button type="submit"
                                    style="width: 100%; padding: 12px; background: rgba(46,204,113,0.15); border: 1px solid rgba(46,204,113,0.3); color: #2ecc71; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                                <i class="fas fa-check-circle"></i> Konfirmasi Barang Dikembalikan
                            </button>
                        </form>
                        @endif

                        {{-- Tombol Batal Pesanan (FR-USR-026) --}}
                        @if(in_array($transaction->status_transaksi, ['menunggu', 'menunggu_admin']))
                            <button type="button" onclick="confirmBatal('{{ $transaction->id }}', '{{ $transaction->status_transaksi }}')"
                                    style="width: 100%; padding: 12px; background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.3); color: #e74c3c; border-radius: 10px; font-size: 0.95rem; font-weight: 600; cursor: pointer; transition: background 0.3s; margin-bottom: 10px;">
                                <i class="fas fa-ban"></i> Batalkan Pesanan
                            </button>

                            <form action="{{ route('pesanan.batal', $transaction->id) }}" method="POST" id="form-batal-pesanan-{{ $transaction->id }}" style="display: none;">
                                @csrf
                            </form>
                        @endif

                        {{-- Kembali ke Riwayat --}}
                        <a href="{{ route('riwayat') }}"
                           style="display: block; text-align: center; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); border-radius: 10px; text-decoration: none; font-size: 0.9rem;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                        </a>
                    </div>
                </div>
            </div>
        </div>
</div>

{{-- Modal Refund --}}
@if($transaction->status_transaksi === 'menunggu_admin')
<div id="modal-refund" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; width: 100%; max-width: 450px; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 8px; color: #333; font-size: 1.2rem;"><i class="fas fa-undo"></i> Pengembalian Dana (Refund)</h3>
        <p style="color: #666; font-size: 0.85rem; margin-bottom: 20px; line-height: 1.5;">Pesanan ini sudah dibayar. Silakan isi data rekening bank atau e-wallet Anda untuk proses pengembalian dana.</p>
        
        <form action="{{ route('pesanan.batal', $transaction->id) }}" method="POST" id="form-refund-modal">
            @csrf
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #444;">Nama Bank / E-Wallet <span style="color:red;">*</span></label>
                <input type="text" name="bank_pengembalian" required placeholder="Contoh: BCA / GoPay" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #444;">Nomor Rekening / No HP <span style="color:red;">*</span></label>
                <input type="text" name="rekening_pengembalian" required placeholder="Masukkan nomor rekening..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: #444;">Nama Pemilik Rekening <span style="color:red;">*</span></label>
                <input type="text" name="atas_nama_pengembalian" required placeholder="Sesuai buku tabungan" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeRefundModal()" style="padding: 10px 16px; background: white; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 600; color: #555; transition: background 0.2s;">Batal</button>
                <button type="submit" style="padding: 10px 16px; background: #e74c3c; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; color: white; transition: background 0.2s;">Konfirmasi Pembatalan</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
@if(isset($snapToken) && $snapToken)
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.getElementById('pay-button')?.addEventListener('click', function(e) {
    e.preventDefault();
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.reload();
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert("Pembayaran gagal! Silakan coba lagi.");
        },
        onClose: function() {
            alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi.');
        }
    });
});
</script>
@endif
<script>
function confirmBatal(id, status) {
    if (status === 'menunggu_admin') {
        const modal = document.getElementById('modal-refund');
        if (modal) {
            modal.style.display = 'flex';
        }
    } else {
        if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
            document.getElementById('form-batal-pesanan-' + id).submit();
        }
    }
}

function closeRefundModal() {
    const modal = document.getElementById('modal-refund');
    if (modal) {
        modal.style.display = 'none';
    }
}
</script>
@endsection
