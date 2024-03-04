<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TubeTracker</title>
</head>
<body>
    {{-- 登録チャンネル名の出力 --}}
    @foreach ($subscriptions['items'] as $result)
    <p>{{ $result['snippet']['title'] }}</p>
    @endforeach

    {{-- 動画の出力 --}}
    @foreach ($videoIds as $videoId)
    <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0"></iframe>
    @endforeach
</body>
</html>
