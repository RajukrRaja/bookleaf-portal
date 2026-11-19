<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body style="margin:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#202938;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#fff;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="background:#182b49;color:#fff;padding:22px 28px;font-size:21px;font-weight:bold;">BookLeaf Author Support</td>
                        </tr>
                        <tr>
                            <td style="padding:28px;">
                                <p style="margin:0 0 16px;font-size:16px;">
                                    Hello
                                    {{ $recipientRole === 'admin' ? 'Support Team' : ($details['author_name'] ?? 'Author') }}
                                    ,
                                </p>
                                <h2 style="font-size:20px;margin:0 0 12px;">{{ $activity }}</h2>
                                <p style="line-height:1.6;margin:0 0 20px;">{{ $details['message'] ?? 'There has been an update to a support ticket.' }}</p>
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="background:#f7f8fa;border:1px solid #e7ebf0;border-radius:8px;">
                                    <tr>
                                        <td style="color:#687386;width:145px;">Ticket</td>
                                        <td>
                                            <strong>{{ $details['ticket_number'] }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:#687386;">Subject</td>
                                        <td>{{ $details['subject'] }}</td>
                                    </tr>
                                    @if(!empty($details['book_title']))
                                        <tr>
                                            <td style="color:#687386;">Book</td>
                                            <td>{{ $details['book_title'] }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="color:#687386;">Status</td>
                                        <td>{{ $details['status'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#687386;">Priority</td>
                                        <td>{{ $details['priority'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#687386;">Category</td>
                                        <td>{{ $details['category'] }}</td>
                                    </tr>
                                    @if(!empty($details['changed_to']))
                                        <tr>
                                            <td style="color:#687386;">Changed to</td>
                                            <td>{{ $details['changed_to'] }}</td>
                                        </tr>
                                    @endif
                                    @if(!empty($details['assigned_to']))
                                        <tr>
                                            <td style="color:#687386;">Assigned to</td>
                                            <td>{{ $details['assigned_to'] }}</td>
                                        </tr>
                                    @endif
                                </table>
                                <p style="margin:24px 0 0;">
                                    <a href="{{ $details['ticket_url'] }}" style="display:inline-block;background:#e66a35;color:#fff;text-decoration:none;padding:12px 18px;border-radius:6px;font-weight:bold;">View ticket</a>
                                </p>
                                <p style="font-size:12px;color:#7b8492;line-height:1.5;margin:24px 0 0;">This is an automated notification from BookLeaf Author Support Portal. Please do not reply to this email address.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
