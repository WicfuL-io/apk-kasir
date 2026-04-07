<x-app-layout>

<div class="p-8">
<div class="grid grid-cols-3 gap-6">

<!-- LEFT SIDE -->
<div class="col-span-2 bg-white shadow-lg rounded-xl p-6">

<div class="flex justify-between items-center mb-4">
<h3 class="font-semibold text-lg text-gray-700">Scan Produk</h3>

<button onclick="openDisplay()"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
Buka Customer Display
</button>
</div>

<div class="flex gap-3 mb-6">
<input type="text" id="barcode"
placeholder="Scan atau ketik barcode..."
class="border border-gray-300 focus:ring-2 focus:ring-indigo-500 p-3 w-full rounded-lg"
autofocus>
</div>

<div class="overflow-x-auto">
<table class="w-full text-sm">
<thead class="bg-indigo-50 text-gray-700">
<tr>
<th class="p-3 text-left">Produk</th>
<th class="p-3 text-center">Harga</th>
<th class="p-3 text-center">Qty</th>
<th class="p-3 text-center">Discount</th>
<th class="p-3 text-center">Subtotal</th>
<th></th>
</tr>
</thead>
<tbody id="cart" class="divide-y"></tbody>
</table>
</div>

</div>

<!-- RIGHT SIDE -->
<div class="bg-white shadow-lg rounded-xl p-6">

<h3 class="text-lg font-bold mb-4 text-gray-700">
Ringkasan Transaksi
</h3>

<div class="space-y-2">
<div class="flex justify-between">
<span>Total Harga</span>
<span id="subtotal">Rp 0</span>
</div>

<div class="flex justify-between text-red-500">
<span>Total Discount</span>
<span id="discountTotal">Rp 0</span>
</div>
</div>

<hr class="my-4">

<div class="flex justify-between font-bold text-xl text-indigo-600">
<span>Total Bayar</span>
<span id="total">Rp 0</span>
</div>

<div class="mt-6">
<label class="text-sm text-gray-500">Metode Pembayaran</label>

<select id="paymentMethod" class="border p-2 w-full rounded-lg mt-1">
<option value="Cash">Cash</option>
<option value="QRIS">QRIS</option>
<option value="Transfer">Transfer</option>
</select>
</div>

<!-- BANK -->
<div id="bankArea" class="hidden mt-4">
<label class="text-sm text-gray-500">Pilih Bank</label>

<select id="bankSelect" class="border p-2 w-full rounded-lg mt-1">
<option value="">-- pilih bank --</option>
<option value="bca">BCA VA</option>
<option value="bni">BNI VA</option>
<option value="bri">BRI VA</option>
<option value="permata">Permata VA</option>
<option value="cimb">CIMB VA</option>
<option value="danamon">Danamon VA</option>
<option value="bsi">BSI VA</option>
<option value="sea">SeaBank VA</option>
<option value="mandiri">Mandiri Bill</option>
</select>
</div>

<!-- QRIS -->
<div id="qrisArea" class="hidden mt-6 text-center">
<p class="text-gray-500 mb-3">Scan QRIS Untuk Membayar</p>

<iframe id="qrisFrame"
class="mx-auto rounded-lg shadow-lg"
style="width:320px;height:320px;border:none"></iframe>

<p id="qrisStatus" class="mt-3 text-yellow-600 font-semibold">
Menunggu Pembayaran
</p>
</div>

<!-- TRANSFER -->
<div id="transferArea" class="hidden mt-6 text-center">
<p class="text-gray-500">Transfer ke Virtual Account</p>

<p id="vaNumber"
class="text-2xl font-bold text-indigo-600 mt-2 cursor-pointer"></p>

<p id="vaBank" class="text-sm text-gray-500"></p>

<p class="mt-3 text-yellow-600 font-semibold">
Menunggu Pembayaran
</p>
</div>

<!-- CASH -->
<div id="cashArea" class="hidden mt-4">
<label class="text-sm text-gray-500">Bayar</label>
<input type="number" id="pay"
class="border p-2 w-full rounded-lg mt-1">
</div>

<div id="changeArea" class="mt-4 hidden">
<label class="text-sm text-gray-500">Kembalian</label>
<p id="change"
class="text-2xl font-bold text-green-600">
Rp 0
</p>
</div>

<button onclick="processPayment()"
class="bg-indigo-600 hover:bg-indigo-700 text-white w-full py-3 rounded-lg mt-6 text-lg font-semibold shadow">
Proses Transaksi
</button>

</div>
</div>
</div>

<script>
function openDisplay(){
    window.open("/customer-display", "_blank", "width=1200,height=800");
}

