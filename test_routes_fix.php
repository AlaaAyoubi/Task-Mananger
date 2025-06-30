<?php

require_once 'vendor/autoload.php';

// إعداد Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== اختبار إصلاح المسارات ===\n\n";

$testResults = [];
$totalTests = 0;
$passedTests = 0;

// دالة مساعدة لتسجيل النتائج
function recordTest($testName, $passed, $message = '') {
    global $testResults, $totalTests, $passedTests;
    $totalTests++;
    if ($passed) $passedTests++;
    
    $status = $passed ? '✅' : '❌';
    $testResults[] = [
        'name' => $testName,
        'passed' => $passed,
        'message' => $message
    ];
    
    echo "{$status} {$testName}\n";
    if (!$passed && $message) {
        echo "   رسالة: {$message}\n";
    }
}

// المسارات التي كانت مفقودة
$missingRoutes = [
    'tasks.edit' => 'GET',
    'tasks.update' => 'PUT',
    'tasks.destroy' => 'DELETE',
    'manager.tasks.edit' => 'GET',
    'manager.tasks.update' => 'PUT',
    'manager.tasks.destroy' => 'DELETE',
    'manager.my-tasks.updateStatus' => 'PATCH',
    'teams.edit' => 'GET',
    'teams.update' => 'PUT',
    'teams.destroy' => 'DELETE',
    'manager.teams.edit' => 'GET',
    'manager.teams.update' => 'PUT',
    'manager.teams.destroy' => 'DELETE',
    'notifications.markAsRead' => 'PATCH',
    'notifications.destroy' => 'DELETE'
];

echo "اختبار المسارات المفقودة سابقاً:\n";

foreach ($missingRoutes as $routeName => $method) {
    try {
        $route = route($routeName);
        recordTest("مسار {$routeName} ({$method})", true, 'متاح الآن');
    } catch (Exception $e) {
        recordTest("مسار {$routeName} ({$method})", false, 'لا يزال غير متاح: ' . $e->getMessage());
    }
}

echo "\n";

// اختبار المسارات الأساسية للتأكد من عدم كسرها
$basicRoutes = [
    'admin.dashboard' => 'GET',
    'manager.dashboard' => 'GET',
    'member.dashboard' => 'GET',
    'tasks.index' => 'GET',
    'tasks.create' => 'GET',
    'tasks.store' => 'POST',
    'teams.index' => 'GET',
    'teams.create' => 'GET',
    'teams.store' => 'POST',
    'notifications.index' => 'GET',
    'notifications.unread' => 'GET'
];

echo "اختبار المسارات الأساسية:\n";

foreach ($basicRoutes as $routeName => $method) {
    try {
        $route = route($routeName);
        recordTest("مسار {$routeName} ({$method})", true, 'متاح');
    } catch (Exception $e) {
        recordTest("مسار {$routeName} ({$method})", false, 'غير متاح: ' . $e->getMessage());
    }
}

echo "\n";

// ملخص النتائج
echo "=== ملخص النتائج ===\n";
echo "إجمالي الاختبارات: {$totalTests}\n";
echo "الاختبارات الناجحة: {$passedTests}\n";
echo "الاختبارات الفاشلة: " . ($totalTests - $passedTests) . "\n";
echo "نسبة النجاح: " . round(($passedTests / $totalTests) * 100, 2) . "%\n\n";

if ($passedTests == $totalTests) {
    echo "🎉 جميع المسارات تعمل الآن! تم إصلاح المشكلة بنجاح.\n";
} elseif ($passedTests >= ($totalTests * 0.8)) {
    echo "✅ معظم المسارات تعمل. الإصلاح ناجح مع بعض المشاكل البسيطة.\n";
} else {
    echo "❌ العديد من المسارات لا تزال لا تعمل. يحتاج إلى مزيد من الإصلاح.\n";
}

echo "\n=== انتهى اختبار إصلاح المسارات ===\n"; 