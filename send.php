<?php

$token = "6821514093:AAH_s_m6mI5DvEdp-tiLimGq1V9IhEuhq40";
$chat_id = "-4137258746";

if ($_POST['act'] == 'order') {
    $name = isset($_POST['Name']) ? $_POST['Name'] : '-';
    $phone = isset($_POST['Phone']) ? $_POST['Phone'] : '-';
    $message = isset($_POST['Task']) ? $_POST['Task'] : '-';
 
    $message = !empty($message) ? $message : '-';
   
    $txt = "Имя: " . $name . "\n";
    $txt .= "Телефон: " . $phone . "\n";
    $txt .= "Задача: " . $message . "\n";   

    $target_dir = "uploads/"; // Путь для сохранения файла
    $media = [];


    if(!empty($_FILES['File']['name'][0])) {
        $file_count = count($_FILES['File']['name']);
        if ($file_count == 1) {
            $post_fields = [
                'chat_id'   => $chat_id,
                'media' => json_encode([  
                    ['type' => 'document', 'media' => 'attach://file0', 'caption' => $txt]
                ])
            ];
        } else if ($file_count == 2) {
            $post_fields = [
                'chat_id'   => $chat_id,
                'media' => json_encode([  
                    ['type' => 'document', 'media' => 'attach://file0'],
                    ['type' => 'document', 'media' => 'attach://file1', 'caption' => $txt]
                ])
            ];
        } else if ($file_count == 3) {
            $post_fields = [
                'chat_id'   => $chat_id,
                'media' => json_encode([  
                    ['type' => 'document', 'media' => 'attach://file0'],
                    ['type' => 'document', 'media' => 'attach://file1'],
                    ['type' => 'document', 'media' => 'attach://file2', 'caption' => $txt]
                ])
            ];
        } else if ($file_count == 4) {
            $post_fields = [
                'chat_id'   => $chat_id,
                'media' => json_encode([  
                    ['type' => 'document', 'media' => 'attach://file0'],
                    ['type' => 'document', 'media' => 'attach://file1'],
                    ['type' => 'document', 'media' => 'attach://file2'],
                    ['type' => 'document', 'media' => 'attach://file3', 'caption' => $txt] 
                ])
            ];
        }

        $files = $_FILES['File'];
        foreach ($files['name'] as $key => $value) {
            $target_file = $target_dir . basename($files["name"][$key]);
            
            if (move_uploaded_file($files["tmp_name"][$key], $target_file)) {
                $media["file{$key}"] = new CURLFile(realpath($target_file)); // Используем имя файла в формате file{$key}
            } else {
                $file = "Ошибка сохранения файла";
            }
        }
        
        $ch = curl_init();
        $options = [
            CURLOPT_URL => "https://api.telegram.org/bot{$token}/sendMediaGroup",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_fields + $media, // Совмещаем массивы $post_fields и $media
            CURLOPT_HTTPHEADER => ["Content-Type:multipart/form-data"]
        ];
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
    } else {
        $post_fields = [
            'chat_id'   => $chat_id,
            'text' => $txt
        ];
        $ch = curl_init();
        $options = [
            CURLOPT_URL => "https://api.telegram.org/bot{$token}/sendMessage", // Изменим sendDocument на sendMessage
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post_fields
        ];
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
    }

}

?>