<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='x-apple-disable-message-reformatting'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Password Change Verification</title>
<style>
    @media only screen and (max-width:600px) {
    .container { width:100% !important; }
    .inner { padding:24px 20px !important; }
    .p-sm { padding:16px 8px !important; }
    .h1 { font-size:24px !important; line-height:30px !important; }
    .subtitle { font-size:15px !important; }
    .text { font-size:15px !important; line-height:22px !important; }
    .code-box { padding:20px 16px !important; }
    .row-label { display:block !important; width:100% !important; padding:16px 0 6px 0 !important; }
    .row-value { display:block !important; width:100% !important; padding:0 0 20px 0 !important; }
    .card { padding:18px !important; }
    }
</style>
</head>
<body style='margin:0; padding:0; background:#f8f9fa; font-family:Arial, Helvetica, sans-serif;'>
<div style='display:none; max-height:0; overflow:hidden; mso-hide:all;'>
    Password change verification code for FXBG Closet
</div>
<center role='article' aria-roledescription='email' lang='en'>
    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='background:#f8f9fa;'>
    <tr>
        <td align='center' class='p-sm' style='padding:32px 16px;'>
        <!--[if mso]>
        <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='600'>
        <tr><td>
        <![endif]-->
        <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' class='container' style='max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
            
            <!-- Alert header bar -->
            <tr>
            <td style='padding:0;'>
                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td style='background:#dc2626; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#ea580c; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#f59e0b; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                </tr>
                </table>
            </td>
            </tr>

            <!-- Header -->
            <tr>
            <td align='center' style='padding:40px 28px 12px 28px; background:linear-gradient(180deg, #fef2f2 0%, #ffffff 100%); background-color:#fef2f2;'>
                <div style='display:inline-block; background:#dc2626; color:#ffffff; padding:8px 16px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; margin-bottom:16px;'>
                Security Alert
                </div>
                <h1 class='h1' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:36px; color:#1a1a1a; font-weight:700;'>
                Password Change Requested
                </h1>
                <p class='subtitle' style='margin:12px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:24px; color:#6b7280;'>
                Use the verification code below to proceed
                </p>
            </td>
            </tr>

            <!-- Content -->
            <tr>
            <td class='inner' style='padding:32px 28px;'>
                
                <!-- Warning message -->
                <div style='background:#fef2f2; border-left:4px solid #dc2626; padding:16px 20px; margin-bottom:24px; border-radius:8px;'>
                <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#991b1b; font-weight:600;'>
                    If you did not request this change, please contact your system administrator immediately.
                </p>
                </div>
                
                <!-- Details card -->
                <div class='card' style='background:#f8f9fa; border-radius:12px; padding:24px; margin-bottom:24px;'>
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='border-collapse:collapse;'>
                    
                    <!-- Initiated by -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Initiated By
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                        <?= htmlspecialchars($userID) ?>
                    </td>
                    </tr>

                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                    <!-- Target account -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Target Account
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                        <?= htmlspecialchars($targetUser) ?>
                    </td>
                    </tr>

                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                    <!-- Time -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Time
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                        <?= htmlspecialchars($when) ?>
                    </td>
                    </tr>
                    
                </table>
                </div>
                
                <!-- Verification code -->
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
                <tr>
                    <td align='center' style='padding:0 0 16px 0;'>
                    <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#6b7280; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Your Verification Code
                    </p>
                    </td>
                </tr>
                <tr>
                    <td align='center' class='code-box' style='padding:28px 24px; background:#ffffff; border:2px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05);'>
                    <div style='font-family:Courier New, Courier, monospace; font-size:32px; font-weight:700; color:#1a1a1a; letter-spacing:8px;'>
                        <?= htmlspecialchars($code) ?>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td align='center' style='padding:16px 0 0 0;'>
                    <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#ef4444; font-weight:600;'>
                        Expires in 10 minutes
                    </p>
                    </td>
                </tr>
                </table>
                
            </td>
            </tr>

            <!-- Footer -->
            <tr>
            <td align='center' style='padding:32px 28px; background:#f9fafb; border-top:1px solid #e5e7eb;'>
                <p class='text' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#9ca3af;'>
                Automatically generated by FXBG Closet Security System
                </p>
                <p class='text' style='margin:6px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#d1d5db;'>
                    <?= htmlspecialchars($when) ?>
                </p>
            </td>
            </tr>

            <!-- Alert footer bar -->
            <tr>
            <td style='padding:0;'>
                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td style='background:#dc2626; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#ea580c; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#f59e0b; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                </tr>
                </table>
            </td>
            </tr>

        </table>
        <!--[if mso]></td></tr></table><![endif]-->
        </td>
    </tr>
    </table>
</center>
</body>
</html>