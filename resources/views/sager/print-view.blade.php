<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <title>Sagsoversigt - Sag {{ $sag->sagsnr ?? $sag->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-size: 90% !important;
            }
            .no-print { 
                display: none !important; 
            }
            .max-w-5xl {
                max-width: 100% !important;
                width: 100% !important;
            }
        }
        body {
            font-size: 90%;
        }
    </style>
</head>
<body class="bg-white text-slate-900 p-4" onload="window.print()">

    <div class="max-w-5xl mx-auto space-y-4">
        
        <!-- HEADER OG PRINT-KNAP -->
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h1 class="text-xl font-bold">Sagsoversigt: {{ $sag->sagsnr ?? $sag->id }}</h1>
                <p class="text-[11px] text-slate-500">Udskrevet den {{ now()->format('d-m-Y H:i') }} af {{ auth()->user()->name ?? 'System' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 bg-slate-100 rounded-full text-xs font-semibold">
                    Status: {{ $sag->status()->value('tekst') ?? '-' }}
                </span>
                <button onclick="window.print()" class="no-print px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700">
                    🖨️ Udskriv igen
                </button>
            </div>
        </div>

        <!-- ====================================================== -->
        <!-- SECTION 1: GENERELT                                    -->
        <!-- ====================================================== -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5">
            <h2 class="text-xs font-bold text-slate-700 border-b pb-1 mb-2 uppercase tracking-wider">Generelt</h2>

            <div class="grid grid-cols-3 gap-3 text-xs">
                <div><strong>Sagsnummer:</strong><br><span class="text-slate-700">{{ $sag->sagsnr ?? '-' }}</span></div>
                <div><strong>Kreditor nr:</strong><br><span class="text-slate-700">{{ $sag->kreditor->first()?->lotusID ?? '-' }}</span></div>
                <div><strong>Kreditor / Firma:</strong><br><span class="text-slate-700">{{ $sag->kreditor->first()?->navn ?? '-' }}</span></div>

                <div><strong>Sag modtaget:</strong><br><span class="text-slate-700">{{ $sag->modtaget ? $sag->modtaget->format('d-m-Y') : '-' }}</span></div>
                <div><strong>Status:</strong><br><span class="text-slate-700">{{ $sag->status()->value('tekst') ?? '-' }}</span></div>
                <div><strong>Seneste rapport:</strong><br><span class="text-slate-700">{{ $sag->senesterapport ? $sag->senesterapport->format('d-m-Y') : '-' }}</span></div>

                <div><strong>Sagsbehandler:</strong><br><span class="text-slate-700">{{ $sag->sagsbehandler->pluck('navn')->join(', ') ?: '-' }}</span></div>
                <div><strong>Afsluttet:</strong><br><span class="text-slate-700">{{ $sag->afsluttet ? $sag->afsluttet->format('d-m-Y') : '-' }}</span></div>
                <div><strong>Aktiv:</strong><br><span class="text-slate-700">{{ $sag->aktiv ?? '-' }}</span></div>

                <div><strong>Stelnummer:</strong><br><span class="text-slate-700">{{ $sag->stelnr ?? '-' }}</span></div>
                <div><strong>Betalt:</strong><br><span class="text-slate-700">{{ $sag->betalt ? $sag->betalt->format('d-m-Y') : '-' }}</span></div>
                <div><strong>Kontrakttype:</strong><br><span class="text-slate-700">{{ $sag->ktr->pluck('tekst')->join(', ') ?: '-' }}</span></div>

                <div><strong>Normal mdl. ydelse:</strong><br><span class="text-slate-700">{{ $sag->n_mdlydelse ? number_format((float)$sag->n_mdlydelse, 2, ',', '.') : '-' }}</span></div>
                <div><strong>Konsulent:</strong><br><span class="text-slate-700">{{ $sag->konsulent->pluck('navn')->join(', ') ?: '-' }}</span></div>
                <div><strong>Faktureret:</strong><br><span class="text-slate-700">{{ $sag->faktureret ? $sag->faktureret->format('d-m-Y') : '-' }}</span></div>

                <div><strong>Udlæg bilbogen:</strong><br><span class="text-slate-700">{{ $sag->udlaeg->pluck('tekst')->join(', ') ?: '-' }}</span></div>
                <div><strong>Afslutning:</strong><br><span class="text-slate-700">{{ $sag->afslutning->pluck('tekst')->join(', ') ?: '-' }}</span></div>
                <div><strong>Bemærkning:</strong><br><span class="text-slate-700">{{ $sag->bemaerkning->pluck('tekst')->join(', ') ?: '-' }}</span></div>
            </div>
        </div>

        <!-- ====================================================== -->
        <!-- SECTION 2: DEBITOR                                     -->
        <!-- ====================================================== -->
        @php $debitor = $sag->debitor->first(); @endphp
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5">
            <h2 class="text-xs font-bold text-slate-700 border-b pb-1 mb-2 uppercase tracking-wider">Debitor</h2>

            <div class="grid grid-cols-3 gap-4 text-xs">
                <div class="space-y-1.5">
                    <div><strong>Navn(e):</strong> {{ $debitor?->navn ?? $sag->navn ?? '-' }}</div>
                    <div><strong>c/o:</strong> {{ $debitor?->co ?? $sag->co ?? '-' }}</div>
                    <div><strong>Adresse:</strong> {{ $debitor?->adresse ?? $sag->adresse ?? '-' }}</div>
                    <div><strong>Postnr / By:</strong> {{ $debitor?->postnr ?? $sag->postnr ?? '' }} {{ $debitor?->by ?? $sag->by ?? '-' }}</div>
                    <div><strong>CPR/CVR:</strong> {{ $debitor?->pnr ?? $sag->pnr ?? '-' }}</div>
                    <div><strong>Adressesøgning:</strong> {{ $sag->adropl ? $sag->adropl->format('d-m-Y') : '-' }}</div>
                </div>

                <div class="space-y-1.5">
                    <div><strong>Mail #1:</strong> {{ $debitor?->email ?? $sag->email ?? '-' }}</div>
                    <div><strong>Mail #2:</strong> {{ $debitor?->email2 ?? $sag->email2 ?? '-' }}</div>
                    <div><strong>Telefon #1:</strong> {{ $debitor?->tlf ?? $sag->tlf ?? '-' }}</div>
                    <div><strong>Telefon #2:</strong> {{ $debitor?->tlf2 ?? $sag->tlf2 ?? '-' }}</div>
                    <div><strong>Telefon #3 (Mobil):</strong> {{ $debitor?->mobil ?? $sag->mobil ?? '-' }}</div>
                </div>

                <div>
                    <strong>Bemærkning vedr. kontakt:</strong>
                    <div class="mt-1 p-2 bg-white border border-slate-200 rounded text-[11px] whitespace-pre-wrap min-h-[80px] max-h-[100px] overflow-hidden">{{ $sag->kontakt_bemaerkning ?? '-' }}</div>
                </div>
            </div>
        </div>

        <!-- ====================================================== -->
        <!-- SECTION 3: ØKONOMI                                     -->
        <!-- ====================================================== -->
        <div class="bg-slate-50 border border-slate-200 rounded-lg p-3.5">
            <h2 class="text-xs font-bold text-slate-700 border-b pb-1 mb-2 uppercase tracking-wider">Økonomi</h2>

            <div class="grid grid-cols-3 gap-4 text-xs">
                <div class="space-y-1">
                    <div class="flex justify-between"><strong>Restance / gældpost:</strong> <span>{{ number_format((float)($sag->hovedstol ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between"><strong>Renter:</strong> <span>{{ number_format((float)($sag->renter ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between"><strong>Inkassosalær/gebyr:</strong> <span>{{ number_format((float)($sag->gebyr ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between border-t pt-0.5 font-bold"><strong>I alt:</strong> <span>{{ number_format((float)($sag->ialt ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between"><strong>Indbetalt:</strong> <span>{{ number_format((float)($sag->indbetalt ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between border-t pt-0.5 font-bold"><strong>Resterende:</strong> <span>{{ number_format((float)($sag->resterende ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between"><strong>Restgæld kreditor:</strong> <span>{{ number_format((float)($sag->restgaeld_kreditor ?? 0), 2, ',', '.') }} kr.</span></div>
                    <div class="flex justify-between font-bold text-indigo-900"><strong>Restgæld inkl. salær:</strong> <span>{{ number_format((float)($sag->restgaeld_dkg ?? 0), 2, ',', '.') }} kr.</span></div>
                </div>

                <div class="space-y-1.5">
                    <div><strong>Fakturadato:</strong> {{ $sag->fakturadato ? $sag->fakturadato->format('d-m-Y') : '-' }}</div>
                    <div><strong>Fakturanr:</strong> {{ $sag->fakturanr ?? '-' }}</div>
                    <div><strong>Startgebyr:</strong> {{ $sag->startgebyr ? number_format((float)$sag->startgebyr, 2, ',', '.') : '-' }}</div>
                    <div><strong>Kode:</strong> {{ $sag->kode ?? '-' }}</div>
                    <div><strong>Dato:</strong> {{ $sag->dato ? $sag->dato->format('d-m-Y') : '-' }}</div>
                </div>

                <div>
                    <strong>Kort bemærkning:</strong>
                    <div class="mt-1 p-2 bg-white border border-slate-200 rounded text-[11px] whitespace-pre-wrap min-h-[80px] max-h-[100px] overflow-hidden">{{ $sag->kort_bemaerkning ?? '-' }}</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>