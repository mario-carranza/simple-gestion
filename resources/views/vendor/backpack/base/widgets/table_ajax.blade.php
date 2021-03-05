@php
$columnNames = $widget['columns'];
$collection = $widget['collection'];
$attributes = json_encode($widget['attributes'], JSON_FORCE_OBJECT);
$functionName = \Str::slug($widget['name'], '_');
$url = $widget['url'];
@endphp
@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start')
<div class="container">
    <h4> {{$widget['name']}}</h4>
    <table class="table {{ $widget['tableClass'] ?? '' }}" data-url="{{$url}}">
        <thead class="{{$widget['tableHeadClass']}}">
            <tr>
                @foreach (collect($columnNames) as $column => $value)
                    @if (is_array($value))
                        <th scope="col">{{ $value['column'] }}</th>
                    @else
                        <th scope="col">{{ $value }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody id="body{{$functionName}}">
        </tbody>
        {{--<tfoot align="right">
            <tr>
                @foreach (collect($attributes) as $column => $value)
                <th></th>
                @endforeach
            </tr>
        </tfoot>--}}
    </table>
</div>
@includeWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end')

@push('after_scripts')
<script>

$(document).ready(function() {

    let dateFrom = $('#date-from');
    let dateTo = $('#date-to');
    if (dateFrom) {
        dateFrom.on('change', function() {
            {{$functionName}}($(this).val(), $('#date-to').val()); }); }
    
    if (dateTo) {
        $('#date-to').on('change', function() {
            {{$functionName}}($('#date-from').val(), $(this).val());
        });
    }
    {{$functionName}}(dateFrom.val(), dateTo.val());
});

function {{$functionName}}(from, to) {
    let attributes = {!! $attributes !!}
    $.ajax({
        url: "{{$widget['url']}}",
        type: 'POST',
        data: {
            from,
            to
        },
        success: function(content) {
            let tableBody = $("#body{{$functionName}}");
            tableBody.children().remove();
            content.forEach(element => {
                let itemTable = "<tr>";
                for (i in attributes) {
                    itemTable += "<td>" + element[attributes[i]] + "</td>";
                }
                itemTable += "</tr>";
                tableBody.append(itemTable);
            })
                
        },
        error: function(error) {
        }
       //dataType: 'mycustomtype'
    });
};

</script>

@endpush
