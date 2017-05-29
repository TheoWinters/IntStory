<?php

require( dirname(__FILE__) . '/tools.php' );
OpenDatabase();

$Sesson = LoadCurrentSesson();

$refPath = "";
$refLink = "";

if(isset($_GET['ref']))
{
    $refPath = $_GET['ref'];
    $refLink = "?ref=".$refPath;
}
else if(isset($_SERVER['HTTP_REFERER']))
{
    $refPath = $_SERVER['HTTP_REFERER'];
    $refLink = "?ref=".$refPath;
}
else
{
    $refPath = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
    $refLink = "?ref=".$refPath;
}

$Message = "";
$FormType = 0;

if(isset($_GET['keycode']) && isset($_GET['ID']))
{
    if($Sesson == null)
        $FormType = 2;
        
    $KeyCode = mysql_fix_string($_GET['keycode']);
    $ID = mysql_fix_string($_GET['ID']);

    // Handle the reset link
    $Activation = GetMessage($ID);
    if(mysql_num_rows($Activation) == 0)
    {      
        $Message = "Account Activation link is invalid.";
    }
    else
    {
        $ActivationData = mysql_fetch_row($Activation);
        if(strcasecmp($ActivationData[1], $KeyCode) != 0 || $ActivationData[3] != 0x40)
        {
            $Message = "Account Activation link is invalid.";
        }
        else if(time() > $ActivationData[4] + 324000)
        {
            // Allow a three hour window for the link to remain valid
            $Message = "Account Activation link has expired.";
        }
        else
        {
            $UserInfo = LoadAccountByID($ActivationData[2]);                

            if(mysql_num_rows($UserInfo) == 0)
            {
                $Message = "Account Activation link has expired.";
            }
            else
            {
                if($Sesson != null && $Sesson['ID'] != $ActivationData[2])
                {
                    $Message = "Can't activate a different account then your own while logged in.";
                    $FormType = 2;
                }
                else
                {
                    $UserData = mysql_fetch_row($UserInfo);

                    if($UserData[5] != 0)
                    {
                        $Message = "Account does not need to be activated";
                        $FormType = 2;                        
                    }
                    else
                    {
                        UpdateAccountAccess($UserData[0], $UserData[4] | 0xFF);
                        UpdateAccountStatus($UserData[0], 1);

                        // If they are logged in, relog them in with the new status and premissions
                        if($Sesson != null)
                        {                        
                            LogInUser($Sesson['ID'], $Sesson['Name'], $UserData[4] | 0xFF, 1);
                        }

                        $Message = "Account has been successfully activated";
                        $FormType = 1;
                    }
                    // Delete the message
                    DeleteMessage($ID);
                }
            }            
        }
    }
}
else
{
    if($Sesson == null)
    {
        // Not logged in, can't activate anything 
        header( 'Location: '.$refPath) ;
        exit();    
    }

    $UserInfo = LoadAccountByID($Sesson["ID"]);
    if(mysql_num_rows($UserInfo) == 0)
    {
        // Not logged in, can't activate anything 
        header( 'Location: '.$refPath) ;
        exit();    
    }
    $UserData = mysql_fetch_row($UserInfo);
    
    $MesageCode = EmailMessageCode($UserData[1]);
    $MesageData = AddMessage($UserData[0], $MesageCode, 0x40); 
    $MesageID = mysql_insert_id();
    $EmailMessage = "Hello ".$UserData[1].".\r\n\r\nYou are receiving this notification because you have (or someone pretending to be you has) created an account on 'The Changing Mirror' interactive story site.\r\n\r\nIf you did not request create this account then please ignore it, if you keep receiving it please contact the board administrator.\r\n\r\nTo activate your account please visit this site:\r\n\r\nhttp://".$_SERVER['HTTP_HOST'].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)."?keycode=".$MesageCode."&ID=".$MesageID."\r\n\r\n";

    if(mail($UserData[3], "Account Activation", $EmailMessage))
    {
        $Message = "Sending  Activation e-mail...";
    }
    else
    {
        $Message = "Unable to send e-mail.";
    }
}

echo <<< _END
<html>
<head>
<title>Account Activation</title>
</head>
<body>
<h2>Account Activation</h2>

<p>$Message</p>
_END;

if($FormType == 0)
{
    echo '<hr /><a href="activateaccount.php'.$refLink.'">Resend activation e-mail</a>.';
}

echo <<< _END
    <hr />
    <p><a href=".">Interactive Stories Homepage</a>.</p>
    </body>
    </html>
_END;



?>