<!DOCTYPE html>
<html>

    <head>
        <title>Code Verification</title>
    </head>

    <body>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"
            style="font-family: 'Roboto', sans-serif;">
            <!-- START HEADER/BANNER -->
            <link
                href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
                rel="stylesheet">
            <tbody>
                <tr>
                    <td align="center">
                        <table class="" width="800" border="0" align="center" cellpadding="0"
                            cellspacing="0">
                            <tbody>
                                {{-- <tr>
                                <td align="center" valign="top">
                                    <table class="" width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="background: #f9f9f9;">
                                        <tbody>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="line-height: 0px;">
                                                    <img style="display:block; line-height:0px; font-size:0px; border:0px;" src="assets/images/logo.png" width="140" height="" alt="logo">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="20"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr> --}}
                                <tr>
                                    <td align="center" valign="top"
                                        style="background-size:cover; background-position:top; background: #643271;">
                                        <table class="" width="800" border="0" align="center"
                                            cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td height="20"></td>
                                                </tr>
                                                <tr>
                                                    <td align="center" style="">
                                                        {{-- <img style="display: block;
                                                    z-index: 999999;
                                                    margin-bottom: 10px;" src="assets/images/email.png" width="250" height="" alt="logo"> --}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="10"></td>
                                                </tr>
                                                <tr>
                                                    <td align="center"
                                                        style="color: #fff; font-size: 30px;font-weight: 300;">
                                                        Thanks For Joining!
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="10"></td>
                                                </tr>
                                                <tr>
                                                    <td align="center"
                                                        style="color: #fff; font-size: 35px;font-weight: 600;">
                                                        Verify Your Access To Lion Roaring
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="20"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <table class="" width="800" border="0" align="center" cellpadding="0"
                            cellspacing="0" style="background: #f9f9f9;">
                            <tbody>
                                <tr>
                                    <td height="35"></td>
                                </tr>
                                <tr>
                                    <td align="left" style="padding: 0 40px;">
                                        <p style="font-size:16px; color:#313131; line-height:24px; font-weight: 400;">
                                            Hello,</p>
                                        <p style="font-size:16px; color:#313131; line-height:24px; font-weight: 400;">
                                            Please use the following one time password(Code)
                                        </p>
                                        <ul style="list-style: none; padding-left: 0;">
                                            @foreach (str_split($otp) as $digit)
                                                <li
                                                    style="list-style: none; display: inline-block; border: 1px solid #643271; width: 60px; height: 60px; padding: 0px 0px; font-size: 45px; text-align: center; font-weight: 600; color: #643271; border-radius: 5px;">
                                                    {{ $digit }}
                                                </li>
                                            @endforeach
                                        </ul>

                                        <p style="font-size:16px; color:#313131; line-height:24px; font-weight: 400;">
                                            This passcode will only be valid temporary. If the
                                            passcode does not work, try again to login.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>





            </tbody>
        </table>

    </body>

</html>
