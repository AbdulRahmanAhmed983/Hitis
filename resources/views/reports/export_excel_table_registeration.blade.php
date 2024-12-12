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
    @foreach($data as $student)
        <tr>
            <td>{{$loop->index + 1}}</td>
            <td>{{$student->seating_number->seating_number ?? ''}}</td>
            <td>{{$student->username}}</td>
            <td>{{$student->name}}</td>
            <td>{{$student->section_number->section_number ?? ''}}</td>
            <td>{{$student->studying_status}}</td>

            @foreach($courses->pluck('full_code') as $course)
                <td>
                    @foreach($student->registerations as $registration)
                        @if($registration->course)
                            @if($course === $registration->course->full_code)
                                ✓
                            @endif
                        @endif
                    @endforeach
                </td>
            @endforeach
            @php
                $i = 0;
            @endphp
            @foreach($student->registerations as $registration)
                @if(is_null($registration->course))
                    @if($i<2)
                        <td>{{\App\Models\Course::where('full_code', $registration->course_code)->first()->name}}</td>
                        @php
                            $i++;
                        @endphp
                    @else
                        @break
                    @endif
                @endif
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
