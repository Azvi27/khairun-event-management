<form method="POST" action="{{ route('password.update') }}" id="passwordForm">
    @csrf
    @method('PUT')

    <div style="display: grid; gap: 25px;">
        <!-- Password Lama -->
        <div class="form-group">
            <label for="current_password" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                🔓 Password Saat Ini
            </label>
            <input 
                id="current_password" 
                name="current_password" 
                type="password" 
                autocomplete="current-password"
                style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(140, 224, 255, 0.2); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
                required
                placeholder="Masukkan password saat ini..."
                onfocus="this.style.borderColor='#8CE0FF'; this.style.background='rgba(52, 54, 70, 0.8)';"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'; this.style.background='rgba(52, 54, 70, 0.6)';">
        </div>

        <!-- Password Baru -->
        <div class="form-group">
            <label for="password" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                🔐 Password Baru
            </label>
            <input 
                id="password" 
                name="password" 
                type="password" 
                autocomplete="new-password"
                style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(140, 224, 255, 0.2); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
                required
                placeholder="Masukkan password baru..."
                onfocus="this.style.borderColor='#8CE0FF'; this.style.background='rgba(52, 54, 70, 0.8)';"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'; this.style.background='rgba(52, 54, 70, 0.6)';"
                oninput="checkPasswordStrength(this.value)">
            
            <!-- Password Strength Indicator -->
            <div id="passwordStrength" style="margin-top: 8px; display: none;">
                <div style="background: rgba(52, 54, 70, 0.6); border-radius: 8px; padding: 10px;">
                    <div style="display: flex; gap: 3px; margin-bottom: 5px;">
                        <div id="strength1" style="flex: 1; height: 4px; background: rgba(140, 224, 255, 0.2); border-radius: 2px;"></div>
                        <div id="strength2" style="flex: 1; height: 4px; background: rgba(140, 224, 255, 0.2); border-radius: 2px;"></div>
                        <div id="strength3" style="flex: 1; height: 4px; background: rgba(140, 224, 255, 0.2); border-radius: 2px;"></div>
                        <div id="strength4" style="flex: 1; height: 4px; background: rgba(140, 224, 255, 0.2); border-radius: 2px;"></div>
                    </div>
                    <p id="strengthText" style="color: #D3D3D9; font-size: 0.8rem; margin: 0;"></p>
                </div>
            </div>
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="form-group">
            <label for="password_confirmation" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                ✅ Konfirmasi Password Baru
            </label>
            <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                autocomplete="new-password"
                style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(140, 224, 255, 0.2); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
                required
                placeholder="Ulangi password baru..."
                onfocus="this.style.borderColor='#8CE0FF'; this.style.background='rgba(52, 54, 70, 0.8)';"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'; this.style.background='rgba(52, 54, 70, 0.6)';"
                oninput="checkPasswordMatch()">
            
            <div id="passwordMatch" style="margin-top: 8px; display: none;">
                <p id="matchText" style="font-size: 0.9rem; margin: 0;"></p>
            </div>
        </div>
    </div>

    <!-- Security Tips -->
    <div style="background: rgba(140, 224, 255, 0.1); border-radius: 12px; padding: 20px; margin: 20px 0; border: 1px solid rgba(140, 224, 255, 0.3);">
        <h4 style="color: #8CE0FF; margin-bottom: 10px; font-size: 1rem;">🛡️ Tips Password Aman:</h4>
        <ul style="color: #D3D3D9; font-size: 0.9rem; line-height: 1.6; margin: 0; padding-left: 20px;">
            <li>Minimal 8 karakter</li>
            <li>Kombinasi huruf besar, kecil, angka, dan simbol</li>
            <li>Hindari informasi pribadi (nama, tanggal lahir)</li>
            <li>Gunakan password yang unik untuk setiap akun</li>
        </ul>
    </div>

    <!-- Submit Button -->
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(140, 224, 255, 0.2); text-align: right;">
        <button 
            type="submit" 
            id="passwordSubmit"
            style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 15px 30px; border-radius: 12px; border: none; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease;"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(34, 197, 94, 0.4)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            🔒 Ganti Password
        </button>
    </div>
</form>

<script>
function checkPasswordStrength(password) {
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (password.length === 0) {
        strengthDiv.style.display = 'none';
        return;
    }
    
    strengthDiv.style.display = 'block';
    
    let strength = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) strength++;
    else feedback.push("minimal 8 karakter");
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push("huruf besar");
    
    // Lowercase check
    if (/[a-z]/.test(password)) strength++;
    else feedback.push("huruf kecil");
    
    // Number or symbol check
    if (/[\d\W]/.test(password)) strength++;
    else feedback.push("angka/simbol");
    
    // Update visual indicators
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById(`strength${i}`);
        if (i <= strength) {
            if (strength <= 2) indicator.style.background = '#ef4444';
            else if (strength === 3) indicator.style.background = '#fbbf24';
            else indicator.style.background = '#22c55e';
        } else {
            indicator.style.background = 'rgba(140, 224, 255, 0.2)';
        }
    }
    
    // Update text
    if (strength <= 2) {
        strengthText.innerHTML = '🔴 Lemah: Tambahkan ' + feedback.join(', ');
        strengthText.style.color = '#ef4444';
    } else if (strength === 3) {
        strengthText.innerHTML = '🟡 Sedang: Tambahkan ' + feedback.join(', ');
        strengthText.style.color = '#fbbf24';
    } else {
        strengthText.innerHTML = '🟢 Kuat: Password aman!';
        strengthText.style.color = '#22c55e';
    }
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const matchDiv = document.getElementById('passwordMatch');
    const matchText = document.getElementById('matchText');
    
    if (confirmation.length === 0) {
        matchDiv.style.display = 'none';
        return;
    }
    
    matchDiv.style.display = 'block';
    
    if (password === confirmation) {
        matchText.innerHTML = '✅ Password cocok!';
        matchText.style.color = '#22c55e';
    } else {
        matchText.innerHTML = '❌ Password tidak cocok';
        matchText.style.color = '#ef4444';
    }
}
</script>