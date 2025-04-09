<?php

const DB_HOST = 'localhost';
const DB_NAME = 'prac';
const DB_USER = 'root';
const DB_PASS = 'YES';

function getDB(): bool|mysqli {
    return mysqli_connect(DB_HOST, DB_USER, '', DB_NAME);
}
?>