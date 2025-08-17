<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QualityReport;
use Carbon\Carbon;

class QualityReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample weekly quality reports
        QualityReport::create([
            'report_type' => 'weekly',
            'start_date' => Carbon::now()->startOfWeek(),
            'end_date' => Carbon::now()->endOfWeek(),
            'quality_comment' => 'In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:',
            'problems' => "• 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.\n• 125mm products showed shrinkage issues during production.\n• Raw material quality variations were observed in some batches.",
            'corrective_actions' => "The problems were reduced by communicating with the shift leader and operators. However, weight and raw material quality problem were not reduced because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products and shrinkage problem in 125mm products was reduced by increasing the length and OD of products and raw material problems were reduced by changing the raw materials.",
            'remarks' => 'As quality we recommended that the double type raw materials quality (purity and density) should be checked.',
            'prepared_by' => 'Yohannes Choma',
            'checked_by' => 'Yeshiamb A.',
            'approved_by' => 'Aschalew',
            'is_active' => true,
        ]);

        // Sample monthly quality reports
        QualityReport::create([
            'report_type' => 'monthly',
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
            'quality_comment' => 'In this month all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:',
            'problems' => "• 160mm PN10 products had a problem of weight (over from standard), thickness, high difference between maximum and minimum thickness value, internal roughness, length and fading of blue stripe, power outage.\n• Production line efficiency dropped by 15% due to equipment maintenance issues.\n• Some batches showed inconsistent color quality.",
            'corrective_actions' => "Most of the problems were solved or minimized by communicating with the shift leader and operator. However, the weight problem was reduced but not eliminated because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products.",
            'remarks' => 'As quality we recommended that the double type raw materials quality (purity and density) should be checked.',
            'prepared_by' => 'Yohannes Choma',
            'checked_by' => 'Yeshiamb A.',
            'approved_by' => 'Aschalew',
            'is_active' => true,
        ]);

        // Sample daily quality reports
        QualityReport::create([
            'report_type' => 'daily',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
            'quality_comment' => 'Today\'s production met quality standards with minor issues that were promptly addressed.',
            'problems' => "• Minor thickness variations in 90mm products.\n• One batch had slight color inconsistency.",
            'corrective_actions' => "Immediate adjustments were made to the production parameters. Color consistency was improved by adjusting the pigment mixing process.",
            'remarks' => 'Overall production quality was satisfactory. No major issues reported.',
            'prepared_by' => 'Yohannes Choma',
            'checked_by' => 'Yeshiamb A.',
            'approved_by' => 'Aschalew',
            'is_active' => true,
        ]);
    }
} 