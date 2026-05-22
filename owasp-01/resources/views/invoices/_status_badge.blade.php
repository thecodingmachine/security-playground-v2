@php
$config = match($status) {
    'paid'    => ['bg-green-100 text-green-700', 'Payée'],
    'sent'    => ['bg-blue-100 text-blue-700', 'Envoyée'],
    'overdue' => ['bg-red-100 text-red-700', 'En retard'],
    default   => ['bg-gray-100 text-gray-600', 'Brouillon'],
};
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $config[0] }}">
    {{ $config[1] }}
</span>
