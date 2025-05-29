<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Chat System</h1>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        @foreach($users as $user)
            <div style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: 200px; text-align: center; cursor: pointer;">
                <form action="/conversation/{{$user->id}}" method="POST" style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: 200px; text-align: center;">
                    @csrf
                    <button type="submit" style="cursor: pointer; padding: 10px; border: none; background-color: #007bff; color: white; border-radius: 5px;">Start Conversation</button>
                </form>
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>