<?php

return [
    'locale' => [
        'english' => 'English',
        'filipino' => 'Filipino',
        'switch_label' => 'Language',
    ],

    'errors' => [
        'heading' => 'Please correct the following errors:',
        'invalid_credentials' => 'Invalid passport number or secret code.',
        'not_deployed' => 'Worker is not currently deployed.',
    ],

    'attributes' => [
        'passport_number' => 'Passport Number',
        'secret_code' => 'Secret Code',
        'report' => 'Report',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
    ],

    'messages' => [
        'report_submitted' => 'Report submitted successfully.',
        'emergency_already_active' => 'An emergency alert is already active for your account. Please wait for assistance.',
        'emergency_sent' => 'Emergency alert sent successfully. Help is on the way.',
        'location_required_emergency' => 'Location is required for emergency alerts. Please enable location services.',
    ],

    'login' => [
        'page_title' => 'Worker Monitoring Login',
        'heading' => 'Worker Reporting Login',
        'subtitle' => 'Input your passport number and 5-digit secret code to submit a monitoring report.',
        'passport_number' => 'Passport Number',
        'secret_code' => 'Secret Code (5 digits)',
        'continue' => 'Continue',
        'cancel' => 'Cancel',
        'pwa_install_hint' => 'Install this app on your device for quick access',
        'pwa_install_button' => 'Download & Install App',
    ],

    'form' => [
        'page_title' => 'Worker Monitoring Report',
        'heading' => 'Worker Reporting Form',
        'agency' => 'Agency',
        'worker' => 'Worker',
        'emergency' => 'EMERGENCY',
        'logout' => 'Logout',
        'honeypot_label' => 'Leave blank',
        'passport_number' => 'Passport Number',
        'secret_code' => 'Secret Code',
        'use_location' => 'Use my location',
        'report_label' => 'Monitoring Report',
        'report_max_chars' => '(max 10,000 characters)',
        'report_char_count' => ':count / 10,000 characters',
        'submit_report' => 'Submit Report',
        'emergency_modal_title' => 'Emergency Alert',
        'emergency_modal_body' => 'Are you sure you want to send an :strong? This will immediately notify the agency of your distress.',
        'emergency_modal_body_strong' => 'EMERGENCY ALERT',
        'emergency_location_required_title' => 'Location Required',
        'emergency_location_required_body' => 'Your current location will be captured and sent with this emergency alert. Please ensure location services are enabled.',
        'getting_location' => 'Getting your location...',
        'cancel' => 'Cancel',
        'confirm_emergency' => 'Yes, Send Emergency',
    ],

    'js' => [
        'app_installed' => 'App Installed',
        'app_already_installed' => 'This app is already installed on your device',
        'install_not_available' => 'Installation not available. Please use your browser menu to install.',
        'installing' => 'Installing...',
        'install_cancelled' => 'Installation cancelled. Click the button to try again.',
        'getting_location' => 'Getting location…',
        'geolocation_not_supported' => 'Geolocation is not supported by your browser.',
        'location_captured' => 'Location captured.',
        'location_failed' => 'Could not get location. You can still submit without it.',
        'getting_location_button' => 'Getting location...',
        'location_required_button' => 'Location Required',
        'location_captured_success' => 'Location captured successfully.',
        'location_captured_emergency' => 'Location captured successfully.',
        'location_failed_emergency' => 'Could not get location. Please enable location services and try again.',
        'location_alert' => 'Location is required for emergency alerts. Please enable location services.',
    ],
];
