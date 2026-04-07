<x-app-layout>

<div class="p-10 bg-gray-50 min-h-screen">

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">

<!-- HEADER -->
<div class="flex justify-between items-center mb-6">

<h1 class="text-xl font-bold flex items-center gap-2">
    {{ $log->icon }} Detail Audit
</h1>

<a href="{{ route('audit-log') }}" 
   class="text-sm bg-gray-200 px-3 py-1 rounded hover:bg-gray-300 transition">
   ← Kembali
</a>

</div>

<div class="space-y-4 text-sm">

<!-- USER -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">User</span>
    <span class="font-medium">{{ $log->user_name }}</span>
</div>

<!-- AKTIVITAS -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">Aktivitas</span>
    <span class="font-medium text-right">{{ $log->description }}</span>
</div>

<!-- MODEL -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">Model</span>

    <span class="px-2 py-1 rounded text-xs
    {{
        $log->model === 'Transaction' ? 'bg-blue-100 text-blue-700' :
        ($log->model === 'Product' ? 'bg-green-100 text-green-700' :
        ($log->model === 'Payment' ? 'bg-yellow-100 text-yellow-700' :
        'bg-gray-100 text-gray-700'))
    }}">
        {{ $log->model }}
    </span>
</div>

<!-- ACTION -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">Action</span>
    <span class="font-medium">{{ $log->action }}</span>
</div>

<!-- STATUS -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">Status</span>
    <span class="px-2 py-1 rounded text-xs
        {{ $log->status === 'SUCCESS'
            ? 'bg-green-100 text-green-700'
            : 'bg-red-100 text-red-700' }}">
        {{ $log->status }}
    </span>
</div>

<!-- IP -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">IP Address</span>
    <span class="font-medium">{{ $log->ip_address ?? '-' }}</span>
</div>

<!-- METHOD -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">Method</span>
    <span class="font-medium">{{ $log->method ?? '-' }}</span>
</div>

<!-- URL -->
<div class="flex justify-between border-b pb-2">
    <span class="text-gray-500">URL</span>
    <span class="font-medium text-right break-all">{{ $log->url ?? '-' }}</span>
</div>

<!-- WAKTU -->
<div class="flex justify-between pt-2">
    <span class="text-gray-500">Waktu</span>
    <span class="font-medium">{{ $log->time }}</span>
</div>

</div>

</div>

</div>

</x-app-layout>