<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CspReport;
use App\Models\Domain;

class CspController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming CSP report
        $report = $request->input('csp-report');
        $domainName = parse_url($report['document-uri'], PHP_URL_HOST);

        // Create or find the domain
        $domain = Domain::firstOrCreate(['domain_name' => $domainName]);

        // Store the CSP report in the database
        CspReport::create([
            'document_uri' => $report['document-uri'],
            'referrer' => $report['referrer'] ?? null,
            'violated_directive' => $report['violated-directive'],
            'blocked_uri' => $report['blocked-uri'],
            'source_file' => $report['source-file'] ?? null,
            'line_number' => $report['line-number'] ?? null,
            'column_number' => $report['column-number'] ?? null,
            'domain_id' => $domain->id,
        ]);

        return response()->json(['status' => 'Report received'], 200);
    }
}
