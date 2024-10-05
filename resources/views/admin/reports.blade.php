<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Reports by Domain</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto py-10 px-4">
    <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">CSP Reports by Domain</h1>

    <div class="overflow-hidden border rounded-lg shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Domain</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">Report Count</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider">View Reports</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($domains as $domain)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $domain->domain_name }}</td>
                    <!-- Center Report Count -->
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $domain->csp_reports_count }}</td>
                    <!-- Center View Reports Link -->
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('reports.show', $domain->id) }}" class="text-blue-600 underline">
                            View Reports
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
