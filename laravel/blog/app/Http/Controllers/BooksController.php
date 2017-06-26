<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index(\Google_Client $client)
    {
        $books = [];
        if ($query = request('q')) {
            $service = new \Google_Service_Books($client);
            $books = array_map(function ($book) {
                return $book->getVolumeInfo();
            }, $service->volumes->listVolumes($query, [])->getItems());
        }

        return view('books.index', ['books' => $books]);
    }
}
