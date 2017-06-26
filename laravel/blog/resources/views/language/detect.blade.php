@extends ('layouts.master')

@section ('content')

<h1>Detect Results</h1>

<dl>
    <dt>Input:</dt>
    <dd>{{ $result['input'] }}</dd>

    <dt>Language Code:</dt>
    <dd>{{ $result['languageCode'] }}</dd>
</dl>

<p>
{{ json_encode($result) }}
</p>

@endsection
