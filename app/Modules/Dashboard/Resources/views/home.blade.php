<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сервисы Элитеврострой-Плюс</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e3a5f;
            --primary-hover: #152a45;
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        
        body {
            min-height: 100vh;
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .hero-header {
            background: var(--primary-color);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            box-shadow: 0 4px 20px rgba(30, 58, 95, 0.3);
        }
        
        .hero-header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .hero-header .subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }
        
        .service-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .service-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .service-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .service-description {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .btn-download {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
        }
        
        .btn-download:hover {
            background: var(--primary-hover);
            box-shadow: 0 6px 25px rgba(30, 58, 95, 0.4);
        }
        
        .btn-download i {
            margin-right: 0.5rem;
        }
        
        .file-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .file-info i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }
        
        footer {
            margin-top: 4rem;
            padding: 2rem 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        
        footer p {
            margin: 0;
            color: #666;
        }
        
        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .hero-header h1 {
                font-size: 1.8rem;
            }
            
            .service-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="hero-header">
        <div class="container text-center">
            <h1>Сервисы Элитеврострой-Плюс</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="bi bi-file-earmark-excel"></i>
                    </div>
                    <h2 class="service-title">Актуальный перечень, ГосСтройПортал</h2>
                    <p class="service-description">
                        Официальный перечень объектов мониторинга Государственного строительного портала 
                        Республики Беларусь. Файл автоматически обновляется при синхронизации
                        с базой данных ГосСтройПортала.
                    </p>
                    
                    <a href="{{ route('gosstroy.download.current-list') }}" class="btn btn-primary btn-download">
                        <i class="bi bi-download"></i>
                        Скачать
                    </a>
                    
                    <div class="file-info">
                        <div class="row">
                            <div class="col-6">
                                <i class="bi bi-file-earmark"></i>
                                Формат: XLSX
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p>&copy; {{ date('Y') }} Элитеврострой-Плюс. Все права защищены.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>