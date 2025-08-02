<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Новая заявка с сайта WoodZavod</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }
        .field {
            margin-bottom: 15px;
            padding: 10px;
            background-color: white;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            color: #212529;
        }
        .footer {
            background-color: #e9ecef;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
        }
        .house-info {
            background-color: #e8f5e8;
            border-left-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Новая заявка с сайта WoodZavod</h1>
    </div>
    
    <div class="content">
        <div class="field">
            <div class="field-label">👤 Имя клиента:</div>
            <div class="field-value">{{ $lead->name }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">📞 Телефон:</div>
            <div class="field-value">{{ $lead->phone }}</div>
        </div>
        
        @if($lead->email)
        <div class="field">
            <div class="field-label">📧 Email:</div>
            <div class="field-value">{{ $lead->email }}</div>
        </div>
        @endif
        
        @if($lead->house)
        <div class="field house-info">
            <div class="field-label">🏡 Интересующий дом:</div>
            <div class="field-value">{{ $lead->house->name }}</div>
        </div>
        @endif
        
        @if($lead->message)
        <div class="field">
            <div class="field-label">💬 Сообщение:</div>
            <div class="field-value">{{ $lead->message }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="field-label">⏰ Время подачи заявки:</div>
            <div class="field-value">{{ $lead->created_at->format('d.m.Y в H:i') }}</div>
        </div>
    </div>
    
    <div class="footer">
        <p>Это автоматическое уведомление с сайта WoodZavod</p>
        <p>Пожалуйста, свяжитесь с клиентом в ближайшее время</p>
    </div>
</body>
</html>