@extends('layouts.master')

@section ('content')

<div class="container">

  <div class="row">
    <div class="col-sm-8 book-main">
        <h1>Books</h1>
        @foreach ($books as $book)
            @include ('books.book')
        @endforeach
    </div>

  </div>
</div>

@endsection
