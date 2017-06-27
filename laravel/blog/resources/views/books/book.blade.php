<div class="book">
    <h2 class="book-title">
        <a href="/books/{{ $book->getId() }}">{{ $book->getVolumeInfo()->getTitle() }}</a>
    </h2>
    <p class="book-meta">
        By {{ implode(', ', $book->getVolumeInfo()->getAuthors()) }} on {{ $book->getVolumeInfo()->getPublishedDate() }}
    </p>
    {{ $book->getVolumeInfo()->getDescription() }}
</div>
