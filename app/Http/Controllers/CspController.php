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
            Log::info('Raw Payload Structure', ['payload' => json_encode($request->all(), JSON_PRETTY_PRINT)]);

            // Extract the csp-report section
            if ($request->header('Content-Type') === 'application/csp-report') {
                $rawBody = $request->getContent();
                $report = json_decode($rawBody, true);

                // Check for the double-wrapped 'csp-report' and extract the inner one
                if (isset($report['csp-report']['csp-report'])) {
                    $report = $report['csp-report']['csp-report'];
                } elseif (isset($report['csp-report'])) {
                    $report = $report['csp-report'];
                }
            } else {
                // Fall back to standard request parsing
                $report = $request->input('csp-report');
            }

            Log::info('Extracted CSP Report', ['csp-report' => $report]);

            // Check if the report contains necessary fields
            if (!$report || !isset($report['document-uri'], $report['violated-directive'], $report['blocked-uri'])) {
                Log::error('Malformed CSP report data', ['received_report' => $report]);
                return response()->json([
                    'error' => 'Malformed CSP report data',
                    'details' => $report
                ], 400);
            }

            // Extract relevant information from the CSP report
            $documentUri = $report['document-uri'] ?? null;
            $referrer = $report['referrer'] ?? null;  // Ensure referrer is extracted here
            $violatedDirective = $report['violated-directive'] ?? null;
            $effectiveDirective = $report['effective-directive'] ?? null;
            $originalPolicy = $report['original-policy'] ?? null;
            $blockedUri = substr($report['blocked-uri'], 0, 2048);
            $statusCode = $report['status-code'] ?? null;
            $scriptSample = $report['script-sample'] ?? null;

            // Extract domain from document URI
            $domainName = parse_url($documentUri, PHP_URL_HOST);

            if (!$domainName) {
                return response()->json(['error' => 'Invalid document URI'], 400);
            }

            // Create or find the domain
            $domain = Domain::firstOrCreate(['domain_name' => $domainName]);

            // Parse the referrer to get only the path (if referrer is present)
            $referrerPath = $referrer ? parse_url($referrer, PHP_URL_PATH) : null;

            // Deduplication Check: Check for an existing report with the same violated directive and blocked URI
            $existingReport = CspReport::where('violated_directive', $violatedDirective)
                ->where('blocked_uri', $blockedUri)
                ->where('domain_id', $domain->id)
                ->first();

            if ($existingReport) {
                Log::info('Duplicate CSP report detected and ignored', [
                    'violated_directive' => $violatedDirective,
                    'blocked_uri' => $blockedUri,
                ]);
                return response()->json(['status' => 'Duplicate report ignored'], 200);
            }

            // Proceed to store the report if not a duplicate
            CspReport::create([
                'document_uri' => $documentUri,
                'referrer' => $referrerPath,
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
            return response()->json([
                'error' => 'Failed to process the CSP report',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
