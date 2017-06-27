@extends('layouts.master')

@section ('content')


<form method="POST" action="/books">
    <div class="form-group">
        <label for="q">Search</label>
        <input type="text" class="form-control" id="q" name="q">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary">Publish</button>
    </div>

    @include ('layouts.errors')
</form>
<h1>Books</h1>
@foreach ($books as $book)
    @include ('books.book')
@endforeach

@endsection
