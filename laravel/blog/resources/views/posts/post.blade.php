<div class="blog-post">
    <h2 class="blog-post-title">
        <a href="/posts/{{ $post->permalink }}">{{ $post->title }}</a>
    </h2>
    <p class="blog-post-meta">
        @if ($post->user)
            {{ $post->user->name }} on
        @endif
        {{ $post->created_at->toFormattedDateString() }}
    </p>
    {{ $post->body }}
</div>
