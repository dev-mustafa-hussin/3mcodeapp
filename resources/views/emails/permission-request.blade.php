<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب صلاحية جديد</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: right; direction: rtl;">
    <h2>طلب وصول جديد</h2>
    <p>قام الموظف <strong>{{ $user->name }}</strong> بطلب صلاحية وصول.</p>
    
    <ul>
        <li><strong>المستخدم:</strong> {{ $user->name }} ({{ $user->email }})</li>
        <li><strong>الصلاحية/الصفحة المطلوبة:</strong> {{ $requestedPermission ?? 'غير محدد' }}</li>
        <li><strong>الرابط:</strong> <a href="{{ $url }}">{{ $url }}</a></li>
    </ul>

    <p>يرجى مراجعة لوحة التحكم للموافقة أو الرفض.</p>
</body>
</html>
