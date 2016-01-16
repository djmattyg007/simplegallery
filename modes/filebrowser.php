<?php

$paths = json_decode(loadPicFile("conf/paths.json"), true);

if (empty($_POST)) {
    loadPicFile("templates/filebrowser.phtml", array("paths" => $paths));
    exit();
}
if (!isset($_POST["path"]) || !is_numeric($_POST["path"])) {
    sendError(400);
}
$pathID = (int) $_POST["path"];
if (!isset($paths[$pathID])) {
    sendError(404);
}
$pathConfig = $paths[$pathID];

use Symfony\Component\Finder\Finder;

$directoryFinder = new Finder();
$directoryFinder->directories()
    ->ignoreUnreadableDirs()
    ->depth(0)
    ->sortByName();
if (isset($pathConfig["followLinks"]) && $pathConfig["followLinks"] === true) {
    $directoryFinder->followLinks();
}
if (!empty($_POST["relpath"])) {
    if (strpos($_POST["relpath"], "..") !== false) {
        sendError(400);
    }
    $directoryFinder->path($_POST["relpath"])
        ->depth(substr_count($_POST["relpath"], "/") + 1);
}
$directoryIterator = $directoryFinder->in($pathConfig["path"]);

$directoryArray = array();
foreach ($directoryIterator as $directory) {
    $directoryArray[] = array(
        "path" => $directory->getRelativePathname(),
        "name" => $directory->getBasename(),
    );
}

$fileFinder = new Finder();
$fileFinder->files()
    ->ignoreUnreadableDirs()
    ->name("*.jpg")
    ->name("*.JPG")
    ->depth(0)
    ->sortByName();
if (isset($pathConfig["followLinks"]) && $pathConfig["followLinks"] === true) {
    $fileFinder->followLinks();
}
if (!empty($_POST["relpath"])) {
    if (strpos($_POST["relpath"], "..") !== false) {
        sendError(400);
    }
    $fileFinder->path($_POST["relpath"])
        ->depth(substr_count($_POST["relpath"], "/") + 1);
}
$fileIterator = $fileFinder->in($pathConfig["path"]);

$fileArray = array();
foreach ($fileIterator as $file) {
    $fileArray[] = array(
        "filename" => $file->getBasename(),
        "relpath" => $file->getRelativePathname(),
        "size" => humanFilesize($file->getSize()),
        "mtime" => date("Y-m-d H:i:s", $file->getMTime()),
        "randthumb" => substr(str_shuffle("ABCDEF0123456789"), 0, 6),
    );
}

header("Content-type: application/json");
echo json_encode(array("directories" => $directoryArray, "files" => $fileArray));