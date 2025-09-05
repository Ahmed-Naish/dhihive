<?php

$host = 'localhost';
$db   = 'admin_dhihive';
$user = 'dhihive_root';
$pass = 'H~f78z69l';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {$pdo = new PDO($dsn, $user, $pass, $options);} catch (PDOException $e) {
    file_put_contents(__DIR__ . '/times', "[DB ERROR] " . $e->getMessage() . "\n", FILE_APPEND);
    exit("Database connection failed.");}

$botToken = "8034898880:AAHdx-XIX7V3laEHUyOcXcsM4-584ju5c7Q";
$url = "https://api.telegram.org/bot$botToken/sendMessage";
$chatId = "-1002687364716";
$today = date('Y-m-d', strtotime("-1 day"));
$message = "* $today Attendance Summary*\n\n";
$sql = "
SELECT 
    u.name as user_name,
    a.check_in,
    a.check_out
FROM attendances a
LEFT JOIN users u ON u.id = a.user_id
WHERE DATE(a.date) = :today
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['today' => $today]);
$rows = $stmt->fetchAll();

if (empty($rows)) {$message .= "No attendance records found for today.";} else {
    foreach ($rows as $row) {
        $name = $row['user_name'] ?? 'Unknown';
        $checkIn = $row['check_in'] ? date('H:i', strtotime($row['check_in'])) : 'N/A';
        $checkOut = $row['check_out'] ? date('H:i', strtotime($row['check_out'])) : 'N/A';
        $message .= "👤 *$name*\n🕓 In: `$checkIn` | Out: `$checkOut`\n\n";
    }}
$postFields = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'Markdown'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);

$result = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

date_default_timezone_set('Etc/GMT-5');
$logLine = "[" . date("Y-m-d H:i:s") . "] ";
$logLine .= $error ? "Telegram send error: $error\n" : "Telegram sent.\n";
file_put_contents(__DIR__ . '/times', $logLine, FILE_APPEND | LOCK_EX);
