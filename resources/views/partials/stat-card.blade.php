{{-- Partials: Stat Card (Dashboard)
    @param $stat - array with: icon, iconClass, label, number, badge (optional), badgeClass (optional)
--}}
<div class="stat-card">
    <div class="stat-card-icon {{ $stat['iconClass'] }}"><i class="{{ $stat['icon'] }}"></i></div>
    <div class="stat-card-info">
        <span class="stat-card-label">{{ $stat['label'] }}</span>
        <span class="stat-card-number">{{ $stat['number'] }}</span>
        @if(!empty($stat['badge']))
            <span class="stat-card-badge {{ $stat['badgeClass'] ?? '' }}">{{ $stat['badge'] }}</span>
        @endif
    </div>
</div>
