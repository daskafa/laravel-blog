@extends('back.layouts.master')
@section('title', $page->title . ' sayfasını güncelle')
@section('content')
  <div class="card shadow mb-4">
    <div class="card-header py-3">
    </div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            <li>{{$error}}</li>
          @endforeach
        </div>
      @endif
      <form action="{{route('admin.page.edit.post', $page->id)}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Makale Başlığı</label>
          <input type="text" name="title" class="form-control" value="{{$page->title}}" required>
        </div>
        <div class="form-group">
          <label>Makale Fotoğrafı</label>
          <img src="{{asset($page->image)}}" style="display:block;width: 20%; height:15em; margin: 10px 0 10px 0; padding: 10px; border:1px solid #737373; border-radius: 5px;object-fit:cover;">
          <input style="margin-bottom:3em; border:0;" type="file" name="image" class="form-control">
        </div>
        <div class="form-group">
          <label>Makale İçeriği</label>
          <textarea id="editor" name="content" class="form-control">{!! $page->content !!}</textarea>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block"> Makaleyi Güncelle </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
  $(document).ready(function() {
    $('#editor').summernote({
      height:350
    });
  });
</script>
@endsection
