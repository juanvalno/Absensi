@if($userCan())
<a href="{{ $href }}" class="btn {{ $class }}" @if($tooltip) data-toggle="tooltip" title="{{ $tooltip }}" @endif>

    @if($icon)
    <i class="{{ $icon }}"></i>
    @endif

    {{ $slot }}
</a>
@endif
