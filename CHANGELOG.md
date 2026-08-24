# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-08-24

### Added

- **Agency/Superadmin monitoring report override.** Agency and superadmin users can now submit a monitoring report on behalf of a deployed worker directly from the monitoring alert banner.
  - New `submitMonitoringReport()` Filament Action on the `ListWorkers` page with a modal form (report textarea, required, min 10 chars).
  - Action is gated to non-FRA users only.
  - Submitted reports record `latitude` and `longitude` as `null`.
  - New `reported_by` nullable foreign key on the `monitorings` table tracks which agency user submitted the report.
  - New "Submit Report" button appears inside each worker's alert banner slide on the Workers list page.
  - `MonitoringResource` table and infolist now display a "Reported By" column/field (shows "Worker Self-Report" placeholder when submitted by the worker).

