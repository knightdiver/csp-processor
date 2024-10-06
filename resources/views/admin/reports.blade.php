<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Reports by Domain</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto py-10">
    <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">CSP Reports by Domain</h1>

    <!-- Buttons for populating and resetting test data -->
    @if (App::environment(['local', 'staging', 'testing']))
        <div class="flex justify-center gap-4 mb-6">
            <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                    onclick="populateTestData()">
                Populate Test Data
            </button>
            <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                    onclick="resetAllData()">
                Reset All Data
            </button>
        </div>
    @endif

    <div class="grid grid-cols-4 gap-4 text-center font-semibold text-gray-700 bg-gray-200 py-3 rounded-t-lg">
        <div>Domain</div>
        <div>Report Count</div>
        <div>View Reports</div>
        <div>Reset Reports</div>
    </div>

    @foreach($domains as $domain)
        <div class="grid grid-cols-4 gap-4 py-4 px-2 border-b odd:bg-gray-50 even:bg-white" id="domain-{{ $domain->id }}">
            <div class="col-span-1">{{ $domain->domain_name }}</div>
            <div class="col-span-1 text-center flex items-center justify-center" id="report-count-{{ $domain->id }}">
                {{ $domain->csp_reports_count }}
            </div>
            <div class="col-span-1 text-center">
                <a href="{{ route('reports.show', $domain->id) }}" class="text-blue-600 underline">
                    View Reports
                </a>
            </div>
            <div class="col-span-1 text-center">
                <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded"
                        onclick="resetDomainReports('{{ $domain->id }}')">
                    Reset
                </button>
            </div>
        </div>
    @endforeach

</div>

<!-- Add the AJAX functionality to handle the actions without refreshing the page -->
<script>
    function resetDomainReports(domainId) {
        if (confirm('Are you sure you want to reset all reports for this domain?')) {
            fetch(`/admin/reports/reset/${domainId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('All reports for this domain have been reset.');
                        document.getElementById(`report-count-${domainId}`).innerText = '0';
                    } else {
                        alert('An error occurred while resetting the reports.');
                    }
                });
        }
    }

    function populateTestData() {
        if (confirm('Are you sure you want to populate the database with test data?')) {
            fetch(`/api/test/populate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload(); // Refresh the page to update the report counts
                    } else {
                        alert('An error occurred while populating test data.');
                    }
                });
        }
    }

    function resetAllReports() {
        if (confirm('Are you sure you want to reset all reports? This will delete all data.')) {
            fetch(`/api/test/reset`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload(); // Refresh the page to update the report counts
                    } else {
                        alert('An error occurred while resetting the data.');
                    }
                });
        }
    }
</script>

</body>
</html>
