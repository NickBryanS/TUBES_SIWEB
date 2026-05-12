@extends('admin.layouts.admin')

@section('title', 'Dasbor Admin - Garkadala')
@section('sidebar-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
@endsection

@section('content')
<div class="admin-dashboard">
    <div class="admin-dash-header">
        <h1 class="admin-dash-title">Dashboard Utama</h1>
        <p class="admin-dash-subtitle">Selamat datang kembali, Admin. Berikut ringkasan operasional Garkadala hari ini.</p>
    </div>

    {{-- STAT CARDS --}}
    <div class="admin-stats" id="admin-stats">
        <div class="admin-stat-card">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-green"><i class="fas fa-desktop"></i></div>
                @if($persenPerubahan != 0)
                <span class="stat-change {{ $persenPerubahan >= 0 ? 'stat-change-up' : 'stat-change-down' }}">{{ $persenPerubahan >= 0 ? '+' : '' }}{{ $persenPerubahan }}% <i class="fas fa-arrow-{{ $persenPerubahan >= 0 ? 'up' : 'down' }}"></i></span>
                @endif
            </div>
            <div class="stat-label">TOTAL PENDAPATAN</div>
            <div class="stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-olive"><i class="fas fa-campground"></i></div>
                @if($menungguPesanan > 0)
                <span class="stat-badge stat-badge-amber">{{ $menungguPesanan }} Menunggu</span>
                @endif
            </div>
            <div class="stat-label">PENYEWAAN AKTIF</div>
            <div class="stat-value">{{ $penyewaanAktif }} <span class="stat-value-unit">Pesanan</span></div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-teal"><i class="fas fa-shield-halved"></i></div>
                @if($menungguVerifikasi > 0)
                <span class="stat-dot stat-dot-green"></span>
                @endif
            </div>
            <div class="stat-label">MENUNGGU VERIFIKASI</div>
            <div class="stat-value">{{ $menungguVerifikasi }} <span class="stat-value-unit">Berkas</span></div>
        </div>
        <div class="admin-stat-card stat-card-danger">
            <div class="stat-card-top">
                <div class="stat-icon stat-icon-red"><i class="fas fa-triangle-exclamation"></i></div>
                @if($stokTipis > 0)
                <span class="stat-badge stat-badge-red">Kritis</span>
                @endif
            </div>
            <div class="stat-label">PERINGATAN STOK TIPIS</div>
            <div class="stat-value stat-value-red">{{ $stokTipis }} <span class="stat-value-unit">Barang</span></div>
        </div>
    </div>

    {{-- MIDDLE ROW --}}
    <div class="admin-middle-grid">
        <div class="admin-card chart-card" id="chart-card">
            <div class="card-header">
                <h2 class="card-title">Tren Pendapatan</h2>
                <div class="chart-period-select"><span>7 Hari Terakhir</span> <i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="chart-container"><canvas id="revenueChart"></canvas></div>
            <div class="chart-labels">
                @foreach($chartData as $data)<span>{{ $data['label'] }}</span>@endforeach
            </div>
        </div>
        <div class="admin-card popular-card" id="popular-card">
            <h2 class="card-title popular-title">Barang Terlaris</h2>
            <div class="popular-list">
                @forelse($barangTerlaris as $item)
                @php $maxSewa = $barangTerlaris->first()->total_sewa ?? 1; $pct = round(($item->total_sewa / $maxSewa) * 100); @endphp
                <div class="popular-item">
                    <span class="popular-name">{{ $item->product->nama_produk ?? 'Produk' }}</span>
                    <div class="popular-bar-wrap">
                        <div class="popular-bar"><div class="popular-bar-fill" style="width:{{ $pct }}%;"></div></div>
                        <span class="popular-count"><strong>{{ $item->total_sewa }}</strong> Sewa</span>
                    </div>
                </div>
                @empty
                <div class="popular-empty"><i class="fas fa-box-open"></i><p>Belum ada data.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BOTTOM ROW --}}
    <div class="admin-bottom-grid">
        <div class="admin-card action-card" id="action-card">
            <div class="card-header">
                <h2 class="card-title">Transaksi Perlu Tindakan</h2>
                <a href="#" class="card-link">Lihat Semua</a>
            </div>
            <table class="action-table" id="action-table">
                <thead><tr><th>ID TRANSAKSI</th><th>PENYEWA</th><th>DURASI</th><th>STATUS</th><th>AKSI</th></tr></thead>
                <tbody>
                    @forelse($transaksiMenunggu as $t)
                    @php
                        $tMulai = \Carbon\Carbon::parse($t->tanggal_mulai);
                        $tSelesai = \Carbon\Carbon::parse($t->tanggal_selesai);
                        $durasi = $tMulai->diffInDays($tSelesai);
                        $initials = collect(explode(' ', $t->user->nama_lengkap ?? 'U'))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
                        $colors = ['#1a3a17','#2D5A27','#5a9e50','#f57f17','#1565c0','#c62828'];
                    @endphp
                    <tr>
                        <td class="action-id">#TRX-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="action-user">
                                <div class="action-avatar" style="background:{{ $colors[$t->id % count($colors)] }};">{{ $initials }}</div>
                                <div class="action-user-info">
                                    <span class="action-user-name">{{ $t->user->nama_lengkap ?? 'User' }}</span>
                                    <span class="action-user-type">Member</span>
                                </div>
                            </div>
                        </td>
                        <td class="action-duration">{{ $durasi }} Hari <span class="action-dates">({{ $tMulai->format('d') }}-{{ $tSelesai->format('d') }} {{ $tSelesai->translatedFormat('M') }}.)</span></td>
                        <td><span class="action-status status-menunggu">MENUNGGU</span></td>
                        <td class="action-btns">
                            <form action="{{ route('admin.transaksi.approve', $t->id) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="action-btn action-btn-approve" title="Setujui"><i class="fas fa-check"></i></button></form>
                            <form action="{{ route('admin.transaksi.reject', $t->id) }}" method="POST" style="display:inline;">@csrf<button type="submit" class="action-btn action-btn-reject" title="Tolak"><i class="fas fa-times"></i></button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="action-empty"><i class="fas fa-check-double"></i> Semua transaksi sudah ditangani.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-card schedule-card" id="schedule-card">
            <div class="card-header">
                <h2 class="card-title">Jadwal Pengembalian</h2>
                <span class="schedule-date">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
            </div>
            <div class="schedule-timeline">
                @forelse($jadwalPengembalian as $index => $jp)
                @php
                    $hours = ['10:00','13:30','15:00','16:30','09:00'];
                    $waktu = $hours[$index % count($hours)];
                    $items = $jp->details->map(fn($d)=>$d->product->nama_produk ?? 'Produk')->implode(', ');
                    $adaDenda = $jp->denda > 0;
                @endphp
                <div class="schedule-item">
                    <div class="schedule-time"><span class="schedule-hour">{{ $waktu }}</span><span class="schedule-wib">WIB</span></div>
                    <div class="schedule-dot-line">
                        <div class="schedule-dot {{ $adaDenda ? 'dot-warning' : 'dot-success' }}"></div>
                        @if(!$loop->last)<div class="schedule-line"></div>@endif
                    </div>
                    <div class="schedule-detail">
                        <span class="schedule-name">{{ $jp->user->nama_lengkap ?? 'User' }}</span>
                        <span class="schedule-items">{{ Str::limit($items, 35) }}</span>
                        <span class="schedule-badge {{ $adaDenda ? 'badge-denda' : 'badge-lunas' }}">{{ $adaDenda ? 'Denda Pending' : 'Lunas' }}</span>
                    </div>
                </div>
                @empty
                <div class="schedule-empty"><i class="fas fa-calendar-check"></i><p>Tidak ada jadwal pengembalian hari ini.</p></div>
                @endforelse
            </div>
            <button class="btn-verifikasi" id="btn-verifikasi">
                <span>Verifikasi Pengembalian Barang</span>
                <div class="btn-verifikasi-icon"><i class="fas fa-plus"></i></div>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const chartData = @json($chartData);

    function resizeCanvas() {
        const c = canvas.parentElement;
        canvas.width = c.offsetWidth;
        canvas.height = 220;
        drawChart();
    }

    function drawChart() {
        const w = canvas.width, h = canvas.height;
        const pad = {top:30,right:30,bottom:10,left:20};
        const cw = w-pad.left-pad.right, ch = h-pad.top-pad.bottom;
        ctx.clearRect(0,0,w,h);
        const vals = chartData.map(d=>d.value);
        const mx = Math.max(...vals, 1);
        const pts = vals.map((v,i)=>({x:pad.left+(i/(vals.length-1||1))*cw, y:pad.top+ch-(v/mx)*ch}));

        ctx.strokeStyle='rgba(0,0,0,0.06)'; ctx.lineWidth=1;
        for(let i=0;i<=4;i++){const y=pad.top+(ch/4)*i; ctx.beginPath();ctx.moveTo(pad.left,y);ctx.lineTo(w-pad.right,y);ctx.stroke();}

        if(pts.length<2) return;
        function curve(p){ctx.beginPath();ctx.moveTo(p[0].x,p[0].y);for(let i=0;i<p.length-1;i++){const cx1=p[i].x+(p[i+1].x-p[i].x)/3,cy1=p[i].y,cx2=p[i+1].x-(p[i+1].x-p[i].x)/3,cy2=p[i+1].y;ctx.bezierCurveTo(cx1,cy1,cx2,cy2,p[i+1].x,p[i+1].y);}}

        const grad=ctx.createLinearGradient(0,pad.top,0,h);
        grad.addColorStop(0,'rgba(45,90,39,0.25)');grad.addColorStop(1,'rgba(45,90,39,0.02)');
        curve(pts);ctx.lineTo(pts[pts.length-1].x,pad.top+ch);ctx.lineTo(pts[0].x,pad.top+ch);ctx.closePath();ctx.fillStyle=grad;ctx.fill();

        curve(pts);ctx.strokeStyle='#2D5A27';ctx.lineWidth=2.5;ctx.stroke();

        pts.forEach(pt=>{ctx.beginPath();ctx.arc(pt.x,pt.y,4,0,Math.PI*2);ctx.fillStyle='#2D5A27';ctx.fill();ctx.beginPath();ctx.arc(pt.x,pt.y,2,0,Math.PI*2);ctx.fillStyle='#fff';ctx.fill();});
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Animations
    document.querySelectorAll('.admin-stat-card').forEach((c,i)=>{c.style.opacity='0';c.style.transform='translateY(20px)';setTimeout(()=>{c.style.transition='all 0.5s cubic-bezier(0.16,1,0.3,1)';c.style.opacity='1';c.style.transform='translateY(0)';},100+i*80);});
    const obs = new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.style.transition='all 0.6s cubic-bezier(0.16,1,0.3,1)';e.target.style.opacity='1';e.target.style.transform='translateY(0)';obs.unobserve(e.target);}});},{threshold:0.1});
    document.querySelectorAll('.admin-card').forEach(c=>{c.style.opacity='0';c.style.transform='translateY(20px)';obs.observe(c);});

    const al=document.getElementById('admin-alert');
    if(al){setTimeout(()=>{al.style.opacity='0';al.style.transform='translateY(-10px)';setTimeout(()=>al.remove(),300);},4000);}
});
</script>
@endsection
