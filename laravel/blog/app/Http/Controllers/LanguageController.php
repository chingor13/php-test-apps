<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Translate\TranslateClient;

use Google\Cloud\Trace\Integrations\Guzzle\Middleware;

class LanguageController extends Controller
{
    public function index()
    {
        return view('language.index');
    }

    public function detect(TranslateClient $client)
    {
        $sentence = request('sentence');
        $result = RequestTracer::inSpan(['name' => 'translate', 'labels' => ['foo' => 'bar']], function () use ($client, $sentence) {
            return $client->detectLanguage($sentence);
        });
        return view('language.detect', ['result' => $result]);
    }
}
