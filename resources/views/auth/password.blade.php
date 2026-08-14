<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CDU</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body role="document">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">CDU Login</div>
                    <div class="panel-body">
                        <form method="POST" action="/password/email">
                            {!! csrf_field() !!}

                            @if (count($errors) > 0)
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <div>
                                Email
                                <input type="email" name="email" value="{{ old('email') }}">
                            </div>

                            <div>
                                <button type="submit">
                                    Send Password Reset Link
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>