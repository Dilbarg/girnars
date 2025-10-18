/* ---------- Products (you gave similar data) ---------- */
const products = [
  { company: "WINedge", name: "PVC Edge Band High Gloss", price: 650, mrp: 1050, size: "18mm x 10m", color: "White", desc: "High gloss finish, iron-on type" },
  { company: "E3", name: "PVC Edge Band Matte Finish", price: 620, mrp: 980, size: "18mm x 10m", color: "Brown", desc: "Matte finish, strong adhesive" },
  { company: "EDGELock", name: "PVC Edge Band Glossy", price: 700, mrp: 1100, size: "18mm x 10m", color: "Beige", desc: "Glossy surface, durable" },
  { company: "MERAKI Interior", name: "PVC Edge Band Premium", price: 750, mrp: 1200, size: "18mm x 10m", color: "Maroon", desc: "Premium look, hot melt glue backing" },
];

/* render products */
const grid = document.getElementById('productGrid');
products.forEach((p, idx) => {
  const el = document.createElement('div');
  el.className = 'card';
  el.innerHTML = `
    <h4>${p.company}</h4>
    <p><strong>${p.name}</strong></p>
    <p class="price">₹${p.price} <span style="text-decoration:line-through;color:#777;margin-left:8px">₹${p.mrp}</span></p>
    <p style="color:var(--muted)">${p.size} • ${p.color}</p>
    <p style="color:var(--muted);margin-top:8px;font-size:13px">${p.desc}</p>
    <button class="buy" onclick="openOrder(${idx})">Buy / Order</button>
  `;
  grid.appendChild(el);
});

/* modal logic */
let currentProductIndex = null;
function openOrder(index) {
  currentProductIndex = index;
  const p = products[index];
  document.getElementById('modalTitle').innerText = `Order: ${p.company} — ${p.name}`;
  document.getElementById('orderSize').value = p.size;
  document.getElementById('orderFinish').value = 'High Gloss';
  document.getElementById('orderQty').value = 1;
  document.getElementById('custName').value = '';
  document.getElementById('custEmail').value = '';
  document.getElementById('custAddress').value = '';
  document.getElementById('orderPayment').value = 'Cash on Delivery';
  document.getElementById('orderMsg').style.display = 'none';
  document.getElementById('orderModal').classList.remove('hidden');
}
function closeOrderModal(){ document.getElementById('orderModal').classList.add('hidden'); }

/* submit order -> AJAX to save.php */
async function confirmOrder() {
  const p = products[currentProductIndex];
  const size = document.getElementById('orderSize').value;
  const finish = document.getElementById('orderFinish').value;
  const quantity = parseInt(document.getElementById('orderQty').value || 1,10);
  const name = document.getElementById('custName').value.trim();
  const email = document.getElementById('custEmail').value.trim();
  const address = document.getElementById('custAddress').value.trim();
  const payment = document.getElementById('orderPayment').value;

  if(!name || !email || !address){ alert('Please fill name, email and address'); return; }

  const data = {
    company: p.company,
    product_name: p.name,
    price: p.price,
    size, finish, quantity,
    customer_name: name,
    customer_email: email,
    address, payment_method: payment
  };

  // send to server
  try {
    const resp = await fetch('save.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(data)
    });
    const text = await resp.text();
    document.getElementById('orderMsg').style.display='block';
    document.getElementById('orderMsg').innerText = text || 'Order received.';
    // clear and close after 2s
    setTimeout(()=>{ closeOrderModal(); }, 1800);
  } catch (err) {
    console.error(err);
    alert('Server error. Try again.');
  }
}

/* contact form -> AJAX to save.php (contact) */
document.getElementById('contactForm')?.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const name = document.getElementById('contactName').value.trim();
  const email = document.getElementById('contactEmail').value.trim();
  const message = document.getElementById('contactMessage').value.trim();
  if(!name || !email){ alert('Please provide name and email'); return; }
  try{
    const resp = await fetch('save.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ contact: true, name, email, message })
    });
    const txt = await resp.text();
    alert(txt || 'Message received. We will contact you.');
    document.getElementById('contactForm').reset();
  }catch(err){
    console.error(err);
    alert('Server error.');
  }
});
