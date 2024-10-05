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

        if (!$report) {
            return response()->json(['error' => 'Invalid report data'], 400);
        }

        // Extract relevant information from the CSP report
        $documentUri = $report['document-uri'] ?? null;
        $referrer = $report['referrer'] ?? null;
        $violatedDirective = $report['violated-directive'] ?? null;
        $effectiveDirective = $report['effective-directive'] ?? null;
        $originalPolicy = $report['original-policy'] ?? null;
        $blockedUri = $report['blocked-uri'] ?? null;
        $statusCode = $report['status-code'] ?? null;
        $scriptSample = $report['script-sample'] ?? null;

        // Extract domain from document URI
        $domainName = parse_url($documentUri, PHP_URL_HOST);

        if (!$domainName) {
            return response()->json(['error' => 'Invalid document URI'], 400);
        }

        // Create or find the domain
        $domain = Domain::firstOrCreate(['domain_name' => $domainName]);

        // Store the CSP report in the database
        CspReport::create([
            'document_uri' => $documentUri,
            'referrer' => $referrer,
            'violated_directive' => $violatedDirective,
            'effective_directive' => $effectiveDirective,
            'original_policy' => $originalPolicy,
            'blocked_uri' => $blockedUri,
            'status_code' => $statusCode,
            'script_sample' => $scriptSample,
            'domain_id' => $domain->id,
        ]);

        return response()->json(['status' => 'Report received'], 200);
    }
}