document.addEventListener("DOMContentLoaded",function(){

let cart = []
let isProcessing = false

let barcodeInput = document.getElementById("barcode")
let payInput = document.getElementById("pay")

let changeArea = document.getElementById("changeArea")
let changeText = document.getElementById("change")

let paymentMethod = document.getElementById("paymentMethod")
let cashArea = document.getElementById("cashArea")
let bankArea = document.getElementById("bankArea")
let qrisArea = document.getElementById("qrisArea")
let transferArea = document.getElementById("transferArea")
let bankSelect = document.getElementById("bankSelect")
let processBtn = document.querySelector("button[onclick='processPayment()']")
let broadcastTimeout;

function resetAll(){

/* reset cart */
cart = []

/* reset input */
payInput.value = ""

/* reset metode */
paymentMethod.value = "Cash"

/* reset bank */
bankSelect.value = ""

/* reset UI */
updatePaymentUI()

/* reset change */
changeArea.classList.add("hidden")
changeText.innerText = "Rp 0"

/* reset QR */
document.getElementById("qrisFrame").src = ""

/* reset VA */
document.getElementById("vaNumber").innerText = ""
document.getElementById("vaBank").innerText = ""

/* render ulang */
renderCart()

/* 🔥 reset display customer */
broadcastCart(0,0,0,0,0)

}

function broadcastCart(subtotal, discountTotal, total, paid = 0, change = 0){

clearTimeout(broadcastTimeout);

broadcastTimeout = setTimeout(()=>{

fetch("/broadcast/cart",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute('content')
},
body:JSON.stringify({
cart,
subtotal,
discount:discountTotal,
total,
cashier:'{{ Auth::user()->name }}',
payment:document.getElementById("paymentMethod").value,
paid,   
change 
})
})

},200)

}

barcodeInput.focus()

function rupiah(n){
return "Rp " + Number(n || 0).toLocaleString()
}

function getNumber(el){
return parseInt((el.innerText || "").replace(/\D/g,'')) || 0
}

/* ================= FETCH (ANTI ERROR) ================= */
function fetchJSON(url, options = {}) {
return fetch(url, {
...options,
headers: {
"Content-Type": "application/json",
"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
...(options.headers || {})
}
})
.then(async res => {
let text = await res.text()

try {
return JSON.parse(text)
} catch (e) {
console.error("Response bukan JSON:", text)
throw new Error("Server error / bukan JSON")
}
})
}

/* ================= UI ================= */
function updatePaymentUI(){

cashArea.classList.add("hidden")
bankArea.classList.add("hidden")
qrisArea.classList.add("hidden")
transferArea.classList.add("hidden")

if(paymentMethod.value==="Cash"){
cashArea.classList.remove("hidden")
}

if(paymentMethod.value==="Transfer"){
bankArea.classList.remove("hidden")
transferArea.classList.remove("hidden")
}

if(paymentMethod.value==="QRIS"){
qrisArea.classList.remove("hidden")
}
}

paymentMethod.addEventListener("change", function(){
updatePaymentUI();

/* 🔥 TRIGGER DISPLAY */
broadcastCart(
getNumber(document.getElementById("subtotal")),
getNumber(document.getElementById("discountTotal")),
getNumber(document.getElementById("total"))
);

})
updatePaymentUI();

function resetProcess(){
isProcessing=false
setLoading(false)
}

function setLoading(state){
processBtn.innerText = state ? "Memproses..." : "Proses Transaksi"
processBtn.disabled = state
}

/* ================= BARCODE ================= */
barcodeInput.addEventListener("keyup",function(e){
if(e.key==="Enter"){
fetchJSON("/kasir/product?barcode="+this.value)
.then(data=>{
if(!data.status) return alert(data.message)

let existing = cart.find(item => item.id === data.id)

if(existing){
existing.qty++
}else{
cart.push({
id:data.id,
name:data.name,
price:data.sell_price,
discount:data.discount||0,
qty:1
})
}

renderCart()
})
.catch(handleError)

this.value=""
}
})

/* ================= CART ================= */
function renderCart(){

let tbody=document.getElementById("cart")
tbody.innerHTML=""

let subtotal=0
let discountTotal=0
let total=0

cart.forEach((item,i)=>{

let disc=item.price*item.discount/100
let final=item.price-disc

let sub=item.price*item.qty
let dis=disc*item.qty
let fin=final*item.qty

subtotal+=sub
discountTotal+=dis
total+=fin

tbody.innerHTML+=`
<tr>
<td>${item.name}</td>
<td>${rupiah(item.price)}</td>
<td>
<div style="display:flex;gap:6px;justify-content:center;align-items:center">
<button onclick="decreaseQty(${i})" style="padding:2px 8px;background:#ef4444;color:white;border-radius:4px">-</button>
<span>${item.qty}</span>
<button onclick="increaseQty(${i})" style="padding:2px 8px;background:#22c55e;color:white;border-radius:4px">+</button>
</div>
</td>
<td>${item.discount}%</td>
<td>${rupiah(fin)}</td>
<td><button onclick="removeItem(${i})">x</button></td>
</tr>`
})

document.getElementById("subtotal").innerText=rupiah(subtotal)
document.getElementById("discountTotal").innerText=rupiah(discountTotal)
document.getElementById("total").innerText=rupiah(total)

broadcastCart(0,0,0);
broadcastCart(
getNumber(document.getElementById("subtotal")),
getNumber(document.getElementById("discountTotal")),
getNumber(document.getElementById("total"))
);
}

