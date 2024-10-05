<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\CspReport;
use Illuminate\Http\Request;

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
}
