<!DOCTYPE html>
<html>
<head>
    <title>Log Viewer</title>
</head>
<body>

    <h1>App Logs</h1>

    @foreach($logs as $log)

        <div>
            <strong>{{ $log['date'] }}</strong>
            [{{ strtoupper($log['channel']) }}]
            [{{ strtoupper($log['level']) }}]

            {{ $log['message'] }}
            {{ $log['fullText'] }}
        </div>

    @endforeach

</body>
</html>