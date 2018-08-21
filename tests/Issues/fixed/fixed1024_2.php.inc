<?php
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 *
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 *
 * For further information visit:
 * 		http://www.fckeditor.net/
 *
 * "Support Open Source software. What about a donation today?"
 *
 * File Name: functions.php
 * 	This is the File Manager Connector for PHP.
 *
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

function GetFolders($currentFolder, $type)
{
    $trust_mode = $type != 'Image';

    // Array that will hold the folders names.
    $aFolders    = array();

    $sServerDir = $trust_mode ? FCK_TRUSTUPLOAD_PATH . $currentFolder : FCK_UPLOAD_PATH . $currentFolder;
    $oCurrentFolder = opendir($sServerDir);

    while (($sFile = readdir($oCurrentFolder)) !== false) {
        if ($sFile != '.' && $sFile != '..' && is_dir($sServerDir . $sFile)) {
            $aFolders[] = '<Folder name="' . ConvertToXmlAttribute($sFile) . '" />';
        }
    }

    closedir($oCurrentFolder);

    // Open the "Folders" node.
    echo "<Folders>";

    sort($aFolders);
    foreach ($aFolders as $sFolder) {
        echo $sFolder;
    }

    // Close the "Folders" node.
    echo "</Folders>";
}


function GetFoldersAndFiles($currentFolder, $type)
{
    $trust_mode = $type != 'Image';

    // Map the virtual path to the local server path.
    $sServerDir = $trust_mode ? FCK_TRUSTUPLOAD_PATH . $currentFolder : FCK_UPLOAD_PATH . $currentFolder;

    // Arrays that will hold the folders and files names.
    $aFolders = array();
    $aFiles = array();

    // check the directory exists
    if (! is_dir($sServerDir)) {
        echo '<Folders /><Files /><CustomError message="Create folder '.htmlspecialchars($sServerDir, ENT_QUOTES).' first" />';
        return;
    }

    $oCurrentFolder = opendir($sServerDir);

    while (($sFile = readdir($oCurrentFolder)) !== false) {
        if (substr($sFile, 0, 1) == '.') {
            continue;
        }

        if (is_dir($sServerDir . $sFile)) {
            // folder
            $aFolders[] = '<Folder name="' . ConvertToXmlAttribute($sFile) . '" />';
        } else {
            // uid prefix check
            if (! empty($GLOBALS['fck_check_user_prefix'])) {
                if (! strstr($sFile, $GLOBALS['fck_user_prefix'])) {
                    continue;
                }
            }

            // extension check
            if (! empty($GLOBALS['fck_resource_type_extensions'][$type])) {
                // file limitation by extension and resource type
                if ($trust_mode) {
                    $extension = strtolower(substr(strrchr(DecodeFileName(substr($sFile, strlen($GLOBALS['fck_user_prefix']))), '.'), 1));
                } else {
                    $extension = strtolower(substr(strrchr($sFile, '.'), 1));
                }
                if (! in_array($extension, $GLOBALS['fck_resource_type_extensions'][$type])) {
                    continue;
                }
            }

            // filesize
            $iFileSize = filesize($sServerDir . $sFile);
            if ($iFileSize > 0) {
                $iFileSize = round($iFileSize / 1024);
                if ($iFileSize < 1) {
                    $iFileSize = 1;
                }
            }

            // filemtime
            $iFileMtime = filemtime($sServerDir . $sFile);

            // can_delete
            $iCanDelete = intval(CheckCanDelete($sServerDir . $sFile));

            if ($trust_mode) {
                // separate filename into 'display name' and 'url'
                $sFileDisplayName = DecodeFileName(substr($sFile, strlen($GLOBALS['fck_user_prefix'])));
                $sFileUrl = FCK_TRUSTUPLOAD_URL.$currentFolder.$sFile;
                $sXmlEntry = '<File name="' . $sFileDisplayName . '" url="' . $sFileUrl . '" size="' . $iFileSize . '" mtime="' . $iFileMtime . '" can_delete="' . $iCanDelete . '" />';
            } else {
                $sXmlEntry = '<File name="' . ConvertToXmlAttribute($sFile) . '" size="' . $iFileSize . '" mtime="' . $iFileMtime . '" can_delete="' . $iCanDelete . '" />';
            }

            $aFiles[ $sXmlEntry ] = $iFileMtime;
        }
    }

    // Send the folders
    sort($aFolders);
    echo '<Folders>';

    foreach ($aFolders as $sFolder) {
        echo $sFolder;
    }

    echo '</Folders>';

    // Send the files
    arsort($aFiles);
    echo '<Files>';

    foreach (array_keys($aFiles) as $sFiles) {
        echo $sFiles;
    }

    echo '</Files>';

    // Send ticket (easy ticket)
    echo '<Ticket value="'.md5(session_id()).'" />';
}


