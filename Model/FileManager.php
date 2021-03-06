<?php

namespace Model;

use Cool\DBManager;

class FileManager
{
    public function findAll()
    {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPdo();
        $result = $pdo->query('SELECT * FROM files');
        
        return $result->fetchAll();
    }
    
    public function findByFolder($folder_id)
    {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPdo();
        $sth = $pdo->prepare('SELECT * FROM files WHERE folder_id = ?');
        $sth->execute([$folder_id]);

        return $sth->fetchAll();
    }

    public function findById($id)
    {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPdo();
        $sth = $pdo->prepare('SELECT * FROM files WHERE id = ?');
        $sth->execute([$id]);

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function deleteById($id)
    {
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPdo();
        $sth = $pdo->prepare('SELECT * FROM files WHERE id = ?');
        $sth->execute([$id]);
        $file = $sth->fetch();
        
        $filename = './uploads/'.$file['id'].'_'.$file['name'];
        if (file_exists($filename)) {
            unlink($filename);
        }

        $sth = $pdo->prepare('DELETE FROM files WHERE id = ?');
        $sth->execute([$id]);

        return $true;
    }
    
    
/*    public function findAllBadass()
    {
       return DBManager::getInstance()->getPdo()->query('SELECT * FROM files')->fetchAll();
    }*/

    public function uploadFile($file_info, $filename, $location_id)
    {
        $filename = $filename ?: $file_info['name'];
        $info = new \SplFileInfo($file_info['name']);
        $ext = $info->getExtension();
        if (!preg_match('/\\.'.$ext.'$/', $filename))
            $filename .= '.'.$ext;
        
        $dbManager = DBManager::getInstance();
        $pdo = $dbManager->getPdo();
        $now = new \DateTime();
        $sth = $pdo->prepare('INSERT INTO `files` VALUES (NULL, ?, ?, ?, ?)');
        $sth->execute([$filename, $now->format('Y-m-d H:i:s'), $location_id, 'file']);
        $id = $pdo->lastInsertId();
        
        $uploaddir = './uploads/';
        $uploadfile = $uploaddir . $id . '_' . basename($filename);
        move_uploaded_file($file_info['tmp_name'], $uploadfile);
    }
}
