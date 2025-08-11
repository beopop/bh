@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" integrity="sha512-TZLzYqS+V1A1dEB+lD1I7sH++FEkmiJYcaUFNjZ1pnoUQApbkBHB6DNuFf0zPWAIymFcpnSUsctNk6mAQmtWYw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<form action="{{ route('files.store') }}" class="dropzone" id="file-dropzone" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="owner_type" value="{{ auth()->user()->getMorphClass() }}">
    <input type="hidden" name="owner_id" value="{{ auth()->id() }}">
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-3g7VmZ4hvs5IrGValutMS8SiHQm5jk+UY69ObZ1srO2Qd6hw2yYB9H9n1tFoZT3zh0+BTtPlqvGjNFH6G+j1NA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
Dropzone.options.fileDropzone = {
    paramName: 'file',
    maxFilesize: {{ config('app.file_max_size_mb') }},
};
</script>
@endsection
