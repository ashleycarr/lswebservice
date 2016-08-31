<?php

/**
 * cls_user.php
 *
 * This class handles user logins.
 *
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial
 * 4.0 International License.
 *
 * To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

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
    
    public function setLoggedIn()
    {
        $this->loggedIn = true;
        $this->lastActivity = time();
    }
    
    public function setLastActivity()
    {
        $this->lastActivity = time();
    }

    public function isLoggedIn()
    {
        return($this->loggedIn &&
               time() - $this->lastActivity < 1800);
    }
    
    public function getUsername()
    {
        return($this->userName);
    }
}
