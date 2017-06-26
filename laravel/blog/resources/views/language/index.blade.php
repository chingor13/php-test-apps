@extends ('layouts.master')

@section ('content')

<h1>Detect</h1>

<form method="POST" action="/language/detect">
    {{ csrf_field() }}

    <div class="form-group">
        <label for="sentence">Sentence:</label>
        <input type="text" class="form-control" id="sentence" name="sentence" required>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Detect</button>
    </div>

    @include ('layouts.errors')

</form>

@endsection
