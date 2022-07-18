<?php

namespace Classes;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Classes\Conversation;
use Classes\User;
use Classes\Customer;
use Classes\Consultant;

class Chat implements MessageComponentInterface
{
    protected $clients;
    private $subscriptions;
    private $users;
    private $accounts;
    private $user;
    private $conversation;
    private $customer;
    private $consultant;
    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->subscriptions = [];
        $this->users = [];
        $this->conversations = [];
        $this->accounts = [];
        $this->admin = [];
        $this->agents = [];
        $this->guests = [];
        $this->user = new User();
        $this->conversation = new Conversation();
        $this->customer = new Customer();
        $this->consultant = new Consultant();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $content = json_decode($msg);
        switch ($content->command) {
            case "writing":
                $dta = array();
                $dta["sender"] = $content->sender;
                $dta["receiver"] = $content->receiver;
                $dta["action"] = "conversationStatus";
                $dta["status"] = 200;
                if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                    if (array_key_exists($this->accounts[$content->id_group][$content->receiver], $this->users)) {
                        $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dta));
                    }
                }
                if ($content->status == 1) {
                    $dta = array();
                    $dta["sender"] = $content->sender;
                    $dta["receiver"] = $content->receiver;
                    $dta["action"] = "writing";
                    $dta["status"] = 200;
                    if (isset($this->admin[$content->id_group])) {
                        foreach ($this->admin[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dta));
                            }
                        }
                    }
                    if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                        $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dta));
                    }
                } else {
                    $dta = array();
                    $dta["sender"] = $content->sender;
                    $dta["receiver"] = $content->receiver;
                    $dta["action"] = "stopWriting";
                    $dta["status"] = 200;
                    if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                        $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dta));
                    }
                }
                break;
            case "attachAccount":
                $dt = array();
                if ($content->role == 2) {
                    if (!isset($this->admin[$content->id_group])) {
                        $this->admin[$content->id_group] = array();
                    }
                    $this->admin[$content->id_group][] = $conn->resourceId;
                } else {
                    $dt["id_user"] = $content->account;
                    $dt["status"] = 200;
                    $dt["action"] = "newConnection";
                    $dt["name"] = $content->name;
                    $dt["role"] = $content->role;
                    $dt["sender_avatar"] = $content->sender_avatar;
                    if (isset($this->accounts[$content->id_group])) {
                        foreach ($this->accounts[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dt));
                            }
                        }
                    } else {
                        $this->accounts[$content->id_group] = array();
                    }
                    $this->accounts[$content->id_group][$content->account] = $conn->resourceId;
                    if ($content->role == 3) {
                        $this->conversations[$content->account] = [];
                        $this->agents[$content->id_group][$content->account] = $conn->resourceId;
                    }
                    if ($content->role == 7) {
                        $this->guests[$content->id_group][$content->account] = $conn->resourceId;
                    }
                    if (isset($this->admin[$content->id_group])) {
                        foreach ($this->admin[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dt));
                            }
                        }
                    }
                }
                $dt = array();
                $dt["status"] = 200;
                $dt["action"] = 'connected';
                $dt["users"] = isset($this->accounts[$content->id_group]) ? array_keys($this->accounts[$content->id_group]) : [];
                $dt["agents"] = isset($this->agents[$content->id_group]) ? array_keys($this->agents[$content->id_group]) : [];
                $dt["guests"] = isset($this->guests[$content->id_group]) ? array_keys($this->guests[$content->id_group]) : [];
                $dt["conversations"] = array_map(function ($agentconv) {
                    return count(array($agentconv));
                }, $this->conversations);
                $this->users[$conn->resourceId]->send(json_encode($dt));
                break;
            case "subscribe":
                $this->subscriptions[$conn->resourceId] = $content->channel;
                break;
            case "affected_guest":
                if ($content->id_guest != null &&  $content->id_agent != null) {
                    $dt = array();
                    $dt["id_guest"] = $content->id_guest;
                    $dt["id_agent"] = $content->id_agent;
                    $dt["status"] = 200;
                    $dt["action"] = 'affect_guest';
                    if (isset($this->accounts[$content->id_group])) {
                        foreach ($this->accounts[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dt));
                            }
                        }
                    }
                    if (isset($this->admin[$content->id_group])) {
                        foreach ($this->admin[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dt));
                            }
                        }
                    }
                }
                break;
            case "connected":
                $dt = array();
                $dt["status"] = 200;
                $dt["action"] = 'connected';
                $dt["users"] = isset($this->accounts[$content->id_group]) ? array_keys($this->accounts[$content->id_group]) : [];
                $dt["guests"] = isset($this->accounts[$content->id_group]) ? array_keys($this->accounts[$content->id_group]) : [];
                $conn->send(json_encode($dt));
                break;
            case "openConversation":
                if ($content->sender != null &&  $content->receiver != null) {
                    //j'ai corriger l'ouverture de conversation
                    $this->conversation->updateConversationStatus(['sender'=>$content->sender,'sender_role'=>$content->sender_role,'receiver'=>$content->receiver,'receiver_role'=>$content->receiver_role,]);
                    $dta = array();
                    $dta["sender"] = $content->sender;
                    $dta["receiver"] = $content->receiver;
                    $dta["action"] = "conversationStatus";
                    $dta["status"] = 200;
                    if (isset($this->admin[$content->id_group])) {
                        foreach ($this->admin[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dta));
                            }
                        }
                    }
                    if (is_array($this->accounts[$content->id_group]) && array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                        if (array_key_exists($this->accounts[$content->id_group][$content->receiver], $this->users)) {
                            $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dta));
                        }
                    }
                    $dta["action"] = "total_unread_messages";
                    $total_unread_messages =  $this->conversation->countUnreadMessages($content->receiver);
                    $dta["total_unread_messages"] = $total_unread_messages;
                    if (is_array($this->accounts[$content->id_group]) && array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                        if (array_key_exists($this->accounts[$content->id_group][$content->receiver], $this->users)) {
                            $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dta));
                        }
                    }
                }
                break;
            case "message":
                if (($content->sender != null || $content->sender == 0) &&  ($content->receiver != null || $content->receiver == 0) && $content->msg != null) {
                    $dta = array();
                    $dta["sender"] = $content->sender;
                    $dta["sender_role"] = $content->sender_role;
                    $dta["receiver"] = $content->receiver;
                    $dta["receiver_role"] = $content->receiver_role;
                    $dta["content"] = $content->msg;
                    if (isset($content->admin)) {
                        $dta["admin"] = $content->admin;
                    }
                    if ($content->sender_role == 7) {
                        $dt = array();
                        $customer_fullName = $this->customer->getFullName($content->customer_id);
                        $dt["sender"] = $content->sender;
                        $dt["sender_role"] = $content->sender_role;
                        $dt["receiver"] = $content->receiver;
                        $dt["receiver_role"] = $content->receiver_role;
                        $dt["message"] = $content->msg;
                        $dt["action"] = "newMessage";
                        $dt["status"] = 200;

                        $create = $this->conversation->storeMessage($dta);
                        $conn->send(json_encode($dt));
                        if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                            $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dt));
                        }
                        if (isset($this->admin[$content->id_group])) {
                            foreach ($this->admin[$content->id_group]  as $user) {
                                if (isset($this->users[$user])) {
                                    $this->users[$user]->send(json_encode($dt));
                                }
                            }
                        }
                    }
                    if ($content->sender_role == 4) {
                        $checkBalance = $this->customer->checkBalance($content->customer_id);
                        if ($checkBalance) {
                            $create = $this->conversation->storeMessage($dta);
                            if ($create) {
                                $newBalance = 0;
                                if ($content->unlimited == 0) {
                                    $newBalance = $this->customer->updateBalance($content->customer_id);
                                }
                                $dt = array();
                                $customer_fullName = $this->customer->getFullName($content->customer_id);
                                $dt["sender"] = $content->sender;
                                $dt["sender_role"] = $content->sender_role;
                                $dt["receiver"] = $content->receiver;
                                $dt["receiver_role"] = $content->receiver_role;
                                $dt["customer_fullName"] = $customer_fullName;
                                $dt["message"] = $content->msg;
                                $dt["action"] = "newMessage";
                                $dt["balance"] = $newBalance;
                                $dt["status"] = 200;

                                $this->users[$conn->resourceId]->send(json_encode($dt));
                                if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                                    $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dt));
                                }
                                if (isset($this->admin[$content->id_group])) {
                                    foreach ($this->admin[$content->id_group]  as $user) {
                                        if (isset($this->users[$user])) {
                                            $this->users[$user]->send(json_encode($dt));
                                        }
                                    }
                                }
                                $dtb["action"] = "balance";
                                $dtb["balance"] = $newBalance;
                                $dtb["status"] = 200;
                                $this->users[$conn->resourceId]->send(json_encode($dtb));
                                if (array_key_exists($content->account, $this->accounts[$content->id_group])) {
                                    $this->users[$this->accounts[$content->id_group][$content->account]]->send(json_encode($dtb));
                                }
                                $dtb["action"] = "total_unread_messages";
                                $total_unread_messages =  $this->conversation->countUnreadMessages($content->receiver);
                                $dtb["total_unread_messages"] = $total_unread_messages;
                                if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                                    $this->users[$this->accounts[$content->id_group][$content->account]]->send(json_encode($dtb));
                                }
                            } else {
                                $dt = array();
                                $dt["sender"] = $content->sender;
                                $dt["message"] = $content->msg;
                                $dt["action"] = "newMessage";
                                $dt["status"] = 501;
                                $this->users[$conn->resourceId]->send(json_encode($dt));
                            }
                        } else {
                            $dt = array();
                            $dt["sender"] = $content->sender;
                            $dt["message"] = $content->msg;
                            $dt["action"] = "newMessage";
                            $dt["newBalance"] = 0;
                            $dt["status"] = 201;
                            $this->users[$conn->resourceId]->send(json_encode($dt));
                        }
                    }
                    if ($content->sender_role == 3) {
                        $create = $this->conversation->storeMessage($dta);
                        if ($create) {
                            $dt = array();
                            $consultant_fullName = $this->consultant->getFullName($content->consultant_id);
                            $dt["consultant_fullName"] = $consultant_fullName;
                            $dt["sender"] = $content->sender;
                            $dt["sender_role"] = $content->sender_role;
                            $dt["receiver"] = $content->receiver;
                            $dt["receiver_role"] = $content->receiver_role;
                            $dt["message"] = $content->msg;
                            $dt["action"] = "newMessage";
                            $dt["status"] = 200;
                            if (isset($content->admin)) {
                                $dt["admin"] = $content->admin;
                                if (array_key_exists($content->sender, $this->accounts[$content->id_group])) {
                                    $this->users[$this->accounts[$content->id_group][$content->sender]]->send(json_encode($dt));
                                }
                            }
                            if (!isset($content->admin)) {
                                $this->users[$conn->resourceId]->send(json_encode($dt));
                            }
                            if (isset($this->admin[$content->id_group])) {
                                    foreach ($this->admin[$content->id_group]  as $user) {
                                        if (isset($this->users[$user])) {
                                            $this->users[$user]->send(json_encode($dt));
                                        }
                                    }
                                }                            
                            
                            //$conn->send(json_encode($this->accounts));
                            if (array_key_exists($content->receiver, $this->accounts[$content->id_group]) && $content->admin != 4) {
                                $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dt));
                            }

                            $dt["action"] = "total_unread_messages";
                            $total_unread_messages =  $this->conversation->countUnreadMessages($content->account);
                            $dt["total_unread_messages"] = $total_unread_messages;
                            if (array_key_exists($content->receiver, $this->accounts[$content->id_group])) {
                                $this->users[$this->accounts[$content->id_group][$content->receiver]]->send(json_encode($dt));
                                $this->conversations[$this->accounts[$content->sender][$content->receiver]] = $content->receiver;
                            }
                        } else {
                            $dt = array();
                            $dt["sender"] = $content->sender;
                            $dt["message"] = $content->msg;
                            $dt["action"] = "newMessage";
                            $dt["status"] = 201;
                            $this->users[$conn->resourceId]->send(json_encode($dt));
                        }
                    }
                    if (intval($this->conversation->checkFirst($content->sender)->checking) == 0) {
                        if ($content->receiver == 0) {
                            $dt = array();
                            $dt["sender"] = $content->receiver;
                            $dt["receiver"] = $content->sender;
                            $dt["sender_role"] = $content->receiver_role;
                            $dt["receiver_role"] = $content->sender_role;
                            $dt["content"] = $this->conversation->getdefaultMessage($content->id_account)->agent_default_message;
                            $dt["action"] = "newMessage";
                            $dt["status"] = 200;
                            $this->conversation->storeMessage($dt);
                            $dt["message"] = $dt["content"];
                            $conn->send(json_encode($dt));
                            if (isset($this->admin[$content->id_group])) {
                                foreach ($this->admin[$content->id_group]  as $user) {
                                    if (isset($this->users[$user])) {
                                        $this->users[$user]->send(json_encode($dt));
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 'reassign':
                if ($content->oldExpert != null &&  $content->newExpert != null && $content->customer != null) {
                    $dta = array();
                    $dta["oldExpert"] = $content->oldExpert;
                    $dta["newExpert"] = $content->newExpert;
                    $dta["pseudo"] = $content->pseudo;
                    $dta["avatar"] = $content->avatar;
                    $dta["status"] = 200;
                    $dta["action"] = 'reassign';
                    $this->users[$this->accounts[$content->id_group][$content->customer]]->send(json_encode($dta));
                }
                break;
            case 'redistribute':
                $dta = array();
                $dta["customer"] = $content->customer;
                $dta["agent"] = $content->agent;
                $dta["action"] = 'redistribute';
                $dta["status"] = 200;
                if (isset($this->admin[$content->id_group])) {
                    foreach ($this->admin[$content->id_group]  as $user) {
                        if (isset($this->users[$user])) {
                            $this->users[$user]->send(json_encode($dta));
                        }
                    }
                }
                foreach ($this->accounts as $account) {
                    if (array_key_exists($content->agent, $account)) {
                        $this->users[$account[$content->agent]]->send(json_encode($dta));
                    }
                }
                break;
            case 'buying':
                $dta = array();
                $dta["sender"] = $content->sender;
                $dta["receiver"] = $content->receiver;
                $dta["action"] = 'buying';
                $dta["status"] = 200;
                $dta["is_buying"] = $content->is_buying;
                if (isset($this->admin[$content->id_group])) {
                    foreach ($this->admin[$content->id_group]  as $user) {
                        if (isset($this->users[$user])) {
                            $this->users[$user]->send(json_encode($dta));
                        }
                    }
                }
                foreach ($this->accounts as $account) {
                    if (array_key_exists($content->receiver, $account)) {
                        $this->users[$account[$content->receiver]]->send(json_encode($dta));
                    }
                }
                break;
            case 'notification':
                $dta = array();
                $dta["sender"] = $content->sender;
                $dta["receiver"] = $content->receiver;
                $dta["id_account"] = $content->id_group;
                $dta["type"] = $content->action;
                $date = date("Y-m-d H:i:s");
                $create = $this->conversation->storeNotification($dta);
                if ($create) {
                    $dta["date"] = $date;
                    $dta["sender_name"] = $content->customer;
                    $dta["receiver_name"] = $content->consultant;
                    $dta["status"] = 200;
                    $dta["action"] = 'notification';
                    if (isset($this->admin[$content->id_group])) {
                        foreach ($this->admin[$content->id_group]  as $user) {
                            if (isset($this->users[$user])) {
                                $this->users[$user]->send(json_encode($dta));
                            }
                        }
                    }
                }
                break;
            case 'guestStatus':
                // $conn->send($msg);
                $dt = array();
                $dt["action"] = 'changestat';
                $dt["id_guest"] = $content->id_guest;
                $dt["id_agent"] = $content->id_agent;
                $dt["id_user"] = $content->id_user;
                $dt["new_cust"] = $content->new_cust;
                $dt["firstname"] = $content->firstname;
                $dt["lastname"] = $content->lastname;
                $dt["balance"] = $content->balance;
                $dt["title"] = $content->title;
                $dt["status"] = $content->status;
                foreach ($this->accounts as $account) {
                    if (array_key_exists($content->id_guest, $account)) {
                        $this->users[$account[$content->id_guest]]->send(json_encode($dt));
                    }
                    if (array_key_exists($content->id_user, $account)) {
                        $this->users[$account[$content->id_user]]->send(json_encode($dt));
                    }
                    if (array_key_exists($content->id_agent, $account)) {
                        $this->users[$account[$content->id_agent]]->send(json_encode($dt));
                    }
                }
                break;
        }
    }

    public function onSubscribe(ConnectionInterface $conn, $channel)
    {
        echo "New Subscribe! ({$conn->resourceId})\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        $dt = array();
        $found = false;
        foreach ($this->admin as $key => $admin) {
            $key2 = array_search($conn->resourceId, $admin);
            if ($key2 != false) {
                unset($this->admin[$key][$key2]);
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            foreach ($this->accounts as $key => $account) {
                if (array_search($conn->resourceId, $account)) {
                    $dt["id_user"] = array_search($conn->resourceId, $account);
                    unset($this->accounts[$key][$dt["id_user"]]);
                    if (isset($this->guests[$key][$dt["id_user"]])) {
                        unset($this->guests[$key][$dt["id_user"]]);
                    }
                    if (isset($this->agents[$key][$dt["id_user"]])) {
                        unset($this->agents[$key][$dt["id_user"]]);
                    }
                    $dt["status"] = false;
                    $this->user->setStatus($dt);
                    $dt["status"] = 200;
                    $dt["action"] = "closedConnection";
                    foreach ($account as $us) {
                        if (isset($this->users[$us])) {
                            $this->users[$us]->send(json_encode($dt));
                        }
                    } 
                    if(isset($this->admin[$key])){
                        foreach ($this->admin[$key] as $us) {
                            if (isset($this->users[$us])) {
                                $this->users[$us]->send(json_encode($dt));
                            }
                        }
                    }
                    break;
                }
            }
        }
        if (isset($this->users[$conn->resourceId]))
            unset($this->users[$conn->resourceId]);
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->send("An error has occurred: {$e->getMessage()}\n");
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
