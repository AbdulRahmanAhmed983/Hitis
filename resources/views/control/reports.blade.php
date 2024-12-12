@extends('layout.layout')
@section('title', 'تحميل التقارير')
@section('styles')
@endsection
@section('content')
    @if(session()->has('success'))
        <div class="alert alert-success mt-3 text-center col-12">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <h6><i class="icon fas fa-check"></i> {{session('success')}}</h6>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger mt-3 text-center">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
            </button>
            <h6><i class="icon fas fa-ban"></i> {{session('error')}}</h6>
        </div>
    @endif
    <div class="row justify-content-around">
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">طباعة نتائج العام الدراسي</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form1" method="post" action="{{route('print.grades.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value=""></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value=""></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                        @php
                                            $deptName = '';
                                              switch($value) {
                                                case 1:
                                                    $deptName = "عام";
                                                    break;
                                                case 2:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                                    break;
                                                case 3:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                                    break;
                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year">العام الدراسى</label>
                                <select class="custom-select" name="year" id="year" required>
                                    <option value=""></option>
                                    @foreach($filter_data['year'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="form1" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-around">
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left"> طباعة نتائج العام الدراسي (الوزارة)</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form3" method="post" action="{{route('print.grades.report2')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value=""></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value=""></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                        @php
                                            $deptName = '';
                                              switch($value) {
                                                case 1:
                                                    $deptName = "عام";
                                                    break;
                                                case 2:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                                    break;
                                                case 3:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                                    break;
                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year">العام الدراسى</label>
                                <select class="custom-select" name="year" id="year" required>
                                    <option value=""></option>
                                    @foreach($filter_data['year'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="form3" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-around">
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left">طباعة نتائج الفصل الصيفي</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="form2" method="post" action="{{route('print.grades.summer.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-6">
                                <label for="study_group_summer">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value=""></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="specialization_summer">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value=""></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year">الشعبة</label>
                                <select class="custom-select" required id="departments" name="departments_id">
                                    <option value="" hidden></option>
                                    @foreach($filter_data['departments_id'] as $value)
                                        @php
                                            $deptName = '';
                                                                                    switch($value) {
                                                case 1:
                                                    $deptName = "عام";
                                                    break;
                                                case 2:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية العضوية";
                                                    break;
                                                case 3:
                                                    $deptName = "ترميم الأثار والمقتنيات الفنية غيرالعضوية";
                                                    break;
                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="year_summer">العام الدراسى</label>
                                <select class="custom-select" name="year" id="year" required>
                                    <option value=""></option>
                                    @foreach($filter_data['year'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="form2" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')


<script>
    let done = true;

    function handleFormInput(formId) {
      const year = $(`#${formId} #year`).val();
      const specialization = $(`#${formId} #specialization`).val();
      const departments = $(`#${formId} #departments`).val();
      const studyGroup = $(`#${formId} #study_group`).val();

      if (year !== '' && specialization !== '' && departments !=='' && studyGroup !== '' && done) {
        done = false;
        $.ajax({
          type: 'get',
          url: '{{ route('check.print.number') }}',
          data: {
            'year': year,
            'specialization': specialization,
            'departments_id': departments,
            'study_group': studyGroup,
          },
          success: function (data) {
            $(`#${formId} #count`).parent().remove();
            $(`#${formId} #year`).parent().parent().append(`
              <div class="form-group col-md-6">
                <label for="year">عدد الطباعة</label>
                <select class="custom-select" name="count" id="count" required>
                  <option value=""></option>
                  ${data}
                </select>
              </div>
            `);
          },
          error: function (data) {
            $(`#${formId} #count`).parent().remove();
          },
          complete: function () {
            done = true;
          }
        });
      }
    }

    // Event handler for form 1
    $('#form1 #year, #form1 #specialization,#form1 #departments, #form1 #study_group').on('input', function (e) {
      handleFormInput('form1');
    });

    // Event handler for form 2
    $('#form2 #year, #form2 #specialization, #form2 #departments, #form2 #study_group').on('input', function (e) {
      handleFormInput('form2');
    });
     // Event handler for form 3
     $('#form3 #year, #form3 #specialization, #form3 #departments,#form3 #study_group').on('input', function (e) {
      handleFormInput('form3');
    });


    // Toastr error messages
    @if($errors->any())
      toastr.options.closeButton = true;
      toastr.options.newestOnTop = false;
      toastr.options.timeOut = 0;
      toastr.options.extendedTimeOut = 0;
      toastr.options.rtl = true;
      toastr.options.positionClass = "toast-top-center";
      toastr.options.progressBar = true;
      @foreach ($errors->all() as $error)
        toastr.error('{{$error}}')
      @endforeach
    @endif
  </script>

@endsection
