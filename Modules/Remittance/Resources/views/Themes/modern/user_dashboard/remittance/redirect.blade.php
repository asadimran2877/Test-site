<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Remittance') }}</title>
</head>

<body>
    <form method="post" action="{{ route('recepient.details') }}" name="member_signup">
        @csrf
    </form>

    <script type="text/javascript">
        window.onload = function() {
            document.forms['member_signup'].submit();
        }
    </script>

</body>

</html>