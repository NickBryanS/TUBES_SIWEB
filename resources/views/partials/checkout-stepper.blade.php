{{-- Partials: Checkout Stepper
    @param $currentStep (1, 2, or 3)
--}}
<div class="checkout-stepper" id="checkout-stepper">
    <div class="step {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}">
        <div class="step-circle">
            @if($currentStep > 1)
                <i class="fas fa-check"></i>
            @else
                <span>1</span>
            @endif
        </div>
        <span class="step-label">RINGKASAN</span>
    </div>
    <div class="step-line {{ $currentStep > 1 ? 'active-line' : '' }}"></div>
    <div class="step {{ $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : '' }}">
        <div class="step-circle">
            @if($currentStep > 2)
                <i class="fas fa-check"></i>
            @else
                <span>2</span>
            @endif
        </div>
        <span class="step-label">PEMBAYARAN</span>
    </div>
    <div class="step-line {{ $currentStep > 2 ? 'active-line' : '' }}"></div>
    <div class="step {{ $currentStep >= 3 ? 'active' : '' }}">
        <div class="step-circle"><span>3</span></div>
        <span class="step-label">SELESAI</span>
    </div>
</div>
