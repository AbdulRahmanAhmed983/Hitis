@if(!empty($heading))
    <table>
        <thead>
        <tr>
            @foreach($heading as $head)
                <th colspan="{{$head['col']}}" rowspan="{{count($head['text'])}}">
                    @for($i = 0;$i < count($head['text']); $i++)
                        <h5><b>{{$head['text'][$i]}}</b></h5>
                    @endfor
                </th>
            @endforeach
        </tr>
        </thead>
    </table>
    @for($i=0;$i<count(max(array_column($heading,'text')))-1;$i++)
        <br>
    @endfor
@endif
<table>
    <thead>
    @foreach($headers as $heads)
        <tr>
            @foreach($heads as $head)
                <th colspan="{{$head['col']}}" rowspan="{{$head['row']}}">
                    <h5>
                        <b>{{ $head['text'] }}</b>
                    </h5>
                </th>
            @endforeach
        </tr>
    @endforeach
    </thead>
    <tbody>
    @foreach($data as $arr)
        <tr>
            @foreach($arr as $a)
                <td>{{$a}}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
