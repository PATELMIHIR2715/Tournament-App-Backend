<?php

$encryptionKey = "mihir@_+27012005";

/**
 * Check if a user's IP exists in the database.
 * If not, add it.
 */
function checkUserIpExists($IiKgq, $Qamgd) {
    global $gqsQ3;
    $RWv6B = "SELECT * FROM user_ips WHERE user_id = '{$IiKgq}' AND ip = '{$Qamgd}'";
    $QLM7E = $gqsQ3->query($RWv6B);

    if ($QLM7E->num_rows == 0) {
        addNewUserIp($IiKgq, $Qamgd, 0);
        $QLM7E = $gqsQ3->query($RWv6B);
    }

    return $QLM7E->fetch_assoc();
}

function blockUserAllIps($IiKgq) {
    global $gqsQ3;
    $iFg_c = date("Y-m-d H:i:s");
    $wYokV = "UPDATE user_ips SET blocked = '1', blocked_date = '{$iFg_c}' WHERE user_id = '{$IiKgq}'";
    $gqsQ3->query($wYokV);
}

function checkPathFolders($L63at) {
    $qECdd = basename($L63at);
    $BVYAp = dirname($L63at);
    $GJP0N = explode("/", $BVYAp);
    $NhySO = '';

    foreach ($GJP0N as $o4P3i) {
        $NhySO .= $o4P3i . "/";
        if (!is_dir($NhySO)) {
            mkdir($NhySO, 0777);
        }
    }
}

function blockUser($IiKgq) {
    global $gqsQ3;
    $uSWy5 = "UPDATE users SET block = '1' WHERE id = '{$IiKgq}'";
    $gqsQ3->query($uSWy5);
}

function generateReferralCode($OkDL9 = "users", $wnnt7 = "referral_code") {
    $Kl4TM = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $qc00D = substr(str_shuffle($Kl4TM), 0, 10);
    $I0f8d = getDataFromTable($OkDL9, $wnnt7, "{$wnnt7} = '{$qc00D}'", true);

    if ($I0f8d == null) {
        return $qc00D;
    } else {
        return generateReferralCode();
    }
}

function uploadFile($Uahvs, $Y8Tss) {
    include_once "lib/upload_file.php";

    if (!is_dir($Uahvs)) {
        mkdir($Uahvs, 0777, true);
    }

    $ytDQQ = new czJ7M();
    $ytDQQ->HgtaR($Y8Tss);
    $tEuOD = $ytDQQ->aNL51($Uahvs, $_FILES[$Y8Tss]);

    if ($tEuOD["status"] == 1) {
        return $tEuOD["filepath"];
    } else {
        createLogs("logs/upload-file-logs.txt", "Unable to create file | Msg = " . $tEuOD["msg"]);
        return '';
    }
}

function verifyToken() {
    if (!isset($_GET["token"])) {
        echoResponse(array("status" => 0, "msg" => "Invalid token"));
        die;
    }

    $EKIgl = getDataFromTable("tokens", "id", "token = '" . $_GET["token"] . "'", true);

    if ($EKIgl == null) {
        echoResponse(array("status" => 0, "msg" => "Invalid token"));
        die;
    }
}

function currentDirPath() {
    $mxDYG = basename($_SERVER["PHP_SELF"]);
    $xnCBO = __DIR__;
    $DVRXV = "https://{$_SERVER["HTTP_HOST"]}{$_SERVER["REQUEST_URI"]}";
    $DVRXV = str_replace($mxDYG, '', $DVRXV);

    return $DVRXV;
}

function echoData($mcrDG) {
    global $encryptionKey;
    echo encrypt($encryptionKey, $encryptionKey, json_encode($mcrDG));
}

function decrypt($encryptionKey, $fVWgq) {
    $Y_iIB = explode(":", $fVWgq);
    $l6YQ0 = $Y_iIB[0];
    $pENjX = $Y_iIB[1];

    return openssl_decrypt(base64_decode($l6YQ0), "aes-128-cbc", $encryptionKey, OPENSSL_RAW_DATA, base64_decode($pENjX));
}

function addNewUserIp($IiKgq, $Qamgd, $epmsO) {
    global $gqsQ3;
    $iFg_c = date("Y-m-d H:i:s");
    $q3_Jl = "INSERT INTO user_ips (user_id, ip, blocked, date_time) VALUES ('{$IiKgq}', '{$Qamgd}', '{$epmsO}', '{$iFg_c}')";
    $gqsQ3->query($q3_Jl);
}

function appendFile($psTNE, $Tqayh) {
    checkPathFolders($psTNE);
    $I3bW3 = fopen($psTNE, "a");
    fwrite($I3bW3, $Tqayh);
    fclose($I3bW3);
}

