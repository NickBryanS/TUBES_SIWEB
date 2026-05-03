@extends('layouts.app')

@section('title', 'Detail Pesanan - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pesanan-detail.css') }}">
@endsection

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
            <h1 class="pesanan-title">Detail Pesanan #GK-2024-8891</h1>
            <button class="btn-invoice"><i class="fas fa-download"></i> Unduh Invoice</button>
        </div>

        <!-- TRACKING STEPPER -->
        <div class="tracking-stepper" id="tracking-stepper">
            <div class="track-step completed">
                <div class="track-circle"><i class="fas fa-check"></i></div>
                <div class="track-info">
                    <span class="track-step-title">Pesanan Dibuat</span>
                    <span class="track-step-date">13 Okt 2024, 10.00</span>
                </div>
            </div>
            <div class="track-line completed-line"></div>
            <div class="track-step completed">
                <div class="track-circle"><i class="fas fa-check"></i></div>
                <div class="track-info">
                    <span class="track-step-title">Barang Disiapkan</span>
                    <span class="track-step-date">13 Okt 2024, 14.00</span>
                </div>
            </div>
            <div class="track-line completed-line"></div>
            <div class="track-step active">
                <div class="track-circle"><i class="fas fa-truck"></i></div>
                <div class="track-info">
                    <span class="track-step-title">Barang Sedang Diantar</span>
                    <span class="track-step-date">~60%</span>
                </div>
            </div>
            <div class="track-line"></div>
            <div class="track-step">
                <div class="track-circle"></div>
                <div class="track-info">
                    <span class="track-step-title">Barang Diterima</span>
                </div>
            </div>
            <div class="track-line"></div>
            <div class="track-step">
                <div class="track-circle"></div>
                <div class="track-info">
                    <span class="track-step-title">Selesai</span>
                </div>
            </div>
        </div>

        <!-- MAIN GRID -->
        <div class="pesanan-grid">
            <!-- LEFT -->
            <div class="pesanan-left">
                <!-- STATUS BANNER -->
                <div class="status-banner" id="status-banner">
                    <div class="status-banner-left">
                        <span class="status-banner-label">Status Terakhir</span>
                        <h2>Paket sedang dalam perjalanan oleh kurir internal.</h2>
                        <p>Estimasi kedatangan hari ini sebelum pukul 18:00 WIB. Pastikan ada orang di rumah untuk menerima barang.</p>
                        <button class="btn-confirm-receive">
                            Konfirmasi Barang Diterima <i class="fas fa-check-circle"></i>
                        </button>
                    </div>
                    <div class="status-banner-img">
                        <img src="{{ asset('images/mountain-adventure.png') }}" alt="Delivery">
                    </div>
                </div>

                <!-- RENTAL ITEMS -->
                <div class="pesanan-section">
                    <h3><i class="fas fa-box"></i> Rincian Alat Sewa</h3>
                    <div class="pesanan-items-list">
                        <div class="pesanan-item">
                            <div class="pesanan-item-img">
                                <img src="{{ asset('images/tent-expedition.png') }}" alt="Tenda Ergo">
                            </div>
                            <div class="pesanan-item-info">
                                <h4>Tenda Ergo Outdoor 4P</h4>
                                <p>3 Hari</p>
                            </div>
                            <span class="pesanan-item-price">Rp 186rs</span>
                        </div>
                        <div class="pesanan-item">
                            <div class="pesanan-item-img">
                                <img src="{{ asset('images/backpack-product.png') }}" alt="Carrier Osprey">
                            </div>
                            <div class="pesanan-item-info">
                                <h4>Carrier Osprey 65L</h4>
                                <p>3 Hari</p>
                            </div>
                            <span class="pesanan-item-price">Rp 120rs</span>
                        </div>
                    </div>
                </div>

                <!-- PAYMENT DETAILS -->
                <div class="pesanan-section">
                    <h3><i class="fas fa-receipt"></i> Rincian Pembayaran</h3>
                    <div class="pesanan-payment-details">
                        <div class="payment-line">
                            <span>Subtotal Sewa</span>
                            <span>Rp 270.000</span>
                        </div>
                        <div class="payment-line">
                            <span>Biaya Layanan</span>
                            <span>Rp 10.000</span>
                        </div>
                        <div class="payment-line">
                            <span>Ongkos Kirim</span>
                            <span>Rp 25.000</span>
                        </div>
                        <div class="payment-line total">
                            <span>Total Bayar</span>
                            <span>Rp 305.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="pesanan-right">
                <!-- SHIPPING ADDRESS -->
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
                    <div class="address-info">
                        <strong>Budi Setiawan</strong>
                        <p>Jl. Kemang Raya No. 45,<br>RT 03/RW 02</p>
                        <p>Mampang Prapatan,<br>Jakarta Selatan,<br>DKI Jakarta, 12730</p>
                        <p class="phone-number"><i class="fas fa-phone"></i> +62 812-3456-7890</p>
                    </div>
                </div>

                <!-- COURIER -->
                <div class="pesanan-section-card">
                    <h3><i class="fas fa-truck"></i> Kurir Pengantar</h3>
                    <div class="courier-info">
                        <div class="courier-avatar">
                            <img src="{{ asset('images/delivery-courier.png') }}" alt="Agus Prasetyo">
                        </div>
                        <div>
                            <strong>Agus Prasetyo</strong>
                            <span class="courier-role">KURIR INTERNAL</span>
                        </div>
                    </div>
                    <div class="courier-actions">
                        <button class="btn-courier btn-chat">
                            <i class="fas fa-comment"></i> Chat Kurir
                        </button>
                        <button class="btn-courier btn-call">
                            <i class="fas fa-phone"></i> Hubungi Kurir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
