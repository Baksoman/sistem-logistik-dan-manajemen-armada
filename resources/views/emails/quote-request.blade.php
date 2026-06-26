<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .header { background: #374151; color: white; padding: 20px; text-align: center; }
        .content { background: white; padding: 30px; margin-top: 20px; }
        .field { margin-bottom: 20px; }
        .label { font-weight: bold; color: #374151; }
        .value { margin-top: 5px; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Request Penawaran Logistik</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Nama Perusahaan:</div>
                <div class="value">{{ $data['company'] }}</div>
            </div>
            <div class="field">
                <div class="label">Email Bisnis:</div>
                <div class="value">{{ $data['email'] }}</div>
            </div>
            <div class="field">
                <div class="label">No. Telepon:</div>
                <div class="value">{{ $data['phone'] }}</div>
            </div>
            <div class="field">
                <div class="label">Kebutuhan Logistik:</div>
                <div class="value">{{ $data['needs'] }}</div>
            </div>
        </div>
    </div>
</body>
</html>
