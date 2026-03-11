<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report — {{ ucfirst($type) }} — {{ $from }} to {{ $to }}</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    font-size: 12px;
    color: #1e293b;
    background: #fff;
    padding: 32px 40px;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 2px solid #0b1629;
    padding-bottom: 16px;
    margin-bottom: 24px;
  }
  .header-left h1 { font-size: 20px; font-weight: 800; color: #0b1629; }
  .header-left p  { font-size: 11px; color: #64748b; margin-top: 4px; }
  .header-right   { text-align: right; font-size: 11px; color: #64748b; }
  .header-right .store { font-size: 14px; font-weight: 700; color: #0b1629; margin-bottom: 4px; }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
  }
  thead tr {
    background: #0b1629;
    color: #fff;
  }
  thead th {
    padding: 8px 10px;
    text-align: left;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-size: 10px;
  }
  thead th:not(:first-child) { text-align: right; }
  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody tr:hover { background: #f1f5f9; }
  tbody td {
    padding: 7px 10px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
  }
  tbody td:not(:first-child) { text-align: right; font-variant-numeric: tabular-nums; }
  tfoot tr { background: #f1f5f9; }
  tfoot td {
    padding: 9px 10px;
    font-weight: 700;
    color: #0b1629;
    border-top: 2px solid #0b1629;
  }
  tfoot td:not(:first-child) { text-align: right; }
  .footer {
    margin-top: 32px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #94a3b8;
  }
  @media print {
    body { padding: 0; }
    .no-print { display: none !important; }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; }
  }
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:24px;display:flex;gap:12px">
  <button onclick="window.print()"
          style="background:#0b1629;color:white;border:none;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600">
    🖨 Print / Save as PDF
  </button>
  <button onclick="window.history.back()"
          style="background:none;border:1px solid #cbd5e1;padding:8px 20px;border-radius:6px;font-size:13px;cursor:pointer">
    ← Back
  </button>
</div>

<div class="header">
  <div class="header-left">
    <h1>{{ ucfirst($type) }} Report</h1>
    <p>Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
  </div>
  <div class="header-right">
    <div class="store">{{ config('app.name', 'Store') }}</div>
    <div>Generated {{ now()->format('M d, Y H:i') }}</div>
  </div>
</div>

@if(!empty($rows))
<table>
  <thead>
    <tr>
      @foreach($rows[0] as $heading)
        <th>{{ $heading }}</th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach(array_slice($rows, 1, -1) as $row)
    <tr>
      @foreach($row as $i => $cell)
        <td>{{ $cell }}</td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
  @php $lastRow = end($rows); @endphp
  @if(count($rows) > 2)
  <tfoot>
    <tr>
      @foreach($lastRow as $cell)
        <td>{{ $cell }}</td>
      @endforeach
    </tr>
  </tfoot>
  @endif
</table>
@else
<p style="color:#64748b;padding:32px;text-align:center">No data for this period.</p>
@endif

<div class="footer">
  <span>{{ config('app.name', 'Store') }} — Admin Report</span>
  <span>{{ $from }} to {{ $to }}</span>
</div>

</body>
</html>
