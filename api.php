<?php
    
    class TelegramAPI {
        public $api;
        public $updates;
        public $ChatID;
        public $text;
        public $message;
        public $from;
        public $query;
        public $queryFrom;
        public $queryUsername;
        public $queryChatID;
        public $queryName;
        public $queryID;
        public $queryUserID;
        public $queryData;
        public $queryMsgID;
        public $userID;
        public $userName;
        
        public function __construct($token) {
            $this->api = "https://api.telegram.org/bot".$token;
            $this->updates = json_decode(file_get_contents("php://input"), true);
            $this->message = $this->updates["message"];
            $this->from = $this->message["from"];
            $this->userID = $this->from["id"];
            $this->userName = $this->from["first_name"];
            $this->ChatID = $this->message["chat"]["id"];
            $this->text = $this->message["text"];
            $this->query = $this->updates["callback_query"];
            $this->queryFrom = $this->query["from"];
            $this->queryUsername = $this->queryFrom["username"];
            $this->queryChatID = $this->update['callback_query']['message']['chat']['id'];
            $this->queryName = $this->queryFrom["first_name"];
            $this->queryID = $this->query["id"];
            $this->queryUserID = $this->queryFrom["id"];
            $this->queryData = $this->query["data"];
            $this->queryMsgID = $this->query["message"]["message_id"];
        }
        
        public function curlRequest($method, $args)
        {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $this->api."/".$method);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, $args);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $r = curl_exec($c);
            curl_close($c);
            return json_decode($r, true);
        }
        
        public function sendMessage($text, $KeyBoard = NULL, $KType = "rimuovi",$reply = NULL) {
            $args = [
                      'chat_id' => $this->ChatID,
                      'text' => $text,
                      'reply_to_message_id' => $reply,
                      "parse_mode" => "HTML",
                    ];
 
            if($KType == "inline") {
              if($KeyBoard != NULL) {
                $args['reply_markup'] = json_encode([
                                                      'inline_keyboard' => $KeyBoard,
                                                      'resize_keyboard' => true,
                                                    ]);
              }
            } else if($KType == "fisica") {
                    if($KeyBoard != NULL) {
                        $args['reply_markup'] = json_encode([
                                                              'keyboard' => $KeyBoard,
                                                              'resize_keyboard' => true,
                                                            ]);
                    }
                } else if($KType == "rimuovi") {
                        $args['reply_markup'] = json_encode([
                                                              'remove_keyboard' => true,
                                                            ]);
                    } else {
                            $args['text'] = "Errore, controlla il KeyBoard type";
                    }
                
            return $this->curlRequest("sendMessage", $args);
        }
        
        public function sendPhoto($file_id, $caption="", $KeyBoard = NULL, $KType = "rimuovi",$reply = NULL) {
            $args = [
                  'chat_id' => $this->ChatID,
                  'photo' => $file_id,
                  'caption' => $caption,
                  'reply_to_message_id' => $reply,
                  "parse_mode" => "HTML",
                ];
            
            if($KType == "inline") {
              if($KeyBoard != NULL) {
                $args['reply_markup'] = json_encode([
                                                      'inline_keyboard' => $KeyBoard,
                                                      'resize_keyboard' => true,
                                                    ]);
              }
            } else if($KType == "fisica") {
                    if($KeyBoard != NULL) {
                        $args['reply_markup'] = json_encode([
                                                              'keyboard' => $KeyBoard,
                                                              'resize_keyboard' => true,
                                                            ]);
                    }
                } else if($KType == "rimuovi") {
                        $args['reply_markup'] = json_encode([
                                                              'remove_keyboard' => true,
                                                            ]);
                    } else {
                            $args['text'] = "Errore, controlla il KeyBoard type";
                    }
            
            return $this->curlRequest('sendPhoto', $args);
        }
        
        public function answerQuery($text = "", $persistent = false) {
            $args = [
                      "callback_query_id" => $this->queryID,
                      "text" => $text,
                      "show_alert" => $persistent,
                    ];
            
            return $this->curlRequest('answerCallbackQuery', $args);
        }

        public function connect($cnn, $db_user, $db_psw) {
          return $db = new PDO($cnn , "$db_user", "$db_psw");
        }

        public function singleFetch($DB, $Query) {
          $Result = $DB->query($Query);
          return $Result->fetch(PDO::FETCH_ASSOC);
        }

        public function multipleFetch($DB, $Query) {
          $Array = array();
          $QueryResult = $DB->query($Query);
          $QueryResult->setFetchMode(PDO::FETCH_ASSOC);
          while($DinamicResult = $QueryResult->fetch()){
            array_push($Array, $DinamicResult);
          }
          return $Array;
        }

        public function dynamicInlineButton($Array, $filter) {
            $NewArr = array();
            for($i = 0; $i<count($Array); $i++) {
                array_push($NewArr, [
                                        ["text" => $Array[$i][$filter], "callback_data" => $Array[$i][$filter]],
                                    ],
                                );
            }
            return $NewArr;
        }
    }
    
    //Debug: $this->sendMessage(json_encode($args, JSON_PRETTY_PRINT));
?>