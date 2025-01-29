<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>

<body>
    <h2>Password Reset</h2>
    <p style="text-align: center; font-weight: 400; font-size: 17px">Hello,</p>
    <p style="text-align: center; font-weight: 400; font-size: 17px">You have requested to reset your password. Your
        Confirmation code is:
    </p>
    <p style="text-align: center;">
        <strong style="font-size:30px; background-color: #eaf5ff; border-radius: 6px; padding: 2px
            6px; ">
            {{ $mailData['code'] }}
        </strong>
    </p>
    <p style="text-align: center; font-weight: 400; font-size: 17px">If you did not request this, please ignore this
        email.</p>
    <p style="text-align: center; font-weight: 400; font-size: 17px">Thank you!</p>
</body>

</html>
