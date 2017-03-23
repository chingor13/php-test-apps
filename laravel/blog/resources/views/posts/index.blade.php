@extends('layouts.master')

@section ('content')

<div class="container">

  <div class="row">

    <div class="col-sm-8 blog-main">
        @foreach ($posts as $post)
            @include ('posts.post')
        @endforeach
    </div>

  </div>
</div>

@endsection
