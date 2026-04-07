<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white shadow-sm border-b border-gray-200">
    
<div class="max-w-7xl mx-auto px-6 lg:px-8">

<div class="flex justify-between h-16">

<!-- LEFT SIDE -->
<div class="flex items-center gap-10">

<!-- LOGO -->
<a href="{{ route('dashboard') }}" class="flex items-center gap-2">

<div class="bg-indigo-600 text-white font-bold w-9 h-9 flex items-center justify-center rounded-lg">
{{ strtoupper(substr(config('app.name'),0,1)) }}
</div>

<span class="font-bold text-lg text-gray-800">
{{ config('app.name') }}
</span>

</a>

<!-- MENU DESKTOP -->
<div class="hidden sm:flex items-center gap-2 text-sm font-medium">

<a href="{{ route('dashboard') }}"
class="px-3 py-2 rounded-lg transition
{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
Dashboard
</a>

<a href="/kasir"
class="px-3 py-2 rounded-lg transition
{{ request()->is('kasir*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
Kasir
</a>

<a href="/inventory"
class="px-3 py-2 rounded-lg transition
{{ request()->is('inventory*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
Inventory
</a>

<a href="/cek-harga"
class="px-3 py-2 rounded-lg transition
{{ request()->is('cek-harga*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
Cek Harga
</a>

<a href="/laporan"
class="px-3 py-2 rounded-lg transition
{{ request()->is('laporan*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
Struk
</a>

<!-- 🔥 AUDIT LOG (FIX + ACTIVE STATE) -->
<a href="{{ route('audit-log') }}"
class="px-3 py-2 rounded-lg transition flex items-center gap-1
{{ request()->is('audit-log*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">

<!-- ICON -->
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
d="M9 17v-6h13M9 5v6h13M5 5h.01M5 12h.01M5 19h.01"/>
</svg>

Audit Log
</a>

</div>

</div>


<!-- USER DROPDOWN -->
<div class="hidden sm:flex items-center">

<x-dropdown align="right" width="48">

<x-slot name="trigger">

<button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">

<div class="w-8 h-8 bg-indigo-600 text-white flex items-center justify-center rounded-full text-sm font-bold">
{{ strtoupper(substr(Auth::user()->name,0,1)) }}
</div>

<span class="text-sm font-medium text-gray-700">
{{ Auth::user()->name }}
</span>

<svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20">
<path fill="currentColor" d="M5.293 7.293L10 12l4.707-4.707"/>
</svg>

</button>

</x-slot>

<x-slot name="content">

<x-dropdown-link :href="route('profile.edit')">
Profile
</x-dropdown-link>

<form method="POST" action="{{ route('logout') }}">
@csrf

<x-dropdown-link :href="route('logout')"
onclick="event.preventDefault(); this.closest('form').submit();">
Logout
</x-dropdown-link>

</form>

</x-slot>

</x-dropdown>

</div>


<!-- MOBILE BUTTON -->
<div class="flex items-center sm:hidden">

<button @click="open = ! open"
class="p-2 rounded-md text-gray-500 hover:bg-gray-100 transition">

<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

<path :class="{'hidden': open, 'inline-flex': ! open}"
stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M4 6h16M4 12h16M4 18h16" />

<path :class="{'hidden': ! open, 'inline-flex': open}"
stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M6 18L18 6M6 6l12 12" />

</svg>

</button>

</div>

</div>

</div>


<!-- MOBILE MENU -->
<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">

<div class="px-4 py-4 space-y-2 text-sm">

<a href="{{ route('dashboard') }}"
class="block px-3 py-2 rounded
{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Dashboard
</a>

<a href="/kasir"
class="block px-3 py-2 rounded
{{ request()->is('kasir*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Kasir
</a>

<a href="/inventory"
class="block px-3 py-2 rounded
{{ request()->is('inventory*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Inventory
</a>

<a href="/cek-harga"
class="block px-3 py-2 rounded
{{ request()->is('cek-harga*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Cek Harga
</a>

<a href="/laporan"
class="block px-3 py-2 rounded
{{ request()->is('laporan*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Struk
</a>

<!-- 🔥 MOBILE AUDIT LOG FIX -->
<a href="{{ route('audit-log') }}"
class="block px-3 py-2 rounded
{{ request()->is('audit-log*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100' }}">
Audit Log
</a>

</div>

</div>

</nav>