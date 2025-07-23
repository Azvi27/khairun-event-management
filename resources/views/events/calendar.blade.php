@extends('layouts.khairun')

@section('title', 'Calendar - Our Memories')

@push('styles')
<style>
    /* Calendar Specific Styles */
    .calendar-layout {
        display: flex;
        gap: 2vw;
        margin-bottom: 3vh;
    }

    .calendar-section {
        flex: 2;
    }

    .event-section {
        flex: 1;
    }

    .content-section {
        background: #343646;
        border-radius: 2.3vh;
        padding: 2.2vh 3.1vw;
        position: relative;
    }

    .section-title {
        color: #D3D3D9;
        font-size: clamp(20px, 1.8vw, 26px);
        font-family: 'DM Serif Text', serif;
        font-weight: 600;
        margin-bottom: 2.5vh;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* Calendar Navigation */
    .calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2vh;
    }

    .nav-btn {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        border: none;
        border-radius: 50%;
        width: 3vw;
        height: 3vw;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: clamp(18px, 1.4vw, 24px);
        color: #181A26;
        font-weight: bold;
        min-width: 45px;
        min-height: 45px;
        box-shadow: 0 3px 10px rgba(140, 224, 255, 0.3);
    }

    .nav-btn:hover {
        background: linear-gradient(135deg, #6bd4ff 0%, #4ac3f7 100%);
        transform: scale(1.15);
        box-shadow: 0 5px 15px rgba(140, 224, 255, 0.5);
    }

    .month-year-selector {
        display: flex;
        align-items: center;
        gap: 1vw;
    }

    .month-select, .year-select {
        background: #181A26;
        color: #D3D3D9;
        border: 2px solid #8CE0FF;
        border-radius: 1.2vh;
        padding: 1vh 1.5vw;
        font-size: clamp(16px, 1.1vw, 20px);
        font-family: 'DM Serif Text', serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(140, 224, 255, 0.2);
    }

    .month-select:hover, .year-select:hover {
        background: #343646;
        border-color: #6bd4ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.4);
    }

    .current-month-year {
        color: #8CE0FF;
        font-size: clamp(20px, 1.6vw, 28px);
        font-family: 'DM Serif Text', serif;
        font-weight: 700;
        min-width: 15vw;
        text-align: center;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    /* Calendar Grid */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5vw;
        margin-top: 2vh;
    }

    .day-header {
        color: #8CE0FF;
        font-size: clamp(16px, 1.1vw, 20px);
        font-weight: 700;
        text-align: center;
        padding: 1.5vh;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .calendar-date {
        color: #D4D4D4;
        font-size: clamp(16px, 1.1vw, 20px);
        font-weight: 500;
        text-align: center;
        padding: 1.8vh;
        height: 4.5vw;
        min-height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.8vh;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        border: 1px solid transparent;
    }

    .calendar-date:hover {
        background: rgba(140, 224, 255, 0.2);
        border-color: #8CE0FF;
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.3);
    }

    .calendar-date.today {
        background: linear-gradient(135deg, rgba(140, 224, 255, 0.4) 0%, rgba(140, 224, 255, 0.2) 100%);
        border: 2px solid #8CE0FF;
        font-weight: 700;
        color: #FFFFFF;
        box-shadow: 0 4px 15px rgba(140, 224, 255, 0.4);
    }

    .calendar-date.other-month {
        color: #6D718D;
        opacity: 0.5;
    }

    .calendar-date.has-event::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        width: 5px;
        height: 5px;
        background: #8CE0FF;
        border-radius: 50%;
    }

    /* Quick Navigation */
    .quick-nav {
        display: flex;
        gap: 1vw;
        margin-top: 1vh;
        justify-content: center;
        flex-wrap: wrap;
    }

    .quick-nav-btn {
        background: transparent;
        border: 2px solid #8CE0FF;
        color: #8CE0FF;
        padding: 1vh 1.8vw;
        border-radius: 1.5vh;
        cursor: pointer;
        font-size: clamp(14px, 0.9vw, 16px);
        font-weight: 600;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quick-nav-btn:hover {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        color: #181A26;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.4);
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 2.2vh;
    }

    .form-label {
        color: #D3D3D9;
        font-size: clamp(16px, 1.1vw, 20px);
        font-weight: 600;
        margin-bottom: 0.8vh;
        display: block;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 1.4vh 1.2vw;
        border-radius: 1.2vh;
        border: 2px solid #4A4D63;
        background: rgba(24, 26, 38, 0.5);
        color: #D3D3D9;
        font-size: clamp(15px, 1vw, 18px);
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .form-input:focus {
        outline: none;
        border-color: #8CE0FF;
        background: rgba(24, 26, 38, 0.8);
        box-shadow: 0 0 15px rgba(140, 224, 255, 0.3);
        transform: translateY(-2px);
    }

    .form-row {
        display: flex;
        gap: 1vw;
    }

    .form-textarea {
        resize: vertical;
        min-height: 10vh;
    }

    .add-btn {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        border-radius: 2.5vh;
        padding: 1.5vh 3vw;
        color: #181A26;
        font-size: clamp(16px, 1.1vw, 20px);
        font-weight: 700;
        border: none;
        float: right;
        margin-top: 1.5vh;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(140, 224, 255, 0.4);
    }

    .add-btn:hover {
        background: linear-gradient(135deg, #6bd4ff 0%, #4ac3f7 100%);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(140, 224, 255, 0.6);
    }

    /* Events List */
    .events-list {
        background: #343646;
        border-radius: 2.3vh;
        padding: 2.2vh 3.1vw;
        margin-top: 3vh;
    }

    .events-title {
        color: #D3D3D9;
        font-size: clamp(18px, 1.4vw, 24px);
        font-family: 'DM Serif Text', serif;
        margin-bottom: 2vh;
        text-align: center;
    }

    .event-item {
        border-bottom: 1px solid #4A4D63;
        padding: 1.5vh 0;
        display: flex;
        align-items: center;
        gap: 1vw;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .event-item:hover {
        transform: translateX(5px);
    }

    .event-item:last-child {
        border-bottom: none;
    }

    .event-date-display {
        color: #8CE0FF;
        font-size: clamp(14px, 0.95vw, 18px);
        font-family: 'DM Serif Text', serif;
        min-width: 8vw;
        font-weight: 600;
    }

    .event-time {
        color: #D4D4D4;
        font-size: clamp(12px, 0.8vw, 15px);
        min-width: 4vw;
    }

    .event-separator {
        width: 3px;
        height: 2vh;
        background: #8CE0FF;
        border-radius: 2px;
    }

    .event-name-display {
        color: #D3D3D9;
        font-size: clamp(13px, 0.85vw, 16px);
        flex: 1;
    }

    .event-actions {
        display: flex;
        gap: 0.5vw;
    }

    .event-action-btn {
        color: #8CE0FF;
        font-size: 0.8em;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .event-action-btn:hover {
        color: #6bd4ff;
    }

    .form-row {
        display: flex;
        gap: 10px;
    }

    .form-row .form-input {
        flex: 1;
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .calendar-layout {
            flex-direction: column;
        }

        .calendar-section, .event-section {
            flex: 1;
        }
    }

    /* Sharing Options */
    .sharing-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 10px;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 1.5vh 1.2vw;
        background: rgba(211, 211, 217, 0.1);
        border-radius: 1.2vh;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkbox-item:hover {
        background: rgba(140, 224, 255, 0.15);
        border-color: rgba(140, 224, 255, 0.3);
        transform: translateX(5px);
    }

    .checkbox-input {
        width: 20px;
        height: 20px;
        accent-color: #8CE0FF;
        cursor: pointer;
        transform: scale(1.2);
    }

    .checkbox-label {
        color: #D3D3D9;
        font-size: clamp(15px, 1vw, 18px);
        font-weight: 500;
        cursor: pointer;
        flex: 1;
        letter-spacing: 0.3px;
    }

    .creator-badge {
        background: #8CE0FF;
        color: #181A26;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 500;
        margin-left: 8px;
    }

    /* 🚀 ENHANCED CALENDAR STYLES */
    
    /* Calendar Header Enhancement */
    .calendar-header-enhanced {
        background: linear-gradient(135deg, #181A26 0%, #262840 100%);
        border-radius: 1.5rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        border: 2px solid rgba(140, 224, 255, 0.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15), 0 4px 15px rgba(140, 224, 255, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    /* View Mode Switcher */
    .view-mode-switcher {
        display: flex;
        gap: 0.5rem;
        background: rgba(52, 54, 70, 0.8);
        padding: 0.5rem;
        border-radius: 1rem;
        border: 1px solid rgba(140, 224, 255, 0.2);
    }

    .view-mode-btn {
        background: transparent;
        border: none;
        color: #D3D3D9;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        letter-spacing: 0.3px;
    }

    .view-mode-btn:hover {
        background: rgba(140, 224, 255, 0.1);
        color: #8CE0FF;
        transform: translateY(-1px);
    }

    .view-mode-btn.active {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        color: #181A26;
        box-shadow: 0 4px 12px rgba(140, 224, 255, 0.3);
        font-weight: 700;
    }

    /* Calendar Actions */
    .calendar-actions {
        display: flex;
        gap: 1rem;
    }

    .action-btn {
        background: linear-gradient(135deg, #262840 0%, #343646 100%);
        border: 2px solid rgba(140, 224, 255, 0.2);
        color: #8CE0FF;
        padding: 0.75rem 1.5rem;
        border-radius: 1rem;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%);
        color: #181A26;
        border-color: #8CE0FF;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(140, 224, 255, 0.3);
    }

    /* Enhanced Event Color Coding */
    .event-type-general {
        background-color: #8CE0FF !important;
        border-color: #6bd4ff !important;
    }
    
    .event-type-cycle {
        background-color: #FF6B6B !important;
        border-color: #ff5252 !important;
    }
    
    .event-type-birthday {
        background-color: #FFD93D !important;
        border-color: #ffc107 !important;
    }

    /* Enhanced Calendar Date with Multiple Events */
    .calendar-date.has-event {
        position: relative;
        border: 2px solid transparent;
    }

    .calendar-date.has-event::before {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        background: #8CE0FF;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(140, 224, 255, 0.6);
        animation: eventPulse 2s ease-in-out infinite;
    }

    @keyframes eventPulse {
        0%, 100% { 
            opacity: 0.7; 
            transform: scale(1); 
        }
        50% { 
            opacity: 1; 
            transform: scale(1.2); 
        }
    }

    /* Event Indicators for Multiple Events */
    .calendar-date .event-indicators {
        position: absolute;
        bottom: 2px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 1px;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 90%;
    }

    .event-indicator {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #8CE0FF;
    }

    .event-indicator.type-cycle {
        background: #FF6B6B;
    }

    .event-indicator.type-birthday {
        background: #FFD93D;
    }

    .event-indicator.type-event {
        background: #8CE0FF;
    }

    /* Hover Effects & Tooltips */
    .calendar-date:hover {
        background: linear-gradient(135deg, rgba(140, 224, 255, 0.15) 0%, rgba(140, 224, 255, 0.05) 100%);
        border-color: rgba(140, 224, 255, 0.4);
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(140, 224, 255, 0.2);
        z-index: 5;
    }

    /* Event Tooltip */
    .event-tooltip {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%) translateY(-100%);
        background: linear-gradient(135deg, #262840 0%, #343646 100%);
        border: 2px solid rgba(140, 224, 255, 0.3);
        border-radius: 0.75rem;
        padding: 1rem;
        min-width: 200px;
        max-width: 300px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .calendar-date:hover .event-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-100%) translateY(-5px);
    }

    .tooltip-header {
        color: #8CE0FF;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        border-bottom: 1px solid rgba(140, 224, 255, 0.2);
        padding-bottom: 0.5rem;
    }

    .tooltip-event {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 0;
        color: #D3D3D9;
        font-size: 0.8rem;
    }

    .tooltip-event-type {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .tooltip-event-title {
        flex: 1;
    }

    .tooltip-event-time {
        color: #8CE0FF;
        font-size: 0.7rem;
    }

    .event-indicator-more {
        background: rgba(140, 224, 255, 0.7);
        color: #181A26;
        font-size: 0.6rem;
        font-weight: 600;
        padding: 1px 3px;
        border-radius: 2px;
        min-width: 12px;
        text-align: center;
    }

    .tooltip-more {
        color: #8CE0FF;
        font-size: 0.7rem;
        font-style: italic;
        text-align: center;
        padding: 0.3rem 0;
        border-top: 1px solid rgba(140, 224, 255, 0.2);
        margin-top: 0.3rem;
    }

    /* Calendar Day Number Styling */
    .calendar-day-number {
        font-weight: 600;
        font-size: 1rem;
        z-index: 2;
        position: relative;
    }

    /* Quick Add Modal */
    .quick-add-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .quick-add-modal.show {
        display: flex;
        opacity: 1;
    }

    .quick-add-content {
        background: linear-gradient(135deg, #181A26 0%, #262840 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        border: 2px solid rgba(140, 224, 255, 0.2);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        transform: scale(0.9);
        transition: transform 0.3s ease;
    }

    .quick-add-modal.show .quick-add-content {
        transform: scale(1);
    }

    .quick-add-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(140, 224, 255, 0.2);
    }

    .quick-add-title {
        color: #8CE0FF;
        font-size: 1.2rem;
        font-weight: 600;
        font-family: 'DM Serif Text', serif;
    }

    .quick-add-close {
        background: rgba(255, 107, 107, 0.2);
        border: 1px solid rgba(255, 107, 107, 0.3);
        color: #FF6B6B;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .quick-add-close:hover {
        background: rgba(255, 107, 107, 0.3);
        transform: scale(1.1);
    }

    /* Week & Day View Containers */
    .week-view-container,
    .day-view-container {
        display: none;
        background: linear-gradient(135deg, #181A26 0%, #262840 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        border: 2px solid rgba(140, 224, 255, 0.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .week-view-container.active,
    .day-view-container.active {
        display: block;
    }

    .view-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(140, 224, 255, 0.2);
    }

    .view-title {
        color: #8CE0FF;
        font-size: 1.5rem;
        font-weight: 600;
        font-family: 'DM Serif Text', serif;
    }

    .view-navigation {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .view-nav-btn {
        background: rgba(140, 224, 255, 0.1);
        border: 1px solid rgba(140, 224, 255, 0.3);
        color: #8CE0FF;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .view-nav-btn:hover {
        background: rgba(140, 224, 255, 0.2);
        transform: scale(1.1);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .calendar-header-enhanced {
            flex-direction: column;
            gap: 1rem;
        }

        .view-mode-switcher {
            width: 100%;
            justify-content: center;
        }

        .calendar-actions {
            width: 100%;
            justify-content: center;
        }

        .view-mode-btn,
        .action-btn {
            flex: 1;
            justify-content: center;
            min-width: 0;
        }

        .quick-add-content {
            width: 95%;
            padding: 1.5rem;
            max-height: 90vh;
        }

        .event-tooltip {
            min-width: 180px;
            max-width: 250px;
        }
    }
</style>
@endpush

@push('styles')
<style>
/* Mobile Calendar Optimization */
@media (max-width: 768px) {
    .calendar-container {
        padding: var(--spacing-md);
    }
    
    .calendar-header {
        flex-direction: column;
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
    }
    
    .calendar-title {
        font-size: var(--font-size-2xl);
    }
    
    .calendar-nav {
        width: 100%;
        justify-content: space-between;
    }
    
    .calendar-nav-btn {
        padding: var(--spacing-md);
        font-size: var(--font-size-lg);
        min-width: 44px;
        min-height: 44px;
    }
    
    .calendar-grid {
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        font-size: var(--font-size-xs);
    }
    
    .calendar-day {
        aspect-ratio: 1;
        padding: var(--spacing-xs);
        font-size: var(--font-size-xs);
        min-height: 44px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .calendar-day.has-events {
        font-size: var(--font-size-xs);
    }
    
    .calendar-day-number {
        font-size: var(--font-size-sm);
    }
    
    .calendar-events {
        margin-top: var(--spacing-xs);
    }
    
    .calendar-event {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        margin: 1px;
    }
    
    .event-form {
        padding: var(--spacing-lg);
    }
    
    .event-form-group {
        margin-bottom: var(--spacing-lg);
    }
    
    .event-form-input {
        width: 100%;
        padding: var(--spacing-md);
        font-size: var(--font-size-base);
    }
    
    .event-form-actions {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .event-btn {
        width: 100%;
        padding: var(--spacing-md);
        font-size: var(--font-size-base);
    }
}
</style>
@endpush

@section('content')
<div class="content-area">
    <h1 class="page-title">Calendar</h1>
    
    <!-- 🚀 NEW: Enhanced Calendar Header with View Modes -->
    <div class="calendar-header-enhanced">
        <div class="view-mode-switcher">
            <button class="view-mode-btn active" data-view="month" onclick="switchCalendarView('month')">
                📅 Month
            </button>
            <button class="view-mode-btn" data-view="week" onclick="switchCalendarView('week')">
                📊 Week  
            </button>
            <button class="view-mode-btn" data-view="day" onclick="switchCalendarView('day')">
                📋 Day
            </button>
        </div>
        
        <div class="calendar-actions">
            <button class="action-btn" onclick="goToToday()" title="Go to Today">
                🎯 Today
            </button>
            <button class="action-btn" onclick="toggleEventForm()" title="Quick Add Event">
                ➕ Quick Add
            </button>
        </div>
    </div>

    <!-- Calendar and Event Layout -->
    <div class="calendar-layout">
        <!-- Calendar Section -->
        <section class="content-section calendar-section" id="monthViewContainer">
            <div class="calendar-nav">
                <button class="nav-btn" id="prevMonth">‹</button>
                
                <div class="month-year-selector">
                    <select class="month-select" id="monthSelect">
                        <option value="0">Januari</option>
                        <option value="1">Februari</option>
                        <option value="2">Maret</option>
                        <option value="3">April</option>
                        <option value="4">Mei</option>
                        <option value="5">Juni</option>
                        <option value="6">Juli</option>
                        <option value="7">Agustus</option>
                        <option value="8">September</option>
                        <option value="9">Oktober</option>
                        <option value="10">November</option>
                        <option value="11">Desember</option>
                    </select>
                    
                    <select class="year-select" id="yearSelect">
                        @for($year = date('Y') - 5; $year <= date('Y') + 10; $year++)
                            <option value="{{ $year }}" {{ $year == $calendarData['year'] ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <button class="nav-btn" id="nextMonth">›</button>
            </div>

            <div class="current-month-year" id="currentMonthYear">
                {{ $calendarData['monthName'] }} {{ $calendarData['year'] }}
            </div>

            <div class="calendar-grid" id="calendarGrid">
                <!-- Days Header -->
                <div class="day-header">Min</div>
                <div class="day-header">Sen</div>
                <div class="day-header">Sel</div>
                <div class="day-header">Rab</div>
                <div class="day-header">Kam</div>
                <div class="day-header">Jum</div>
                <div class="day-header">Sab</div>
                
                <!-- Calendar Days -->
                        @php
                            $startOfMonth = $calendarData['firstDay'];
                            $endOfMonth = $calendarData['lastDay'];
                            $startOfCalendar = $startOfMonth->copy()->startOfWeek();
                            $endOfCalendar = $endOfMonth->copy()->endOfWeek();
                            $currentDate = $startOfCalendar->copy();
                        @endphp

                        @while($currentDate <= $endOfCalendar)
                            @php
                                $dateString = $currentDate->format('Y-m-d');
                                $dayEvents = $eventsByDate[$dateString] ?? collect();
                                $isCurrentMonth = $currentDate->month === $calendarData['month'];
                                $isToday = $currentDate->isToday();
                            @endphp
                            
                    <div class="calendar-date 
                        {{ !$isCurrentMonth ? 'other-month' : '' }}
                        {{ $isToday ? 'today' : '' }}
                        {{ $dayEvents->count() > 0 ? 'has-event' : '' }}"
                        data-date="{{ $dateString }}"
                        onclick="selectCalendarDate('{{ $dateString }}')">
                        
                        <!-- Day Number -->
                        <span class="calendar-day-number">{{ $currentDate->day }}</span>
                        
                        <!-- Event Indicators -->
                        @if($dayEvents->count() > 0)
                            <div class="event-indicators">
                                @foreach($dayEvents->take(3) as $event)
                                    <div class="event-indicator type-{{ $event->type }}" 
                                         title="{{ $event->title }}"></div>
                                @endforeach
                                @if($dayEvents->count() > 3)
                                    <div class="event-indicator-more">+{{ $dayEvents->count() - 3 }}</div>
                                @endif
                            </div>
                            
                            <!-- Event Tooltip -->
                            <div class="event-tooltip">
                                <div class="tooltip-header">
                                    📅 {{ $currentDate->format('d M Y') }} ({{ $dayEvents->count() }} events)
                                </div>
                                @foreach($dayEvents->take(5) as $event)
                                    <div class="tooltip-event">
                                        <div class="tooltip-event-type type-{{ $event->type }}"></div>
                                        <div class="tooltip-event-title">
                                            {{ $event->getTypeIcon() }} {{ Str::limit($event->title, 20) }}
                                        </div>
                                        <div class="tooltip-event-time">
                                            {{ $event->start_date->format('H:i') }}
                                        </div>
                                    </div>
                                @endforeach
                                @if($dayEvents->count() > 5)
                                    <div class="tooltip-more">
                                        ... and {{ $dayEvents->count() - 5 }} more events
                                    </div>
                                @endif
                            </div>
                        @endif
                            </div>
                            
                            @php
                                $currentDate->addDay();
                            @endphp
                        @endwhile
                    </div>

            <div class="quick-nav">
                <button class="quick-nav-btn" onclick="goToToday()">Hari Ini</button>
                <button class="quick-nav-btn" onclick="goToMonth(-1)">Bulan Lalu</button>
                <button class="quick-nav-btn" onclick="goToMonth(1)">Bulan Depan</button>
                        </div>
        </section>
        
        <!-- 🚀 NEW: Week View Container -->
        <section class="week-view-container" id="weekViewContainer">
            <div class="view-header">
                <h3 class="view-title">📊 Week View</h3>
                <div class="view-navigation">
                    <button class="view-nav-btn" onclick="navigateWeek(-1)">‹</button>
                    <span id="currentWeek">Week of Jan 15-21, 2025</span>
                    <button class="view-nav-btn" onclick="navigateWeek(1)">›</button>
                </div>
            </div>
            
            <div class="week-grid" id="weekGrid">
                <!-- Week view will be populated by JavaScript -->
                <div class="coming-soon">
                    <p style="text-align: center; color: #8CE0FF; font-style: italic; padding: 2rem;">
                        🚧 Week view coming soon! This will show a 7-day detailed view with hourly slots.
                    </p>
                </div>
            </div>
        </section>
        
        <!-- 🚀 NEW: Day View Container -->
        <section class="day-view-container" id="dayViewContainer">
            <div class="view-header">
                <h3 class="view-title">📋 Day View</h3>
                <div class="view-navigation">
                    <button class="view-nav-btn" onclick="navigateDay(-1)">‹</button>
                    <span id="currentDay">Today, January 15, 2025</span>
                    <button class="view-nav-btn" onclick="navigateDay(1)">›</button>
                </div>
            </div>
            
            <div class="day-schedule" id="daySchedule">
                <!-- Day view will be populated by JavaScript -->
                <div class="coming-soon">
                    <p style="text-align: center; color: #8CE0FF; font-style: italic; padding: 2rem;">
                        🚧 Day view coming soon! This will show detailed hourly schedule for the selected day.
                    </p>
                </div>
            </div>
        </section>

        <!-- Add Event Section -->
        <section class="content-section event-section">
            <h2 class="section-title">Add Event</h2>
            
            @if(session('success'))
                <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('events.store') }}" method="POST" id="eventForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Event Name</label>
                    <input type="text" name="title" class="form-input" 
                        placeholder="Enter event name" 
                        value="{{ old('title') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Start Date & Time</label>
                    <div class="form-row">
                        <input type="date" name="start_date" class="form-input" 
                            id="eventDate" value="{{ old('start_date') }}" required>
                        <input type="time" name="start_time" class="form-input" 
                            value="{{ old('start_time', '09:00') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">End Date & Time (Optional)</label>
                    <div class="form-row">
                        <input type="date" name="end_date" class="form-input" 
                            value="{{ old('end_date') }}">
                        <input type="time" name="end_time" class="form-input" 
                            value="{{ old('end_time') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">
                        <span class="icon">🏷️</span>
                        Event Type
                    </label>
                    <select id="type" name="type" class="form-input" required>
                        <option value="">Select event type...</option>
                        <option value="event">🎉 General Event</option>
                        <option value="cycle">🔄 Cycle Tracking</option>
                        <option value="birthday">🎂 Birthday</option>
                    </select>
                    @error('type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ✅ TAMBAHKAN INI - Spotify Track Field -->
                <div class="form-group">
                    <label for="spotify_track_id" class="form-label">
                        <span class="icon">🎵</span>
                        Background Music (Optional)
                    </label>
                    <input 
                        type="text" 
                        id="spotify_track_id" 
                        name="spotify_track_id" 
                        value="{{ old('spotify_track_id') }}"
                        class="form-input"
                        placeholder="Spotify track ID (optional)"
                    >
                    @error('spotify_track_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Share With -->
                <div class="form-group">
                    <label class="form-label">
                        <span class="icon">👥</span>
                        Share With
                    </label>
                    <div class="sharing-options">
                        @foreach($allUsers as $user)
                            <label class="checkbox-item">
                                <input 
                                    type="checkbox" 
                                    name="shared_with[]" 
                                    value="{{ $user->id }}"
                                    {{ auth()->id() === $user->id ? 'checked disabled' : '' }}
                                    {{ in_array($user->id, old('shared_with', [])) ? 'checked' : '' }}
                                    class="checkbox-input"
                                >
                                <span class="checkbox-label">
                                    {{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="creator-badge">(You)</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('shared_with')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input form-textarea" 
                            placeholder="Write your description here">{{ old('description') }}</textarea>
                </div>
                
                <button type="submit" class="add-btn">Add Event</button>
            </form>
        </section>
    </div>

    <!-- Events List Section -->
    <section class="events-list">
        <h2 class="events-title">Upcoming Events</h2>
        
        @if($upcomingEvents->count() > 0)
            @foreach($upcomingEvents as $event)
                <div class="event-item" onclick="window.location='{{ route('events.show', $event) }}'">
                    <div class="event-date-display">
                        {{ $event->start_date->format('d F') }}
                                    </div>
                    <div class="event-time">
                        {{ $event->start_date->format('H:i') }}
                            </div>
                    <div class="event-separator"></div>
                    <div class="event-name-display">
                        {{ $event->getTypeIcon() }} {{ $event->title }}
                        </div>
                    <div class="event-actions">
                        <a href="{{ route('events.edit', $event) }}" class="event-action-btn" 
                           onclick="event.stopPropagation()">Edit</a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" 
                              style="display: inline;" onsubmit="return confirm('Delete this event?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="event-action-btn" 
                                    onclick="event.stopPropagation()">Delete</button>
                        </form>
                        </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; color: #8F8C8C; padding: 2vh;">
                No upcoming events
            </div>
        @endif
    </section>
    
    <!-- 🚀 NEW: Quick Add Event Modal -->
    <div class="quick-add-modal" id="quickAddModal">
        <div class="quick-add-content">
            <div class="quick-add-header">
                <h3 class="quick-add-title">⚡ Quick Add Event</h3>
                <button class="quick-add-close" onclick="closeQuickAdd()">✕</button>
            </div>
            
            <form id="quickAddForm" action="{{ route('events.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">📝 Event Title</label>
                    <input type="text" name="title" class="form-input" 
                           placeholder="Enter event title" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">📅 Date & Time</label>
                    <div class="form-row">
                        <input type="date" name="start_date" class="form-input" 
                               id="quickEventDate" required>
                        <input type="time" name="start_time" class="form-input" 
                               value="09:00" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">🏷️ Event Type</label>
                    <select name="type" class="form-input" required>
                        <option value="event">🎉 General Event</option>
                        <option value="cycle">🔄 Cycle Tracking</option>
                        <option value="birthday">🎂 Birthday</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">🎵 Background Music (Optional)</label>
                    <input type="text" name="spotify_track_id" class="form-input" 
                           placeholder="Spotify track ID (optional)">
                </div>
                
                <div class="form-group">
                    <label class="form-label">📝 Description (Optional)</label>
                    <textarea name="description" class="form-input form-textarea" 
                              placeholder="Brief description" rows="3"></textarea>
                </div>
                
                <!-- Hidden field for sharing (default to current user) -->
                <input type="hidden" name="shared_with[]" value="{{ auth()->id() }}">
                
                <div class="form-group" style="text-align: right; margin-top: 1.5rem;">
                    <button type="button" class="nav-btn" onclick="closeQuickAdd()" 
                            style="margin-right: 1rem; background: rgba(107, 114, 128, 0.2);">
                        Cancel
                    </button>
                    <button type="submit" class="add-btn">
                        ➕ Add Event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// 🚀 ENHANCED CALENDAR JAVASCRIPT

// Global state
let currentView = 'month';
let selectedDate = null;

// Calendar JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Enhanced Calendar initialized');
    
    // Initialize calendar
    initializeCalendar();
    initializeViewSwitching();
    initializeQuickAdd();
    initializeCalendarInteractions();
});

function initializeCalendar() {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    
    if (!monthSelect || !yearSelect) return;
    
    // Set current month/year
    monthSelect.value = {{ $calendarData['month'] - 1 }};
    yearSelect.value = {{ $calendarData['year'] }};
    
    // Navigation handlers
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            let month = parseInt(monthSelect.value);
            let year = parseInt(yearSelect.value);
            
            if (month === 0) {
                month = 11;
                year--;
            } else {
                month--;
            }
            
            navigateToMonth(year, month + 1);
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            let month = parseInt(monthSelect.value);
            let year = parseInt(yearSelect.value);
            
            if (month === 11) {
                month = 0;
                year++;
            } else {
                month++;
            }
            
            navigateToMonth(year, month + 1);
        });
    }
    
    monthSelect.addEventListener('change', () => {
        navigateToMonth(yearSelect.value, parseInt(monthSelect.value) + 1);
    });
    
    yearSelect.addEventListener('change', () => {
        navigateToMonth(yearSelect.value, parseInt(monthSelect.value) + 1);
    });
}

function initializeViewSwitching() {
    // Set Month view as default active
    currentView = 'month';
    showView('month');
}

function initializeQuickAdd() {
    // Close modal when clicking outside
    const modal = document.getElementById('quickAddModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeQuickAdd();
            }
        });
    }
    
    // Handle form submission
    const quickForm = document.getElementById('quickAddForm');
    if (quickForm) {
        quickForm.addEventListener('submit', function(e) {
            // Let the form submit normally, but provide user feedback
            const submitBtn = quickForm.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '⏳ Adding...';
                submitBtn.disabled = true;
            }
        });
    }
}

function initializeCalendarInteractions() {
    // Enhanced calendar date interactions
    document.querySelectorAll('.calendar-date:not(.other-month)').forEach(date => {
        // Click handler
        date.addEventListener('click', function() {
            const dateValue = this.getAttribute('data-date');
            selectCalendarDate(dateValue);
        });
        
        // Keyboard accessibility
        date.setAttribute('tabindex', '0');
        date.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

// 🚀 VIEW SWITCHING FUNCTIONALITY
function switchCalendarView(viewType) {
    console.log('Switching to view:', viewType);
    
    // Update button states
    document.querySelectorAll('.view-mode-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === viewType);
    });
    
    currentView = viewType;
    showView(viewType);
}

function showView(viewType) {
    // Hide all views
    const monthContainer = document.getElementById('monthViewContainer');
    const weekContainer = document.getElementById('weekViewContainer');
    const dayContainer = document.getElementById('dayViewContainer');
    
    if (monthContainer) monthContainer.style.display = 'none';
    if (weekContainer) weekContainer.classList.remove('active');
    if (dayContainer) dayContainer.classList.remove('active');
    
    // Show selected view
    switch(viewType) {
        case 'month':
            if (monthContainer) monthContainer.style.display = 'block';
            break;
        case 'week':
            if (weekContainer) {
                weekContainer.classList.add('active');
                updateWeekView();
            }
            break;
        case 'day':
            if (dayContainer) {
                dayContainer.classList.add('active');
                updateDayView();
            }
            break;
    }
}

// 🚀 QUICK ADD FUNCTIONALITY
function toggleEventForm() {
    const modal = document.getElementById('quickAddModal');
    if (modal) {
        if (modal.classList.contains('show')) {
            closeQuickAdd();
        } else {
            openQuickAdd();
        }
    }
}

function openQuickAdd() {
    const modal = document.getElementById('quickAddModal');
    if (modal) {
        modal.classList.add('show');
        
        // Set date to selected date or today
        const dateInput = document.getElementById('quickEventDate');
        if (dateInput) {
            if (selectedDate) {
                dateInput.value = selectedDate;
            } else {
                dateInput.value = new Date().toISOString().split('T')[0];
            }
        }
        
        // Focus on title input
        const titleInput = modal.querySelector('input[name="title"]');
        if (titleInput) {
            setTimeout(() => titleInput.focus(), 100);
        }
    }
}

function closeQuickAdd() {
    const modal = document.getElementById('quickAddModal');
    if (modal) {
        modal.classList.remove('show');
        
        // Reset form
        const form = document.getElementById('quickAddForm');
        if (form) {
            form.reset();
            
            // Reset submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '➕ Add Event';
                submitBtn.disabled = false;
            }
        }
    }
}

// 🚀 CALENDAR DATE SELECTION
function selectCalendarDate(dateString) {
    console.log('Selected date:', dateString);
    
    selectedDate = dateString;
    
    // Update main event form date
    const eventDateInput = document.getElementById('eventDate');
    if (eventDateInput) {
        eventDateInput.value = dateString;
    }
    
    // Highlight selected date
    document.querySelectorAll('.calendar-date').forEach(d => {
        d.classList.remove('highlight');
    });
    
    const selectedDateElement = document.querySelector(`[data-date="${dateString}"]`);
    if (selectedDateElement) {
        selectedDateElement.classList.add('highlight');
    }
    
    // Open quick add modal
    openQuickAdd();
}

// 🚀 NAVIGATION FUNCTIONS
function navigateToMonth(year, month) {
    const url = `{{ route('calendar') }}?year=${year}&month=${month}`;
    window.location.href = url;
}

function goToToday() {
    const today = new Date();
    if (currentView === 'month') {
        navigateToMonth(today.getFullYear(), today.getMonth() + 1);
    } else {
        // For week/day views, implement specific navigation
        console.log('Navigate to today in', currentView, 'view');
        switchCalendarView('month'); // Fall back to month view for now
        setTimeout(() => {
            navigateToMonth(today.getFullYear(), today.getMonth() + 1);
        }, 100);
    }
}

function goToMonth(offset) {
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    
    if (!monthSelect || !yearSelect) return;
    
    let month = parseInt(monthSelect.value) + offset;
    let year = parseInt(yearSelect.value);
    
    if (month < 0) {
        month = 11;
        year--;
    } else if (month > 11) {
        month = 0;
        year++;
    }
    
    navigateToMonth(year, month + 1);
}

// 🚀 WEEK VIEW FUNCTIONS (Placeholder)
function updateWeekView() {
    const currentWeekElement = document.getElementById('currentWeek');
    if (currentWeekElement) {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        
        currentWeekElement.textContent = `Week of ${startOfWeek.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})} - ${endOfWeek.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}, ${today.getFullYear()}`;
    }
}

function navigateWeek(offset) {
    console.log('Navigate week:', offset);
    // Implement week navigation
}

// 🚀 DAY VIEW FUNCTIONS (Placeholder)
function updateDayView() {
    const currentDayElement = document.getElementById('currentDay');
    if (currentDayElement) {
        const today = new Date();
        currentDayElement.textContent = today.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
}

function navigateDay(offset) {
    console.log('Navigate day:', offset);
    // Implement day navigation
}

// Add CSS for highlight effect
const style = document.createElement('style');
style.textContent = `
    .calendar-date.highlight {
        background: linear-gradient(135deg, #8CE0FF 0%, #6bd4ff 100%) !important;
        color: #181A26 !important;
        font-weight: 700 !important;
        transform: scale(1.1) !important;
        box-shadow: 0 8px 25px rgba(140, 224, 255, 0.4) !important;
        z-index: 10 !important;
        border-color: #8CE0FF !important;
    }
    
    .calendar-date.highlight .calendar-day-number {
        color: #181A26 !important;
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection