@extends('superadmin.layouts.superadmin')

@section('title', 'Log Aktivitas - Super Admin GKDL')
@section('sidebar-log', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-activity-log.css') }}">
@endsection

@section('content')
<div class="sa-log-page">
    {{-- HEADER --}}
    <div class="sa-page-header">
        <div>
            <span class="sa-page-label">Audit Trail</span>
            <h1 class="sa-page-title">Log Aktivitas Admin</h1>
            <p class="sa-page-subtitle">Riwayat otomatis setiap aksi yang dilakukan admin pada sistem.</p>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="sa-log-filters">
        <form method="GET" action="{{ route('superadmin.activity-log') }}" class="sa-filter-form">
            <div class="sa-search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Cari deskripsi atau nama admin..." value="{{ request('search') }}">
            </div>
            <select name="aksi" class="sa-filter-select" onchange="this.form.submit()">
                <option value="">Semua Aksi</option>
                @foreach($aksiList as $aksi)
                <option value="{{ $aksi }}" {{ request('aksi') === $aksi ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $aksi)) }}
                </option>
                @endforeach
            </select>
            @if(request('search') || request('aksi'))
            <a href="{{ route('superadmin.activity-log') }}" class="sa-btn-secondary sa-btn-sm"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>

    {{-- LOG TABLE --}}
    <div class="sa-card sa-log-table-card">
        <table class="sa-activity-table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>ADMIN</th>
                    <th>AKSI</th>
                    <th>DESKRIPSI</th>
                    <th>IP ADDRESS</th>
                    <th>WAKTU</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                @php
                    $aksiColors = [
                        'tambah_admin' => 'badge-green',
                        'update_admin' => 'badge-blue',
                        'hapus_admin' => 'badge-red',
                        'toggle_status_admin' => 'badge-orange',
                        'export_pdf' => 'badge-purple',
                        'export_excel' => 'badge-purple',
                        'backup_database' => 'badge-gold',
                        'update_pengaturan' => 'badge-blue',
                        'tambah_rekening' => 'badge-green',
                        'update_rekening' => 'badge-blue',
                        'hapus_rekening' => 'badge-red',
                        'toggle_rekening' => 'badge-orange',
                        'konfirmasi_transaksi' => 'badge-green',
                        'tolak_transaksi' => 'badge-red',
                    ];
                    $badgeClass = $aksiColors[$log->aksi] ?? 'badge-default';
                    $aksiIcons = [
                        'tambah_admin' => 'fa-user-plus',
                        'update_admin' => 'fa-user-pen',
                        'hapus_admin' => 'fa-user-minus',
                        'toggle_status_admin' => 'fa-user-lock',
                        'export_pdf' => 'fa-file-pdf',
                        'export_excel' => 'fa-file-excel',
                        'backup_database' => 'fa-database',
                        'update_pengaturan' => 'fa-gear',
                        'tambah_rekening' => 'fa-credit-card',
                        'update_rekening' => 'fa-credit-card',
                        'hapus_rekening' => 'fa-credit-card',
                        'toggle_rekening' => 'fa-toggle-on',
                    ];
                    $icon = $aksiIcons[$log->aksi] ?? 'fa-clipboard';
                @endphp
                <tr>
                    <td class="sa-log-num">{{ $logs->firstItem() + $i }}</td>
                    <td>
                        <div class="sa-log-user">
                            <div class="sa-log-avatar">
                                {{ strtoupper(substr($log->user->nama_lengkap ?? 'S', 0, 1)) }}
                            </div>
                            <span>{{ $log->user->nama_lengkap ?? 'System' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="sa-aksi-badge {{ $badgeClass }}">
                            <i class="fas {{ $icon }}"></i>
                            {{ ucwords(str_replace('_', ' ', $log->aksi)) }}
                        </span>
                    </td>
                    <td class="sa-log-desc">{{ $log->deskripsi }}</td>
                    <td class="sa-log-ip">{{ $log->ip_address ?? '-' }}</td>
                    <td class="sa-log-time">
                        <span title="{{ $log->created_at->format('d M Y, H:i:s') }}">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="sa-empty-text">
                        <div class="sa-empty-state">
                            <i class="fas fa-clipboard-list"></i>
                            <p>Belum ada aktivitas yang tercatat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="sa-pagination">
            {{ $logs->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Entrance animations
    document.querySelectorAll('.sa-card, .sa-log-filters').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(12px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1'; c.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
});
</script>
@endsection
