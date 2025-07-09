@extends('layouts.khairun')

@section('title', 'Profile Edit')

@section('content')
    <script>
        // Redirect otomatis ke halaman profil utama
        window.location.href = "{{ route('profile.index') }}";
    </script>
@endsection