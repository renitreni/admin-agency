@php
    $monitoringI18n = [
        'appInstalled' => __('monitoring.js.app_installed'),
        'appAlreadyInstalled' => __('monitoring.js.app_already_installed'),
        'installNotAvailable' => __('monitoring.js.install_not_available'),
        'installing' => __('monitoring.js.installing'),
        'installCancelled' => __('monitoring.js.install_cancelled'),
        'gettingLocation' => __('monitoring.js.getting_location'),
        'geolocationNotSupported' => __('monitoring.js.geolocation_not_supported'),
        'locationCaptured' => __('monitoring.js.location_captured'),
        'locationFailed' => __('monitoring.js.location_failed'),
        'gettingLocationButton' => __('monitoring.js.getting_location_button'),
        'locationRequiredButton' => __('monitoring.js.location_required_button'),
        'locationCapturedEmergency' => __('monitoring.js.location_captured_emergency'),
        'locationFailedEmergency' => __('monitoring.js.location_failed_emergency'),
        'locationAlert' => __('monitoring.js.location_alert'),
        'confirmEmergency' => __('monitoring.form.confirm_emergency'),
        'reportCharCount' => __('monitoring.form.report_char_count', ['count' => ':count']),
    ];
@endphp
<script>
window.monitoringI18n = @json($monitoringI18n);
</script>
