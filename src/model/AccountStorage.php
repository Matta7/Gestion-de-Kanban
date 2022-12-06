<?php

interface AccountStorage {
    public function checkAuth($login, $password);
    public function registration($name, $login, $password);
    public function getAccount($login);
    public function getAllAccounts();
}