function getUserIp() {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER["HTTP_CLIENT_IP"] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    $clientIp = @$_SERVER["HTTP_CLIENT_IP"];
    $forwardedIp = @$_SERVER["HTTP_X_FORWARDED_FOR"];
    $remoteAddr = $_SERVER["REMOTE_ADDR"];

    if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
        return $clientIp;
    } elseif (filter_var($forwardedIp, FILTER_VALIDATE_IP)) {
        return $forwardedIp;
    } else {
        return $remoteAddr;
    }
}

function blockUserIp($gyAuO) {
    global $gqsQ3;
    $iFg_c = date("Y-m-d H:i:s");
    $wYokV = "UPDATE user_ips SET blocked = '1', blocked_date = '{$iFg_c}' WHERE id = '{$gyAuO}'";
    $gqsQ3->query($wYokV);
}

function makeGetRequest($D5gX8) {
    $s9E46 = curl_init();
    curl_setopt($s9E46, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($s9E46, CURLOPT_URL, $D5gX8);
    $X_kme = curl_exec($s9E46);
    curl_close($s9E46);

    return $X_kme;
}

function encrypt($encryptionKey, $pENjX, $l6YQ0) {
    return base64_encode(openssl_encrypt($l6YQ0, "aes-128-cbc", $encryptionKey, OPENSSL_RAW_DATA, $pENjX));
}

function getQueryParams() {
    $XhK4q = array();
    $XhK4q["order"] = isset($_GET["order"]) ? $_GET["order"] : null;
    $XhK4q["order_by"] = isset($_GET["order_by"]) ? $_GET["order_by"] : null;
    $XhK4q["row_count"] = isset($_GET["row_count"]) ? $_GET["row_count"] : 10;
    $XhK4q["page"] = isset($_GET["page"]) ? $_GET["page"] : 0;

    if ($XhK4q["page"] < 1) {
        $XhK4q["page"] = 1;
    }

    $XhK4q["page"] = ($XhK4q["page"] - 1) * $XhK4q["row_count"];
    return $XhK4q;
}

function createFile($psTNE, $Tqayh) {
    checkPathFolders($psTNE);
    $yte0b = fopen($psTNE, "w");
    fwrite($yte0b, $Tqayh);
    fclose($yte0b);
}

function validateUserRequest($IiKgq, $m3tW0) {
    global $gqsQ3;
    $Qamgd = getUserIp();
    $QtPxN = checkUserIpExists($IiKgq, $Qamgd);

    if ($m3tW0 != "yy/Gpr6nidF+dgse9KUiTMtrEAc=" && $m3tW0 != "qsBpvQm5csRQjhindPZy4aoWMhA=" && $m3tW0 != "vpX8BaJaOMzaBig3rm/dAJU8Cng=") {
        appendFile("logs/user_validation_logs.txt", date("Y-m-d H:i:s") . " | SHA doesn't match | User Id = {$IiKgq} | SHA = {$m3tW0} | User Ip = {$Qamgd}\n\n");
        blockUserIp($QtPxN["id"]);
        $gqsQ3->close();
        die;
    }

    if (userDetails($IiKgq, "block") == 1) {
        appendFile("logs/user_validation_logs.txt", date("Y-m-d H:i:s") . " | User Id is blocked | User Id = {$IiKgq} | User Ip = {$Qamgd}\n\n");
        blockUserAllIps($IiKgq);
        $gqsQ3->close();
        die;
    }

    if ($QtPxN["blocked"] == 1) {
        appendFile("logs/user_validation_logs.txt", date("Y-m-d H:i:s") . " | User Ip Address is blocked | User Id = {$IiKgq} | User Ip = {$Qamgd}\n\n");
        blockUser($IiKgq);
        $gqsQ3->close();
        die;
    }
}

function createLogs($psTNE, $Tqayh) {
    $Tqayh = date("Y-m-d H:i:s") . " | " . $Tqayh;
    $Tqayh .= "\nLog Data = " . json_encode(debug_backtrace()) . "\n\n";
    appendFile($psTNE, $Tqayh);
}

function generateToken($cVy5b = 80, $rqQVT = null, $zf1cf = null) {
    $Kl4TM = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $qc00D = substr(str_shuffle($Kl4TM), 0, $cVy5b);

    if ($rqQVT != null && $zf1cf != null) {
        return $qc00D;
    }

    return $qc00D;
}

function uploadBase64File($psTNE, $j6qf7) {
    if ($j6qf7 != '') {
        checkPathFolders($psTNE);
        if (file_put_contents($psTNE, base64_decode($j6qf7))) {
            return $psTNE;
        } else {
            createLogs("logs/upload-file-logs.txt", date("Y-m-d H:i:s") . " | Unable to upload base64 file");
        }
    }

    return '';
}

// New function: tournamentResult
function tournamentResult($tournamentId) {
    global $gqsQ3;
    $query = "SELECT * FROM tournament_results WHERE tournament_id = '{$tournamentId}'";
    $result = $gqsQ3->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return array("status" => 0, "msg" => "No results found for this tournament.");
    }
}
?>