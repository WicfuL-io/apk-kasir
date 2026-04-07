<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Approve User</title>
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
                <table width="100%" style="background:#ffffff; border-radius:18px; padding:35px;">

                    <!-- LOGO -->
                    <tr>
                        <td align="center">
                            <div style="
                                background:#eef2ff;
                                width:60px; height:60px;
                                border-radius:50%;
                                line-height:60px;
                                font-size:26px;">
                                🛒
                            </div>

                            <h2 style="margin:10px 0 0; color:#4f46e5;">
                                {{ config('app.name') }}
                            </h2>
                        </td>
                    </tr>

                    <!-- TITLE -->
                    <tr>
                        <td style="padding-top:25px; text-align:center;">
                            <p style="margin:0; font-size:18px; font-weight:600; color:#111;">
                                👤 User Baru Masuk
                            </p>
                            <p style="margin:5px 0 0; font-size:13px; color:#777;">
                                Ada user baru yang butuh approval
                            </p>
                        </td>
                    </tr>

                    <!-- USER CARD -->
                    <tr>
                        <td style="padding-top:20px;">
                            <div style="
                                background:#f9fafb;
                                padding:15px;
                                border-radius:12px;
                                font-size:14px;">

                                <p style="margin:5px 0;">
                                    <strong>Nama:</strong> {{ $user->name }}
                                </p>

                                <p style="margin:5px 0;">
                                    <strong>Email:</strong> {{ $user->email }}
                                </p>

                            </div>
                        </td>
                    </tr>

                    <!-- BUTTON -->
                    <tr>
                        <td align="center" style="padding-top:25px;">

                            <a href="{{ url('/approve-user/'.$user->id) }}"
                               style="
                               background:#10b981;
                               color:#fff;
                               padding:12px 22px;
                               border-radius:999px;
                               text-decoration:none;
                               font-weight:600;
                               margin-right:10px;
                               display:inline-block;">
                                ✅ Approve
                            </a>

                            <a href="{{ url('/reject-user/'.$user->id) }}"
                               style="
                               background:#111827;
                               color:#fff;
                               padding:12px 22px;
                               border-radius:999px;
                               text-decoration:none;
                               font-weight:600;
                               display:inline-block;">
                                ❌ Reject
                            </a>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="padding-top:30px; text-align:center; font-size:12px; color:#aaa;">
                            © {{ date('Y') }} {{ config('app.name') }} • Built with ❤️
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