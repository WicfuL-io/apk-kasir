<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Success</title>
</head>

<body style="margin:0; background:linear-gradient(135deg,#6366f1,#8b5cf6); font-family:Arial;">

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
                    background:#ecfdf5;
                    border-radius:50%;
                    line-height:70px;
                    font-size:30px;
                    margin:auto;">
                    ✅
                </div>

                <h2 style="color:#4f46e5; margin:10px 0;">
                    {{ config('app.name') }}
                </h2>
            </td>
        </tr>

        <!-- TITLE -->
        <tr>
            <td style="padding-top:10px;">
                <h3 style="margin:0; color:#111;">
                    User Berhasil Disetujui
                </h3>
            </td>
        </tr>

        <!-- USER -->
        <tr>
            <td style="padding-top:15px; color:#555;">
                <b>{{ $user->name }}</b> sekarang sudah bisa login 🎉
            </td>
        </tr>

        <!-- BUTTON -->
        <tr>
            <td style="padding-top:25px;">
                <a href="/"
                   style="
                   background:#4f46e5;
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