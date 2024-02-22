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

    public function index()
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

        // アクセストークンが設定されていない場合、認証URLを生成
        if (! isset($_GET['code'])) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            } else {
            // アクセストークンを取得
            $client->authenticate($_GET['code']);
            $access_token = $client->getAccessToken();

            // YouTube Data APIにリクエストを送信
            $youtube = new Google_Service_YouTube($client);
            $response = $youtube->subscriptions->listSubscriptions('snippet,contentDetails,subscriberSnippet', array("mine" => "true"));

            return view('youtubeapi.index', compact($response));
        }
    }
}
