<fieldset id="ci_profiler_info">
    <legend style="color:#900;">BENCHMARKS</legend>
    <table style='width:100%'>
        @foreach ($profile as $key => $val)
        <tr>
            <td style='padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;'>{!! ucwords(str_replace(array('_', '-'), ' ', $key)) !!}</td>
            <td style='padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;'>{!! $val !!}</td>
        </tr>
        @endforeach
    </table>

    <legend class="mt-2" style="color:#995300;">CLASS/METHOD</legend>
    <table style='width:100%'>
        <tr>
            <td style='padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;'>Class</td>
            <td style='padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;'>{!! $class !!}</td>
        </tr>
        <tr>
            <td style='padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;'>Method</td>
            <td style='padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;'>{!! $method !!}</td>
        </tr>
    </table>

    <legend class="mt-2" style="color:#5a0099;">RAM</legend>
    <table style='width:100%'>
        <tr>
            <td style='padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;'>Ram</td>
            <td style='padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;'>{!! $ram !!}</td>
        </tr>
    </table>
</fieldset>