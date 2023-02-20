#!/usr/bin/php
<?php
//This script creates a sqlite db with phone numbers and the owners name. 
//I tried khard but that is to slow

//it reads files from $contactDir It looks for a global dumb and groups with sub sets. It is written for NextCloud vcf file name format. 

include("./vCard.php");
$contactDir = "/opt/vcf2asterisk/contacts/";
$nCountryCode = "31";

class asteriskContacts {
   public $aPhones = array();

   public function write() {
      $db = new SQLite3('./phones.db');
      $db->query("DROP TABLE if exists `contacts`");
      $db->query("CREATE TABLE IF NOT EXISTS contacts (number text,name text,`group` text);");
      #print_r($this->aPhones);
      foreach($this->aPhones as $number => $vPhone) {
      # echo "INSERT INTO contacts (number,name,`group`) VALUES ('$number','".$vPhone["name"]."','".$vPhone["group"]."');\n";
       $db->query("INSERT INTO contacts (number,name,`group`) VALUES ('$number','".$vPhone["name"]."','".$vPhone["group"]."');");

     } 
   }
   
   public function add($sNumber,$sName,$sGroup = "all") {
     global $nCountryCode;
     $sGroup = str_replace(' ','_',$sGroup);
     if (preg_match('/(00|\+)'.$nCountryCode.'(\d+)/',$sNumber,$aMatch)) {
       $sNumber = "0".$aMatch[2];
     }    
     if (@$vGroup = $this->aPhones[$sNumber]["group"]) 
     {
       if ($vGroup == "all" ) {
        $this->aPhones[$sNumber]["name"] = $sName;
        $this->aPhones[$sNumber]["group"] = $sGroup;
       }
     } else {
        $this->aPhones[$sNumber]["name"] = $sName;
        $this->aPhones[$sNumber]["group"] = $sGroup;
     }
   }
}

$asterisk = new asteriskContacts();

#Find Files
$aFiles = scandir($contactDir);
foreach($aFiles as $sFile) {
  if (preg_match('/\d+-\d+-\d+_\d+-\d+_(.*)\.vcf/',$sFile,$aMatch)) {
    $aGroup[$aMatch[1]] = $contactDir.$sFile;
    echo "found $sFile use as ".$aMatch[1]."\n";
  }
  if (preg_match('/contacts-\d+-\d+-\d+\.vcf/',$sFile,$aMatch)) {
    $sAllContacts = $contactDir.$sFile;
    echo "found $sFile use for all\n";
  }

}

#parse groups

foreach($aGroup as $sGroup => $sFile) {

   $vCard = new vCard($sFile);
   if (count($vCard) == 1)
   {
        $sName = $vCard->fn[0];
        foreach($vCard->tel as $vTel) 
        {
          if (is_array($vTel)) {
            $asterisk->add($vTel['Value'],$sName,$sGroup);
          }
          else 
          {
            #echo "tel: ".$vTel;
            $asterisk->add($vTel['Value'],$sName,$sGroup);
          }
        }
   }
   else
   {
      foreach ($vCard as $vCardPart)
      {
        #print_r($vCardPart );
        $sName = $vCardPart->fn[0];
        foreach($vCardPart->tel as $vTel) 
        {
          if (is_array($vTel)) {
            #print_r($vTel['Value']);
            $asterisk->add($vTel['Value'],$sName,$sGroup);
          }
          else 
          {
            #echo "tel: ".$vTel;
            $asterisk->add($vTel,$sName,$sGroup);
          }
        }
      }
   }
}
#parse contacts

$vCard = new vCard($sAllContacts);
if (count($vCard) == 1)
{
   #print_r($vCard);
     $sName = $vCard->fn[0];
     foreach($vCard->tel as $vTel) 
     {
       if (is_array($vTel)) {
         #print_r($vTel['Value']);
         $asterisk->add($vTel['Value'],$sName,$sGroup);
       }
       else 
       {
         #echo "tel: ".$vTel;
         $asterisk->add($vTel['Value'],$sName,$sGroup);
       }
     }
}
else
{
   foreach ($vCard as $vCardPart)
   {
     #print_r($vCardPart );
     $sName = $vCardPart->fn[0];
     foreach($vCardPart->tel as $vTel) 
     {
       if (is_array($vTel)) {
        #print_r($vTel['Value']);
        $asterisk->add($vTel['Value'],$sName);
       }
       else 
       {
        #echo "tel: ".$vTel;
        $asterisk->add($vTel,$sName);
      }
    }
  }
}
$asterisk->write();

?>