window.removeItem=i=>{
cart.splice(i,1)
renderCart()
}

window.increaseQty = i => {
cart[i].qty++
renderCart()
}

window.decreaseQty = i => {
if(cart[i].qty > 1){
cart[i].qty--
}else{
cart.splice(i,1) // kalau 0 langsung hapus
}
renderCart()
}

/* ================= CASH ================= */
payInput.addEventListener("input",function(){

let pay=parseInt(this.value)||0
let total=getNumber(document.getElementById("total"))
let change = pay - total

if(pay>=total){
changeArea.classList.remove("hidden")
changeText.innerText=rupiah(change)
}else{
changeArea.classList.add("hidden")
change = 0
}

/* 🔥 KIRIM KE DISPLAY */
broadcastCart(
getNumber(document.getElementById("subtotal")),
getNumber(document.getElementById("discountTotal")),
total,
pay,
change
);

})

/* ================= PAYMENT ================= */
window.processPayment=function(){

if(isProcessing) return
isProcessing=true
setLoading(true)

if(cart.length===0){
alert("Cart kosong")
return resetProcess()
}

let total=getNumber(document.getElementById("total"))
let method=paymentMethod.value

/* ================= CASH ================= */
if(method==="Cash"){

let pay=parseInt(payInput.value)||0
if(pay<total){
alert("Uang kurang")
return resetProcess()
}

fetchJSON("/kasir/checkout",{
method:"POST",
body:JSON.stringify({cart,total,pay,change:pay-total,method:"Cash"})
})
.then(res => {

if(!res || res.status !== true){
alert(res?.message || "Gagal finalize")
return
}

/* 🔥 LANGSUNG BUKA STRUK */
if(res.transaction_id){
window.open("/struk/" + res.transaction_id, "_blank")
}

/* reset cart */
cart = []
payInput.value = ""
renderCart()

})
.catch(handleError)

return
}

/* ================= TRANSFER ================= */
if(method==="Transfer"){

let bank=bankSelect.value
if(!bank){
alert("Pilih bank dulu")
return resetProcess()
}

fetchJSON("/payment/create",{
method:"POST",
body:JSON.stringify({cart,total,method:"Transfer",bank})
})
.then(res=>{
if(!res.status){
console.error(res)
return alert(res.message || "Gagal transfer")
}

if(!res.va_number){
return alert("VA tidak ditemukan")
}

document.getElementById("vaNumber").innerText=res.va_number
document.getElementById("vaBank").innerText=res.bank

startCheckPayment(res.order_id)
})
.catch(handleError)

resetProcess()
return
}

/* ================= QRIS ================= */
if(method==="QRIS"){

fetchJSON("/payment/create",{
method:"POST",
body:JSON.stringify({cart,total,method:"QRIS"})
})
.then(res=>{
if(!res.status){
console.error(res)
return alert(res.message || "Gagal QRIS")
}

if(!res.qr){
return alert("QR tidak ditemukan")
}

document.getElementById("qrisFrame").src=res.qr

startCheckPayment(res.order_id)
})
.catch(handleError)

resetProcess()
return
}

resetProcess()
}

/* ================= POLLING ================= */
function startCheckPayment(order_id){

let interval = setInterval(()=>{

fetchJSON("/payment/status/"+order_id)
.then(res=>{

if(res.transaction_status==="settlement" || res.transaction_status==="capture"){

clearInterval(interval)
alert("Pembayaran berhasil")

finalizeTransaction(order_id)

}

if(["expire","cancel","deny"].includes(res.transaction_status)){
clearInterval(interval)
alert("Pembayaran gagal")
}

})
.catch(err=>{
console.error("Polling error:", err)
})

},3000)
}

/* ================= FINALIZE ================= */
function finalizeTransaction(order_id){

fetchJSON("/kasir/checkout-universal",{
method:"POST",
body:JSON.stringify({order_id})
})
.then(handleSuccess)
.catch(handleError)

}

/* ================= HANDLER ================= */
function handleSuccess(res){

if(!res || res.status!==true){
console.error(res)
alert(res?.message || "Gagal")
return resetProcess()
}

if(res.transaction_id){
window.open("/struk/"+res.transaction_id,"_blank")
}

cart=[]
payInput.value=""
renderCart()

alert("Transaksi berhasil")
resetProcess()
}

function handleError(err){
console.error("ERROR:", err)
alert("Error sistem: " + err.message)
resetProcess()
}

}

)
</script>

</x-app-layout>