function CreateFolder($currentFolder, $type)
{
    $trust_mode = $type != 'Image';

    $sErrorNumber    = '0';
    $sErrorMsg        = '';

    $sNewFolderName = preg_replace('/[^0-9a-zA-Z_-]/', '', @$_GET['NewFolderName']);
    if (empty($sNewFolderName)) {
        $sErrorNumber = '102';
    } elseif (empty($GLOBALS['fck_isadmin'])) {
        // permission check (only admin create folder)
        $sErrorNumber = '103';
    } elseif (ini_get('safe_mode')) {
        $sErrorNumber = '1';
        $sErrorMsg = 'Your server runs under safe_mode. Thus, you have to make directories by yourself';
    } else {

        // Map the virtual path to the local server path of the current folder.
        $sServerDir = $trust_mode ? FCK_TRUSTUPLOAD_PATH . $currentFolder : FCK_UPLOAD_PATH . $currentFolder;

        if (is_writable($sServerDir) && ! file_exists($sServerDir . $sNewFolderName)) {
            $oldumask = umask(0);
            @mkdir($sServerDir . $sNewFolderName, 0777);
            umask($oldumask);
        } else {
            $sErrorNumber = '103';
        }
    }

    // Create the "Error" node.
    echo '<Error number="' . $sErrorNumber . '" text="' . ConvertToXmlAttribute($sErrorMsg) . '" />';
}


function FileUpload($currentFolder = '/')
{
    global $fck_allowed_extensions;

    // Check upload permission
    if (empty($GLOBALS['fck_canupload'])) {
        SendResultsHTML('202', '', 'You are not permitted to upload any files', '');
    }

    // Check if the file has been correctly uploaded.
    if (empty($_FILES[FCK_UPLOAD_NAME]) || empty($_FILES[FCK_UPLOAD_NAME]['tmp_name']) || empty($_FILES[FCK_UPLOAD_NAME]['name']) || ! is_uploaded_file($_FILES[FCK_UPLOAD_NAME]['tmp_name'])) {
        SendResultsHTML('202', '', 'failed to upload', '');
    }

    // Get extension from the uploaded file
    $extension = strtolower(substr(strrchr($_FILES[FCK_UPLOAD_NAME]['name'], '.'), 1));

    // White list check
    if (! in_array($extension, array_keys($fck_allowed_extensions))) {
        SendResultsHTML('1', '', 'Invalid file extension. allowed ('.htmlspecialchars(implode(',', array_keys($fck_allowed_extensions))).') only', '');
    }

    // Image mode (inside DocumentRoot) or Trust mode (outside DocumentRoot)
    $trust_mode = empty($fck_allowed_extensions[ $extension ]);

    // Create new file name
    if ($trust_mode) {
        // create encoded name
        $original_file_name4encode = mb_convert_encoding($_FILES[FCK_UPLOAD_NAME]['name'], 'UTF-8', 'auto');
        $new_filename = @$GLOBALS['fck_user_prefix'] . FCK_FILE_PREFIX . EncodeFileName($original_file_name4encode);
        $sServerDir = FCK_TRUSTUPLOAD_PATH . $currentFolder;
        $new_filefullpath = $sServerDir . $new_filename;
        $new_fileurl = FCK_TRUSTUPLOAD_URL.$currentFolder.$new_filename;
    } else {
        // create random name
        $new_filename = @$GLOBALS['fck_user_prefix'] . FCK_FILE_PREFIX . date('YmdHis') . substr(md5(uniqid(rand(), true)), 0, 8) . '.' . $extension;
        $sServerDir = FCK_UPLOAD_PATH . $currentFolder;
        $new_filefullpath = $sServerDir . $new_filename;
        $new_fileurl = FCK_UPLOAD_URL.$currentFolder.$new_filename;
    }

    // check the directory exists
    if (! is_dir($sServerDir)) {
        SendResultsHTML('1', '', 'Create the directory '.$sServerDir.' first', '');
    }

    // move temporary
    $prev_mask = @umask(0022);
    $upload_result = move_uploaded_file($_FILES[FCK_UPLOAD_NAME]['tmp_name'], $new_filefullpath);
    @umask($prev_mask);
    if (! $upload_result) {
        SendResultsHTML('202');
    }
    @chmod($new_filefullpath, 0644);

    // check the file is valid (image mode only)
    if (! $trust_mode) {
        $check_result = @getimagesize($new_filefullpath);
        if ($check_result === false && empty($GLOBALS['fck_isadmin'])) {
            // admin can upload non-image-files into root side
            SendResultsHTML(0, $new_fileurl, $new_filename);
            return;
        }
        if (! is_array(@$check_result) || empty($check_result['mime']) || stristr($check_result['mime'], $fck_allowed_extensions[ $extension ]) === false) {
            @unlink($new_filefullpath);
            SendResultsHTML('202', '', 'File extension does not match the file contents', '');
        } else {
            // resize or make thumbnail etc.
            if (defined('FCK_FUNCTION_AFTER_IMGUPLOAD') && function_exists(FCK_FUNCTION_AFTER_IMGUPLOAD)) {
                $func_name = FCK_FUNCTION_AFTER_IMGUPLOAD;
                $func_name($new_filefullpath);
            }
        }
    }

    // success and exit
    SendResultsHTML(0, $new_fileurl, $new_filename);
}


