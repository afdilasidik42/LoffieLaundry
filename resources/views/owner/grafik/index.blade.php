@extends('layouts.owner')
@section('title', 'Kelola Grafik')
@section('page-title', 'Kelola Grafik')
@section('page-description', 'Visualisasi data bisnis Loffie Laundry secara interaktif')

@section('content')
<div class="space-y-8">

    {{-- Section 1: Akurasi Prediksi --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Akurasi Prediksi</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Prediksi vs Aktual</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Waktu selesai dalam jam</p>
                    </div>
                    <span class="text-[10px] bg-violet-100 text-violet-600 px-2.5 py-1 rounded-full font-semibold">Line Chart</span>
                </div>
                <div id="prediksiChartWrapper" class="relative rounded-xl" style="height:280px;">
                    <div id="prediksiLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <p class="text-xs text-gray-400 font-medium">Memuat data…</p>
                        </div>
                    </div>
                    <div id="prediksiEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                        <p class="text-xs text-gray-400 font-medium">Belum ada data prediksi selesai</p>
                    </div>
                    <canvas id="prediksiChart"></canvas>
                </div>
                <div id="mapeSummary" class="mt-4 hidden">
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border" id="mapeSummaryBox">
                        <div id="mapeIcon" class="w-10 h-10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Rata-rata MAPE: <span id="mapeValue" class="font-bold"></span></p>
                            <p class="text-xs mt-0.5" id="mapeCategory"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Volume Transaksi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Volume Transaksi & Pendapatan</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Tren pesanan dan revenue bulanan</p>
                    </div>
                    <select id="volumeRangeFilter" class="text-xs bg-gray-100 border border-gray-200 text-gray-700 rounded-lg px-3 py-1.5 font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                        <option value="tahun" selected>Tahun Ini</option>
                        <option value="6bulan">6 Bulan Terakhir</option>
                    </select>
                </div>
                <div id="volumeChartWrapper" class="relative rounded-xl" style="height:280px;">
                    <div id="volumeLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <p class="text-xs text-gray-400 font-medium">Memuat data…</p>
                        </div>
                    </div>
                    <div id="volumeEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                        <p class="text-xs text-gray-400 font-medium">Belum ada data transaksi</p>
                    </div>
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Analitik Pelanggan --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Analitik Pelanggan</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Top 10 Pelanggan Terloyal</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Berdasarkan frekuensi transaksi</p>
                    </div>
                    <span class="text-[10px] bg-sky-100 text-sky-600 px-2.5 py-1 rounded-full font-semibold">Bar Chart</span>
                </div>
                <div id="pelangganChartWrapper" class="relative rounded-xl" style="height:300px;">
                    <div id="pelangganLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <p class="text-xs text-gray-400 font-medium">Memuat data…</p>
                        </div>
                    </div>
                    <div id="pelangganEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                        <p class="text-xs text-gray-400 font-medium">Belum ada data pelanggan</p>
                    </div>
                    <canvas id="pelangganChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Distribusi Jenis Layanan</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Persentase sebaran layanan yang dipilih</p>
                    </div>
                    <span class="text-[10px] bg-rose-100 text-rose-600 px-2.5 py-1 rounded-full font-semibold">Doughnut</span>
                </div>
                <div id="layananChartWrapper" class="relative rounded-xl" style="height:300px;">
                    <div id="layananLoading" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            <p class="text-xs text-gray-400 font-medium">Memuat data…</p>
                        </div>
                    </div>
                    <div id="layananEmpty" class="absolute inset-0 bg-gray-50 rounded-xl border border-dashed border-gray-300 flex items-center justify-center z-10 hidden">
                        <p class="text-xs text-gray-400 font-medium">Belum ada data layanan</p>
                    </div>
                    <canvas id="layananChart"></canvas>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function formatRupiah(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }
    function klasifikasiMape(m) {
        if (m < 10) return {label:'Sangat Baik (< 10%)',colorClass:'text-emerald-600',bgClass:'bg-emerald-100',borderClass:'border-emerald-200',iconBg:'bg-emerald-500'};
        if (m < 20) return {label:'Baik (10–20%)',colorClass:'text-sky-600',bgClass:'bg-sky-100',borderClass:'border-sky-200',iconBg:'bg-sky-500'};
        if (m < 50) return {label:'Layak (20–50%)',colorClass:'text-amber-600',bgClass:'bg-amber-50',borderClass:'border-amber-200',iconBg:'bg-amber-500'};
        return {label:'Buruk (> 50%)',colorClass:'text-red-600',bgClass:'bg-red-50',borderClass:'border-red-200',iconBg:'bg-red-500'};
    }
    var chartOpts = {titleFont:{size:11,family:'Inter'},bodyFont:{size:11,family:'Inter'},padding:10,cornerRadius:8,backgroundColor:'#1F2937'};
    var fontSm = {size:10,family:'Inter'};
    var fontLabel = {size:11,family:'Inter',weight:'600'};

    // ── Chart 1: Prediksi Akurasi ──
    var c1 = null;
    async function loadC1() {
        try {
            var r = await fetch('{{ route("owner.grafik.prediksi-akurasi.data") }}');
            var d = await r.json();
            document.getElementById('prediksiLoading').classList.add('hidden');
            if (!d.labels || d.labels.length === 0) { document.getElementById('prediksiEmpty').classList.remove('hidden'); return; }
            if (c1) c1.destroy();
            c1 = new Chart(document.getElementById('prediksiChart').getContext('2d'), {
                type:'line', data:{labels:d.labels, datasets:[
                    {label:'Prediksi (jam)',data:d.predicted,borderColor:'#3B82F6',backgroundColor:'rgba(59,130,246,0.08)',borderWidth:2.5,pointBackgroundColor:'#3B82F6',pointBorderColor:'#fff',pointBorderWidth:2,pointRadius:4,pointHoverRadius:6,tension:0.3,fill:true},
                    {label:'Aktual (jam)',data:d.actual,borderColor:'#10B981',backgroundColor:'rgba(16,185,129,0.08)',borderWidth:2.5,pointBackgroundColor:'#10B981',pointBorderColor:'#fff',pointBorderWidth:2,pointRadius:4,pointHoverRadius:6,tension:0.3,fill:true}
                ]}, options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},
                    plugins:{legend:{position:'top',labels:{usePointStyle:true,pointStyle:'circle',padding:16,font:{size:11,family:'Inter',weight:'500'}}},tooltip:Object.assign({},chartOpts,{callbacks:{afterBody:function(ctx){var mv=d.mape[ctx[0].dataIndex];return mv!==null?'\nMAPE: '+mv+'%':'';}}})},
                    scales:{x:{ticks:{font:fontSm,maxRotation:45},grid:{display:false}},y:{beginAtZero:true,title:{display:true,text:'Jam',font:fontLabel},ticks:{font:fontSm},grid:{color:'rgba(0,0,0,0.04)'}}}
                }
            });
            if (d.avg_mape !== null) {
                var k = klasifikasiMape(d.avg_mape);
                document.getElementById('mapeValue').textContent = d.avg_mape + '%';
                document.getElementById('mapeCategory').textContent = 'Kategori: ' + k.label;
                document.getElementById('mapeCategory').className = 'text-xs mt-0.5 font-medium ' + k.colorClass;
                document.getElementById('mapeSummaryBox').className = 'flex items-center gap-3 px-4 py-3 rounded-xl border ' + k.bgClass + ' ' + k.borderClass;
                document.getElementById('mapeIcon').className = 'w-10 h-10 rounded-lg flex items-center justify-center ' + k.iconBg;
                document.getElementById('mapeSummary').classList.remove('hidden');
            }
        } catch(e) { console.error(e); document.getElementById('prediksiLoading').classList.add('hidden'); document.getElementById('prediksiEmpty').classList.remove('hidden'); }
    }

    // ── Chart 2: Volume Transaksi ──
    var c2 = null;
    async function loadC2(range) {
        document.getElementById('volumeLoading').classList.remove('hidden');
        document.getElementById('volumeEmpty').classList.add('hidden');
        try {
            var r = await fetch('{{ route("owner.grafik.volume-transaksi.data") }}?range=' + encodeURIComponent(range));
            var d = await r.json();
            document.getElementById('volumeLoading').classList.add('hidden');
            if (!d.labels || d.labels.length === 0) { document.getElementById('volumeEmpty').classList.remove('hidden'); return; }
            if (c2) c2.destroy();
            c2 = new Chart(document.getElementById('volumeChart').getContext('2d'), {
                data:{labels:d.labels, datasets:[
                    {type:'bar',label:'Jumlah Pesanan',data:d.volume,backgroundColor:'rgba(16,185,129,0.7)',hoverBackgroundColor:'rgba(16,185,129,0.9)',borderColor:'rgba(16,185,129,1)',borderWidth:1,borderRadius:6,barPercentage:0.6,yAxisID:'y',order:2},
                    {type:'line',label:'Pendapatan (Rp)',data:d.revenue,borderColor:'#6366F1',backgroundColor:'rgba(99,102,241,0.1)',borderWidth:2.5,pointBackgroundColor:'#6366F1',pointBorderColor:'#fff',pointBorderWidth:2,pointRadius:4,pointHoverRadius:6,tension:0.3,fill:true,yAxisID:'y1',order:1}
                ]}, options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},
                    plugins:{legend:{position:'top',labels:{usePointStyle:true,pointStyle:'circle',padding:16,font:{size:11,family:'Inter',weight:'500'}}},tooltip:Object.assign({},chartOpts,{callbacks:{label:function(ctx){if(ctx.dataset.yAxisID==='y1')return ctx.dataset.label+': '+formatRupiah(ctx.raw);return ctx.dataset.label+': '+ctx.raw+' pesanan';}}})},
                    scales:{x:{ticks:{font:fontSm},grid:{display:false}},y:{type:'linear',position:'left',beginAtZero:true,title:{display:true,text:'Pesanan',font:fontLabel},ticks:{font:fontSm,stepSize:1,precision:0},grid:{color:'rgba(0,0,0,0.04)'}},y1:{type:'linear',position:'right',beginAtZero:true,title:{display:true,text:'Pendapatan',font:fontLabel},ticks:{font:fontSm,callback:function(v){return formatRupiah(v);}},grid:{drawOnChartArea:false}}}
                }
            });
        } catch(e) { console.error(e); document.getElementById('volumeLoading').classList.add('hidden'); document.getElementById('volumeEmpty').classList.remove('hidden'); }
    }

    // ── Chart 3: Tren Pelanggan + Distribusi Layanan ──
    var c3a = null, c3b = null;
    async function loadC3() {
        try {
            var r = await fetch('{{ route("owner.grafik.tren-pelanggan.data") }}');
            var d = await r.json();
            // 3a: Horizontal Bar
            document.getElementById('pelangganLoading').classList.add('hidden');
            if (!d.pelanggan || !d.pelanggan.labels || d.pelanggan.labels.length===0) { document.getElementById('pelangganEmpty').classList.remove('hidden'); }
            else {
                if (c3a) c3a.destroy();
                var hues=[160,200,260,30,340,180,220,280,50,140];
                var cols=d.pelanggan.data.map(function(_,i){return 'hsla('+hues[i%hues.length]+',70%,55%,0.85)';});
                c3a = new Chart(document.getElementById('pelangganChart').getContext('2d'),{type:'bar',data:{labels:d.pelanggan.labels,datasets:[{label:'Jumlah Transaksi',data:d.pelanggan.data,backgroundColor:cols,borderColor:cols.map(function(c){return c.replace('0.85','1');}),borderWidth:1,borderRadius:4,barPercentage:0.7}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:Object.assign({},chartOpts,{callbacks:{label:function(ctx){return ctx.raw+' transaksi';}}})},scales:{x:{beginAtZero:true,title:{display:true,text:'Frekuensi',font:fontLabel},ticks:{font:fontSm,stepSize:1,precision:0},grid:{color:'rgba(0,0,0,0.04)'}},y:{ticks:{font:fontSm},grid:{display:false}}}}});
            }
            // 3b: Doughnut
            document.getElementById('layananLoading').classList.add('hidden');
            if (!d.layanan || !d.layanan.labels || d.layanan.labels.length===0) { document.getElementById('layananEmpty').classList.remove('hidden'); }
            else {
                if (c3b) c3b.destroy();
                var dc=['rgba(16,185,129,0.85)','rgba(59,130,246,0.85)','rgba(249,115,22,0.85)','rgba(139,92,246,0.85)','rgba(236,72,153,0.85)','rgba(234,179,8,0.85)','rgba(20,184,166,0.85)','rgba(99,102,241,0.85)'];
                c3b = new Chart(document.getElementById('layananChart').getContext('2d'),{type:'doughnut',data:{labels:d.layanan.labels,datasets:[{data:d.layanan.data,backgroundColor:dc.slice(0,d.layanan.labels.length),borderColor:'#fff',borderWidth:3,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'55%',plugins:{legend:{position:'bottom',labels:{usePointStyle:true,pointStyle:'circle',padding:16,font:{size:11,family:'Inter',weight:'500'}}},tooltip:Object.assign({},chartOpts,{callbacks:{label:function(ctx){var t=ctx.dataset.data.reduce(function(a,b){return a+b;},0);var p=t>0?((ctx.raw/t)*100).toFixed(1):0;return ctx.label+': '+ctx.raw+' ('+p+'%)';}}})}}});
            }
        } catch(e) { console.error(e); document.getElementById('pelangganLoading').classList.add('hidden'); document.getElementById('pelangganEmpty').classList.remove('hidden'); document.getElementById('layananLoading').classList.add('hidden'); document.getElementById('layananEmpty').classList.remove('hidden'); }
    }

    // Init
    loadC1(); loadC2('tahun'); loadC3();
    document.getElementById('volumeRangeFilter').addEventListener('change', function(){ loadC2(this.value); });
});
</script>
@endpush
