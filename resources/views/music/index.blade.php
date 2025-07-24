@extends('layouts.khairun')

@section('title', 'Music - Our Memories')

@push('head')
<!-- Spotify Web Playback SDK -->
<script src="https://sdk.scdn.co/spotify-player.js"></script>
@endpush

@push('styles')
<style>
    .music-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--space-6);
        background: var(--color-background);
        min-height: calc(100vh - 144px);
    }

    /* Alert Messages */
    .alert {
        padding: var(--space-4) var(--space-6);
        border-radius: 12px;
        margin-bottom: var(--space-6);
        font-weight: 500;
        border: 1px solid;
        position: relative;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        color: #065f46;
        border-color: rgba(16, 185, 129, 0.3);
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        color: #991b1b;
        border-color: rgba(239, 68, 68, 0.3);
    }
    
    .alert-info {
        background: rgba(59, 130, 246, 0.1);
        color: #1e40af;
        border-color: rgba(59, 130, 246, 0.3);
    }
    
    .music-header {
        text-align: center;
        margin-bottom: var(--space-12);
        background: linear-gradient(135deg, var(--color-secondary) 0%, rgba(24, 26, 38, 0.95) 100%);
        border-radius: 20px;
        padding: var(--space-16);
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        border: 1px solid rgba(140, 224, 255, 0.1);
    }
    
    .music-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.05) 0%, rgba(140, 224, 255, 0.1) 100%);
        z-index: 1;
    }
    
    .music-header > * {
        position: relative;
        z-index: 2;
    }
    
    .music-title {
        color: #FFFFFF;
        font-size: clamp(2rem, 4vw, 3rem);
        font-family: var(--font-primary);
        font-weight: 700;
        margin-bottom: var(--space-4);
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
        background: linear-gradient(45deg, var(--color-primary), #60a5fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .music-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: clamp(1rem, 2vw, 1.2rem);
        opacity: 0.9;
        font-style: italic;
        font-weight: 400;
    }

    /* Enhanced Music Player */
    .music-player {
        background: white;
        border-radius: 16px;
        padding: var(--space-8);
        margin-bottom: var(--space-12);
        border: 1px solid rgba(140, 224, 255, 0.2);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        transition: var(--transition-normal);
    }

    .music-player::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(140, 224, 255, 0.05), transparent);
        transition: left 1.5s ease-in-out;
    }

    .music-player:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .music-player:hover::before {
        left: 100%;
    }

    .now-playing {
        display: flex;
        align-items: center;
        gap: var(--space-6);
        margin-bottom: var(--space-6);
        position: relative;
        z-index: 2;
    }

    .album-art {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--color-primary), #60a5fa);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        box-shadow: var(--shadow-md);
        animation: pulse 3s ease-in-out infinite alternate;
        border: 2px solid rgba(140, 224, 255, 0.2);
    }

    @keyframes pulse {
        0% { transform: scale(1); box-shadow: var(--shadow-md); }
        100% { transform: scale(1.03); box-shadow: var(--shadow-lg); }
    }

    .track-details {
        flex: 1;
        color: var(--color-text-primary);
    }

    .current-track {
        font-size: 1.375rem;
        font-weight: 600;
        margin-bottom: var(--space-1);
        color: var(--color-text-primary);
        font-family: var(--font-primary);
    }

    .current-artist {
        font-size: 1rem;
        color: var(--color-text-secondary);
        font-weight: 500;
    }

    /* Enhanced Progress Bar */
    .progress-container {
        position: relative;
        margin: var(--space-6) 0;
        z-index: 2;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: var(--space-3);
        color: var(--color-text-secondary);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: var(--transition-normal);
    }

    .progress-bar:hover {
        height: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--color-primary) 0%, #60a5fa 100%);
        border-radius: 10px;
        position: relative;
        transition: width 0.3s ease;
    }

    .progress-handle {
        position: absolute;
        right: -6px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        background: white;
        border-radius: 50%;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
        border: 2px solid var(--color-primary);
    }

    .progress-bar:hover .progress-handle {
        transform: translateY(-50%) scale(1.3);
        box-shadow: var(--shadow-md);
    }

    /* Music Controls */
    .music-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-4);
        position: relative;
        z-index: 2;
    }

    .control-btn {
        background: white;
        border: 2px solid var(--color-primary);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-normal);
        color: var(--color-primary);
        font-size: 1.1rem;
        box-shadow: var(--shadow-sm);
    }

    .control-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        background: var(--color-primary);
        color: white;
    }

    .control-btn.play {
        width: 56px;
        height: 56px;
        background: var(--color-primary);
        color: white;
        font-size: 1.3rem;
        border: none;
    }

    .control-btn.play:hover {
        background: #6bd4ff;
        transform: translateY(-2px) scale(1.05);
    }

    /* Volume Control */
    .volume-control {
        display: flex;
        align-items: center;
        gap: var(--space-3);
        margin-left: var(--space-8);
    }

    .volume-icon {
        color: var(--color-text-secondary);
        font-size: 1.1rem;
    }

    .volume-slider {
        width: 100px;
        height: 4px;
        background: #e5e7eb;
        border-radius: 10px;
        appearance: none;
        cursor: pointer;
        transition: var(--transition-normal);
    }

    .volume-slider::-webkit-slider-thumb {
        appearance: none;
        width: 12px;
        height: 12px;
        background: var(--color-primary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-normal);
    }

    .volume-slider::-webkit-slider-thumb:hover {
        transform: scale(1.2);
        box-shadow: var(--shadow-md);
    }

    /* Track Cards - Enhanced */
    .tracks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--space-6);
        margin-bottom: var(--space-12);
    }
    
    .track-card {
        background: white;
        border-radius: 16px;
        padding: var(--space-6);
        border: 1px solid rgba(140, 224, 255, 0.1);
        transition: var(--transition-normal);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    
    .track-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.02) 0%, rgba(140, 224, 255, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .track-card:hover::before {
        opacity: 1;
    }
    
    .track-card:hover {
        transform: translateY(-4px);
        border-color: rgba(140, 224, 255, 0.3);
        box-shadow: var(--shadow-lg);
    }
    
    .track-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: var(--space-4);
        transition: var(--transition-normal);
    }

    .track-card:hover .track-image {
        transform: scale(1.02);
    }
    
    .track-info {
        color: var(--color-text-primary);
        position: relative;
        z-index: 2;
    }
    
    .track-name {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: var(--space-2);
        font-family: var(--font-primary);
        color: var(--color-text-primary);
        line-height: 1.4;
    }
    
    .track-artist {
        font-size: 0.875rem;
        color: var(--color-primary);
        margin-bottom: var(--space-1);
        font-weight: 500;
    }
    
    .track-album {
        font-size: 0.8rem;
        color: var(--color-text-secondary);
        margin-bottom: var(--space-4);
    }

    /* Search Section - Enhanced */
    .search-section {
        background: white;
        border-radius: 16px;
        padding: var(--space-8);
        margin-bottom: var(--space-12);
        border: 1px solid rgba(140, 224, 255, 0.2);
        box-shadow: var(--shadow-md);
    }
    
    .search-input {
        width: 100%;
        background: var(--color-background);
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: var(--space-4) var(--space-6);
        color: var(--color-text-primary);
        font-size: 1rem;
        transition: var(--transition-normal);
        font-family: var(--font-primary);
    }
    
    .search-input:focus {
        outline: none;
        border-color: var(--color-primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(140, 224, 255, 0.1);
    }

    .search-input::placeholder {
        color: var(--color-text-secondary);
    }

    /* Section Headers - Enhanced */
    .section-header {
        color: var(--color-text-primary);
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-family: var(--font-primary);
        font-weight: 700;
        margin: var(--space-16) 0 var(--space-8) 0;
        display: flex;
        align-items: center;
        gap: var(--space-4);
    }

    .section-header span:first-child {
        font-size: 1.3em;
    }

    /* Mock Notice */
    .mock-notice {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        border-radius: 12px;
        padding: var(--space-4);
        margin-bottom: var(--space-8);
        text-align: center;
    }

    .mock-notice-text {
        color: #856404;
        font-size: 0.9rem;
        margin: 0;
        font-weight: 500;
    }

    /* Track Actions */
    .track-actions {
        display: flex;
        gap: var(--space-2);
        margin-top: var(--space-3);
        flex-wrap: wrap;
    }

    .track-btn {
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        padding: var(--space-2) var(--space-3);
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        transition: var(--transition-normal);
        border: 1px solid transparent;
    }

    .btn-spotify {
        background: #1db954;
        color: white;
        border-color: #1db954;
    }

    .btn-spotify:hover {
        background: #1ed760;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .btn-memory {
        background: rgba(140, 224, 255, 0.1);
        color: var(--color-primary);
        border-color: rgba(140, 224, 255, 0.3);
    }

    .btn-memory:hover {
        background: rgba(140, 224, 255, 0.2);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .btn-play {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
        cursor: pointer;
    }

    .btn-play:hover {
        background: #6bd4ff;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .btn-preview {
        background: rgba(156, 163, 175, 0.1);
        color: #6b7280;
        border-color: rgba(156, 163, 175, 0.3);
        cursor: pointer;
    }

    .btn-preview:hover {
        background: rgba(156, 163, 175, 0.2);
        color: #4b5563;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    /* Memory and Event Info */
    .memory-info,
    .event-info {
        font-size: 0.8rem;
        color: var(--color-text-secondary);
        margin-bottom: var(--space-3);
        padding: var(--space-2);
        background: rgba(140, 224, 255, 0.05);
        border-radius: 6px;
        border-left: 3px solid var(--color-primary);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: var(--space-16) var(--space-8);
        color: var(--color-text-secondary);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: var(--space-4);
        opacity: 0.5;
    }

    /* Loading Animation */
    @keyframes loading {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading {
        animation: loading 1s linear infinite;
    }

    /* Spotify Connection Styles */
    .spotify-connect-section,
    .spotify-connected-section {
        margin-bottom: var(--space-8);
    }

    .connect-card,
    .connected-card {
        background: white;
        border-radius: 16px;
        padding: var(--space-8);
        text-align: center;
        border: 2px solid rgba(140, 224, 255, 0.2);
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }

    .connect-card::before,
    .connected-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.02) 0%, rgba(140, 224, 255, 0.05) 100%);
        z-index: 1;
    }

    .connect-card > *,
    .connected-card > * {
        position: relative;
        z-index: 2;
    }

    .connect-icon {
        font-size: 3rem;
        margin-bottom: var(--space-4);
        opacity: 0.8;
    }

    .connect-title {
        color: var(--color-text-primary);
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: var(--space-3);
        font-family: var(--font-primary);
    }

    .connect-description {
        color: var(--color-text-secondary);
        margin-bottom: var(--space-6);
        font-size: 1rem;
        line-height: 1.6;
    }

    .connect-btn {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        background: #1db954;
        color: white;
        padding: var(--space-4) var(--space-6);
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition-normal);
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .connect-btn:hover {
        background: #1ed760;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* Connected Status */
    .connected-card {
        text-align: left;
    }

    .connected-info {
        display: flex;
        align-items: center;
        gap: var(--space-4);
        margin-bottom: var(--space-4);
    }

    .spotify-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(140, 224, 255, 0.3);
    }

    .connected-name {
        color: var(--color-text-primary);
        font-weight: 600;
        font-size: 1.1rem;
    }

    .connected-user {
        color: var(--color-text-secondary);
        font-size: 0.9rem;
    }

    .disconnect-btn {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        border: 1px solid rgba(220, 38, 38, 0.3);
        padding: var(--space-2) var(--space-4);
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition-normal);
    }

    .disconnect-btn:hover {
        background: rgba(220, 38, 38, 0.2);
        transform: translateY(-1px);
    }

    /* Playlist Styles */
    .playlists-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--space-6);
        margin-bottom: var(--space-12);
    }

    .playlist-card {
        background: white;
        border-radius: 16px;
        padding: var(--space-6);
        border: 1px solid rgba(140, 224, 255, 0.1);
        transition: var(--transition-normal);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .playlist-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(140, 224, 255, 0.02) 0%, rgba(140, 224, 255, 0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .playlist-card:hover::before {
        opacity: 1;
    }

    .playlist-card:hover {
        transform: translateY(-4px);
        border-color: rgba(140, 224, 255, 0.3);
        box-shadow: var(--shadow-lg);
    }

    .playlist-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: var(--space-4);
        transition: var(--transition-normal);
    }

    .playlist-card:hover .playlist-image {
        transform: scale(1.02);
    }

    .playlist-info {
        color: var(--color-text-primary);
        position: relative;
        z-index: 2;
    }

    .playlist-name {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: var(--space-2);
        font-family: var(--font-primary);
        color: var(--color-text-primary);
        line-height: 1.4;
    }

    .playlist-description {
        font-size: 0.875rem;
        color: var(--color-text-secondary);
        margin-bottom: var(--space-3);
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .playlist-meta {
        font-size: 0.8rem;
        color: var(--color-text-secondary);
        margin-bottom: var(--space-4);
        display: flex;
        gap: var(--space-2);
        flex-wrap: wrap;
    }

    .playlist-tracks {
        background: rgba(140, 224, 255, 0.1);
        color: var(--color-primary);
        padding: var(--space-1) var(--space-2);
        border-radius: 4px;
        font-weight: 500;
    }

    .playlist-actions {
        display: flex;
        gap: var(--space-2);
        margin-top: var(--space-3);
        flex-wrap: wrap;
    }

    .playlist-btn {
        display: inline-flex;
        align-items: center;
        gap: var(--space-1);
        padding: var(--space-2) var(--space-3);
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        transition: var(--transition-normal);
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-play {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .btn-play:hover {
        background: #6bd4ff;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    /* Playlist Modal */
    .playlist-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: var(--space-4);
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        max-width: 800px;
        width: 100%;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-6);
        border-bottom: 1px solid rgba(140, 224, 255, 0.1);
        background: var(--color-background);
    }

    .modal-header h3 {
        color: var(--color-text-primary);
        font-family: var(--font-primary);
        font-weight: 600;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--color-text-secondary);
        padding: var(--space-2);
        border-radius: 4px;
        transition: var(--transition-normal);
    }

    .modal-close:hover {
        background: rgba(140, 224, 255, 0.1);
        color: var(--color-text-primary);
    }

    .modal-body {
        padding: var(--space-6);
        max-height: calc(80vh - 120px);
        overflow-y: auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .music-container {
            padding: var(--space-4);
        }

        .music-header {
            padding: var(--space-8);
            margin-bottom: var(--space-8);
        }

        .music-player {
            padding: var(--space-6);
        }

        .now-playing {
            flex-direction: column;
            text-align: center;
            gap: var(--space-4);
        }

        .music-controls {
            flex-wrap: wrap;
            gap: var(--space-3);
            justify-content: center;
        }

        .volume-control {
            margin-left: 0;
            margin-top: var(--space-4);
            width: 100%;
            justify-content: center;
        }

        .tracks-grid {
            grid-template-columns: 1fr;
            gap: var(--space-4);
        }

        .search-section {
            padding: var(--space-6);
        }

        .section-header {
            margin: var(--space-12) 0 var(--space-6) 0;
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .music-title {
            font-size: 1.75rem;
        }

        .music-subtitle {
            font-size: 1rem;
        }

        .control-btn {
            width: 44px;
            height: 44px;
            font-size: 1rem;
        }

        .control-btn.play {
            width: 52px;
            height: 52px;
            font-size: 1.2rem;
        }

        .track-card {
            padding: var(--space-4);
        }

        .track-image {
            height: 160px;
        }
    }


</style>
@endpush

@section('content')
<div class="music-container">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="music-header">
        <h1 class="music-title">🎵 Our Playlist</h1>
        <p class="music-subtitle">For everything I couldn't say, I made a playlist instead.</p>
    </div>

    <!-- Enhanced Music Player -->
    <div class="music-player">
        <div class="now-playing">
            <div class="album-art">
                🎵
            </div>
            <div class="track-details">
                <div class="current-track" id="currentTrack">Perfect</div>
                <div class="current-artist" id="currentArtist">Ed Sheeran</div>
            </div>
        </div>

        <div class="progress-container">
            <div class="progress-info">
                <span id="currentTime">2:15</span>
                <span id="totalTime">4:22</span>
            </div>
            <div class="progress-bar" id="progressBar">
                <div class="progress-fill" id="progressFill" style="width: 35%;">
                    <div class="progress-handle"></div>
                </div>
            </div>
        </div>

        <div class="music-controls">
            <button class="control-btn" id="prevBtn">⏮️</button>
            <button class="control-btn play" id="playBtn">▶️</button>
            <button class="control-btn" id="nextBtn">⏭️</button>
            <button class="control-btn" id="shuffleBtn">🔀</button>
            <button class="control-btn" id="repeatBtn">🔁</button>
            
            <div class="volume-control">
                <span class="volume-icon">🔊</span>
                <input type="range" class="volume-slider" id="volumeSlider" min="0" max="100" value="75">
            </div>
        </div>
    </div>

    <!-- Spotify Connection Status -->
    @if(!$hasSpotifyConnection)
        <div class="spotify-connect-section">
            <div class="connect-card">
                <div class="connect-icon">🎵</div>
                <h3 class="connect-title">Connect to Spotify</h3>
                <p class="connect-description">
                    Connect your Spotify account to play music directly from your playlists and control playback.
                </p>
                <a href="{{ route('spotify.connect') }}" class="connect-btn">
                    <span>🎧</span>
                    <span>Connect Spotify Account</span>
                </a>
            </div>
        </div>
    @else
        <!-- Connected - Show User Info -->
        <div class="spotify-connected-section">
            <div class="connected-card">
                <div class="connected-info">
                    <img src="{{ auth()->user()->getSpotifyProfileImage() ?? 'https://via.placeholder.com/50x50?text=🎵' }}" 
                         alt="Spotify Profile" class="spotify-avatar">
                    <div class="connected-details">
                        <div class="connected-name">Connected to Spotify</div>
                        <div class="connected-user">{{ auth()->user()->getSpotifyDisplayName() }}</div>
                    </div>
                </div>
                <form action="{{ route('spotify.disconnect') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="disconnect-btn">Disconnect</button>
                </form>
            </div>
        </div>
    @endif

    @if($isMockMode)
    <!-- Mock Notice -->
    <div class="mock-notice">
        <p class="mock-notice-text">
            🎧 Demo Mode: Menampilkan data contoh. API Spotify akan aktif saat hosting.
        </p>
    </div>
    @endif

    <!-- Search Section -->
    <div class="search-section">
        <input type="text" 
               class="search-input" 
               placeholder="🔍 Search for songs, artists, or albums..."
               id="musicSearch">
        <div class="search-results" id="searchResults" style="display: none;">
            <div class="tracks-grid" id="searchGrid"></div>
        </div>
    </div>

    <!-- User Playlists -->
    @if($hasSpotifyConnection && count($userPlaylists) > 0)
        <h2 class="section-header">
            <span>🎵</span>
            <span>Your Spotify Playlists</span>
        </h2>
        <div class="playlists-grid">
            @foreach($userPlaylists as $playlist)
                <div class="playlist-card" onclick="loadPlaylist('{{ $playlist['id'] }}', '{{ $playlist['name'] }}')">
                    <img src="{{ $playlist['image'] ?? 'https://via.placeholder.com/300x300?text=Playlist' }}" 
                         alt="{{ $playlist['name'] }}" 
                         class="playlist-image">
                    <div class="playlist-info">
                        <div class="playlist-name">{{ $playlist['name'] }}</div>
                        <div class="playlist-description">{{ $playlist['description'] ?: 'No description' }}</div>
                        <div class="playlist-meta">
                            <span class="playlist-tracks">{{ $playlist['tracks_total'] }} tracks</span>
                            <span class="playlist-owner">by {{ $playlist['owner'] }}</span>
                        </div>
                        
                        <div class="playlist-actions">
                            <button class="playlist-btn btn-play" onclick="playPlaylist('{{ $playlist['id'] }}', event)">
                                <span>▶️</span>
                                <span>Play</span>
                            </button>
                            @if($playlist['external_url'])
                                <a href="{{ $playlist['external_url'] }}" 
                                   target="_blank" 
                                   class="playlist-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Playlist Tracks Modal -->
        <div id="playlistModal" class="playlist-modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="playlistModalTitle">Playlist Tracks</h3>
                    <button class="modal-close" onclick="closePlaylistModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="tracks-grid" id="playlistTracks"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Memory Tracks -->
    @if($memoryTracks->count() > 0)
        <h2 class="section-header">
            <span>💭</span>
            <span>Soundtrack of Our Memories</span>
        </h2>
        <div class="tracks-grid">
            @foreach($memoryTracks as $track)
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        @if(isset($track['memory']))
                            <div class="memory-info">
                                💭 From memory: {{ $track['memory']->memory_date->format('d M Y') }}
                            </div>
                        @endif
                        
                        <div class="track-actions">
                            @if($hasSpotifyConnection)
                                <button class="track-btn btn-play" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event)">
                                    <span>▶️</span>
                                    <span>Play Now</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['memory']))
                                <a href="{{ route('memories.show', $track['memory']->id) }}" 
                                   class="track-btn btn-memory"
                                   onclick="event.stopPropagation()">
                                    <span>👁️</span>
                                    <span>View Memory</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Event Tracks -->
    @if($eventTracks->count() > 0)
        <h2 class="section-header">
            <span>📅</span>
            <span>Event Soundtracks</span>
        </h2>
        <div class="tracks-grid">
            @foreach($eventTracks as $track)
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        @if(isset($track['event']))
                            <div class="event-info">
                                📅 From event: {{ $track['event']->title }}
                            </div>
                        @endif
                        
                        <div class="track-actions">
                            @if($hasSpotifyConnection)
                                <button class="track-btn btn-play" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event)">
                                    <span>▶️</span>
                                    <span>Play Now</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                            
                            @if(isset($track['event']))
                                <a href="{{ route('events.show', $track['event']->id) }}" 
                                   class="track-btn btn-memory"
                                   onclick="event.stopPropagation()">
                                    <span>👁️</span>
                                    <span>View Event</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Recommended Tracks -->
    <h2 class="section-header">
        <span>🎶</span>
        <span>Recommended for You</span>
    </h2>
    @if(count($recommendedTracks) > 0)
        <div class="tracks-grid">
            @foreach($recommendedTracks as $track)
                <div class="track-card" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', null, '{{ $track['preview_url'] ?? '' }}')">
                    <img src="{{ $track['image'] ?? 'https://via.placeholder.com/300x300?text=No+Image' }}" 
                         alt="{{ $track['name'] }}" 
                         class="track-image">
                    <div class="track-info">
                        <div class="track-name">{{ $track['name'] }}</div>
                        <div class="track-artist">{{ $track['artist'] }}</div>
                        <div class="track-album">{{ $track['album'] }}</div>
                        
                        <div class="track-actions">
                            @if($hasSpotifyConnection)
                                <button class="track-btn btn-play" onclick="playSpotifyTrack('{{ $track['id'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}', event, '{{ $track['preview_url'] ?? '' }}')">
                                    <span>▶️</span>
                                    <span>Play Now</span>
                                </button>
                            @endif
                            
                            @if($track['preview_url'])
                                <button class="track-btn btn-preview" onclick="playPreviewAudio('{{ $track['preview_url'] }}', '{{ $track['name'] }}', '{{ $track['artist'] }}'); event.stopPropagation()">
                                    <span>🎧</span>
                                    <span>Preview</span>
                                </button>
                            @endif
                            
                            @if($track['external_url'])
                                <a href="{{ $track['external_url'] }}" 
                                   target="_blank" 
                                   class="track-btn btn-spotify"
                                   onclick="event.stopPropagation()">
                                    <span>🎵</span>
                                    <span>Open in Spotify</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🎵</div>
            <p>No tracks available at the moment</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('musicSearch');
    const searchResults = document.getElementById('searchResults');
    const searchGrid = document.getElementById('searchGrid');
    let searchTimeout;

    // Player controls
    const playBtn = document.getElementById('playBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    const volumeSlider = document.getElementById('volumeSlider');
    
    let isPlaying = false;
    let currentProgress = 35;

    // Search functionality
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchMusic(query);
        }, 500);
    });

    // Player controls
    playBtn.addEventListener('click', function() {
        isPlaying = !isPlaying;
        this.innerHTML = isPlaying ? '⏸️' : '▶️';
        
        if (isPlaying) {
            startProgressAnimation();
        } else {
            stopProgressAnimation();
        }
    });

    // Progress bar interaction
    progressBar.addEventListener('click', function(e) {
        const rect = this.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const percentage = (clickX / rect.width) * 100;
        
        progressFill.style.width = percentage + '%';
        currentProgress = percentage;
        
        // Update time display
        updateTimeDisplay(percentage);
    });

    // Volume control
    volumeSlider.addEventListener('input', function() {
        const volume = this.value;
        // Here you would control actual volume
        console.log('Volume set to:', volume);
    });

    function startProgressAnimation() {
        // Simulate progress animation
        const interval = setInterval(() => {
            if (!isPlaying) {
                clearInterval(interval);
                return;
            }
            
            currentProgress += 0.1;
            if (currentProgress >= 100) {
                currentProgress = 0;
                isPlaying = false;
                playBtn.innerHTML = '▶️';
                clearInterval(interval);
            }
            
            progressFill.style.width = currentProgress + '%';
            updateTimeDisplay(currentProgress);
        }, 100);
    }

    function stopProgressAnimation() {
        // Animation stopped by changing isPlaying flag
    }

    function updateTimeDisplay(percentage) {
        const totalSeconds = 262; // 4:22 in seconds
        const currentSeconds = Math.floor((percentage / 100) * totalSeconds);
        
        const minutes = Math.floor(currentSeconds / 60);
        const seconds = currentSeconds % 60;
        
        document.getElementById('currentTime').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    // Global function for track selection
    window.playTrack = function(trackName, artistName) {
        document.getElementById('currentTrack').textContent = trackName;
        document.getElementById('currentArtist').textContent = artistName;
        
        // Reset progress
        currentProgress = 0;
        progressFill.style.width = '0%';
        updateTimeDisplay(0);
        
        // Start playing
        isPlaying = true;
        playBtn.innerHTML = '⏸️';
        startProgressAnimation();
    };

    async function searchMusic(query) {
        try {
            const response = await fetch(`{{ route('music.search') }}?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            displaySearchResults(data.tracks || []);
        } catch (error) {
            console.error('Search failed:', error);
        }
    }

    function displaySearchResults(tracks) {
        if (tracks.length === 0) {
            searchResults.style.display = 'none';
            return;
        }

        searchGrid.innerHTML = tracks.map(track => `
            <div class="track-card" onclick="playSpotifyTrack('${track.id}', '${track.name}', '${track.artist}', null, '${track.preview_url || ''}')">
                <img src="${track.image || 'https://via.placeholder.com/300x300?text=No+Image'}" 
                     alt="${track.name}" 
                     class="track-image">
                <div class="track-info">
                    <div class="track-name">${track.name}</div>
                    <div class="track-artist">${track.artist}</div>
                    <div class="track-album">${track.album}</div>
                    
                    <div class="track-actions">
                        <button class="track-btn btn-play" onclick="playSpotifyTrack('${track.id}', '${track.name}', '${track.artist}', event, '${track.preview_url || ''}')">
                            <span>▶️</span>
                            <span>Play</span>
                        </button>
                        ${track.preview_url ? `
                            <button class="track-btn btn-preview" onclick="playPreviewAudio('${track.preview_url}', '${track.name}', '${track.artist}'); event.stopPropagation()">
                                <span>🎧</span>
                                <span>Preview</span>
                            </button>
                        ` : ''}
                        ${track.external_url ? `
                            <a href="${track.external_url}" 
                               target="_blank" 
                               class="track-btn btn-spotify"
                               onclick="event.stopPropagation()">
                                <span>🎵</span>
                                <span>Open in Spotify</span>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        searchResults.style.display = 'block';
    }

    // Spotify Integration Functions
    window.loadPlaylist = function(playlistId, playlistName) {
        document.getElementById('playlistModalTitle').textContent = playlistName;
        
        fetch(`{{ route('music.playlist.tracks', ':playlistId') }}`.replace(':playlistId', playlistId))
            .then(response => response.json())
            .then(data => {
                displayPlaylistTracks(data.tracks);
                document.getElementById('playlistModal').style.display = 'flex';
            })
            .catch(error => {
                console.error('Failed to load playlist tracks:', error);
                alert('Failed to load playlist tracks');
            });
    };

    window.playPlaylist = function(playlistId, event) {
        event.stopPropagation();
        
        const spotifyUri = `spotify:playlist:${playlistId}`;
        
        fetch('{{ route('music.play') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                context_uri: spotifyUri
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('🎵 Playing playlist on Spotify!', 'success');
                updateCurrentPlayback();
            } else {
                showNotification('❌ Failed to play playlist. Make sure Spotify is open.', 'error');
            }
        })
        .catch(error => {
            console.error('Playback failed:', error);
            showNotification('❌ Failed to start playback', 'error');
        });
    };

    window.playSpotifyTrack = function(trackId, trackName, artistName, event, previewUrl = null) {
        // Update UI immediately
        document.getElementById('currentTrack').textContent = trackName;
        document.getElementById('currentArtist').textContent = artistName;
        
        const spotifyUri = `spotify:track:${trackId}`;
        
        fetch('{{ route('music.play') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                track_uri: spotifyUri
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('🎵 Playing on Spotify!', 'success');
                isPlaying = true;
                playBtn.innerHTML = '⏸️';
                startProgressAnimation();
                updateCurrentPlayback();
            } else {
                handlePlaybackError(data, trackName, artistName, previewUrl);
            }
        })
        .catch(error => {
            console.error('Playback failed:', error);
            showNotification('❌ Failed to start playback', 'error');
            // Try preview URL as fallback
            if (previewUrl) {
                playPreviewAudio(previewUrl, trackName, artistName);
            }
        });
    };
    
    function handlePlaybackError(data, trackName, artistName, previewUrl) {
        const errorCode = data.error_code;
        let message = data.error || 'Failed to play track';
        let showPreviewOption = false;
        
        switch(errorCode) {
            case 'NO_ACTIVE_DEVICE':
                message = '📱 No active Spotify device found. Please open Spotify on any device first.';
                showPreviewOption = true;
                break;
            case 'PREMIUM_REQUIRED':
                message = '💎 Spotify Premium is required for playback control.';
                showPreviewOption = true;
                break;
            case 'DEVICE_NOT_FOUND':
                message = '🔍 Spotify device not found. Please ensure Spotify is running.';
                showPreviewOption = true;
                break;
            default:
                message = '❌ ' + message;
                showPreviewOption = true;
        }
        
        showNotification(message, 'error');
        
        // Auto-fallback to preview if available
        if (showPreviewOption && previewUrl) {
            setTimeout(() => {
                showNotification('🎧 Playing 30-second preview instead...', 'info');
                playPreviewAudio(previewUrl, trackName, artistName);
            }, 2000);
        }
    }
    
    function playPreviewAudio(previewUrl, trackName, artistName) {
        // Stop any currently playing preview
        if (window.currentPreviewAudio) {
            window.currentPreviewAudio.pause();
            window.currentPreviewAudio = null;
        }
        
        if (!previewUrl) {
            showNotification('❌ No preview available for this track', 'error');
            return;
        }
        
        const audio = new Audio(previewUrl);
        window.currentPreviewAudio = audio;
        
        audio.play().then(() => {
            showNotification(`🎧 Playing preview: ${trackName}`, 'success');
            isPlaying = true;
            playBtn.innerHTML = '⏸️';
            
            // Update UI
            document.getElementById('currentTrack').textContent = trackName + ' (Preview)';
            document.getElementById('currentArtist').textContent = artistName;
            
            // Handle audio end
            audio.addEventListener('ended', () => {
                isPlaying = false;
                playBtn.innerHTML = '▶️';
                showNotification('🎧 Preview ended', 'info');
            });
            
        }).catch(error => {
            console.error('Preview playback failed:', error);
            showNotification('❌ Failed to play preview', 'error');
        });
    }

    window.closePlaylistModal = function() {
        document.getElementById('playlistModal').style.display = 'none';
    };

    function displayPlaylistTracks(tracks) {
        const playlistTracksContainer = document.getElementById('playlistTracks');
        
        playlistTracksContainer.innerHTML = tracks.map((track, index) => `
            <div class="track-card" onclick="playPlaylistTrack('${track.id}', ${index}, '${track.name}', '${track.artist}')">
                <img src="${track.image || 'https://via.placeholder.com/300x300?text=No+Image'}" 
                     alt="${track.name}" 
                     class="track-image">
                <div class="track-info">
                    <div class="track-name">${track.name}</div>
                    <div class="track-artist">${track.artist}</div>
                    <div class="track-album">${track.album}</div>
                    
                    <div class="track-actions">
                        <button class="track-btn btn-play" onclick="playSpotifyTrack('${track.id}', '${track.name}', '${track.artist}', event, '${track.preview_url || ''}')">
                            <span>▶️</span>
                            <span>Play</span>
                        </button>
                        ${track.preview_url ? `
                            <button class="track-btn btn-preview" onclick="playPreviewAudio('${track.preview_url}', '${track.name}', '${track.artist}'); event.stopPropagation()">
                                <span>🎧</span>
                                <span>Preview</span>
                            </button>
                        ` : ''}
                        ${track.external_url ? `
                            <a href="${track.external_url}" 
                               target="_blank" 
                               class="track-btn btn-spotify"
                               onclick="event.stopPropagation()">
                                <span>🎵</span>
                                <span>Open in Spotify</span>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');
    }

    function updateCurrentPlayback() {
        fetch('{{ route('music.current-playback') }}')
            .then(response => response.json())
            .then(data => {
                if (data.playback && data.playback.track) {
                    const track = data.playback.track;
                    document.getElementById('currentTrack').textContent = track.name;
                    document.getElementById('currentArtist').textContent = track.artist;
                    
                    // Update play/pause button
                    isPlaying = data.playback.is_playing;
                    playBtn.innerHTML = isPlaying ? '⏸️' : '▶️';
                    
                    // Update progress
                    if (track.duration_ms > 0) {
                        const progressPercent = (data.playback.progress_ms / track.duration_ms) * 100;
                        progressFill.style.width = progressPercent + '%';
                        updateTimeDisplay(progressPercent);
                    }
                }
            })
            .catch(error => {
                console.error('Failed to update playback:', error);
            });
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'info' ? '#3b82f6' : '#6b7280',
            color: 'white',
            padding: '12px 20px',
            borderRadius: '8px',
            zIndex: '10000',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            maxWidth: '300px',
            fontSize: '14px',
            fontWeight: '500'
        });
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Enhanced player controls for Spotify
    const originalPlayBtnClick = playBtn.onclick;
    playBtn.onclick = function() {
        if ({{ $hasSpotifyConnection ? 'true' : 'false' }}) {
            // Use Spotify API
            const endpoint = isPlaying ? '{{ route('music.pause') }}' : '{{ route('music.play') }}';
            
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    isPlaying = !isPlaying;
                    this.innerHTML = isPlaying ? '⏸️' : '▶️';
                    
                    if (isPlaying) {
                        startProgressAnimation();
                    }
                } else {
                    showNotification('❌ Playback control failed', 'error');
                }
            })
            .catch(error => {
                console.error('Playback control failed:', error);
                showNotification('❌ Playback control failed', 'error');
            });
        } else {
            // Use original mock functionality
            originalPlayBtnClick.call(this);
        }
    };

    // Update playback status periodically if connected
    if ({{ $hasSpotifyConnection ? 'true' : 'false' }}) {
        setInterval(updateCurrentPlayback, 10000); // Update every 10 seconds
        updateCurrentPlayback(); // Initial update
    }
});

// Spotify Web Playback SDK Implementation
let spotifyPlayer = null;
let spotifyDeviceId = null;

// Check if Spotify SDK is loaded
console.log('Checking Spotify SDK availability...');
console.log('window.Spotify:', typeof window.Spotify);
console.log('window.onSpotifyWebPlaybackSDKReady defined:', typeof window.onSpotifyWebPlaybackSDKReady);

window.onSpotifyWebPlaybackSDKReady = () => {
    console.log('Spotify Web Playback SDK Ready!');
    const token = '{{ $spotifyAccessToken ?? "" }}';
    
    console.log('Spotify token available:', token ? 'Yes' : 'No');
    console.log('Has Spotify connection:', {{ $hasSpotifyConnection ? 'true' : 'false' }});
    console.log('Spotify access token from controller:', token ? token.substring(0, 20) + '...' : 'null');
    
    if (!token) {
        console.log('No Spotify access token available - using mock mode');
        showNotification('ℹ️ Connect to Spotify for full playback control', 'info');
        return;
    }
    
    spotifyPlayer = new Spotify.Player({
        name: 'Khairun Web Player',
        getOAuthToken: cb => { cb(token); },
        volume: 0.5
    });
    
    // Error handling
    spotifyPlayer.addListener('initialization_error', ({ message }) => {
        console.error('Spotify Player initialization error:', message);
    });
    
    spotifyPlayer.addListener('authentication_error', ({ message }) => {
        console.error('Spotify Player authentication error:', message);
        showNotification('❌ Spotify authentication failed. Please reconnect.', 'error');
    });
    
    spotifyPlayer.addListener('account_error', ({ message }) => {
        console.error('Spotify Player account error:', message);
        showNotification('💎 Spotify Premium is required for playback control.', 'error');
    });
    
    spotifyPlayer.addListener('playback_error', ({ message }) => {
        console.error('Spotify Player playback error:', message);
        showNotification('❌ Playback error: ' + message, 'error');
    });
    
    // Playback status updates
    spotifyPlayer.addListener('player_state_changed', (state) => {
        if (!state) return;
        
        const track = state.track_window.current_track;
        if (track) {
            document.getElementById('currentTrack').textContent = track.name;
            document.getElementById('currentArtist').textContent = track.artists[0].name;
            
            // Update play/pause button
            isPlaying = !state.paused;
            playBtn.innerHTML = isPlaying ? '⏸️' : '▶️';
            
            // Update progress
            const progressPercent = (state.position / state.duration) * 100;
            progressFill.style.width = progressPercent + '%';
            updateTimeDisplay(progressPercent);
            
            if (isPlaying) {
                startProgressAnimation();
            }
        }
    });
    
    // Ready
    spotifyPlayer.addListener('ready', ({ device_id }) => {
        console.log('Spotify Web Player ready with Device ID:', device_id);
        spotifyDeviceId = device_id;
        showNotification('🎵 Khairun Web Player is ready!', 'success');
        
        // Transfer playback to this device
        transferPlaybackToWebPlayer(device_id);
    });
    
    // Not Ready
    spotifyPlayer.addListener('not_ready', ({ device_id }) => {
        console.log('Spotify Web Player not ready with Device ID:', device_id);
    });
    
    // Connect to the player!
    console.log('Attempting to connect to Spotify Web Player...');
    spotifyPlayer.connect().then(success => {
        if (success) {
            console.log('Successfully connected to Spotify Web Player');
            showNotification('🔗 Connected to Spotify Web Player', 'success');
        } else {
            console.error('Failed to connect to Spotify Web Player');
            showNotification('❌ Failed to connect to Spotify Web Player', 'error');
        }
    }).catch(error => {
        console.error('Spotify Web Player connection error:', error);
        showNotification('❌ Spotify connection error: ' + error.message, 'error');
    });
};

// Transfer playback to web player
function transferPlaybackToWebPlayer(deviceId) {
    fetch('{{ route('music.transfer-playback') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            device_id: deviceId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Playback transferred to web player');
        } else {
            console.log('Failed to transfer playback:', data.error);
        }
    })
    .catch(error => {
        console.error('Transfer playback failed:', error);
    });
}

// Enhanced playSpotifyTrack function with device ID
const originalPlaySpotifyTrack = window.playSpotifyTrack;
window.playSpotifyTrack = function(trackId, trackName, artistName, event, previewUrl = null) {
    // If we have a web player device, use it
    if (spotifyDeviceId) {
        const spotifyUri = `spotify:track:${trackId}`;
        
        fetch('{{ route('music.play') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                track_uri: spotifyUri,
                device_id: spotifyDeviceId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('🎵 Playing on Khairun Web Player!', 'success');
                // Player state will be updated via player_state_changed listener
            } else {
                handlePlaybackError(data, trackName, artistName, previewUrl);
            }
        })
        .catch(error => {
            console.error('Playback failed:', error);
            showNotification('❌ Failed to start playback', 'error');
            if (previewUrl) {
                playPreviewAudio(previewUrl, trackName, artistName);
            }
        });
    } else {
        // Fallback to original function
        originalPlaySpotifyTrack(trackId, trackName, artistName, event, previewUrl);
    }
};

// Fallback: Check if SDK is ready after 3 seconds
setTimeout(() => {
    if (typeof window.Spotify !== 'undefined' && !spotifyPlayer) {
        console.log('Spotify SDK loaded but callback not triggered, manually initializing...');
        window.onSpotifyWebPlaybackSDKReady();
    } else if (typeof window.Spotify === 'undefined') {
        console.log('Spotify SDK not loaded after 3 seconds');
        showNotification('⚠️ Spotify SDK loading issue. Please refresh the page.', 'error');
    }
}, 3000);

</script>
@endpush