<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSP Reports for {{ $domain->domain_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 300px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -150px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .expandable {
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            font-size: 16px; /* Increased font size */
        }

        .expandable.expanded {
            white-space: normal;
            max-width: none;
        }

        .copy-icon {
            cursor: pointer;
            display: inline-block;
            margin-right: 8px;
            width: 20px;
            height: 20px;
            background-image: url('https://img.icons8.com/ios-filled/50/000000/copy.png');
            background-size: contain;
            background-repeat: no-repeat;
        }

        .copied {
            color: green;
            font-size: 12px;
        }

        .grid-header {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr;
            gap: 10px;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
    </style>
    <script>
        function copyToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    console.log('Copied to clipboard:', text);
                }).catch(function (err) {
                    console.error('Could not copy text: ', err);
                });
            } else {
                const tempTextArea = document.createElement('textarea');
                tempTextArea.value = text;
                document.body.appendChild(tempTextArea);
                tempTextArea.select();
                try {
                    document.execCommand('copy');
                    console.log('Fallback: Copied to clipboard', text);
                } catch (err) {
                    console.error('Fallback: Could not copy text', err);
                }
                document.body.removeChild(tempTextArea);
            }
        }

    </script>
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

    <!-- Grid Header with Sorting -->
    <div class="grid-container grid-header text-gray-700 bg-gray-200">
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
                Blocked URI & Copy
                @if(request('sort') === 'blocked_uri')
                    <span>{{ request('direction') === 'asc' ? '▲' : '▼' }}</span>
                @endif
            </a>
        </div>
        <div>
            <a href="{{ route('reports.show', ['domain' => $domain->id, 'sort' => 'referrer', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                Referrer
                @if(request('sort') === 'referrer')
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

    <!-- Grid Content -->
    @foreach($reports as $report)
        <div class="grid-container odd:bg-gray-50 even:bg-white">
            <!-- Violated Directive -->
            <div>{{ $report->violated_directive }}</div>

            <!-- Blocked URI with Copy Button -->
            <div class="inline-flex items-center space-x-2">
                <button class="copy-icon" onclick="copyToClipboard('{{ $report->blocked_uri }}')"></button>
                <span class="expandable" onclick="this.classList.toggle('expanded')" title="Click to expand">{{ $report->blocked_uri }}</span>
            </div>

            <!-- Referrer with Tooltip -->
            <div class="tooltip">
                <span class="expandable" onclick="this.classList.toggle('expanded')" title="Click to expand">{{ parse_url($report->referrer, PHP_URL_PATH) ?? 'N/A' }}</span>
                <span class="tooltiptext">{{ parse_url($report->referrer, PHP_URL_PATH) ?? 'N/A' }}</span>
            </div>

            <!-- Script Sample -->
            <div class="tooltip">
                <span class="expandable" onclick="this.classList.toggle('expanded')" title="Click to expand">{{ $report->script_sample ?? 'N/A' }}</span>
                <span class="tooltiptext">{{ $report->script_sample ?? 'N/A' }}</span>
            </div>
        </div>
    @endforeach

</div>

</body>
</html>
