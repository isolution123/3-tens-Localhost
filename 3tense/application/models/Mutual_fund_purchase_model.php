<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mutual_fund_purchase_model extends CI_Model{
    

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function bsc_scheme_list($search,$amc,$schemetype)
    {
        if($search=="")
        {
            $query = $this->db->query('SELECT Id,SchemeCode,SchemeName FROM BSC_SchemeMaster bs where  bs.PurchaseAllowed="Y" and bs.PurchaseTransactionmode!="D" and bs.SchemeCode not like "%L0" and bs.SchemeCode not like "%L1" limit 50');
            return $query->result();
        }
        
        else
        
        {
            $str='SELECT Id,SchemeCode,SchemeName
                    FROM BSC_SchemeMaster bs
                    where bs.PurchaseAllowed="Y" and bs.PurchaseTransactionmode!="D" and bs.SchemeCode not like "%L1" and bs.SchemeCode not like "%L0" and bs.SchemeName like "%'.$search.'%" ';
            if($amc!='')
            {
                $str=$str.' and ( bs.AMCCode= "'.$amc.'"';
                $str=$str.' or bs.AMCName= "'.$amc.'")';
            }
            if($schemetype!='')
            {
                $str=$str.' and bs.SchemeType= "'.$schemetype.'"';
            }
                $str=$str.' limit 50';
            
              
            $query = $this->db->query($str);
                    return $query->result();
        }
    }
     public function bsc_scheme_list_sip($search,$amc,$schemetype)
    {
        if($search=="")
        {
            $query = $this->db->query('SELECT Id,SchemeCode,SchemeName FROM BSC_SchemeMaster bs where bs.SIPFLAG="Y" and  bs.SchemeCode not like "%L1" limit 50');
            return $query->result();
        }
        
        else
        
        {
            $str='SELECT Id,SchemeCode,SchemeName
                    FROM BSC_SchemeMaster bs
                    where bs.SIPFLAG="Y" and  bs.SchemeCode not like "%L1" and bs.SchemeName like "%'.$search.'%" ';
            if($amc!='')
            {
                $str=$str.' and ( bs.AMCCode= "'.$amc.'"';
                $str=$str.' or bs.AMCName= "'.$amc.'")';
            }
            if($schemetype!='')
            {
                $str=$str.' and bs.SchemeType= "'.$schemetype.'"';
            }
                $str=$str.' limit 50';
            
                
            $query = $this->db->query($str);
                    return $query->result();
        }
    }
    
    public function bsc_scheme_amc_list($schemetype)
    {
        if($schemetype=="")
        {
            $query = $this->db->query('SELECT  AMCCode,max(AMCName) as AMCName
                    FROM BSC_SchemeMaster 
                    where ifnull(AMCName,"")!=""
                    and ifnull(AMCCode,"")!=""  group by AMCCode
                    order by AMCName');
                    return $query->result();
        }else{
            
            $query = $this->db->query('SELECT AMCCode,max(AMCName) as AMCName
                    FROM BSC_SchemeMaster
                    
                    where SchemeType = "'.$schemetype.'" and ifnull(AMCName,"")!=""
                    and ifnull(AMCCode,"")!="" 
                    group by AMCCode
                    order by AMCName');
                    return $query->result();
        }
    }
    public function bsc_scheme_amc_list_sip($schemetype)
    {
        if($schemetype=="")
        {
            $query = $this->db->query("SELECT distinct AMCCode,AMCName
                    FROM BSC_SchemeMaster where SIPFLAG='Y' order by AMCName ");
                    return $query->result();
        }else{
            
            $query = $this->db->query("SELECT distinct AMCCode,AMCName
                    FROM BSC_SchemeMaster
                     where SIPFLAG='Y' and SchemeType = '".$schemetype."' order by AMCName");
                    return $query->result();
        }
    }
    public function get_client_folionumber($schemeid,$clientcode)
    {
   
            $query = $this->db->query('SELECT DISTINCT mft.folio as folio_number FROM BSC_SchemeMaster bsc    
                                    inner join Bsc_MFFolio mft
                                    	on mft.ChannelPartnerCode=bsc.ChannelPartnerCode
                                    inner join BSC_ClientMaster_New cd
                                        ON cd.ClientCode="'.$clientcode.'"
                                        and mft.PanNo=cd.PrimaryHolderPAN
                                        and ((mft.JointHolder1PanNo=cd.SecondHolderPAN) or (ifnull(mft.JointHolder1PanNo,"")="" and  ifnull(cd.SecondHolderPAN,"")=""))
                                        and ((mft.JointHolder2PanNo=cd.ThirdHolderPAN) or (ifnull(mft.JointHolder2PanNo,"")="" and  ifnull(cd.ThirdHolderPAN,"")=""))
                                        and ((mft.GuardianPanNo=cd.GuardianPAN) or (ifnull(mft.GuardianPanNo,"")="" and  ifnull(cd.GuardianPAN,"")=""))
                                    WHERE bsc.AMCCode in (select AMCCode from BSC_SchemeMaster where SchemeCode="'.$schemeid.'")');
            return $query->result();
        
    }
    
    public function bsc_scheme_detail($condition)
    {
        $this->db->select('*');
        $this->db->from('BSC_SchemeMaster bs');
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
    public function bsc_scheme_type_list($condition)
    {
        $this->db->select('SchemeType');
        $this->db->distinct(true);
        $this->db->from('BSC_SchemeMaster bs');
        $this->db->where($condition);
        $this->db->order_by('bs.SchemeType', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function bsc_account_list($client_Id,$family_id,$broker_Id)
    {
        
        if($family_id=='')
        {
            
        $query = $this->db->query('SELECT distinct bc.id, bc.ClientCode as CLIENTCODE,
                                (case when bc.SecondHolderFirstName!="" then  CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName , " - ",bc.SecondHolderFirstName ," ", bc.SecondHolderLastName)  else CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) end)  as FIRSTAPPLICANTNAME1, 
                                bc.HoldingNature as CLIENTHOLDING, CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) as FIRSTAPPLICANTNAME, CONCAT(bc.SecondHolderFirstName," ",bc.SecondHolderLastName) as SECONDAPPLICANTNAME, 
                                CONCAT(bc.ThirdHolderFirstName," ",bc.ThirdHolderLastName) as THIRDAPPLICANTNAME, 
                                bc.Nominee1Name as  CLIENTNOMINEE, bc.AccountType1 as ACCTYPE1,bc.AccountNo1 as ACCNO1, bc.BankName1 as BANKNAME1,bc.BankBranch1 as BANKBRANCH1, bc.AccountType2 as ACCTYPE2, bc.AccountNo2 as ACCNO2, 
                                bc.BankName2 as BANKNAME2, bc.BANKBRANCH2 as BANKBRANCH2, bc.AccountType3 as ACCTYPE3, bc.AccountNo3 as ACCNO3, bc.BankName3 as BANKNAME3, bc.BANKBRANCH3 as BankBranch3, bc.Accounttype4 as ACCTYPE4, bc.AccountNo4 as ACCNO4,bc.BankName4 as BANKNAME4,bc.BANKBRANCH4 as BANKBRANCH4, 
                                bc.Accounttype5 as ACCTYPE5, bc.AccountNo5 as ACCNO5, bc.BankName5 as BANKNAME5,bc.BankBranch5 as BANKBRANCH5,c.head_of_family
                    FROM BSC_ClientMaster_New bc 
                    inner join clients as c on c.pan_no=bc.PrimaryHolderPAN 
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where c.client_id= "'.$client_Id.'" 
                    order by bc.PrimaryHolderFirstName ');
                    return $query->result();
                    
        }
        else
        {
            $query = $this->db->query('SELECT distinct bc.id, bc.ClientCode as CLIENTCODE,
                                (case when bc.SecondHolderFirstName!="" then  CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName , " - ",bc.SecondHolderFirstName ," ", bc.SecondHolderLastName)  else CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) end)  as FIRSTAPPLICANTNAME1, 
                                bc.HoldingNature as CLIENTHOLDING, CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) as FIRSTAPPLICANTNAME, CONCAT(bc.SecondHolderFirstName," ",bc.SecondHolderLastName) as SECONDAPPLICANTNAME, 
                                CONCAT(bc.ThirdHolderFirstName," ",bc.ThirdHolderLastName) as THIRDAPPLICANTNAME, 
                                bc.Nominee1Name as  CLIENTNOMINEE,
                                 bc.AccountType1 as ACCTYPE1,bc.AccountNo1 as ACCNO1, bc.BankName1 as BANKNAME1,bc.BankBranch1 as BANKBRANCH1, bc.AccountType2 as ACCTYPE2, bc.AccountNo2 as ACCNO2, 
                                bc.BankName2 as BANKNAME2, bc.BANKBRANCH2 as BANKBRANCH2, bc.AccountType3 as ACCTYPE3, bc.AccountNo3 as ACCNO3, bc.BankName3 as BANKNAME3, bc.BANKBRANCH3 as BankBranch3, bc.Accounttype4 as ACCTYPE4, bc.AccountNo4 as ACCNO4,bc.BankName4 as BANKNAME4,bc.BANKBRANCH4 as BANKBRANCH4, 
                                bc.Accounttype5 as ACCTYPE5, bc.AccountNo5 as ACCNO5, bc.BankName5 as BANKNAME5,bc.BankBranch5 as BANKBRANCH5,c.head_of_family
                    FROM BSC_ClientMaster_New bc 
                    inner join clients as c on c.pan_no=bc.PrimaryHolderPAN 
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where f.family_id = "'.$family_id.'" 
                    order by bc.PrimaryHolderFirstName ');
                    return $query->result();
        }
        
    
    }
     public function bsc_account_list_SIP($client_Id,$family_id,$broker_Id)
    {
        
        if($family_id=='')
        {
            
        $query = $this->db->query('SELECT distinct bc.id, bc.ClientCode as CLIENTCODE,
                    (case when bc.SecondHolderFirstName!="" then  CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName , " - ",bc.SecondHolderFirstName ," ", bc.SecondHolderLastName)  else CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) end)  as FIRSTAPPLICANTNAME1, 
                    bc.HoldingNature as CLIENTHOLDING, CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) as FIRSTAPPLICANTNAME, CONCAT(bc.SecondHolderFirstName," ",bc.SecondHolderLastName) as SECONDAPPLICANTNAME, 
                    CONCAT(bc.ThirdHolderFirstName," ",bc.ThirdHolderLastName) as THIRDAPPLICANTNAME, 
                    bc.Nominee1Name as CLIENTNOMINEE,
                    cam.BANKACCOUNTNUMBER as ACCNO1,cam.BANKNAME as BANKNAME1,cam.BANKBRANCH as BANKBRANCH1,
                    c.head_of_family
                    FROM BSC_ClientMaster_New bc 
                    inner join BSC_ClientAccountMandateMaster as cam on cam.CLIENTCODE=bc.ClientCode and (cam.Status="APPROVED" or cam.Status="REGISTERED BY MEMBER")
                    inner join clients as c on c.pan_no=bc.PrimaryHolderPAN 
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'"                     
                    where c.client_id= "'.$client_Id.'" 
                    order by bc.PrimaryHolderFirstName ');
                    return $query->result();
                    
        }
        else
        {
            $query = $this->db->query('SELECT distinct bc.id, bc.ClientCode as CLIENTCODE,
                    (case when bc.SecondHolderFirstName!="" then  CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName , " - ",bc.SecondHolderFirstName ," ", bc.SecondHolderLastName)  else CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) end)  as FIRSTAPPLICANTNAME1, 
                    bc.HoldingNature as CLIENTHOLDING, CONCAT(bc.PrimaryHolderFirstName," ",bc.PrimaryHolderLastName) as FIRSTAPPLICANTNAME, CONCAT(bc.SecondHolderFirstName," ",bc.SecondHolderLastName) as SECONDAPPLICANTNAME, 
                    CONCAT(bc.ThirdHolderFirstName," ",bc.ThirdHolderLastName) as THIRDAPPLICANTNAME, 
                    bc.Nominee1Name as CLIENTNOMINEE,
                    cam.BANKACCOUNTNUMBER as ACCNO1,cam.BANKNAME as BANKNAME1,cam.BANKBRANCH as BANKBRANCH1,
                    c.head_of_family
                    FROM BSC_ClientMaster_New bc 
                    inner join BSC_ClientAccountMandateMaster as cam on cam.CLIENTCODE=bc.ClientCode and (cam.Status="APPROVED" or cam.Status="REGISTERED BY MEMBER")
                    inner join clients as c on c.pan_no=bc.PrimaryHolderPAN 
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where f.family_id = "'.$family_id.'" 
                    order by bc.PrimaryHolderFirstName ');
                    return $query->result();
        }
        
    
    }
    public function find_client_familyId($client_Id,$broker_Id)
    {
        $query = $this->db->query('SELECT distinct c.family_id,c.head_of_family
                    FROM  clients as c
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where c.client_id= "'.$client_Id.'"');
        return $query->result();
    
    }
    
    public function bsc_bank_list($ClientCode)
    {
        $query = $this->db->query('SELECT bc.id, bc.ClientCode as CLIENTCODE,
                    bc.AccountNo1 as ACCNO1,bc.IFSCCode1 as NEFTIFSCCODE1,bc.BankName1 as BANKNAME1,bc.DefaultBankFlag1 as DEFAULTBANKFLAG1,
                    bc.AccountNo2 as ACCNO2,bc.IFSCCode2 as NEFTIFSCCODE2,bc.BankName2 as BANKNAME2,bc.DefaultBankFlag2 as DEFAULTBANKFLAG2,
                    bc.AccountNo3 as ACCNO3,bc.IFSCCode3 as NEFTIFSCCODE3,bc.BankName3 as BANKNAME3,bc.DefaultBankFlag3 as DefaultBankFlag3,
                    bc.AccountNo4 as ACCNO4,bc.IFSCCode4 as NEFTIFSCCODE4,bc.BankName4 as BANKNAME4,bc.DefaultBankFlag4 as DEFAULTBANKFLAG4,
                    bc.AccountNo5 as ACCNO5,bc.IFSCCode5 as NEFTIFSCCODE5,bc.BankName5 as BANKNAME5,bc.DefaultBankFlag5 as DEFAULTBANKFLAG5 
                    FROM BSC_ClientMaster_New bc 
                    where bc.ClientCode= "'.$ClientCode.'"');
        return $query->result();
    
    }
    public function bsc_mendate_bank_list($ClientCode)
    {
        $query = $this->db->query('SELECT MANDATECODE,
                                          CLIENTCODE,
                                          BANKNAME
                    FROM BSC_ClientAccountMandateMaster 
                    WHERE  (Status="APPROVED" or Status="REGISTERED BY MEMBER")
                    and CLIENTCODE= "'.$ClientCode.'"');
        
        return $query->result();
    
    }
    
      public function get_clientid_by_bsc_account($Account,$broker_Id)
    {
         $query = $this->db->query('SELECT distinct c.client_id
                    FROM BSC_ClientMaster_New bc 
                    inner join clients as c on c.pan_no=bc.PrimaryHolderPAN 
                    inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where bc.ClientCode= "'.$Account.'"');
                    return $query->result();
                    
    
    }
    
    public function bsc_account_detail($CLIENTCODE)
    {
        $query = $this->db->query('SELECT  Id, MemberCode, ClientCode as CLIENTCODE, PrimaryHolderFirstName, PrimaryHolderMiddleName, PrimaryHolderLastName, TaxStatus, Gender, PrimaryHolderDOBIncorporation, OccupationCode, HoldingNature, SecondHolderFirstName, SecondHolderMiddleName, SecondHolderLastName, ThirdHolderFirstName, ThirdHolderMiddleName, ThirdHolderLastName, SecondHolderDOB, ThirdHolderDOB, GuardianFirstName, GuardianMiddleName, GuardianLastName, GuardianDOB, PrimaryHolderPANExempt, SecondHolderPANExempt, ThirdHolderPANExempt, GuardianPANExempt, PrimaryHolderPAN, SecondHolderPAN, ThirdHolderPAN, GuardianPAN, PrimaryHolderExemptCategory, SecondHolderExemptCategory, ThirdHolderExemptCategory, GuardianExemptCategory, ClientType, PMS, DefaultDP, CDSLDPID, CDSLCLTID, CMBPId, NSDLDPID, NSDLCLTID, AccountType1, AccountNo1, MICRNo1, IFSCCode1, BankName1, BankBranch1, DefaultBankFlag1, Bank1CreatedAt, Bank1LastModifiedAt, Bank1Status, AccountType2, AccountNo2, MICRNo2, IFSCCode2, BankName2, BankBranch2, DefaultBankFlag2, Bank2CreatedAt, Bank2LastModifiedAt, Bank2Status, Accounttype3, AccountNo3, MICRNo3, IFSCCode3, BankName3, BankBranch3, DefaultBankFlag3, Bank3CreatedAt, Bank3LastModifiedAt, Bank3Status, Accounttype4, AccountNo4, MICRNo4, IFSCCode4, BankName4, BankBranch4, DefaultBankFlag4, Bank4CreatedAt, Bank4LastModifiedAt, Bank4Status, Accounttype5, AccountNo5, MICRNo5, IFSCCode5, BankName5, BankBranch5, DefaultBankFlag5, Bank5CreatedAt, Bank5LastModifiedAt, Bank5Status, ChequeName, Divpaymode, Address1, Address2, Address3, City, State, Pincode, Country, ResiPhone, ResiFax, OfficePhone, OfficeFax, Email, CommunicationMode, ForeignAddress1, ForeignAddress2, ForeignAddress3, ForeignAddressCity, ForeignAddressPincode, ForeignAddressState, ForeignAddressCountry, ForeignAddressResiPhone, ForeignAddressFax, ForeignAddressOffPhone, ForeignAddressOffFax, IndianMobileNo, Nominee1Name, Nominee1Relationship, Nominee1ApplicablePer, Nominee1MinorFlag, Nominee1DOB, Nominee1Guardian, Nominee2Name, Nominee2Relationship, Nominee2ApplicablePer, Nominee2DOB, Nominee2MinorFlag, Nominee2Guardian, Nominee3Name, Nominee3Relationship, Nominee3ApplicablePer, Nominee3DOB, Nominee3MinorFlag, Nominee3Guardian, PrimaryHolderKYCType, PrimaryHolderCKYCNumber, SecondHolderKYCType, SecondHolderCKYCNumber, ThirdHolderKYCType, ThirdHolderCKYCNumber, GuardianKYCType, GuardianCKYCNumber, PrimaryHolderKRAExemptRefNo, SecondHolderKRAExemptRefNo, ThirdHolderKRAExemptRefNo, GuardianExemptRefNo, AadhaarUpdated, MapinId, Paperlessflag, LEINo, LEIValidity, EmailDeclarationFlag, MobileDeclarationFlag, Branch, Dealer, NominationOpt, NominationAuthenticationMode, Nominee1PAN, Nominee1GuardianPAN, Nominee2PAN, Nominee2GuardianPAN, Nominee3PAN, Nominee3GuardianPAN, SecondHolderEmail, SecondholderEmailDeclaration, SecondholderMobile, SecondholderMobileDeclaration, ThirdHolderEmail, ThirdholderEmailDeclaration, ThirdholderMobile, ThirdholderMobileDeclaration, NominationFlag, NominationAuthenticationDate, CreatedBy, CreatedAt, LastModifiedBy, LastModifiedAt
                    FROM BSC_ClientMaster_New bc 
                    where bc.ClientCode= "'.$CLIENTCODE.'"');
        return $query->result();
    
    }
    
    function find_scheme_detail($schemename,$schemetype,$account)
    {
         $str="SELECT distinct @s:=@s+1 RowNo, min(bs.id) as id,bs.SchemeCode,bs.SchemeName,bs.SchemeType FROM BSC_SchemeMaster bs ,(select @s:=0) as s where bs.PurchaseAllowed='Y' and bs.RedemptionAllowed = 'Y' and bs.SwitchFLAG='Y'  ";
                    
        if($schemename)
        {
            
            $str= $str.' and  FIND_IN_SET(bs.SchemeCode,"'.$schemename.'")';
        }
         if($schemetype)
        {
             $str=$str. ' and  bs.SchemeType= "'.$schemetype.'"';
        }
        
        $str.=' group by bs.SchemeCode,bs.SchemeName,bs.SchemeType ';
        
        
        
        
        $query = $this->db->query($str);  
        return $query->result();
    }


    public function add_transcation_request($requestData)
    {
        if(!$this->db->insert('BSC_Transcation_Request',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    
    public function add_transcation_response($responseData)
    {
        if(!$this->db->insert('BSC_Transcation_Response',$responseData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    
    public function add_payment_request($requestData)
    {
        if(!$this->db->insert('BSC_Payment_Request',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    public function add_payment_response($responseData)
    {
        if(!$this->db->insert('BSC_Payment_Response',$responseData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }

    public function get_transcation_response($orderId)
    {
        $query = $this->db->query('SELECT res.OrderId as OrderId,res.UserID as UserID,res.MemberId as MemberId,res.ClientCode as ClientCode,
        req.Amount as Amount,res.UniqueReferenceNumber as UniqueReferenceNumber
        
                    FROM BSC_Transcation_Response as res
                    inner join BSC_Transcation_Request as req on req.TransNo = res.UniqueReferenceNumber 
                    where res.OrderId = "'.$orderId.'"');
        return $query->result();
    }
    public function get_payment_response($orderId)
    {
       
        $query = $this->db->query('SELECT t2.* FROM `BSC_Payment_Request` t1
                            inner join BSC_Payment_Response t2 on t1.`internalrefno`=t2.internalrefno
                            WHERE t1.ordernumber = "'.$orderId.'"');
        return $query->result();
    }
    
    public function get_today_trancation_detail($ClientCode)
    {
        $query = $this->db->query('SELECT TransactionCode FROM BSC_Transcation_Response 
                    where  CAST(CreatedOn AS DATE)= CAST(now() AS DATE) and ClientCode = "'.$ClientCode.'"');
        return $query->result();
    }
    
    public function check_bsc_scheme_by_unique_no($uniqueNo)
    {
         $query = $this->db->query('SELECT id FROM BSC_SchemeMaster WHERE UniqueNo= "'.$uniqueNo.'"');
        return $query->result();
    }
    public function add_bsc_scheme($requestData)
    {
         if(!$this->db->insert('BSC_SchemeMaster',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    function update_bsc_scheme($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('BSC_SchemeMaster', $data);
        return true;
    }
    
    public function check_bsc_client_by_client_code($clientcode)
    {
         $query = $this->db->query('SELECT Id FROM BSC_ClientMaster_New WHERE ClientCode= "'.$clientcode.'"');
        return $query->result();
    }
    public function check_bsc_client_by_client_code_new($clientcode)
    {
         $query = $this->db->query('SELECT Id FROM BSC_ClientMaster_New WHERE ClientCode= "'.$clientcode.'"');
        return $query->result();
    }
    public function add_bsc_client($requestData)
    {
         if(!$this->db->insert('BSC_ClientMaster',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
     public function add_bsc_client_new($requestData)
    {
         if(!$this->db->insert('BSC_ClientMaster_New',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    function update_bsc_client($data,$id)
    {
        $this->db->where('Id', $id);
        $this->db->update('BSC_ClientMaster', $data);
        return true;
    }

    function update_bsc_client_new($data,$id)
    {
        $this->db->where('Id', $id);
        $this->db->update('BSC_ClientMaster_New', $data);
        return true;
    }

     public function bsc_account_detail_SIP_By_BSEClientCode($clientCode)
    {
        
        
            $query = $this->db->query('SELECT distinct bc.id, bc.CLIENTCODE,
                    
                    FROM BSC_ClientMaster_New bc 
                    inner join BSC_ClientAccountMandateMaster as cam on cam.CLIENTCODE=bc.CLIENTCODE and cam.Status="APPROVED"
                    where  bc.CLIENTCODE = "'.$clientCode.'" 
                    order by bc.FIRSTAPPLICANTNAME ');
                    return $query->result();
        
    }


    
    public function check_bsc_client_mandate_by_mandate_code($MANDATECODE)
    {
         $query = $this->db->query('SELECT Id FROM BSC_ClientAccountMandateMaster WHERE MANDATECODE= "'.$MANDATECODE.'"');
        return $query->result();
    }
    public function add_bsc_client_mandate($requestData)
    {
         if(!$this->db->insert('BSC_ClientAccountMandateMaster',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    function update_bsc_client_mandate($data,$id)
    {
        $this->db->where('Id', $id);
        $this->db->update('BSC_ClientAccountMandateMaster', $data);
        return true;
    }
       public function get_fd_rate($IsSeniorCitizen)
    {
         $query = $this->db->query('SELECT * FROM FDRateMaster WHERE IsSeniorCitizen= "'.$IsSeniorCitizen.'" order by CompanyName,Period ');
        return $query->result();
    }
    public function get_fd_rate_detail($Id)
    {
         $query = $this->db->query('SELECT * FROM FDRateMaster WHERE Id= "'.$Id.'"');
        return $query->result();
    }
    
    
     public function check_bsc_client_folio($FolioNumber)
    {
         $query = $this->db->query('SELECT Id FROM Bsc_MFFolio WHERE Folio= "'.$FolioNumber.'"');
        return $query->result();
    }
    function update_client_mf_folio_master($data,$id)
    {
        $this->db->where('Id', $id);
        $this->db->update('Bsc_MFFolio', $data);
        return true;
    }
    public function add_client_mf_folio_master($requestData)
    {
         if(!$this->db->insert('Bsc_MFFolio',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
    
    public function fd_account_list($client_Id,$family_id,$broker_Id)
    {
        if($family_id=='')
        {
            $query = $this->db->query('SELECT distinct c.name,c.client_id from clients as c  where c.client_id= "'.$client_Id.'"  order by c.name ');
        }
        else
        {
            $query = $this->db->query('SELECT distinct c.name,c.client_id from clients as c  inner join families as f on c.family_id=f.family_id and f.broker_id="'.$broker_Id.'" 
                    where f.family_id = "'.$family_id.'"  order by c.name ');
        }
        return $query->result();
    
    }
    public function client_detail($client_Id,$broker_Id)
    {
        
        $query = $this->db->query('SELECT * from clients as c  where c.client_id= "'.$client_Id.'" ');
        
        return $query->result();
    
    }
    
    public function check_fd_rate($period,$CompanyName,$IsSeniorCitizen)
    {
         $query = $this->db->query('SELECT Id FROM FDRateMaster WHERE IsSeniorCitizen= "'.$IsSeniorCitizen.'" and Period= "'.$period.'" and CompanyName  = "'.$CompanyName.'" ');
        return $query->result();
    }
      public function add_fd_rate_master($requestData)
    {
         if(!$this->db->insert('FDRateMaster',$requestData)) {
            return $this->db->error();
        } else {
            return $this->db->insert_id();
        }
    }
     function update_fd_rate_master($data,$id)
    {
        $this->db->where('Id', $id);
        $this->db->update('FDRateMaster', $data);
        return true;
    }
}
