<?php
require "../../vendor/autoload.php";
ini_set("display_errors", 1);

use Classes\DataService;
// BASE DE DONNEES
try {
   $ds = new DataService();
   $conn = $ds->conn;
} catch (Exception $e) {
   die('Erreur : ' . $e->getMessage());
}
$icons = array('pdf' => '../../assets/icons/svg/pdf.svg', 'txt' => '../../assets/icons/svg/text.svg', 'doc' => '../../assets/icons/svg/word.svg', 'docx' => '../../assets/icons/svg/word.svg', 'csv' => '../../assets/icons/svg/spreadsheet.svg', 'xlsx' => '../../assets/icons/svg/spreadsheet.svg');
function formatSizeUnits($bytes)
{
   if ($bytes >= 1073741824) {
      $bytes = number_format($bytes / 1073741824, 2) . ' GB';
   } elseif ($bytes >= 1048576) {
      $bytes = number_format($bytes / 1048576, 2) . ' MB';
   } elseif ($bytes >= 1024) {
      $bytes = number_format($bytes / 1024, 2) . ' KB';
   } elseif ($bytes > 1) {
      $bytes = $bytes . ' bytes';
   } elseif ($bytes == 1) {
      $bytes = $bytes . ' byte';
   } else {
      $bytes = '0 bytes';
   }
   return $bytes;
}
$type = $_POST['action'];
if ($type == "moveFile") {
   $id = intval($_POST['id']);
   $sender = intval($_POST['sender']);
   $receiver = intval($_POST['receiver']);
   if ($_POST['type'] == 1) $dirLogo = '../../uploads/messages/files/';
   else $dirLogo = '../../uploads/messages/pictures/';
   $account = $conn->prepare("SELECT storage,max_size FROM websites WHERE id_website=:ID");
   $account->bindParam(':ID', $id, PDO::PARAM_INT);
   $account->execute();
   $storage = $account->fetchObject();
   $files = glob($dirLogo . "*-" . $id . ".*");
   $totalsize = 0;
   foreach ($files as $file) {
      $totalsize += filesize($file);
   }
   $size = number_format(intval($_FILES['file']['size']) / 1048576, 2);
   if ($size < $storage->max_size) {
      if (floatval($storage->storage) - number_format(intval($totalsize) / 1048576, 2) > $size) {
         $uploadFile = $_FILES["file"]["name"];
         $uploadFileTmp = $_FILES["file"]["tmp_name"];
         $fileData1 = pathinfo(basename($uploadFile));
         $Filenom = basename($uploadFile, "." . $fileData1['extension']);
         $photo = $Filenom  . '-' . $sender . '-' . $receiver . '-' . $id . '.' . $fileData1['extension'];
         $target_path1 = ($dirLogo . $photo);
         $max = count(glob('../../uploads/messages/pictures/' . "*-" . $sender . "-" . $receiver . "-" . $id . ".*"));
         while (file_exists($target_path1)) {
            $photo = $Filenom . '(' . $max++ . ')'  . '-' . $sender . '-' . $receiver . '-' . $id . '.' . $fileData1['extension'];
            $target_path1 = ($dirLogo . $photo);
         }
         if (move_uploaded_file($uploadFileTmp, $target_path1)) {
            if ($_POST['type'] == 1) {
               echo json_encode(array('status' => 200, 'content' => '<div class="el-element-overlay">
            <div class="el-card-item">
            <div class="el-overlay-1 d-flex  justify-content-end align-items-center p-10">
            <img src="' . $icons[$fileData1['extension']] . '" alt="user" style="height:30px;width:30px" />
            <div class="m-l-10 text-left">
               <span>' . $Filenom . "." . $fileData1['extension'] . '</span><br>
               <small class="text-muted">' . formatSizeUnits($_FILES['file']['size']) . '</small>
            </div>
            <div class="el-overlay">
            <ul class="el-info">
            <li><a href="' . $target_path1 . '" target="_blank "><i class="mdi mdi-eye" title="see"></i></a></li>
            <li><a href="'  . $target_path1 . '" download="PrivateChat_' . $Filenom . '" target="_blank "><i class="mdi mdi-download " title="download "></i></a></li>
            </ul></div></div></div></div>'));
            } else if ($_POST['type'] == 2) {
               echo json_encode(array('status' => 200, 'content' => '<div class="el-element-overlay" >
            <div class="el-card-item">
            <div class="el-overlay-1">
            <img src="' . $target_path1 . '" alt="user" class="user"/>
            <div class="el-overlay">
            <ul class="el-info">
            <li><a href="' . $target_path1 . '" target="_blank "><i class="mdi mdi-eye" title="see"></i></a></li>
            <li><a href="'  . $target_path1 . '" download="PrivateChat_' . $Filenom . '" target="_blank "><i class="mdi mdi-download " title="download "></i></a></li>
            </ul></div></div></div></div>'));
            }
         } else {
            echo json_encode($_FILES['file']['error']);
         }
      } else {
         echo json_encode(array('status' => 201));
      }
   } else {
      echo json_encode(array('status' => 202, 'max_size' => $storage->max_size));
   }
} elseif ($type == "getFiles") {
   $id = intval($_POST['id']);
   $sender = intval($_POST['sender']);
   $receiver = intval($_POST['receiver']);
   $icons = array('pdf' => '../../assets/icons/svg/pdf.svg', 'txt' => '../../assets/icons/svg/text.svg', 'doc' => '../../assets/icons/svg/word.svg', 'docx' => '../../assets/icons/svg/word.svg', 'csv' => '../../assets/icons/svg/spreadsheet.svg', 'xlsx' => '../../assets/icons/svg/spreadsheet.svg');
   $files = glob('../../uploads/messages/files/' . "*-" . $sender . "-" . $receiver . "-" . $id . ".*");
   $files1 = glob('../../uploads/messages/pictures/' . "*-" . $sender . "-" . $receiver . "-" . $id . ".*");
   $result = array();
   $result['pictures'] = array();
   $result['files'] = array();
   if (!empty($files)) {
      foreach ($files as $file) {
         $s = explode(".", basename($file));
         $s1 = explode("-", $s[0]);
         $result['files'][] = '<li><div class="el-element-overlay" style="all: unset;">
            <div class="el-card-item">
            <div class="el-overlay-1 d-flex  align-items-center p-10">
            <img src="' . $icons[pathinfo($file, PATHINFO_EXTENSION)] . '" alt="user" style="height:30px;width:30px" />
            <div class="m-l-10 text-left">
               <span>' . $s1[0] . '.' . $s[1] . '</span><br>
               <small class="text-muted">' . formatSizeUnits(filesize($file)) . '</small>
            </div>
            <div class="el-overlay">
            <ul class="el-info">
            <li><a href="' . '../../uploads/messages/files/' . basename($file) . '" target="_blank "><i class="mdi mdi-eye" title="see"></i></a></li>
            <li><a href="'  . '../../uploads/messages/files/' . basename($file) . '" download="PrivateChat_' . $s1[0] . '" target="_blank "><i class="mdi mdi-download " title="download "></i></a></li>
            </ul></div></div></div></div></li>';
      }
   }
   if (!empty($files1)) {
      foreach ($files1 as $file) {
         $s = explode(".", basename($file));
         $s1 = explode("-", $s[0]);
         $result['pictures'][] = '<div class="el-element-overlay" style="all:unset">
      <div class="el-card-item m-r-5" style="height: 80px;width: 80px;">
      <div class="el-overlay-1 d-flex  align-items-center">
      <img src="../../uploads/messages/pictures/' . basename($file) . '" alt="user" class="user" style="height:80px !important;width:80px!important"/>
      <div class="el-overlay d-flex align-items-center">
<a href="../../uploads/messages/pictures/' . basename($file) . '" target="_blank "><i class="mdi mdi-eye" title="see"></i></a>
<a href="../../uploads/messages/pictures/' . basename($file) . '" download="PrivateChat_' . $s1[0] . '.' . $s[1] . '" target="_blank "><i class="mdi mdi-download " title="download "></i></a>
</div></div></div></div>';
      }
   }
   echo json_encode($result);
} elseif ($type == "search") {
   $word = $_POST['word'];
   $sender = intval($_POST['sender']);
   $receiver = intval($_POST['receiver']);
   $messages = $conn->prepare("SELECT id_message from (SELECT id_message,sender,receiver,content FROM messages WHERE content LIKE '%$word%' and content NOT LIKE '<%') as messages");
   $messages->execute();
   $result = $messages->fetchAll();
   $resultat = array();
   foreach ($result as $message) {
      $messages = $conn->prepare("SELECT * from messages where id_message=:ID union all (select * from messages where id_message <  :ID order by id_message desc limit 5) union all (select * from messages where id_message > :ID order by id_message asc limit 5) order by  id_message DESC");
      $messages->bindParam(':ID', $message['id_message']);
      $messages->execute();
      $searched = $messages->fetchAll();
      foreach ($searched as &$se) {
         if ($se['id_message'] == $message['id_message']) {
            $se['content'] = '<span style="background-color: #fc0;border-radius: .3em;box-shadow: 2px 0 #ffcc00, -2px 0 #fc0;padding: 2px 0;">' . $se['content'] . '</span>';
         }
      }
      $resultat[] = $searched;
   }
   echo json_encode($resultat);
} elseif ($type == "report") {
   $reason = $_POST['reason']?intval($_POST['reason']):NULL;
   $subject = intval($_POST['subject']);
   $sender = intval($_POST['sender']);
   $receiver = $_POST['receiver']?intval($_POST['receiver']):NULL;
   $desc = $_POST['desc'];
   $id_account = intval($_POST['id_account']);
   $stmt = $conn->prepare('INSERT INTO contact_ticket(subject,reason,date,status,id_account,id_customer,id_consultant) VALUES (:sb,:re,NOW(),0,:IDA,:IDC,:IDE)');
    $stmt->bindParam(':IDC', $sender);
    $stmt->bindParam(':IDA', $id_account);
    $stmt->bindParam(':IDE', $receiver);
    $stmt->bindParam(':sb', $subject);
    $stmt->bindParam(':re', $reason);
    $stmt->execute();
    $affected_rows = $stmt->rowCount();
    if ($affected_rows != 0) {
        $id = $conn->lastInsertId();
        $stmt = $conn->prepare('INSERT INTO contact_messages(message,id_ticket,date,id_sender) VALUES (:msg,:IDT,NOW(),:IDS)');
        $stmt->bindParam(':IDS', $sender);
        $stmt->bindParam(':IDT', $id);
        $stmt->bindParam(':msg', $desc);
        $stmt->execute();
        $affected_rows = $stmt->rowCount();
        echo 1;
    }else echo 0;
} elseif ($type == "getContactConv") {
   $id = intval($_POST['id']);
   $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id_ticket=:id");
   $stmt->bindParam(':id', $id);
   $stmt->execute();
   $messages = $stmt->fetchAll();
   echo json_encode($messages);
}elseif ($type == "sendReponse") {
   $sender = intval($_POST['sender']);
   $id = intval($_POST['id']);
   $message = $_POST['message'];
   $stmt = $conn->prepare("INSERT INTO contact_messages(message,id_ticket,date,id_sender) values (:msg,:IDT,NOW(),:IDS)");
   $stmt->bindParam(':msg', $message);
   $stmt->bindParam(':IDT', $id);
   $stmt->bindParam(':IDS', $sender);
   $stmt->execute();
   $affected_rows = $stmt->rowCount();

   if ($affected_rows != 0) {
       $stmt = $conn->prepare("UPDATE contact_ticket SET status = 1 WHERE id_ticket=:id");
       $stmt->bindParam(':id', $id);
       $stmt->execute();
       $html = '<li><div class="chat-content"><h5>'.$_POST['sender_name'].'</h5><div class="box bg-light-info">' . $message . '</div><div class="chat-time">' . date("Y-m-d H:i:s") . '</div></div></li>';
       echo json_encode($html);
   }
}
