<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\CspReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
    public function show($domainId)
    {
        $domain = Domain::findOrFail($domainId);

        // Get sorting parameters
        $sort = request('sort', 'violated_directive'); // Default sort by 'violated_directive'
        $direction = request('direction', 'desc'); // Default direction is descending

        // Fetch reports with sorting
        $reports = CspReport::where('domain_id', $domainId)
            ->orderBy($sort, $direction)
            ->get();

        // Initialize array to store CSP directives and blocked URIs
        $cspDirectives = [
            'default-src' => ["'self'"],        // Fallback for other directives if not specified
            'script-src' => ["'self'"],         // Controls allowed sources for JavaScript
            'script-src-elem' => ["'self'"],    // Controls allowed sources specifically for <script> elements
            'img-src' => ["'self'"],            // Controls allowed sources for images
            'style-src' => ["'self'"],          // Controls allowed sources for CSS stylesheets
            'style-src-elem' => ["'self'"],     // Controls allowed sources specifically for <style> elements and inline styles
            'connect-src' => ["'self'"],        // Controls allowed sources for XMLHttpRequest, WebSockets, and EventSource
            'font-src' => ["'self'"],           // Controls allowed sources for fonts
            'media-src' => ["'self'"],          // For media resources like video/audio
            'object-src' => ["'self'"],         // For embedded objects such as <object>, <embed>, or <applet>
            'child-src' => ["'self'"],          // For nested browsing contexts like <iframe> and <frame>
            'worker-src' => ["'self'"],         // For web workers, including service workers
            'frame-src' => ["'self'"],          // For frames, specifying allowed sources for iframes
            'manifest-src' => ["'self'"],       // For web app manifests, specifying allowed sources
            // Add other directives as needed
        ];

        // Iterate through reports and organize blocked URIs by directive
        foreach ($reports as $report) {
            // Handle 'data' URIs explicitly
            if (stripos($report->blocked_uri, 'data:') === 0) {
                if (!in_array('data:', $cspDirectives[$report->violated_directive])) {
                    $cspDirectives[$report->violated_directive][] = 'data:';
                }
                continue;
            }

            // Handle 'inline' and 'eval' as usual
            if (stripos($report->blocked_uri, 'inline') !== false) {
                if (!in_array("'unsafe-inline'", $cspDirectives[$report->violated_directive])) {
                    $cspDirectives[$report->violated_directive][] = "'unsafe-inline'";
                }
                continue;
            } elseif (stripos($report->blocked_uri, 'eval') !== false) {
                if (!in_array("'unsafe-eval'", $cspDirectives[$report->violated_directive])) {
                    $cspDirectives[$report->violated_directive][] = "'unsafe-eval'";
                }
                continue;
            }

            // Extract the base domain for other URIs
            $scheme = parse_url($report->blocked_uri, PHP_URL_SCHEME);
            $host = parse_url($report->blocked_uri, PHP_URL_HOST);
            $blockedUri = ($scheme && $host) ? "{$scheme}://{$host}" : null;

            // Add the blocked URI if valid and not already included
            if ($blockedUri && !in_array($blockedUri, $cspDirectives[$report->violated_directive])) {
                $cspDirectives[$report->violated_directive][] = $blockedUri;
            }
        }

        // After collecting all URIs, ensure 'unsafe-inline' and 'unsafe-eval' are properly ordered
        foreach ($cspDirectives as $directive => &$sources) {
            // Check for 'unsafe-inline' and 'unsafe-eval' in the sources
            $hasUnsafeInline = in_array("'unsafe-inline'", $sources);
            $hasUnsafeEval = in_array("'unsafe-eval'", $sources);

            // Remove 'unsafe-inline' and 'unsafe-eval' if present
            $sources = array_diff($sources, ["'unsafe-inline'", "'unsafe-eval'"]);

            // Re-add 'unsafe-inline' and 'unsafe-eval' at the start, right after 'self'
            if ($hasUnsafeInline) {
                array_splice($sources, 1, 0, "'unsafe-inline'");
            }
            if ($hasUnsafeEval) {
                array_splice($sources, 1, 0, "'unsafe-eval'");
            }
        }

        return view('admin.report-details', compact('domain', 'reports', 'cspDirectives'));
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
