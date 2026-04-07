<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Akun Ditolak</title>
</head>

<body style="margin:0; background:linear-gradient(135deg,#ef4444,#dc2626); font-family:Arial;">

<table width="100%">
<tr>
<td align="center">

    <table width="420" style="background:#fff; border-radius:20px; padding:35px; text-align:center; margin-top:50px;">

        <!-- ICON -->
        <tr>
            <td>
                <div style="
                    background:#fee2e2;
                    width:70px;
                    height:70px;
                    border-radius:50%;
                    line-height:70px;
                    font-size:28px;
                    margin:auto;">
                    ❌
                </div>

                <h2 style="color:#ef4444;">{{ config('app.name') }}</h2>
            </td>
        </tr>

        <!-- TITLE -->
        <tr>
            <td style="padding-top:15px;">
                <h3 style="margin:0;">Pendaftaran Ditolak</h3>
            </td>
        </tr>

        <!-- MESSAGE -->
        <tr>
            <td style="padding-top:10px; color:#555;">
                Halo <b>{{ $user->name }}</b>,<br><br>
                Maaf, akun kamu belum dapat disetujui oleh admin.
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td style="padding-top:25px; font-size:12px; color:#aaa;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>