<?php
function totalPost($pdo){
    $query = query($pdo, 'SELECT COUNT(*) FROM post');
    $row = $query->fetch();
    return $row[0];
}

function query($pdo, $sql, $parameters = []){
    $query = $pdo->prepare($sql);
    $query->execute($parameters);
    return $query;
}

function deletePost($pdo, $id) {
    $parameters = [':id' => $id];
    query($pdo, 'DELETE FROM post WHERE postid = :id', $parameters);
}

function insertPost($pdo, $posttext, $studentid, $modulecategoryid){
    $query = 'INSERT INTO post (posttext, postdate, studentid, modulecategoryid)
    VALUES (:posttext, CURDATE(), :studentid, :modulecategoryid)';
    $parameters = [':posttext' => $posttext,':studentid' => $studentid,':modulecategoryid' => $modulecategoryid];
    query($pdo, $query, $parameters);
}

function allStudent($pdo) {
    $student = query($pdo, 'SELECT * FROM student');
    return $student->fetchAll();
}

function allModulecategory($pdo) {
    $modulecategory = query($pdo, 'SELECT * FROM modulecategory');
    return $modulecategory->fetchAll();
}

function allPost ($pdo) {
    $query = 'SELECT post.postid, post.posttext, student.studentname, student.email, modulecategory.modulecategoryid 
              FROM post
              INNER JOIN student ON post.studentid = student.studentid
              INNER JOIN modulecategory ON post.modulecategoryid = modulecategory.modulecategoryid';
    $post = query($pdo, $query);
    return $post->fetchAll();
}
