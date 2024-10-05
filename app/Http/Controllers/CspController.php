<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CspReport;
use App\Models\Domain;
use Illuminate\Support\Facades\Log;

class CspController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Log the entire request body
            Log::info('Full CSP Report Payload', ['request' => $request->all()]);

            // Extract the csp-report section
            $report = $request->input('csp-report');
            Log::info('Extracted CSP Report', ['csp-report' => $report]);

            // Check if the report contains necessary fields
            if (!$report || !isset($report['document-uri'], $report['violated-directive'], $report['blocked-uri'])) {
                Log::error('Malformed CSP report data', ['received_report' => $report]);
                return response()->json([
                    'error' => 'Malformed CSP report data',
                    'details' => $report
                ], 427);
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
                return response()->json(['error' => 'Invalid document URI'], 420);
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

            // Log the successful report creation
            Log::info('CSP Report successfully stored', ['report' => $report]);

            return response()->json(['status' => 'Report received'], 200);
        } catch (\Exception $e) {
            // Log any exceptions that occur
            Log::error('Failed to process CSP report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return detailed error message for debugging
            return response()->json(['error' => 'Failed to process the CSP report',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),], 500);
        }
    }
}
