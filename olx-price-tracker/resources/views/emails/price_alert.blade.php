<!DOCTYPE html>
<html>
<head>
    <title>{{ $type->title() }}</title>
</head>
<body>
@if($type === \App\Enums\NotificationType::SUBSCRIPTION)
    <h1>{{ $type->title() }}</h1>
    <p>Ви підписалися на відстеження ціни оголошення:</p>
@else
    <h1>{{ $type->title() }}</h1>
    <p>Ціна в оголошенні змінилася:</p>
@endif

<p><a href="{{ $subscription->url }}">{{ $subscription->url }}</a></p>
<p>Поточна ціна: {{ $subscription->current_price }} {{ $subscription->currency }}</p>
<p>Для відписки перейдіть за <a href="{{ url('/unsubscribe/'.$subscription->token) }}">посиланням</a></p>
</body>
</html>
