<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Rejected</title>
</head>

<body style="margin:0; background:linear-gradient(135deg,#ef4444,#dc2626); font-family:Arial;">

<table width="100%" height="100%">
<tr>
<td align="center">

    <table width="420" style="background:#fff; border-radius:20px; padding:40px; text-align:center; margin-top:80px;">

        <!-- ICON -->
        <tr>
            <td>
                <div style="
                    width:70px;
                    height:70px;
                    background:#fee2e2;
                    border-radius:50%;
                    line-height:70px;
                    font-size:30px;
                    margin:auto;">
                    ❌
                </div>

                <h2 style="color:#ef4444; margin:10px 0;">
                    {{ config('app.name') }}
                </h2>
            </td>
        </tr>

        <!-- TITLE -->
        <tr>
            <td style="padding-top:10px;">
                <h3 style="margin:0; color:#111;">
                    User Ditolak
                </h3>
            </td>
        </tr>

        <!-- USER -->
        <tr>
            <td style="padding-top:15px; color:#555;">
                User <b>{{ $user->name }}</b> telah ditolak ❌
            </td>
        </tr>

        <!-- BUTTON -->
        <tr>
            <td style="padding-top:25px;">
                <a href="/"
                   style="
                   background:#ef4444;
                   color:white;
                   padding:12px 25px;
                   border-radius:999px;
                   text-decoration:none;
                   display:inline-block;">
                    Kembali
                </a>
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="padding-top:30px; font-size:12px; color:#aaa;">
                © {{ date('Y') }} {{ config('app.name') }} • All rights reserved.
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>