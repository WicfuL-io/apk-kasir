<!DOCTYPE html>
<html>

<head>

<title>Customer Display</title>

<script src="https://cdn.tailwindcss.com"></script>
@vite(['resources/js/app.js'])

<style>
body {
    font-family: 'Segoe UI', sans-serif;
}
.fade-in {
    animation: fadeIn 0.4s ease;
}
@keyframes fadeIn {
    from {opacity:0; transform:translateY(10px);}
    to {opacity:1; transform:translateY(0);}
}

.pulse-strong {
    animation: pulseStrong 1s infinite;
}
@keyframes pulseStrong {
    0% {transform: scale(1);}
    50% {transform: scale(1.03);}
    100% {transform: scale(1);}
}
</style>

</head>

<body class="bg-black text-white h-screen flex flex-col overflow-hidden">

<!-- HEADER -->
<div class="bg-gray-900 p-6 flex justify-between items-center">

<h1 class="text-4xl font-bold text-green-400">
{{ config('app.name') }}
</h1>

<div class="text-right">
<p class="text-lg text-gray-400">Kasir</p>
<p id="cashierName" class="text-2xl font-bold">-</p>
<p id="clock" class="text-sm text-gray-500 mt-1"></p>
</div>

</div>

<!-- CONTENT -->
<div class="flex flex-1">

<!-- LEFT -->
<div class="w-2/3 p-10 overflow-hidden">

<h2 class="text-3xl font-bold mb-6">Pesanan Anda</h2>

<table class="w-full text-2xl">

<thead class="border-b border-gray-600 text-gray-300">
<tr>
<th class="text-left p-4">Produk</th>
<th class="p-4">Qty</th>
<th class="p-4">Harga</th>
<th class="p-4 text-red-400">Disc</th>
<th class="p-4">Subtotal</th>
</tr>
</thead>

<tbody id="displayCart" class="divide-y divide-gray-700"></tbody>

</table>

</div>

<!-- RIGHT -->
<div class="w-1/3 bg-gray-900 p-10 flex flex-col justify-between">

<div>

<p class="text-xl text-gray-400 mb-2">Subtotal</p>
<p id="displaySubtotal" class="text-3xl font-bold mb-6">Rp 0</p>

<p class="text-xl text-red-400 mb-2">Total Discount</p>
<p id="displayDiscount" class="text-3xl font-bold mb-6">Rp 0</p>

<p class="text-xl text-gray-400 mb-2">Total Bayar</p>
<p id="displayTotal" class="text-5xl font-bold text-green-400 mb-10 pulse-strong">Rp 0</p>

<p class="text-xl text-gray-400">Metode Pembayaran</p>
<p id="paymentMethod" class="text-3xl font-bold text-yellow-400 mb-6">-</p>

<!-- 🔥 TAMBAHAN VA -->
<div id="vaArea" class="hidden text-center mt-6">

<p class="text-gray-400 text-xl">Transfer ke Virtual Account</p>

<p id="displayVA"
class="text-4xl font-bold text-blue-400 mt-2 tracking-widest"></p>

<p id="displayBank" class="text-lg text-gray-400 mt-1"></p>

<p id="vaStatus" class="text-yellow-400 mt-3 text-xl">
Menunggu pembayaran...
</p>

</div>

<p class="text-xl text-gray-400 mt-6">Bayar</p>
<p id="paidMoney" class="text-2xl font-bold mb-6">Rp 0</p>

<p class="text-xl text-gray-400">Kembalian</p>
<p id="changeMoney" class="text-3xl font-bold text-green-400">Rp 0</p>

</div>

<div class="text-center text-gray-500 text-sm">
Terima kasih telah berbelanja
</div>

</div>

</div>

<!-- QR POPUP -->
<div id="qrPopup"
class="hidden fixed inset-0 bg-black bg-opacity-95 flex items-center justify-center z-50">

<div class="text-center w-full max-w-5xl">

<p class="text-yellow-400 text-4xl font-bold mb-8 animate-pulse">
Scan QRIS untuk Membayar
</p>

<div class="flex justify-center">
<iframe id="qrisFrame"
class="rounded-xl shadow-2xl bg-white"
style="width:659px;height:650px;border:none"></iframe>
</div>

<p id="paymentStatus" class="text-2xl text-yellow-300 mt-6">
Menunggu Pembayaran
</p>

