<?php

return [
    'locale' => [
        'english' => 'Ingles',
        'filipino' => 'Filipino',
        'switch_label' => 'Wika',
    ],

    'errors' => [
        'heading' => 'Pakitama ang mga sumusunod na error:',
        'invalid_credentials' => 'Hindi wasto ang numero ng pasaporte o lihim na code.',
        'not_deployed' => 'Ang manggagawa ay kasalukuyang hindi naka-deploy.',
    ],

    'attributes' => [
        'passport_number' => 'Numero ng Pasaporte',
        'secret_code' => 'Lihim na Code',
        'report' => 'Ulat',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
    ],

    'messages' => [
        'report_submitted' => 'Matagumpay na naisumite ang ulat.',
        'emergency_already_active' => 'May aktibong emergency alert na para sa iyong account. Pakihintay ang tulong.',
        'emergency_sent' => 'Matagumpay na naipadala ang emergency alert. Paparating na ang tulong.',
        'location_required_emergency' => 'Kinakailangan ang lokasyon para sa emergency alert. Pakibuksan ang location services.',
    ],

    'login' => [
        'page_title' => 'Pag-login sa Worker Monitoring',
        'heading' => 'Pag-login sa Worker Reporting',
        'subtitle' => 'Ilagay ang numero ng pasaporte at 5-digit na lihim na code upang magsumite ng ulat sa monitoring.',
        'passport_number' => 'Numero ng Pasaporte',
        'secret_code' => 'Lihim na Code (5 digit)',
        'continue' => 'Magpatuloy',
        'cancel' => 'Kanselahin',
        'pwa_install_hint' => 'I-install ang app na ito sa iyong device para sa mabilis na access',
        'pwa_install_button' => 'I-download at I-install ang App',
    ],

    'form' => [
        'page_title' => 'Ulat sa Worker Monitoring',
        'heading' => 'Form sa Worker Reporting',
        'agency' => 'Ahensya',
        'worker' => 'Manggagawa',
        'emergency' => 'EMERGENCY',
        'logout' => 'Mag-logout',
        'honeypot_label' => 'Iwanang blangko',
        'passport_number' => 'Numero ng Pasaporte',
        'secret_code' => 'Lihim na Code',
        'use_location' => 'Gamitin ang aking lokasyon',
        'report_label' => 'Ulat sa Monitoring',
        'report_max_chars' => '(max na 10,000 character)',
        'report_char_count' => ':count / 10,000 character',
        'submit_report' => 'Isumite ang Ulat',
        'emergency_modal_title' => 'Emergency Alert',
        'emergency_modal_body' => 'Sigurado ka bang gusto mong magpadala ng :strong? Agad na aabisuhan ang ahensya tungkol sa iyong kalagayan.',
        'emergency_modal_body_strong' => 'EMERGENCY ALERT',
        'emergency_location_required_title' => 'Kinakailangan ang Lokasyon',
        'emergency_location_required_body' => 'Kukunin at ipapadala ang iyong kasalukuyang lokasyon kasama ng emergency alert na ito. Pakitiyak na naka-on ang location services.',
        'getting_location' => 'Kinukuha ang iyong lokasyon...',
        'cancel' => 'Kanselahin',
        'confirm_emergency' => 'Oo, Ipadala ang Emergency',
    ],

    'js' => [
        'app_installed' => 'Naka-install na ang App',
        'app_already_installed' => 'Naka-install na ang app na ito sa iyong device',
        'install_not_available' => 'Hindi available ang pag-install. Gamitin ang menu ng browser para mag-install.',
        'installing' => 'Ini-install...',
        'install_cancelled' => 'Kinansela ang pag-install. I-click ang button para subukang muli.',
        'getting_location' => 'Kinukuha ang lokasyon…',
        'geolocation_not_supported' => 'Hindi suportado ng browser mo ang geolocation.',
        'location_captured' => 'Nakuha ang lokasyon.',
        'location_failed' => 'Hindi makuha ang lokasyon. Maaari ka pa ring magsumite nang wala nito.',
        'getting_location_button' => 'Kinukuha ang lokasyon...',
        'location_required_button' => 'Kinakailangan ang Lokasyon',
        'location_captured_success' => 'Matagumpay na nakuha ang lokasyon.',
        'location_captured_emergency' => 'Matagumpay na nakuha ang lokasyon.',
        'location_failed_emergency' => 'Hindi makuha ang lokasyon. Pakibuksan ang location services at subukang muli.',
        'location_alert' => 'Kinakailangan ang lokasyon para sa emergency alert. Pakibuksan ang location services.',
    ],
];
