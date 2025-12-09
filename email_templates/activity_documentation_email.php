<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='x-apple-disable-message-reformatting'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>
<title>Volunteer Activity Documentation</title>
<style>
    @media only screen and (max-width:600px) {
    .container { width:100% !important; }
    .inner { padding:24px 20px !important; }
    .p-sm { padding:16px 8px !important; }
    .h1 { font-size:24px !important; line-height:30px !important; }
    .subtitle { font-size:15px !important; }
    .text { font-size:15px !important; line-height:22px !important; }
    .badge { padding:8px 14px !important; font-size:13px !important; }
    .row-label { display:block !important; width:100% !important; padding:16px 0 6px 0 !important; }
    .row-value { display:block !important; width:100% !important; padding:0 0 20px 0 !important; }
    .card { padding:18px !important; }
    }
</style>
</head>
<body style='margin:0; padding:0; background:#f8f9fa; font-family:Arial, Helvetica, sans-serif;'>
<div style='display:none; max-height:0; overflow:hidden; mso-hide:all;'>
    Activity submission received — <?= htmlspecialchars($eventName) ?>
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
            
            <!-- Rainbow header bar -->
            <tr>
            <td style='padding:0;'>
                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td style='background:#e53935; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#fb8c00; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#fdd835; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#43a047; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#1e88e5; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#8e24aa; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                </tr>
                </table>
            </td>
            </tr>

            <!-- Header -->
            <tr>
            <td align='center' style='padding:40px 28px 12px 28px; background:linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%); background-color:#f8f9fa;'>
                <div style='display:inline-block; background:linear-gradient(135deg, #e53935 0%, #fb8c00 50%, #fdd835 100%); background-color:#fb8c00; color:#ffffff; padding:8px 16px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; margin-bottom:16px;'>
                    Documentation
                </div>
                <h1 class='h1' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:36px; color:#1a1a1a; font-weight:700;'>
                    Volunteer Activity Recorded
                </h1>
                <p class='subtitle' style='margin:12px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:24px; color:#6b7280;'>
                    Your submission has been successfully received
                </p>
            </td>
            </tr>

            <!-- Content -->
            <tr>
            <td class='inner' style='padding:32px 28px;'>
                
                <!-- Card container -->
                <div class='card' style='background:#f8f9fa; border-radius:12px; padding:24px; margin-bottom:24px;'>
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='border-collapse:collapse;'>
                    
                    <!-- Name -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Name
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:17px; line-height:26px; color:#111827; font-weight:600;'>
                       <?= htmlspecialchars($person_name) ?>
                    </td>
                    </tr>

                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                    <!-- Role -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Role
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:17px; line-height:26px; color:#111827; font-weight:600;'>
                        <?= htmlspecialchars($role_title) ?>
                    </td>
                    </tr>

                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                    <!-- Event -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Event
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:17px; line-height:26px; color:#111827; font-weight:600;'>
                        <?= htmlspecialchars($eventName) ?>
                    </td>
                    </tr>
                    
                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>
                    
                    <!-- Date -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Date
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                        <?= htmlspecialchars($date) ?>
                    </td>
                    </tr>
                    
                    <!-- Spacer -->
                    <tr><td colspan='2' style='padding:12px 0;'></td></tr>
                    
                    <!-- Hours -->
                    <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                        Hours Logged
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                        <?= htmlspecialchars($hours_spent) ?> hours
                    </td>
                    </tr>
                    
                </table>
                </div>
                
                <!-- Attachments section -->
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
                <tr>
                    <td class='row-label' valign='top' width='140' style='padding:0 20px 12px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                    Attachments
                    </td>
                    <td class='row-value' valign='top' style='padding:0 0 0 0;'>
                    <!-- Badge -->
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' style='margin-bottom:16px;'>
                        <tr>
                        <td class='badge' style='background:linear-gradient(135deg, #43a047 0%, #1e88e5 100%); background-color:#43a047; color:#ffffff; padding:10px 20px; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:600; border-radius:10px; box-shadow:0 2px 8px rgba(67,160,71,0.2);'>
                            <?= htmlspecialchars($photoCount) ?> file(s)
                        </td>
                        </tr>
                    </table>
                    <!-- File list container -->
                    <div style='padding:20px; background:#ffffff; border:2px solid #e5e7eb; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.05);'>
                        <div style='font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#6b7280;'>
                            <?= htmlspecialchars($photoList) ?>
                        </div>
                    </div>
                    </td>
                </tr>
                </table>
                
            </td>
            </tr>

            <!-- Footer -->
            <tr>
            <td align='center' style='padding:32px 28px; background:#f9fafb; border-top:1px solid #e5e7eb;'>
                <p class='text' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#9ca3af;'>
                    Automatically generated by the Documentation System
                </p>
                <p class='text' style='margin:6px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#d1d5db;'>
                    <?= date('F j, Y \a\t g:i A') ?>
                </p>
            </td>
            </tr>

            <!-- Rainbow footer bar -->
            <tr>
            <td style='padding:0;'>
                <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                <tr>
                    <td style='background:#e53935; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#fb8c00; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#fdd835; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#43a047; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#1e88e5; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                    <td style='background:#8e24aa; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
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