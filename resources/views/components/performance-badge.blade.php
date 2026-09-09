<div>
    <span class="flex items-center gap-1">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>{{ $metrics['total_time'] }} ms</span>
    </span>
    <span class="text-slate-500">|</span>
    <span>{{ $metrics['query_count'] }} forespørgsler ({{ $metrics['query_time'] }} ms)</span>
</div>