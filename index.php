<?php
    include 'api.php';
    include 'config.php';

    function isIn($value, $arr) {
        for($i = 0; $i < count(($arr)); $i++) {
            if($value == $arr[$i]) return true;
        }
        return false;
    }

    $Silksong = new TelegramAPI($_GET["token"]);

    try{
        $DB = $Silksong->connect($cnn, $db_user, $db_psw);
    }catch(PDOException $e){
        $Silksong->sendMessage($e->getMessage());
    }
    
    $Menu = [
                [
                    ['text' => "Coming soon...", 'callback_data' => "Info"],
                    ['text' => "Coming soon...", 'callback_data' => "Info"],
                ],
                [
                    ['text' => "Coming soon...", 'callback_data' => "Info"],
                ],
            ];
    
    if(isset($Silksong->text)) {
        switch($Silksong->text) {
            case "/start":
                try{
                    $checkID = $Silksong->multipleFetch($DB,  "SELECT UserID FROM User");

                    if(!isIn($Silksong->userID, $checkID)) $DB->exec("INSERT INTO User (UserID, Name) VALUES ('$Silksong->userID','$Silksong->userName');");
                    
                    $totalUser = $DB->query('select count(UserID) from User')->fetchColumn(); // Prende un singolo elemento dalla query
                    
                    $Silksong->sendMessage("Al momento il bot è stato avviato da: ". $totalUser ." utenti diversi.");
                    $Silksong->sendMessage("Per qualsiasi problema contattatemi qui: <b>@The_LukeBot</b>.\nIn attesa che Silksong venga rilasciato...");
                    $Silksong->sendPhoto("https://lucalaspina.netsons.org/SilksongBot/image/1.jpg", "", $Menu, "inline");
                    
            
                }catch(PDOException $e){
                    $Silksong->sendMessage($e->getMessage());
                }
            break;
            
            case "/try":
                try{
                    $arr = $Silksong->multipleFetch($DB,  "SELECT UserID FROM User");

                    $Keyboard = $Silksong->dynamicInlineButton($arr, "UserID");
                    $Silksong->sendMessage(json_encode($arr), $Keyboard, "inline");
                    
                }catch(PDOException $e){
                    $Silksong->sendMessage($e->getMessage());
                }
            break;

            default:
                $Silksong->sendMessage("Silksong uscirà nel 2048, usa /start");
            break;
        }
    }
    
    if(isset($Silksong->query)) {
        switch($Silksong->queryData) {
            case "Info":
                $Silksong->answerQuery("Coming soon...", true);
            break;
            
            default:
                $Silksong->answerQuery($Silksong->queryData, true);
            break;
        }
    }

    $DB = null;
?>