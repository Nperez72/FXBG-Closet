<?php /* Implemented by Aidan Meyer */

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Groups.php');

/*
 * Add a group to dbGroups table: if already there, return false
 */
function add_group($group) {
    if (!$group instanceof Group)
        die("Error: add_group type mismatch");
    $con = connect();
    $query = "SELECT * FROM dbgroups WHERE group_name = '" . $group->get_group_name() . "'";
    $result = mysqli_query($con, $query);
    
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con, 'INSERT INTO dbgroups (group_name, color_level) VALUES ("' .
            $group->get_group_name() . '", "' .
            $group->get_color_level() . '");'
        );
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*
 * Remove a group from dbGroups table. If not there, return false
 */
function remove_group($group_name) {
    $con = connect();
    $query = 'SELECT * FROM dbgroups WHERE group_name = "' . $group_name . '"';
    $result = mysqli_query($con, $query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbgroups WHERE group_name = "' . $group_name . '"';
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result ? true : false;
}

/*
 * Retrieve a group from dbGroups table matching a particular group_name.
 * If not in table, return false
 */
function retrieve_group($group_name) {
    $con = connect();
    $query = "SELECT * FROM dbgroups WHERE group_name = '" . $group_name . "'";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    $theGroup = new Group($result_row['group_name'], $result_row['color_level']);
    mysqli_close($con);
    return $theGroup;
}
function get_all_groups() {
    $con = connect();
    $query = "SELECT * FROM dbgroups";
    $result = mysqli_query($con, $query);

    // If no groups are found, return an empty array
    if (mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return [];
    }

    // Create an array of Group objects
    $groups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $group = new Group($row['group_name'], $row['color_level']);
        $groups[] = $group;
    }

    mysqli_close($con);
    return $groups;
}
/*
add a user to a volunteer group
*/
function add_user_to_group($user_id, $group_name) {
    $con = connect();  

    $query = "INSERT INTO user_groups (user_id, group_name) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Use prepared statements to prevent SQL injection
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $group_name);  

        // Execute the prepared statement and check for success
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $success = false;
    }

    mysqli_close($con);  // Close the connection after query execution
    return $success;
}

/*
Remove a user from a volunteer group
*/
function remove_user_from_group($user_id, $group_name) {
    $con = connect();  

    // Prepare the query to check if the user exists in the group
    $query = "SELECT * FROM user_groups WHERE user_id = ? AND group_name = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Bind parameters and execute the query
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $group_name); // Bind both as strings
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // If the user is not in the group, return false
        if (mysqli_num_rows($result) == 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            return false;  // User is not in the group
        }

        // Prepare and execute the delete query to remove the user
        $delete_query = "DELETE FROM user_groups WHERE user_id = ? AND group_name = ?";
        $delete_stmt = mysqli_prepare($con, $delete_query);

        if ($delete_stmt) {
            // Bind parameters for the delete query and execute
            mysqli_stmt_bind_param($delete_stmt, "ss", $user_id, $group_name); // Bind both as strings
            $delete_result = mysqli_stmt_execute($delete_stmt);
            mysqli_stmt_close($delete_stmt);
        } else {
            $delete_result = false;
        }

        mysqli_stmt_close($stmt);
    } else {
        $delete_result = false;
    }

    mysqli_close($con);  // Close the connection
    return $delete_result ? true : false;
}
function remove_all_users_in_group($group_name){
    $con = connect();

    $query = "DELETE FROM user_groups WHERE group_name = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $group_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        error_log("Statement preparation failed: " . mysqli_error($con));
        return false;
    }

    mysqli_close($con);
    return true;

}
/*
    return group name from database
*/
function get_group_name($group_name){
    $con = connect();

    // Prepare the SQL query to prevent SQL injection
    $query = "SELECT * FROM dbgroups WHERE group_name = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $group_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            mysqli_close($con);
            return new Group($row['group_name'], $row['color_level']); 
        }
    }

    mysqli_close($con);
    return false; // Group not found
}
/*
 * Get all users in a specific group
 */
function get_users_in_group($group_name) {
    $con = connect();

    $query = "SELECT dbpersons.id, dbpersons.first_name, dbpersons.last_name, dbpersons.email
              FROM dbpersons 
              INNER JOIN user_groups ON dbpersons.id = user_groups.user_id 
              WHERE user_groups.group_name = ?";  

    $stmt = mysqli_prepare($con, $query);
    $users = [];

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $group_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
    return $users;
}

/*
 * Get users NOT in a specific group
 */
function get_users_not_in_group($group_name) {
    $con = connect();

    $query = "SELECT id, first_name, last_name FROM dbpersons 
              WHERE id NOT IN (SELECT user_id FROM user_groups WHERE group_name = ?)";

    $stmt = mysqli_prepare($con, $query);
    $users = [];

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $group_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = [
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'] 
            ];
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
    return $users;
}

function get_groups_from_user($user_id) {
    $con = connect();
    $query = "SELECT ug.group_name, dg.color_level 
              FROM user_groups ug
              JOIN dbgroups dg ON ug.group_name = dg.group_name
              WHERE ug.user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    $groups = [];

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $groups[] = [
                'group_name' => $row['group_name'],
                'color_level' => $row['color_level']
            ];
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
    return $groups;
}

