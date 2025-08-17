# Dynamic Quality Reports System

## Overview

The dynamic quality reports system allows you to manage quality comments, problems, corrective actions, and remarks for daily, weekly, and monthly production reports. Instead of having static text in the reports, you can now create and manage dynamic quality content that can be updated and customized for different periods.

## Features

-   **Dynamic Content**: Quality comments, problems, corrective actions, and remarks are now stored in the database
-   **Period-based Management**: Create quality reports for specific date ranges (daily, weekly, monthly)
-   **Easy Management**: User-friendly interface to create, edit, and manage quality reports
-   **Fallback Content**: If no quality report exists for a period, the system shows default static content
-   **Multi-line Support**: Support for formatted text with line breaks

## How to Use

### 1. Access Quality Report Manager

Navigate to: `/settings/quality-reports`

### 2. Create a Quality Report

1. Click "Add Quality Report"
2. Fill in the form:
    - **Report Type**: Choose Daily, Weekly, or Monthly
    - **Start Date & End Date**: Set the period for this quality report
    - **Quality Comment**: General quality assessment
    - **Problems**: List specific problems observed
    - **Corrective Actions**: Describe actions taken to address problems
    - **Remarks**: Additional notes or recommendations
    - **Prepared By, Checked By, Approved By**: Names of responsible persons
    - **Active**: Toggle to enable/disable the report

### 3. Edit or Delete Reports

-   Use the edit button to modify existing reports
-   Use the delete button to remove reports
-   Only active reports will appear in production reports

## How It Works

### Database Structure

The system uses a `quality_reports` table with the following fields:

-   `report_type`: daily, weekly, monthly
-   `start_date` & `end_date`: Period coverage
-   `quality_comment`: General quality assessment
-   `problems`: Specific problems observed
-   `corrective_actions`: Actions taken
-   `remarks`: Additional notes
-   `prepared_by`, `checked_by`, `approved_by`: Responsible persons
-   `is_active`: Whether the report is active

### Report Matching Logic

The system automatically matches quality reports to production reports based on:

-   **Daily Reports**: Matches by exact date
-   **Weekly Reports**: Matches by date range (start of week to end of week)
-   **Monthly Reports**: Matches by month

### Fallback Behavior

If no quality report exists for a specific period, the system displays default static content to maintain report consistency.

## Production Reports Updated

The following reports now use dynamic quality data:

1. **Daily Production Report** (`/operations/production-report`)
2. **Weekly Production Report** (`/reports/weekly`)
3. **Monthly Production Report** (`/reports/monthly`)

## Benefits

1. **Flexibility**: Easy to update quality content without code changes
2. **Consistency**: Standardized format across all reports
3. **Maintainability**: Centralized management of quality content
4. **Historical Tracking**: Keep track of quality issues over time
5. **User-Friendly**: Simple interface for non-technical users

## Sample Data

The system includes sample quality reports for testing:

-   Weekly report for current week
-   Monthly report for current month
-   Daily report for today

## Technical Implementation

-   **Model**: `App\Models\QualityReport`
-   **Controller**: `App\Livewire\Settings\QualityReportManager`
-   **View**: `resources/views/livewire/settings/quality-report-manager.blade.php`
-   **Migration**: `database/migrations/2025_01_15_000000_create_quality_reports_table.php`
-   **Seeder**: `database/seeders/QualityReportSeeder.php`

## Routes

-   Quality Report Manager: `/settings/quality-reports`
-   Daily Production Report: `/operations/production-report`
-   Weekly Production Report: `/reports/weekly`
-   Monthly Production Report: `/reports/monthly`
