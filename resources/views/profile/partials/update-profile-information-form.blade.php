<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
    @csrf
    @method('PATCH')

    <div style="display: grid; gap: 25px;">
        <!-- Nama -->
        <div class="form-group">
            <label for="name" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                👤 Nama Lengkap
            </label>
            <input 
                id="name" 
                name="name" 
                type="text" 
                value="{{ old('name', Auth::user()->name) }}"
                style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(140, 224, 255, 0.2); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
                required 
                autofocus
                onfocus="this.style.borderColor='#8CE0FF'; this.style.background='rgba(52, 54, 70, 0.8)';"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'; this.style.background='rgba(52, 54, 70, 0.6)';">
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                📧 Alamat Email
            </label>
            <input 
                id="email" 
                name="email" 
                type="email" 
                value="{{ old('email', Auth::user()->email) }}"
                style="width: 100%; background: rgba(52, 54, 70, 0.6); border: 2px solid rgba(140, 224, 255, 0.2); border-radius: 12px; padding: 15px; color: #D3D3D9; font-size: 1rem; transition: all 0.3s ease;"
                required
                onfocus="this.style.borderColor='#8CE0FF'; this.style.background='rgba(52, 54, 70, 0.8)';"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'; this.style.background='rgba(52, 54, 70, 0.6)';">
        </div>

        <!-- Foto Profil -->
        <div class="form-group">
            <label for="profile_photo" style="display: block; color: #8CE0FF; font-weight: 600; font-size: 1rem; margin-bottom: 8px;">
                📷 Foto Profil
            </label>
            
            @if(Auth::user()->profile_photo_path)
                <div style="margin-bottom: 10px;">
                    <img src="{{ app('App\Services\StorageService')->getFileUrl(Auth::user()->profile_photo_path) }}"
                         alt="Current Profile Photo"
                         style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid #8CE0FF;">
                </div>
            @endif
            
            <input 
                type="file" 
                id="profile_photo"
                name="profile_photo"
                accept="image/*"
                style="width: 100%; padding: 12px; background: transparent; border: 2px solid #8CE0FF; border-radius: 8px; color: #D3D3D9; font-size: 0.95rem;"
                onfocus="this.style.borderColor='#8CE0FF'"
                onblur="this.style.borderColor='rgba(140, 224, 255, 0.2)'">
            
            <div style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #fbbf24; border-radius: 8px; padding: 12px; margin-top: 10px;">
                <p style="color: #D3D3D9; font-size: 0.9rem; margin: 0;">
                    💡 Pilih gambar dengan ukuran maksimal 2MB. Format yang didukung: JPG, PNG, GIF
                </p>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(140, 224, 255, 0.2); text-align: right;">
        <button 
            type="submit" 
            style="background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%); color: #181A26; padding: 15px 30px; border-radius: 12px; border: none; font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease;"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(140, 224, 255, 0.4)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            💾 Simpan Perubahan
        </button>
    </div>
</form>