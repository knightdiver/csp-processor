<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\CspReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Shows the summary of domains with counts of reports
    public function index()
    {
        // Get the count of reports for each domain
        $domains = Domain::withCount('cspReports')->get();

        return view('admin.reports', compact('domains'));
    }

    // Shows detailed reports for a specific domain
    public function show(Domain $domain)
    {
        // Load the specific reports for the given domain
        $reports = CspReport::where('domain_id', $domain->id)->get();

        return view('admin.report-details', compact('domain', 'reports'));
    }
    public function reset($domainId) {
        try {
            // Log the domain ID to ensure it's correct
            Log::info('Resetting reports for domain ID: ' . $domainId);

            // Attempt to delete reports
            $deletedRows = CspReport::where('domain_id', $domainId)->delete();

            // Log the number of deleted rows
            Log::info('Deleted rows: ' . $deletedRows);

            // Check if any rows were deleted
            if ($deletedRows > 0) {
                return response()->json(['success' => true, 'message' => 'Reports reset successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'No reports found for this domain']);
            }
        } catch (\Exception $e) {
            Log::error('Error resetting reports: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error resetting reports.']);
        }
    }
}
