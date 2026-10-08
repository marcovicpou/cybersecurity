const modal=document.getElementById('recordModal');
function openModal(){ if(modal) modal.classList.add('show'); }
function closeModal(){ if(modal) modal.classList.remove('show'); const u=new URL(location.href); if(u.searchParams.has('edit')){u.searchParams.delete('edit');history.replaceState({},'',u)} }
if(modal) modal.addEventListener('click',e=>{if(e.target===modal) closeModal()});
document.getElementById('menuBtn')?.addEventListener('click',()=>document.querySelector('.sidebar')?.classList.toggle('open'));
document.getElementById('globalSearch')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('.data-table tbody tr').forEach(r=>r.style.display=r.innerText.toLowerCase().includes(q)?'':'none')});
