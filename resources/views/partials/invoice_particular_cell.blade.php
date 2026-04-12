@php
    use App\Support\PendingInvoiceDue;
    $particular = PendingInvoiceDue::particular($invoice);
@endphp
<td class="particular-cell align-top">
    @if ($particular === '—')
        —
    @else
        {!! nl2br(e($particular)) !!}
    @endif
</td>
