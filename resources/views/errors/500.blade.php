<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 500 - Logiez</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3f0d71 0%, #63499E 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
        }
        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            max-width: 500px;
            margin: 1rem;
        }
        .error-code {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #ff6b6b;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .error-description {
            font-size: 1rem;
            margin-bottom: 2rem;
            opacity: 0.8;
        }
        .btn {
            background: #7209B7;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5a078a;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-message">Erro Interno do Servidor</div>
        <div class="error-description">
            Ops! Algo deu errado. Nossa equipe foi notificada e está trabalhando para resolver o problema.
        </div>
        <a href="/" class="btn">Voltar ao Início</a>
    </div>
</body>
</html>