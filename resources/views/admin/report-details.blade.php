<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Reports for {{ $domain->domain_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="container mx-auto py-10">

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('reports.index') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300">
            ← Back to All Reports
        </a>
    </div>

    <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">
        CSP Reports for {{ $domain->domain_name }}
    </h1>

    <div class="grid grid-cols-5 gap-4 text-center font-semibold text-gray-700 bg-gray-200 py-3 rounded-t-lg">
        <div>
            <a href="{{ route('reports.show', ['domain' => $domain->id, 'sort' => 'violated_directive', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                Violated Directive
                @if(request('sort') === 'violated_directive')
                    <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                @endif
            </a>
        </div>
        <div>
            <a href="{{ route('reports.show', ['domain' => $domain->id, 'sort' => 'blocked_uri', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                Blocked URI
                @if(request('sort') === 'blocked_uri')
                    <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                @endif
            </a>
        </div>
        <div>
            <a href="{{ route('reports.show', ['domain' => $domain->id, 'sort' => 'status_code', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                Status Code
                @if(request('sort') === 'status_code')
                    <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                @endif
            </a>
        </div>
        <div>
            <a href="{{ route('reports.show', ['domain' => $domain->id, 'sort' => 'script_sample', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                Script Sample
                @if(request('sort') === 'script_sample')
                    <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                @endif
            </a>
        </div>
    </div>

    @foreach($reports as $report)
        <div class="grid grid-cols-5 gap-4 py-4 px-2 border-b odd:bg-gray-50 even:bg-white">
            <div>{{ $report->violated_directive }}</div>
            <div>{{ $report->blocked_uri }}</div>
            <div>{{ $report->status_code }}</div>
            <div>{{ $report->script_sample }}</div>
        </div>
    @endforeach

</div>

</body>
</html>
