<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Akun Disetujui</title>
</head>

<body style="margin:0; padding:0; background:linear-gradient(135deg,#6366f1,#8b5cf6); font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">

    <!-- WRAPPER -->
    <table width="100%" style="max-width:520px; margin:50px auto;">
        <tr>
            <td style="padding:10px;">

                <!-- CARD -->
                <table width="100%" style="background:#ffffff; border-radius:18px; padding:35px; text-align:center;">

                    <!-- ICON -->
                    <tr>
                        <td>
                            <div style="
                                background:#ecfdf5;
                                width:70px; height:70px;
                                border-radius:50%;
                                line-height:70px;
                                font-size:30px;
                                margin:auto;">
                                🎉
                            </div>

                            <h2 style="margin:10px 0 0; color:#4f46e5;">
                                {{ config('app.name') }}
                            </h2>
                        </td>
                    </tr>

                    <!-- TITLE -->
                    <tr>
                        <td style="padding-top:20px;">
                            <p style="margin:0; font-size:20px; font-weight:700; color:#111;">
                                ✅ Akun Disetujui!
                            </p>
                            <p style="margin:5px 0 0; font-size:13px; color:#777;">
                                Selamat! kamu sudah bisa mulai
                            </p>
                        </td>
                    </tr>

                    <!-- GREETING -->
                    <tr>
                        <td style="padding-top:20px; font-size:15px; color:#333;">
                            Halo <strong>{{ $user->name }}</strong> 👋
                        </td>
                    </tr>

                    <!-- MESSAGE -->
                    <tr>
                        <td style="padding-top:10px; font-size:14px; color:#555;">
                            Akun kamu sudah berhasil disetujui oleh admin.
                            Sekarang kamu bisa login dan mulai menggunakan aplikasi.
                        </td>
                    </tr>

                    <!-- BUTTON -->
                    <tr>
                        <td style="padding-top:25px;">

                            <a href="{{ url('/login') }}"
                               style="
                               background:#10b981;
                               color:#fff;
                               padding:12px 28px;
                               border-radius:999px;
                               text-decoration:none;
                               font-weight:600;
                               display:inline-block;">
                                🚀 Login Sekarang
                            </a>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="padding-top:30px; font-size:12px; color:#aaa;">
                            © {{ date('Y') }} {{ config('app.name') }} • Enjoy your journey ✨
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</td>
</tr>
</table>

</body>
</html>