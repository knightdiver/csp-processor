<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CspReport;
use App\Models\Domain;

class TestCspController extends Controller
{
    // Centralized list of test domains
    private $domains = [
        'https://jude.com',
        'https://example.com',
        'https://testdomain.com',
    ];

    // Populate the database with 30-40 test reports per domain
    public function populate(Request $request)
    {
        try {
            // Automatically insert the required domains
            $this->insertDomains();

            // Generate test data and insert it into the csp_reports table
            $testReports = $this->generateTestData();

            foreach ($testReports as $report) {
                $domainId = $this->getDomainId($report['csp-report']['document-uri']);

                if (!$domainId) {
                    return response()->json(['error' => 'Domain not found for URI: ' . $report['csp-report']['document-uri']], 500);
                }

                CspReport::create([
                    'document_uri' => $report['csp-report']['document-uri'],
                    'referrer' => $report['csp-report']['referrer'],
                    'violated_directive' => $report['csp-report']['violated-directive'],
                    'effective_directive' => $report['csp-report']['effective-directive'],
                    'original_policy' => $report['csp-report']['original-policy'],
                    'blocked_uri' => $report['csp-report']['blocked-uri'],
                    'status_code' => $report['csp-report']['status-code'],
                    'script_sample' => $report['csp-report']['script-sample'],
                    'source_file' => $report['csp-report']['source_file'],
                    'line_number' => $report['csp-report']['line_number'],
                    'column_number' => $report['csp-report']['column_number'],
                    'domain_id' => $domainId,
                ]);
            }

            return response()->json(['message' => 'Test data populated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Reset CSP violations data and domains
    public function reset()
    {
        try {
            CspReport::query()->delete();  // Delete all reports
            Domain::query()->delete();     // Delete all domains

            return response()->json(['message' => 'CSP reports and domains reset successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Automatically insert the required domains
    private function insertDomains()
    {
        foreach ($this->domains as $domain) {
            Domain::updateOrCreate(
                ['domain_name' => parse_url($domain, PHP_URL_HOST)],
                ['domain_name' => parse_url($domain, PHP_URL_HOST)]
            );
        }
    }

    // Generate test data for each domain, 30-40 reports per domain
    private function generateTestData()
    {
        $testReports = [];

        foreach ($this->domains as $domain) {
            for ($i = 0; $i < rand(30, 40); $i++) {
                $testReports[] = [
                    'csp-report' => [
                        'document-uri' => "{$domain}/page{$i}",
                        'referrer' => "{$domain}/referrer{$i}",
                        'violated-directive' => $this->getRandomDirective(),
                        'effective-directive' => $this->getRandomDirective(),
                        'original-policy' => $this->getRandomPolicy(),
                        'disposition' => 'report',
                        'blocked-uri' => $this->getRandomBlockedUri(),
                        'status-code' => rand(200, 403),
                        'script-sample' => $i % 2 == 0 ? '' : 'alert("XSS")',
                        'source_file' => 'https://source-file.com/file.js',
                        'line_number' => rand(1, 100),
                        'column_number' => rand(1, 50),
                    ],
                ];
            }
        }

        return $testReports;
    }

    // Fetch the domain ID based on the domain URL
    private function getDomainId($documentUri)
    {
        $domain = parse_url($documentUri, PHP_URL_HOST);
        return Domain::where('domain_name', $domain)->value('id');
    }

    // Randomized directive
    private function getRandomDirective()
    {
        $directives = ['script-src', 'style-src', 'img-src', 'font-src', 'frame-src'];
        return $directives[array_rand($directives)];
    }

    // Randomized blocked URI
    private function getRandomBlockedUri()
    {
        $blockedUris = [
            'https://malicious-site.com/script.js',
            'https://cdn.badcdn.com/style.css',
            'https://bad-image-source.com/image.jpg',
        ];
        return $blockedUris[array_rand($blockedUris)];
    }

    // Randomized policy
    private function getRandomPolicy()
    {
        return "default-src 'self'; script-src 'self' https://trusted-site.com; img-src 'self'; style-src 'self';";
    }
}
