<?php

/**
 * cls_user.php
 *
 * This class handles user logins.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver\Library;

class User
{
    private $loggedIn = false;
    private $userName = false;
    private $lastActivity;
    
    public function __construct($userName)
    {
        $this->userName = $userName;
    }
    
    
    /**
     * authenticates a user by verifying their credentials in the database.
     * @param  string  $password the users password
     * @return boolean true if verified
     */
    public function authenticate($password)
    {
        $dbh = localDBConnect(
            LOCALDB_DBNAME,
            LOCALDB_USERNAME,
            LOCALDB_PASSWORD
        );
        
        $sth = $dbh->prepare('
            SELECT userPassword FROM `adminUsers`
            WHERE userName=:userName
        ');
        
        $sth->execute(array(':userName' => $this->userName));

        if ($sth->rowCount() != 0 &&
           password_verify($password, $sth->fetchColumn(0))) {
            return(true);
        } else {
            return false;
        }
    }
    
    
    /**
     * updates a users password in the database
     * @param string $oldPassword the user's old password
     * @param string $newPassword the user's new password to update
     * @return boolean true if the password was successfully updated
     */
    public function updatePassword($oldPassword, $newPassword)
    {
        $dbh = localDBConnect(
            LOCALDB_DBNAME,
            LOCALDB_USERNAME,
            LOCALDB_PASSWORD
        );
        
        if (!$this->authenticate($oldPassword)) {
            return(false);
        }
        
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sth = $dbh->prepare('
            UPDATE `adminUsers` SET userPassword=:newPassword
            WHERE userName=:userName
        ');
        
        $sth->execute(
            array(':userName' => $this->userName,
                  ':oldPassword' => $oldPassword,
                  ':newPassword' => $newPassword)
        );
        
        return(true);
    }
    
    
    /**
     * set's the user as logged in.
     */
    public function setLoggedIn()
    {
        $this->loggedIn = true;
        $this->lastActivity = time();
    }
    
    
    /**
     * Updates the last activity timestamp for timeouts.
     */
    public function setLastActivity()
    {
        $this->lastActivity = time();
    }

    
    /**
     * @return boolean true if the user is logged in
     */
    public function isLoggedIn()
    {
        return($this->loggedIn &&
               time() - $this->lastActivity < 1800);
    }
    
    
    /**
     * @return string the username of this user.
     */
    public function getUsername()
    {
        return($this->userName);
    }
}
