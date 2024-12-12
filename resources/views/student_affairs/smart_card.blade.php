@extends('layout.layout')
@section('title', 'تقارير الكارنيهات')
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
                    <h2 class="card-title float-left">كشف كارنيهات الطلاب المسددين</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="paying-students-subject" method="post"
                          action="{{route('smartId.report')}}">
                        @csrf
                        <div class="row justify-content-around">
                            <div class="form-group col-md-4">
                                <label for="study_group">الفرقة الدراسية</label>
                                <select class="custom-select" name="study_group" id="study_group" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['study_group'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="specialization">التخصص</label>
                                <select class="custom-select" name="specialization" id="specialization" required>
                                    <option value="" hidden></option>
                                    @foreach($filter_data['specialization'] as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="departments_id">الشعبة</label>
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
                                                    $deptName = "نظم معلومات الاعمال";
                                                    break;
                                                case 3:
                                                    $deptName = "المحاسبة و المراجعة";
                                                    break;
                                                case 4:
                                                    $deptName = "التسويق والتجارة الالكترونية";
                                                    break;
                                                case 5:
                                                    $deptName = "Business information systems";
                                                    break;
                                                case 6:
                                                    $deptName = "Accounting and Review";
                                                    break;
                                                case 7:
                                                    $deptName = "Marketing and E-commerce";
                                                    break;

                                            }
                                        @endphp
                                        <option value="{{$value}}" @if (old('departments_id') == $value) selected @endif>{{$deptName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="submit" form="paying-students-subject" class="btn btn-primary mx-3 align-self-center"
                            style="height: fit-content">تنزيل <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
        </div>

                    {{-- Upload File --}}
        <div class="col-lg-6">
            <div class="card card-primary collapsed-card">
                <div class="card-header" data-card-widget="collapse" style="cursor:pointer;">
                    <h2 class="card-title float-left"> رفع كارنيهات الطلاب</h2>
                    <div class="card-tools float-right">
                        <button type="button" class="btn btn-tool">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form  method="post" action="{{route('uploadSmartId.report')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex justify-center flex-wrap -mx-3 mb-6">
                            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                                <label
                                    class="block uppercase tracking-wide text-gray-500 text-lg mb-2"
                                    for="grid-first-name">
                                    اختر الملف
                                </label>
                                <input type="file"
                                       class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                       name="file">
                            </div>
                        </div>

                        <div class="flex justify-center flex-wrap -mx-3 mb-6">
                            <div class="w-full flex justify-center md:w-1/2 px-3 mb-6 md:mb-0">
                                <button
                                    class="shadow focus:shadow-outline focus:outline-none text-white font-bold mt-3 py-1 px-20 rounded"
                                    style="background:#1027c2; width:150px">اضافة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
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


<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat&display=swap');
body {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	font-family: 'Montserrat', sans-serif;
}
.form-container {
	width: 50vw;
	height: 50vh;
	background-color: #7b2cbf;
	display: flex;
   	justify-content: center;
	align-items: center;
}
.upload-files-container {
	background-color: #f7fff7;
	width: 390px;
	padding: 30px 60px;
	border-radius: 40px;
	display: flex;
   	align-items: center;
   	justify-content: center;
	flex-direction: column;
	box-shadow: rgba(0, 0, 0, 0.24) 0px 10px 20px, rgba(0, 0, 0, 0.28) 0px 6px 6px;
}
.drag-file-area {
	border: 2px dashed #7b2cbf;
	border-radius: 40px;
	margin: 10px 0 15px;
	padding: 30px 50px;
	width: 350px;
	text-align: center;
}
.drag-file-area .upload-icon {
	font-size: 50px;
}
.drag-file-area h3 {
	font-size: 26px;
	margin: 15px 0;
}
.drag-file-area label {
	font-size: 19px;
}
.drag-file-area label .browse-files-text {
	color: #7b2cbf;
	font-weight: bolder;
	cursor: pointer;
}
.browse-files span {
	position: relative;
	top: -25px;
}
.default-file-input {
	opacity: 0;
}
.cannot-upload-message {
	background-color: #ffc6c4;
	font-size: 17px;
	display: flex;
	align-items: center;
	margin: 5px 0;
	padding: 5px 10px 5px 30px;
	border-radius: 5px;
	color: #BB0000;
	display: none;
}
@keyframes fadeIn {
  0% {opacity: 0;}
  100% {opacity: 1;}
}
.cannot-upload-message span, .upload-button-icon {
	padding-right: 10px;
}
.cannot-upload-message span:last-child {
	padding-left: 20px;
	cursor: pointer;
}
.file-block {
	color: #f7fff7;
	background-color: #7b2cbf;
  	transition: all 1s;
	width: 390px;
	position: relative;
	display: none;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	margin: 10px 0 15px;
	padding: 10px 20px;
	border-radius: 25px;
	cursor: pointer;
}
.file-info {
	display: flex;
	align-items: center;
	font-size: 15px;
}
.file-icon {
	margin-right: 10px;
}
.file-name, .file-size {
	padding: 0 3px;
}
.remove-file-icon {
	cursor: pointer;
}
.progress-bar {
	display: flex;
	position: absolute;
	bottom: 0;
	left: 4.5%;
	width: 0;
	height: 5px;
	border-radius: 25px;
	background-color: #4BB543;
}
.upload-button {
	font-family: 'Montserrat';
	background-color: #7b2cbf;
	color: #f7fff7;
	display: flex;
	align-items: center;
	font-size: 18px;
	border: none;
	border-radius: 20px;
	margin: 10px;
	padding: 7.5px 50px;
	cursor: pointer;
}
</style>
