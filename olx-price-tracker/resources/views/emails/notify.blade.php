<!DOCTYPE html>
<html>
<head>
    <title>{{ $type->title() }}</title>
</head>
<body>
@if($type === \App\Enums\NotificationType::SUBSCRIPTION)
    <h1>{{ $type->title() }}</h1>
    <p>Ви підписалися на відстеження ціни оголошення.</p>
@else
    <h1>{{ $type->title() }}</h1>
    <p>Ціна в оголошенні змінилася.</p>
@endif

<p><a href="{{ $subscription->url }}">{{ $subscription->url }}</a></p>
<p>Поточна ціна: {{ $subscription->current_price }} {{ $subscription->currency }}</p>

<h2>Історія змін цін:</h2>
<table>
    <thead>
    <tr>
        <th>Ціна</th>
        <th>Валюта</th>
        <th>Дата</th>
    </tr>
    </thead>
    <tbody>
    @foreach($subscription->priceHistories->sortByDesc('date') as $history)
        <tr>
            <td>{{ $history->price }}</td>
            <td>{{ $history->currency }}</td>
            <td>{{ $history->date->format('d.m.Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<p>Для відписки перейдіть за <a href="{{ url('/unsubscribe/'.$subscription->token) }}">посиланням</a></p>

</body>
</html>
