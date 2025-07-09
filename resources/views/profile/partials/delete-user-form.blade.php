<form method="POST" action="{{ route('profile.destroy') }}" id="deleteForm" onsubmit="return confirmDelete();">
    @csrf
    @method('DELETE')

    <!-- Warning Box -->
    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 15px; padding: 25px; margin-bottom: 25px; border: 2px solid rgba(239, 68, 68, 0.3);">
        <div style="display: flex; align-items: center; margin-bottom: 15px;">
            <div style="background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                <span style="font-size: 1.2rem;">⚠️</span>
            </div>
            <h4 style="color: white; font-family: 'DM Serif Text', serif; font-size: 1.3rem; margin: 0;">ZONA BAHAYA</h4>
        </div>
        <p style="color: rgba(255, 255, 255, 0.9); font-size: 1rem; line-height: 1.6; margin: 0;">
            Menghapus akun akan <strong>menghapus semua data Anda secara permanen</strong> termasuk semua kenangan, surprise, dan event yang telah dibuat. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
        </p>
    </div>

    <!-- What Will Be Deleted -->
    <div style="background: rgba(239, 68, 68, 0.1); border-radius: 15px; padding: 20px; margin-bottom: 25px; border: 1px solid rgba(239, 68, 68, 0.3);">
        <h5 style="color: #ef4444; font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">📋 Data yang akan dihapus:</h5>
        <ul style="color: #D3D3D9; font-size: 0.95rem; line-height: 1.6; margin: 0; padding-left: 20px;">
            <li>🏠 Profil dan informasi pribadi</li>
            <li>💝 Semua memories dan kenangan</li>
            <li>🎂 Birthday surprises yang dibuat dan diterima</li>
            <li>📅 Event dan calendar yang dibuat</li>
            <li>🔐 Akses login dan autentikasi</li>
        </ul>
    </div>

    <!-- Password Confirmation -->
    <div style="margin-bottom: 25px;">
        <label for="password" style="display: block; color: #ef4444; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
            🔑 Konfirmasi Password
        </label>
        <input 
            id="password" 
            name="password" 
            type="password" 
            autocomplete="current-password"
            style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
            required
            placeholder="Masukkan password untuk konfirmasi penghapusan..."
            onfocus="this.style.borderColor='#ef4444'; this.style.background='rgba(52, 54, 70, 0.8)';"
            onblur="this.style.borderColor='rgba(239, 68, 68, 0.3)'; this.style.background='rgba(52, 54, 70, 0.6)';">
        
        <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; border-radius: 8px; padding: 12px; margin-top: 10px;">
            <p style="color: #D3D3D9; font-size: 0.9rem; margin: 0;">
                💡 Masukkan password saat ini untuk mengonfirmasi penghapusan akun
            </p>
        </div>
    </div>

    <!-- Final Warning Checkbox -->
    <div style="background: rgba(239, 68, 68, 0.1); border-radius: 12px; padding: 20px; margin-bottom: 30px; border: 1px solid rgba(239, 68, 68, 0.3);">
        <label style="display: flex; align-items: center; cursor: pointer;">
            <input 
                type="checkbox" 
                id="finalConfirm" 
                style="width: 20px; height: 20px; margin-right: 12px; accent-color: #ef4444;"
                onchange="toggleDeleteButton()">
            <span style="color: #D3D3D9; font-size: 1rem;">
                Saya memahami bahwa tindakan ini akan menghapus semua data saya secara permanen dan tidak dapat dibatalkan.
            </span>
        </label>
    </div>

    <!-- Delete Button -->
    <div style="padding-top: 20px; border-top: 1px solid rgba(239, 68, 68, 0.2); text-align: right;">
        <button 
            type="submit" 
            id="deleteButton"
            disabled
            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 15px 30px; border-radius: 12px; border: none; font-weight: 600; font-size: 1.1rem; cursor: not-allowed; transition: all 0.3s ease; opacity: 0.5;"
            onmouseover="if(!this.disabled) { this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(239, 68, 68, 0.4)'; }"
            onmouseout="if(!this.disabled) { this.style.transform='translateY(0)'; this.style.boxShadow='none'; }">
            🗑️ Hapus Akun Selamanya
        </button>
    </div>
</form>

<script>
function toggleDeleteButton() {
    const checkbox = document.getElementById('finalConfirm');
    const deleteButton = document.getElementById('deleteButton');
    
    if (checkbox.checked) {
        deleteButton.disabled = false;
        deleteButton.style.cursor = 'pointer';
        deleteButton.style.opacity = '1';
    } else {
        deleteButton.disabled = true;
        deleteButton.style.cursor = 'not-allowed';
        deleteButton.style.opacity = '0.5';
    }
}

function confirmDelete() {
    const password = document.getElementById('password').value;
    const checkbox = document.getElementById('finalConfirm');
    
    if (!password) {
        alert('❌ Password wajib diisi untuk konfirmasi!');
        return false;
    }
    
    if (!checkbox.checked) {
        alert('❌ Anda harus mencentang kotak konfirmasi!');
        return false;
    }
    
    const confirmation = confirm(`
🚨 PERINGATAN TERAKHIR! 🚨

Apakah Anda BENAR-BENAR yakin ingin menghapus akun ini?

Tindakan ini akan:
• Menghapus SEMUA data pribadi Anda
• Menghapus SEMUA kenangan dan memories
• Menghapus SEMUA birthday surprises
• Menghapus SEMUA event dan calendar
• TIDAK DAPAT DIBATALKAN!

Ketik "HAPUS AKUN SAYA" untuk melanjutkan:`);
    
    if (confirmation) {
        const finalConfirm = prompt('Ketik "HAPUS AKUN SAYA" untuk melanjutkan (huruf besar semua):');
        if (finalConfirm === 'HAPUS AKUN SAYA') {
            return true;
        } else {
            alert('❌ Konfirmasi tidak sesuai. Penghapusan dibatalkan.');
            return false;
        }
    }
    
    return false;
}
</script>