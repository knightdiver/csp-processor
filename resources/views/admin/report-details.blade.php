<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Reports for {{ $domain->domain_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto py-10 px-4">
    <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">CSP Reports for {{ $domain->domain_name }}</h1>

    <div class="overflow-hidden border rounded-lg shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-200">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Document URI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Referrer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Violated Directive</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Blocked URI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $report->document_uri }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $report->referrer }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $report->violated_directive }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $report->blocked_uri }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $report->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
