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
    <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">CSP Reports for {{ $domain->domain_name }}</h1>

    <div class="grid grid-cols-4 gap-4 text-center font-semibold text-gray-700 bg-gray-200 py-3 rounded-t-lg">
        <div>Violated Directive</div>
        <div>Blocked URI</div>
        <div>Status Code</div>
        <div>Script Sample</div>
    </div>

    @foreach($reports as $report)
        <div class="grid grid-cols-4 gap-4 py-4 px-2 border-b odd:bg-gray-50 even:bg-white">
            <div class="col-span-1">{{ $report->violated_directive }}</div>
            <div class="col-span-1">{{ $report->blocked_uri }}</div>
            <div class="col-span-1">{{ $report->status_code }}</div>
            <div class="col-span-1">{{ $report->script_sample }}</div>
        </div>
    @endforeach
</div>


</div>

</body>
</html>
