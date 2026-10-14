<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AgriCo Frequently Asked Questions
    |--------------------------------------------------------------------------
    |
    | Master dataset for the public AgriCo FAQ page (/faq). Each entry includes
    | a unique anchor slug for direct deep-linking, category, clear student &
    | farmer-friendly answers, relative internal links, and search keywords.
    |
    */

    'categories' => [
        'all'              => 'All Questions',
        'getting-started'  => 'Getting Started',
        'account-password' => 'Account & Password',
        'suspended-account'=> 'Suspended Accounts',
        'soil-scanning'    => 'Soil Scanning',
        'soil-map'         => 'Soil Map & Pins',
        'munsell-chart'    => 'Munsell Color Chart',
        'mobile-app'       => 'Mobile App',
        'terms-privacy'    => 'Terms & Privacy',
        'support-contact'  => 'Support & Contact',
    ],

    'items' => [
        // ── GETTING STARTED ──
        [
            'id'       => 'what-is-agrico',
            'category' => 'getting-started',
            'question' => 'What is AgriCo and what can I do with it?',
            'answer'   => 'AgriCo is a smart agriculture platform designed to help farmers, students, and researchers identify soil colors and classifications using standard Munsell charts. You can upload or capture a photo of your soil sample on the <a href="/scan">Soil Scanner</a> to get instant color codes and soil names. You can also explore community soil samples on our interactive <a href="/map">Soil Map</a> and browse the complete <a href="/munsell">Munsell Color Chart</a>.',
            'keywords' => 'agrico overview scan soil map munsell features farmers agriculture introduction',
        ],
        [
            'id'       => 'how-to-register',
            'category' => 'getting-started',
            'question' => 'How do I create an account?',
            'answer'   => 'Click <a href="/register">Sign Up</a> on the top menu to create your free AgriCo account. Enter your full name, email address, password, and choose whether you are a general user or a student. If you are a student, please provide your Student ID and class section so administrators can verify your academic profile.',
            'keywords' => 'register signup create account student id section new account profile',
        ],
        [
            'id'       => 'account-approval',
            'category' => 'getting-started',
            'question' => 'Why can\'t I access all features right after I register?',
            'answer'   => 'To ensure the accuracy and reliability of soil data on our public map, all new accounts are reviewed by an administrator before full access is unlocked. While your registration is pending approval, you can browse public resources, but scanning and pinning locations will be enabled once approved. You will receive an email confirmation as soon as an administrator approves your request.',
            'keywords' => 'pending approval admin review access unlocked registration verification why waiting',
        ],
        [
            'id'       => 'approval-notification',
            'category' => 'getting-started',
            'question' => 'How will I know if my account was approved or not approved?',
            'answer'   => 'When an administrator reviews your registration, an automated notification is sent directly to your registered email address from our system. If approved, you can immediately <a href="/login">Log In</a> and start scanning soil. If your request cannot be approved, you will receive an email explaining the decision, or you can contact our support team at <a href="mailto:agricosite77@gmail.com">agricosite77@gmail.com</a>.',
            'keywords' => 'approval notification email confirmation disapproved status check email',
        ],

        // ── ACCOUNT & PASSWORD ──
        [
            'id'       => 'forgot-password',
            'category' => 'account-password',
            'question' => 'I forgot my password. What do I do?',
            'answer'   => 'Go to the <a href="/forgot-password">Forgot Password</a> page and enter the email address linked to your AgriCo account. We will immediately email you a 6-digit verification code. Enter that code on the verification screen to securely create a new password and log back in.',
            'keywords' => 'forgot password reset code 6 digit recovery account login issue',
        ],
        [
            'id'       => 'no-code',
            'category' => 'account-password',
            'question' => 'I didn\'t receive the 6-digit code. What should I check?',
            'answer'   => 'First, check your spam or junk mail folder, as automated emails can occasionally be filtered there. Make sure the email address you typed on the <a href="/forgot-password">Forgot Password</a> page exactly matches the one you registered with. If the email still has not arrived after a few minutes, you can click "Resend code" on the verification page.',
            'keywords' => 'did not receive code verification email missing spam junk resend code',
        ],
        [
            'id'       => 'expired-code',
            'category' => 'account-password',
            'question' => 'My code is wrong or expired. What now?',
            'answer'   => 'For security, each 6-digit password reset code expires after <strong>15 minutes</strong>. If your code has expired or was entered incorrectly, simply return to the <a href="/forgot-password">Forgot Password</a> page to request a fresh code. If you make too many unsuccessful attempts, please wait 15 minutes before trying again.',
            'keywords' => 'expired code invalid code retry limit 15 minutes reset code token',
        ],
        [
            'id'       => 'strong-password',
            'category' => 'account-password',
            'question' => 'What makes a strong password?',
            'answer'   => 'AgriCo requires a strong password to keep your research and soil data safe. Your password must be at least <strong>8 characters long</strong> and include at least one uppercase letter, one lowercase letter, one number, and one special character (such as !@#$%&*). Avoid using common words, your name, or birthdates.',
            'keywords' => 'password requirements strong security rules uppercase lowercase number special character length',
        ],
        [
            'id'       => 'profile-details',
            'category' => 'account-password',
            'question' => 'How do I change my profile details?',
            'answer'   => 'Once logged in, visit your <a href="/profile">Profile</a> page from the navigation bar. From there, you can update your full name, email address, student details, and upload a new profile picture. If you wish to change your password, you will be asked to enter your current password first for verification.',
            'keywords' => 'profile edit name email avatar photo change password student id section update',
        ],

        // ── SUSPENDED ACCOUNTS ──
        [
            'id'       => 'why-suspended',
            'category' => 'suspended-account',
            'question' => 'Why is my account suspended?',
            'answer'   => 'Accounts are temporarily suspended when an administrator notices repeated invalid uploads, non-soil images, or activity that violates community guidelines. When you attempt to <a href="/login">Log In</a>, a notice will appear detailing the exact reason for the suspension and who issued it.',
            'keywords' => 'account suspended why banned blocked suspension notice violation reason',
        ],
        [
            'id'       => 'suspension-duration',
            'category' => 'suspended-account',
            'question' => 'How long does a suspension last?',
            'answer'   => 'Most temporary suspensions last between 24 hours and a few days, depending on the reason provided by the administrator. Your login screen displays an exact countdown timer showing when the suspension ends. Once the timer reaches zero, your account automatically reactivates on your next login attempt.',
            'keywords' => 'suspension length duration countdown 24 hours timer auto reactivate when end',
        ],
        [
            'id'       => 'request-reactivation',
            'category' => 'suspended-account',
            'question' => 'How do I request reactivation, and how long does it take?',
            'answer'   => 'If you believe your account was suspended by mistake, click the <strong>Request Reactivation</strong> button directly on the suspended notice on the <a href="/login">Log In</a> page. This sends an urgent appeal ticket to the administration team. Review typically takes 1 to 2 business days, and you will receive an email as soon as an administrator resolves your request.',
            'keywords' => 'request reactivation appeal unban unlock account review duration admin appeal',
        ],

        // ── SOIL SCANNING ──
        [
            'id'       => 'how-to-scan',
            'category' => 'soil-scanning',
            'question' => 'How do I scan my soil?',
            'answer'   => 'Navigate to the <a href="/scan">Soil Scanner</a> page while logged into your approved account. You can either take a fresh photo using your mobile camera or upload an existing image from your device. Click "Scan Soil" and our machine learning model will analyze the sample and display the Munsell color match within seconds.',
            'keywords' => 'how to scan upload soil photo take picture camera scanner steps analyze',
        ],
        [
            'id'       => 'photo-guide',
            'category' => 'soil-scanning',
            'question' => 'How do I take a good photo for scanning?',
            'answer'   => 'For the most accurate result, place a clean, smooth clump of soil on a neutral background in natural, indirect daylight. Avoid harsh shadows, camera flash, direct glare, and artificial yellow lighting. Make sure fingers, tools, leaves, and background clutter are kept out of the camera frame.',
            'keywords' => 'photo guidelines tips lighting glare shadow good picture clear soil photography camera',
        ],
        [
            'id'       => 'scan-results',
            'category' => 'soil-scanning',
            'question' => 'What does the scan result mean?',
            'answer'   => 'The result shows the dominant <strong>Munsell notation</strong> (such as 10YR 4/3), the common soil color name (such as Brown), and a confidence percentage indicating how closely the sample matches our reference catalog. You will also see secondary color tones detected within the sample. These details help assess soil composition, organic matter, and moisture levels.',
            'keywords' => 'scan result meaning dominant munsell percentage confidence score soil color',
        ],
        [
            'id'       => 'what-is-munsell',
            'category' => 'soil-scanning',
            'question' => 'What is a Munsell code?',
            'answer'   => 'The Munsell system is the global scientific standard used by soil scientists and agronomists to describe soil colors precisely. A code like 5YR 5/6 describes three attributes: Hue (color family), Value (lightness from black to white), and Chroma (color purity or saturation). You can browse all standard soil colors on our <a href="/munsell">Munsell Color Chart</a>.',
            'keywords' => 'munsell code definition hue value chroma soil science classification explanation',
        ],
        [
            'id'       => 'different-results',
            'category' => 'soil-scanning',
            'question' => 'Why is the result different from another scan of the same soil?',
            'answer'   => 'Soil color naturally changes when the sample is wet versus dry, as moisture darkens the soil significantly. Changes in camera angle, distance, or ambient room lighting can also alter how color appears to the sensor. For consistent comparisons, try to scan soil samples when they are air-dried under similar daylight conditions.',
            'keywords' => 'different result inconsistency wet dry lighting angle camera variation why changed',
        ],
        [
            'id'       => 'scan-limits',
            'category' => 'soil-scanning',
            'question' => 'Can I scan more than once?',
            'answer'   => 'Yes, you can scan as many soil samples as you need! Every completed scan is saved to your personal history so you can review previous readings anytime. If you are examining a field, we recommend taking samples from several different spots to understand the overall soil variation across your land.',
            'keywords' => 'scan limit multiple scans history repeated scans field sampling quotas',
        ],

        // ── SOIL MAP & PINS ──
        [
            'id'       => 'map-pins',
            'category' => 'soil-map',
            'question' => 'What are the pins on the map?',
            'answer'   => 'Each pin on the <a href="/map">Interactive Soil Map</a> represents a real, verified soil sample collected across the municipality of Lal-lo and neighboring areas. Clicking on any pin displays the dominant Munsell color code, soil group name, barangay, and the estimated covered area.',
            'keywords' => 'map pins markers soil map locations lal-lo barangay points data',
        ],
        [
            'id'       => 'who-can-see-pins',
            'category' => 'soil-map',
            'question' => 'Who can see my scan on the map?',
            'answer'   => 'When you submit a location pin from your scan result, it is sent to administrators for validation. Once approved, the pin becomes visible to all farmers, students, and guests visiting the public <a href="/map">Soil Map</a>. Your personal account credentials and private notes remain secure and are never shared publicly.',
            'keywords' => 'privacy visibility who can see public map pin security anonymous',
        ],
        [
            'id'       => 'pin-not-showing',
            'category' => 'soil-map',
            'question' => 'Why is my pin not showing on the map?',
            'answer'   => 'Newly submitted pins remain in a pending state until an administrator verifies that the GPS coordinates and soil data are valid. This moderation step prevents duplicate or incorrect entries from cluttering the map. Once reviewed and approved, your pin will immediately appear on the live map.',
            'keywords' => 'pin missing not showing pending admin approval map marker delay',
        ],

        // ── MUNSELL COLOR CHART ──
        [
            'id'       => 'how-to-use-chart',
            'category' => 'munsell-chart',
            'question' => 'What is the Munsell Color Chart and how do I use it?',
            'answer'   => 'The <a href="/munsell">Munsell Color Chart</a> is a digital reference library of standardized agricultural soil colors. You can filter by Hue tabs (such as 10R, 5YR, or 10YR) to compare color chips side-by-side with your physical soil samples. Each swatch shows the exact code, agronomic color name, and color preview.',
            'keywords' => 'munsell chart guide how to use colors swatches chips hues digital catalog',
        ],
        [
            'id'       => 'how-to-read-munsell',
            'category' => 'munsell-chart',
            'question' => 'How do I read hue, value, and chroma (for example 5YR 6/6)?',
            'answer'   => 'In a code like 5YR 6/6, <strong>5YR</strong> is the <strong>Hue</strong> (representing a balance of Yellow and Red). The first number <strong>6</strong> is the <strong>Value</strong>, indicating lightness on a scale where 0 is pure black and 10 is pure white. The second number <strong>/6</strong> is the <strong>Chroma</strong>, measuring the strength or saturation of the color.',
            'keywords' => 'read munsell hue value chroma 5yr 6/6 notation formula explanation',
        ],

        // ── MOBILE APP ──
        [
            'id'       => 'download-app',
            'category' => 'mobile-app',
            'question' => 'How do I download the mobile app?',
            'answer'   => 'You can download the AgriCo Android application directly from our <a href="/download">Download App</a> page. The installer is provided as an APK file (v1.0.4, ~59 MB) that you can install on any compatible Android device. Make sure to allow installation from your browser when prompted by Android.',
            'keywords' => 'download mobile app apk android install application offline scan',
        ],
        [
            'id'       => 'app-updates',
            'category' => 'mobile-app',
            'question' => 'How do I know I have the latest version?',
            'answer'   => 'The mobile app automatically checks for new releases every time it opens by contacting our server. If an update is available, an in-app banner will notify you and let you download the latest version with one tap. You can also re-download the newest version at any time from the <a href="/download">Download App</a> page.',
            'keywords' => 'latest version update app version checker notification release upgrade',
        ],

        // ── TERMS & PRIVACY ──
        [
            'id'       => 'terms-and-privacy',
            'category' => 'terms-privacy',
            'question' => 'What are AgriCo\'s Terms of Service and Privacy Policy?',
            'answer'   => 'AgriCo is dedicated to open agricultural education and research while protecting your personal information. When you register, we collect your name, email, and academic details solely to administer your account and verify soil scans. We never sell your personal data, and public soil map pins only display soil classifications and geographic barangay locations.',
            'keywords' => 'terms of service privacy policy data protection user safety rights rules agreement',
        ],

        // ── SUPPORT & CONTACT ──
        [
            'id'       => 'contact-support',
            'category' => 'support-contact',
            'question' => 'Who do I contact if my problem isn\'t listed here?',
            'answer'   => 'If you need further assistance or encounter technical difficulties, you can reach out directly to our administrative support team by emailing <a href="mailto:agricosite77@gmail.com">agricosite77@gmail.com</a>. Please include your registered email address and a brief description of the issue so we can help you promptly.',
            'keywords' => 'contact support email help desk assistance customer service report problem',
        ],
    ],
];
