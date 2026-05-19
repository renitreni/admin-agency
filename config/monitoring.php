<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monitoring Thresholds
    |--------------------------------------------------------------------------
    |
    | These values define the number of days after which workers should submit
    | reports based on their deployment status and reporting history.
    |
    */

    // Number of days after deployment when a worker needs to submit their first report
    'first_report_threshold_days' => env('MONITORING_FIRST_REPORT_THRESHOLD_DAYS', 3),

    // Number of days after a previous report when a deployed worker should submit a new report
    'subsequent_report_threshold_days' => env('MONITORING_SUBSEQUENT_REPORT_THRESHOLD_DAYS', 15),
];