<?php

require_once('database/dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Event.php');
// include_once(dirname(__FILE__).'/../domain/Animal.php');
date_default_timezone_set("America/New_York");

function get_user_messages($userID) {
    $query = "select * from dbmessages
              where recipientID='$userID'
              order by prioritylevel desc";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($messages as &$message) {
        foreach ($message as $key => $value) {
            $message[$key] = htmlspecialchars($value);
        }
    }
    unset($message);
    mysqli_close($connection);
    return $messages;
}

function get_user_unread_messages($userID) {
    $query = "select * from dbmessages
              where recipientID='$userID' AND wasread = 0
              order by time ASC";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($messages as &$message) {
        foreach ($message as $key => $value) {
            $message[$key] = htmlspecialchars($value);
        }
    }
    unset($message);
    mysqli_close($connection);
    return $messages;
}
function get_user_read_messages($userID) {
    $query = "select * from dbmessages
              where recipientID='$userID' AND wasread = 1
              order by time ASC";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
    foreach ($messages as &$message) {
        foreach ($message as $key => $value) {
            $message[$key] = htmlspecialchars($value);
        }
    }
    unset($message);
    mysqli_close($connection);
    return $messages;
}

function get_user_unread_count($userID) {
    $query = "select count(*) from dbmessages 
        where recipientID='$userID' and wasRead=0";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }

    $row = mysqli_fetch_row($result);
    mysqli_close($connection);
    return intval($row[0]);
}

function get_message_by_id($id) {
    $query = "select * from dbmessages where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    mysqli_close($connection);
    if ($row == null) {
        return null;
    }
    foreach ($row as $key => $value) {
        $row[$key] = htmlspecialchars($value);
    }
    $row['body'] = str_replace("\r\n", "<br>", $row['body']);
    return $row;
}

function send_message($from, $to, $title, $body) {
    $time = date('Y-m-d-H:i');
    $connection = connect();
    $title = mysqli_real_escape_string($connection, $title);
    $body = mysqli_real_escape_string($connection, $body);
    $query = "insert into dbmessages
        (senderID, recipientID, title, body, time)
        values ('$from', '$to', '$title', '$body', '$time')";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $id = mysqli_insert_id($connection);
    mysqli_close($connection);
    return $id; // get row id
}

function send_system_message($to, $title, $body) {
    send_message('vmsroot', $to, $title, $body);
}

function mark_read($id) {
    $query = "update dbmessages set wasRead=1
              where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return false;
    }
    mysqli_close($connection);
    return true;
}

function mark_all_as_read($userID) {
    $query = "update dbmessages set wasRead=1
              where recipientID='$userID'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return false;
    }
    mysqli_close($connection);
    return true;
}

function message_all_users_of_types($from, $types, $title, $body) {
    $types = implode(', ', $types);
    $time = date('Y-m-d-H:i');
    $query = "select id from dbpersons where type in ($types)";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_NUM);
    foreach ($rows as $row) {
        $to = $row[0];
        $query = "insert into dbmessages (senderID, recipientID, title, body, time, wasRead, prioritylevel)
                  values ('$from', '$to', '$title', '$body', '$time', 0, 0)";
        $result = mysqli_query($connection, $query);
    }
    mysqli_close($connection);    
    return true;
}

function message_all_volunteers($from, $title, $body) {
    return message_all_users_of_types($from, ['"volunteer"'], $title, $body);
}

function system_message_all_volunteers($title, $body) {
    return message_all_users_of_types('vmsroot', ['"volunteer"'], $title, $body);
}

function message_all_admins($from, $title, $body) {
    return message_all_users_of_types($from, ['"admin"', '"superadmin"'], $title, $body);
}

function system_message_all_admins($title, $body) {
    return message_all_users_of_types('vmsroot', ['"admin"', '"superadmin"'], $title, $body);
}

function system_message_all_users_except($except, $title, $body) {
    $time = date('Y-m-d-H:i');
    $query = "select id from dbpersons where id!='$except'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_NUM);
    foreach ($rows as $row) {
        $to = $row[0];
        $query = "insert into dbmessages (senderID, recipientID, title, body, time)
                  values ('vmsroot', '$to', '$title', '$body', '$time')";
        $result = mysqli_query($connection, $query);
    }
    mysqli_close($connection);    
    return true;
}

//function to go through all users within the database of user accounts and send them a notification given a title and body 
function message_all_users($from, $title, $body) {
    $time = date('Y-m-d-H:i');
    $query = "select id from dbpersons";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_NUM); //get all the users in the database dbPersons
    foreach ($rows as $row) { //for every user in db person, generate a notification
        $to = json_encode($row); //converting the array of users into strings to put into the database of messages
        $to = substr($to,2,-2); //getting rid of the brackets and quotes in the string: ie - ["user"]
        $query = "insert into dbmessages (senderID, recipientID, title, body, time)
                  values ('$from', '$to', '$title', '$body', '$time')"; //inserting the notification in that users inbox
        $result = mysqli_query($connection, $query); 
    }
    mysqli_close($connection);    
    return true;
}

function message_all_users_prio($from, $title, $body, $prio) {
    $time = date('Y-m-d-H:i');
    $query = "select id from dbpersons where id!='$from'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $rows = mysqli_fetch_all($result, MYSQLI_NUM); //get all the users in the database dbPersons
    foreach ($rows as $row) { //for every user in db person, generate a notification
        $to = json_encode($row); //converting the array of users into strings to put into the database of messages
        $to = substr($to,2,-2); //getting rid of the brackets and quotes in the string: ie - ["user"]
        $query = "insert into dbmessages (senderID, recipientID, title, body, time, prioritylevel)
                  values ('$from', '$to', '$title', '$body', '$time', '$prio')"; //inserting the notification in that users inbox
        $result = mysqli_query($connection, $query); 
    }
    mysqli_close($connection);    
    return true;
}
function delete_message($id) {
    $query = "delete from dbmessages where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}
function delete_all_messages_for_user($userId) {
    $query = "DELETE FROM dbmessages WHERE recipientID = '$userId'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}
function delete_messages_by_ids($ids, $userID) {
    $ids_str = implode(',', array_map('intval', $ids));
    $query = "DELETE FROM dbmessages WHERE recipientID='$userID' AND id IN ($ids_str)";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    return boolval($result);
}