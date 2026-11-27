@php
    $wheelCenter = 220;
    $wheelRadius = 154;
    $wheelOuterRadius = 214;
    $wheelInnerRadius = 171;
    $wheelSegmentColors = [
        '#35b64a',
        '#168acb',
        '#f7941d',
        '#8fa9d8',
        '#f6c873',
        '#16a9df',
        '#8b2bd4',
        '#ed1c24',
    ];
@endphp

<svg id="{{ $chartId }}" class="w-full h-full" viewBox="0 0 440 440" role="img"
     font-family="Plus Jakarta Sans, sans-serif"
     aria-label="Biểu đồ Bánh xe cuộc đời với 8 lĩnh vực, chấm theo thang điểm từ 1 đến 10">
    <title>Bánh xe cuộc đời</title>
    <desc>Biểu đồ thể hiện điểm tự đánh giá trong tám lĩnh vực của cuộc sống.</desc>

    @foreach($wheelChartItems as $index => $item)
        @php
            $centerAngle = -67.5 + $index * 45;
            $startAngle = deg2rad($centerAngle - 22.5);
            $endAngle = deg2rad($centerAngle + 22.5);
            $outerStartX = round($wheelCenter + cos($startAngle) * $wheelOuterRadius, 2);
            $outerStartY = round($wheelCenter + sin($startAngle) * $wheelOuterRadius, 2);
            $outerEndX = round($wheelCenter + cos($endAngle) * $wheelOuterRadius, 2);
            $outerEndY = round($wheelCenter + sin($endAngle) * $wheelOuterRadius, 2);
            $innerEndX = round($wheelCenter + cos($endAngle) * $wheelInnerRadius, 2);
            $innerEndY = round($wheelCenter + sin($endAngle) * $wheelInnerRadius, 2);
            $innerStartX = round($wheelCenter + cos($startAngle) * $wheelInnerRadius, 2);
            $innerStartY = round($wheelCenter + sin($startAngle) * $wheelInnerRadius, 2);
            $labelAngle = deg2rad($centerAngle);
            $labelX = round($wheelCenter + cos($labelAngle) * 192, 2);
            $labelY = round($wheelCenter + sin($labelAngle) * 192, 2);
            $labelRotation = $centerAngle + 90;
            if ($labelRotation > 90 && $labelRotation < 270) {
                $labelRotation += 180;
            }
        @endphp
        <path d="M {{ $outerStartX }} {{ $outerStartY }}
                 A {{ $wheelOuterRadius }} {{ $wheelOuterRadius }} 0 0 1 {{ $outerEndX }} {{ $outerEndY }}
                 L {{ $innerEndX }} {{ $innerEndY }}
                 A {{ $wheelInnerRadius }} {{ $wheelInnerRadius }} 0 0 0 {{ $innerStartX }} {{ $innerStartY }} Z"
              fill="{{ $wheelSegmentColors[$index % count($wheelSegmentColors)] }}"
              stroke="#ffffff" stroke-width="2" />
        <text x="{{ $labelX }}" y="{{ $labelY }}"
              transform="rotate({{ $labelRotation }} {{ $labelX }} {{ $labelY }})"
              fill="#ffffff" font-size="12" font-weight="700" text-anchor="middle" dominant-baseline="middle">
            {{ $item['short_label'] }}
        </text>
    @endforeach

    @foreach([2, 4, 6, 8, 10] as $level)
        <circle cx="{{ $wheelCenter }}" cy="{{ $wheelCenter }}"
                r="{{ round($wheelRadius * $level / 10, 2) }}"
                fill="none" stroke="#cfd5da" stroke-width="1" />
        <text x="{{ $wheelCenter + 5 }}" y="{{ round($wheelCenter - $wheelRadius * $level / 10 + 3, 2) }}"
              fill="#64748b" font-size="9" font-weight="600">{{ $level }}</text>
    @endforeach

    @foreach($wheelChartItems as $index => $item)
        @php
            $axisAngle = deg2rad(-90 + $index * 45);
            $axisX = round($wheelCenter + cos($axisAngle) * $wheelRadius, 2);
            $axisY = round($wheelCenter + sin($axisAngle) * $wheelRadius, 2);
        @endphp
        <line x1="{{ $wheelCenter }}" y1="{{ $wheelCenter }}" x2="{{ $axisX }}" y2="{{ $axisY }}"
              stroke="#cfd5da" stroke-width="1" />
    @endforeach

    @foreach($wheelChartItems as $index => $item)
        @php
            $centerAngle = -67.5 + $index * 45;
            $startAngle = deg2rad($centerAngle - 22.5);
            $endAngle = deg2rad($centerAngle + 22.5);
            $pointRadius = $wheelRadius * max(0, min(10, (int) $item['score'])) / 10;
            $arcStartX = round($wheelCenter + cos($startAngle) * $pointRadius, 2);
            $arcStartY = round($wheelCenter + sin($startAngle) * $pointRadius, 2);
            $arcEndX = round($wheelCenter + cos($endAngle) * $pointRadius, 2);
            $arcEndY = round($wheelCenter + sin($endAngle) * $pointRadius, 2);
            $arcPath = "M {$wheelCenter} {$wheelCenter} L {$arcStartX} {$arcStartY} A {$pointRadius} {$pointRadius} 0 0 1 {$arcEndX} {$arcEndY} Z";
            $angle = deg2rad($centerAngle);
            $pointX = round($wheelCenter + cos($angle) * $pointRadius, 2);
            $pointY = round($wheelCenter + sin($angle) * $pointRadius, 2);
        @endphp
        <path id="{{ $chartId }}_sector_{{ $index }}" d="{{ $arcPath }}"
              fill="#10b981" fill-opacity="0.22" stroke="#047857" stroke-width="2" />
    @endforeach
</svg>
