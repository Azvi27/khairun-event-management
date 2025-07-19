<?php

namespace App\Services;

class ThemeService
{
    /**
     * Available themes configuration
     */
    private const THEMES = [
        'dark' => [
            'name' => 'Dark Theme',
            'css_file' => 'theme-variables.css',
            'primary_bg' => '#343646',
            'accent_color' => '#8CE0FF',
            'text_color' => '#D3D3D9',
        ],
        'light' => [
            'name' => 'Light Theme', 
            'css_file' => 'theme-variables-light.css',
            'primary_bg' => '#f8fafc',
            'accent_color' => '#3b82f6',
            'text_color' => '#1e293b',
        ]
    ];

    /**
     * Get current theme from session or default
     */
    public function getCurrentTheme(): string
    {
        return session('theme', 'dark');
    }

    /**
     * Set theme in session
     */
    public function setTheme(string $theme): bool
    {
        if (!$this->isValidTheme($theme)) {
            return false;
        }

        session(['theme' => $theme]);
        return true;
    }

    /**
     * Get theme configuration
     */
    public function getThemeConfig(string $theme = null): array
    {
        $theme = $theme ?? $this->getCurrentTheme();
        return self::THEMES[$theme] ?? self::THEMES['dark'];
    }

    /**
     * Get all available themes
     */
    public function getAvailableThemes(): array
    {
        return self::THEMES;
    }

    /**
     * Check if theme is valid
     */
    public function isValidTheme(string $theme): bool
    {
        return array_key_exists($theme, self::THEMES);
    }

    /**
     * Get CSS file path for current theme
     */
    public function getThemeCssFile(): string
    {
        $config = $this->getThemeConfig();
        return asset('css/' . $config['css_file']);
    }

    /**
     * Generate CSS variables for inline styles
     */
    public function generateInlineCssVariables(): string
    {
        $config = $this->getThemeConfig();
        
        return sprintf(
            ':root { --theme-primary-bg: %s; --theme-accent: %s; --theme-text: %s; }',
            $config['primary_bg'],
            $config['accent_color'],
            $config['text_color']
        );
    }

    /**
     * Get theme-specific asset path
     */
    public function getThemedAsset(string $assetPath): string
    {
        $theme = $this->getCurrentTheme();
        $themedPath = str_replace('.', "-{$theme}.", $assetPath);
        
        // Check if themed version exists, fallback to default
        $fullPath = public_path($themedPath);
        if (file_exists($fullPath)) {
            return asset($themedPath);
        }
        
        return asset($assetPath);
    }

    /**
     * Apply theme to response headers for caching
     */
    public function applyThemeHeaders($response)
    {
        $theme = $this->getCurrentTheme();
        $response->header('X-Theme', $theme);
        $response->header('Vary', 'X-Theme');
        
        return $response;
    }
}