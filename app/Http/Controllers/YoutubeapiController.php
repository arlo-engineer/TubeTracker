<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google_Client;
use Google_Service_YouTube;

class YoutubeapiController extends Controller
{
    private $client_id;
    private $client_secret;
    private $redirect_uri;

    public function redirectToProvider()
    {
        // クライアントIDと秘密鍵を設定
        $this->client_id = config('youtubeapi.client_id');
        $this->client_secret = config('youtubeapi.client_secret');

        // リダイレクトURIを指定
        $this->redirect_uri = config('youtubeapi.redirect_uri');

        // OAuth2.0クライアントを作成
        $client = new Google_Client();
        $client->setClientId($this->client_id);
        $client->setClientSecret($this->client_secret);
        $client->setRedirectUri($this->redirect_uri);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.readonly']);

        // dd($client->getAccessToken());
        return redirect($client->createAuthUrl());
    }

    public function getVideos()
    {
        // $_SESSIONの開始
        session_start();
        // クライアントIDと秘密鍵を設定
        $this->client_id = config('youtubeapi.client_id');
        $this->client_secret = config('youtubeapi.client_secret');

        // リダイレクトURIを指定
        $this->redirect_uri = config('youtubeapi.redirect_uri');

        // OAuth2.0クライアントを作成
        $client = new Google_Client();
        $client->setClientId($this->client_id);
        $client->setClientSecret($this->client_secret);
        $client->setRedirectUri($this->redirect_uri);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.readonly']);
        // $client->setDeveloperKey('AIzaSyDQocZYw1gRVQzXN8HGAbNquWrTKv7YIYU');

        if (isset($_SESSION['access_token'])) {
            $client->setAccessToken($_SESSION['access_token']);
        }

        $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $access_token = $client->getAccessToken();
        if (isset($access_token)) {
            $_SESSION['access_token'] = $access_token;
        }

        $youtube = new Google_Service_YouTube($client);

        // 登録チャンネル情報の取得
        $subscriptions = $youtube->subscriptions->listSubscriptions('snippet,contentDetails,subscriberSnippet', [
            "mine" => "true",
            'maxResults' => 10,
        ]);

        // 連想配列は扱いづらいためcollection化して処理
        $channelIds = collect($subscriptions->getItems())->pluck('snippet.resourceId')->all();

        $videoIds = array();

        // channelIdからvideoIdの取得
        foreach ($channelIds as $channelId) {
            $items = $youtube->search->listSearch('snippet', [
                'channelId' => $channelId->channelId, // channelId: UCutJqz56653xV2wwSvut_hQ
                'order' => 'viewCount',
                'maxResults' => 10,
            ]);

            // 連想配列は扱いづらいためcollection化して処理
            $ids = collect($items->getItems())->pluck('id')->all();

            // videoIdを配列として取得
            foreach ($ids as $id) {
                $videoIds[] = $id->videoId;
            }
        }

        return view('youtubeapi.index', compact('subscriptions', 'videoIds'));
    }
}