function DeleteFile($currentFolder = '/', $type)
{
    $trust_mode = $type != 'Image';

    $sErrorNumber    = '0';
    $sErrorMsg        = '';

    // get physical path of the targeted file
    if ($trust_mode) {
        list(, $sDeleteFile) = explode('=', @$_GET['file_url'], 2);
        $sDeleteFile = preg_replace('/[^a-zA-Z0-9_.-]/', '', basename($sDeleteFile));
        $sServerDir = FCK_TRUSTUPLOAD_PATH . $currentFolder;
    } else {
        $sDeleteFile = preg_replace('/[^a-zA-Z0-9_.-]/', '', basename(@$_GET['file_url']));
        $sServerDir = FCK_UPLOAD_PATH . $currentFolder;
    }
    $sServerDeleteFile = $sServerDir . $sDeleteFile;

    if ($_GET['Ticket'] != md5(session_id())) {
        // easy Ticket check
        $sErrorNumber = '1';
        $sErrorMsg = 'Ticket error';
    } elseif (empty($sDeleteFile)) {
        // File not specified
        $sErrorNumber = '1';
        $sErrorMsg = 'Invalid file name';
    } elseif (! CheckCanDelete($sServerDeleteFile)) {
        // admin or owned
        $sErrorNumber = '1';
        $sErrorMsg = 'You are not permitted to delete the file';
    } else {
        // all ok
        $result = @unlink($sServerDeleteFile);
        if (! $result) {
            $sErrorNumber = '1';
            $sErrorMsg = 'Cannot delete the file. check permissions etc.';
        }
    }

    // Create the "Error" node.
    echo '<Error number="' . $sErrorNumber . '" text="' . ConvertToXmlAttribute($sErrorMsg) . '" />';
}


function CheckCanDelete($file_full_path)
{
    $mtime = filemtime($file_full_path);

    if ($GLOBALS['fck_isadmin']) {
        return true;
    } elseif ($mtime + FCK_USER_SELFDELETE_LIMIT > time() && (empty($GLOBALS['fck_check_user_prefix']) || strstr(basename($file_full_path), $GLOBALS['fck_user_prefix']))) {
        return true;
    } else {
        return false;
    }
}


function ConvertToXmlAttribute($value)
{
    return utf8_encode(htmlspecialchars($value, ENT_QUOTES));
}


function SetXmlHeaders()
{
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Prevent the browser from caching the result.
    // Date in the past
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    // always modified
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    // HTTP/1.1
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    // HTTP/1.0
    header('Pragma: no-cache');

    // Set the response format.
    header('Content-Type:text/xml; charset=utf-8');
}


function CreateXmlHeader($command, $currentFolder)
{
    SetXmlHeaders();

    // Create the XML document header.
    echo '<?xml version="1.0" encoding="utf-8" ?>';

    // Create the main "Connector" node.
    echo '<Connector command="' . $command . '" resourceType="Image">';

    // Add the current folder node.
    echo '<CurrentFolder path="' . ConvertToXmlAttribute($currentFolder) . '" url="' . ConvertToXmlAttribute(FCK_UPLOAD_URL . $currentFolder) . '" />';
}


function CreateXmlFooter()
{
    echo '</Connector>';
}


// return error by XML (for browser AJAX)
function SendErrorXML($number, $fileUrl = '', $fileName = '',  $text = '')
{
    SetXmlHeaders();

    // Create the XML document header
    echo '<?xml version="1.0" encoding="utf-8" ?>';

    echo '<Connector><Error number="' . $number . '" text="' . htmlspecialchars($text) . '" /></Connector>';

    exit;
}


// return results by HTML (for upload AHAH)
function SendResultsHTML($number, $fileUrl = '', $fileName = '', $text = '')
{
    if (defined('FCK_IS_BROWSER_CONNECTOR')) {
        echo '<script type="text/javascript">';
        echo 'window.parent.frames["frmUpload"].OnUploadCompleted(' . $number . ',"' . str_replace('"', '\\"', $fileName) . '") ;';
        echo '</script>';
    } else {
        echo '<script type="text/javascript">';
        echo 'window.parent.OnUploadCompleted(' . $number . ',"' . str_replace('"', '\\"', $fileUrl) . '","' . str_replace('"', '\\"', $fileName) . '", "' . str_replace('"', '\\"', $text) . '") ;';
        echo '</script>';
    }
    exit;
}


// Error Wrapper
function SendError($number, $fileUrl = '', $fileName = '',  $text = '')
{
    if (defined('FCK_IS_BROWSER_CONNECTOR')) {
        SendErrorXML($number, $fileUrl, $fileName,  $text);
    } else {
        SendResultsHTML($number, $fileUrl, $fileName, $text);
    }
}


// filename encoder (from pukiwiki)
function EncodeFileName($key)
{
    return ($key == '') ? '' : strtoupper(join('', unpack('H*0', $key)));
}

// filename decoder (from pukiwiki)
function DecodeFileName($key)
{
    return ($key == '') ? '' : substr(pack('H*', '20202020'.$key), 4);
}
