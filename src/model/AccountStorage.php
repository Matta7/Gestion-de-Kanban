<?php

interface AccountStorage {
    public function checkAuth($login, $password);
    public function registration($name, $login, $password);
}