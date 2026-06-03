<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan {{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; color: #555; }
        .filter-info { margin-bottom: 15px; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #5D87FF; color: white; padding: 7px 5px; text-align: left; font-size: 10px; }
        td { padding: 5px; border: 1px solid #ddd; font-size: 10px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
        .badge-status { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 9px; }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .bg-dark { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan {{ $title }}</h1>
        <p>Sego Sambel Merdeka</p>
        <p>{{ $subtitle }}</p>
    </div>

    @if($startDate || $endDate || $bahanNama)
    <div class="filter-info">
        <strong>Filter:</strong>
        @if($startDate) Dari: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} @endif
        @if($endDate) Sampai: {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @endif
        @if($bahanNama) Bahan: {{ $bahanNama }} @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{!! $cell !!}</td>
                @endforeach
            </tr>
            @endforeach
            @if(count($rows) === 0)
                <tr><td colspan="{{ count($headings) }}" style="text-align:center;color:#999;">Tidak ada data.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
