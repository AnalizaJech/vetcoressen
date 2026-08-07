<!DOCTYPE html>
<html lang="es" x-data x-init="$store.theme.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Página no encontrada | {{ config('app.name') }}</title>
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Google Material Symbols Outlined --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    {{-- CSS Design System --}}
    <link rel="stylesheet" href="{{ asset('css/vetcoressen.css') }}">
    
    <script src="{{ asset('js/theme.js') }}"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--vc-bg);
            color: var(--vc-text);
        }
        .error-container {
            width: 100%;
            max-width: 600px;
            margin: 20px;
            background-color: var(--vc-surface);
            border: 1px solid var(--vc-border);
            border-radius: 32px;
            box-shadow: var(--vc-shadow-lg);
            position: relative;
            overflow: hidden;
            text-align: center;
            padding: 60px 40px;
        }
        .error-bg-glow {
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, var(--vc-emerald-glow) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }
        .error-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .error-icon-box {
            width: 96px;
            height: 96px;
            background-color: rgba(20, 184, 166, 0.1);
            color: var(--vc-emerald);
            border: 1px solid var(--vc-border-emerald);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        .error-icon-box span {
            font-size: 48px;
        }
        .error-code {
            font-size: 100px;
            font-weight: 800;
            line-height: 1;
            margin: 0 0 16px 0;
            background: linear-gradient(135deg, var(--vc-emerald-light), var(--vc-emerald-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .error-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 16px 0;
            color: var(--vc-text);
        }
        .error-desc {
            font-size: 16px;
            color: var(--vc-text-secondary);
            margin: 0 0 40px 0;
            line-height: 1.6;
            max-width: 400px;
        }
        .error-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary-error {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background-color: var(--vc-emerald);
            color: white;
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(20, 184, 166, 0.3);
            border: none;
            cursor: pointer;
        }
        .btn-primary-error:hover {
            background-color: var(--vc-emerald-light);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(20, 184, 166, 0.4);
        }
        .btn-secondary-error {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background-color: var(--vc-surface-elevated);
            color: var(--vc-text);
            font-weight: 600;
            font-size: 15px;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid var(--vc-border);
            cursor: pointer;
        }
        .btn-secondary-error:hover {
            background-color: var(--vc-surface-hover);
        }
    </style>
</head>
<body>

    <div class="error-container">
        <div class="error-bg-glow"></div>
        
        <div class="error-content">
            <div class="error-icon-box">
                <span class="material-symbols-outlined">pets</span>
            </div>
            
            <h1 class="error-code">404</h1>
            <h2 class="error-title">¡Ups! Nos perdimos en el camino</h2>
            <p class="error-desc">La página que buscas parece haber huido. Tal vez un perro se llevó el enlace o simplemente ya no existe.</p>
            
            <div class="error-actions">
                <a href="/dashboard" class="btn-primary-error">
                    <span class="material-symbols-outlined" style="font-size: 20px;">home</span>
                    Volver al Inicio
                </a>
                
                <button onclick="window.history.back()" class="btn-secondary-error">
                    <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
                    Regresar
                </button>
            </div>
        </div>
    </div>
    
</body>
</html>