</div>

</div>

<script>

document.addEventListener("DOMContentLoaded", function(){

setInterval(()=>{
document.getElementById("clock").innerText =
new Date().toLocaleTimeString()
},1000)

let tbody = document.getElementById("displayCart")

let totalText = document.getElementById("displayTotal")
let subtotalText = document.getElementById("displaySubtotal")
let discountText = document.getElementById("displayDiscount")

let cashierText = document.getElementById("cashierName")

let paymentText = document.getElementById("paymentMethod")
let paidText = document.getElementById("paidMoney")
let changeText = document.getElementById("changeMoney")

let qrPopup = document.getElementById("qrPopup")
let qrFrame = document.getElementById("qrisFrame")
let paymentStatus = document.getElementById("paymentStatus")

/* 🔥 VA */
let vaArea = document.getElementById("vaArea")
let displayVA = document.getElementById("displayVA")
let displayBank = document.getElementById("displayBank")
let vaStatus = document.getElementById("vaStatus")

function rupiah(n){
return "Rp " + Number(n || 0).toLocaleString()
}

function formatVA(va){
return (va || "").replace(/(.{4})/g,"$1 ").trim()
}

function updateDisplay(e){

if(!e) return

tbody.innerHTML=""

let subtotal = 0
let discountTotal = 0
let total = 0

let cart = e.cart ?? []

cart.forEach((item,index)=>{

let disc = item.price * item.discount / 100
let final = item.price - disc

let sub = item.price * item.qty
let dis = disc * item.qty
let fin = final * item.qty

subtotal += sub
discountTotal += dis
total += fin

let highlight = index === cart.length-1
? "bg-green-900 animate-pulse"
: ""

tbody.innerHTML += `
<tr class="${highlight} fade-in">
<td class="p-4">${item.name}</td>
<td class="p-4 text-center">${item.qty}</td>
<td class="p-4 text-center">${rupiah(item.price)}</td>
<td class="p-4 text-center text-red-400">${item.discount}%</td>
<td class="p-4 text-center font-bold">${rupiah(fin)}</td>
</tr>`
})

subtotalText.innerText = rupiah(subtotal)
discountText.innerText = rupiah(discountTotal)
totalText.innerText = rupiah(e.total ?? total)

cashierText.innerText = e.cashier ?? "-"
paymentText.innerText = e.payment ?? "-"

paidText.innerText = rupiah(e.paid ?? 0)
changeText.innerText = rupiah(e.change ?? 0)

/* QR */
if(e.qr){
qrPopup.classList.remove("hidden")
qrFrame.src = e.qr
paymentStatus.innerText = e.status ?? "Menunggu Pembayaran"
}else{
qrPopup.classList.add("hidden")
qrFrame.src = ""
}

/* 🔥 VA DISPLAY */
if(e.va){
vaArea.classList.remove("hidden")
displayVA.innerText = formatVA(e.va)
displayBank.innerText = "Bank: " + (e.bank ?? "-")
vaStatus.innerText = e.status ?? "Menunggu pembayaran..."
}else{
vaArea.classList.add("hidden")
}

/* SUCCESS */
if(["success","settlement","paid"].includes(e.status)){

paymentStatus.innerText = "✅ Pembayaran Berhasil"
paymentStatus.className = "text-2xl text-green-400 font-bold"

try{ new Audio('/success.mp3').play() }catch(e){}

setTimeout(()=>resetDisplay(),2000)
}

/* FAILED */
if(e.status === "failed"){
paymentStatus.innerText = "❌ Pembayaran Gagal"
paymentStatus.className = "text-2xl text-red-500 font-bold"
}

}

function resetDisplay(){

tbody.innerHTML=""
subtotalText.innerText="Rp 0"
discountText.innerText="Rp 0"
totalText.innerText="Rp 0"

paidText.innerText="Rp 0"
changeText.innerText="Rp 0"

paymentText.innerText="-"

qrFrame.src=""
qrPopup.classList.add("hidden")

vaArea.classList.add("hidden")

}

function connectEcho(){

if(typeof Echo === "undefined"){
setTimeout(connectEcho,2000)
return
}

Echo.channel("cart-channel")
.listen(".cart.updated",(e)=>{
updateDisplay(e)
})
}

connectEcho()

})

</script>

</body>
</html>