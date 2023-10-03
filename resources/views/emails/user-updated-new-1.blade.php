<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html data-editor-version="2" class="sg-campaigns" xmlns="http://www.w3.org/1999/xhtml">
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>brandx update user details</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .main {
            width: 100%;
            height: 100%;
            background: #EEF2F6;
        }

        .logo-center {
            margin: 0% 20%;
            padding: 20px;
            height: 100%;
        }

        .main-section {
            background: #FBFBFB;
            margin: 0% 10%;
            padding: 20px;
            padding-left: 40px;
            padding-right: 40px;
            height: 100%;
        }

        .title-section {
            color: #231F20;
            /* font-family: Roboto; */
            font-size: 36px;
            font-style: normal;
            font-weight: 900;
            line-height: normal;
        }

        .sub-title-section {
            color: #231F20;
            /* font-family: Roboto; */
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .subcriber-section {
            margin: 0% 20%;
            padding: 45px;
            /* padding-left: 40px;
            padding-right: 40px; */
            background: #F4E44C;
            border-radius: 50px;
            height: 100%;
        }

        .clearfix {
            content: "";
            clear: both;
            display: table;
        }

        .important-section {
            background: rgba(244, 228, 76, 0.31);
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 32px;
            padding-right: 32px;
        }

        .imp-description {
            color: #404952;
            font-family: Roboto;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .imp-title {
            color: #404952;
            text-align: center;
            font-family: Roboto;
            font-size: 24px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .box-title {
            background: #333;
            display: inline-block;
            /* Allows the container to expand based on content */
            padding: 10px 20px;
            /* Add padding for spacing around the text */
            color: #fff;
            /* Text color */
            border: none;
            /* Remove border */
            cursor: pointer;
            /* Add cursor pointer on hover */
            color: #F4E44C;
            font-family: Roboto;
            font-size: 36px;
            font-style: normal;
            font-weight: 900;
            line-height: normal;
        }
    </style>
</head>

<body>
    <div class="main">
        {{-- logo --}}
        <div class="logo-center">
            <center>
                <img style="width: 290.86px;
                height: 70px;
                left: 396px;
                top: 52px;"
                    src="{{ asset('/images/logo.png') }}" />
            </center>
        </div>
        {{-- content --}}
        <div class="main-section">
            <br />
            <div class="title-section">
                Welcome to Brand X
            </div>
            <br />
            <div class="sub-title-section">
                Hi {{ '<USER FIRST NAME>' }},
                <br />
                <br />
                This email is confirmation that your Artist Pass subscription is now active. A receipt will be
                emailed to you shortly.
            </div>
            <br />
            <br />
            <div class="subcriber-section" style="">
                <center>
                    <div style="float: left; width: 50%;">
                        <img style="width: 237px; height: 192px; left: 337px; top: 497px;"
                            src="{{ asset('/images/Brand_X_ArtistPass_Logo.png') }}" />
                    </div>
                    <div style="float: left; width: 50%; padding-top:100px;">
                        <span
                            style="color: black; font-size: 16px; font-family: Roboto; font-weight: 400; word-wrap: break-word">
                            {{ '<Company Name>' }}<br />
                        </span>
                        <span
                            style="color: black; font-size: 16px; font-family: Roboto; font-weight: 700; word-wrap: break-word">Arts
                            Organisation <br />
                        </span>
                        <span
                            style="color: black; font-size: 16px; font-family: Roboto; font-weight: 400; word-wrap: break-word">Joined:
                            10/07/2023<br />
                            Expiry: 09/07/2024
                        </span>
                    </div>
                    <div class="clearfix"></div>
                </center>
            </div>
            <br />
            <br />
            <br />
            <br />
            <div class="title-section">
                Book a space
            </div>
            <div class="sub-title-section">
                Your Artist Pass enables you to book spaces starting *from {{ '<PRICE>' }}. This is a
                {{ '<PERCENT>' }}
                discount from our base rate of $66 per hour.
                <br />
                <br />

                *Please note:  when using the space for a commercial activity you may be charged slightly more. This
                enables Brand X to continue to offer the lowest rates possible for non commercial activities and
                solo artists.
                <br />
                <br />

                To make a booking for a studio space, login and click BOOK A SPACE (in the top right corner), to
                view the calendar and select times to book.
                <br />
                <br />

                Further discounts apply when booking 8 hours in one day or 40 hours in one week. Use codes
                “8hourday” and “40hourweek” respectively at the checkout.
            </div>
            <br />

            <div class="important-section">
                <div class="imp-title">
                    Important
                </div>
                <div class="imp-description">
                    When booking a space for the first time our calendar will prevent you from booking within an initial
                    12 hour period. This is to allow our staff time to prepare for your first visit and site induction.
                    After your first booking, all time restrictions will be automatically removed for the term of your
                    subscription to Brand X.
                </div>
            </div>
            <br />
            <br />
            <div class="box-title">
                View booking calendar
            </div>
            <br />
            <br />
            <br />
            <div class="title-section">
                Get Discounted Tickets to Flying Nun
            </div>
            <br />
            <div class="sub-title-section">
                Artist Pass subscribers receive a $5 discount to all The Flying Nun performances at the East Sydney
                Community and Arts Centre in Darlinghurst.
                <br />
                <br />
                To book tickets to a show, copy and paste this code into the "promo code" section when purchasing
                tickets to claim your concession: <strong>artistpass-4567382</strong>
            </div>
            <br />
            <br />
            <div class="box-title">
                Check out the Flying Nun
            </div>
            <br />
            <br />
            <br />
            <div class="title-section">
                Free access to A2A (Artist-2-Artist) Program
            </div>
            <br />
            <div class="sub-title-section">
                Join industry professionals across Sydney as they share their knowledge and passion for their arts
                practice.
                <br />
                <br />
                Valued at $25 per ticket, Artist Pass holders can access any online or in person A-2A program for free
                with the code **artistpass-4567382**
            </div>
            <br />
            <div class="box-title" style="flex: left;">
                A-2-A in person
            </div>
            <div class="box-title " style="flex: left;">
                A-2-A in on demand
            </div>
            <div class="clearfix"></div>
            <br />
            <div class="sub-title-section">
                Please note that Artist Pass benefits are based on accurate answers to questions during registration. If
                your circumstances change at any time, please reach out and contact us at programs@brandx.org.au.
                <br />
                <br />
                Look forward to seeing you in our spaces, at a show or in an A2A program.
            </div>
            <div class="sub-title-section">
                Thanks,<br />
                The Brand X Team
            </div>
        </div>
    </div>

</body>

</html>
