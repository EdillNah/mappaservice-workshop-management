<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?>MappaService</title>
  
  <!-- Google Fonts: Inter & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
            mono: ['"JetBrains Mono"', 'Source Code Pro', 'ui-monospace', 'monospace'],
          },
          colors: {
            obsidian: '#121212',
            ash: '#242424',
            charcoal: '#2e2e2e',
            slate: '#393939',
            graphite: '#4d4d4d',
            smoke: '#898989',
            silver: '#b4b4b4',
            snow: '#fafafa',
            phosphor: {
              DEFAULT: '#3ecf8e',
              light: '#5be8a6',
              dark: '#1f4b37',
              emerald: '#006239',
              mint: '#00c573'
            }
          },
          letterSpacing: {
            tightest: '-0.02em',
            tight: '-0.007em',
          }
        }
      }
    }
  </script>
  <style>
    :root {
      --color-obsidian: #121212;
      --color-ash: #242424;
      --color-charcoal: #2e2e2e;
      --color-slate: #393939;
      --color-graphite: #4d4d4d;
      --color-smoke: #898989;
      --color-silver: #b4b4b4;
      --color-snow: #fafafa;
      --color-phosphor: #3ecf8e;
    }

    body {
      background-color: #121212;
      color: #fafafa;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      letter-spacing: -0.007em;
    }

    /* Custom scrollbars */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #121212;
    }
    ::-webkit-scrollbar-thumb {
      background: #2e2e2e;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #4d4d4d;
    }

    /* Supabase focus style: crisp phosphor green border without glow */
    input:focus, select:focus, textarea:focus {
      outline: none !important;
      border-color: #3ecf8e !important;
      box-shadow: none !important;
    }

    /* Custom sleek select dropdown chevron */
    select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23898989'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 0.85rem center;
      background-size: 1rem 1rem;
      padding-right: 2.5rem !important;
      cursor: pointer;
    }

    select:focus {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%233ecf8e'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3e%3c/svg%3e");
    }

    select option {
      background-color: #1a1a1a;
      color: #fafafa;
    }

    .border-charcoal {
      border-color: #2e2e2e;
    }
  </style>
</head>

<body class="bg-obsidian text-snow antialiased min-h-screen">
<div class="min-h-screen flex flex-col md:flex-row">