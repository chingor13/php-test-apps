@extends ('layouts.master')

@section ('content')

<h1>{{ $post->title }}</h1>

<p>
    {{ $post->body }}
</p>

<hr>

<div class="comments">
    <ul class="list-group">
        @foreach ($post->comments as $comment)
            <li class="list-group-item">
                <strong>
                    {{ $comment->created_at->diffForHumans() }}:&nbsp;
                </strong>
                {{ $comment->body }}
            </li>
        @endforeach
    </ul>
</div>

<hr>

<div class="card">
    <div class="card-block">
        <form method="POST" action="/posts/{{$post->permalink}}/comments">
            {{ csrf_field() }}
            <div class="form-group">
                <textarea name="body" placeholder="your comment here." class="form-control"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Add comment</button>
            </div>

            @include ('layouts.errors')
        </form>
    </div>
</div>

@endsection
