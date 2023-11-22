-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 05, 2023 at 11:17 AM
-- Server version: 5.7.43
-- PHP Version: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `threeten_3tense_db_test`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `completeInsurance` (IN `policyNum` VARCHAR(100))  NO SQL begin
select @finalBonus := annual_cumm, 
@planTypeName := iptn.plan_type_name from insurances im 
inner join ins_plans ipm on im.plan_id = ipm.plan_id 
inner join ins_plan_types iptn on 
iptn.plan_type_id = ipm.plan_type_id inner join premium_transactions pt	on pt.policy_number=im.policy_num
where pt.policy_number = policyNum;
if (planTypeName = 'Traditional')
then
	select @bonusCal := sum(bonus_calculation) from insurance_traditional_plans where policy_number = policyNumber;
	select @sumAssured := amt_insured, premiumPaidTillDate = prem_paid_till_date from insurances where policy_num = policyNum;
	set @finalBonusCal = @sumAssured * @finalBonus;
	set @maturityAmount = @bonusCal + @finalBonusCal + @premiumPaidTillDate;
	update insurances set fund_value = maturityAmount 
    where policy_num = policyNum;
elseif(planTypeName = 'Unit Linked')
then
	select @bonusCal := sum(annual_returns_calculation) from insurance_unit_linked_plans where policy_number = policyNumber;
	select @premiumPaidTillDate := prem_paid_till_date from insurances where policy_num = policyNum;
	set @maturityAmount = @bonusCal + @premiumPaidTillDate;
	update insurances set fund_value = @maturityAmount 
    where policy_num = policyNum;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `forEachBroker` ()   BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE brokerID VARCHAR(10);
  DECLARE b, c INT;
  DECLARE cur1 CURSOR FOR SELECT id FROM users WHERE (user_type = 'broker' AND broker_id IS NULL);
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN cur1;
  read_loop: LOOP
    FETCH cur1 INTO brokerID;
    IF done THEN
      LEAVE read_loop;
    END IF;
    INSERT INTO families(family_id, name, user_id, broker_id, `status`) 
    VALUES(familyID(brokerID), 'Default family', brokerID, brokerID, 
     		1);
  END LOOP;
  CLOSE cur1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_mutual_funds_trancation_amount` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL if(familyID !='') then
  
select 
     case when mft.transaction_type='Purchase' then ROUND(mft.amount*-1)  
          else ROUND(mft.amount) end as amount,
		mft.purchase_date,
f.family_id
		from mutual_fund_transactions mft 
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where 
		mft.broker_id = brokerID AND 
		f.family_id = familyID and
        mft.mutual_fund_type!='DIV'
		and mft.purchase_date<=reportDate
order by 		mft.purchase_date,mft.transaction_id;

else

select transaction_type,mutual_fund_type,mutual_fund_scheme,
     case when mft.transaction_type='Purchase' then ROUND(mft.amount*-1)  
          else ROUND(mft.amount) end as amount,
		mft.purchase_date
		from mutual_fund_transactions mft 
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where 
		mft.broker_id = brokerID AND 
		c.client_id = clientID
		and mft.mutual_fund_type!='DIV'
		and c.status=1 
		and mft.purchase_date<=reportDate
order by 		mft.purchase_date,mft.transaction_id;
end if$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_mutual_funds_trancation_amount_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20), IN `reportDate` DATE)  NO SQL if(familyID !='') then
  
select 
     case when mft.transaction_type='Purchase' then ROUND(mft.amount*-1)  
          else ROUND(mft.amount) end as amount,
		mft.purchase_date,
f.family_id
		from mutual_fund_transactions mft 
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where 
		mft.broker_id = brokerID AND 
		f.family_id = familyID 
        and (case when clientID='' then 1 
                when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
                else 0 end)=1 and
        mft.mutual_fund_type!='DIV'
		and mft.purchase_date<=reportDate
order by 		mft.purchase_date,mft.transaction_id;

else

select transaction_type,mutual_fund_type,mutual_fund_scheme,
     case when mft.transaction_type='Purchase' then ROUND(mft.amount*-1)  
          else ROUND(mft.amount) end as amount,
		mft.purchase_date
		from mutual_fund_transactions mft 
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where 
		mft.broker_id = brokerID AND 
		c.client_id = clientID
		and mft.mutual_fund_type!='DIV'
		and c.status=1 
		and mft.purchase_date<=reportDate
order by 		mft.purchase_date,mft.transaction_id;
end if$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mf_detail_aum_report_from_file` (IN `brokerID` VARCHAR(10))  NO SQL begin
    create temporary table if not exists `mf_detail_aum_report_from_file` as 
    (
		select 
			f.name as family_name, 
        	
			c.name as client_name, 
			mfs.scheme_name as mf_scheme_name,

			mft.folio_number, 
			mft.mutual_fund_type as mf_scheme_type, 
			mft.from_file			,
			Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date, 
			
			SUM(mfv.p_amount) as p_amount, 
			SUM(mfv.div_amount) as div_amount, 
			sum(mfv.live_unit) AS live_unit, 
			max(mfv.c_nav)  as c_nav, 			
			SUM(mfv.div_r2) as div_r2, 
			SUM(mfv.div_payout) as div_payout, 
			SUM(mfv.c_nav * mfv.live_unit) as current_value
			
		from mutual_fund_valuation mfv 
		inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id 
		inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id 
		inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id 
		inner join clients c on mft.client_id = c.client_id 
		inner join families f on c.family_id = f.family_id 
		where mfv.broker_id = brokerID
		and round((mfv.c_nav * mfv.live_unit)) > 3 
		group by f.name , 
			c.name , 
        		mfs.scheme_name ,
        	
			mft.folio_number ,
			mft.from_file	,
			mft.mutual_fund_type			,
			mfv.c_nav_date
		);
		
	select * from mf_detail_aum_report_from_file order by 
			family_name,
			client_name, 
			mf_scheme_name, 
			folio_number;
    
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_abc` ()  NO SQL select * from users$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_asset_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT 
	c.name as client_name, pro.product_name, t.type_name, comp.company_name, sch.scheme_name, getAssetDate(a.asset_id) AS 'Period', 
	a.installment_amount, a.expected_mat_value, a.goal, a.asset_id, a.client_id, a.product_id, a.ref_number, TRIM(LEADING '0' FROM a.folio_no) as folio_no, 
	Date_format(a.start_date, '%d/%m/%Y') as start_date, 
	Date_format(a.end_date, '%d/%m/%Y') as end_date, a.narration, fam.name as family_name
FROM asset_transactions AS a 
INNER JOIN clients AS c 
	ON a.client_id = c.client_id LEFT JOIN al_products AS pro ON 
		a.product_id = pro.product_id 
inner join families as fam 
	on c.family_id = fam.family_id
left join al_types as t 
	on a.type_id = t.type_id 
left join al_companies as comp 
	on a.company_id = comp.company_id 
left join mutual_fund_schemes as sch 
	on a.scheme_id = sch.scheme_id 
WHERE (case when familyID='0' then 1 
			when familyID!='0' AND c.family_id = familyID then 1 end)=1 AND 
	  (a.end_date >= NOW()) AND 
	  a.broker_id = brokerID
ORDER BY c.report_order, c.name$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_calculate_live_units_mf` (IN `brokerID` VARCHAR(10))  NO SQL begin
Declare varRentAmount decimal(18,2);
select rent_amount into varRentAmount from reminder_days where broker_id=brokerId;
insert into today_reminders (reminder_type, client_id, client_name, broker_id, reminder_date, reminder_message) 
select 'Birthday Reminder', client_id, c.name, brokerID, CURRENT_DATE(), 
CONCAT('Many Many happy returns of the day ', c.name, ' :)') 
from clients c inner join families fam on c.family_id = fam.family_id where dob_app = 1 and 
fam.broker_id = brokerID and 
DATE_FORMAT(DATE_ADD(scriptDate, interval personalRem day), '%d') = DAYOFMONTH(dob) and DATE_FORMAT(DATE_ADD(scriptDate, interval personalRem day), '%m') = MONTH(dob) union all 
select 'Anniversary Reminder', client_id, c.name, brokerID, CURRENT_DATE(), 
'Wishing you a happy Marriage Anniversary :)' 
from clients c inner join families fam on c.family_id = fam.family_id where anv_app = 1 and 
fam.broker_id = brokerID and 
DATE_FORMAT(DATE_ADD(scriptDate, interval personalRem day), '%d') = DAYOFMONTH(anv_date) and 
DATE_FORMAT(DATE_ADD(scriptDate, interval personalRem day), '%m') = MONTH(anv_date) union all 
select 'Premium Due', i.client_id, c1.name, brokerID, CURRENT_DATE(), 
Concat('Premium Rs. ', round(i.prem_amt), ' for ', ipm.plan_name, ', Policy Number: ', i.policy_num, ' is due on ', DATE_FORMAT(i.next_prem_due_date, '%d/%m/%Y')) 
from insurances i inner join clients c1 on c1.client_id = i.client_id INNER JOIN ins_plans ipm 
on i.plan_id = ipm.plan_id 
where DATEDIFF(i.next_prem_due_date, DATE_ADD(scriptDate, interval insPremiumRem day)) = 0 and 
i.prem_amt >= insPremiumAmt and i.broker_id = brokerID union all 
select 'Grace Date', i.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT('Premium Rs. ', round(i.prem_amt), ' for ', ipm.plan_name, ', Policy Number:', i.policy_num, ' has a grace days till ', DATE_FORMAT(i.grace_due_date, '%d/%m/%Y')) 
from insurances i inner join clients c1 on c1.client_id=i.client_id INNER JOIN ins_plans ipm on 
i.plan_id = ipm.plan_id 
where DateDiff(i.grace_due_date, DATE_ADD(scriptDate, interval insGraceRem day)) = 0  and 
i.prem_amt >= insGraceAmt and i.broker_id = brokerID union all 
select 'Insurance Maturity', c1.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(ipm.plan_name, ', Policy Number:', im.policy_num, ' is getting matured on: ', DATE_FORMAT(pm.maturity_date, '%d/%m/%Y')) 
from premium_maturities pm inner join insurances im on pm.policy_num = im.policy_num inner join 
clients c1 on c1.client_id = im.client_id INNER JOIN ins_plans ipm on im.plan_id = ipm.plan_id 
INNER JOIN ins_plan_types ipt on ipt.plan_type_id=ipt.plan_type_id
where datediff(pm.maturity_date, DATE_ADD(scriptDate, interval insMaturityRem day)) = 0 and 
(pm.amount >= insMaturityAmt || ipt.plan_type_name='General Insurance') and im.broker_id = brokerID union all 
select 'Fixed Income Maturity',c1.client_id, c1.name, brokerID, CURRENT_DATE(), CONCAT('Rs. ',round(fdt.maturity_amount),' Maturity amount is getting matured from ',fdc.fd_comp_name,', Ref. No.: ',fdt.ref_number,' on ',DATE_FORMAT(fdt.maturity_date, '%d/%m/%Y')) from fd_transactions fdt inner join clients c1 on fdt.client_id=c1.client_id INNER JOIN fd_companies fdc ON fdc.fd_comp_id=fdt.fd_comp_id where datediff(fdt.maturity_date, DATE_ADD(scriptDate, interval fdMaturityRem day)) = 0 and fdt.maturity_amount >= fdMaturityAmt and fdt.broker_id = brokerID union all  
select 'Fixed Income Payout', c1.client_id, c1.name, brokerID, CURRENT_DATE(), CONCAT('Rs. ',round(fdi.interest_amount),' ',fdt.interest_mode,' interest for ',fdit.fd_inv_type,' in  ',fdc.fd_comp_name,', Ref. No.: ',fdt.ref_number,' on ',DATE_FORMAT(fdi.interest_date, '%d/%m/%Y')) from fd_transactions fdt inner join fd_interests fdi on fdi.fd_transaction_id=fdt.fd_transaction_id inner join clients c1 on fdt.client_id=c1.client_id INNER JOIN fd_companies fdc ON fdc.fd_comp_id=fdt.fd_comp_id INNER JOIN fd_investment_types fdit ON fdit.fd_inv_id=fdt.fd_inv_id where datediff(fdi.interest_date, DATE_ADD(scriptDate, interval fdIntRem day)) = 0 and fdi.interest_amount >= fdIntAmt and fdt.broker_id = brokerID
union all 
select 'Asset', at.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' ', als.scheme_name, ' Rs. ', round(am.maturity_amount), ' is due on ', DATE_FORMAT(am.maturity_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from asset_transactions at inner join clients c1 on c1.client_id=at.client_id INNER JOIN asset_maturity am on 
am.asset_id = at.asset_id  inner join al_products alp on
alp.product_id=at.product_id inner join al_types alt on
alt.type_id=at.type_id inner join al_schemes als on
als.scheme_id=at.scheme_id
where DateDiff(am.maturity_date, DATE_ADD(scriptDate, interval varAssetRem day)) = 0  and 
am.maturity_amount >= varAssetAmount and at.broker_id = brokerID
union all 
select 'Liabilty', lt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' of ', alc.company_name,' ', als.scheme_name, ' Rs. ', round(lm.maturity_amount), ' is due on ', DATE_FORMAT(lm.maturity_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from liability_transactions lt inner join clients c1 on c1.client_id=lt.client_id INNER JOIN liability_maturity lm on 
lm.liability_id = lt.liability_id  inner join al_products alp on
alp.product_id=lt.product_id inner join al_types alt on
alt.type_id=lt.type_id inner join al_schemes als on
als.scheme_id=lt.scheme_id inner join al_companies alc on
alc.company_id=lt.company_id 
where DateDiff(lm.maturity_date, DATE_ADD(scriptDate, interval varAssetRem day)) = 0  and 
lm.maturity_amount >= varAssetAmount and lt.broker_id = brokerID
union all 
select 'Rent', pt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(pt.property_name,' ', pt.property_location,' rent of Rs. ', round(prd.amount), ' is due on ', DATE_FORMAT(prd.rent_date, '%d/%m/%Y'))
from property_transactions pt inner join clients c1 on c1.client_id=pt.client_id INNER JOIN property_rents pr on 
pr.pro_transaction_id = pt.pro_transaction_id  inner join property_rent_details prd on
prd.rent_id=pr.pro_rent_id
where DateDiff(prd.rent_date,scriptDate) = 0  and 
prd.amount >= varRentAmount and pt.broker_id = brokerID
union all
select 'Last Rent Reminder', pt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(pt.property_name,' ', pt.property_location,' rent of Rs. ', round(prd.amount), ' is due on ', DATE_FORMAT(max(prd.rent_date), '%d/%m/%Y'))
from property_transactions pt inner join clients c1 on c1.client_id=pt.client_id inner join property_rent_details prd on
prd.pro_transaction_id=pt.pro_transaction_id
where pt.pro_transaction_id in (select prd2.pro_transaction_id from property_rent_details prd2 group by (prd2.pro_transaction_id) having max(prd2.rent_date)=scriptDate)  and 
prd.amount >= varRentAmount and pt.broker_id = brokerID
union all
select 'Last Liabilty', lt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' of ', alc.company_name,' ', als.scheme_name, ' Rs. ', round(lt.installment_amount), ' is due on ', DATE_FORMAT(lt.end_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from liability_transactions lt inner join clients c1 on 
c1.client_id=lt.client_id  inner join al_products alp on
alp.product_id=lt.product_id inner join al_types alt on
alt.type_id=lt.type_id inner join al_schemes als on
als.scheme_id=lt.scheme_id inner join al_companies alc on
alc.company_id=lt.company_id 
where liability_id IN (select lt2.liability_id from liability_transactions lt2 group by lt2.liability_id having max(lt2.end_date)=scriptDate) and 
lt.installation_amount >= varAssetAmount and lt.broker_id = brokerID;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cash_flow_report_client` (IN `clientID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10))   BEGIN
  SET @sql = NULL;
  SET @name = NULL;
  SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN `investment` = "', investment,'" THEN `amount` END) AS `', investment, '`'))
  INTO @sql FROM (
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_fd where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_fd_maturity where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_rent where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance_premium where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance_life_cover where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID

      ) AS a;
  SET @name = 'CONCAT(a.name, CASE WHEN dob IS NOT NULL THEN CONCAT(" [Age ",(a.`year` - DATE_FORMAT(c.dob,"%Y")),"]") ELSE "" END)';
  SET @sql = CONCAT('SELECT ',@name,' AS client_name, a.client_id, a.year, ', @sql, ' FROM (
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_fd where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_fd_maturity where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union  all
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_rent where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance_premium where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select investment,client_id,comp_date,year,amount,name,broker_id from cf_insurance_life_cover where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 

                   ) AS a 
        INNER JOIN clients c on c.client_id = a.client_id
		WHERE a.client_id = "',clientID,'" and a.broker_id = "',brokerID,'"  
        AND (a.comp_date BETWEEN "',fromDate,'" AND "',toDate,'") 
        GROUP BY a.name, a.client_id, a.`year` 
        ORDER BY a.name');
  /*SELECT @sql as `sql`;*/
  /*comment below lines in case of mysql bug which gives error of Re-prepare statement 
	and find some other way to generate report */
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cash_flow_report_client_Commitments` (IN `clientID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10))   BEGIN
  SET @sql = NULL;
  SET @name = NULL;
  SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN investment = "', investment,'" THEN amount END) AS ', investment, ''))
  INTO @sql FROM (

select 
	'Commitments' AS investment,
	c.client_id AS client_id,
	lm.maturity_date AS comp_date,
    	year(lm.maturity_date) AS year,
	lm.maturity_amount AS amount,
    	c.name AS name,
    	lt.broker_id AS broker_id 
from liability_maturity lm 
inner join liability_transactions lt 
	on lt.liability_id = lm.liability_id 
join threeten_3tense_db.clients c 
     	on lt.client_id = c.client_id 
where year(lm.maturity_date) = year(curdate())
and lt.broker_id = brokerID
and lm.maturity_date BETWEEN fromDate AND toDate
union all 
select 
	'Commitments' AS investment,
    	c.client_id AS client_id,
    	am.maturity_date AS maturity_date,
	year(am.maturity_date) AS year,
	am.maturity_amount AS amount,
	c.name AS name,
	att.broker_id AS broker_id 
from asset_maturity am 
Inner join threeten_3tense_db.asset_transactions att 
	on att.asset_id = am.asset_id 
Inner join threeten_3tense_db.clients c 
     on att.client_id = c.client_id 
where year(am.maturity_date) = year(curdate())
and 	att.broker_id = brokerID
and am.maturity_date BETWEEN fromDate AND toDate


      ) AS a;
  SET @name = 'CONCAT(a.name, CASE WHEN dob IS NOT NULL THEN CONCAT(" [Age ",(a.year - DATE_FORMAT(c.dob,"%Y")),"]") ELSE "" END)';
  SET @sql = CONCAT('SELECT ',@name,' AS client_name, a.client_id, a.year, ', @sql, ' FROM (

select 
	''Commitments'' AS investment,
	c.client_id AS client_id,
	lm.maturity_date AS comp_date,
    	year(lm.maturity_date) AS year,
	lm.maturity_amount AS amount,
    	c.name AS name,
    	lt.broker_id AS broker_id 
from liability_maturity lm 
inner join liability_transactions lt 
	on lt.liability_id = lm.liability_id 
join threeten_3tense_db.clients c 
     	on lt.client_id = c.client_id 
where year(lm.maturity_date) = year(curdate())
att.broker_id=  "',brokerID,'" 
and lm.maturity_date BETWEEN "',fromDate,'" AND "',toDate,'"

union all 
select 
	''Commitments'' AS investment,
    	c.client_id AS client_id,
    	am.maturity_date AS maturity_date,
	year(am.maturity_date) AS year,
	am.maturity_amount AS amount,
	c.name AS name,
	att.broker_id AS broker_id 
from asset_maturity am 
Inner join threeten_3tense_db.asset_transactions att 
	on att.asset_id = am.asset_id 
Inner join threeten_3tense_db.clients c 
     on att.client_id = c.client_id 
where year(am.maturity_date) = year(curdate())
att.broker_id=  "',brokerID,'" 
and am.maturity_date BETWEEN "',fromDate,'" AND "',toDate,'"
   
                   ) AS a 
        INNER JOIN clients c on c.client_id = a.client_id
		WHERE a.client_id = "',clientID,'" and a.broker_id = "',brokerID,'" 
        AND (a.comp_date BETWEEN "',fromDate,'" AND "',toDate,'") 
        GROUP BY a.name, a.client_id, a.year 
        ORDER BY a.name');

  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cash_flow_report_family` (IN `familyID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10))   BEGIN
  SET @sql = NULL;
  SET @name = NULL;
  SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN `investment` = "', investment,'" THEN `amount` END) AS `', investment, '`'))
  INTO @sql FROM (
        select * from cf_fd  where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select * from cf_fd_maturity where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select * from cf_rent where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select * from cf_insurance where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select * from cf_insurance_premium  where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID
        union all
        select * from cf_insurance_life_cover where comp_date BETWEEN fromDate AND toDate and broker_id =brokerID

      ) AS a;
  SET @name = 'CONCAT(a.name, CASE WHEN dob IS NOT NULL THEN CONCAT(" [Age ",(a.`year` - DATE_FORMAT(c.dob,"%Y")),"]") ELSE "" END)';
  SET @sql = CONCAT('SELECT ', @name, ' AS client_name, a.client_id, a.year, ', @sql, ' FROM (
                    select * from cf_fd where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'"  
                    union all
                    select * from cf_fd_maturity  where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union  all
                    select * from cf_rent where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select * from cf_insurance where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select * from cf_insurance_premium where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 
                    union all
                    select * from cf_insurance_life_cover where comp_date BETWEEN "',fromDate,'" AND "',toDate,'" and broker_id = "',brokerID,'" 

                   ) AS a 
        INNER JOIN clients c on c.client_id = a.client_id
		WHERE a.client_id IN (select client_id from clients where family_id = "',familyID,'") and a.broker_id = "',brokerID,'" 
        AND (a.comp_date BETWEEN "',fromDate,'" AND "',toDate,'") 
        GROUP BY a.name, a.client_id, a.`year` 
        ORDER BY a.year, c.report_order, a.name');
  /*SELECT @sql as `sql`;*/
  /*comment below lines in case of mysql bug which gives error of Re-prepare statement 
	and find some other way to generate report */
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cash_flow_report_family_Commitments` (IN `familyID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10))   BEGIN
  SET @sql = '';
  SET @name = '';
  SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN `investment` = "', investment,'" THEN `amount` END) AS `', investment, '`'))
  INTO @sql FROM (
select 
	'Commitments' AS investment,
	c.client_id AS client_id,
	lm.maturity_date AS comp_date,
    	year(lm.maturity_date) AS year,
	lm.maturity_amount AS amount,
    	c.name AS name,
    	lt.broker_id AS broker_id 
from liability_maturity lm 
inner join liability_transactions lt 
	on lt.liability_id = lm.liability_id 
join threeten_3tense_db.clients c 
     	on lt.client_id = c.client_id 
where year(lm.maturity_date) = year(curdate())
and lt.broker_id = brokerID
and lm.maturity_date BETWEEN fromDate AND toDate
union all 
select 
	'Commitments' AS investment,
    	c.client_id AS client_id,
    	am.maturity_date AS maturity_date,
	year(am.maturity_date) AS year,
	am.maturity_amount AS amount,
	c.name AS name,
	att.broker_id AS broker_id 
from asset_maturity am 
Inner join threeten_3tense_db.asset_transactions att 
	on att.asset_id = am.asset_id 
Inner join threeten_3tense_db.clients c 
     on att.client_id = c.client_id 
where year(am.maturity_date) = year(curdate())
and 	att.broker_id = brokerID
and am.maturity_date BETWEEN fromDate AND toDate



      ) AS a;
  SET @name = 'CONCAT(a.name, CASE WHEN dob IS NOT NULL THEN CONCAT(" [Age ",(a.`year` - DATE_FORMAT(c.dob,"%Y")),"]") ELSE "" END)';
  SET @sql = CONCAT('SELECT ', @name, ' AS client_name, a.client_id, a.year, ', @sql, ' FROM (
                  

select 
	''Commitments'' AS investment,
	c.client_id AS client_id,
	lm.maturity_date AS comp_date,
    	year(lm.maturity_date) AS year,
	lm.maturity_amount AS amount,
    	c.name AS name,
    	lt.broker_id AS broker_id 
from liability_maturity lm 
inner join liability_transactions lt 
	on lt.liability_id = lm.liability_id 
join threeten_3tense_db.clients c 
     	on lt.client_id = c.client_id 
where year(lm.maturity_date) = year(curdate())
and lt.broker_id =  "',brokerID,'" 
and lm.maturity_date BETWEEN "',fromDate,'" AND "',toDate,'"
union all 
select 
	''Commitments'' AS investment,
    	c.client_id AS client_id,
    	am.maturity_date AS maturity_date,
	year(am.maturity_date) AS year,
	am.maturity_amount AS amount,
	c.name AS name,
	att.broker_id AS broker_id 
from asset_maturity am 
Inner join threeten_3tense_db.asset_transactions att 
	on att.asset_id = am.asset_id 
Inner join threeten_3tense_db.clients c 
     on att.client_id = c.client_id 
where year(am.maturity_date) = year(curdate())
and att.broker_id =  "',brokerID,'" 
and am.maturity_date BETWEEN "',fromDate,'" AND "',toDate,'"
                   ) AS a 
        INNER JOIN clients c on c.client_id = a.client_id
		WHERE a.client_id IN (select client_id from clients where family_id = "',familyID,'") and a.broker_id = "',brokerID,'" 
        AND (a.comp_date BETWEEN "',fromDate,'" AND "',toDate,'") 
        GROUP BY a.name, a.client_id, a.`year` 
        ORDER BY a.year, c.report_order, a.name');

  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_Client_dashboard_data` (IN `clientID` VARCHAR(20), IN `brokerID` VARCHAR(20))   Begin
DECLARE brokerID0 VARCHAR(10);
Declare varTotal_life_cover int;
Declare varTotal_portfolio int;
Declare varLiability int;
Declare varNetWorth int;
Declare varTraditionalPaid decimal(18,2);
Declare varUnitLikedPaid decimal(18,2);
Declare varGeneralPaid decimal(18,2);
Declare varPrem_paid_till_date decimal(18,2);
Declare varFdInvested int;
Declare varFdReturn int;
Declare varPropertyPurchase decimal(18,2);
Declare varPropertyCurrent decimal(18,2);
Declare varDebt decimal(18,2);
Declare varEquity decimal(18,2);
Declare varHybrid decimal(18,2);
Declare varEMIassests decimal(18,2);
Declare varInstallmentAssests decimal(18,2);
Declare varSIPAssests decimal(18,2);
Declare varFDTotal decimal(18,2);
Declare varTotalEquityPortfolio decimal(18,2);
Declare varTop1share varchar(20);
Declare varTop2share varchar(20);
Declare varTop3share varchar(20);
Declare varTop4share varchar(20);
Declare varTop5share varchar(20);
Declare varTopQty1 decimal(18,2);
Declare varTopQty2 decimal(18,2);
Declare varTopQty3 decimal(18,2);
Declare varTopQty4 decimal(18,2);
Declare varTopQty5 decimal(18,2);
Declare varMFLastPurhase int;
Declare varMFLastRed int;
Declare varUpcomingPremDue decimal(18,2);
Declare varUpcomingMat decimal(18,2);
Declare varUpcomingFDMat decimal(18,2);
Declare varUcompingFDInterest decimal(18,2);
Declare varUpcomingAssetsAndLia decimal(18,2);
Declare  assets  decimal(18,2);
Declare  liability  decimal(18,2);
Declare varUpcomingAssetsAndLiaDue decimal(18,2);
Declare  assets_due  decimal(18,2);
Declare  liability_due  decimal(18,2);
declare varMFTotal decimal(18,2);
Declare varDebitBal decimal(18,2);
Declare varRETotal decimal(18,2);
Declare varCommodityTotal decimal(18,2);
Declare varInsuranceTotal decimal(18,2);
Declare varPurchase_Amount decimal(18,2);
Declare varCurrent_Amount decimal(18,2);
declare div_payout_total_amount decimal(18,0);

SELECT CASE WHEN broker_id IS NULL THEN id ELSE broker_id END INTO brokerID0 FROM users 
WHERE broker_id = brokerID OR id = brokerID 
LIMIT 1;
SET brokerID = brokerID0;
/*select sum(mfv.p_amount) into varPurchase_Amount */
select sum(mft.nav * mfv.live_unit) into varPurchase_Amount
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     where mfv.broker_id = brokerID AND mft.client_id = clientID
     and round((mfv.c_nav * mfv.live_unit)) > 3
	 and mft.mutual_fund_type != 'DIV'
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date;
/*select sum(amount) into varPurchase_Amount
from mutual_fund_transactions
where mutual_fund_type in('PIP','IPO','SWI','TIN')
and broker_id = brokerID AND client_id = clientID;*/
select
     sum(mfv.c_nav * mfv.live_unit) into varCurrent_Amount
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     where mfv.broker_id = brokerID AND mft.client_id = clientID
     and round((mfv.c_nav * mfv.live_unit)) > 3
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date;
select calculateTotalInsuranceInv(clientID,brokerID) into varInsuranceTotal ;
select calculateTotalFD(clientID,brokerID) into varFDTotal;
select calculateTotalMFCurrentVal(clientID,brokerID) into varMFTotal;
select calculateTotalShares(clientID,brokerID) into  varTotalEquityPortfolio;
select calculateTotalProperties(clientID,brokerID) into varRETotal;
select calculateTotalCommodity(clientID,brokerID) into varCommodityTotal;
select sum(total) into varDebitBal  from (select client_id,broker,sum(balance) as total from client_brokers where client_id=clientID group by broker )x;
select sum(maturity_amount) into varUpcomingAssetsAndLiaDue from(select * from (select c.name,am.maturity_date,ap.product_name,at.ref_number ,am.maturity_amount from asset_transactions at inner join al_products ap on at.product_id=ap.product_id inner join clients c on c.client_id=at.client_id inner join asset_maturity am on am.asset_id=at.asset_id where c.user_id=brokerID and c.client_id=clientID and maturity_date>= curdate() union select c.name,lm.maturity_date,lp.product_name,lt.ref_number ,lm.maturity_amount from liability_transactions lt inner join al_products lp on lt.product_id=lp.product_id inner join clients c on c.client_id=lt.client_id inner join liability_maturity lm on lm.liability_id=lt.liability_id where c.user_id=brokerID and c.client_id=clientID and maturity_date>= curdate() ) as abc order by maturity_date asc limit 5)x;
select case when sum(total) is null then 0 else sum(total) end into assets from (select installment_amount as total from asset_transactions where end_date>= curdate() and client_id=clientID limit 5)x;
select case when sum(total) is null then 0 else sum(total) end into  liability from (select installment_amount as total from liability_transactions where end_date>= curdate() and client_id=clientID limit 5)x;
select  (assets+liability) into varUpcomingAssetsAndLia;
select sum(total) into  varUcompingFDInterest from( SELECT c.name, fdi.interest_date, fdc.fd_comp_name, fdt.ref_number, ROUND(fdi.interest_amount) as total FROM fd_transactions as fdt INNER JOIN fd_companies as fdc ON fdt.fd_comp_id = fdc.fd_comp_id INNER JOIN clients as c ON fdt.client_id = c.client_id INNER JOIN fd_interests as fdi ON fdi.fd_transaction_id =fdt.fd_transaction_id WHERE fdt.user_id = brokerID and fdi.interest_date >= curdate() and fdt.client_id = clientID ORDER BY fdi.interest_date asc LIMIT 5 )x;
select sum(max_amount) into varUpcomingFDMat from(SELECT maturity_amount as max_amount FROM `fd_transactions` as `fdt` INNER JOIN `clients` as `c` ON `fdt`.`client_id` = `c`.`client_id` INNER JOIN `families` as `fam` ON `fam`.`family_id` = `c`.`family_id` INNER JOIN `fd_investment_types` as `fdi` ON `fdt`.`fd_inv_id` = `fdi`.`fd_inv_id` INNER JOIN `fd_companies` as `fdc` ON `fdt`.`fd_comp_id` = `fdc`.`fd_comp_id` INNER JOIN `advisers` as `adv` ON `fdt`.`adv_id` = `adv`.`adviser_id`  WHERE `fdt`.`broker_id` = brokerID and `fdt`.`maturity_date` >= curdate() and fdt.client_id = clientID ORDER BY `fdt`.`transaction_date` DESC, `fdt`.`maturity_date` asc LIMIT 5)x;
select sum(mat_sum) into varUpcomingMat from( select (p.amount) as mat_sum from insurances i inner join premium_maturities p  on i.policy_num=p.policy_num  where p.maturity_date >= curdate() and  i.status in(1,2,3,4) and  i.client_id=clientID   order by p.maturity_date asc limit 5)x;
select sum(max_amt) into  varUpcomingPremDue from (select i.prem_amt as max_amt  from   insurances i where i.next_prem_due_date >=curdate() and i.client_id=clientID and  i.status in(1,2,3,4) order by i.next_prem_due_date  limit 5)x;
select sum(max_amount) into varMFLastPurhase from  (select amount as  max_amount from mutual_fund_transactions e  where e.client_id=clientID  and e.mutual_fund_type IN('PIP','IPO','NFO','TIN') order by purchase_date desc limit 5)x;
select  sum(max_amount) into varMFLastRed from  (select e.amount as  max_amount from mutual_fund_transactions e where e.client_id=clientID and e.mutual_fund_type in('RED','DP') order by e.purchase_date DEsc limit 5)x;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop1share,varTopQty1 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code  where e.client_id=clientID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 0,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop2share,varTopQty2 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code  where e.client_id=clientID   group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 1,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop3share,varTopQty3 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code  where e.client_id=clientID   group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 2,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop4share,varTopQty4 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code  where e.client_id=clientID   group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 3,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop5share,varTopQty5 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code  where e.client_id=clientID   group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 4,1;
SELECT  calculateTotalLifeCover(clientID,brokerID)  into  varTotal_life_cover ;
select  total_portfolio(clientID,brokerID) into varTotal_portfolio ;
select sum(lm.maturity_amount) into varLiability from liability_transactions as lt inner join liability_maturity as lm on lm.liability_id=lt.liability_id WHERE client_id =clientID  AND (lm.maturity_date >= curdate()) and broker_id =brokerID;
select (varTotal_portfolio-(case when varLiability is null then 0 else varLiability end)) into varNetWorth;
Select sum(prem_paid_till_date) into varTraditionalPaid from insurances where plan_type_id=1 and client_id=clientID and status IN(1,2,3,4);
Select sum(prem_paid_till_date) into varUnitLikedPaid from insurances where plan_type_id=2 and client_id=clientID and status IN(1,2,3,4);
Select sum(prem_paid_till_date) into varGeneralPaid from insurances where plan_type_id=3 and client_id=clientID and status IN(1,2,3,4);
select sum(premium_amount) into  varPrem_paid_till_date from premium_transactions where  client_id= clientID;
select sum(maturity_amount) into varFdReturn from  fd_transactions where client_id=clientID;
select sum(amount_invested) into varFdInvested from  fd_transactions where client_id=clientID;
select sum(property_area*transaction_rate) into varPropertyPurchase from property_transactions where property_name NOT IN(select property_name from property_transactions where transaction_type='sale') and client_id=clientID and transaction_type='purchase';
select  sum(property_area*current_rate) into varPropertyCurrent from property_transactions where property_name NOT IN(select property_name from property_transactions where transaction_type='sale') and client_id=clientID and transaction_type='purchase';
/*commented by Salmaan - 2018-08-22
select sum(mfv.live_unit* msh.current_nav) into varDebt from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Debt","Capital Protection","FMP","Liquid","LT Debt")
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and mft.client_id = clientID and round((mfv.c_nav * mfv.live_unit)) > 3;
select sum(mfv.live_unit* msh.current_nav) into varEquity from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and mft.client_id = clientID and round((mfv.c_nav * mfv.live_unit)) > 3;
select sum(mfv.live_unit* msh.current_nav) into varHybrid from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Hybrid","Balanced","MIP")
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and mft.client_id = clientID and round((mfv.c_nav * mfv.live_unit)) > 3;
below new queries added - Salmaan - 2018-08-22*/

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varDebt from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")
and mft.client_id = clientID;

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varEquity from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
and mft.client_id = clientID;

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varHybrid from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Hybrid","Balanced","MIP")
and mft.client_id = clientID;


select sum(installment_amount) into varEMIassests from  asset_transactions where type_id="2" and client_id=clientID;
select sum(installment_amount) into varInstallmentAssests from  asset_transactions where type_id="4" and client_id=clientID;
select sum(installment_amount) into varSIPAssests from  asset_transactions where type_id="5" and client_id=clientID;

  select 
        sum(mfv.div_payout) into div_payout_total_amount
        
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund","Hybrid","Balanced","MIP","Debt","Capital Protection","FMP")
 ;

select varCurrent_Amount,varPurchase_Amount,varInsuranceTotal,varMFTotal,varRETotal,varCommodityTotal,varDebitBal,varUpcomingAssetsAndLiaDue,varUpcomingAssetsAndLia,varUcompingFDInterest,varUpcomingFDMat,varUpcomingMat,varUpcomingPremDue,varMFLastRed,varMFLastPurhase,varTopQty1,varTopQty2,varTopQty3,varTopQty4,varTopQty5,varTop1share,varTop2share,varTop3share,varTop4share,varTop5share,varTotalEquityPortfolio,varFDTotal,varTotal_life_cover,varTotal_portfolio,varLiability,varNetWorth,varTraditionalPaid,varUnitLikedPaid,varGeneralPaid,varPropertyPurchase,varPropertyCurrent,varDebt,varEquity,varHybrid,varFdInvested,varFdReturn,varPrem_paid_till_date,varEMIassests,varInstallmentAssests,varSIPAssests,div_payout_total_amount;

end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_commodity_by_client_report` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin 
select *, quantity*transaction_rate as `total_amount`, quantity*current_rate as `market_value`, 
(quantity*current_rate - quantity*transaction_rate) as `unrealised_gain`, 
case when quantity*transaction_rate <> 0 then (quantity*current_rate - quantity*transaction_rate)*100/(quantity*transaction_rate) else 0 end as `abs`, 
case when datediff(now(),temp_trans_date) <> 0 then ((quantity*current_rate - quantity*transaction_rate)*100/(quantity*transaction_rate))*365/datediff(now(),temp_trans_date) else 0 end as `cagr`, 
datediff(now(),temp_trans_date) as `datediff` 
from (
SELECT ct.commodity_trans_id, fam.family_id, fam.name as family_name, ct.client_id, c.name as client_name, ct.transaction_date as `temp_trans_date`, 
    Date_Format(ct.transaction_date, "%d/%m/%Y") as transaction_date, ct.commodity_item_id, ci.item_name, ct.transaction_rate,
    (ct.quantity - case when commoditySale(ct.commodity_trans_id) is not null then commoditySale(ct.commodity_trans_id) else 0.00 end) as `quantity`, ct.commodity_unit_id, cu.unit_name, ct.quality, ct.transaction_type, ct.adviser_id, a.adviser_name, 
    ct.initial_investment, Date_Format(ct.added_on, "%d/%m/%Y") as added_on, Date_Format(ct.updated_on, "%d/%m/%Y") as updated_on,
    cr.current_rate, ct.sale_ref 
    from commodity_transactions ct 
    inner join clients as c on ct.client_id = c.client_id 
    inner join families as fam on fam.family_id = c.family_id 
    inner join commodity_items as ci on ci.item_id = ct.commodity_item_id 
    inner join commodity_units as cu on cu.unit_id = ct.commodity_unit_id 
    inner join commodity_rates as cr on cr.item_id = ct.commodity_item_id and cr.unit_id = ct.commodity_unit_id and 
cr.broker_id=ct.broker_id
    inner join advisers as a on a.adviser_id = ct.adviser_id 
    where ct.transaction_type = 'Purchase' and c.client_id = clientID and ct.broker_id = brokerID 
    order by c.report_order)
    as x WHERE quantity != 0;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_commodity_by_family_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin 
select *, quantity*transaction_rate as `total_amount`, quantity*current_rate as `market_value`, 
(quantity*current_rate - quantity*transaction_rate) as `unrealised_gain`, 
case when quantity*transaction_rate <> 0 then (quantity*current_rate - quantity*transaction_rate)*100/(quantity*transaction_rate) else 0 end as `abs`, 
case when datediff(now(),temp_trans_date) <> 0 then ((quantity*current_rate - quantity*transaction_rate)*100/(quantity*transaction_rate))*365/datediff(now(),temp_trans_date) else 0 end as `cagr`, 
datediff(now(),temp_trans_date) as `datediff` 
from (
SELECT ct.commodity_trans_id, fam.family_id, fam.name as family_name, ct.client_id, c.name as client_name, ct.transaction_date as `temp_trans_date`, 
    Date_Format(ct.transaction_date, "%d/%m/%Y") as transaction_date, ct.commodity_item_id, ci.item_name, ct.transaction_rate,
    (ct.quantity - case when commoditySale(ct.commodity_trans_id) is not null then commoditySale(ct.commodity_trans_id) else 0 end) as `quantity`, ct.commodity_unit_id, cu.unit_name, ct.quality, ct.transaction_type, ct.adviser_id, a.adviser_name, 
    ct.initial_investment, Date_Format(ct.added_on, "%d/%m/%Y") as added_on, Date_Format(ct.updated_on, "%d/%m/%Y") as updated_on,
    cr.current_rate, ct.sale_ref 
    from commodity_transactions ct 
    inner join clients as c on ct.client_id = c.client_id 
    inner join families as fam on fam.family_id = c.family_id 
    inner join commodity_items as ci on ci.item_id = ct.commodity_item_id 
    inner join commodity_units as cu on cu.unit_id = ct.commodity_unit_id 
    inner join commodity_rates as cr on cr.item_id = ct.commodity_item_id and cr.unit_id = ct.commodity_unit_id and 
cr.broker_id=ct.broker_id
    inner join advisers as a on a.adviser_id = ct.adviser_id 
    where ct.transaction_type = 'Purchase' and c.family_id = familyID and ct.broker_id = brokerID 
    order by c.report_order)
    as x WHERE quantity != 0;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_dashboard_data` (IN `brokerId` VARCHAR(50))  NO SQL Begin
Declare varActiveClient int;
Declare varInActiveClient int;
Declare varTraditionalPaid decimal(18,2);
Declare varUnitLikedPaid decimal(18,2);
Declare varGeneralPaid decimal(18,2);
Declare varHeldFD decimal(18,2);
Declare varNonHeldFD decimal(18,2);
Declare varPropertyPurchase decimal(18,2);
Declare varPropertyCurrent decimal(18,2);
Declare varHeldInvested decimal(18,2);
Declare varNonHeldInvested decimal(18,2);
Declare varDebt decimal(18,2);
Declare varEquity decimal(18,2);
Declare varHybrid decimal(18,2);
/*Client*/
Select count(client_id) into varActiveClient from clients c inner join families f on c.family_id=f.family_id where c.status=1 and f.broker_id=BrokerId;
Select count(client_id) into varInActiveClient from clients c inner join families f on c.family_id=f.family_id where c.status=0 and f.broker_id=BrokerId;
/*Insurance*/
Select sum(prem_paid_till_date) into varTraditionalPaid from insurances where plan_type_id=1 and broker_id=BrokerId and status NOT IN(5,6,7);
Select sum(prem_paid_till_date) into varUnitLikedPaid from insurances where plan_type_id=2 and broker_id=BrokerId and status NOT IN(5,6,7);
Select sum(prem_paid_till_date) into varGeneralPaid from insurances where plan_type_id=3 and broker_id=BrokerId and status NOT IN(5,6,7);
/*FD*/
select sum(amount_invested) into varHeldFD from fd_transactions fd inner join advisers ad on ad.adviser_id=fd.adv_id where held_type='Held' and fd.broker_id=BrokerId  and status='Active';
select sum(amount_invested) into varNonHeldFD from fd_transactions fd inner join advisers ad on ad.adviser_id=fd.adv_id where held_type!='Held' and fd.broker_id=BrokerId and status='Active';
/*Property*/
select sum(property_area*transaction_rate) into varPropertyPurchase from property_transactions where property_name NOT IN(select property_name from property_transactions where transaction_type='sale') and broker_id=BrokerId and transaction_type='purchase';
select  sum(property_area*current_rate) into varPropertyCurrent from property_transactions where property_name NOT IN(select property_name from property_transactions where transaction_type='sale') and broker_id=BrokerId and transaction_type='purchase';
/*Equity*/
/*select sum(round(quantity*acquiring_rate)) into varHeldInvested from equities e inner join trading_brokers t on t.trading_broker_id=e.trading_broker_id inner join client_brokers cb on cb.broker=t.trading_broker_id  where cb.held_type='Held' and e.broker_id=BrokerId;*/
select sum(round(e.quantity*s.close_rate)) into varHeldInvested from equities e inner join scrip_rates s on s.scrip_code = e.scrip_code left outer join client_brokers cb on cb.client_code=e.client_code where cb.held_type='held' and e.broker_id=BrokerId;
select sum(round(e.quantity*s.close_rate)) into varNonHeldInvested from equities e inner join scrip_rates s on s.scrip_code = e.scrip_code left outer join client_brokers cb on cb.client_code=e.client_code where cb.held_type='non-held' and e.broker_id=BrokerId;
/*Mutual Funds*/
select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varDebt from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Debt","Capital Protection","FMP")
and mfv.broker_id = brokerId;
select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varEquity from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
and mfv.broker_id = brokerId;
select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varHybrid from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
where mst.scheme_type IN("Hybrid","Balanced","MIP")
and mfv.broker_id = brokerId;
Select varActiveClient, varInActiveClient,varTraditionalPaid,varUnitLikedPaid,varGeneralPaid,varHeldFD,varNonHeldFD,varPropertyPurchase,varPropertyCurrent,varHeldInvested,varNonHeldInvested,varDebt,varEquity,varHybrid;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_equity_summary_report_client_import` (IN `brokerID` VARCHAR(10))  NO SQL begin 

insert into equities_monthly_summary(value,client_id)
select SUM(ROUND(e.quantity*sr.close_rate )) AS value,c.client_id
        from equities e
        inner join scrip_rates sr on sr.scrip_code = e.scrip_code
        inner join clients c on c.client_id = e.client_id 
        inner join families f on f.family_id=c.family_id
		WHERE f.broker_id = brokerID 
        group by c.client_id;
        
       select 'Success' as result;
 end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_family_wise_summary_aum_report` (IN `brokerID` VARCHAR(10))  NO SQL begin
create temporary table if not exists `mf_report_client_summary` as (
select 
    f.name as family_name,
    sum(mfv.p_amount) as Amount, 
    sum(mfv.div_amount) as Div_Amount,
    sum(mfv.live_unit) as Units,
	( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as payout,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
	(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
from mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
inner join clients c on mft.client_id = c.client_id
inner join families f on f.family_id=c.family_id
where mfv.broker_id = brokerID
    and c.status=1
    and c.name not like '%(NH)%'
	and round((mfv.c_nav * mfv.live_unit)) > 3
group by f.name);
select * from mf_report_client_summary order by current_value desc;

end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_funds_info` (IN `clientID` VARCHAR(30), IN `clientCode` VARCHAR(100), IN `famID` VARCHAR(20), IN `userID` VARCHAR(10))   begin
/*Declare total_portfolio float;*/
delete from `temp_funds_info` where user_id = userID; 
if(clientCode IS NOT NULL && clientCode != '')
then
INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    userID AS `user_id` FROM 
    (select client_id,transaction_date,amount,'add' as `type_of_trans` 
     from add_funds 
     where client_id=clientID and shares_app='1' and client_code=clientCode 
     union all 
     select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     from withdraw_funds 
     where client_id=clientID and withdraw_from='Equity' and 
     client_code=clientCode)      
     as x;
elseif(clientID IS NOT NULL && clientID != '' && (clientCode IS NULL || clientCode = ''))
then
INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    userID AS `user_id` FROM 
    (select client_id,transaction_date,amount,'add' as `type_of_trans` 
     from add_funds 
     where client_id=clientID and shares_app='1' 
     union all 
     select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     from withdraw_funds 
     where client_id=clientID and withdraw_from='Equity')      
     as x;
elseif(famID IS NOT NULL && famID != '')
then
INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    userID AS `user_id` FROM 
    (select client_id,transaction_date,amount,'add' as `type_of_trans` 
     from add_funds 
     where client_id in (select client_id from clients where family_id=famID) and shares_app='1' 
     union all 
     select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     from withdraw_funds 
     where client_id in (select client_id from clients where family_id=famID) and withdraw_from='Equity')      
     as x;
end if;
SELECT * FROM `temp_funds_info` WHERE user_id = userID order by transaction_date;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_funds_info_history` (IN `clientID` VARCHAR(50), IN `clientCode` VARCHAR(100), IN `famID` VARCHAR(20), IN `userID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin
/*Declare total_portfolio float;*/
delete from `temp_funds_info` where user_id = userID; 
if(clientCode IS NOT NULL && clientCode != '')
then

	INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    		CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    		CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    		userID AS `user_id` 
	FROM 
    		(select client_id,transaction_date,amount,'add' as `type_of_trans` 
     	from add_funds 
     	where client_id=clientID and shares_app='1' and client_code=clientCode 
		and transaction_date <=reportDate
     	
	union all 
     
	select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     	from withdraw_funds 
     	where client_id=clientID and withdraw_from='Equity' and 
     	client_code=clientCode
	and transaction_date <=reportDate) as x;
elseif(clientID IS NOT NULL && clientID != '' && (clientCode IS NULL || clientCode = ''))
then

	INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    		CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    		CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    		userID AS `user_id` FROM 
    (select client_id,transaction_date,amount,'add' as `type_of_trans` 
     from add_funds 
     where client_id=clientID and shares_app='1' 
		and transaction_date <=reportDate     
union all 
     select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     from withdraw_funds 
     where client_id=clientID and withdraw_from='Equity'
	and transaction_date <=reportDate
)      
     as x;
elseif(famID IS NOT NULL && famID != '')
then
INSERT INTO `temp_funds_info` (`client_id`,`transaction_date`,`add`,`withdraw`,`user_id`) 
	SELECT client_id,transaction_date,
    CASE WHEN (`type_of_trans`='add') THEN amount ELSE 0 END AS `add`,
    CASE WHEN (`type_of_trans`='withdraw') THEN amount ELSE 0 END AS `withdraw`,
    userID AS `user_id` FROM 
    (select client_id,transaction_date,amount,'add' as `type_of_trans` 
     from add_funds 
     where client_id in (select client_id from clients where family_id=famID) and shares_app='1' 
	and transaction_date <=reportDate
     union all 
     select client_id,transaction_date,amount,'withdraw' as `type_of_trans` 
     from withdraw_funds 
     where client_id in (select client_id from clients where family_id=famID) and withdraw_from='Equity'
	and transaction_date <=reportDate)      
     as x;
end if;
SELECT * FROM `temp_funds_info` WHERE user_id = userID order by transaction_date;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_funds_xirr` (IN `clientID` VARCHAR(30), IN `famID` VARCHAR(30), IN `type` VARCHAR(30))  NO SQL begin
Declare total_portfolio float;
Declare l_balance float;
DROP TABLE IF EXISTS `temp`;
create temporary table temp
(
	cash_flows float(18,2),
    data_date date,
    day_diff int
);
if(type = 'client_code')
then
insert into temp(cash_flows, data_date) 
select case when `add`=0 then withdraw else -(`add`) end as cash_flows, 
 transaction_date as data_date from temp_funds_info fi 
 inner join clients c on c.client_id = fi.client_id 
 where fi.client_id = clientID;
 select sum(e.quantity * sr.close_rate) from equities e 
 inner join scrip_rates sr on sr.scrip_code = e.scrip_code 
 inner join clients c on c.client_id = e.client_id 
 where c.client_id = clientID 
 into total_portfolio;
 Select SUM(balance) AS `balance` from client_brokers cb where cb.client_id = clientID into l_balance;
 insert into temp(cash_flows, data_date) values((total_portfolio+l_balance), now());
else 
insert into temp(cash_flows, data_date) 
select case when `add`=0 then withdraw else -(`add`) end as cash_flows, 
 transaction_date as data_date from temp_funds_info fi 
 inner join clients c on c.client_id = fi.client_id 
 where fi.client_id IN (select client_id from clients where family_id = famID);
 select sum(e.quantity * sr.close_rate) from equities e 
 inner join scrip_rates sr on sr.scrip_code = e.scrip_code 
 inner join clients c on c.client_id = e.client_id 
 where c.family_id = famID 
 into total_portfolio;
 Select SUM(balance) AS `balance` from client_brokers cb 
 inner join clients c on c.client_id = cb.client_id 
 where c.family_id = famID into l_balance;
 insert into temp(cash_flows, data_date) values((total_portfolio+l_balance), now());
end if;
begin 
Declare cashflow float;
Declare DataDate date;
Declare tempDate date;
Declare dd int;
DECLARE flag INT DEFAULT 0;
declare eq_csr cursor for 
select distinct cash_flows,data_date from temp order by data_date; 
DECLARE CONTINUE HANDLER FOR NOT FOUND SET flag = 1;
open eq_csr; 
myloop:LOOP
	fetch eq_csr into cashFlow,DataDate; 
    /*select tempDate as tempDate, DataDate as dataDate;*/
    if(tempDate is null)
	then
		set dd=1;
		set tempDate=DataDate;
	else
		set dd=datediff(DataDate,tempDate);
    end if;
	update temp set day_diff = dd where cash_flows=cashFlow and data_date=DataDate;
    if flag = 1 then
    	leave myloop;
    end if;
end loop myloop;
close eq_csr; 
end;
SELECT * from temp;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_client` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports 
		where client_id = "',clientID,'" and broker_id = "',brokerID,'" GROUP BY broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_client_historical` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports 
		where client_id = "',clientID,'" and broker_id = "',brokerID,'" GROUP BY broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports mfr inner join clients c on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_family_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin
SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports mfr inner join clients c on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_family_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `reportDate` DATE, IN `clientID` VARCHAR(500))  NO SQL begin
SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports mfr inner join clients c on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'"  and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
                    GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_current_value_summary_family_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `current_value` END) `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports;
  SET @sql = CONCAT('SELECT ', @sql, ', sum(current_value) as "Total" FROM mf_valuation_reports mfr inner join clients c on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" 
                    and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
                    and mfr.broker_id = "',brokerID,'" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_fd_by_client_report` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT date_format(fdt.issued_date, '%d/%m/%Y') as issued_date, 
c.name as 'client_name', TIMESTAMPDIFF(MONTH, fdt.issued_date, fdt.maturity_date) AS 'Term', 
ROUND(DATEDIFF(fdt.maturity_date, fdt.issued_date)/30) AS 'Term0', 
fdt.maturity_amount, fc.fd_comp_name AS 'issuing_authority', fdt.ref_number, 
date_format(fdt.maturity_date, '%d/%m/%Y') as maturity_date, fdt.amount_invested, 
fit.fd_inv_type AS 'type_of_investment', 
CASE WHEN fdt.status = 'New' THEN 'Active' ELSE fdt.status END AS 'status', 
Concat(fdt.interest_rate, '%') as 'interest_rate', fpm.payout_mode, 
fdt.fd_method, fdt.nominee, 
case when adv.held_type = 'Held' then usr.name else adv.adviser_name end AS 'broker_details', 
fdt.adjustment_ref_number, 
case when fdt.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag, 
fdt.adjustment, fdt.interest_mode 
FROM fd_transactions as fdt 
INNER JOIN fd_investment_types fit on fdt.fd_inv_id = fit.fd_inv_id 
inner join fd_companies as fc on fdt.fd_comp_id = fc.fd_comp_id 
left join fd_payout_modes fpm on fdt.maturity_payout_id = fpm.payout_mode_id 
inner join advisers AS adv ON adv.adviser_id = fdt.adv_id 
INNER JOIN users as usr ON fdt.broker_id = usr.id 
INNER JOIN clients as c ON fdt.client_id = c.client_id 
WHERE (c.client_id = clientID) AND (fdt.broker_id = brokerID) 
AND (fdt.status = 'Active') 
ORDER BY fdt.fd_transaction_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_fd_by_family_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT date_format(fdt.issued_date, '%d/%m/%Y') as issued_date,
c.name as 'client_name', fam.name as 'family_name',
TIMESTAMPDIFF(MONTH, fdt.issued_date, fdt.maturity_date) AS 'Term',
ROUND(DATEDIFF(fdt.maturity_date, fdt.issued_date)/30) AS 'Term0',
fdt.maturity_amount, fc.fd_comp_name AS 'issuing_authority',
fdt.ref_number, date_format(fdt.maturity_date, '%d/%m/%Y') as maturity_date,
fdt.amount_invested,
fit.fd_inv_type AS 'type_of_investment',
CASE WHEN fdt.status = 'New' THEN 'Active' ELSE fdt.status END AS 'status',
Concat(fdt.interest_rate, '%') as 'interest_rate',
fpm.payout_mode, fdt.fd_method, fdt.nominee,
Case when adv.held_type='Held' then usr.name else adv.adviser_name end AS 'broker_details',
fdt.adjustment_ref_number, c.report_order,
case when fdt.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag,
fdt.adjustment,
fdt.interest_mode
FROM fd_transactions as fdt
INNER JOIN fd_investment_types fit on fdt.fd_inv_id = fit.fd_inv_id
inner join fd_companies as fc on fdt.fd_comp_id = fc.fd_comp_id
left join fd_payout_modes fpm on fdt.maturity_payout_id = fpm.payout_mode_id
inner join advisers AS adv ON adv.adviser_id = fdt.adv_id
INNER JOIN clients as c ON fdt.client_id = c.client_id
inner join users as usr on fdt.broker_id = usr.id
inner JOIN families AS fam ON c.family_id = fam.family_id
WHERE (c.family_id = familyID)  AND (fdt.broker_id = brokerID) AND (fdt.status = 'Active')
ORDER BY c.report_order, c.name, fdt.fd_transaction_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_fund_options` (IN `policy_num` VARCHAR(50), IN `brokerID` VARCHAR(50))  NO SQL SELECT fund_option,value FROM fund_options where policy_number= policy_num and broker_id=brokerID order by value DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_gen_ins_by_client_report` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT DISTINCT  c.report_order, c.name, icm.ins_comp_name, ipm.plan_name, im.commence_date, im.policy_num, 
im.next_prem_due_date, im.maturity_date, findFirstMaturityDate(im.policy_num) AS 'maturity_date2', im.prem_amt, nom.name as 'nominee', 
im.amt_insured, floor(DATEDIFF(im.paidup_date, im.commence_date)/365 + 1) AS 'PPT',
floor(DATEDIFF(im.maturity_date, im.commence_date)/365) AS 'benefit_term', pmm.mode_name, 
floor(DATEDIFF(im.paidup_date, next_prem_due_date)/365 + 1) AS 'remaining_PPT', im.prem_paid_till_date, psm.status, im.fund_value, 
getFundValue(im.policy_num, im.broker_id) AS 'system_fund', 
case when im.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag, im.adjustment, adv.adviser_name 
FROM insurances AS im LEFT OUTER JOIN clients AS c ON c.client_id = im.client_id 
LEFT OUTER JOIN premium_maturities AS pm ON pm.policy_num = im.policy_num 
LEFT OUTER JOIN ins_plans AS ipm ON im.plan_id = ipm.plan_id 
LEFT OUTER JOIN premium_status AS psm ON im.status = psm.status_id 
LEFT OUTER JOIN premium_modes AS pmm ON im.mode = pmm.mode_id 
LEFT OUTER JOIN ins_companies AS icm ON icm.ins_comp_id = ipm.ins_comp_id 
LEFT OUTER JOIN advisers AS adv ON adv.adviser_id = im.adv_id
LEFT OUTER JOIN clients AS nom ON im.nominee = nom.client_id
LEFT OUTER JOIN ins_plan_types AS ipt ON im.plan_type_id = ipt.plan_type_id
WHERE (im.status NOT IN (SELECT status_id FROM premium_status 
WHERE (status IN ('Matured', 'Surrender', 'Paid Up Cancellation')))) AND (c.client_id = clientID) AND ipt.plan_type_name IN ('GENERAL INSURANCE') AND im.broker_id = brokerID
GROUP BY c.report_order, c.name, ipm.plan_name, im.policy_num, im.commence_date, im.next_prem_due_date, pm.maturity_date, 
im.prem_amt, im.nominee,im.amt_insured, im.paidup_date, im.maturity_date, im.mode, im.prem_paid_till_date, psm.status, 
im.fund_value, im.adjustment_flag, im.adjustment, pmm.mode_name, icm.ins_comp_name, adv.adviser_name
ORDER BY c.report_order, icm.ins_comp_name, ipm.plan_name, im.commence_date$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_gen_ins_by_family_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT DISTINCT c.report_order, c.name, fam.name as 'family_name', icm.ins_comp_name, ipm.plan_name, im.policy_num, im.commence_date, 
im.next_prem_due_date, im.maturity_date, findFirstMaturityDate(im.policy_num) AS 'maturity_date2', im.prem_amt, 
nom.name as 'nominee', im.amt_insured, floor(DATEDIFF(im.paidup_date, im.commence_date)/365 + 1) AS 'PPT',
floor(DATEDIFF(im.maturity_date, im.commence_date)/365) AS 'benefit_term', pmm.mode_name, 
floor(DATEDIFF(im.paidup_date, next_prem_due_date)/365 + 1) AS 'remaining_PPT', im.prem_paid_till_date, psm.status, 
im.fund_value, getFundValue(im.policy_num, im.broker_id) AS 'system_fund', case when im.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag, im.adjustment, adv.adviser_name
FROM insurances AS im LEFT OUTER JOIN clients AS c ON c.client_id = im.client_id LEFT OUTER JOIN 
premium_maturities AS pm ON pm.policy_num = im.policy_num LEFT OUTER JOIN ins_plans AS ipm ON 
im.plan_id = ipm.plan_id LEFT OUTER JOIN premium_status AS psm ON im.status = psm.status_id LEFT OUTER JOIN premium_modes AS pmm ON im.mode = pmm.mode_id LEFT OUTER JOIN ins_companies AS icm ON icm.ins_comp_id = ipm.ins_comp_id LEFT OUTER JOIN advisers AS adv ON adv.adviser_id = im.adv_id LEFT OUTER JOIN clients AS nom ON 
im.nominee = nom.client_id LEFT OUTER JOIN ins_plan_types AS ipt ON im.plan_type_id = ipt.plan_type_id LEFT OUTER JOIN families AS fam ON c.family_id = fam.family_id 
WHERE 
(im.status NOT IN (SELECT status_id FROM premium_status WHERE 
(`status` IN ('Matured', 'Surrender', 'Paid Up Cancellation')))) AND (c.client_id IN (SELECT client_id FROM 
clients WHERE (family_id = familyID)) AND (ipt.plan_type_name = 'GENERAL INSURANCE')) AND im.broker_id = brokerID GROUP BY c.report_order, c.name, ipm.plan_name, im.policy_num, im.commence_date, im.next_prem_due_date, 
pm.maturity_date, im.prem_amt, im.nominee,im.amt_insured, im.paidup_date, im.maturity_date, im.mode, 
im.prem_paid_till_date, psm.status, im.fund_value, im.adjustment_flag, im.adjustment, pmm.mode_name, 
icm.ins_comp_name, adv.adviser_name ORDER BY c.report_order, c.name, icm.ins_comp_name, ipm.plan_name, im.commence_date$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_insurance_by_client_report` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT Distinct c.report_order, c.name, icm.ins_comp_name, ipm.plan_name, im.commence_date,
im.policy_num, im.next_prem_due_date, 
im.maturity_date AS 'maturity_date',  im.prem_amt, nom.name as 'nominee',
im.amt_insured, 
floor(DATEDIFF(im.paidup_date, im.commence_date)/365)+1 AS 'PPT',
floor(DATEDIFF(im.maturity_date, im.commence_date)/365) AS 'benefit_term', pmm.mode_name, 
(case when im.Mode!=5 then floor(DATEDIFF(im.paidup_date, next_prem_due_date)/365 + 1) else 0 end) AS 'remaining_PPT', 
im.prem_paid_till_date, psm.status, im.fund_value, getFundValue(im.policy_num, im.broker_id) AS 'system_fund', 
case when im.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag, im.adjustment,
adv.adviser_name 
FROM insurances AS im LEFT OUTER JOIN clients AS c ON c.client_id = im.client_id 
LEFT OUTER JOIN premium_maturities AS pm ON pm.policy_num = im.policy_num 
LEFT OUTER JOIN ins_plans AS ipm ON im.plan_id = ipm.plan_id 
LEFT OUTER JOIN premium_status AS psm ON im.status = psm.status_id 
LEFT OUTER JOIN premium_modes AS pmm ON im.mode = pmm.mode_id 
LEFT OUTER JOIN ins_companies AS icm ON icm.ins_comp_id = ipm.ins_comp_id 
LEFT OUTER JOIN advisers AS adv ON adv.adviser_id = im.adv_id
LEFT OUTER JOIN clients AS nom ON im.nominee = nom.client_id
LEFT OUTER JOIN ins_plan_types AS ipt ON im.plan_type_id = ipt.plan_type_id
WHERE (im.status NOT IN (
select status_id from premium_status where status IN('Matured','Surrender','Paid Up Cancellation')) and 
ipt.plan_type_name IN ('TRADITIONAL', 'UNIT LINKED')) AND (c.client_id = clientID) AND im.broker_id = brokerID
GROUP BY c.report_order, c.name, ipm.plan_name, im.policy_num, im.commence_date, im.next_prem_due_date,
pm.maturity_date, im.prem_amt, im.nominee, im.amt_insured, im.paidup_date, im.maturity_date, im.mode,
im.prem_paid_till_date, psm.status, im.fund_value, im.adjustment_flag, im.adjustment, pmm.mode_name,
icm.ins_comp_name, adv.adviser_name
ORDER BY c.report_order, icm.ins_comp_name, ipm.plan_name, im.commence_date$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_insurance_by_family_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT Distinct c.report_order, c.name, fam.name as 'family_name', icm.ins_comp_name, ipm.plan_name, im.policy_num, im.commence_date, 
im.next_prem_due_date, im.maturity_date AS 'maturity_date', im.prem_amt, nom.name as 'nominee', 
im.amt_insured, floor(DATEDIFF(im.paidup_date, im.commence_date)/365)+1 AS 'PPT', 
floor(DATEDIFF(im.maturity_date, im.commence_date)/365) AS 'benefit_term', pmm.mode_name, 

(case when im.mode!=5 then floor(DATEDIFF(im.paidup_date, im.next_prem_due_date)/365 + 1) else 0 end) AS 'remaining_PPT', 

im.prem_paid_till_date, psm.status, im.fund_value, getFundValue(im.policy_num, im.broker_id) AS 'system_fund',  case when im.adjustment_flag = 0 then 'N.A' else 'Yes' end as adjustment_flag, im.adjustment, adv.adviser_name
FROM insurances AS im LEFT OUTER JOIN clients AS c ON c.client_id = im.client_id LEFT OUTER JOIN 
premium_maturities AS pm ON pm.policy_num = im.policy_num LEFT OUTER JOIN ins_plans AS ipm ON im.plan_id = ipm.plan_id 
LEFT OUTER JOIN premium_status AS psm ON im.status = psm.status_id LEFT OUTER JOIN 
premium_modes AS pmm ON im.mode = pmm.mode_id LEFT OUTER JOIN 
ins_companies AS icm ON icm.ins_comp_id = ipm.ins_comp_id LEFT OUTER JOIN 
advisers AS adv ON adv.adviser_id = im.adv_id LEFT OUTER JOIN clients AS nom ON im.nominee = nom.client_id
LEFT OUTER JOIN ins_plan_types AS ipt ON im.plan_type_id = ipt.plan_type_id LEFT OUTER JOIN families AS fam ON c.family_id = fam.family_id
WHERE (im.status NOT IN (SELECT status_id FROM premium_status WHERE
(`status` IN ('Matured', 'Surrender', 'Paid Up Cancellation')))) AND (im.client_id IN (SELECT client_id FROM clients WHERE
(family_id = familyID)) AND (ipt.plan_type_name <> 'GENERAL INSURANCE')) AND im.broker_id = brokerID
GROUP BY c.report_order, c.name, ipm.plan_name, im.policy_num, im.commence_date, im.next_prem_due_date,
pm.maturity_date, im.prem_amt, im.nominee, im.amt_insured, im.paidup_date, im.maturity_date, im.mode,
im.prem_paid_till_date, psm.status, im.fund_value, im.adjustment_flag, im.adjustment, pmm.mode_name,
icm.ins_comp_name, adv.adviser_name
ORDER BY c.report_order, c.name, icm.ins_comp_name, ipm.plan_name, im.commence_date$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_client` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` 
		where client_id = "',clientID,'" and broker_id = "',brokerID,'" and `mf_scheme_type` != "DIV" GROUP BY broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_client_historical` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` 
		where client_id = "',clientID,'" and broker_id = "',brokerID,'" and `mf_scheme_type` != "DIV" GROUP BY broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL BEGIN
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` `mfr` inner join `clients` `c` on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'" and `mf_scheme_type` != "DIV" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_family_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL BEGIN
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` `mfr` inner join `clients` `c` on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'" and `mf_scheme_type` != "DIV" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_family_historical_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(20), IN `reportDate` DATE, IN `clientID` VARCHAR(500))  NO SQL BEGIN
SET @sql = '';
SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` `mfr` inner join `clients` `c` on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and mfr.broker_id = "',brokerID,'" 
                    
                    and `mf_scheme_type` != "DIV" GROUP BY mfr.broker_id');
          
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	 EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
  
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_investment_summary_family_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL BEGIN
SET @sql = '';

SELECT GROUP_CONCAT(DISTINCT CONCAT('SUM(CASE WHEN scheme_type = "', scheme_type,'" THEN `p_nav` * `live_unit` END) AS `', scheme_type, '`'))
  INTO @sql FROM mf_valuation_reports where `mf_scheme_type` != 'DIV';
  
  SET @sql = CONCAT('SELECT ', @sql, ', sum(`p_nav` * `live_unit`) as `Total` FROM `mf_valuation_reports` `mfr` inner join `clients` `c` on mfr.client_id = c.client_id inner join families f on c.family_id = f.family_id
		where f.family_id = "',familyID,'" and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1 and
                    mfr.broker_id = "',brokerID,'" and `mf_scheme_type` != "DIV" GROUP BY mfr.broker_id');
  IF(@sql != '') THEN
  	PREPARE stmt FROM @sql;
  	EXECUTE stmt;
  	DEALLOCATE PREPARE stmt;
    /*SELECT @sql as `sql`;*/
  ELSE 
  	SELECT NULL;
  END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_re_by_client_report` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL select pt.pro_transaction_id, Date_format(pt.transaction_date, '%d/%m/%Y') as transaction_date, pt.transaction_type, pt.property_name, property_type_name, 
pt.property_location, pt.property_area, unit_name, pt.amount, pt.current_rate, pt.transaction_rate,  a.adviser_name, 
pt.remarks, abs, cagr, c.name as client_name, c.report_order, find_rent(pt.pro_transaction_id, now()) as rent_amount,
Date_format(pt.property_updated_on, '%d/%m/%Y') as property_updated_on
from property_transactions as pt inner join clients c on pt.client_id = c.client_id inner join property_types pTypes on pt.property_type_id = pTypes.property_type_id inner join property_units u on pt.property_unit_id = u.unit_id inner join advisers as a on 
a.adviser_id = pt.adviser_id where (pt.property_name not in (select property_name from property_transactions as pt1 group by property_name having (COUNT(property_name)>1))) and (pt.client_id = clientID) and (pt.broker_id = brokerID)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_re_by_family_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))   select pt.pro_transaction_id, Date_format(pt.transaction_date, '%d/%m/%Y') as transaction_date, pt.transaction_type, 
pt.property_name, property_type_name, pt.property_location, pt.property_area, unit_name, pt.amount, pt.current_rate, 
pt.transaction_rate,  a.adviser_name, pt.remarks, abs, cagr, c.name as client_name, c.report_order, fam.name as family_name,
find_rent(pt.pro_transaction_id, now()) as rent_amount, Date_format(pt.property_updated_on, '%d/%m/%Y') as property_updated_on
from property_transactions as pt inner join clients c on pt.client_id = c.client_id inner join property_types pTypes 
on pt.property_type_id = pTypes.property_type_id inner join property_units u on pt.property_unit_id = u.unit_id inner join families as fam on c.family_id = fam.family_id inner join advisers as a on a.adviser_id = pt.adviser_id where (pt.property_name not in (select property_name from 
property_transactions as pt1 group by property_name having (COUNT(property_name) > 1))) and 
(c.family_id = familyID) and (pt.broker_id = brokerID) order by c.report_order, c.name$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_HOF_dashboard_data` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))   Begin
DECLARE brokerID0 VARCHAR(10);
Declare varTotal_life_cover int;
Declare varTotal_portfolio int;
Declare varLiability int;
Declare varNetWorth int;
Declare varTraditionalPaid decimal(18,2);
Declare varUnitLikedPaid decimal(18,2);
Declare varGeneralPaid decimal(18,2);
Declare varFdReturn int;
Declare varFdInvested int;
Declare varPropertyPurchase int;
Declare varPropertyCurrent int;
Declare varDebt decimal(18,2);
Declare varEquity decimal(18,2);
Declare varHybrid decimal(18,2);
Declare varEMIassests decimal(18,2);
Declare varInstallmentAssests decimal(18,2);
Declare varSIPAssests decimal(18,2);
Declare varPrem_paid_till_date decimal(18,2);
Declare varTop1share varchar(20);
Declare varTop2share varchar(20);
Declare varTop3share varchar(20);
Declare varTop4share varchar(20);
Declare varTop5share varchar(20);
Declare varTopQty1 decimal(18,2);
Declare varTopQty2 decimal(18,2);
Declare varTopQty3 decimal(18,2);
Declare varTopQty4 decimal(18,2);
Declare varTopQty5 decimal(18,2);
Declare varMFLastPurhase int;
Declare varMFLastRed int;
Declare varUpcomingPremDue decimal(18,2);
Declare varUpcomingMat decimal(18,2);
Declare varUpcomingFDMat decimal(18,2);
Declare varUcompingFDInterest decimal(18,2);
Declare varUpcomingAssetsAndLia decimal(18,2);
declare varUpcomingAssetsAndLiaDue decimal(18,2);
Declare assets decimal(18,2);
Declare liability decimal(18,2);
Declare varTotalEquityPortfolio decimal(18,2);
Declare varMFTotal decimal(18,2);
Declare varRETotal decimal(18,2);
Declare varCommodityTotal decimal(18,2);
Declare varInsuranceTotal decimal(18,2);
Declare varFDTotal decimal(18,2);
Declare varPurchase_Amount decimal(18,2);
Declare varCurrent_Amount decimal(18,2);
Declare varDebitBal decimal(18,2);
Declare PerTotal decimal(18,2);
Declare PerTrad decimal(18,2);
Declare PerGen decimal(18,2);
Declare PerUnit decimal(18,2);
declare div_payout_total_amount decimal(18,0);
SELECT CASE WHEN broker_id IS NULL THEN id ELSE broker_id END INTO brokerID0 FROM users 
WHERE broker_id = brokerID OR id = brokerID 
LIMIT 1;
SET brokerID = brokerID0;
select sum(total) into varDebitBal  from (select cb.client_id,broker,sum(balance) as total from client_brokers cb inner join clients c on c.client_id=cb.client_id  where c.family_id=familyID group by broker )x;
select sum(mfv.p_amount) into varPurchase_Amount
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     where mfv.broker_id = brokerID AND c.family_id = familyID
     and round((mfv.c_nav * mfv.live_unit)) > 3
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date;
select
     sum(mfv.c_nav * mfv.live_unit) into varCurrent_Amount
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     where mfv.broker_id = brokerID AND c.family_id = familyID
     and round((mfv.c_nav * mfv.live_unit)) > 3
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date;
select sum(x.inv), sum(x.fd), sum(x.cval), sum(x.eq), sum(x.prop), sum(x.comm)
into  varInsuranceTotal,varFDTotal, varMFTotal,varTotalEquityPortfolio,varRETotal,varCommodityTotal
from (SELECT c.name,
calculateTotalInsuranceInv(c.client_id,f.broker_id) as inv,
calculateTotalFD(c.client_id,f.broker_id) as fd,
calculateTotalMFCurrentVal(c.client_id,f.broker_id) as cval
      ,
calculateTotalShares(c.client_id,f.broker_id) as eq ,
calculateTotalProperties(c.client_id,f.broker_id) as prop,
calculateTotalCommodity(c.client_id,f.broker_id) as comm
FROM clients c
INNER JOIN families f ON f.family_id = c.family_id
WHERE c.family_id = familyID AND f.broker_id = brokerID
) as x;
select sum(maturity_amount) into varUpcomingAssetsAndLiaDue from (select * from (select c.name,am.maturity_date,ap.product_name,at.ref_number ,am.maturity_amount from asset_transactions at inner join al_products ap on at.product_id=ap.product_id inner join clients c on c.client_id=at.client_id inner join asset_maturity am on am.asset_id=at.asset_id where c.user_id=brokerID and c.family_id=familyID and maturity_date>= curdate() union select c.name,lm.maturity_date,lp.product_name,lt.ref_number ,lm.maturity_amount from liability_transactions lt inner join al_products lp on lt.product_id=lp.product_id inner join clients c on c.client_id=lt.client_id inner join liability_maturity lm on lm.liability_id=lt.liability_id where c.user_id=brokerID and c.family_id=familyID and maturity_date>= curdate() ) as abc order by maturity_date asc limit 5)x;

select sum(installment_amount) into varUpcomingAssetsAndLia from(select * from ( select c.name,at.end_date as end_date,ap.product_name,at.ref_number,at.installment_amount
            from asset_transactions at inner join al_products ap on at.product_id=ap.product_id
            inner join clients c on c.client_id=at.client_id where c.user_id=brokerID and c.family_id=familyID and end_date>= curdate()
                union
               select c.name,lt.end_date as end_date,ap.product_name,lt.ref_number,lt.installment_amount
            from liability_transactions lt
            inner join al_products ap on lt.product_id=ap.product_id
            inner join clients c on c.client_id=lt.client_id
               where c.user_id=brokerID and c.family_id=familyID and end_date>= curdate())as abc order by end_date asc limit 5)x;
			   
select sum(total) into  varUcompingFDInterest from( SELECT c.name, fdi.interest_date, fdc.fd_comp_name, fdt.ref_number, round(fdi.interest_amount) as total FROM fd_transactions as fdt INNER JOIN fd_companies as fdc ON fdt.fd_comp_id = fdc.fd_comp_id INNER JOIN clients as c ON fdt.client_id = c.client_id INNER JOIN fd_interests as fdi ON fdi.fd_transaction_id =fdt.fd_transaction_id WHERE fdt.user_id = brokerID and fdi.interest_date >= curdate() and fdt.status = 'Active' and c.family_id = familyID ORDER BY fdi.interest_date asc LIMIT 5 )x;

select sum(total) into varUpcomingFDMat from (SELECT fd_transaction_id, fdt.client_id, c.name as client_name, fdt.family_id, fam.name as family_name, interest_mode, Date_Format(transaction_date, '%d/%m/%Y') as transaction_date, fdt.fd_inv_id, fd_inv_type, fdt.fd_comp_id, fd_comp_name, fd_method, ref_number, date_format(issued_date, '%d/%m/%Y') as issued_date, amount_invested, interest_rate, maturity_date as ogi_mat_date, Date_Format(maturity_date, '%d/%m/%Y') as maturity_date,maturity_amount as total, nom.name as nominee, nominee as nominee_id, fdt.status, adv_id, adviser_name, adjustment, maturity_payout_id, adjustment_flag, payout_mode, inv_bank_id, inv_account_number, inv_cheque_number, date_format(inv_cheque_date, '%d/%m/%Y') as inv_cheque_date, inv_amount, maturity_bank_id, maturity_account_number, adjustment_ref_number, int_round_off, fdt.broker_id FROM `fd_transactions` as `fdt` INNER JOIN `clients` as `c` ON `fdt`.`client_id` = `c`.`client_id` INNER JOIN `families` as `fam` ON `fam`.`family_id` = `c`.`family_id` left JOIN `clients` as `nom` ON `fdt`.`nominee` = `nom`.`client_id` INNER JOIN `fd_investment_types` as `fdi` ON `fdt`.`fd_inv_id` = `fdi`.`fd_inv_id` INNER JOIN `fd_companies` as `fdc` ON `fdt`.`fd_comp_id` = `fdc`.`fd_comp_id` INNER JOIN `advisers` as `adv` ON `fdt`.`adv_id` = `adv`.`adviser_id` LEFT JOIN `fd_payout_modes` as `fpm` ON `fdt`.`maturity_payout_id` = `fpm`.`payout_mode_id` WHERE `fdt`.`broker_id` = brokerID and `fdt`.`status` = 'active' and `fdt`.`maturity_date` > curdate() and `c`.`family_id` =familyID ORDER BY `fdt`.`maturity_date` ASC LIMIT 5)x;

select sum(max_amt) into varUpcomingMat from(SELECT  premium_maturities.amount as max_amt FROM insurances INNER JOIn clients ON clients.client_id = insurances.client_id INNER JOIN premium_maturities ON premium_maturities.policy_num = insurances.policy_num WHERE insurances.broker_id = brokerID and premium_maturities.maturity_date > CURRENT_DATE() and clients.family_id = familyID ORDER BY premium_maturities.maturity_date asc LIMIT 5)x;
select  sum(prem_amt) into varUpcomingPremDue from (select cli.name,insurances.next_prem_due_date,insurances.policy_num,ins_plans.plan_name,insurances.prem_amt from insurances inner join clients cli on insurances.client_id=cli.client_id inner join ins_plans on insurances.plan_id = ins_plans.plan_id where insurances.next_prem_due_date >=curdate() and cli.family_id=familyID and insurances.status in(1,2,3,4) and insurances.broker_id=brokerID order by insurances.next_prem_due_date,insurances.prem_amt limit 5)x;

select sum(max_amount) into varMFLastPurhase from( SELECT mf.amount as max_amount FROM `mutual_fund_transactions` as `mf` INNER JOIN `clients` as `c` ON `mf`.`client_id` = `c`.`client_id` INNER JOIN `families` as `fam` ON `fam`.`family_id` = `c`.`family_id` INNER JOIN `mutual_fund_schemes` as `mfs` ON `mfs`.`scheme_id` = `mf`.`mutual_fund_scheme` INNER JOIN `mf_scheme_types` as `mft` ON `mft`.`scheme_type_id` = `mfs`.`scheme_type_id` LEFT JOIN `banks` as `b` ON `mf`.`bank_id` = `b`.`bank_id` WHERE `mf`.`broker_id` = brokerID and `mf`.`mutual_fund_type` IN('PIP','NFO','IPO','TIN') and c.family_id=familyID ORDER BY `mf`.`purchase_date` DESC LIMIT 5)x;

select 
	sum(max_amt) into varMFLastRed 
from(
	SELECT  
		mf.amount as max_amt 
	FROM mutual_fund_transactions as mf 
	INNER JOIN clients as c 
		ON mf.client_id = c.client_id 
	INNER JOIN families as fam 	
		ON fam.family_id = mf.family_id 
	INNER JOIN mutual_fund_schemes as mfs 
		ON mfs.scheme_id = mf.mutual_fund_scheme 
	INNER JOIN mf_scheme_types as mft 
		ON mft.scheme_type_id = mfs.scheme_type_id 
	LEFT JOIN banks as b 
		ON mf.bank_id = b.bank_id 
	WHERE mf.broker_id = brokerID 
	and mf.mutual_fund_type in('RED','DP') 
	and c.family_id = familyID 
	
	ORDER BY mf.purchase_date DESC,c.name asc LIMIT 5)x;
	

SELECT  calculateTotalLifeCover(clientID,brokerID) into  varTotal_life_cover;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop1share,varTopQty1 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code inner join clients c on e.client_id=c.client_id where c.family_id=familyID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 0,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop2share,varTopQty2 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code inner join clients c on e.client_id=c.client_id where c.family_id=familyID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 1,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop3share,varTopQty3 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code inner join clients c on e.client_id=c.client_id where c.family_id=familyID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 2,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop4share,varTopQty4 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code inner join clients c on e.client_id=c.client_id where c.family_id=familyID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 3,1;
select s.scrip_name,sum(e.quantity*s.close_rate) into varTop5share,varTopQty5 from equities e inner join scrip_rates s on e.scrip_code=s.scrip_code inner join clients c on e.client_id=c.client_id where c.family_id=familyID  group by e.scrip_code order by sum(e.quantity*s.close_rate) DEsc limit 4,1;
select total_portfolio_HOF(familyID,brokerID) into  varTotal_portfolio;
select sum(lm.maturity_amount) into  varLiability from   liability_transactions as lt inner join clients as c on  c.client_id=lt.client_id inner join liability_maturity as lm on lm.liability_id=lt.liability_id where c.family_id=familyID and broker_id=brokerID AND (lm.maturity_date >= curdate());
select (varTotal_portfolio-(case when varLiability is null then 0 else varLiability end)) into varNetWorth;
Select sum(i.prem_paid_till_date) into varTraditionalPaid from insurances i inner join clients c   on i.client_id=c.client_id where i.plan_type_id=1 and c.family_id=familyID and i.status in(1,2,3,4);
Select sum(i.prem_paid_till_date) into varUnitLikedPaid from insurances i inner join clients c   on i.client_id=c.client_id where plan_type_id=2 and  c.family_id=familyID and i.status in(1,2,3,4);
Select sum(i.prem_paid_till_date) into varGeneralPaid from insurances  i inner join clients c   on i.client_id=c.client_id where plan_type_id=3  and c.family_id=familyID and i.status in(1,2,3,4);
select sum(p.premium_amount) into  varPrem_paid_till_date from premium_transactions p inner join clients c on c.client_id=p.client_id where  c.family_id=familyID;
select sum(f.maturity_amount+f.amount_invested) into varFdReturn from  fd_transactions f inner join clients c on  f.client_id=c.client_id where  c.family_id=familyID ;
select sum(f.amount_invested) into varFdInvested from  fd_transactions f inner join clients c on  c.client_id=f.client_id where c.family_id=familyID;
select sum(a.installment_amount) into varEMIassests from  asset_transactions a  inner join clients c on c.client_id=a.client_id where a.type_id="2" and c.family_id=familyID;
select sum(a.installment_amount) into varInstallmentAssests from  asset_transactions a  inner join clients c on c.client_id=a.client_id where a.type_id="4" and c.family_id=familyID;
select sum(a.installment_amount) into varSIPAssests from  asset_transactions a  inner join clients c on c.client_id=a.client_id where a.type_id="5" and c.family_id=familyID;
select sum(p.property_area*p.transaction_rate) into varPropertyPurchase from property_transactions  p inner join clients  c on p.client_id=c.client_id  where p.property_name NOT IN(select    property_name from property_transactions where transaction_type='sale') and c.family_id=familyID and transaction_type='purchase';
select sum(p.property_area*p.current_rate) into varPropertyCurrent from property_transactions p inner join clients  c on p.client_id=c.client_id  where p.property_name NOT IN(select property_name from property_transactions where transaction_type='sale') and c.family_id=familyID and transaction_type='purchase';

/* commented by Salmaan - 2018-08-22
select sum(mfv.live_unit* msh.current_nav) into varDebt from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
inner join clients c on mft.client_id=c.client_id
where mst.scheme_type IN("Debt","Capital Protection","FMP",'LT Debt','Liquid')
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and c.family_id =familyID;
select sum(mfv.live_unit* msh.current_nav) into varEquity from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
inner join clients c on mft.client_id=c.client_id
where mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and c.family_id =familyID;
select sum(mfv.live_unit* msh.current_nav) into varHybrid from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_schemes_histories msh on msh.scheme_id=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id
inner join clients c on mft.client_id=c.client_id
where mst.scheme_type IN("Hybrid","Balanced","MIP")
and msh.scheme_date = (select max(scheme_date) from mf_schemes_histories where scheme_id=mfs.scheme_id)
and c.family_id =familyID;
below new queries added - Salmaan - 2018-08-22 */

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varDebt from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id 
inner join clients c on mft.client_id=c.client_id 
where mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")
and c.family_id = familyID;

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varEquity from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id 
inner join clients c on mft.client_id=c.client_id 
where mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
and c.family_id = familyID;

select sum(mfv.live_unit * (IFNULL(mfv.c_nav, 0))) into varHybrid from
mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mft.transaction_id=mfv.transaction_id
inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme=mfs.scheme_id
inner join mf_scheme_types mst on mst.scheme_type_id=mfs.scheme_type_id 
inner join clients c on mft.client_id=c.client_id 
where mst.scheme_type IN("Hybrid","Balanced","MIP")
and c.family_id = familyID;
select sum(mfv.div_payout) into div_payout_total_amount from mutual_fund_valuation mfv inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id inner join clients c on mft.client_id = c.client_id inner join families f on f.family_id=c.family_id inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id where mfv.broker_id = brokerID and f.family_id =familyID  and c.status=1 and round((mfv.c_nav * mfv.live_unit)) > 3 and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund","Hybrid","Balanced","MIP","Debt","Capital Protection","FMP");

select  varDebitBal,varCurrent_Amount,varPurchase_Amount,varInsuranceTotal,varMFTotal,varRETotal,varCommodityTotal, varUpcomingAssetsAndLiaDue,varUpcomingAssetsAndLia, varUcompingFDInterest,varUpcomingFDMat,varUpcomingMat,varUpcomingPremDue,varMFLastRed,varMFLastPurhase,varTopQty1,varTopQty2,varTopQty3,varTopQty4,varTopQty5,varTop1share,varTop2share,varTop3share,varTop4share,varTop5share,varTotalEquityPortfolio,varEMIassests,varInstallmentAssests,varSIPAssests,varPrem_paid_till_date,varFDTotal,varTotal_life_cover,varLiability,varTotal_portfolio,varNetWorth,varTraditionalPaid ,varUnitLikedPaid,varGeneralPaid,varFdReturn,varFdInvested,varPropertyPurchase,varPropertyCurrent,varDebt,varEquity,varHybrid,div_payout_total_amount;


end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_interest_calender_client` (IN `month` VARCHAR(3), IN `start_date` DATE, IN `clientID` VARCHAR(30))  NO SQL BEGIN
if(month = 'Jan')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec` 
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Feb')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Mar')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Apr')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'May')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Jun')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Jul')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Aug')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Sep')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Oct')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Nov')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
elseif(month = 'Dec')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number, 
fd.amount_invested,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov
FROM `fd_transactions` fd 
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and 
fd.client_id = clientID 
group by fi.fd_transaction_id order by name);
end if;
select * from `interest_calendar`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_interest_calender_family` (IN `month` VARCHAR(3), IN `start_date` DATE, IN `familyID` VARCHAR(30))  NO SQL BEGIN
if(month = 'Jan')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Feb')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Mar')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Apr')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'May')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Jun')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Jul')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Aug')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Sep')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Oct')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Nov')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
elseif(month = 'Dec')
then
create TEMPORARY TABLE IF NOT EXISTS `interest_calendar` AS
(select c.name as client_name, f.name as family_name, fdc.fd_comp_name, fd.maturity_account_number, fd.ref_number,
fd.amount_invested,
sum(if(month(fi.interest_date) = 12, interest_amount, 0)) AS `Dec`,
sum(if(month(fi.interest_date) = 1, interest_amount, 0))  AS Jan,
sum(if(month(fi.interest_date) = 2, interest_amount, 0))  AS Feb,
sum(if(month(fi.interest_date) = 3, interest_amount, 0))  AS Mar,
sum(if(month(fi.interest_date) = 4, interest_amount, 0))  AS Apr,
sum(if(month(fi.interest_date) = 5, interest_amount, 0))  AS May,
sum(if(month(fi.interest_date) = 6, interest_amount, 0))  AS Jun,
sum(if(month(fi.interest_date) = 7, interest_amount, 0))  AS Jul,
sum(if(month(fi.interest_date) = 8, interest_amount, 0))  AS Aug,
sum(if(month(fi.interest_date) = 9, interest_amount, 0))  AS Sep,
sum(if(month(fi.interest_date) = 10, interest_amount, 0)) AS Oct,
sum(if(month(fi.interest_date) = 11, interest_amount, 0)) AS Nov
FROM `fd_transactions` fd
inner join fd_interests fi on fd.fd_transaction_id = fi.fd_transaction_id
inner join fd_companies fdc on fd.fd_comp_id = fdc.fd_comp_id
inner join clients c on c.client_id = fd.client_id
inner join families f on f.family_id = c.family_id
where fi.interest_date >= start_date and
fi.interest_date < Date_Add(start_date, interval 12 month) and
c.family_id = familyID
group by fi.fd_transaction_id order by report_order, c.name);
end if;
select * from `interest_calendar`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_lapse_client` (IN `clientID` VARCHAR(30))  NO SQL SELECT c.name, ipm.plan_name, icm.ins_comp_name, im.policy_num, im.commence_date, im.next_prem_due_date, 
findLastPremiumPaidForLapseReport(im.policy_num) AS last_premium_paid, im.nominee, im.prem_amt, im.amt_insured, 
pmm.mode_name, im.prem_paid_till_date, psm.status, im.adjustment, im.fund_value FROM insurances AS im 
INNER JOIN clients AS c ON c.client_id = im.client_id INNER JOIN ins_plans AS ipm ON ipm.plan_id = im.plan_id
INNER JOIN ins_companies AS icm ON ipm.ins_comp_id = icm.ins_comp_id 
INNER JOIN premium_modes AS pmm ON pmm.mode_id = im.mode 
INNER JOIN premium_status AS psm ON psm.status_id = im.status WHERE (im.client_id = clientID) AND 
(im.status IN (SELECT status_id FROM premium_status WHERE (status = 'Lapsed')))$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_lapse_family` (IN `familyID` VARCHAR(30))  NO SQL SELECT c.name, f.name as fam_name, ipm.plan_name, icm.ins_comp_name, im.policy_num, im.commence_date, im.next_prem_due_date, 
findLastPremiumPaidForLapseReport(im.policy_num) AS last_premium_paid, im.nominee, im.prem_amt, im.amt_insured, 
pmm.mode_name, im.prem_paid_till_date, psm.status, im.adjustment, im.fund_value FROM insurances AS im 
INNER JOIN clients AS c ON c.client_id = im.client_id 
INNER JOIN families AS f ON f.family_id = c.family_id 
INNER JOIN ins_plans AS ipm ON ipm.plan_id = im.plan_id
INNER JOIN ins_companies AS icm ON ipm.ins_comp_id = icm.ins_comp_id 
INNER JOIN premium_modes AS pmm ON pmm.mode_id = im.mode 
INNER JOIN premium_status AS psm ON psm.status_id = im.status WHERE (im.client_id in (select client_id from clients where family_id = familyID)) AND 
(im.status IN (SELECT status_id FROM premium_status WHERE (status = 'Lapsed')))$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_dividend_client` (IN `clientID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
SELECT 'Dividend Reinvested' AS `Particular`, ROUND(SUM(mft.quantity * mft.nav),2) AS `amount` ,
c.name as clientName,
    'Mutual Fund' as ProductName 
FROM mutual_fund_transactions mft
INNER JOIN clients AS c ON mft.client_id = c.client_id 
WHERE (mft.mutual_fund_type = 'DIV') AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id = clientID) AND (mft.broker_id = brokerID) AND `amount` != 0; 
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_dividend_family` (IN `familyID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
SELECT 'Dividend Reinvested' AS `Particular`, ROUND(SUM(mft.quantity * mft.nav),2) AS `amount` ,
c.name as clientName,
            'Mutual Fund' as ProductName  
FROM mutual_fund_transactions mft
INNER JOIN clients AS c ON mft.client_id = c.client_id 
WHERE (mft.mutual_fund_type = 'DIV') AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id IN (select client_id from clients where family_id = familyID)) AND (mft.broker_id = brokerID) AND `amount` != 0; 
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_inflow_client` (IN `clientID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
    SELECT comp_date,Particular,amount, ClientName,ProductName FROM (
        SELECT pt.cheque_date AS `comp_date`, 
            c.name as clientName,
            'Insurance' as ProductName,
            CONCAT('Premium paid for Policy No.: ', i.policy_num, ', ', ic.ins_comp_name, '  ', ip.plan_name, 
                               CASE WHEN b.bank_name IS NULL THEN '' ELSE CONCAT('(',b.bank_name) END, 
                              CASE WHEN pt.account_number IS NULL THEN '' ELSE CONCAT(', A/C No:',pt.account_number) END, 
                              CASE WHEN pt.cheque_number IS NULL THEN '' ELSE CONCAT(', Cheque No: ',pt.cheque_number,')') END 
                              ) AS `Particular`, ROUND(pt.premium_amount,2) AS `amount`,2 as type 
        FROM premium_transactions AS pt 
        LEFT JOIN banks AS b ON b.bank_id = pt.bank_id 
        INNER JOIN insurances AS i ON pt.policy_number = i.policy_num 
        INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
        INNER JOIN ins_companies AS ic ON ic.ins_comp_id = i.ins_comp_id 
        INNER JOIN clients AS c ON i.client_id = c.client_id 
        WHERE (pt.cheque_date BETWEEN fromDate AND toDate) AND (c.client_id = clientID) AND (pt.broker_id = brokerID) AND (pt.premium_amount <> 0)
UNION

SELECT af.transaction_date AS `comp_date`, 
        c.name as clientName,
            'Other Investment' as ProductName,
CONCAT('Others Investment - ', CASE WHEN af.add_notes IS NULL THEN '' ELSE af.add_notes END, 
                                   ' (', CASE WHEN af.bank_account_id IS NULL THEN '' ELSE b0.bank_name END, 
                                   CASE WHEN af.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No:',ba.account_number) END, 
                                   CASE WHEN af.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',af.cheque_no) END, 
                                   ') ') AS `Particular`, ROUND(af.amount,2) AS `amount` ,3 as type
FROM add_funds AS af 
INNER JOIN clients AS c ON af.client_id = c.client_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = af.bank_account_id 
LEFT JOIN banks AS b0 ON b0.bank_id = ba.bank_id 
WHERE (af.transaction_date BETWEEN fromDate AND toDate) AND (af.client_id = clientID) AND (af.broker_id = brokerID) AND (shares_app <> '1') 
UNION 

SELECT af1.transaction_date AS `comp_date`,
    c.name as clientName,
            'Equity' as ProductName,
 CONCAT('Equity Investment in ', CASE WHEN af1.trading_broker_id IS NULL THEN '' ELSE tb1.trading_broker_name END, 
                                   CASE WHEN af1.client_code IS NULL THEN '' ELSE CONCAT(', Client Code: ', af1.client_code) END, 
                                   ', (', CASE WHEN b1.bank_name IS NULL THEN '' ELSE b1.bank_name END, 
                                   CASE WHEN ba1.account_number IS NULL THEN '' ELSE CONCAT(', A/C No:',ba1.account_number) END, 
                                   CASE WHEN af1.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ', af1.cheque_no) END, 
                                   ')') AS `Particular`, ROUND(af1.amount,2) AS `amount` ,3 as type
FROM add_funds AS af1 
INNER JOIN clients AS c ON af1.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb1 ON tb1.trading_broker_id = af1.trading_broker_id 
LEFT JOIN bank_accounts AS ba1 ON ba1.account_id = af1.bank_account_id 
LEFT JOIN banks AS b1 ON b1.bank_id = ba1.bank_id 
WHERE (af1.transaction_date BETWEEN fromDate AND toDate) AND (af1.client_id = clientID) AND (af1.broker_id = brokerID) AND (af1.shares_app = '1') 
      AND (af1.add_notes IS NULL OR af1.add_notes = '') 
UNION 

SELECT af2.transaction_date AS `comp_date`, 
    c.name as clientName,
    'Equity' as ProductName,
    CONCAT('Equity Investment in ', CASE WHEN af2.trading_broker_id IS NULL THEN '' ELSE tb2.trading_broker_name END, 
                                   CASE WHEN af2.client_code IS NULL THEN '' ELSE CONCAT(', Client Code: ', af2.client_code) END, 
                                    ', (', CASE WHEN (af2.add_notes IS NULL OR af2.add_notes = '') THEN '' ELSE af2.add_notes END, 
                                    ')') AS `Particular`, ROUND(af2.amount,2) AS `amount`,3 as type 
FROM add_funds AS af2 
INNER JOIN clients AS c ON af2.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb2 ON tb2.trading_broker_id = af2.trading_broker_id 
WHERE (af2.transaction_date BETWEEN fromDate AND toDate) AND (af2.client_id = clientID) AND (af2.broker_id = brokerID) AND (af2.shares_app = '1') 
      AND (af2.add_notes IS NOT NULL OR af2.add_notes <> '') 
UNION 

SELECT ct.transaction_date AS `comp_date`, 
  c.name as clientName,
    'Commodity' as ProductName,
CONCAT('Commodity Bought ', ct.quantity, ' ', cu.unit_name, ' of ', ci.item_name, ' @ Rs.', ct.transaction_rate) 
AS `Particular`, ROUND(ct.total_amount,2) AS `amount`,4 as type 
FROM commodity_transactions AS ct 
INNER JOIN clients AS c ON ct.client_id = c.client_id 
INNER JOIN commodity_units AS cu ON cu.unit_id = ct.commodity_unit_id 
INNER JOIN commodity_items AS ci ON ci.item_id = ct.commodity_item_id 
WHERE (ct.transaction_type = 'Purchase') AND (ct.transaction_date BETWEEN fromDate AND toDate) AND 
(ct.client_id = clientID) AND (ct.broker_id = brokerID) 
	  AND (adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 

SELECT ppt.transaction_date AS `comp_date`, 
 c.name as clientName,
    'Real Estate' as ProductName,
CONCAT('Real Estate Investments for ', ppt.property_name) AS 'Particular', ROUND(ppt.amount,2) AS `amount` ,5 as type
FROM property_transactions ppt 
INNER JOIN clients AS c ON ppt.client_id = c.client_id 
WHERE (ppt.transaction_type = 'Purchase') AND (ppt.transaction_date BETWEEN fromDate AND toDate) 
AND (ppt.client_id = clientID) AND (ppt.broker_id = brokerID) 
      AND (ppt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT fdt.issued_date AS `comp_date`, 
c.name as clientName,
    'Fixed Deposite' as ProductName,
CONCAT('Fixed Income investment in ', fdit.fd_inv_type, ' of ', fdc.fd_comp_name, ', Ref No.: ', fdt.ref_number, 
                               ', (', CASE WHEN fdt.inv_bank_id IS NULL THEN '' ELSE b2.bank_name END, 
                               CASE WHEN fdt.inv_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.:',fdt.inv_account_number) END, 
                               ', Cheque No.: ', fdt.inv_cheque_number, ')') AS 'Particulars', ROUND(fdt.amount_invested,2) AS `amount` ,6 as type
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b2 ON b2.bank_id = fdt.inv_bank_id 
WHERE (fdt.issued_date BETWEEN fromDate AND toDate) AND (fdt.client_id = clientID) AND (fdt.broker_id = brokerID) 
	  AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) 
UNION 

SELECT mft.purchase_date AS `comp_date`, 
c.name as clientName,
    'Mutual Fund' as ProductName,
CONCAT('Mutual Fund Investment of ', mfs.scheme_name, ', Folio No.:', mft.folio_number, CASE WHEN mft.bank_name IS NULL OR mft.bank_name = '' THEN CASE WHEN cb.bank_name IS NULL THEN '' ELSE CONCAT(' (',cb.bank_name) END ELSE CONCAT(' (',mft.bank_name) END, 
                                   CASE WHEN mft.account_number IS NULL OR mft.account_number = '' THEN CASE WHEN cb.bank_acc_no IS NULL THEN '' ELSE CONCAT(', A/C No: ',cb.bank_acc_no) END ELSE CONCAT(', A/C No: ',mft.account_number) END,  CASE WHEN mft.cheque_number IS NULL THEN ')' ELSE CONCAT(', Cheque No:',mft.cheque_number,')') END) AS `Particular`, 
	   ROUND((mft.quantity * mft.nav),2) AS `amount` ,1 as type
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
LEFT JOIN client_bank_details cb ON (cb.client_id = mft.client_id AND cb.folio_number = mft.folio_number AND cb.productId = mfs.prod_code) 
WHERE (mft.mutual_fund_type IN ('PIP', 'IPO')) AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id = clientID) AND (mft.broker_id = brokerID) 
UNION 
SELECT mft1.purchase_date AS `comp_date`, 
c.name as clientName,
    'Mutual Fund' as ProductName,
CONCAT('Portfolio Investment in Mutual Fund of ', mfs1.scheme_name, ', Folio No.:', mft1.folio_number) AS `Particular`, 
	   ROUND((mft1.quantity * mft1.nav),2) AS `amount` ,1 as type
FROM mutual_fund_transactions AS mft1 
INNER JOIN clients AS c ON mft1.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs1 ON mfs1.scheme_id = mft1.mutual_fund_scheme 
WHERE (mft1.mutual_fund_type IN ('TIN')) AND (mft1.purchase_date BETWEEN fromDate AND toDate) AND (mft1.client_id = clientID) AND (mft1.broker_id = brokerID)
    ) AS tbl WHERE tbl.amount != 0 and (case when InvestmentType=0 then 1 when InvestmentType!=0 and tbl.type=InvestmentType then 1 
                                        end)=1 ORDER BY `comp_date` ASC; 
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_inflow_family` (IN `familyID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
SELECT comp_date,Particular,amount, ClientName,ProductName FROM (
SELECT pt.cheque_date AS `comp_date`, 
c.Name as ClientName,
'Insurances' as ProductName,
CONCAT('Premium paid for Policy No.: ', i.policy_num, ', ', ic.ins_comp_name, '  ', ip.plan_name, 
                              CASE WHEN b.bank_name IS NULL THEN '' ELSE CONCAT(' (',b.bank_name) END, 
                              CASE WHEN pt.account_number IS NULL THEN '' ELSE CONCAT(', A/C No:',pt.account_number) END, 
                              CASE WHEN pt.cheque_number IS NULL THEN '' ELSE CONCAT(', Cheque No: ',pt.cheque_number,')') END 
                              ) AS `Particular`, ROUND(pt.premium_amount,2) AS `amount` ,2 as InvestmentType 
FROM premium_transactions AS pt 
LEFT JOIN banks AS b ON b.bank_id = pt.bank_id 
INNER JOIN insurances AS i ON pt.policy_number = i.policy_num 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN ins_companies AS ic ON ic.ins_comp_id = i.ins_comp_id 
INNER JOIN clients AS c ON i.client_id = c.client_id 
WHERE (pt.cheque_date BETWEEN fromDate AND toDate) AND (c.client_id IN (select client_id from clients where family_id = familyID)) AND (pt.broker_id = brokerID) AND (pt.premium_amount <> 0)
UNION
SELECT af.transaction_date AS `comp_date`, 
c.Name as ClientName,
'Others Investment' as ProductName,
CONCAT('Others Investment - ',  CASE WHEN af.add_notes IS NULL THEN '' ELSE af.add_notes END, 
                                   ' (', CASE WHEN af.bank_account_id IS NULL THEN '' ELSE b0.bank_name END, 
                                   CASE WHEN af.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No:',ba.account_number) END, 
                                   CASE WHEN af.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',af.cheque_no) END, 
                                   ') ') AS `Particular`, ROUND(af.amount,2) AS `amount` ,3 as InvestmentType
FROM add_funds AS af 
INNER JOIN clients AS c ON af.client_id = c.client_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = af.bank_account_id 
LEFT JOIN banks AS b0 ON b0.bank_id = ba.bank_id 
WHERE (af.transaction_date BETWEEN fromDate AND toDate) AND (af.client_id IN (select client_id from clients where family_id = familyID)) AND (af.broker_id = brokerID) AND (shares_app <> '1') 
UNION 
SELECT af1.transaction_date AS `comp_date`, 
c.Name as ClientName,
'Equity' as ProductName,
CONCAT('Equity Investment in ', CASE WHEN af1.trading_broker_id IS NULL THEN '' ELSE tb1.trading_broker_name END, 
                                   CASE WHEN af1.client_code IS NULL THEN '' ELSE CONCAT(', Client Code: ', af1.client_code) END, 
                                   ', (', CASE WHEN b1.bank_name IS NULL THEN '' ELSE b1.bank_name END, 
                                   CASE WHEN ba1.account_number IS NULL THEN '' ELSE CONCAT(', A/C No:',ba1.account_number) END, 
                                   CASE WHEN af1.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ', af1.cheque_no) END, 
                                   ')') AS `Particular`, ROUND(af1.amount,2) AS `amount` ,3 as InvestmentType
FROM add_funds AS af1 
INNER JOIN clients AS c ON af1.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb1 ON tb1.trading_broker_id = af1.trading_broker_id 
LEFT JOIN bank_accounts AS ba1 ON ba1.account_id = af1.bank_account_id 
LEFT JOIN banks AS b1 ON b1.bank_id = ba1.bank_id 
WHERE (af1.transaction_date BETWEEN fromDate AND toDate) AND (af1.client_id IN (select client_id from clients where family_id = familyID)) AND (af1.broker_id = brokerID) AND (af1.shares_app = '1') 
      AND (af1.add_notes IS NULL OR af1.add_notes = '') 
UNION 
SELECT af2.transaction_date AS `comp_date`, 
c.Name as ClientName,
'Equity' as ProductName,
CONCAT('Equity Investment in ', CASE WHEN af2.trading_broker_id IS NULL THEN '' ELSE tb2.trading_broker_name END, 
                                   CASE WHEN af2.client_code IS NULL THEN '' ELSE CONCAT(', Client Code: ', af2.client_code) END, 
                                    ', (', CASE WHEN (af2.add_notes IS NULL OR af2.add_notes = '') THEN '' ELSE af2.add_notes END, 
                                    ')') AS `Particular`, ROUND(af2.amount,2) AS `amount` ,3 as InvestmentType 
FROM add_funds AS af2 
INNER JOIN clients AS c ON af2.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb2 ON tb2.trading_broker_id = af2.trading_broker_id 
WHERE (af2.transaction_date BETWEEN fromDate AND toDate) AND (af2.client_id IN (select client_id from clients where family_id = familyID)) AND (af2.broker_id = brokerID) AND (af2.shares_app = '1') 
      AND (af2.add_notes IS NOT NULL OR af2.add_notes <> '') 
UNION 
SELECT ct.transaction_date AS `comp_date`, 
c.Name as ClientName,
'Commodity' as ProductName,
CONCAT('Commodity Bought ', ct.quantity, ' ', cu.unit_name, ' of ', ci.item_name, ' @ Rs.', ct.transaction_rate) 
AS `Particular`, ROUND(ct.total_amount,2) AS `amount`  ,4 as InvestmentType
FROM commodity_transactions AS ct 
INNER JOIN clients AS c ON ct.client_id = c.client_id 
INNER JOIN commodity_units AS cu ON cu.unit_id = ct.commodity_unit_id 
INNER JOIN commodity_items AS ci ON ci.item_id = ct.commodity_item_id 
WHERE (ct.transaction_type = 'Purchase') AND (ct.transaction_date BETWEEN fromDate AND toDate) AND (ct.client_id IN (select client_id from clients where family_id = familyID)) AND (ct.broker_id = brokerID) 
	  AND (adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT ppt.transaction_date AS `comp_date`,
c.Name as ClientName,
'Real Estate' as ProductName,
CONCAT('Real Estate Investments for ', ppt.property_name) AS 'Particular', ROUND(ppt.amount,2) AS `amount` ,5 as InvestmentType
FROM property_transactions ppt 
INNER JOIN clients AS c ON ppt.client_id = c.client_id 
WHERE (ppt.transaction_type = 'Purchase') AND (ppt.transaction_date BETWEEN fromDate AND toDate) AND (ppt.client_id IN (select client_id from clients where family_id = familyID)) AND (ppt.broker_id = brokerID) 
      AND (ppt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT fdt.issued_date AS `comp_date`, 
c.Name as ClientName,
'Fixed Deposite' as ProductName,
CONCAT('Fixed Income investment in ', fdit.fd_inv_type, ' of ', fdc.fd_comp_name, ', Ref No.: ', fdt.ref_number, 
                               ', (', CASE WHEN fdt.inv_bank_id IS NULL THEN '' ELSE b2.bank_name END, 
                               CASE WHEN fdt.inv_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.:',fdt.inv_account_number) END, 
                               ', Cheque No.: ', fdt.inv_cheque_number, ')') AS 'Particulars', ROUND(fdt.amount_invested,2) AS `amount`  ,6 as InvestmentType
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b2 ON b2.bank_id = fdt.inv_bank_id 
WHERE (fdt.issued_date BETWEEN fromDate AND toDate) AND (fdt.client_id IN (select client_id from clients where family_id = familyID)) AND (fdt.broker_id = brokerID) 
	  AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT mft.purchase_date AS `comp_date`, 
c.Name as ClientName,
'Mutual Fund' as ProductName,
CONCAT('Mutual Fund Investment of ', mfs.scheme_name, ', Folio No.:', mft.folio_number, CASE WHEN (mft.bank_name IS NULL OR mft.bank_name = '') THEN CASE WHEN cb.bank_name IS NULL THEN '' ELSE CONCAT(' (',cb.bank_name) END ELSE CONCAT(' (',mft.bank_name) END, 
                                   CASE WHEN (mft.account_number IS NULL OR mft.account_number = '') THEN CASE WHEN cb.bank_acc_no IS NULL THEN '' ELSE CONCAT(', A/C No: ',cb.bank_acc_no) END ELSE CONCAT(', A/C No: ',mft.account_number) END,  CASE WHEN mft.cheque_number IS NULL THEN ')' ELSE CONCAT(', Cheque No:',mft.cheque_number,')') END) AS `Particular`, 
	   ROUND((mft.quantity * mft.nav),2) AS `amount` ,1 as InvestmentType
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
LEFT JOIN client_bank_details cb ON (cb.client_id = mft.client_id AND cb.folio_number = mft.folio_number AND cb.productId = mfs.prod_code) 
WHERE (mft.mutual_fund_type IN ('PIP', 'IPO')) AND (mft.purchase_date BETWEEN fromDate AND toDate) AND (mft.client_id IN (select client_id from clients where family_id = familyID)) AND (mft.broker_id = brokerID) 
UNION 
SELECT mft1.purchase_date AS `comp_date`, 
c.Name as ClientName,
'Mutual Fund' as ProductName,
CONCAT('Portfolio Investment in Mutual Fund of ', mfs1.scheme_name, ', Folio No.:', mft1.folio_number) AS `Particular`, 
	   ROUND((mft1.quantity * mft1.nav),2) AS `amount`  ,1 as InvestmentType
FROM mutual_fund_transactions AS mft1 
INNER JOIN clients AS c ON mft1.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs1 ON mfs1.scheme_id = mft1.mutual_fund_scheme 
WHERE (mft1.mutual_fund_type IN ('TIN')) AND (mft1.purchase_date BETWEEN fromDate AND toDate) AND (mft1.client_id IN (select client_id from clients where family_id = familyID)) AND (mft1.broker_id = brokerID) 
    ) AS tbl WHERE tbl.amount != 0  and (case when InvestmentType=0 then 1 when InvestmentType!=0 and tbl.InvestmentType=InvestmentType then 1 end)=1 ORDER BY `comp_date` ASC; 
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_outflow_client` (IN `clientID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
SELECT comp_date,Particular,amount,clientName,ProductName FROM (
SELECT 
	MAX(pm.maturity_date) AS `comp_date`, 
	CONCAT('Maturity proceeds for Policy No: ', 
	i.policy_num,', ', ip.plan_name) AS `Particular`, 
	ROUND(SUM(pm.amount),2) AS `amount`,
	2 as 'InvestmentType' ,
    c.name as clientName,
    'Insurances' as ProductName
FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id = clientID) AND (i.broker_id = brokerID) 
AND (i.mat_type = 'Single') AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
AND (i.status NOT IN (SELECT status_id FROM premium_status WHERE status IN ('Lapsed', 'Surrender')))
GROUP BY i.policy_num, ip.plan_name, a.adviser_name 
UNION 

SELECT pm.maturity_date AS `comp_date`, 
CONCAT('Insurance payout for Policy No: ', i.policy_num, ', ', ip.plan_name) AS `Particular`, 
ROUND(pm.amount,2) AS `amount` ,2 as InvestmentType ,
c.name as clientName,
    'Insurances' as ProductName
FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id = clientID) AND (i.broker_id = brokerID) 
AND (i.mat_type = 'Regular') AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
AND (i.status NOT IN (SELECT status_id FROM premium_status WHERE status IN ('Lapsed', 'Surrender'))) 
GROUP BY i.policy_num, ip.plan_name, a.adviser_name, pm.maturity_date, pm.amount 
UNION

SELECT MAX(pm.maturity_date) AS `comp_date`, 
CONCAT('Surrender proceeds for Policy No: ', i.policy_num, ', ', ip.plan_name) AS `Particular`, 
ROUND(pm.amount,2) AS `amount` ,2 as InvestmentType ,
c.name as clientName,
    'Insurances' as ProductName
FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id = clientID) AND (i.broker_id = brokerID) 
AND (i.status IN (SELECT status_id FROM premium_status WHERE status = 'Surrender')) 
AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
GROUP BY i.policy_num, ip.plan_name, a.adviser_name, pm.amount 
UNION 

SELECT wf.transaction_date AS `comp_date`,
 CONCAT('Withdrawal Proceeds', CASE WHEN wf.withdraw_from IS NOT NULL THEN CONCAT(' from ',wf.withdraw_from) ELSE '' END, ' - ', CASE WHEN add_notes IS NOT NULL THEN add_notes ELSE '' END, 
                                                  ' (', CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE b.bank_name END, 
                                                  CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No.: ',ba.account_number) END, 
                                                  CASE WHEN wf.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',wf.cheque_no) END, 
                                                  ')') AS `Particular`,
ROUND(wf.amount,2) AS `amount`,3 as InvestmentType ,
c.name as clientName,
            'Equity' as ProductName
FROM withdraw_funds AS wf 
INNER JOIN clients AS c ON wf.client_id = c.client_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = wf.bank_account_id 
LEFT JOIN banks AS b ON b.bank_id = ba.bank_id 
WHERE (wf.transaction_date BETWEEN fromDate AND toDate) AND (wf.client_id = clientID) AND (wf.broker_id = brokerID) AND (wf.withdraw_from <> 'Equity') 
UNION 

SELECT wf.transaction_date AS `comp_date`, CONCAT('Payout from ', tb.trading_broker_name, 
                                                  ', Client Code: ', wf.client_code, 
                                                  ' (', CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE b.bank_name END, 
                                                  CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No.: ',ba.account_number) END, 
                                                  CASE WHEN wf.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',wf.cheque_no) END, 
                                                  ')') AS `Particular`, 
ROUND(wf.amount,2) AS `amount` ,3 as InvestmentType,
c.name as clientName,
            'Equity' as ProductName
FROM withdraw_funds AS wf 
INNER JOIN clients AS c ON wf.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb ON tb.trading_broker_id = wf.trading_broker_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = wf.bank_account_id 
LEFT JOIN banks AS b ON b.bank_id = ba.bank_id 
WHERE (wf.transaction_date BETWEEN fromDate AND toDate) AND (wf.client_id = clientID) AND (wf.broker_id = brokerID) AND (wf.withdraw_from = 'Equity') 
GROUP BY tb.trading_broker_name, wf.client_code, wf.transaction_date, b.bank_name, wf.cheque_no, ba.account_number 
UNION

SELECT fdi.interest_date AS `comp_date`, CONCAT(fdt.interest_mode, ' interest for ', fdit.fd_inv_type, 
                                                ' of ', fdc.fd_comp_name, ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(fdi.interest_amount,2) AS `amount` 
                                                ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                

FROM fd_interests AS fdi 
INNER JOIN fd_transactions AS fdt ON fdi.fd_transaction_id = fdt.fd_transaction_id 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
INNER JOIN advisers AS a ON a.adviser_id = fdt.adv_id 
WHERE (fdi.interest_date BETWEEN fromDate AND toDate) AND (fdt.client_id = clientID) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) 
UNION 

SELECT fdt.maturity_date AS `comp_date`, CONCAT('Maturity proceeds of ', fdit.fd_inv_type, ' in ', fdc.fd_comp_name, 
                                                ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(SUM(fdt.maturity_amount),2) AS `amount` 
                                                ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                   
                                           
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
WHERE (fdt.maturity_date BETWEEN fromDate AND toDate) AND (fdt.client_id = clientID) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) AND (fdt.status <> 'PreMature') 
GROUP BY fdit.fd_inv_type, fdc.fd_comp_name, fdt.maturity_date, fdt.ref_number, b.bank_name, fdt.maturity_account_number 
UNION 

SELECT fdt.maturity_date AS `comp_date`, CONCAT('Premature withdrawal of ', fdit.fd_inv_type, ' in ', fdc.fd_comp_name, 
                                                ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(SUM(fdt.maturity_amount),2) AS `amount` 
                                                ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                  
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
INNER JOIN advisers AS a ON a.adviser_id = fdt.adv_id 
WHERE (fdt.maturity_date BETWEEN fromDate AND toDate) AND (fdt.client_id = clientID) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) AND (fdt.status = 'PreMature') 
GROUP BY fdit.fd_inv_type, fdc.fd_comp_name, fdt.maturity_date, fdt.ref_number, b.bank_name, fdt.maturity_account_number 
UNION 

SELECT prd.rent_date AS `comp_date`, 
CONCAT('Rent received for ', pt.property_name) AS `Particular`, ROUND(prd.amount,2) AS `amount` ,
5 as InvestmentType,
c.name as clientName,
            'Real Estate' as ProductName  
FROM property_rent_details AS prd 
INNER JOIN property_transactions AS pt ON prd.pro_transaction_id = pt.pro_transaction_id 
INNER JOIN clients AS c ON pt.client_id = c.client_id 
INNER JOIN advisers AS a ON pt.adviser_id = a.adviser_id 
WHERE (pt.transaction_type = 'Purchase') AND (prd.rent_date BETWEEN fromDate AND toDate) AND (pt.client_id = clientID) AND (pt.broker_id = brokerID) 
AND (pt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT pt.transaction_date AS `comp_date`, 
CONCAT('Sale proceeds of ', pt.property_name) AS `Particular`, ROUND(pt.amount,2) AS `amount` ,
5 as InvestmentType,
c.name as clientName,
            'Real Estate' as ProductName  
FROM property_transactions AS pt 
INNER JOIN clients AS c ON pt.client_id = c.client_id 
INNER JOIN advisers AS a ON pt.adviser_id = a.adviser_id 
WHERE (pt.transaction_type = 'Sale') AND (pt.transaction_date BETWEEN fromDate AND toDate) AND (pt.client_id = clientID) AND (pt.broker_id = brokerID) 
AND (pt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 

SELECT ct.transaction_date AS `comp_date`,
 CONCAT('Sale proceeds of ', ci.item_name, ' ', ct.quantity, ' ', cu.unit_name, 
                                                  ' @ Rs.', ct.transaction_rate) AS `Particular`, 
                                                  ct.total_amount AS `amount` ,4 as InvestmentType ,
c.name as clientName,
            'Commodity' as ProductName                                                    
FROM commodity_transactions AS ct 
INNER JOIN clients AS c ON ct.client_id = c.client_id 
INNER JOIN commodity_units AS cu ON cu.unit_id = ct.commodity_unit_id 
INNER JOIN commodity_items AS ci ON ci.item_id = ct.commodity_item_id 
INNER JOIN advisers AS a ON ct.adviser_id = a.adviser_id 
WHERE (ct.transaction_type = 'Sale') AND (ct.transaction_date BETWEEN fromDate AND toDate) AND (ct.client_id = clientID) AND (ct.broker_id = brokerID) 
AND (ct.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 

SELECT mft.purchase_date AS `comp_date`, CONCAT('Redemption proceeds of ', mfs.scheme_name, ', Folio No: ', mft.folio_number, CASE WHEN mft.bank_name IS NULL OR mft.bank_name = '' THEN CASE WHEN cb.bank_name IS NULL THEN '' ELSE CONCAT(' (',cb.bank_name) END ELSE CONCAT(' (',mft.bank_name) END,
                                   CASE WHEN mft.account_number IS NULL OR mft.account_number = '' THEN CASE WHEN cb.bank_acc_no IS NULL THEN '' ELSE CONCAT(', A/C No: ',cb.bank_acc_no) END ELSE CONCAT(', A/C No: ',mft.account_number) END,  CASE WHEN mft.cheque_number IS NULL OR mft.cheque_number = '' THEN ')' ELSE CONCAT(', Cheque No: ',mft.cheque_number,')') END) AS `Particular`, 
		(mft.quantity * mft.nav) AS `amount` ,1 as InvestmentType ,
        c.name as clientName,
            'Mutual Fund' as ProductName      
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
LEFT JOIN client_bank_details cb ON (cb.client_id = mft.client_id AND cb.folio_number = mft.folio_number AND cb.productId = mfs.prod_code) 
WHERE (mft.mutual_fund_type IN ('RED')) AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id = clientID) AND (mft.broker_id = brokerID) 
UNION 

SELECT mft.purchase_date AS `comp_date`, CONCAT('Dividend payout of ', mfs.scheme_name, ', Folio No: ', mft.folio_number) AS `Particular`, ROUND(mft.amount,2) AS `amount`  ,1 as InvestmentType ,
c.name as clientName,
            'Mutual Fund' as ProductName 
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
WHERE (mft.mutual_fund_type IN ('DP')) AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id = clientID) AND (mft.broker_id = brokerID) 
    ) AS tbl WHERE tbl.amount != 0 and (case when InvestmentType=0 then 1 when InvestmentType!=0 and tbl.InvestmentType=InvestmentType then 1 end)=1  ORDER BY `comp_date` ASC;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_ledger_report_outflow_family` (IN `familyID` VARCHAR(100), IN `fromDate` DATE, IN `toDate` DATE, IN `brokerID` VARCHAR(10), IN `InvestmentType` INT)   begin 
SELECT comp_date,Particular,amount,ProductName,clientName FROM (
SELECT MAX(pm.maturity_date) AS `comp_date`, CONCAT('Maturity proceeds for Policy No: ', i.policy_num, 
                                                    ', ', ip.plan_name) AS `Particular`, 
       ROUND(SUM(pm.amount),2) AS `amount` ,2 as InvestmentType ,
       c.name as clientName,
    'Insurances' as ProductName
FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id IN (select client_id from clients where family_id = familyID)) AND (i.broker_id = brokerID) 
AND (i.mat_type = 'Single') AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
AND (i.status NOT IN (SELECT status_id FROM premium_status WHERE status IN ('Lapsed', 'Surrender')))
GROUP BY i.policy_num, ip.plan_name, a.adviser_name 
UNION 
SELECT pm.maturity_date AS `comp_date`, CONCAT('Insurance payout for Policy No: ', i.policy_num, 
                                              ', ', ip.plan_name) AS `Particular`, ROUND(pm.amount,2) AS `amount` ,2 as InvestmentType ,
c.name as clientName,
    'Insurances' as ProductName                                              

FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id IN (select client_id from clients where family_id = familyID)) AND (i.broker_id = brokerID) 
AND (i.mat_type = 'Regular') AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
AND (i.status NOT IN (SELECT status_id FROM premium_status WHERE status IN ('Lapsed', 'Surrender'))) 
GROUP BY i.policy_num, ip.plan_name, a.adviser_name, pm.maturity_date, pm.amount 
UNION
SELECT MAX(pm.maturity_date) AS `comp_date`, CONCAT('Surrender proceeds for Policy No: ', i.policy_num, 
                                                    ', ', ip.plan_name) AS `Particular`, ROUND(pm.amount,2) AS `amount` ,2 as InvestmentType,
c.name as clientName,
    'Insurances' as ProductName  
FROM premium_maturities AS pm 
INNER JOIN insurances AS i ON pm.policy_num = i.policy_num 
INNER JOIN clients AS c ON i.client_id = c.client_id 
INNER JOIN ins_plans AS ip ON i.plan_id = ip.plan_id 
INNER JOIN advisers AS a ON i.adv_id = a.adviser_id 
WHERE (pm.maturity_date BETWEEN fromDate AND toDate) AND (i.client_id IN (select client_id from clients where family_id = familyID)) AND (i.broker_id = brokerID) 
AND (i.status IN (SELECT status_id FROM premium_status WHERE status = 'Surrender')) 
AND (i.adv_id IN (SELECT adviser_id FROM advisers)) 
GROUP BY i.policy_num, ip.plan_name, a.adviser_name, pm.amount 
UNION 
SELECT wf.transaction_date AS `comp_date`, CONCAT('Withdrawal Proceeds', CASE WHEN wf.withdraw_from IS NOT NULL THEN CONCAT(' from ',wf.withdraw_from) ELSE '' END, ' - ', CASE WHEN add_notes IS NOT NULL THEN add_notes ELSE '' END, 
                                                  ' (', CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE b.bank_name END, 
                                                  CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No.: ',ba.account_number) END, 
                                                  CASE WHEN wf.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',wf.cheque_no) END, 
                                                  ')') AS `Particular`, ROUND(wf.amount,2) AS `amount` ,3 as InvestmentType,
c.name as clientName,
            'Equity' as ProductName

FROM withdraw_funds AS wf 
INNER JOIN clients AS c ON wf.client_id = c.client_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = wf.bank_account_id 
LEFT JOIN banks AS b ON b.bank_id = ba.bank_id 
WHERE (wf.transaction_date BETWEEN fromDate AND toDate) AND (wf.client_id IN (select client_id from clients where family_id = familyID)) AND (wf.broker_id = brokerID) AND (wf.withdraw_from <> 'Equity') 
UNION 
SELECT wf.transaction_date AS `comp_date`, CONCAT('Payout from ', tb.trading_broker_name, 
                                                  ', Client Code: ', wf.client_code, 
                                                  ' (', CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE b.bank_name END, 
                                                  CASE WHEN wf.bank_account_id IS NULL THEN '' ELSE CONCAT(', A/C No.: ',ba.account_number) END, 
                                                  CASE WHEN wf.cheque_no IS NULL THEN '' ELSE CONCAT(', Cheque No: ',wf.cheque_no) END, 
                                                  ')') AS `Particular`, ROUND(wf.amount,2) AS `amount` ,3 as InvestmentType,
c.name as clientName,
            'Equity' as ProductName                                                  
FROM withdraw_funds AS wf 
INNER JOIN clients AS c ON wf.client_id = c.client_id 
LEFT JOIN trading_brokers AS tb ON tb.trading_broker_id = wf.trading_broker_id 
LEFT JOIN bank_accounts AS ba ON ba.account_id = wf.bank_account_id 
LEFT JOIN banks AS b ON b.bank_id = ba.bank_id 
WHERE (wf.transaction_date BETWEEN fromDate AND toDate) AND (wf.client_id IN (select client_id from clients where family_id = familyID)) AND (wf.broker_id = brokerID) AND (wf.withdraw_from = 'Equity') 
GROUP BY tb.trading_broker_name, wf.client_code, wf.transaction_date, b.bank_name, wf.cheque_no, ba.account_number 
UNION
SELECT fdi.interest_date AS `comp_date`, CONCAT(fdt.interest_mode, ' interest for ', fdit.fd_inv_type, 
                                                ' of ', fdc.fd_comp_name, ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(fdi.interest_amount,2) AS `amount` ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                 
FROM fd_interests AS fdi 
INNER JOIN fd_transactions AS fdt ON fdi.fd_transaction_id = fdt.fd_transaction_id 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
INNER JOIN advisers AS a ON a.adviser_id = fdt.adv_id 
WHERE (fdi.interest_date BETWEEN fromDate AND toDate) AND (fdt.client_id IN (select client_id from clients where family_id = familyID)) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT fdt.maturity_date AS `comp_date`, CONCAT('Maturity proceeds of ', fdit.fd_inv_type, ' in ', fdc.fd_comp_name, 
                                                ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(SUM(fdt.maturity_amount),2) AS `amount` ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                  
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
WHERE (fdt.maturity_date BETWEEN fromDate AND toDate) AND (fdt.client_id IN (select client_id from clients where family_id = familyID)) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) AND (fdt.status <> 'PreMature') 
GROUP BY fdit.fd_inv_type, fdc.fd_comp_name, fdt.maturity_date, fdt.ref_number, b.bank_name, fdt.maturity_account_number 
UNION 
SELECT fdt.maturity_date AS `comp_date`, CONCAT('Premature withdrawal of ', fdit.fd_inv_type, ' in ', fdc.fd_comp_name, 
                                                ', Ref No.: ', fdt.ref_number, 
                                                ' (', CASE WHEN fdt.maturity_bank_id IS NULL THEN '' ELSE b.bank_name END, 
                                                CASE WHEN fdt.maturity_account_number IS NULL THEN '' ELSE CONCAT(', A/C No.: ',fdt.maturity_account_number) END, 
                                                ')') AS `Particular`, ROUND(SUM(fdt.maturity_amount),2) AS `amount` ,6 as InvestmentType,
c.name as clientName,
            'Fixed Deposite' as ProductName                                                  
FROM fd_transactions AS fdt 
INNER JOIN clients AS c ON fdt.client_id = c.client_id 
INNER JOIN fd_investment_types AS fdit ON fdit.fd_inv_id = fdt.fd_inv_id 
INNER JOIN fd_companies AS fdc ON fdc.fd_comp_id = fdt.fd_comp_id 
LEFT JOIN banks AS b ON fdt.maturity_bank_id = b.bank_id 
INNER JOIN advisers AS a ON a.adviser_id = fdt.adv_id 
WHERE (fdt.maturity_date BETWEEN fromDate AND toDate) AND (fdt.client_id IN (select client_id from clients where family_id = familyID)) AND (fdt.broker_id = brokerID) 
AND (fdt.adv_id IN (SELECT adviser_id FROM advisers)) AND (fdt.status = 'PreMature') 
GROUP BY fdit.fd_inv_type, fdc.fd_comp_name, fdt.maturity_date, fdt.ref_number, b.bank_name, fdt.maturity_account_number 
UNION 
SELECT prd.rent_date AS `comp_date`, CONCAT('Rent received for ', pt.property_name) AS `Particular`, ROUND(prd.amount,2) AS `amount` ,5 as InvestmentType,
c.name as clientName,
            'Real Estate' as ProductName  
FROM property_rent_details AS prd 
INNER JOIN property_transactions AS pt ON prd.pro_transaction_id = pt.pro_transaction_id 
INNER JOIN clients AS c ON pt.client_id = c.client_id 
INNER JOIN advisers AS a ON pt.adviser_id = a.adviser_id 
WHERE (pt.transaction_type = 'Purchase') AND (prd.rent_date BETWEEN fromDate AND toDate) AND (pt.client_id IN (select client_id from clients where family_id = familyID)) AND (pt.broker_id = brokerID) 
AND (pt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT pt.transaction_date AS `comp_date`, CONCAT('Sale proceeds of ', pt.property_name) AS `Particular`, ROUND(pt.amount,2) AS `amount` ,5 as InvestmentType,
c.name as clientName,
            'Real Estate' as ProductName 
FROM property_transactions AS pt 
INNER JOIN clients AS c ON pt.client_id = c.client_id 
INNER JOIN advisers AS a ON pt.adviser_id = a.adviser_id 
WHERE (pt.transaction_type = 'Sale') AND (pt.transaction_date BETWEEN fromDate AND toDate) AND (pt.client_id IN (select client_id from clients where family_id = familyID)) AND (pt.broker_id = brokerID) 
AND (pt.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT ct.transaction_date AS `comp_date`, CONCAT('Sale proceeds of ', ci.item_name, ' ', ct.quantity, ' ', cu.unit_name, 
                                                  ' @ Rs.', ct.transaction_rate) AS `Particular`, ct.total_amount AS `amount` ,4 as InvestmentType ,
c.name as clientName,
            'Commodity' as ProductName                                                 
FROM commodity_transactions AS ct 
INNER JOIN clients AS c ON ct.client_id = c.client_id 
INNER JOIN commodity_units AS cu ON cu.unit_id = ct.commodity_unit_id 
INNER JOIN commodity_items AS ci ON ci.item_id = ct.commodity_item_id 
INNER JOIN advisers AS a ON ct.adviser_id = a.adviser_id 
WHERE (ct.transaction_type = 'Sale') AND (ct.transaction_date BETWEEN fromDate AND toDate) AND (ct.client_id IN (select client_id from clients where family_id = familyID)) AND (ct.broker_id = brokerID) 
AND (ct.adviser_id IN (SELECT adviser_id FROM advisers)) 
UNION 
SELECT mft.purchase_date AS `comp_date`, CONCAT('Redemption proceeds of ', mfs.scheme_name, ', Folio No: ', mft.folio_number, CASE WHEN mft.bank_name IS NULL OR mft.bank_name = '' THEN CASE WHEN cb.bank_name IS NULL THEN '' ELSE CONCAT(' (',cb.bank_name) END ELSE CONCAT(' (',mft.bank_name) END,
                                   CASE WHEN mft.account_number IS NULL OR mft.account_number = '' THEN CASE WHEN cb.bank_acc_no IS NULL THEN '' ELSE CONCAT(', A/C No: ',cb.bank_acc_no) END ELSE CONCAT(', A/C No: ',mft.account_number) END,  CASE WHEN mft.cheque_number IS NULL OR mft.cheque_number = '' THEN ')' ELSE CONCAT(', Cheque No: ',mft.cheque_number,')') END) AS `Particular`, 
		(mft.quantity * mft.nav) AS `amount` ,1 as InvestmentType ,
c.name as clientName,
            'Mutual Fund' as ProductName          
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
LEFT JOIN client_bank_details cb ON (cb.client_id = mft.client_id AND cb.folio_number = mft.folio_number AND cb.productId = mfs.prod_code) 
WHERE (mft.mutual_fund_type IN ('RED')) AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id IN (select client_id from clients where family_id = familyID)) AND (mft.broker_id = brokerID) 
UNION 
SELECT mft.purchase_date AS `comp_date`, CONCAT('Dividend payout of ', mfs.scheme_name, ', Folio No: ', mft.folio_number) AS `Particular`, ROUND(mft.amount,2) AS `amount` ,1 as InvestmentType ,
c.name as clientName,
            'Mutual Fund' as ProductName  
FROM mutual_fund_transactions AS mft 
INNER JOIN clients AS c ON mft.client_id = c.client_id 
INNER JOIN mutual_fund_schemes AS mfs ON mfs.scheme_id = mft.mutual_fund_scheme 
WHERE (mft.mutual_fund_type IN ('DP')) AND (mft.purchase_date BETWEEN fromDate AND toDate) 
AND (mft.client_id IN (select client_id from clients where family_id = familyID)) AND (mft.broker_id = brokerID)
    ) AS tbl WHERE tbl.amount != 0 and (case when InvestmentType=0 then 1 when InvestmentType!=0 and tbl.InvestmentType=InvestmentType then 1 end)=1  ORDER BY `comp_date` ASC;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_liability_prepayment` (IN `liabilityID` INT, IN `prepayDate` DATE, IN `endDate` DATE, IN `amount` DECIMAL(18,2), IN `intRate` DECIMAL(18,2))  NO SQL BEGIN
	declare newDate date;
	delete from liability_maturity where liability_id = liabilityID and maturity_date > prepayDate;
	while DATEDIFF(endDate, prepayDate) >= 0 DO
		set prepayDate = Date_Add(prepayDate, INTERVAL 1 MONTH);
		insert into liability_maturity(liability_id, maturity_date, maturity_amount, interest_rate) values(liabilityID, prepayDate, amount, intRate);
	delete from liability_maturity where liability_id = liabilityID and maturity_date > endDate;
	END WHILE;
    update liability_transactions set end_date = endDate where liability_id = liabilityID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_liability_report` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL SELECT 
	c.name as client_name, pro.product_name, t.type_name, comp.company_name, sch.scheme_name, getLiabilityDate(l.liability_id) AS 'Period', interest_rate,
	l.installment_amount, l.total_liability, l.particular, l.liability_id, l.client_id, l.product_id, l.ref_number, Date_format(l.start_date, '%d/%m/%Y') as start_date, 
	Date_format(l.end_date, '%d/%m/%Y') as end_date, l.narration, fam.name as family_name
FROM liability_transactions AS l 
INNER JOIN clients AS c ON l.client_id = c.client_id 
inner join families as fam on c.family_id = fam.family_id
left JOIN al_products AS pro ON l.product_id = pro.product_id 
left join al_types as t on l.type_id = t.type_id 
left join al_companies as comp on l.company_id = comp.company_id 
left join al_schemes as sch on l.scheme_id = sch.scheme_id 
WHERE 
	(case when familyID='0' then 1 
			when familyID!='0' AND c.family_id = familyID then 1 end)=1 
	AND (l.end_date >= NOW()) and l.broker_id = brokerID
ORDER BY c.name$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_all_summary` (IN `brokerID` VARCHAR(10))  NO SQL begin
  create temporary table if not exists mf_clientwise_summary_family as (
  select
    c.name as client_name,
    c.pan_no,
	mfs.scheme_name as mf_scheme_name,
    mfs.prod_code,
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID and
   c.name not like '%(NH)%' 
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id,c.pan_no, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by f.name ,c.name  );
   select * from mf_clientwise_summary_family;


end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_aum_report` (IN `brokerID` VARCHAR(20))  NO SQL begin
create temporary table if not exists mf_aum_report as (
	select
    u.name as broker_name,
	f.name as family_name,
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mst.scheme_type as scheme_type,
    mfs.market_cap as market_cap,
    mfs.prod_code as prod_code,
  	sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
	(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
	(case when sum(mfv.div_payout) is null then sum((mfv.c_nav * mfv.live_unit)) else (sum((mfv.c_nav * mfv.live_unit))+sum(mfv.div_payout)) end) as total
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id and
	           upper(c.name) not like '%(NH)%' 
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    inner join users u on u.id=mfv.broker_id
    where mfv.broker_id = brokerID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by  mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  
		(case when sum(mfv.div_payout) is null then sum((mfv.c_nav * mfv.live_unit)) else (sum((mfv.c_nav * mfv.live_unit))+sum(mfv.div_payout)) end)
		desc );
   select * from mf_aum_report;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_aum_report_for_chart` (IN `brokerID` VARCHAR(30))  NO SQL select
    mfs.scheme_name as mf_scheme_name,
    mst.scheme_type as scheme_type,
    mst.scheme_type_id as scheme_type_id,
    mfs.market_cap as market_cap,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
  
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id and
	           upper(c.name) not like '%(NH)%' 
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    inner join users u on u.id=mfv.broker_id
    where mfv.broker_id = brokerID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by  mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0  
ORDER BY `mf_scheme_name` ASC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_cagr_mail` (IN `brokerID` VARCHAR(30))  NO SQL SELECT 
	mfv.valuation_id,
	mfv.transaction_id,
	mft.folio_number,
	fml.name as family_name,
	clnt.name as client_name,
	mft.purchase_date,
	mfv.div_payout,
	mfv.p_amount as purchase_amount,
	round(mfv.c_nav * mfv.live_unit, 2) as current_value,
	mfv.mf_cagr,
	mfv.mf_abs ,
    mfs.scheme_name,
    mfst.scheme_type,
   	(case when mft.mutual_fund_sub_type!='' 
     		then mft.mutual_fund_sub_type 
    	  else  mft.mutual_fund_type 
    end) as mutual_fund_type

FROM `mutual_fund_valuation` mfv 
INNER JOIN `mutual_fund_transactions` mft 
	ON mfv.transaction_id = mft.transaction_id 
INNER JOIN `clients` clnt 
	ON clnt.client_id = mft.client_id 
INNER JOIN `families` fml 
	ON fml.family_id = mft.family_id 

INNER JOIN `mutual_fund_schemes` mfs 
	ON mft.mutual_fund_scheme = mfs.scheme_id 
INNER JOIN `mf_scheme_types` mfst 
	ON mfs.scheme_type_id = mfst.scheme_type_id 
LEFT JOIN `mutual_fund_valuation_cagr` mfvc 
	ON mfvc.transaction_id = mfv.transaction_id 
WHERE 
	IFNULL(mfvc.mf_cagr,0)< mfst.scheme_target_value and
	mfst.scheme_target_value <= mfv.mf_cagr AND 
    mfst.scheme_target_value <= mfv.mf_abs AND
    mft.mutual_fund_type <> 'DIV' AND
    mfv.p_amount > 1 AND
  mfv.broker_id = brokerID
ORDER BY current_value DESC$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divp` (IN `brokerID` VARCHAR(10), IN `partialCalc` INT)  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';
IF partialCalc = 1 THEN
SET @qry1 = CONCAT('update mutual_fund_transactions t 
set t.DPO_units = t.amount/NULLIF((
	(select case when sum(t1.quantity) is null then 0 
     else sum(t1.quantity) end from (select * from mutual_fund_transactions) as t1 
     where (t1.purchase_date <= t.purchase_date) 
     and t1.client_id = t.client_id 
     and t1.mutual_fund_scheme = t.mutual_fund_scheme 
     and t1.folio_number = t.folio_number 
     and t1.transaction_type = "Purchase" and t1.broker_id = t.broker_id) - 
	(select case when sum(t2.quantity) is null then 0 
     else sum(t2.quantity) end from (select * from mutual_fund_transactions) as t2 
     where (t2.purchase_date <= t.purchase_date) 
     and t2.client_id = t.client_id 
     and t2.mutual_fund_scheme = t.mutual_fund_scheme 
     and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
     and t2.mutual_fund_type IN ("SWO","RED"))
),0)
where t.mutual_fund_type = "DP" and t.broker_id = "',brokerID,'" 
and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'");');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
drop temporary table if exists tempvalp;
SET @qry2 = CONCAT('create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_transactions t where t.broker_id = "',brokerID,'" 
and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'"));');
IF(@qry2 != '') THEN
  	PREPARE stmt2 FROM @qry2;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
alter table tempvalp add primary key (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
/*CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	div_payout DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);*/
/*INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id 
FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;*/
SET @qry3 = CONCAT('CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, v.transaction_id, live_unit, div_payout,  v.broker_id 
    FROM mutual_fund_valuation v 
	INNER JOIN mutual_fund_transactions t 
	ON v.transaction_id = t.transaction_id 
    WHERE v.broker_id = "',brokerID,'" 
	and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
    ORDER BY valuation_id
);');
IF(@qry3 != '') THEN
  	PREPARE stmt3 FROM @qry3;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;
ELSE 
update mutual_fund_transactions t 
set t.DPO_units = t.amount/NULLIF((
	(select case when sum(t1.quantity) is null then 0 
     else sum(t1.quantity) end from (select * from mutual_fund_transactions) as t1 
     where (t1.purchase_date <= t.purchase_date) 
     and t1.client_id = t.client_id 
     and t1.mutual_fund_scheme = t.mutual_fund_scheme 
     and t1.folio_number = t.folio_number 
     and t1.transaction_type = 'Purchase' and t1.broker_id = t.broker_id) - 
	(select case when sum(t2.quantity) is null then 0 
     else sum(t2.quantity) end from (select * from mutual_fund_transactions) as t2 
     where (t2.purchase_date <= t.purchase_date) 
     and t2.client_id = t.client_id 
     and t2.mutual_fund_scheme = t.mutual_fund_scheme 
     and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
     and t2.mutual_fund_type IN ('SWO','RED'))
),0)
where t.mutual_fund_type = 'DP' and t.broker_id = brokerID;
drop temporary table if exists tempvalp;
create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_transactions t where broker_id = brokerID);
alter table tempvalp add primary key (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
/*CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	div_payout DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);*/
/*INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id 
FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;*/
CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, transaction_id, live_unit, div_payout,  broker_id 
    FROM mutual_fund_valuation 
    WHERE broker_id = brokerID 
    ORDER BY valuation_id
);
END IF;
alter table mf_val_temp add primary key (valuation_id), 
	add index transaction_id (transaction_id), 
	add index broker_id (broker_id);
update mf_val_temp v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_payout = v.live_unit * (
	(select sum(DPO_units) from tempvalp mft 
     where mft.client_id = t.client_id 
     and mft.mutual_fund_scheme = t.mutual_fund_scheme 
     and mft.folio_number = t.folio_number 
     and mft.broker_id = v.broker_id 
     and mft.purchase_date >= t.purchase_date 
     and mft.transaction_id != v.transaction_id)
) 
where v.broker_id = brokerID;
UPDATE mutual_fund_valuation v 
INNER JOIN mf_val_temp vt 
ON v.transaction_id = vt.transaction_id 
SET v.div_payout = vt.div_payout 
WHERE v.broker_id = brokerID 
AND vt.div_payout != v.div_payout;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divp_delete_op_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';



SET @qry3 = CONCAT('update mutual_fund_valuation_delete_op_',brokerID,' v 
inner join mutual_fund_transactions t 
	on v.transaction_id = t.transaction_id and 
	   t.purchase_date <="',reportDate,'"
set v.div_payout = v.live_unit * 
		((select sum(DPO_units) 
		  from mutual_fund_transactions mft 
		  where
				mft.purchase_date <="',reportDate,'" and 
				mft.client_id = t.client_id and 
				mft.mutual_fund_scheme = t.mutual_fund_scheme and 
				mft.folio_number = t.folio_number and 
				mft.broker_id = v.broker_id and 
				mft.purchase_date >= t.purchase_date and 
				mft.transaction_id != v.transaction_id))');
IF(@qry3 != '') THEN
  	PREPARE stmt3 FROM @qry3;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;




END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divp_historical` (IN `brokerID` VARCHAR(20), IN `reportDate` DATE)  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';



SET @qry3 = CONCAT('update mutual_fund_valuation_h_',brokerID,' v 
inner join mutual_fund_transactions t 
	on v.transaction_id = t.transaction_id and 
	   t.purchase_date <="',reportDate,'"
set v.div_payout = v.live_unit * 
		((select sum(DPO_units) 
		  from mutual_fund_transactions mft 
		  where
				mft.purchase_date <="',reportDate,'" and 
				mft.client_id = t.client_id and 
				mft.mutual_fund_scheme = t.mutual_fund_scheme and 
				mft.folio_number = t.folio_number and 
				mft.broker_id = v.broker_id and 
				mft.purchase_date >= t.purchase_date and 
				mft.transaction_id != v.transaction_id))');
IF(@qry3 != '') THEN
  	PREPARE stmt3 FROM @qry3;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;




END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divp_OLD` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';
IF partialCalc = 1 THEN
SET @qry1 = CONCAT('update mutual_fund_transactions t 
set t.DPO_units = t.amount/NULLIF((
	(select case when sum(t1.quantity) is null then 0 
     else sum(t1.quantity) end from (select * from mutual_fund_transactions) as t1 
     where (t1.purchase_date <= t.purchase_date) 
     and t1.client_id = t.client_id 
     and t1.mutual_fund_scheme = t.mutual_fund_scheme 
     and t1.folio_number = t.folio_number 
     and t1.transaction_type = "Purchase" and t1.broker_id = t.broker_id) - 
	(select case when sum(t2.quantity) is null then 0 
     else sum(t2.quantity) end from (select * from mutual_fund_transactions) as t2 
     where (t2.purchase_date <= t.purchase_date) 
     and t2.client_id = t.client_id 
     and t2.mutual_fund_scheme = t.mutual_fund_scheme 
     and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
     and t2.mutual_fund_type IN ("SWO","RED"))
),0)
where t.mutual_fund_type = "DP" and t.broker_id = "',brokerID,'" 
and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'");');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
drop temporary table if exists tempvalp;
SET @qry2 = CONCAT('create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_transactions t where t.broker_id = "',brokerID,'" 
and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'"));');
IF(@qry2 != '') THEN
  	PREPARE stmt2 FROM @qry2;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
alter table tempvalp add primary key (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
/*CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	div_payout DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);*/
/*INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id 
FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;*/
SET @qry3 = CONCAT('CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, v.transaction_id, live_unit, div_payout,  v.broker_id 
    FROM mutual_fund_valuation v 
	INNER JOIN mutual_fund_transactions t 
	ON v.transaction_id = t.transaction_id 
    WHERE v.broker_id = "',brokerID,'" 
	and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
    ORDER BY valuation_id
);');
IF(@qry3 != '') THEN
  	PREPARE stmt3 FROM @qry3;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;
ELSE 
update mutual_fund_transactions t 
set t.DPO_units = t.amount/NULLIF((
	(select case when sum(t1.quantity) is null then 0 
     else sum(t1.quantity) end from (select * from mutual_fund_transactions) as t1 
     where (t1.purchase_date <= t.purchase_date) 
     and t1.client_id = t.client_id 
     and t1.mutual_fund_scheme = t.mutual_fund_scheme 
     and t1.folio_number = t.folio_number 
     and t1.transaction_type = 'Purchase' and t1.broker_id = t.broker_id) - 
	(select case when sum(t2.quantity) is null then 0 
     else sum(t2.quantity) end from (select * from mutual_fund_transactions) as t2 
     where (t2.purchase_date <= t.purchase_date) 
     and t2.client_id = t.client_id 
     and t2.mutual_fund_scheme = t.mutual_fund_scheme 
     and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
     and t2.mutual_fund_type IN ('SWO','RED'))
),0)
where t.mutual_fund_type = 'DP' and t.broker_id = brokerID;
drop temporary table if exists tempvalp;
create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_transactions t where broker_id = brokerID);
alter table tempvalp add primary key (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
/*CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	div_payout DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);*/
/*INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id 
FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;*/
CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, transaction_id, live_unit, div_payout,  broker_id 
    FROM mutual_fund_valuation 
    WHERE broker_id = brokerID 
    ORDER BY valuation_id
);
END IF;
alter table mf_val_temp add primary key (valuation_id), 
	add index transaction_id (transaction_id), 
	add index broker_id (broker_id);
update mf_val_temp v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_payout = v.live_unit * (
	(select sum(DPO_units) from tempvalp mft 
     where mft.client_id = t.client_id 
     and mft.mutual_fund_scheme = t.mutual_fund_scheme 
     and mft.folio_number = t.folio_number 
     and mft.broker_id = v.broker_id 
     and mft.purchase_date >= t.purchase_date 
     and mft.transaction_id != v.transaction_id)
) 
where v.broker_id = brokerID;
UPDATE mutual_fund_valuation v 
INNER JOIN mf_val_temp vt 
ON v.transaction_id = vt.transaction_id 
SET v.div_payout = vt.div_payout 
WHERE v.broker_id = brokerID 
AND vt.div_payout != v.div_payout;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divr` (IN `brokerID` VARCHAR(10), IN `partialCalc` INT)  NO SQL BEGIN
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';
IF partialCalc = 1 THEN
SET @qry1 = CONCAT('update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = "Purchase" and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN ("SWO","RED"))
),0)
where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'");');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
drop temporary table if exists tempval;
SET @qry2 = CONCAT('create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id 
                                  where v.broker_id = "',brokerID,'" 
                                  and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'"));');
IF(@qry2 != '') THEN
  	PREPARE stmt2 FROM @qry2;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
alter table tempval add primary key (valuation_id),
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
SET @qry3 = CONCAT('CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, v.transaction_id, live_unit, unit_per_count, div_r2, v.broker_id 
    FROM mutual_fund_valuation v 
	INNER JOIN mutual_fund_transactions t 
	ON v.transaction_id = t.transaction_id 
    WHERE v.broker_id = "',brokerID,'" 
	and t.mutual_fund_scheme IN (select distinct mutual_fund_scheme from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.folio_number IN (select distinct folio_number from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'") 
	and t.client_id IN(select distinct client_id from mf_trans_temp_',brokerID,' where broker_id = "',brokerID,'")
    ORDER BY valuation_id);'
    );
IF(@qry3 != '') THEN
  	PREPARE stmt3 FROM @qry3;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;
ELSE 
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = 'Purchase' and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN ('SWO','RED'))
),0)
where t.mutual_fund_type = 'DIV' and v.broker_id = brokerID;
drop temporary table if exists tempval;
create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id 
                                  where v.broker_id = brokerID);
alter table tempval add primary key (valuation_id),
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
/*CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	unit_per_count DECIMAL (30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);
INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;*/
CREATE TEMPORARY TABLE `mf_val_temp` AS (
    SELECT valuation_id, transaction_id, live_unit, unit_per_count, div_r2, broker_id 
    FROM mutual_fund_valuation 
    WHERE broker_id = brokerID 
    ORDER BY valuation_id
    );
END IF;
alter table mf_val_temp add primary key (valuation_id),
	add index transaction_id (transaction_id),  
	add index broker_id (broker_id);
update mf_val_temp v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_r2 = v.live_unit * (
	(select sum(mfv.unit_per_count) from tempval mfv 
     where mfv.client_id = t.client_id 
     and mfv.mutual_fund_scheme = t.mutual_fund_scheme 
     and mfv.folio_number = t.folio_number 
     and mfv.broker_id = v.broker_id 
     and mfv.purchase_date >= t.purchase_date 
     and mfv.transaction_id != v.transaction_id
     group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
) 
where v.broker_id = brokerID;
UPDATE mutual_fund_valuation v 
INNER JOIN mf_val_temp vt 
ON v.transaction_id = vt.transaction_id 
SET v.div_r2 = vt.div_r2 
WHERE v.broker_id = brokerID 
AND vt.div_r2 != v.div_r2;
/* update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_amount = v.p_amount, v.p_amount = 0 
where v.transaction_id IN(
    select transaction_id from mutual_fund_transactions 
    where mutual_fund_type = 'DIV' and broker_id = brokerID
);
*/
/*update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.p_amount = (v.live_unit * t.nav), v.div_amount = 0.00 
where v.broker_id = brokerID;
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_amount = v.p_amount, v.p_amount = 0.00 
where t.mutual_fund_type = 'DIV' and v.broker_id = brokerID 
and v.p_amount > 0;*/
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divr_delete_op_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL BEGIN
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';

SET @qry1 = CONCAT('UPDATE mutual_fund_valuation_delete_op_',brokerID,' v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
	 and purchase_date <= "',reportDate,'"
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = "Purchase" and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
	 and purchase_date <= "',reportDate,'"
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN ("SWO","RED"))
),0)
where t.mutual_fund_type = 'DIV' and v.broker_id = "',brokerID,'";');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



drop temporary table if exists tempval;

SET @qry1 = CONCAT('
create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation_h_',brokerID,' v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id 
                                  where v.broker_id = "',brokerID,'");');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

alter table tempval add primary key (valuation_id),
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);


SET @qry1 = CONCAT('update mutual_fund_valuation_h_',brokerID,' v 
inner join mutual_fund_transactions t1 
on v.transaction_id = t1.transaction_id 
set v.div_r2 = v.live_unit * (
	(select sum(mfv.unit_per_count) from tempval mfv 
     where mfv.client_id = t1.client_id 
     and mfv.mutual_fund_scheme = t1.mutual_fund_scheme 
     and mfv.folio_number = t1.folio_number 
     and mfv.broker_id = v.broker_id 
     and mfv.purchase_date >= t1.purchase_date 
     and mfv.transaction_id != v.transaction_id
     group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
) 
where v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divr_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL BEGIN
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';

SET @qry1 = CONCAT('UPDATE mutual_fund_valuation_h_',brokerID,' v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
	 and purchase_date <= "',reportDate,'"
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = "Purchase" and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
	 and purchase_date <= "',reportDate,'"
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN (''SWO'',''RED''))
),0)
where t.mutual_fund_type = ''DIV'' and v.broker_id = "',brokerID,'";');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



drop temporary table if exists tempval;

SET @qry1 = CONCAT('
create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation_h_',brokerID,' v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id 
                                  where v.broker_id = "',brokerID,'");');
IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

alter table tempval add primary key (valuation_id),
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);


SET @qry1 = CONCAT('update mutual_fund_valuation_h_',brokerID,' v 
inner join mutual_fund_transactions t1 
on v.transaction_id = t1.transaction_id 
set v.div_r2 = v.live_unit * (
	(select sum(mfv.unit_per_count) from tempval mfv 
     where mfv.client_id = t1.client_id 
     and mfv.mutual_fund_scheme = t1.mutual_fund_scheme 
     and mfv.folio_number = t1.folio_number 
     and mfv.broker_id = v.broker_id 
     and mfv.purchase_date >= t1.purchase_date 
     and mfv.transaction_id != v.transaction_id
     group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
) 
where v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_divr_OLD` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN
IF brokerID = 'all' THEN 
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = 'Purchase' and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN ('SWO','RED'))
),0)
where t.mutual_fund_type = 'DIV';
drop temporary table if exists tempval;
create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id);
alter table tempval add primary key (valuation_id);
alter table tempval 
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	unit_per_count DECIMAL (30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);
INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id FROM mutual_fund_valuation 
WHERE 1 
ORDER BY valuation_id;
update mf_val_temp v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_r2 = v.live_unit * (
	(select sum(mfv.unit_per_count) from tempval mfv 
     where mfv.client_id = t.client_id 
     and mfv.mutual_fund_scheme = t.mutual_fund_scheme 
     and mfv.folio_number = t.folio_number 
     and mfv.broker_id = v.broker_id 
     and mfv.purchase_date >= t.purchase_date 
     and mfv.transaction_id != v.transaction_id
     group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
) 
where 1;
UPDATE mutual_fund_valuation v 
INNER JOIN mf_val_temp vt 
ON v.transaction_id = vt.transaction_id 
SET v.div_r2 = vt.div_r2 
WHERE vt.div_r2 != v.div_r2;
ELSE 
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.unit_per_count = t.amount/NULLIF((
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number 
     and transaction_type = 'Purchase' and broker_id = t.broker_id) - 
	(select case when sum(quantity) is null then 0 
     else sum(quantity) end from mutual_fund_transactions 
     where (purchase_date < t.purchase_date) 
     and client_id = t.client_id 
     and mutual_fund_scheme = t.mutual_fund_scheme 
     and folio_number = t.folio_number and broker_id = t.broker_id 
     and mutual_fund_type IN ('SWO','RED'))
),0)
where t.mutual_fund_type = 'DIV' and v.broker_id = brokerID;
drop temporary table if exists tempval;
create temporary table tempval as (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
									t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
									from mutual_fund_valuation v 
									inner join mutual_fund_transactions t on v.transaction_id = t.transaction_id);
alter table tempval add primary key (valuation_id);
alter table tempval 
	add index transaction_id (transaction_id), 
	add index mutual_fund_scheme (mutual_fund_scheme), 
	add index folio_number (folio_number), 
    add index purchase_date (purchase_date), 
	add index client_id (client_id), 
	add index broker_id (broker_id);
DROP TEMPORARY TABLE IF EXISTS `mf_val_temp`;
CREATE TEMPORARY TABLE `mf_val_temp` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
	unit_per_count DECIMAL (30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,10) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);
INSERT INTO `mf_val_temp`(valuation_id, transaction_id, live_unit, broker_id)
SELECT valuation_id, transaction_id, live_unit, broker_id FROM mutual_fund_valuation 
WHERE broker_id = brokerID 
ORDER BY valuation_id;
update mf_val_temp v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_r2 = v.live_unit * (
	(select sum(mfv.unit_per_count) from tempval mfv 
     where mfv.client_id = t.client_id 
     and mfv.mutual_fund_scheme = t.mutual_fund_scheme 
     and mfv.folio_number = t.folio_number 
     and mfv.broker_id = v.broker_id 
     and mfv.purchase_date >= t.purchase_date 
     and mfv.transaction_id != v.transaction_id
     group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
) 
where v.broker_id = brokerID;
UPDATE mutual_fund_valuation v 
INNER JOIN mf_val_temp vt 
ON v.transaction_id = vt.transaction_id 
SET v.div_r2 = vt.div_r2 
WHERE v.broker_id = brokerID 
AND vt.div_r2 != v.div_r2;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_div_temp_trans` (IN `brokerID` VARCHAR(10), IN `transID` BIGINT)  NO SQL BEGIN 
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_alter = '';
/* drop table if exists */
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_trans_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
/* create new temp table */
SET @sql_create = CONCAT('CREATE TABLE `mf_trans_temp_',brokerID,'` as 					(select t.transaction_id, 
                  t.purchase_date, t.quantity,
                  t.mutual_fund_scheme, t.folio_number, 
                  t.client_id, t.broker_id 
                  from mutual_fund_transactions t 
                  where t.broker_id = "',brokerID,'" 
                  and t.transaction_id > ',transID,'
				 );');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
/* add primary key and indexes to temp table */
SET @sql_alter = CONCAT('ALTER TABLE `mf_trans_temp_',brokerID,'` 
                         ADD PRIMARY KEY (transaction_id), 
                         ADD INDEX mutual_fund_scheme (mutual_fund_scheme), 
                         ADD INDEX folio_number (folio_number), 
                         ADD INDEX purchase_date (purchase_date), 
                         ADD INDEX client_id (client_id), 
                         ADD INDEX broker_id (broker_id);');
IF(@sql_alter != '') THEN
  	PREPARE stmt3 FROM @sql_alter;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units` (IN `brokerID` VARCHAR(10), IN `transID` BIGINT)  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';

DELETE FROM mutual_fund_valuation 
WHERE broker_id = brokerID AND transaction_id > transID;

INSERT INTO mutual_fund_valuation(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id 
FROM mutual_fund_transactions WHERE broker_id = brokerID 
AND transaction_id > transID AND transaction_type = "Purchase"
 ORDER BY purchase_date asc,quantity desc, transaction_id Asc;

DELETE v0.* FROM mutual_fund_valuation v0 
INNER JOIN mutual_fund_transactions t0 ON v0.transaction_id = t0.transaction_id 
WHERE (v0.transaction_id IN
(SELECT MAX(t.transaction_id) FROM mutual_fund_transactions t 
INNER JOIN mutual_fund_transactions t2 
ON t.ref_no = t2.rej_ref_no AND t.quantity = -t2.quantity 
AND t.mutual_fund_scheme = t2.mutual_fund_scheme 
AND t.folio_number = t2.folio_number AND t.client_id = t2.client_id 
AND t.broker_id = t2.broker_id 
WHERE t.transaction_type = "Purchase" 
AND t.broker_id = brokerID 
AND t.quantity >= 0 AND t2.transaction_id > transID 
AND t.purchase_date <= t2.purchase_date 
GROUP BY t.ref_no,t.folio_number,  t.mutual_fund_scheme, 
 t.client_id, t.quantity, t.broker_id 
ORDER BY t.purchase_date asc, t.transaction_type asc, t.quantity desc, 
t.orig_trxn_no asc, t.transaction_id asc) 
OR t0.quantity <= 0 AND t0.rej_ref_no != "" AND t0.rej_ref_no IS NOT NULL)
AND v0.broker_id = brokerID 
AND t0.transaction_type = "Purchase";




SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

SET @sql_create = CONCAT('CREATE TABLE `mf_val_temp_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;

SET @sql_insert = CONCAT('INSERT INTO `mf_val_temp_',brokerID,'`(transaction_id, live_unit, broker_id)
SELECT transaction_id, live_unit, broker_id FROM mutual_fund_valuation 
WHERE broker_id = "',brokerID,'" 
ORDER BY valuation_id;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;


SET @sql_update_first = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
inner join (SELECT MIN(x.purchase_date) as purchase_date , min(x.customOrder) as customOrder1, x.mutual_fund_scheme, x.folio_number, x.client_id,x.broker_id
			FROM 
            (SELECT t.* from mutual_fund_transactions t 
     		INNER JOIN `mf_val_temp_',brokerID,'` v 
     		ON v.transaction_id = t.transaction_id
    		ORDER BY t.purchase_date,t.transaction_id) x 		
     GROUP BY x.broker_id,x.client_id, x.folio_number,x.mutual_fund_scheme
	 HAVING MIN(x.purchase_date) ) t10
	on  t10.folio_number = mft.folio_number
	 AND t10.mutual_fund_scheme = mft.mutual_fund_scheme
     AND t10.client_id = mft.client_id
     AND t10.broker_id = mft.broker_id	 
	 AND t10.purchase_date=mft.purchase_date 
     and t10.customOrder1=mft.customOrder
	 
                               
SET vt.live_unit = vt.live_unit +
IFNULL((SELECT -(SUM(t2.quantity)) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	AND t2.transaction_id > ',transID,'
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id
    AND t2.broker_id = mft.broker_id 
),0)	
WHERE 
	mft.broker_id = "',brokerID,'";');
	
	
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
SET @sql_update_all = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio = folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mf_val_temp_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id,a.folio_number,  a.mutual_fund_scheme, a.purchase_date, a.valuation_id) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance 
WHERE vt1.live_unit != x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt6 FROM @sql_update_all;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;
SET @sql_update_val = CONCAT('UPDATE mutual_fund_valuation v 
INNER JOIN `mf_val_temp_',brokerID,'` vt 
ON v.transaction_id = vt.transaction_id 
SET v.live_unit = vt.live_unit 
WHERE vt.live_unit != v.live_unit;');
IF(@sql_update_val != '') THEN
  	PREPARE stmt7 FROM @sql_update_val;
  	EXECUTE stmt7;
  	DEALLOCATE PREPARE stmt7;
END IF;
SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation` 
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt10 FROM @sql_drop;
  	EXECUTE stmt10;
  	DEALLOCATE PREPARE stmt10;
END IF;
UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = brokerID; 
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_amount = v.p_amount, v.p_amount = 0.00 
where t.mutual_fund_type = 'DIV' and v.broker_id = brokerID 
and v.p_amount > 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units_backup` (IN `brokerID` VARCHAR(10), IN `transID` BIGINT)  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';

DELETE FROM mutual_fund_valuation 
WHERE broker_id = brokerID AND transaction_id > transID;

INSERT INTO mutual_fund_valuation(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id 
FROM mutual_fund_transactions WHERE broker_id = brokerID 
AND transaction_id > transID AND transaction_type = "Purchase"
 ORDER BY purchase_date asc,transaction_id Asc;

DELETE v0.* FROM mutual_fund_valuation v0 
INNER JOIN mutual_fund_transactions t0 ON v0.transaction_id = t0.transaction_id 
WHERE (v0.transaction_id IN
(SELECT MAX(t.transaction_id) FROM mutual_fund_transactions t 
INNER JOIN mutual_fund_transactions t2 
ON t.ref_no = t2.rej_ref_no AND t.quantity = -t2.quantity 
AND t.mutual_fund_scheme = t2.mutual_fund_scheme 
AND t.folio_number = t2.folio_number AND t.client_id = t2.client_id 
AND t.broker_id = t2.broker_id 
WHERE t.transaction_type = "Purchase" 
AND t.broker_id = brokerID 
AND t.quantity >= 0 AND t2.transaction_id > transID 
AND t.purchase_date <= t2.purchase_date 
GROUP BY t.ref_no,t.folio_number,  t.mutual_fund_scheme, 
 t.client_id, t.quantity, t.broker_id 
ORDER BY t.purchase_date asc, t.transaction_type asc, t.quantity desc, 
t.orig_trxn_no asc, t.transaction_id asc) 
OR t0.quantity <= 0 AND t0.rej_ref_no != "" AND t0.rej_ref_no IS NOT NULL)
AND v0.broker_id = brokerID 
AND t0.transaction_type = "Purchase";




SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

SET @sql_create = CONCAT('CREATE TABLE `mf_val_temp_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;

SET @sql_insert = CONCAT('INSERT INTO `mf_val_temp_',brokerID,'`(transaction_id, live_unit, broker_id)
SELECT transaction_id, live_unit, broker_id FROM mutual_fund_valuation 
WHERE broker_id = "',brokerID,'" 
ORDER BY valuation_id;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;


SET @sql_update_first = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
inner join (SELECT MIN(x.purchase_date) as purchase_date ,x.mutual_fund_scheme, x.folio_number, x.client_id,x.broker_id
			FROM 
            (SELECT t.* from mutual_fund_transactions t 
     		INNER JOIN `mf_val_temp_',brokerID,'` v 
     		ON v.transaction_id = t.transaction_id
    		ORDER BY t.purchase_date) x 		
     GROUP BY x.folio_number, x.mutual_fund_scheme,  x.client_id ,x.broker_id
	 HAVING MIN(x.purchase_date)) t10
	on  t10.folio_number = mft.folio_number
	 AND t10.mutual_fund_scheme = mft.mutual_fund_scheme
     AND t10.client_id = mft.client_id
     AND t10.broker_id = mft.broker_id	 
	 AND t10.purchase_date=mft.purchase_date 
	 
                               
SET vt.live_unit = vt.live_unit +
IFNULL((SELECT -(SUM(t2.quantity)) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	AND t2.transaction_id > ',transID,'
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id
    AND t2.broker_id = mft.broker_id 
),0)	
WHERE 
	mft.broker_id = "',brokerID,'";');
	
	
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
SET @sql_update_all = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio = folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mf_val_temp_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id,a.folio_number,  a.mutual_fund_scheme, a.purchase_date, a.valuation_id) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance 
WHERE vt1.live_unit != x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt6 FROM @sql_update_all;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;
SET @sql_update_val = CONCAT('UPDATE mutual_fund_valuation v 
INNER JOIN `mf_val_temp_',brokerID,'` vt 
ON v.transaction_id = vt.transaction_id 
SET v.live_unit = vt.live_unit 
WHERE vt.live_unit != v.live_unit;');
IF(@sql_update_val != '') THEN
  	PREPARE stmt7 FROM @sql_update_val;
  	EXECUTE stmt7;
  	DEALLOCATE PREPARE stmt7;
END IF;
SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation` 
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt10 FROM @sql_drop;
  	EXECUTE stmt10;
  	DEALLOCATE PREPARE stmt10;
END IF;
UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = brokerID; 
update mutual_fund_valuation v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 
set v.div_amount = v.p_amount, v.p_amount = 0.00 
where t.mutual_fund_type = 'DIV' and v.broker_id = brokerID 
and v.p_amount > 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units_delete_op_historical_c` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE, IN `clientID` VARCHAR(30))  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';


SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mutual_fund_valuation_delete_op_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mutual_fund_valuation_delete_op_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
	c_nav DECIMAL(18,4) DEFAULT NULL,
	c_nav_date DATE DEFAULT NULL,
    live_unit DECIMAL(30,4) DEFAULT NULL,
	unit_per_count DECIMAL(30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,4) DEFAULT NULL,
	div_payout DECIMAL(30,4) DEFAULT NULL,
	div_amount DECIMAL(30,2) DEFAULT NULL,
	p_amount DECIMAL(30,2) DEFAULT NULL,
	transaction_day int DEFAULT NULL,
	mf_abs DECIMAL(18,2) DEFAULT NULL,
	mf_cagr DECIMAL(18,2) DEFAULT NULL,
	prod_code varchar(100) DEFAULT NULL,
	scheme_name varchar(300) DEFAULT NULL,
    status int default 0,
    broker_id VARCHAR(10) NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');

IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;


CREATE TEMPORARY TABLE `mutual_fund_valuation_temp` (
    valuation_id BIGINT,
    transaction_id BIGINT,
    client_id varchar(50),
    scheme_id int,
    folio_number varchar(50),
    live_unit DECIMAL(30,4) DEFAULT NULL,
	status int default 0
);


INSERT INTO `mutual_fund_valuation_temp`(transaction_id, live_unit, client_id,scheme_id,folio_number) 
SELECT transaction_id, quantity, client_id,mutual_fund_scheme,folio_number FROM mutual_fund_transactions 
WHERE broker_id = brokerID
and purchase_date <= reportDate
AND client_id = clientID
AND transaction_type = "Purchase" 
order by purchase_date ;

SET @sql_insert = CONCAT('INSERT INTO `mutual_fund_valuation_delete_op_',brokerID,'`(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id FROM mutual_fund_transactions 
WHERE broker_id = "',brokerID,'" 
and purchase_date <= "',reportDate,'" 
AND client_id = "',clientID,'"
AND transaction_type = "Purchase" 
order by purchase_date ;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;

while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit<0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit<0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
   

    update mutual_fund_valuation_temp set live_unit=0,status=1 where live_unit=ABS(@live_unit) and client_id=@client_id and scheme_id=@scheme_id
    and folio_number=@folio_number;

    update mutual_fund_valuation_temp set live_unit=0,status=1  where transaction_id=@transaction_id;
    
end while;



while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit>0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);

    set @RedemptionQty=(SELECT SUM(t2.quantity) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	and t2.purchase_date <= reportDate
    AND t2.mutual_fund_scheme = @scheme_id
    AND t2.folio_number = @folio_number
    AND t2.client_id = @client_id);

    if(@RedemptionQty>0)
    then
    BEGIN
            if(@RedemptionQty<=@live_unit)
            then
            BEGIN
                update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=1 where  transaction_id=@transaction_id;
                set @RedemptionQty=@RedemptionQty-@live_unit;
            end;
            else
            BEGIN
            
                while (EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number) and @RedemptionQty>0) do
                
                    set @transaction_id_1=(select transaction_id from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number limit 1);
                    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id_1 limit 1);

                    if(@RedemptionQty<=@live_unit)
                    then
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=@RedemptionQty where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    else
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=0,status=1 where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    end if;
                end while;
            end;
            end if;
    end;
    end if;
    update mutual_fund_valuation_temp set status=1 
    WHERE scheme_id = @scheme_id AND folio_number = @folio_number AND client_id = @client_id and status=0; 
end while;


SET @sql_update_first ='';
SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_delete_op_',brokerID,'` vt
INNER JOIN mutual_fund_valuation_temp temp
ON vt.transaction_id = temp.transaction_id
	 
SET vt.live_unit = temp.live_unit');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;



SET @sql_update_all = '';
SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation_delete_op_',brokerID,'`
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;



SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_delete_op_',brokerID,'` v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 

SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;




SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_delete_op_',brokerID,'` v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 

set v.div_amount = v.p_amount, 
v.p_amount = 0.00 
where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
and v.p_amount > 0;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE, IN `familyID` VARCHAR(30))  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';


SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mutual_fund_valuation_h_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mutual_fund_valuation_h_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
	c_nav DECIMAL(18,4) DEFAULT NULL,
	c_nav_date DATE DEFAULT NULL,
    live_unit DECIMAL(30,4) DEFAULT NULL,
	unit_per_count DECIMAL(30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,4) DEFAULT NULL,
	div_payout DECIMAL(30,4) DEFAULT NULL,
	div_amount DECIMAL(30,2) DEFAULT NULL,
	p_amount DECIMAL(30,2) DEFAULT NULL,
	transaction_day int DEFAULT NULL,
	mf_abs DECIMAL(18,2) DEFAULT NULL,
	mf_cagr DECIMAL(18,2) DEFAULT NULL,
	prod_code varchar(100) DEFAULT NULL,
	scheme_name varchar(300) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`)
);');

IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;


SET @sql_insert = CONCAT('INSERT INTO `mutual_fund_valuation_h_',brokerID,'`(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id FROM mutual_fund_transactions 
WHERE broker_id = "',brokerID,'" 
and purchase_date <= "',reportDate,'" 
AND client_id in (SELECT client_id FROM `clients` where family_id= "',familyID,'")
AND transaction_type = "Purchase" 
order by purchase_date ;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;



CREATE TEMPORARY TABLE `mutual_fund_valuation_temp` (
    valuation_id BIGINT,
    transaction_id BIGINT,
    client_id varchar(50),
    scheme_id int,
    folio_number varchar(50),
    live_unit DECIMAL(30,4) DEFAULT NULL,
	status int default 0
);


INSERT INTO `mutual_fund_valuation_temp`(transaction_id, live_unit, client_id,scheme_id,folio_number) 
SELECT transaction_id, quantity, client_id,mutual_fund_scheme,folio_number FROM mutual_fund_transactions 
WHERE broker_id = brokerID
and purchase_date <= reportDate
AND client_id in (SELECT client_id FROM `clients` where family_id= familyID)
AND transaction_type = "Purchase" 
order by purchase_date ;



while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit<0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit<0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
   

    update mutual_fund_valuation_temp set live_unit=0,status=1 where live_unit=ABS(@live_unit) and client_id=@client_id and scheme_id=@scheme_id
    and folio_number=@folio_number;

    update mutual_fund_valuation_temp set live_unit=0,status=1  where transaction_id=@transaction_id;
    
end while;



while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit>0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);

    set @RedemptionQty=(SELECT SUM(t2.quantity) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	and t2.purchase_date <= reportDate
    AND t2.mutual_fund_scheme = @scheme_id
    AND t2.folio_number = @folio_number
    AND t2.client_id = @client_id);

    if(@RedemptionQty>0)
    then
    BEGIN
            if(@RedemptionQty<=@live_unit)
            then
            BEGIN
                update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=1 where  transaction_id=@transaction_id;
                set @RedemptionQty=@RedemptionQty-@live_unit;
            end;
            else
            BEGIN
            
                while (EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number) and @RedemptionQty>0) do
                
                    set @transaction_id_1=(select transaction_id from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number limit 1);
                    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id_1 limit 1);

                    if(@RedemptionQty<=@live_unit)
                    then
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=1 where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    else
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=0,status=1 where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    end if;
                end while;
            end;
            end if;
    end;
    end if;
    update mutual_fund_valuation_temp set status=1 
    WHERE scheme_id = @scheme_id AND folio_number = @folio_number AND client_id = @client_id;
end while;




SET @sql_update_first ='';
SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` vt
INNER JOIN mutual_fund_valuation_temp temp
ON vt.transaction_id = temp.transaction_id
	 
SET vt.live_unit = temp.live_unit');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;


SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation_h_',brokerID,'`
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;



SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 

SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;




SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 

set v.div_amount = v.p_amount, 
v.p_amount = 0.00 
where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
and v.p_amount > 0;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units_historical_backup` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE, IN `familyID` VARCHAR(30))  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';


SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mutual_fund_valuation_h_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mutual_fund_valuation_h_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
	c_nav DECIMAL(18,4) DEFAULT NULL,
	c_nav_date DATE DEFAULT NULL,
    live_unit DECIMAL(30,4) DEFAULT NULL,
	unit_per_count DECIMAL(30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,4) DEFAULT NULL,
	div_payout DECIMAL(30,4) DEFAULT NULL,
	div_amount DECIMAL(30,2) DEFAULT NULL,
	p_amount DECIMAL(30,2) DEFAULT NULL,
	transaction_day int DEFAULT NULL,
	mf_abs DECIMAL(18,2) DEFAULT NULL,
	mf_cagr DECIMAL(18,2) DEFAULT NULL,
	prod_code varchar(100) DEFAULT NULL,
	scheme_name varchar(300) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');

IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;


SET @sql_insert = CONCAT('INSERT INTO `mutual_fund_valuation_h_',brokerID,'`(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id FROM mutual_fund_transactions 
WHERE broker_id = "',brokerID,'" 
and purchase_date <= "',reportDate,'" 
AND client_id in (SELECT client_id FROM `clients` where family_id= "',familyID,'")
AND transaction_type = "Purchase" 
order by purchase_date ;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;


SET @sql_update_first ='';
SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
inner join (SELECT min(x.transaction_id) as transaction_id, MIN(x.purchase_date) as purchase_date,x.mutual_fund_scheme, x.folio_number, x.client_id,x.broker_id
			FROM 
            (SELECT t.* from mutual_fund_transactions t 
     		INNER JOIN `mutual_fund_valuation_h_',brokerID,'` v 
     		ON v.transaction_id = t.transaction_id
    		ORDER BY t.purchase_date,t.transaction_date,t.transaction_id) x 		
     GROUP BY x.broker_id,x.client_id, x.folio_number,x.mutual_fund_scheme
	 HAVING MIN(x.purchase_date) and min(x.transaction_id)) t10
	on  t10.folio_number = mft.folio_number
	 AND t10.mutual_fund_scheme = mft.mutual_fund_scheme
     AND t10.client_id = mft.client_id
     AND t10.broker_id = mft.broker_id	 
	 AND t10.purchase_date=mft.purchase_date 


                  
	 
SET vt.live_unit = vt.live_unit +
IFNULL((SELECT -(SUM(t2.quantity)) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	and t2.purchase_date <= "',reportDate,'" 
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id    
    AND t2.broker_id = mft.broker_id 
),0) WHERE mft.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
SET @sql_update_all = '';

SET @sql_update_all = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio = folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mutual_fund_valuation_h_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id, a.folio_number, a.mutual_fund_scheme, a.purchase_date,a.ref_no) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance 
WHERE vt1.live_unit != x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt6 FROM @sql_update_all;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;



SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation_h_',brokerID,'`
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;



SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 

SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;




SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 

set v.div_amount = v.p_amount, 
v.p_amount = 0.00 
where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
and v.p_amount > 0;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_live_units_historical_c` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE, IN `clientID` VARCHAR(30))  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert_val = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
SET @sql_update_val = '';
SET @sql_delete_val = '';


SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mutual_fund_valuation_h_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mutual_fund_valuation_h_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
	c_nav DECIMAL(18,4) DEFAULT NULL,
	c_nav_date DATE DEFAULT NULL,
    live_unit DECIMAL(30,4) DEFAULT NULL,
	unit_per_count DECIMAL(30,4) DEFAULT NULL,
	div_r2 DECIMAL(30,4) DEFAULT NULL,
	div_payout DECIMAL(30,4) DEFAULT NULL,
	div_amount DECIMAL(30,2) DEFAULT NULL,
	p_amount DECIMAL(30,2) DEFAULT NULL,
	transaction_day int DEFAULT NULL,
	mf_abs DECIMAL(18,2) DEFAULT NULL,
	mf_cagr DECIMAL(18,2) DEFAULT NULL,
	prod_code varchar(100) DEFAULT NULL,
	scheme_name varchar(300) DEFAULT NULL,
    status int default 0,
    broker_id VARCHAR(10) NOT NULL,
    added_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');

IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;


CREATE TEMPORARY TABLE `mutual_fund_valuation_temp` (
    valuation_id BIGINT,
    transaction_id BIGINT,
    client_id varchar(50),
    scheme_id int,
    folio_number varchar(50),
    live_unit DECIMAL(30,4) DEFAULT NULL,
	status int default 0
);


INSERT INTO `mutual_fund_valuation_temp`(transaction_id, live_unit, client_id,scheme_id,folio_number) 
SELECT transaction_id, quantity, client_id,mutual_fund_scheme,folio_number FROM mutual_fund_transactions 
WHERE broker_id = brokerID
and purchase_date <= reportDate
AND client_id = clientID
AND transaction_type = "Purchase" 
order by purchase_date ;

SET @sql_insert = CONCAT('INSERT INTO `mutual_fund_valuation_h_',brokerID,'`(transaction_id, live_unit, broker_id) 
SELECT transaction_id, quantity, broker_id FROM mutual_fund_transactions 
WHERE broker_id = "',brokerID,'" 
and purchase_date <= "',reportDate,'" 
AND client_id = "',clientID,'"
AND transaction_type = "Purchase" 
order by purchase_date ;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;

while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit<0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit<0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
   

    update mutual_fund_valuation_temp set live_unit=0,status=1 where live_unit=ABS(@live_unit) and client_id=@client_id and scheme_id=@scheme_id
    and folio_number=@folio_number;

    update mutual_fund_valuation_temp set live_unit=0,status=1  where transaction_id=@transaction_id;
    
end while;



while EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0) do

    set @transaction_id=(select transaction_id from mutual_fund_valuation_temp where status=0  and live_unit>0 limit 1);
    set @client_id=(select client_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @scheme_id=(select scheme_id from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @folio_number=(select folio_number from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);
    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id limit 1);

    set @RedemptionQty=(SELECT SUM(t2.quantity) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	and t2.purchase_date <= reportDate
    AND t2.mutual_fund_scheme = @scheme_id
    AND t2.folio_number = @folio_number
    AND t2.client_id = @client_id);

    if(@RedemptionQty>0)
    then
    BEGIN
            if(@RedemptionQty<=@live_unit)
            then
            BEGIN
                update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=1 where  transaction_id=@transaction_id;
                set @RedemptionQty=@RedemptionQty-@live_unit;
            end;
            else
            BEGIN
            
                while (EXISTS(select 1 from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number) and @RedemptionQty>0) do
                
                    set @transaction_id_1=(select transaction_id from mutual_fund_valuation_temp where status=0 and live_unit>0 and client_id=@client_id and scheme_id=@scheme_id
        and folio_number=@folio_number limit 1);
                    set @live_unit=(select live_unit from mutual_fund_valuation_temp where transaction_id=@transaction_id_1 limit 1);

                    if(@RedemptionQty<=@live_unit)
                    then
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=(live_unit-@RedemptionQty),status=@RedemptionQty where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    else
                    BEGIN
                        update mutual_fund_valuation_temp set live_unit=0,status=1 where  transaction_id=@transaction_id_1;
                        set @RedemptionQty=@RedemptionQty-@live_unit;
                    end;
                    end if;
                end while;
            end;
            end if;
    end;
    end if;
    update mutual_fund_valuation_temp set status=1 
    WHERE scheme_id = @scheme_id AND folio_number = @folio_number AND client_id = @client_id and status=0; 
end while;


SET @sql_update_first ='';
SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` vt
INNER JOIN mutual_fund_valuation_temp temp
ON vt.transaction_id = temp.transaction_id
	 
SET vt.live_unit = temp.live_unit');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;



SET @sql_update_all = '';
SET @sql_delete_val = CONCAT('DELETE FROM `mutual_fund_valuation_h_',brokerID,'`
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;



SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 

SET v.p_amount = (v.live_unit * t.nav), 
v.div_amount = 0.00 
WHERE v.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;




SET @sql_update_first = CONCAT('UPDATE `mutual_fund_valuation_h_',brokerID,'` v 
inner join mutual_fund_transactions t 
on v.transaction_id = t.transaction_id 

set v.div_amount = v.p_amount, 
v.p_amount = 0.00 
where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
and v.p_amount > 0;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	 EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_redemption` (IN `schemeID` INT, IN `folioNo` VARCHAR(100), IN `clientID` VARCHAR(100), IN `brokerID` VARCHAR(10), IN `redUnit` DECIMAL(18,2))  NO SQL BEGIN
DECLARE mf_red_done BOOLEAN DEFAULT FALSE;
DECLARE transID bigint(20);
DECLARE unit decimal(18,4);
DECLARE liveUnits decimal(18,4);
DECLARE redAmt decimal(18,4);
if(redUnit > 0) then 
	SET redAmt = -redUnit;
else 
	SET redAmt = redUnit;
end if;
BLOCK1:begin
	declare mf_red cursor for 
    /*select mfv.`transaction_id`, mfv.`live_unit`
    from `mutual_fund_valuation` mfv 
    INNER JOIN `mutual_fund_transactions` mft ON mft.transaction_id = mfv.transaction_id 
	where mft.`mutual_fund_type` IN ('PIP','SWI','DIV','IPO','TIN') 
    and mft.`mutual_fund_scheme` = schemeID 
    and mft.`folio_number` = folioNo 
    and mft.`client_id` = clientID and mfv.`broker_id` = brokerID 
    order by mft.`purchase_date`,mft.`mutual_fund_type`;*/
    select mfv.`transaction_id`, mfv.`live_unit`
    from `mutual_fund_valuation` mfv 
    where mfv.transaction_id IN (select transaction_id from 
                                 `mutual_fund_transactions` 
								 where `mutual_fund_type` IN
                                 ('PIP','SWI','DIV','IPO','TIN') 
                                 and `mutual_fund_scheme` = schemeID 
                                 and `folio_number` = folioNo 
                                 and `client_id` = clientID 
                                 and `broker_id` = brokerID);
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_red_done = TRUE;
		set liveUnits = redAmt;
    	OPEN mf_red; 
        mf_red_loop: LOOP
        fetch from mf_red into transID, unit;
			IF mf_red_done THEN
            set mf_red_done = false;
            CLOSE mf_red;
            LEAVE mf_red_loop;
            END IF; 
			if liveUnits <= 0
			then
				set liveUnits = liveUnits + unit;
			else
				set liveUnits = unit;
            end if;
			UPDATE `mutual_fund_valuation` SET live_unit = liveUnits 
            WHERE `transaction_id` = transID AND `broker_id` = brokerID;
            if liveUnits > 0 
            then
            	set mf_red_done = TRUE;
            end if;
	END LOOP mf_red_loop; 
end BLOCK1;
DELETE from `mutual_fund_valuation` WHERE live_unit <= 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculate_redemption_rev` (IN `schemeID` INT, IN `folioNo` VARCHAR(100), IN `clientID` VARCHAR(100), IN `brokerID` VARCHAR(10), IN `redUnit` DECIMAL(18,2))  NO SQL BEGIN
DECLARE mf_red_done BOOLEAN DEFAULT FALSE;
DECLARE transID bigint(20);
DECLARE unit decimal(18,4);
DECLARE liveUnits decimal(18,4);
DECLARE redAmt decimal(18,4);
if(redUnit < 0) then 
	SET redAmt = -redUnit;
else 
	SET redAmt = redUnit;
end if;
BLOCK1:begin
	declare mf_red cursor for 
    select mfv.`transaction_id`, mfv.`live_unit`
    from `mutual_fund_valuation` mfv 
    INNER JOIN `mutual_fund_transactions` mft ON mft.transaction_id = mfv.transaction_id 
	where mft.`mutual_fund_type` IN ('PIP','SWI','DIV','IPO','TIN') 
    and mft.`mutual_fund_scheme` = schemeID 
    and mft.`folio_number` = folioNo 
    and mft.`client_id` = clientID and mfv.`broker_id` = brokerID 
    and live_unit < 0 
    order by mft.`purchase_date` desc, mft.`mutual_fund_type` desc;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_red_done = TRUE;
		set liveUnits = redAmt;
    	OPEN mf_red; 
        mf_red_loop: LOOP
        fetch from mf_red into transID, unit;
			IF mf_red_done THEN
            set mf_red_done = false;
            CLOSE mf_red;
            LEAVE mf_red_loop;
            END IF; 
			if liveUnits >= 0
			then
				set liveUnits = liveUnits + unit;
			else
				set liveUnits = unit;
            end if;
			UPDATE `mutual_fund_valuation` SET live_unit = liveUnits 
            WHERE `transaction_id` = transID AND `broker_id` = brokerID;
            if liveUnits <= 0 
            then
            	set mf_red_done = TRUE;
            end if;
	END LOOP mf_red_loop; 
end BLOCK1;
#DELETE from `mutual_fund_valuation` WHERE live_unit <= 0;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_calculation_historical` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN 
/*      Div Payout   */
SET @qry1 = CONCAT('update temp_mf_transaction t   
           set t.DPO_units = t.amount/NULLIF((
            (select case when sum(t1.quantity) is null then 0 
             else sum(t1.quantity) end from (select * from temp_mf_transaction) as t1 
             where (t1.purchase_date < t.purchase_date) 
             and t1.client_id = t.client_id 
             and t1.mutual_fund_scheme = t.mutual_fund_scheme 
             and t1.folio_number = t.folio_number 
             and t1.transaction_type = "Purchase" and t1.broker_id = t.broker_id) - 
            (select case when sum(t2.quantity) is null then 0 
             else sum(t2.quantity) end from (select * from temp_mf_transaction) as t2 
             where (t2.purchase_date < t.purchase_date) 
             and t2.client_id = t.client_id 
             and t2.mutual_fund_scheme = t.mutual_fund_scheme 
             and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
             and t2.mutual_fund_type IN("SWO","RED"))
        ),0)
        where t.mutual_fund_type = "DP" and t.broker_id = "',brokerID,'" ;');
IF(@qry1 != '') THEN
    PREPARE stmt1 FROM @qry1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
  --                    add index mutual_fund_scheme (mutual_fund_scheme), 
set @div_pp=CONCAT('drop temporary table if exists tempvalp;');
IF(@div_pp != '') THEN
    PREPARE stmt1 FROM @div_pp;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_p=CONCAT('create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
                                    t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
                                    from temp_mf_transaction t where broker_id = "',brokerID,'" );');
IF(@div_p != '') THEN
    PREPARE stmt1 FROM @div_p;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
/*set @div_pp_1=CONCAT('DROP TEMPORARY TABLE IF EXISTS mf_val_temp;');
IF(@div_pp_1 != '') THEN
    PREPARE stmt1 FROM @div_pp_1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_p_1=CONCAT('CREATE TEMPORARY TABLE mf_val_temp AS 
                        SELECT valuation_id, transaction_id, live_unit, div_payout,  broker_id 
                        FROM mf_val_valuation_temp_',brokerID,' 
                        WHERE broker_id = "',brokerID,'" 
                        ORDER BY valuation_id
                    ;');   
if(@div_p_1!='') then
 PREPARE stmt1 from @div_p_1;
 EXECUTE stmt1;
 DEALLOCATE PREPARE stmt1;
 end if; 
*/
     set @div_p2=CONCAT('update mf_val_valuation_temp_h_',brokerID,' v 
                        inner join temp_mf_transaction t 
                        on v.transaction_id = t.transaction_id 
                        set v.div_payout = v.live_unit * (
                            (select sum(DPO_units) from tempvalp mft 
                             where mft.client_id = t.client_id 
                             and mft.mutual_fund_scheme = t.mutual_fund_scheme 
                             and mft.folio_number = t.folio_number 
                             and mft.broker_id = v.broker_id 
                             and mft.purchase_date >= t.purchase_date 
                             and mft.transaction_id != v.transaction_id)
                        ) 
                        where v.broker_id = "',brokerID,'"; ');
  if(@div_p2!='')then
    PREPARE stmt1  from @div_p2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if;
 /* set @div_p_2=CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                        INNER JOIN mf_val_temp vt 
                        ON v.transaction_id = vt.transaction_id 
                        SET v.div_payout = vt.div_payout 
                        WHERE vt.div_payout != v.div_payout;
                     ');
   if(@div_p_2!='')then
    PREPARE stmt1  from @div_p_2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if;
*/
SET @qry2 = CONCAT('update mf_val_valuation_temp_h_',brokerID,' v 
                  inner join temp_mf_transaction t 
                  on v.transaction_id = t.transaction_id 
                  set v.unit_per_count = t.amount/NULLIF((
                    (select case when sum(quantity) is null then 0 
                       else sum(quantity) end from temp_mf_transaction 
                       where (purchase_date < t.purchase_date) 
                       and client_id = t.client_id 
                       and mutual_fund_scheme = t.mutual_fund_scheme 
                       and folio_number = t.folio_number 
                       and transaction_type = "Purchase" and broker_id = t.broker_id) - 
                    (select case when sum(quantity) is null then 0 
                       else sum(quantity) end from temp_mf_transaction 
                       where (purchase_date < t.purchase_date) 
                       and client_id = t.client_id 
                       and mutual_fund_scheme = t.mutual_fund_scheme 
                       and folio_number = t.folio_number and broker_id = t.broker_id 
                       and mutual_fund_type IN ("SWO","RED"))
                  ),0)
        where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'"
    ;');
IF(@qry2 != '') THEN
    PREPARE stmt1 FROM @qry2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payoutt_1=CONCAT('drop temporary table if exists tempval;');
IF(@div_payoutt_1 != '') THEN
    PREPARE stmt1 FROM @div_payoutt_1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payout_1=CONCAT('create temporary table tempval as
                         (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
                         	t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
                         	from mf_val_valuation_temp_h_',brokerID,' v 
                         	inner join temp_mf_transaction t on v.transaction_id = t.transaction_id 
                          where v.broker_id = "',brokerID,'" 
                         );
                       ');
if(@div_payout_1!='')then
PREPARE stmt1 from @div_payout_1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end if;
 alter table tempval add primary key (valuation_id),
                            add index transaction_id (transaction_id), 
                            add index mutual_fund_scheme (mutual_fund_scheme), 
                            add index folio_number (folio_number), 
                            add index purchase_date (purchase_date), 
                            add index client_id (client_id), 
                            add index broker_id (broker_id);
/*set @div_payoutt_2=CONCAT('DROP TEMPORARY TABLE IF EXISTS mf_val_temp;');
IF(@div_payoutt_2 != '') THEN
    PREPARE stmt1 FROM @div_payoutt_2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payout_2= CONCAT('CREATE TEMPORARY TABLE mf_val_temp AS (
                              SELECT valuation_id, transaction_id, live_unit, unit_per_count, div_r2, broker_id 
                              FROM mutual_fund_valuation 
                              WHERE broker_id = "',brokerID,'" 
                              ORDER BY valuation_id
                              );
                         ');
 if(@div_payout_2!='') then
 PREPARE stmt1 from @div_payout_2;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
*/
set @div_payout_3=CONCAT('update mf_val_valuation_temp_h_',brokerID,' v 
                          inner join temp_mf_transaction t 
                          on v.transaction_id = t.transaction_id 
                          set v.div_r2 = v.live_unit * (
                              (select sum(mfv.unit_per_count) from tempval mfv 
                               where mfv.client_id = t.client_id 
                               and mfv.mutual_fund_scheme = t.mutual_fund_scheme 
                               and mfv.folio_number = t.folio_number 
                               and mfv.broker_id = v.broker_id 
                               and mfv.purchase_date >= t.purchase_date 
                               and mfv.transaction_id != v.transaction_id
                               group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
                          ) 
                          where v.broker_id = "',brokerID,'";
                      ');
if(@div_payout_3!='') then
 PREPARE stmt1 from @div_payout_3;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
/*set @div_payout_4=CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                              INNER JOIN mf_val_temp vt 
                              ON v.transaction_id = vt.transaction_id 
                              SET v.div_r2 = vt.div_r2 
                              WHERE v.broker_id = "',brokerID,'" ;
                         ');
if(@div_payout_4!='')then
 PREPARE stmt1 from @div_payout_4;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
*/
/*  Update divAmt */
 set @update_divAmt=CONCAT('update mf_val_valuation_temp_h_',brokerID,' v 
                          INNER JOIN mutual_fund_transactions t 
                          ON v.transaction_id = t.transaction_id 
                          SET v.p_amount = (v.live_unit * t.nav), 
                          v.div_amount = "0.00" 
                          WHERE v.broker_id = "',brokerID,'" 
                          and v.transaction_id = t.transaction_id;  
                       ');
    if(@update_divAmt!='') then
    PREPARE stmt1 from @update_divAmt;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if; 
 set @update_divAmt1=CONCAT('update mf_val_valuation_temp_h_',brokerID,' v 
                            inner join mutual_fund_transactions t 
                            on v.transaction_id = t.transaction_id 
                            set v.div_amount = v.p_amount, v.p_amount = "0.00" 
                            where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
                            and v.p_amount > 0;
                       ');
    if(@update_divAmt1!='') then
    PREPARE stmt1 from @update_divAmt1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if; 
/*  Update current nav */
SET @update_c_nav1 = CONCAT(' UPDATE mf_val_valuation_temp_h_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t 
                              ON v.transaction_id = t.transaction_id 
                              SET v.c_nav = (SELECT current_nav 
                                                    FROM mf_schemes_histories 
                                                    WHERE scheme_id = t.mutual_fund_scheme 
                                                    ORDER BY scheme_date DESC LIMIT 1), 
                                  v.c_nav_date = (SELECT MAX(scheme_date) 
                                                    FROM mf_schemes_histories 
                                                    WHERE scheme_id = t.mutual_fund_scheme 
                                                    GROUP BY scheme_id) 
                              WHERE v.broker_id = "',brokerID,'" 
                          ;');  
if(@update_c_nav1!='')then
 PREPARE stmt1 from @update_c_nav1;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
SET @update_c_nav2 = CONCAT('UPDATE mf_val_valuation_temp_h_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t on t.transaction_id=v.transaction_id                           
                              inner join    temp_mf_transaction mft on mft.transaction_id=v.red_trans_id                    
                              SET v.transaction_day = DATEDIFF(mft.purchase_date, t.purchase_date), 
                              v.mf_abs = ((((v.live_unit * mft.nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
                              WHERE v.broker_id = "',brokerID,'"
                          ;');
if(@update_c_nav2!='')then
 PREPARE stmt1 from @update_c_nav2;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
SET @update_c_nav3 = CONCAT('UPDATE mf_val_valuation_temp_h_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t 
                              ON v.transaction_id = t.transaction_id 
                              SET v.mf_cagr = (CASE WHEN v.transaction_day > 365 THEN 
                                      (power(
                                              (abs(IFNULL((v.mf_abs+100),0)/100)), 
                                              abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
                                          ELSE 
                                          ((v.mf_abs)/v.transaction_day)*365 
                                          END) 
                              WHERE v.broker_id = "',brokerID,'"
                           ;');
if(@update_c_nav3!='')then
 PREPARE stmt1 from @update_c_nav3;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_capitalgain_calculation` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN 
/*      Div Payout   */
SET @qry1 = CONCAT('update temp_mf_transaction t   
           set t.DPO_units = t.amount/NULLIF((
            (select case when sum(t1.quantity) is null then 0 
             else sum(t1.quantity) end from (select * from temp_mf_transaction) as t1 
             where (t1.purchase_date < t.purchase_date) 
             and t1.client_id = t.client_id 
             and t1.mutual_fund_scheme = t.mutual_fund_scheme 
             and t1.folio_number = t.folio_number 
             and t1.transaction_type = "Purchase" and t1.broker_id = t.broker_id) - 
            (select case when sum(t2.quantity) is null then 0 
             else sum(t2.quantity) end from (select * from temp_mf_transaction) as t2 
             where (t2.purchase_date < t.purchase_date) 
             and t2.client_id = t.client_id 
             and t2.mutual_fund_scheme = t.mutual_fund_scheme 
             and t2.folio_number = t.folio_number and t2.broker_id = t.broker_id 
             and t2.mutual_fund_type IN("SWO","RED"))
        ),0)
        where t.mutual_fund_type = "DP" and t.broker_id = "',brokerID,'" ;');
IF(@qry1 != '') THEN
    PREPARE stmt1 FROM @qry1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
  --                    add index mutual_fund_scheme (mutual_fund_scheme), 
set @div_pp=CONCAT('drop temporary table if exists tempvalp;');
IF(@div_pp != '') THEN
    PREPARE stmt1 FROM @div_pp;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_p=CONCAT('create temporary table tempvalp as (select t.transaction_id, t.DPO_units, t.broker_id, 
                                    t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
                                    from temp_mf_transaction t where broker_id = "',brokerID,'" );');
IF(@div_p != '') THEN
    PREPARE stmt1 FROM @div_p;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
/*set @div_pp_1=CONCAT('DROP TEMPORARY TABLE IF EXISTS mf_val_temp;');
IF(@div_pp_1 != '') THEN
    PREPARE stmt1 FROM @div_pp_1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_p_1=CONCAT('CREATE TEMPORARY TABLE mf_val_temp AS 
                        SELECT valuation_id, transaction_id, live_unit, div_payout,  broker_id 
                        FROM mf_val_valuation_temp_',brokerID,' 
                        WHERE broker_id = "',brokerID,'" 
                        ORDER BY valuation_id
                    ;');   
if(@div_p_1!='') then
 PREPARE stmt1 from @div_p_1;
 EXECUTE stmt1;
 DEALLOCATE PREPARE stmt1;
 end if; 
*/
     set @div_p2=CONCAT('update mf_val_valuation_temp_',brokerID,' v 
                        inner join temp_mf_transaction t 
                        on v.transaction_id = t.transaction_id 
                        set v.div_payout = v.live_unit * (
                            (select sum(DPO_units) from tempvalp mft 
                             where mft.client_id = t.client_id 
                             and mft.mutual_fund_scheme = t.mutual_fund_scheme 
                             and mft.folio_number = t.folio_number 
                             and mft.broker_id = v.broker_id 
                             and mft.purchase_date >= t.purchase_date 
                             and mft.transaction_id != v.transaction_id)
                        ) 
                        where v.broker_id = "',brokerID,'"; ');
  if(@div_p2!='')then
    PREPARE stmt1  from @div_p2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if;
 /* set @div_p_2=CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                        INNER JOIN mf_val_temp vt 
                        ON v.transaction_id = vt.transaction_id 
                        SET v.div_payout = vt.div_payout 
                        WHERE vt.div_payout != v.div_payout;
                     ');
   if(@div_p_2!='')then
    PREPARE stmt1  from @div_p_2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if;
*/
SET @qry2 = CONCAT('update mf_val_valuation_temp_',brokerID,' v 
                  inner join temp_mf_transaction t 
                  on v.transaction_id = t.transaction_id 
                  set v.unit_per_count = t.amount/NULLIF((
                    (select case when sum(quantity) is null then 0 
                       else sum(quantity) end from temp_mf_transaction 
                       where (purchase_date < t.purchase_date) 
                       and client_id = t.client_id 
                       and mutual_fund_scheme = t.mutual_fund_scheme 
                       and folio_number = t.folio_number 
                       and transaction_type = "Purchase" and broker_id = t.broker_id) - 
                    (select case when sum(quantity) is null then 0 
                       else sum(quantity) end from temp_mf_transaction 
                       where (purchase_date < t.purchase_date) 
                       and client_id = t.client_id 
                       and mutual_fund_scheme = t.mutual_fund_scheme 
                       and folio_number = t.folio_number and broker_id = t.broker_id 
                       and mutual_fund_type IN ("SWO","RED"))
                  ),0)
        where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'"
    ;');
IF(@qry2 != '') THEN
    PREPARE stmt1 FROM @qry2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payoutt_1=CONCAT('drop temporary table if exists tempval;');
IF(@div_payoutt_1 != '') THEN
    PREPARE stmt1 FROM @div_payoutt_1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payout_1=CONCAT('create temporary table tempval as
                         (select v.valuation_id, v.transaction_id, v.unit_per_count, v.div_r2, v.broker_id, 
                         	t.mutual_fund_scheme, t.folio_number, t.client_id, t.purchase_date 
                         	from mf_val_valuation_temp_',brokerID,' v 
                         	inner join temp_mf_transaction t on v.transaction_id = t.transaction_id 
                          where v.broker_id = "',brokerID,'" 
                         );
                       ');
if(@div_payout_1!='')then
PREPARE stmt1 from @div_payout_1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end if;
 alter table tempval add primary key (valuation_id),
                            add index transaction_id (transaction_id), 
                            add index mutual_fund_scheme (mutual_fund_scheme), 
                            add index folio_number (folio_number), 
                            add index purchase_date (purchase_date), 
                            add index client_id (client_id), 
                            add index broker_id (broker_id);
/*set @div_payoutt_2=CONCAT('DROP TEMPORARY TABLE IF EXISTS mf_val_temp;');
IF(@div_payoutt_2 != '') THEN
    PREPARE stmt1 FROM @div_payoutt_2;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
END IF;
set @div_payout_2= CONCAT('CREATE TEMPORARY TABLE mf_val_temp AS (
                              SELECT valuation_id, transaction_id, live_unit, unit_per_count, div_r2, broker_id 
                              FROM mutual_fund_valuation 
                              WHERE broker_id = "',brokerID,'" 
                              ORDER BY valuation_id
                              );
                         ');
 if(@div_payout_2!='') then
 PREPARE stmt1 from @div_payout_2;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
*/
set @div_payout_3=CONCAT('update mf_val_valuation_temp_',brokerID,' v 
                          inner join temp_mf_transaction t 
                          on v.transaction_id = t.transaction_id 
                          set v.div_r2 = v.live_unit * (
                              (select sum(mfv.unit_per_count) from tempval mfv 
                               where mfv.client_id = t.client_id 
                               and mfv.mutual_fund_scheme = t.mutual_fund_scheme 
                               and mfv.folio_number = t.folio_number 
                               and mfv.broker_id = v.broker_id 
                               and mfv.purchase_date >= t.purchase_date 
                               and mfv.transaction_id != v.transaction_id
                               group by mfv.mutual_fund_scheme,mfv.folio_number,mfv.broker_id)
                          ) 
                          where v.broker_id = "',brokerID,'";
                      ');
if(@div_payout_3!='') then
 PREPARE stmt1 from @div_payout_3;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
/*set @div_payout_4=CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                              INNER JOIN mf_val_temp vt 
                              ON v.transaction_id = vt.transaction_id 
                              SET v.div_r2 = vt.div_r2 
                              WHERE v.broker_id = "',brokerID,'" ;
                         ');
if(@div_payout_4!='')then
 PREPARE stmt1 from @div_payout_4;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
*/
/*  Update divAmt */
 set @update_divAmt=CONCAT('update mf_val_valuation_temp_',brokerID,' v 
                          INNER JOIN mutual_fund_transactions t 
                          ON v.transaction_id = t.transaction_id 
                          SET v.p_amount = (v.live_unit * t.nav), 
                          v.div_amount = "0.00" 
                          WHERE v.broker_id = "',brokerID,'" 
                          and v.transaction_id = t.transaction_id;  
                       ');
    if(@update_divAmt!='') then
    PREPARE stmt1 from @update_divAmt;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if; 
 set @update_divAmt1=CONCAT('update mf_val_valuation_temp_',brokerID,' v 
                            inner join mutual_fund_transactions t 
                            on v.transaction_id = t.transaction_id 
                            set v.div_amount = v.p_amount, v.p_amount = "0.00" 
                            where t.mutual_fund_type = "DIV" and v.broker_id = "',brokerID,'" 
                            and v.p_amount > 0;
                       ');
    if(@update_divAmt1!='') then
    PREPARE stmt1 from @update_divAmt1;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
    end if; 
/*  Update current nav */
SET @update_c_nav1 = CONCAT(' UPDATE mf_val_valuation_temp_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t 
                              ON v.transaction_id = t.transaction_id 
                              SET v.c_nav = (SELECT current_nav 
                                                    FROM mf_schemes_histories 
                                                    WHERE scheme_id = t.mutual_fund_scheme 
                                                    ORDER BY scheme_date DESC LIMIT 1), 
                                  v.c_nav_date = (SELECT MAX(scheme_date) 
                                                    FROM mf_schemes_histories 
                                                    WHERE scheme_id = t.mutual_fund_scheme 
                                                    GROUP BY scheme_id) 
                              WHERE v.broker_id = "',brokerID,'" 
                          ;');  
if(@update_c_nav1!='')then
 PREPARE stmt1 from @update_c_nav1;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
SET @update_c_nav2 = CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t on t.transaction_id=v.transaction_id                           
                              inner join    temp_mf_transaction mft on mft.transaction_id=v.red_trans_id                    
                              SET v.transaction_day = DATEDIFF(mft.purchase_date, t.purchase_date), 
                              v.mf_abs = ((((v.live_unit * mft.nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
                              WHERE v.broker_id = "',brokerID,'"
                          ;');
if(@update_c_nav2!='')then
 PREPARE stmt1 from @update_c_nav2;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
SET @update_c_nav3 = CONCAT('UPDATE mf_val_valuation_temp_',brokerID,' v 
                              INNER JOIN temp_mf_transaction t 
                              ON v.transaction_id = t.transaction_id 
                              SET v.mf_cagr = (CASE WHEN v.transaction_day > 365 THEN 
                                      (power(
                                              (abs(IFNULL((v.mf_abs+100),0)/100)), 
                                              abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
                                          ELSE 
                                          ((v.mf_abs)/v.transaction_day)*365 
                                          END) 
                              WHERE v.broker_id = "',brokerID,'"
                           ;');
if(@update_c_nav3!='')then
 PREPARE stmt1 from @update_c_nav3;
 EXECUTE stmt1; 
 DEALLOCATE PREPARE stmt1;
 end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_capitalgain_report` (IN `brokerID` VARCHAR(10), IN `familyID` VARCHAR(20), IN `clientID` VARCHAR(20), IN `startDate` DATE, IN `endDate` DATE)  NO SQL begin
  DECLARE done INT DEFAULT FALSE;
  DECLARE done2 INT DEFAULT FALSE;
  DECLARE red_trans bigint;
  DECLARE folio varchar(100);
  DECLARE client varchar(30);
  Declare scheme int;
  Declare red_units decimal(18,4);
  Declare temp_units decimal(18,4);
  Declare trans_id bigint;
  Declare units decimal(18,4);
  Declare query_tbl varchar(500);
  Declare rt_id bigint;
  Declare qty decimal(18,4);
  Declare broker_id VARCHAR(10) ;
  Declare scheme2 int;
  Declare folio2 varchar(100);
  Declare client2 varchar(30);                                       
SET @sql_select='';
SET @temp_qry='';
SET @valuation_select='';
IF(familyID!="") then
      DROP TABLE IF EXISTS temp_mf_transaction;
     create table if not exists temp_mf_transaction as
     (
        select          
    mft.DPO_units,
        f.name as name ,
        c.name as client_name,
        mft.client_id as client_id, 
        mft.transaction_id,
        mft.folio_number as folio_number,
        mft.transaction_date as transaction_date,
        mft.purchase_date as purchase_date,
        mft.quantity,
        mft.nav,
        mft.amount,
        mft.transaction_type,
        mft.mutual_fund_type as mutual_fund_type,
        mft.mutual_fund_scheme ,
        mft.broker_id
        from mutual_fund_transactions mft
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        where mft.broker_id =brokerID
            and f.family_id =familyID
            and c.status=1
            and mft.purchase_date  between  startDate and endDate 
        ORDER BY mft.purchase_date); 
     call sp_mf_capitalgain_valuation(brokerID,familyID,clientID,startDate);
else
    DROP TABLE IF EXISTS temp_mf_transaction;
    create table if not exists temp_mf_transaction as
    (
        select         
        mft.DPO_units,
        c.name as name,
        c.name as client_name,
        mft.client_id as client_id, 
        mft.transaction_id,
        mft.folio_number as folio_number,
        mft.transaction_date as transaction_date,
        mft.purchase_date as purchase_date,
        mft.quantity,
        mft.nav,
        mft.amount,
        mft.transaction_type,
        mft.mutual_fund_type as mutual_fund_type,
        mft.mutual_fund_scheme ,
        mft.broker_id
        from mutual_fund_transactions mft
        inner join clients c on mft.client_id = c.client_id
        where mft.broker_id = brokerID
        and c.client_id =clientID
        and c.status=1
        and mft.purchase_date  between  startDate and endDate
        ORDER BY mft.purchase_date); 
    call sp_mf_capitalgain_valuation(brokerID,familyID,clientID,startDate);
end if;
drop temporary table if exists temp_val_cg;
set @create_temp_val=CONCAT('CREATE TEMPORARY TABLE temp_val_cg LIKE `mf_val_valuation_temp_',brokerID,'`;');
if(@create_temp_val!='') then
	PREPARE stmt1 from @create_temp_val;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if; 
set @insert_temp_val=CONCAT('INSERT INTO temp_val_cg (select * from `mf_val_valuation_temp_',brokerID,'`);');
if(@insert_temp_val!='') then
	PREPARE stmt1 from @insert_temp_val;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if; 
BLOCK1: begin
  DECLARE mf_capital_gain CURSOR FOR SELECT transaction_id, mutual_fund_scheme, folio_number, client_id, quantity FROM temp_mf_transaction where mutual_fund_type in('RED','SWO') ORDER BY client_id, mutual_fund_scheme, folio_number, purchase_date, transaction_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN mf_capital_gain;
  read_loop: LOOP
    FETCH mf_capital_gain INTO red_trans,scheme,folio,client,red_units;
        IF done THEN
      CLOSE mf_capital_gain;
            LEAVE read_loop;
        END IF;
        -- select red_units;
    SET temp_units = -(red_units);
  	SET done2 = false;
          BLOCK2: begin
          DECLARE mf_valuation CURSOR FOR select transaction_id, live_unit, red_trans_id, quantity, mutual_fund_scheme, folio_number, client_id FROM temp_val_cg where mutual_fund_scheme=scheme and folio_number=folio and client_id=client and quantity > 0.03 AND red_trans_id IS NULL order by transaction_id; 
          DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
          OPEN mf_valuation;
          read_loop2: LOOP
            FETCH mf_valuation INTO trans_id, units, rt_id, qty, scheme2, folio2, client2;
                IF done2 THEN
                    CLOSE mf_valuation;
                    LEAVE read_loop2;
                END IF;
                -- select 'inside2';
            update temp_val_cg set quantity=(temp_units+units), red_trans_id=red_trans where transaction_id=trans_id order by valuation_id desc limit 1; 
            set @update_val=CONCAT('update `mf_val_valuation_temp_',brokerID,'` set quantity=',(temp_units+units),', red_trans_id=',red_trans,'  where transaction_id=',trans_id,' order by valuation_id desc limit 1;');
            if(@update_val!='') then
            PREPARE stmt1 from @update_val;
            EXECUTE stmt1;
            DEALLOCATE PREPARE stmt1;
            end if; 
          SET temp_units=temp_units+units;
          if temp_units > 0 THEN 
				update temp_val_cg set live_unit=(live_unit - temp_units) where transaction_id=trans_id order by valuation_id desc limit 1; 
            set @update_val=CONCAT('update `mf_val_valuation_temp_',brokerID,'` set live_unit=(live_unit - ',temp_units,') where transaction_id=',trans_id,' order by valuation_id desc limit 1;');
            if(@update_val!='') then
            PREPARE stmt1 from @update_val;
            EXECUTE stmt1;
            DEALLOCATE PREPARE stmt1;
            end if; 
                insert into temp_val_cg(transaction_id,live_unit,quantity,red_trans_id,broker_id,mutual_fund_scheme,folio_number,client_id)
                values(trans_id,temp_units,temp_units,null,brokerID,scheme2,folio2,client2);
                set @insert_val_temp=CONCAT('insert into  mf_val_valuation_temp_',brokerID,'(transaction_id,live_unit,quantity,red_trans_id,broker_id,mutual_fund_scheme,folio_number,client_id) 
                values(',trans_id,',',temp_units,',',temp_units,',null,"',brokerID,'",',scheme2,',"',folio2,'","',client2,'");');
                  if(@insert_val_temp!='') then
                      PREPARE stmt1 from @insert_val_temp;
                      EXECUTE stmt1;
                      DEALLOCATE PREPARE stmt1;
                  end if; 
              end if;
         if temp_units >=0 then
          set done2=true;
               end if;
         END LOOP;
        end BLOCK2;
   END LOOP;
end BLOCK1;
set @delete_0 = CONCAT('delete from mf_val_valuation_temp_',brokerID, ' where live_unit <= 0.03;');
if(@delete_0 != '') then
	PREPARE stmt0 from @delete_0;
	EXECUTE stmt0;
	DEALLOCATE PREPARE stmt0;
end if; 
delete from temp_val_cg where live_unit < 0.05;
call sp_mf_capitalgain_calculation(brokerID);
select'hellooooo';
set @sql_select = concat('select 
                      tmft.name,
                      tmft.client_name as client_name,  
                      tmft.folio_number,
                      mfs.scheme_name,
                      mst.scheme_type as scheme_type,
                      Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
                      Date_format(tmft.purchase_date, "%d/%m/%Y") as sale_date,
                      tmft.mutual_fund_type,
                      mft.nav as p_nav,
                      tmft.nav as sale_nav,
                      tv.div_amount as div_amount,
                      tv.live_unit as units,
                      tv.p_amount as purchase_amount,
                     (tmft.nav * tv.live_unit) as sale_amount,
                      tv.unit_per_count,
                      mft.DPO_units,
                      tv.transaction_day,
                     ((tmft.nav * tv.live_unit)-(tv.p_amount+tv.div_amount)) as gain,
                      tv.div_r2 as div_r2,
                      tv.div_payout as payout,
                      tv.mf_cagr,
                      tv.mf_abs                                   
                      from temp_mf_transaction tmft
                      inner join mf_val_valuation_temp_',brokerID,' tv on  tmft.transaction_id=tv.red_trans_id
                      inner join mutual_fund_schemes mfs on  mfs.scheme_id= tmft.mutual_fund_scheme
                      inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
                      inner join temp_mf_transaction mft on mft.transaction_id = tv.transaction_id
                      order by mfs.scheme_name, mft.purchase_date, tmft.purchase_date;');
     IF(@sql_select != '') THEN
        PREPARE stmt10 FROM @sql_select;
        EXECUTE stmt10;
        DEALLOCATE PREPARE stmt10;
      END IF;
/*set @delete_qry = concat('drop table `mf_val_valuation_temp_',brokerID,'`;');
   IF(@delete_qry != '') THEN
        PREPARE stmt0 FROM @delete_qry;
        EXECUTE stmt0;
        DEALLOCATE PREPARE stmt0;
      END IF;
set @delete_qry1 = concat('drop temporary if exists table temp_val_cg;');
   IF(@delete_qry1 != '') THEN
        PREPARE stmt1 FROM @delete_qry;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;
      END IF;*/
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_capitalgain_report_new` (IN `brokerID` VARCHAR(10), IN `familyID` VARCHAR(20), IN `clientID` VARCHAR(20), IN `startDate` DATE, IN `endDate` DATE)  NO SQL begin
  DECLARE done INT DEFAULT FALSE;
  DECLARE done2 INT DEFAULT FALSE;
  DECLARE red_trans bigint;
  DECLARE folio varchar(100);
  DECLARE client varchar(30);
  Declare scheme int;
  Declare red_units decimal(18,4);
  Declare temp_units decimal(18,4);
  Declare trans_id bigint;
  Declare units decimal(18,4);
  Declare query_tbl varchar(500);
  Declare rt_id bigint;
  Declare qty decimal(18,4);
  Declare broker_id VARCHAR(10) ;
  Declare scheme2 int;
  Declare folio2 varchar(100);
  Declare client2 varchar(30);                                       
SET @sql_select='';
SET @temp_qry='';
SET @valuation_select='';
IF(familyID!="") then
      DROP TABLE IF EXISTS temp_mf_transaction;
     create table if not exists temp_mf_transaction as
     (
        select          
    mft.DPO_units,
        f.name as name ,
        c.name as client_name,
        mft.client_id as client_id, 
        mft.transaction_id,
        mft.folio_number as folio_number,
        mft.transaction_date as transaction_date,
        mft.purchase_date as purchase_date,
        mft.quantity,
        mft.nav,
        mft.amount,
        mft.transaction_type,
        mft.mutual_fund_type as mutual_fund_type,
        mft.mutual_fund_scheme ,
        mft.broker_id
        from mutual_fund_transactions mft
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        where mft.broker_id =brokerID
            and f.family_id =familyID
           and (case when clientID='' then 1
        when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
        else 0 end)=1
            and c.status=1
            and mft.purchase_date  between  startDate and endDate 
        ORDER BY mft.purchase_date); 
     call sp_mf_capitalgain_valuation(brokerID,familyID,clientID,startDate);
else
    DROP TABLE IF EXISTS temp_mf_transaction;
    create table if not exists temp_mf_transaction as
    (
        select         
        mft.DPO_units,
        c.name as name,
        c.name as client_name,
        mft.client_id as client_id, 
        mft.transaction_id,
        mft.folio_number as folio_number,
        mft.transaction_date as transaction_date,
        mft.purchase_date as purchase_date,
        mft.quantity,
        mft.nav,
        mft.amount,
        mft.transaction_type,
        mft.mutual_fund_type as mutual_fund_type,
        mft.mutual_fund_scheme ,
        mft.broker_id
        from mutual_fund_transactions mft
        inner join clients c on mft.client_id = c.client_id
        where mft.broker_id = brokerID
        and c.client_id =clientID
        and c.status=1
        and mft.purchase_date  between  startDate and endDate
        ORDER BY mft.purchase_date); 
    call sp_mf_capitalgain_valuation(brokerID,familyID,clientID,startDate);
end if;
drop temporary table if exists temp_val_cg;
set @create_temp_val=CONCAT('CREATE TEMPORARY TABLE temp_val_cg LIKE `mf_val_valuation_temp_',brokerID,'`;');
if(@create_temp_val!='') then
	PREPARE stmt1 from @create_temp_val;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if; 
set @insert_temp_val=CONCAT('INSERT INTO temp_val_cg (select * from `mf_val_valuation_temp_',brokerID,'`);');
if(@insert_temp_val!='') then
	PREPARE stmt1 from @insert_temp_val;
    EXECUTE stmt1;
    DEALLOCATE PREPARE stmt1;
end if; 
BLOCK1: begin
  DECLARE mf_capital_gain CURSOR FOR SELECT transaction_id, mutual_fund_scheme, folio_number, client_id, quantity FROM temp_mf_transaction where mutual_fund_type in('RED','SWO') ORDER BY client_id, mutual_fund_scheme, folio_number, purchase_date, transaction_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN mf_capital_gain;
  read_loop: LOOP
    FETCH mf_capital_gain INTO red_trans,scheme,folio,client,red_units;
        IF done THEN
      CLOSE mf_capital_gain;
            LEAVE read_loop;
        END IF;
        -- select red_units;
    SET temp_units = -(red_units);
  	SET done2 = false;
          BLOCK2: begin
          DECLARE mf_valuation CURSOR FOR select transaction_id, live_unit, red_trans_id, quantity, mutual_fund_scheme, folio_number, client_id FROM temp_val_cg where mutual_fund_scheme=scheme and folio_number=folio and client_id=client and quantity > 0.03 AND red_trans_id IS NULL order by transaction_id; 
          DECLARE CONTINUE HANDLER FOR NOT FOUND SET done2 = TRUE;
          OPEN mf_valuation;
          read_loop2: LOOP
            FETCH mf_valuation INTO trans_id, units, rt_id, qty, scheme2, folio2, client2;
                IF done2 THEN
                    CLOSE mf_valuation;
                    LEAVE read_loop2;
                END IF;
                -- select 'inside2';
            update temp_val_cg set quantity=(temp_units+units), red_trans_id=red_trans where transaction_id=trans_id order by valuation_id desc limit 1; 
            set @update_val=CONCAT('update `mf_val_valuation_temp_',brokerID,'` set quantity=',(temp_units+units),', red_trans_id=',red_trans,'  where transaction_id=',trans_id,' order by valuation_id desc limit 1;');
            if(@update_val!='') then
            PREPARE stmt1 from @update_val;
            EXECUTE stmt1;
            DEALLOCATE PREPARE stmt1;
            end if; 
          SET temp_units=temp_units+units;
          if temp_units > 0 THEN 
				update temp_val_cg set live_unit=(live_unit - temp_units) where transaction_id=trans_id order by valuation_id desc limit 1; 
            set @update_val=CONCAT('update `mf_val_valuation_temp_',brokerID,'` set live_unit=(live_unit - ',temp_units,') where transaction_id=',trans_id,' order by valuation_id desc limit 1;');
            if(@update_val!='') then
            PREPARE stmt1 from @update_val;
            EXECUTE stmt1;
            DEALLOCATE PREPARE stmt1;
            end if; 
                insert into temp_val_cg(transaction_id,live_unit,quantity,red_trans_id,broker_id,mutual_fund_scheme,folio_number,client_id)
                values(trans_id,temp_units,temp_units,null,brokerID,scheme2,folio2,client2);
                set @insert_val_temp=CONCAT('insert into  mf_val_valuation_temp_',brokerID,'(transaction_id,live_unit,quantity,red_trans_id,broker_id,mutual_fund_scheme,folio_number,client_id) 
                values(',trans_id,',',temp_units,',',temp_units,',null,"',brokerID,'",',scheme2,',"',folio2,'","',client2,'");');
                  if(@insert_val_temp!='') then
                      PREPARE stmt1 from @insert_val_temp;
                      EXECUTE stmt1;
                      DEALLOCATE PREPARE stmt1;
                  end if; 
              end if;
         if temp_units >=0 then
          set done2=true;
               end if;
         END LOOP;
        end BLOCK2;
   END LOOP;
end BLOCK1;
set @delete_0 = CONCAT('delete from mf_val_valuation_temp_',brokerID, ' where live_unit <= 0.03;');
if(@delete_0 != '') then
	PREPARE stmt0 from @delete_0;
	EXECUTE stmt0;
	DEALLOCATE PREPARE stmt0;
end if; 
delete from temp_val_cg where live_unit < 0.05;
call sp_mf_capitalgain_calculation(brokerID);
select'hellooooo';
set @sql_select = concat('select 
                      tmft.name,
                      tmft.client_name as client_name,  
                      tmft.folio_number,
                      mfs.scheme_name,
                      mst.scheme_type as scheme_type,
                      Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
                      Date_format(tmft.purchase_date, "%d/%m/%Y") as sale_date,
                      tmft.mutual_fund_type,
                      mft.nav as p_nav,
                      tmft.nav as sale_nav,
                      tv.div_amount as div_amount,
                      tv.live_unit as units,
                      tv.p_amount as purchase_amount,
                     (tmft.nav * tv.live_unit) as sale_amount,
                      tv.unit_per_count,
                      mft.DPO_units,
                      tv.transaction_day,
                     ((tmft.nav * tv.live_unit)-(tv.p_amount+tv.div_amount)) as gain,
                      tv.div_r2 as div_r2,
                      tv.div_payout as payout,
                      tv.mf_cagr,
                      tv.mf_abs                                   
                      from temp_mf_transaction tmft
                      inner join mf_val_valuation_temp_',brokerID,' tv on  tmft.transaction_id=tv.red_trans_id
                      inner join mutual_fund_schemes mfs on  mfs.scheme_id= tmft.mutual_fund_scheme
                      inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
                      inner join temp_mf_transaction mft on mft.transaction_id = tv.transaction_id
                      order by mfs.scheme_name, mft.purchase_date, tmft.purchase_date;');
     IF(@sql_select != '') THEN
        PREPARE stmt10 FROM @sql_select;
        EXECUTE stmt10;
        DEALLOCATE PREPARE stmt10;
      END IF;
/*set @delete_qry = concat('drop table `mf_val_valuation_temp_',brokerID,'`;');
   IF(@delete_qry != '') THEN
        PREPARE stmt0 FROM @delete_qry;
        EXECUTE stmt0;
        DEALLOCATE PREPARE stmt0;
      END IF;
set @delete_qry1 = concat('drop temporary if exists table temp_val_cg;');
   IF(@delete_qry1 != '') THEN
        PREPARE stmt1 FROM @delete_qry;
        EXECUTE stmt1;
        DEALLOCATE PREPARE stmt1;
      END IF;*/
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_capitalgain_valuation` (IN `brokerID` VARCHAR(10), IN `familyID` VARCHAR(20), IN `clientID` VARCHAR(20), IN `startDate` VARCHAR(20))  NO SQL begin
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_valuation_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mf_val_valuation_temp_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    red_trans_id bigint,
    quantity decimal(18,4),
    mutual_fund_scheme  int,
    folio_number varchar(50),
    client_id varchar(30),                                          
    div_payout decimal(30,10),
    c_nav decimal(18,4),
    c_nav_date date,
    unit_per_count decimal(30,10),
    transaction_day int(11),
    mf_cagr decimal(18,2),
    mf_abs decimal(18,2),                    
    div_r2 decimal(30,10),
    p_amount decimal(30,12),
    div_amount decimal(30,12),                     
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`),
    INDEX `folio_number` (`folio_number`),
    INDEX `client_id` (`client_id`)
);');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
IF(familyID!="") then
    SET @sql_insert = CONCAT('INSERT INTO `mf_val_valuation_temp_',brokerID,'`(transaction_id, live_unit,quantity,mutual_fund_scheme,folio_number,client_id, broker_id)
    SELECT mft.transaction_id, mft.quantity,mft.quantity, mft.mutual_fund_scheme,mft.folio_number,mft.client_id, mft.broker_id FROM mutual_fund_transactions mft
      inner join  clients c on c.client_id=mft.client_id                                            
      WHERE mft.broker_id = "',brokerID,'"
            and c.family_id="',familyID,'"                                         
            and mft.transaction_type="Purchase"                 
      ORDER BY mft.transaction_id;');
    IF(@sql_insert != '') THEN
        PREPARE stmt4 FROM @sql_insert;
        EXECUTE stmt4;
        DEALLOCATE PREPARE stmt4;
    END IF;
else
	SET @sql_insert = CONCAT('INSERT INTO `mf_val_valuation_temp_',brokerID,'`(transaction_id, live_unit,quantity,mutual_fund_scheme,folio_number,client_id, broker_id)
    SELECT mft.transaction_id, mft.quantity,mft.quantity, mft.mutual_fund_scheme,mft.folio_number,mft.client_id, mft.broker_id FROM mutual_fund_transactions mft
      inner join  clients c on c.client_id=mft.client_id                                            
      WHERE mft.broker_id = "',brokerID,'"
            and c.client_id="',clientID,'"                                         
            and mft.transaction_type="Purchase"                 
      ORDER BY mft.transaction_id;');
    IF(@sql_insert != '') THEN
        PREPARE stmt4 FROM @sql_insert;
        EXECUTE stmt4;
        DEALLOCATE PREPARE stmt4;
    END IF;
end if;
SET @sql_update_first = CONCAT('UPDATE `mf_val_valuation_temp_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
SET vt.live_unit = vt.live_unit +
IFNULL((SELECT -(SUM(t2.quantity)) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	AND t2.purchase_date < "',startDate,'"
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id
    AND t2.broker_id = mft.broker_id 
),0) WHERE vt.transaction_id =
    (SELECT MIN(transaction_id) FROM mutual_fund_transactions
     WHERE mutual_fund_scheme = mft.mutual_fund_scheme
     AND folio_number = mft.folio_number
     AND client_id = mft.client_id
     AND broker_id = mft.broker_id
     GROUP BY mutual_fund_scheme, folio_number, client_id
    )
AND mft.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
SET @sql_update_all = CONCAT('UPDATE `mf_val_valuation_temp_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio = folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mf_val_valuation_temp_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id, a.mutual_fund_scheme, a.folio_number, a.purchase_date, a.valuation_id) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance 
WHERE vt1.live_unit != x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt6 FROM @sql_update_all;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;
/*
SET @sql_update_val = CONCAT('UPDATE mutual_fund_valuation v 
INNER JOIN `mf_val_temp_',brokerID,'` vt 
ON v.transaction_id = vt.transaction_id 
SET v.live_unit = vt.live_unit 
WHERE vt.live_unit != v.live_unit;');
IF(@sql_update_val != '') THEN
  	PREPARE stmt7 FROM @sql_update_val;
  	EXECUTE stmt7;
  	DEALLOCATE PREPARE stmt7;
END IF;
*/
SET @sql_delete_val = CONCAT('DELETE FROM `mf_val_valuation_temp_',brokerID,'` 
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_details` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
 create temporary table if not exists `mf_report_family` as
    (select mfs.scheme_name as mf_scheme_name,
     f.name as family_name,
     c.name as client_name,
     mft.client_id,
     mft.folio_number,
     Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
     mft.mutual_fund_type as mf_scheme_type,
     mst.scheme_type, 
     mfv.p_amount,
     mfv.div_amount,
     mft.nav as p_nav,
     mfv.live_unit,
     mfv.transaction_day,
     mfv.c_nav,
     Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
     mfv.div_r2, 
     mfv.div_payout,
     mfv.mf_cagr as cagr,
     mfv.mf_abs,
     (mfv.c_nav * mfv.live_unit) as current_value
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     inner join families f on c.family_id = f.family_id
     where mfv.broker_id = brokerID
     	AND c.family_id = familyID and c.status=1
     	and round((mfv.c_nav * mfv.live_unit)) > 3
     order by c.report_order, c.name, mfs.scheme_name, mft.folio_number, mft.purchase_date);
    select * from mf_report_family;
else
 	create temporary table if not exists `mf_report_client` as
    (select mfs.scheme_name as mf_scheme_name, 
     c.name as client_name,
     mft.client_id,
     mft.folio_number,
     Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
     mft.mutual_fund_type as mf_scheme_type, 
     mst.scheme_type, 
     mfv.p_amount,
     mfv.div_amount,
     mft.nav as p_nav,
     mfv.live_unit,
     mfv.transaction_day,
     mfv.c_nav, 
     Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
     mfv.div_r2,
     mfv.div_payout,
     mfv.mf_cagr as cagr,
     mfv.mf_abs,
     (mfv.c_nav * mfv.live_unit) as current_value
     from mutual_fund_valuation mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
     inner join clients c on mft.client_id = c.client_id
     where mfv.broker_id=brokerID
     	and mft.client_id=clientID  
     	and c.status=1
     	and round((mfv.c_nav * mfv.live_unit)) > 3
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date);
    select * from mf_report_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_clientwise_summary_family as (
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,  
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
           (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, 
      mst.scheme_type, mfs.market_cap, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number
  );
   select * from mf_clientwise_summary_family;
else
   create temporary table if not exists mf_clientwise_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,   
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
            (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name,mst.scheme_type, mfs.market_cap, min(year(mft.purchase_date)), mfs.scheme_name, mft.folio_number);
select * from mf_clientwise_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';

if(familyID !='') then
  
	SET @sql = CONCAT('select
		c.name as client_name,
		mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,              
		f.name as family_name,
		mft.client_id,
		mft.folio_number as folio_number,
		Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
		mst.scheme_type as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
		sum(mfv.div_amount) as div_amount,
		( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
		sum(mfv.live_unit) as live_unit,
		mft.mutual_fund_type as mf_scheme_type,
		MAX(mfv.transaction_day) as transaction_day,
		mfv.c_nav  as c_nav,
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
		sum((mfv.c_nav * mfv.live_unit)) as current_value,
		sum(mfv.div_r2)as div_r2,
		sum(mfv.div_payout) as div_payout,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,     (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'" and 
    c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, mst.scheme_type, mfs.market_cap, min(year(mft.transaction_id)),mfs.scheme_name, mft.folio_number;');
IF(@sql != '') THEN
  	PREPARE stmt1 FROM @sql;
	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
  
else
  	SET @sql = CONCAT('select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
                           (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, mst.scheme_type, mfs.market_cap, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';

if(familyID !='') then
  
	SET @sql = CONCAT('select
		c.name as client_name,
		mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,              
		f.name as family_name,
		mft.client_id,
		mft.folio_number as folio_number,
		Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
		mst.scheme_type as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
		sum(mfv.div_amount) as div_amount,
		( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
		sum(mfv.live_unit) as live_unit,
		mft.mutual_fund_type as mf_scheme_type,
		MAX(mfv.transaction_day) as transaction_day,
		mfv.c_nav  as c_nav,
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
		sum((mfv.c_nav * mfv.live_unit)) as current_value,
		sum(mfv.div_r2)as div_r2,
		sum(mfv.div_payout) as div_payout,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
                      (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'" 
                      
           and 
      (case when "',clientID,'"!='' and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='' then 1 
            else 0 end
             )=1            
                      
                      and 
    c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name,   mst.scheme_type, mfs.market_cap, min(year(mft.transaction_id)),mfs.scheme_name, mft.folio_number;');
IF(@sql != '') THEN
  	PREPARE stmt1 FROM @sql;
	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
  
else
  	SET @sql = CONCAT('select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
                      (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name,  mst.scheme_type, mfs.market_cap, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_clientwise_summary_family as (
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,  
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
           (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
   	and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1 else 0 end)=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, 
      mst.scheme_type, mfs.market_cap, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number
  );
   select * from mf_clientwise_summary_family;
else
   create temporary table if not exists mf_clientwise_summary_client as(
    select
       
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,   
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
            (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
      order by c.report_order,c.name,
       mst.scheme_type,mfs.market_cap,
     min(year(mft.purchase_date)), mfs.scheme_name, mft.folio_number);
select * from mf_clientwise_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary_year_wise` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_clientwise_summary_family as (
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,  
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
   	and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1 else 0 end)=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name,  min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number
  );
   select * from mf_clientwise_summary_family;
else
   create temporary table if not exists mf_clientwise_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,   
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, min(year(mft.purchase_date)), mfs.scheme_name, mft.folio_number);
select * from mf_clientwise_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_clientwise_summary_year_wise_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';

if(familyID !='') then
  
	SET @sql = CONCAT('select
		c.name as client_name,
		mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,              
		f.name as family_name,
		mft.client_id,
		mft.folio_number as folio_number,
		Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
		mst.scheme_type as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
		sum(mfv.div_amount) as div_amount,
		( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
		sum(mfv.live_unit) as live_unit,
		mft.mutual_fund_type as mf_scheme_type,
		MAX(mfv.transaction_day) as transaction_day,
		mfv.c_nav  as c_nav,
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
		sum((mfv.c_nav * mfv.live_unit)) as current_value,
		sum(mfv.div_r2)as div_r2,
		sum(mfv.div_payout) as div_payout,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'" 
                      
           and 
      (case when "',clientID,'"!='''' and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1            
                      
                      and 
    c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number;');
IF(@sql != '') THEN
  	PREPARE stmt1 FROM @sql;
	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
  
else
  	SET @sql = CONCAT('select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_1` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists 
  
  mf_comman_cap_detail_family as (
  select
	mfs.market_cap,
      mst.scheme_type_id,
      mst.scheme_type,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
	
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
   select * from mf_comman_cap_detail_family;
else
   create temporary table if not exists mf_comman_cap_detail_client as(
    select
	mfs.market_cap,
	mst.scheme_type,
       mst.scheme_type_id,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
    
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,
       mst.scheme_type,
       mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
select * from mf_comman_cap_detail_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_1_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
	mfs.market_cap,
	mst.scheme_type,
    mst.scheme_type_id,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
  from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id
    inner join clients 
		c on mft.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,	mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
	order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
		mfs.market_cap,	
mst.scheme_type,
                    mst.scheme_type_id,
		sum((mfv.c_nav * mfv.live_unit)) as current_value
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  

    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_1_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
	mfs.market_cap,
	mst.scheme_type,
    mst.scheme_type_id,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
  from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id
    inner join clients 
		c on mft.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
         and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,	mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
	order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
		mfs.market_cap,	
mst.scheme_type,
                    mst.scheme_type_id,
		sum((mfv.c_nav * mfv.live_unit)) as current_value
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  

    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_1_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists 
  
  mf_comman_cap_detail_family as (
  select
	mfs.market_cap,
      mst.scheme_type_id,
      mst.scheme_type,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
	
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
	and (case when clientID='' then 1 
                when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
                else 0 end)=1
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,mst.scheme_type,mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
   select * from mf_comman_cap_detail_family;
else
   create temporary table if not exists mf_comman_cap_detail_client as(
    select
	mfs.market_cap,
	mst.scheme_type,
       mst.scheme_type_id,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
    
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mfs.market_cap,
       mst.scheme_type,
       mst.scheme_type_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
select * from mf_comman_cap_detail_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_2` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_common_scheme_summary_family as (
  select
	mst.scheme_type,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
	
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mst.scheme_type
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
   select * from mf_common_scheme_summary_family;
else
   create temporary table if not exists mf_common_scheme_summary_client as(
    select
	mst.scheme_type,
    sum((mfv.c_nav * mfv.live_unit)) as current_value
    
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mst.scheme_type
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
select * from mf_common_scheme_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_cap_detail_2_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
	mst.scheme_type,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
  from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id
    inner join clients 
		c on mft.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
		
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mst.scheme_type
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
	order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
		mst.scheme_type,
		sum((mfv.c_nav * mfv.live_unit)) as current_value,    
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  		
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mst.scheme_type
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_scheme_summary` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_common_scheme_summary_family as (
  select
	mfs.scheme_name as mf_scheme_name,
    f.name as family_name,
    c.name as client_name,
	mft.client_id,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
      sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
   ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
   select * from mf_common_scheme_summary_family;
else
   create temporary table if not exists mf_common_scheme_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
	mft.client_id,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
	sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
select * from mf_common_scheme_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_scheme_summary_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_scheme_summary_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
                     and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
	
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_comman_scheme_summary_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_common_scheme_summary_family as (
  select
	mfs.scheme_name as mf_scheme_name,
    f.name as family_name,
    c.name as client_name,
	mft.client_id,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
      sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
   ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
      and (case when clientID='' then 1 
                when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
                else 0 end)=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
   select * from mf_common_scheme_summary_family;
else
   create temporary table if not exists mf_common_scheme_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
	mft.client_id,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
	sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name );
select * from mf_common_scheme_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_master` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin 
	if(familyID!='') then
    create temporary table if not exists mf_folio_master as ( 
	      select 
            c.name,
            mfs.scheme_name as scheme_name,
            f.name as family_name,
            cbd.folio_number,
            c.add_flat,
            c.add_street,
            c.add_area,
            c.add_city,
            c.add_state,
            c.add_pin,
            c.mobile,
            c.email_id,
            cbd.mode_holding,
            cbd.jointName1,
            cbd.jointName2,
            cbd.bank_name,
            cbd.bank_branch,
            cbd.bank_acc_no,
            cbd.nominee_name1 from client_bank_details cbd
            inner join clients c on c.client_id=cbd.client_id
            inner join families f on f.family_id=c.family_id
        	left join mutual_fund_schemes mfs on cbd.productId= mfs.prod_code
          	where f.family_id=familyID
              and f.broker_id=brokerID
        	order by c.report_order, c.name
        );
   else
    create temporary table if not exists mf_folio_master as ( 
     select 
            c.name,
        	mfs.scheme_name as scheme_name,
            cbd.folio_number,
            c.add_flat,
            c.add_street,
            c.add_area,
            c.add_city,
            c.add_state,
            c.add_pin,
            c.mobile,
            c.email_id,
            cbd.mode_holding,
            cbd.jointName1,
            cbd.jointName2,
            cbd.bank_name,
            cbd.bank_branch,
            cbd.bank_acc_no,
            cbd.nominee_name1 from client_bank_details cbd
            inner join clients c on c.client_id=cbd.client_id 
        	inner join families f on f.family_id=c.family_id 
        	left join mutual_fund_schemes mfs on cbd.productId= mfs.prod_code
            where c.client_id=clientID
              and f.broker_id=brokerID
        	order by c.report_order, c.name);
     end if;
 select * from mf_folio_master;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_master_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin 
	if(familyID!='') then
    create temporary table if not exists mf_folio_master as ( 
	      select 
            c.name,
            mfs.scheme_name as scheme_name,
            f.name as family_name,
            cbd.folio_number,
            c.add_flat,
            c.add_street,
            c.add_area,
            c.add_city,
            c.add_state,
            c.add_pin,
            c.mobile,
            c.email_id,
            cbd.mode_holding,
            cbd.jointName1,
            cbd.jointName2,
            cbd.bank_name,
            cbd.bank_branch,
            cbd.bank_acc_no,
            cbd.nominee_name1 from client_bank_details cbd
            inner join clients c on c.client_id=cbd.client_id
            inner join families f on f.family_id=c.family_id
        	left join mutual_fund_schemes mfs on cbd.productId= mfs.prod_code
          	where f.family_id=familyID
              and f.broker_id=brokerID
        	and (case when clientID='' then 1 
                when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
                else 0 end)=1
        	order by c.report_order, c.name
        );
   else
    create temporary table if not exists mf_folio_master as ( 
     select 
            c.name,
        	mfs.scheme_name as scheme_name,
            cbd.folio_number,
            c.add_flat,
            c.add_street,
            c.add_area,
            c.add_city,
            c.add_state,
            c.add_pin,
            c.mobile,
            c.email_id,
            cbd.mode_holding,
            cbd.jointName1,
            cbd.jointName2,
            cbd.bank_name,
            cbd.bank_branch,
            cbd.bank_acc_no,
            cbd.nominee_name1 from client_bank_details cbd
            inner join clients c on c.client_id=cbd.client_id 
        	inner join families f on f.family_id=c.family_id 
        	left join mutual_fund_schemes mfs on cbd.productId= mfs.prod_code
            where c.client_id=clientID
              and f.broker_id=brokerID
        	order by c.report_order, c.name);
     end if;
 select * from mf_folio_master;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_wise` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_foliowise_summary_family as (
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
      mfs.market_cap,
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date
  );
   select * from mf_foliowise_summary_family;
else
   create temporary table if not exists mf_foliowise_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
       mfs.market_cap,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date);
select * from mf_foliowise_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_wise_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_wise_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  SET @sql = CONCAT('
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
                     and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
  SET @sql = CONCAT(' 
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
	and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_folio_wise_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
  create temporary table if not exists mf_foliowise_summary_family as (
  select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
      mfs.market_cap,
    f.name as family_name,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
      (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and f.family_id =  familyID
    and c.status=1
    and (case when clientID='' then 1
        when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
        else 0 end)=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,c.name,mft.folio_number,mfs.scheme_name, mft.purchase_date
  );
   select * from mf_foliowise_summary_family;
else
   create temporary table if not exists mf_foliowise_summary_client as(
    select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
       mfs.market_cap,
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    avg(mft.nav) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    mfv.transaction_day as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.folio_number,mft.mutual_fund_scheme
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by  c.report_order,mft.folio_number,mfs.scheme_name, mft.purchase_date);
select * from mf_foliowise_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_client` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin
    create temporary table if not exists `mf_report_client` as 
    (select mfs.scheme_name as mf_scheme_name,mfs.market_cap, c.name as client_name, mft.client_id, 
     mft.folio_number, Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date, 
     mft.mutual_fund_type as mf_scheme_type, mst.scheme_type,  mfv.p_amount, 
     mfv.div_amount, mft.nav as p_nav, mfv.live_unit, 
     mfv.transaction_day, mfv.c_nav, Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date, 
     mfv.div_r2, mfv.div_payout, mfv.mf_cagr as cagr, mfv.mf_abs, 
     (mfv.c_nav * mfv.live_unit) as current_value 
     from mutual_fund_valuation mfv 
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c on mft.client_id = c.client_id 
     where mfv.broker_id = brokerID AND mft.client_id = clientID 
     and round((mfv.c_nav * mfv.live_unit)) > 3 
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date);
    select * from mf_report_client;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_client_historical` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)   begin

SET @sql_update_first = '';

SET @sql_update_first = CONCAT('select  distinct
		mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,
		f.name as family_name, 
		c.name as client_name, 
		mft.client_id, 
		mft.folio_number, 
		Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date, 
		mft.mutual_fund_type as mf_scheme_type, 
		mst.scheme_type, 
        mft.ref_no,
		mfv.p_amount, 
		mfv.div_amount, 
		mft.nav as p_nav, 
		mfv.live_unit, 
		mfv.transaction_day, 
		mfv.c_nav, 
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date, 
		mfv.div_r2, 
		mfv.div_payout, 
		mfv.mf_cagr as cagr, 
		mfv.mf_abs, 
		(mfv.c_nav * mfv.live_unit) as current_value 
     from `mutual_fund_valuation_h_',brokerID,'` mfv 
     inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c 
		on mft.client_id = c.client_id 
     inner join families f 
		on c.family_id = f.family_id 
     where mfv.broker_id = "',brokerID,'" AND 
		    c.client_id = "',clientID,'" and 
		   round((mfv.c_nav * mfv.live_unit)) > 3 
     order by 
		   c.report_order, 
		   c.name, 
		   mfs.scheme_name, 
		   mft.folio_number, 
		   mft.purchase_date;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
    create temporary table if not exists `mf_report_family` as 
    (select mfs.scheme_name as mf_scheme_name,mfs.market_cap, f.name as family_name, c.name as client_name, mft.client_id, 
     mft.folio_number, Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date, 
     mft.mutual_fund_type as mf_scheme_type, mst.scheme_type, mfv.p_amount, 
     mfv.div_amount, mft.nav as p_nav, mfv.live_unit, 
     mfv.transaction_day, mfv.c_nav, Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date, 
     mfv.div_r2, mfv.div_payout, mfv.mf_cagr as cagr, mfv.mf_abs, 
     (mfv.c_nav * mfv.live_unit) as current_value 
     from mutual_fund_valuation mfv 
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c on mft.client_id = c.client_id 
     inner join families f on c.family_id = f.family_id 
     where mfv.broker_id = brokerID AND c.family_id = familyID 
     and round((mfv.c_nav * mfv.live_unit)) > 3 
     order by c.report_order, c.name, mfs.scheme_name, mft.folio_number, mft.purchase_date);
    select * from mf_report_family;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_family_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)   begin

SET @sql_update_first = '';

SET @sql_update_first = CONCAT('select 
		mfs.scheme_name as mf_scheme_name, 
		mfs.market_cap,
		f.name as family_name, 
		c.name as client_name, 
		mft.client_id, 
		mft.folio_number, 
		Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date, 
		mft.mutual_fund_type as mf_scheme_type, 
		mst.scheme_type, 
		mfv.p_amount, 
		mfv.div_amount, 
		mft.nav as p_nav, 
		mfv.live_unit, 
		mfv.transaction_day, 
		mfv.c_nav, 
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date, 
		mfv.div_r2, 
		mfv.div_payout, 
		mfv.mf_cagr as cagr, 
		mfv.mf_abs, 
		(mfv.c_nav * mfv.live_unit) as current_value 
     from `mutual_fund_valuation_h_',brokerID,'` mfv 
     inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c 
		on mft.client_id = c.client_id 
     inner join families f 
		on c.family_id = f.family_id 
     where mfv.broker_id = "',brokerID,'" AND 
		   c.family_id = "',familyID,'" and 
		   round((mfv.c_nav * mfv.live_unit)) > 3 
     order by 
		   c.report_order, 
		   c.name, 
		   mfs.scheme_name, 
		   mft.folio_number, 
		   mft.purchase_date;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_family_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `reportDate` DATE, IN `clientID` VARCHAR(500))  NO SQL begin

SET @sql_update_first = '';

SET @sql_update_first = CONCAT('select 
		mfs.scheme_name as mf_scheme_name, 
		mfs.market_cap,
		f.name as family_name, 
		c.name as client_name, 
		mft.client_id, 
		mft.folio_number, 
       	mft.ref_no,
		Date_format(mft.purchase_date, "%d/%m/%Y") as purchase_date, 
		mft.mutual_fund_type as mf_scheme_type, 
		mst.scheme_type, 
		mfv.p_amount, 
		mfv.div_amount, 
		mft.nav as p_nav, 
		mfv.live_unit, 
		mfv.transaction_day, 
		mfv.c_nav, 
		Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date, 
		mfv.div_r2, 
		mfv.div_payout, 
		mfv.mf_cagr as cagr, 
		mfv.mf_abs, 
		(mfv.c_nav * mfv.live_unit) as current_value 
     from `mutual_fund_valuation_h_',brokerID,'` mfv 
     inner join mutual_fund_transactions mft 
		on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs 
		on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst 
		on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c 
		on mft.client_id = c.client_id 
     inner join families f 
		on c.family_id = f.family_id 
     where mfv.broker_id = "',brokerID,'" AND 
		   c.family_id = "',familyID,'" 
            and 
      (case when "',clientID,'"!=''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='' then 1 
            else 0 end
             )=1
                               and 
		   round((mfv.c_nav * mfv.live_unit)) > 3 
     order by 
		   c.report_order, 
		   c.name, 
		   mfs.scheme_name, 
		   mft.folio_number, 
		   mft.purchase_date;');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_report_family_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
    create temporary table if not exists `mf_report_family` as 
    (select mfs.scheme_name as mf_scheme_name,mfs.market_cap, f.name as family_name, c.name as client_name, mft.client_id, 
     mft.folio_number, Date_format(mft.purchase_date, '%d/%m/%Y') as purchase_date, 
     mft.mutual_fund_type as mf_scheme_type, mst.scheme_type, mfv.p_amount, 
     mfv.div_amount, mft.nav as p_nav, mfv.live_unit, 
     mfv.transaction_day, mfv.c_nav, Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date, 
     mfv.div_r2, mfv.div_payout, mfv.mf_cagr as cagr, mfv.mf_abs, 
     (mfv.c_nav * mfv.live_unit) as current_value 
     from mutual_fund_valuation mfv 
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c on mft.client_id = c.client_id 
     inner join families f on c.family_id = f.family_id 
     where mfv.broker_id = brokerID AND 
     c.family_id = familyID 
     and 
     (case when clientID!='' and FIND_IN_SET(c.client_id,clientID) then 1 
     when clientID='' then 1 
     else 0 end)=1
     and round((mfv.c_nav * mfv.live_unit)) > 3 
     order by c.report_order, c.name, mfs.scheme_name, mft.folio_number, mft.purchase_date);
    select * from mf_report_family;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_detail` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
    create temporary table if not exists mf_summary_report_scheme_wise_family as (
        select mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,
    f.name as family_name,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date);
    select * from mf_summary_report_scheme_wise_family;
else
create temporary table if not exists mf_summary_report_scheme_wise_family as (
    select mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date);
select * from mf_summary_report_scheme_wise_family;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_detail_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';

if(familyID !='') then
  
	SET @sql = CONCAT('
	select 
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
    f.name as family_name,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id

    
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date;');
IF(@sql != '') THEN
  	PREPARE stmt1 FROM @sql;
	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
  
else
  	SET @sql = CONCAT(' select mfs.scheme_name as mf_scheme_name,
                      mfs.market_cap,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2

    from mutual_fund_valuation_h_',brokerID,' mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
		and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_detail_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';

if(familyID !='') then
  
	SET @sql = CONCAT('
	select 
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
    f.name as family_name,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id

    
    where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
                       and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date;');
IF(@sql != '') THEN
  	PREPARE stmt1 FROM @sql;
	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
  
else
  	SET @sql = CONCAT(' select mfs.scheme_name as mf_scheme_name,
                      mfs.market_cap,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2

    from mutual_fund_valuation_h_',brokerID,' mfv
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
		and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_detail_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
    create temporary table if not exists mf_summary_report_scheme_wise_family as (
        select mfs.scheme_name as mf_scheme_name,
        mfs.market_cap,
    f.name as family_name,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and (case when clientID='' then 1
            when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1 else 0 end)=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id, mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date);
    select * from mf_summary_report_scheme_wise_family;
else
create temporary table if not exists mf_summary_report_scheme_wise_family as (
    select mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,
    c.name as client_name, mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), '%d/%m/%Y') as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, '%d/%m/%Y') as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID
    and c.client_id =  clientID
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number 
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by mfs.scheme_name,mft.folio_number, mft.purchase_date);
select * from mf_summary_report_scheme_wise_family;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_net_invest` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL Begin
Declare purchase decimal(18,2);
Declare swi decimal(18,2);
Declare redemption decimal(18,2);
Declare swo decimal(18,2);
Declare payout decimal(18,2);
Declare net_invest decimal(18,2);
Declare current_value decimal(18,2);
if(familyID !='') then

	select sum(t.amount) into payout from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and mutual_fund_type in("DP");
	select sum(t.amount) into purchase from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
		and mutual_fund_type in("IPO","NFO","PIP","TIN");
	select sum(t.amount) into swi from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
		and mutual_fund_type in("SWI");	
	select sum(t.amount) into redemption from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
		and mutual_fund_type in("RED");
	select sum(t.amount) into swo from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
		and mutual_fund_type in("SWO");
 	select
    	sum((mfv.c_nav * mfv.live_unit)) into  current_value
    	from mutual_fund_valuation mfv
    	inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    	inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
    	where mfv.broker_id = brokerID
	 	and f.family_id =  familyID
    	and c.status=1 
    	and round((mfv.c_nav * mfv.live_unit)) > 3;
	select net_invest,payout,purchase,redemption,current_value,swi,swo,brokerID;

else
	select sum(t.amount) into payout from mutual_fund_transactions t
		where t.broker_id = brokerID and client_id =  clientID
		and mutual_fund_type in("DP");
	select sum(amount) into purchase from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Purchase'
		and mutual_fund_type in("IPO","NFO","PIP","TIN");
	select sum(amount) into swi from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Purchase'
		and mutual_fund_type in("SWI");
	select sum(amount) into redemption from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Redemption'
		and mutual_fund_type in("RED");
	select sum(amount) into swo from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Redemption'
		and mutual_fund_type in("SWO");
	select
    	sum((mfv.c_nav * mfv.live_unit)) into  current_value
    	from mutual_fund_valuation mfv
    	inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    	inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
    	where mfv.broker_id = brokerID
	 	and c.client_id =clientID  
    	and c.status=1 
    	and round((mfv.c_nav * mfv.live_unit)) > 3;
	select net_invest,payout,purchase,redemption,current_value,swi,swo,brokerID;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_net_invest_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL Begin
	Declare purchase decimal(18,2);
	Declare swi decimal(18,2);
	Declare redemption decimal(18,2);
	Declare swo decimal(18,2);
	Declare payout decimal(18,2);
	Declare net_invest decimal(18,2);
Declare current_value decimal(18,2);	
	
	if(familyID !='') then
		select sum(t.amount) into payout from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.purchase_date <= reportDate
		and mutual_fund_type in("DP");

		select sum(t.amount) into purchase from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
		and t.purchase_date<=reportDate
		and mutual_fund_type in("IPO","NFO","PIP","TIN");

		select sum(t.amount) into swi from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
		and t.purchase_date<=reportDate
		and mutual_fund_type in("SWI");

		select sum(t.amount) into redemption from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
		and t.purchase_date<=reportDate
		and mutual_fund_type in("RED");

		select sum(t.amount) into swo from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
		and t.purchase_date<=reportDate
		and mutual_fund_type in("SWO");
	 
     
	 SET @sql= CONCAT('select
		sum((mfv.c_nav * mfv.live_unit)) into @current_value
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
		and c.status=1 
		and mft.purchase_date<="',reportDate,'"  
		and round((mfv.c_nav * mfv.live_unit)) > 3');
        
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

	select net_invest,payout,purchase,redemption,@current_value as current_value,swi,swo,brokerID;
else

	select sum(t.amount) into payout from mutual_fund_transactions t
	where t.broker_id = brokerID and client_id =  clientID
	and t.purchase_date<=reportDate
	and mutual_fund_type in("DP");

	select sum(amount) into purchase from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Purchase'
	and purchase_date<=reportDate
	and mutual_fund_type in("IPO","NFO","PIP","TIN");

	select sum(amount) into swi from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Purchase'
	and purchase_date<=reportDate
	and mutual_fund_type in("SWI");

	select sum(amount) into redemption from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Redemption'
	and purchase_date<=reportDate
	and mutual_fund_type in("RED");

	select sum(amount) into swo from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Redemption'
	and purchase_date<=reportDate
	and mutual_fund_type in("SWO");
	

	


	SET @sql = CONCAT('select
		sum((mfv.c_nav * mfv.live_unit)) into @current_value
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
		and c.status=1 
		and mft.purchase_date<="',reportDate,'"  
		and round((mfv.c_nav * mfv.live_unit)) > 3');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

	select net_invest,payout,purchase,redemption,@current_value as current_value,swi,swo,brokerID;
       

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_net_invest_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL Begin
	Declare purchase decimal(18,2);
	Declare swi decimal(18,2);
	Declare redemption decimal(18,2);
	Declare swo decimal(18,2);
	Declare payout decimal(18,2);
	Declare net_invest decimal(18,2);
Declare current_value decimal(18,2);	
	
	if(familyID !='') then
		select sum(t.amount) into payout from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.purchase_date <= reportDate
         and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and mutual_fund_type in("DP");

		select sum(t.amount) into purchase from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
         and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.purchase_date<=reportDate
		and mutual_fund_type in("IPO","NFO","PIP","TIN");

		select sum(t.amount) into swi from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Purchase'
         and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.purchase_date<=reportDate
		and mutual_fund_type in("SWI");

		select sum(t.amount) into redemption from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
         and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.purchase_date<=reportDate
		and mutual_fund_type in("RED");

		select sum(t.amount) into swo from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
		and t.transaction_type = 'Redemption'
         and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.purchase_date<=reportDate
		and mutual_fund_type in("SWO");
	 
     
	 SET @sql= CONCAT('select
		sum((mfv.c_nav * mfv.live_unit)) into @current_value
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where 
		mfv.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
                       and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
		and c.status=1 
		and mft.purchase_date<="',reportDate,'"  
		and round((mfv.c_nav * mfv.live_unit)) > 3');
        
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

	select net_invest,payout,purchase,redemption,@current_value as current_value,swi,swo,brokerID;
else

	select sum(t.amount) into payout from mutual_fund_transactions t
	where t.broker_id = brokerID and client_id =  clientID
	and t.purchase_date<=reportDate
	and mutual_fund_type in("DP");

	select sum(amount) into purchase from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Purchase'
	and purchase_date<=reportDate
	and mutual_fund_type in("IPO","NFO","PIP","TIN");

	select sum(amount) into swi from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Purchase'
	and purchase_date<=reportDate
	and mutual_fund_type in("SWI");

	select sum(amount) into redemption from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Redemption'
	and purchase_date<=reportDate
	and mutual_fund_type in("RED");

	select sum(amount) into swo from mutual_fund_transactions
	where broker_id = brokerID and client_id = clientID
	and transaction_type = 'Redemption'
	and purchase_date<=reportDate
	and mutual_fund_type in("SWO");
	

	


	SET @sql = CONCAT('select
		sum((mfv.c_nav * mfv.live_unit)) into @current_value
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
		inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
	where mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
		and c.status=1 
		and mft.purchase_date<="',reportDate,'"  
		and round((mfv.c_nav * mfv.live_unit)) > 3');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

	select net_invest,payout,purchase,redemption,@current_value as current_value,swi,swo,brokerID;
       

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_net_invest_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL Begin
Declare purchase decimal(18,2);
Declare swi decimal(18,2);
Declare redemption decimal(18,2);
Declare swo decimal(18,2);
Declare payout decimal(18,2);
Declare net_invest decimal(18,2);
Declare current_value decimal(18,2);
if(familyID !='') then

	select sum(t.amount) into payout from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and mutual_fund_type in("DP");
	select sum(t.amount) into purchase from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.transaction_type = 'Purchase'
		and mutual_fund_type in("IPO","NFO","PIP","TIN");
	select sum(t.amount) into swi from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.transaction_type = 'Purchase'
		and mutual_fund_type in("SWI");	
	select sum(t.amount) into redemption from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.transaction_type = 'Redemption'
		and mutual_fund_type in("RED");
	select sum(t.amount) into swo from mutual_fund_transactions t
		inner join clients c on t.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
		where t.broker_id = brokerID and f.family_id = familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
		and t.transaction_type = 'Redemption'
		and mutual_fund_type in("SWO");
 	select
    	sum((mfv.c_nav * mfv.live_unit)) into  current_value
    	from mutual_fund_valuation mfv
    	inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    	inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
    	where mfv.broker_id = brokerID
	 	and f.family_id =  familyID
        and (case when clientID='' then 1 
        when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
        else 0 end)=1
    	and c.status=1 
    	and round((mfv.c_nav * mfv.live_unit)) > 3;
	select net_invest,payout,purchase,redemption,current_value,swi,swo,brokerID;

else
	select sum(t.amount) into payout from mutual_fund_transactions t
		where t.broker_id = brokerID and client_id =  clientID
		and mutual_fund_type in("DP");
	select sum(amount) into purchase from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Purchase'
		and mutual_fund_type in("IPO","NFO","PIP","TIN");
	select sum(amount) into swi from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Purchase'
		and mutual_fund_type in("SWI");
	select sum(amount) into redemption from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Redemption'
		and mutual_fund_type in("RED");
	select sum(amount) into swo from mutual_fund_transactions
		where broker_id = brokerID and client_id = clientID
		and transaction_type = 'Redemption'
		and mutual_fund_type in("SWO");
	select
    	sum((mfv.c_nav * mfv.live_unit)) into  current_value
    	from mutual_fund_valuation mfv
    	inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    	inner join clients c on mft.client_id = c.client_id
		inner join families f on f.family_id=c.family_id
    	where mfv.broker_id = brokerID
	 	and c.client_id =clientID  
    	and c.status=1 
    	and round((mfv.c_nav * mfv.live_unit)) > 3;
	select net_invest,payout,purchase,redemption,current_value,swi,swo,brokerID;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_clientwise` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
if(familyID !='') then
create temporary table if not exists `mf_report_client_summary` as (
select c.name as client_name,
    sum(mfv.p_amount) as Amount, 
    sum(mfv.div_amount) as Div_Amount,
    sum(mfv.live_unit) as Units,
	( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as payout,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
	(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
from mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
inner join clients c on mft.client_id = c.client_id
inner join families f on f.family_id=c.family_id
where mfv.broker_id = brokerID
    and f.family_id = familyID
    and c.status=1
	and round((mfv.c_nav * mfv.live_unit)) > 3
group by c.name
order by c.report_order, c.name);
select * from mf_report_client_summary;
else
create temporary table if not exists `mf_report_client_summary_client` as (
select c.name as client_name,
    sum(mfv.p_amount) as Amount, 
    sum(mfv.div_amount) as Div_Amount,
    sum(mfv.live_unit) as Units,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as payout,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
  (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
from mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
inner join clients c on mft.client_id = c.client_id
where mfv.broker_id = brokerID
    and c.client_id = clientID
    and c.status=1
	and round((mfv.c_nav * mfv.live_unit)) > 3
group by c.name
order by c.report_order, c.name);
select * from mf_report_client_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_clientwise_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql='';
if(familyID !='') then
	SET @sql = CONCAT(' 
		select c.name as client_name,
			sum(mfv.p_amount) as Amount, 
			sum(mfv.div_amount) as Div_Amount,
			sum(mfv.live_unit) as Units,
			( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
			sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
		   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
		inner join clients c 
			on mft.client_id = c.client_id
		inner join families f 
			on f.family_id=c.family_id
		where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
		    and c.status=1
			and round((mfv.c_nav * mfv.live_unit)) > 3
		group by c.name
		order by c.report_order, c.name;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
	SET @sql = CONCAT(' 
		select c.name as client_name,
			sum(mfv.p_amount) as Amount, 
			sum(mfv.div_amount) as Div_Amount,
			sum(mfv.live_unit) as Units,
			( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
			sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
		inner join clients c 
			on mft.client_id = c.client_id
		where 
			mfv.broker_id = "',brokerID,'" AND 
			c.client_id = "',clientID,'"  
			and c.status=1
			and round((mfv.c_nav * mfv.live_unit)) > 3
		group by c.name
		order by c.report_order, c.name;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_clientwise_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql='';
if(familyID !='') then
	SET @sql = CONCAT(' 
		select c.name as client_name,
			sum(mfv.p_amount) as Amount, 
			sum(mfv.div_amount) as Div_Amount,
			sum(mfv.live_unit) as Units,
			( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
			sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
		   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
		inner join clients c 
			on mft.client_id = c.client_id
		inner join families f 
			on f.family_id=c.family_id
		where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
                       and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
		    and c.status=1
			and round((mfv.c_nav * mfv.live_unit)) > 3
		group by c.name
		order by c.report_order, c.name;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
else
	SET @sql = CONCAT(' 
		select c.name as client_name,
			sum(mfv.p_amount) as Amount, 
			sum(mfv.div_amount) as Div_Amount,
			sum(mfv.live_unit) as Units,
			( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
			sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
		from mutual_fund_valuation_h_',brokerID,' mfv
		inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
		inner join clients c 
			on mft.client_id = c.client_id
		where 
			mfv.broker_id = "',brokerID,'" AND 
			c.client_id = "',clientID,'"  
			and c.status=1
			and round((mfv.c_nav * mfv.live_unit)) > 3
		group by c.name
		order by c.report_order, c.name;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_clientwise_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID !='') then
create temporary table if not exists `mf_report_client_summary` as (
select c.name as client_name,
    sum(mfv.p_amount) as Amount, 
    sum(mfv.div_amount) as Div_Amount,
    sum(mfv.live_unit) as Units,
	( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as payout,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
	(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
from mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
inner join clients c on mft.client_id = c.client_id
inner join families f on f.family_id=c.family_id
where mfv.broker_id = brokerID
    and f.family_id = familyID
    and c.status=1
       and (case when clientID='' then 1
        when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
        else 0 end)=1
	and round((mfv.c_nav * mfv.live_unit)) > 3
group by c.name
order by c.report_order, c.name);
select * from mf_report_client_summary;
else
create temporary table if not exists `mf_report_client_summary_client` as (
select c.name as client_name,
    sum(mfv.p_amount) as Amount, 
    sum(mfv.div_amount) as Div_Amount,
    sum(mfv.live_unit) as Units,
    ( (sum(mfv.p_amount)+sum(mfv.div_amount) ) / sum(mfv.live_unit) ) as nav,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as payout,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
  (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
from mutual_fund_valuation mfv
inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
inner join clients c on mft.client_id = c.client_id
where mfv.broker_id = brokerID
    and c.client_id = clientID
    and c.status=1
	and round((mfv.c_nav * mfv.live_unit)) > 3
group by c.name
order by c.report_order, c.name);
select * from mf_report_client_summary_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_typewise` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(20))  NO SQL begin
IF(familyID != "" )then
       select 'Equity' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
    	sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
   union 
    	select 'Hybrid' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
		select 'Debt' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid");
  else
        select 'Equity'  as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
 union
		select 'Hybrid' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
        select  'Debt'  as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and  mst.scheme_type IN("Debt","Capital Protection","FMP",'LT Debt','Liquid');
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_typewise_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin

IF(familyID != "" )then
	  SET @sql = CONCAT('select "Equity" as scheme_type,
			sum(mfv.p_amount) as purchase_amount, 
    		sum(mfv.div_amount) as div_amount,
			sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
        inner join clients c 
			on mft.client_id = c.client_id
        inner join families f 
			on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs 
			on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst 
			on mfs.scheme_type_id = mst.scheme_type_id
        where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
   union 
    	select "Hybrid" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
		select "Debt" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
  else
       SET @sql = CONCAT(' select "Equity"  as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
 union
		select "Hybrid" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
        select  "Debt"  as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_typewise_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin

IF(familyID != "" )then
	  SET @sql = CONCAT('select "Equity" as scheme_type,
			sum(mfv.p_amount) as purchase_amount, 
    		sum(mfv.div_amount) as div_amount,
			sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
			sum(mfv.div_payout) as payout,
			sum((mfv.c_nav * mfv.live_unit)) as current_value,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft 
			on mfv.transaction_id = mft.transaction_id
        inner join clients c 
			on mft.client_id = c.client_id
        inner join families f 
			on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs 
			on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst 
			on mfs.scheme_type_id = mst.scheme_type_id
        where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
                         and 
      (case when "',clientID,'"!=''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='' then 1 
            else 0 end
             )=1
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
   union 
    	select "Hybrid" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
			mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'"  
                         and 
      (case when "',clientID,'"!=''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='' then 1 
            else 0 end
             )=1
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
		select "Debt" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
			f.family_id = "',familyID,'" 
                         and 
      (case when "',clientID,'"!=''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='' then 1 
            else 0 end
             )=1
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
  else
       SET @sql = CONCAT(' select "Equity"  as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
		
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
 union
		select "Hybrid" as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
        select  "Debt"  as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation_h_',brokerID,' mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
	    and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid")');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_schemewise_summary_report_typewise_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
IF(familyID != "" )then
       select 'Equity' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
    	sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and (case when clientID='' then 1
                    when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
                    else 0 end)=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
   union 
    	select 'Hybrid' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and (case when clientID='' then 1
                    when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
                    else 0 end)=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
		select 'Debt' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
		(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
        (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join families f on f.family_id=c.family_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and f.family_id = familyID
        and c.status=1
        and (case when clientID='' then 1
                    when clientID!='' and FIND_IN_SET(c.client_id,clientID ) then 1
                    else 0 end)=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid");
  else
        select 'Equity'  as scheme_type,
		sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund")
 union
		select 'Hybrid' as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and mst.scheme_type IN("Hybrid","Balanced","MIP")
union
        select  'Debt'  as scheme_type,
        sum(mfv.p_amount) as purchase_amount, 
        sum(mfv.div_amount) as div_amount,
        sum(mfv.live_unit) as units,sum(mfv.div_r2)as div_r2,
        sum(mfv.div_payout) as payout,
        sum((mfv.c_nav * mfv.live_unit)) as current_value,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
       (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as  abs
        from mutual_fund_valuation mfv
        inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
        inner join clients c on mft.client_id = c.client_id
        inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
        inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
        where mfv.broker_id = brokerID
        and c.client_id =  clientID
        and c.status=1
        and round((mfv.c_nav * mfv.live_unit)) > 3
        and  mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid");
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_sip` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30))  NO SQL begin
if(familyID != '') then
  create temporary table if not exists sip_report as (
   SELECT at.cease_date AS cease_date, f.name AS family_name,
      sum(mfv.p_amount) AS total_invested_value,
      at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, '%d/%m/%Y' ) AS start_date,
DATE_FORMAT( at.end_date,  '%d/%m/%Y' ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, mfv.c_nav AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
 AND at.scheme_id = mft.mutual_fund_scheme 
 AND TRIM(LEADING '0' FROM at.folio_no)=TRIM(LEADING '0' FROM mft.folio_number)
    AND at.installment_amount=mft.amount
     AND at.broker_id=mft.broker_id)
  INNER JOIN mutual_fund_valuation mfv on mft.transaction_id=mfv.transaction_id
 WHERE at.broker_id = brokerID
AND f.family_id =  familyID
 AND c.status =1 
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != '0000-00-00' THEN at.cease_date ELSE at.end_date END) 
GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no), at.client_id, at.broker_id
  );
 select * from sip_report;
else
   create temporary table if not exists sip_report_client as
(
    SELECT at.cease_date AS cease_date, f.name AS family_name,
    sum(mfv.p_amount) AS total_invested_value,
    at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, '%d/%m/%Y' ) AS start_date,
DATE_FORMAT( at.end_date,  '%d/%m/%Y' ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, SUM(mfv.c_nav) AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
 AND at.scheme_id = mft.mutual_fund_scheme 
 AND TRIM(LEADING '0' FROM at.folio_no)=TRIM(LEADING '0' FROM mft.folio_number)
    AND at.installment_amount=mft.amount
     AND at.broker_id=mft.broker_id)
  INNER JOIN mutual_fund_valuation mfv on mft.transaction_id=mfv.transaction_id
 WHERE at.broker_id =  brokerID
AND at.client_id =  clientID
 AND c.status =1 
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != '0000-00-00' THEN at.cease_date ELSE at.end_date END)
 GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no), at.client_id, at.broker_id
);
select * from sip_report_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_sip_historical` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID != '') then
  SET @sql = CONCAT('SELECT	
			at.cease_date AS cease_date, 
			f.name AS family_name,
			sum(mfv.p_amount) AS total_invested_value,
			at.frequency AS Frequency, 
			at.goal, SUM(mfv.p_amount) AS purchase_amount,
			c.name AS client_name, 
			mfs.scheme_name AS Scheme_Name, 
			DATE_FORMAT( at.start_date, "%d/%m/%Y" ) AS start_date,
			DATE_FORMAT( at.end_date,  "%d/%m/%Y" ) AS end_date, 
			at.installment_amount AS installment_amt, 
			at.scheme_id, TRIM(LEADING "0" FROM at.folio_no) as folio_no,
			at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, mfv.c_nav AS current_nav, SUM(mfv.div_r2) AS Divr,
			SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
			MAX(mft.purchase_date) as last_installment_date
	FROM asset_transactions at
	INNER JOIN clients c ON at.client_id = c.client_id
	INNER JOIN families f ON c.family_id = f.family_id
	INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
	INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
												AND at.scheme_id = mft.mutual_fund_scheme 
												AND TRIM(LEADING "0" FROM at.folio_no)=TRIM(LEADING "0" FROM mft.folio_number)
												AND at.installment_amount=mft.amount
												AND at.broker_id=mft.broker_id)
	INNER JOIN mutual_fund_valuation_h_',brokerID,' mfv 
			on mft.transaction_id=mfv.transaction_id
	WHERE 
		at.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
		AND c.status =1 
		AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != "0000-00-00" THEN at.cease_date ELSE at.end_date END) 
		GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no), at.client_id, at.broker_id
		');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

 
else
   SET @sql = CONCAT('
    SELECT at.cease_date AS cease_date, f.name AS family_name,
    sum(mfv.p_amount) AS total_invested_value,
    at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, "%d/%m/%Y" ) AS start_date,
DATE_FORMAT( at.end_date,  "%d/%m/%Y" ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, SUM(mfv.c_nav) AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
											AND at.scheme_id = mft.mutual_fund_scheme 
											AND TRIM(LEADING "0" FROM at.folio_no)=TRIM(LEADING "0" FROM mft.folio_number)
											AND at.installment_amount=mft.amount
											AND at.broker_id=mft.broker_id)
INNER JOIN mutual_fund_valuation_h_',brokerID,' mfv on mft.transaction_id=mfv.transaction_id
WHERE 
	at.broker_id = "',brokerID,'" AND 
	at.client_id = "',clientID,'" 
 AND c.status =1 
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != "0000-00-00" THEN at.cease_date ELSE at.end_date END)
 GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no), at.client_id, at.broker_id');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
 
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_sip_historical_new` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(500), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID != '') then
  SET @sql = CONCAT('SELECT	
			at.cease_date AS cease_date, 
			f.name AS family_name,
			sum(mfv.p_amount) AS total_invested_value,
			at.frequency AS Frequency, 
			at.goal, SUM(mfv.p_amount) AS purchase_amount,
			c.name AS client_name, 
			mfs.scheme_name AS Scheme_Name, 
			DATE_FORMAT( at.start_date, "%d/%m/%Y" ) AS start_date,
			DATE_FORMAT( at.end_date,  "%d/%m/%Y" ) AS end_date, 
			at.installment_amount AS installment_amt, 
			at.scheme_id, TRIM(LEADING "0" FROM at.folio_no) as folio_no,
			at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, mfv.c_nav AS current_nav, SUM(mfv.div_r2) AS Divr,
			SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
			(sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
			MAX(mft.purchase_date) as last_installment_date
	FROM asset_transactions at
	INNER JOIN clients c ON at.client_id = c.client_id
	INNER JOIN families f ON c.family_id = f.family_id
	INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
	INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
												AND at.scheme_id = mft.mutual_fund_scheme 
												AND TRIM(LEADING "0" FROM at.folio_no)=TRIM(LEADING "0" FROM mft.folio_number)
												AND at.installment_amount=mft.amount
												AND at.broker_id=mft.broker_id)
	INNER JOIN mutual_fund_valuation_h_',brokerID,' mfv 
			on mft.transaction_id=mfv.transaction_id
	WHERE 
		at.broker_id = "',brokerID,'" AND 
		f.family_id = "',familyID,'"  
                     and 
      (case when "',clientID,'"!=''''  and FIND_IN_SET(c.client_id,"',clientID,'") then 1 
            when "',clientID,'"='''' then 1 
            else 0 end
             )=1
		AND c.status =1 
		AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != "0000-00-00" THEN at.cease_date ELSE at.end_date END) 
		GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no), at.client_id, at.broker_id
		');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

 
else
   SET @sql = CONCAT('
    SELECT at.cease_date AS cease_date, f.name AS family_name,
    sum(mfv.p_amount) AS total_invested_value,
    at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, "%d/%m/%Y" ) AS start_date,
DATE_FORMAT( at.end_date,  "%d/%m/%Y" ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, SUM(mfv.c_nav) AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
											AND at.scheme_id = mft.mutual_fund_scheme 
											AND TRIM(LEADING "0" FROM at.folio_no)=TRIM(LEADING "0" FROM mft.folio_number)
											AND at.installment_amount=mft.amount
											AND at.broker_id=mft.broker_id)
INNER JOIN mutual_fund_valuation_h_',brokerID,' mfv on mft.transaction_id=mfv.transaction_id
WHERE 
	at.broker_id = "',brokerID,'" AND 
	at.client_id = "',clientID,'" 
 AND c.status =1 
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != "0000-00-00" THEN at.cease_date ELSE at.end_date END)
 GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING "0" FROM at.folio_no), at.client_id, at.broker_id');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;
 
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_sip_new` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
if(familyID != '') then
  create temporary table if not exists sip_report as (
   SELECT at.cease_date AS cease_date, f.name AS family_name,
      sum(mfv.p_amount) AS total_invested_value,
      at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, '%d/%m/%Y' ) AS start_date,
DATE_FORMAT( at.end_date,  '%d/%m/%Y' ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, mfv.c_nav AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
 AND at.scheme_id = mft.mutual_fund_scheme 
 AND TRIM(LEADING '0' FROM at.folio_no)=TRIM(LEADING '0' FROM mft.folio_number)
    AND at.installment_amount=mft.amount
     AND at.broker_id=mft.broker_id)
  INNER JOIN mutual_fund_valuation mfv on mft.transaction_id=mfv.transaction_id
 WHERE at.broker_id = brokerID
AND f.family_id =  familyID
 AND c.status =1 
 and (case when clientID='' then 1 
     	when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
     else 0 end)=1
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != '0000-00-00' THEN at.cease_date ELSE at.end_date END) 
GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no), at.client_id, at.broker_id
  );
 select * from sip_report;
else
   create temporary table if not exists sip_report_client as
(
    SELECT at.cease_date AS cease_date, f.name AS family_name,
    sum(mfv.p_amount) AS total_invested_value,
    at.frequency AS Frequency, at.goal, SUM(mfv.p_amount) AS purchase_amount,
c.name AS client_name, mfs.scheme_name AS Scheme_Name, DATE_FORMAT( at.start_date, '%d/%m/%Y' ) AS start_date,
DATE_FORMAT( at.end_date,  '%d/%m/%Y' ) AS end_date, at.installment_amount AS installment_amt, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no) as folio_no,
 at.client_id, SUM(mfv.div_amount) AS div_amount, SUM(mfv.live_unit) AS balance_unit, SUM(mfv.c_nav) AS current_nav, SUM(mfv.div_r2) AS Divr,
 SUM(mfv.div_payout) AS DivP, SUM(mfv.live_unit*mfv.c_nav) AS current_value, SUM((mfv.live_unit*mfv.c_nav) + mfv.div_payout) AS total,
 (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day) /sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
   (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
MAX(mft.purchase_date) as last_installment_date
FROM asset_transactions at
INNER JOIN clients c ON at.client_id = c.client_id
INNER JOIN families f ON c.family_id = f.family_id
INNER JOIN mutual_fund_schemes mfs ON at.scheme_id = mfs.scheme_id
INNER JOIN mutual_fund_transactions mft ON (at.client_id = mft.client_id 
 AND at.scheme_id = mft.mutual_fund_scheme 
 AND TRIM(LEADING '0' FROM at.folio_no)=TRIM(LEADING '0' FROM mft.folio_number)
    AND at.installment_amount=mft.amount
     AND at.broker_id=mft.broker_id)
  INNER JOIN mutual_fund_valuation mfv on mft.transaction_id=mfv.transaction_id
 WHERE at.broker_id =  brokerID
AND at.client_id =  clientID
 AND c.status =1 
AND mft.purchase_date BETWEEN at.start_date AND (CASE WHEN at.cease_date IS NOT NULL AND at.cease_date != '0000-00-00' THEN at.cease_date ELSE at.end_date END)
 GROUP BY at.ref_number, at.scheme_id, TRIM(LEADING '0' FROM at.folio_no), at.client_id, at.broker_id
);
select * from sip_report_client;
end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_summary_detail` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(500))  NO SQL begin
SET @sql = '';
if(familyID !='') then
  select
	sum(mfs.Purchase_Value) as Purchase_Value,
	sum(mfs.value) as value,
   DATE(CreatedDTStamp) as SummaryDate  ,
    f.family_id
  from mutual_fund_monthly_summary mfs
    inner join clients c 
            on mfs.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    where 
		f.broker_id =brokerID AND 
		f.family_id = familyID 
         and 
      (case when clientID!='' and FIND_IN_SET(c.client_id,clientID) 
       then 1 
            when clientID='' then 1 
            else 0 end
             )=1
     group by f.family_id,DATE(CreatedDTStamp)
	order by  CreatedDTStamp asc;	
else
  
    select
	mfs.Purchase_Value,
	mfs.value,
     DATE(CreatedDTStamp) as SummaryDate,
    DATE_FORMAT(`CreatedDTStamp` ,'%M-%Y') as DisplayDate
  from mutual_fund_monthly_summary mfs
    inner join clients c 
            on mfs.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    where 
		f.broker_id = brokerID AND 
		c.client_id = clientID  
    order by CreatedDTStamp asc;
	end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_summary_detail_historical` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(10), IN `clientID` VARCHAR(30), IN `reportDate` DATE)  NO SQL begin
SET @sql = '';
if(familyID !='') then
  select
	sum(mfs.Purchase_Value) as Purchase_Value,
	sum(mfs.value) as value,
   DATE(CreatedDTStamp) as SummaryDate  ,
    f.family_id
  from mutual_fund_monthly_summary mfs
    inner join clients c 
            on mfs.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    where 
		f.broker_id =brokerID AND 
		f.family_id = familyID 
        and  DATE(CreatedDTStamp) <= reportDate
         and 
      (case when clientID!='' and FIND_IN_SET(c.client_id,clientID) 
       then 1 
            when clientID='' then 1 
            else 0 end
             )=1
             group by f.family_id,DATE(CreatedDTStamp)
	order by  CreatedDTStamp asc;	
else
  
    select
	mfs.Purchase_Value,
	mfs.value,
     DATE(CreatedDTStamp) as SummaryDate,
    DATE_FORMAT(`CreatedDTStamp` ,'%M-%Y') as DisplayDate
  from mutual_fund_monthly_summary mfs
    inner join clients c 
            on mfs.client_id = c.client_id
	inner join families f 
		on f.family_id=c.family_id
    where 
		f.broker_id = brokerID AND 
		c.client_id = clientID  
        and  DATE(CreatedDTStamp) <= reportDate
    order by CreatedDTStamp asc;
	end if;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_unit` (IN `schemeID` VARCHAR(100), IN `folioNumber` VARCHAR(100), IN `brokerID` VARCHAR(10))  NO SQL begin
	DECLARE unit_done, divR2_done, divDiv_done, divDivPay_done BOOLEAN DEFAULT FALSE;
	declare mfSchemeType varchar(30) default '';
	declare pAmt decimal(30, 2) default 0.00;
	declare pNav decimal(18, 4) default 0.00;
	declare liveUnit decimal(18, 4) default 0.00;
	declare cNav decimal(18, 2) default 0.00;
	declare divR2 decimal(30, 10) default 0.00;
	declare divAmt decimal(30, 2) default 0.00;
	declare divPay decimal(30, 10) default 0.00;
  declare transID int;
	declare pDate date;
	declare cNavDate date;
	set @initialAmount = 0;
    set @unit_value = 0;
	set @final_unit_value = 0;
	#Calculate Unit Per Count
	BLOCK1: begin
	declare unit_cur cursor for 
    select mft.mutual_fund_type, mft.nav, mft.amount, mft.purchase_date, mft.transaction_id 
    from mutual_fund_transactions mft 
    where mft.mutual_fund_scheme = schemeID and mft.folio_number = folioNumber and mft.mutual_fund_type IN ('DIV') 
    and mft.broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET unit_done = TRUE;
	OPEN unit_cur; 
	unit_loop: LOOP
	fetch from unit_cur into mfSchemeType, pNav, pAmt, pDate, transID;
		IF unit_done THEN
    set unit_done = false;
		CLOSE unit_cur;
		LEAVE unit_loop;
		END IF;
		if(mfSchemeType = 'DIV') then
			select sum(quantity) from mutual_fund_transactions where (purchase_date < pDate) and mutual_fund_scheme = schemeID 
			and folio_number = folioNumber and broker_id = brokerID and mutual_fund_type IN ('SWO','RED') into @redAmount;
			select sum(quantity) from mutual_fund_transactions where (purchase_date < pDate) and mutual_fund_scheme = schemeID
			and folio_number = folioNumber and transaction_type = 'Purchase' and broker_id = brokerID into @initialAmount;
			if @redAmount is NULL then
				set @redAmount = 0;
			end if;
            set @initialAmount = @initialAmount - @redAmount;
			set @unit_value = pAmt / @initialAmount;
      /*set @unit_value = (pAmt * pNav) / @initialAmount;
			select mfSchemeType, pNav, pAmt, divAmt, pDate, transID, @unit_value, pAmt, pNav, (pAmt * pNav), @initialAmount;
    select @redAmount,@initialAmount,@unit_value;*/
			/*set @initialAmount = @initialAmount + pAmt;
			set @final_unit_value = @final_unit_value + @unit_value;*/
			update mutual_fund_valuation set unit_per_count = @unit_value where transaction_id = transID and broker_id = brokerID;
		end if;
	END LOOP unit_loop;
	end BLOCK1;
	#Calculate For divR2
	BLOCK2: begin
	declare divR2_cur cursor for 
    select mfv.transaction_id from mutual_fund_valuation mfv 
    inner join mutual_fund_transactions mft on mft.transaction_id = mfv.transaction_id 
    where mft.mutual_fund_scheme = schemeID and mft.folio_number = folioNumber 
    and mfv.broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divR2_done = TRUE;
	OPEN divR2_cur; 
	divR2_loop: LOOP
	fetch from divR2_cur into transID;
		IF divR2_done THEN
    set divR2_done = false;
		CLOSE divR2_cur;
		LEAVE divR2_loop;
		END IF;
		select live_unit from mutual_fund_valuation where transaction_id = transID and broker_id = brokerID into @liveUnit_r;
		select sum(unit_per_count) from mutual_fund_valuation mfv 
    inner join mutual_fund_transactions mft on mft.transaction_id = mfv.transaction_id 
    where mft.mutual_fund_scheme = schemeID and mft.folio_number = folioNumber 
    and mfv.broker_id = brokerID and mfv.transaction_id > transID into @unit_sum;
		if @liveUnit_r is NULL then
			set @liveUnit_r = 0;
		end if;
		if @unit_sum is NULL then
			set @unit_sum = 0;
		end if;
		set @divR = @liveUnit_r * @unit_sum;
		update mutual_fund_valuation set div_r2 = @divR where transaction_id = transID and broker_id = brokerID;
	END LOOP divR2_loop;
	end BLOCK2;
	#Calculate Dividend Payout
	BLOCK3: begin
	declare divDivPay_cur cursor for 
    select mft.mutual_fund_type, mft.amount, mft.purchase_date, mfv.transaction_id, mfv.live_unit 
    from mutual_fund_valuation mfv 
    inner join mutual_fund_transactions mft on mft.transaction_id = mfv.transaction_id 
    where mft.mutual_fund_scheme = schemeID and mft.folio_number = folioNumber 
    and mfv.broker_id = brokerID 
    and mft.mutual_fund_type IN ('PIP','SWI','IPO','TIN','DIV','NFO');
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divDivPay_done = TRUE;
	OPEN divDivPay_cur; 
	divDivPay_loop: LOOP
	fetch from divDivPay_cur into mfSchemeType, pAmt, pDate, transID, liveUnit;
		IF divDivPay_done THEN
    set divDivPay_done = false;
		CLOSE divDivPay_cur;
		LEAVE divDivPay_loop;
		END IF;
		select sum(DPO_units) from mutual_fund_transactions where mutual_fund_scheme = schemeID and folio_number = folioNumber and 
		purchase_date > pDate and broker_id = brokerID into @totalDPOUnit;
		update mutual_fund_valuation set div_payout = @totalDPOUnit * liveUnit where transaction_id = transID and 
		broker_id = brokerID;
	END LOOP divDivPay_loop;
	end BLOCK3;
	#Calculate div_amount, transDay, mf_abs, mf_cagr
	BLOCK4: begin
	declare divDiv_cur cursor for 
    select mft.mutual_fund_type, mft.amount, mft.nav, mft.purchase_date, mfv.transaction_id, mfv.c_nav, mfv.c_nav_date, mfv.div_r2,
	mfv.div_amount, mfv.div_payout, mfv.live_unit 
    from mutual_fund_valuation mfv 
    inner join mutual_fund_transactions mft on mft.transaction_id = mfv.transaction_id 
    where mft.mutual_fund_scheme = schemeID and mft.folio_number = folioNumber 
    and mfv.broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divDiv_done = TRUE;
	OPEN divDiv_cur; 
	divDiv_loop: LOOP
	fetch from divDiv_cur into mfSchemeType, pAmt, pNav, pDate, transID, cNav, cNavDate, divR2, divAmt, divPay, liveUnit;
		IF divDiv_done THEN
    set divDiv_done = false;
		CLOSE divDiv_cur;
		LEAVE divDiv_loop;
		END IF;
		if(mfSchemeType = 'DIV') then
			update mutual_fund_valuation set div_amount = pAmt, p_amount=0 where transaction_id = transID and broker_id = brokerID;
            /*update mutual_fund_valuation set div_amount = pAmt where transaction_id = transID and broker_id = brokerID;*/
		end if;
		set @transDay = DATEDIFF(cNavDate, pDate);
		update mutual_fund_valuation set transaction_day = @transDay where transaction_id = transID and broker_id = brokerID;
		if pAmt is NULL then
			set pAmt = 0;
		end if;
		if divR2 is null then
			set divR2 = 0;
		end if;
		if divPay is NULL then
			set divPay = 0;
		end if;
		if divAmt is NULL then
			set divAmt = 0;
		end if;
		/*set @abs = (((liveUnit * cNav) + divR2 + divPay) * 100) / ((liveUnit * pNav) + (divAmt * pNav));*/
		set @abs = (((liveUnit * cNav) + divR2 + divPay) * 100) / (pAmt);
		update mutual_fund_valuation set mf_abs = @abs - 100 where transaction_id = transID and broker_id = brokerID;
		if @transDay > 365 then
			set @powerval = @transDay / 365;
			/*set @mf_cagr = power((((liveUnit * cNav) + divR2 + divPay) / ((liveUnit * pNav) + (divAmt * pNav))), ((1 / (@transDay/365))))-1;*/
			set @mf_cagr = power((((liveUnit * cNav) + divR2 + divPay) / (pAmt)), ((1 / (@transDay/365))))-1;
      update mutual_fund_valuation set mf_cagr = @mf_cagr * 100 where transaction_id = transID and broker_id = brokerID;
		else
			update mutual_fund_valuation set mf_cagr = ((@abs - 100)/ @transDay) * 365 where transaction_id = transID and 
			broker_id = brokerID;
		end if;
    #select transID,pAmt*cNav+divR2+divPay,((pAmt*pNav)+(divAmt*pNav)),@transDay,(@abs-100),((@abs-100)/@transDay);
	END LOOP divDiv_loop;
	end BLOCK4;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_update_c_nav` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN 

IF brokerID = 'all' THEN

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
inner join mf_schemes_current_nav t1
on t.mutual_fund_scheme=t1.scheme_id
SET v.c_nav = current_nav, 
    v.c_nav_date = scheme_date      
WHERE 1;

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
v.mf_abs = ((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 

WHERE 1; 

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.mf_cagr = round((CASE WHEN v.transaction_day > 365 THEN 
				(power(
                (abs(IFNULL((v.mf_abs+100),0)/100)), 
                abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
            ELSE 
            ((v.mf_abs)/v.transaction_day)*365 
            END),2)
WHERE 1;
ELSE 

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
inner join mf_schemes_current_nav t1
on t.mutual_fund_scheme=t1.scheme_id
SET v.c_nav = current_nav, 
    v.c_nav_date = scheme_date                      
WHERE v.broker_id = brokerID;

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
v.mf_abs = round(((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100),2) ,
v.mf_cagr = round((CASE WHEN DATEDIFF(v.c_nav_date, t.purchase_date) > 365 THEN 
				(power(
                (abs(IFNULL((((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100)+100),0)/100)), 
                abs(IFNULL((1/(DATEDIFF(v.c_nav_date, t.purchase_date)/365)),0)))-1)*100 
            ELSE 
            ((((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100))/DATEDIFF(v.c_nav_date, t.purchase_date))*365 
            END),2) 
WHERE v.broker_id = brokerID;

END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_update_c_nav_Backup` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN 

IF brokerID = 'all' THEN

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.c_nav = (SELECT current_nav 
                      FROM mf_schemes_current_nav 
                      WHERE scheme_id = t.mutual_fund_scheme 
                      ORDER BY scheme_date DESC LIMIT 1), 
    v.c_nav_date = (SELECT MAX(scheme_date) 
                      FROM mf_schemes_current_nav 
                      WHERE scheme_id = t.mutual_fund_scheme 
                      GROUP BY scheme_id) 
WHERE 1;

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
v.mf_abs = ((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
WHERE 1; 

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.mf_cagr = round((CASE WHEN v.transaction_day > 365 THEN 
				(power(
                (abs(IFNULL((v.mf_abs+100),0)/100)), 
                abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
            ELSE 
            ((v.mf_abs)/v.transaction_day)*365 
            END),2)
WHERE 1;
ELSE 

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.c_nav = (SELECT current_nav 
                      FROM mf_schemes_current_nav 
                      WHERE scheme_id = t.mutual_fund_scheme 
                      ORDER BY scheme_date DESC LIMIT 1), 
    v.c_nav_date = (SELECT MAX(scheme_date) 
                      FROM mf_schemes_current_nav 
                      WHERE scheme_id = t.mutual_fund_scheme 
                      GROUP BY scheme_id) 
WHERE v.broker_id = brokerID;

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
v.mf_abs = ((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
WHERE v.broker_id = brokerID;

UPDATE mutual_fund_valuation v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.mf_cagr = round((CASE WHEN v.transaction_day > 365 THEN 
				(power(
                (abs(IFNULL((v.mf_abs+100),0)/100)), 
                abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
            ELSE 
            ((v.mf_abs)/v.transaction_day)*365 
            END),2) 
WHERE v.broker_id = brokerID;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_update_c_nav_delete_op_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE, IN `clientID` VARCHAR(30))  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_delete_op_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.c_nav = (SELECT current_nav 
                      FROM mf_schemes_histories 
                      WHERE scheme_id = t.mutual_fund_scheme 
					  AND scheme_date <="',reportDate,'" 
                      ORDER BY scheme_date DESC LIMIT 1), 
    v.c_nav_date = (SELECT MAX(scheme_date) 
                      FROM mf_schemes_histories 
                      WHERE scheme_id = t.mutual_fund_scheme 
					  AND scheme_date <="',reportDate,'"
                      GROUP BY scheme_id) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_delete_op_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
	v.mf_abs = ((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_delete_op_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.mf_cagr = (CASE WHEN v.transaction_day > 365 THEN 
				(power(
                (abs(IFNULL((v.mf_abs+100),0)/100)), 
                abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
            ELSE 
            ((v.mf_abs)/v.transaction_day)*365 
            END) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

delete v1.* FROM mutual_fund_valuation v1
inner join mutual_fund_transactions t1 on v1.transaction_id=t1.transaction_id
where t1.client_id=clientID;


SET @sql_insert = CONCAT('INSERT INTO mutual_fund_valuation(transaction_id, c_nav, c_nav_date, live_unit, unit_per_count, div_r2, div_payout, div_amount, p_amount, transaction_day, mf_abs, mf_cagr, broker_id, added_on)
SELECT  transaction_id, c_nav, c_nav_date, live_unit, unit_per_count, div_r2, div_payout, div_amount, p_amount, transaction_day, mf_abs, mf_cagr, broker_id, added_on 
FROM mutual_fund_valuation_delete_op_',brokerID,' 
WHERE broker_id = "',brokerID,'" 
ORDER BY valuation_id;');
IF(@sql_insert != '') THEN
  	PREPARE stmt4 FROM @sql_insert;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;





END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_update_c_nav_historical` (IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL BEGIN 
SET @qry1 = '';
SET @qry2 = '';
SET @qry3 = '';



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_h_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.c_nav = (SELECT current_nav 
                      FROM mf_schemes_histories 
                      WHERE scheme_id = t.mutual_fund_scheme 
					  AND scheme_date <="',reportDate,'" 
                      ORDER BY scheme_date DESC LIMIT 1), 
    v.c_nav_date = (SELECT MAX(scheme_date) 
                      FROM mf_schemes_histories 
                      WHERE scheme_id = t.mutual_fund_scheme 
					  AND scheme_date <="',reportDate,'"
                      GROUP BY scheme_id) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_h_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.transaction_day = DATEDIFF(v.c_nav_date, t.purchase_date), 
	v.mf_abs = ((((v.live_unit * v.c_nav + IFNULL(v.div_r2,0) + IFNULL(v.div_payout,0)) * 100) / (v.live_unit * t.nav))-100) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;



SET @qry1 = CONCAT('
UPDATE mutual_fund_valuation_h_',brokerID,'  v 
INNER JOIN mutual_fund_transactions t 
ON v.transaction_id = t.transaction_id 
SET v.mf_cagr = (CASE WHEN v.transaction_day > 365 THEN 
				(power(
                (abs(IFNULL((v.mf_abs+100),0)/100)), 
                abs(IFNULL((1/(v.transaction_day/365)),0)))-1)*100 
            ELSE 
            ((v.mf_abs)/v.transaction_day)*365 
            END) 
WHERE v.broker_id = "',brokerID,'";');

IF(@qry1 != '') THEN
  	PREPARE stmt1 FROM @qry1;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation` (IN `brokerID` VARCHAR(10), IN `transID` BIGINT UNSIGNED)  NO SQL begin
DECLARE mf_dp_done, mf_unit_done BOOLEAN DEFAULT FALSE;
declare schemeID int default 0;
declare folioNo varchar(50) default '';
declare liveUnit decimal(20,4) default 0.00;
declare dpoLive decimal(18,4) default 1.00;
declare dpoAmt decimal(18,2) default 0.00;
declare dpoDate date;
declare dpoTransID bigint default 0;
declare dpoUnit decimal(18,4) default 0.00;
if transID IS NULL OR transID = '' then 
	set transID = 0;
end if;
BLOCK1: begin
	declare mf_dp cursor for select `transaction_id`, `mutual_fund_scheme`, `folio_number`,`amount`,`purchase_date` from `mutual_fund_transactions` 
    where `mutual_fund_type` in ('DP') and broker_id = brokerID 
    and `transaction_id` > transID order by `purchase_date`;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_dp_done = TRUE;
	OPEN mf_dp;
	mf_dp_loop: LOOP
	fetch from mf_dp into dpoTransID, schemeID, folioNo, dpoAmt, dpoDate;
       	if mf_dp_done then
	        set mf_dp_done = false;
			close mf_dp;
			leave mf_dp_loop;
		end if;
        #main functionality of mf_dp starts from here
		set dpoLive = 1;
		/*select SUM(mfv.live_unit) from `mutual_fund_valuation` mfv 
        INNER JOIN `mutual_fund_transactions` mft ON mft.transaction_id = mfv.transaction_id 
        where mft.`mutual_fund_type` in ('PIP','IPO','DIV','SWI','TIN','NFO') 
        and mft.`purchase_date` < dpoDate 
		and mft.`mutual_fund_scheme` = schemeID 
        and mft.`folio_number` = folioNo and mfv.`broker_id` = brokerID 
        and mfv.`live_unit` > 0 into dpoLive;  */
        select SUM(mft.quantity) from `mutual_fund_transactions` mft 
        where mft.`mutual_fund_type` in ('PIP','IPO','DIV','SWI','TIN','NFO') 
        and mft.`purchase_date` < dpoDate 
		and mft.`mutual_fund_scheme` = schemeID 
        and mft.`folio_number` = folioNo and mft.`broker_id` = brokerID 
        into @purAmt;
        if @purAmt IS NULL then
        	set @purAmt = 0;
        end if;
        select SUM(mft.quantity) from `mutual_fund_transactions` mft 
        where mft.`mutual_fund_type` in ('RED','SWO') 
        and mft.`purchase_date` < dpoDate 
		and mft.`mutual_fund_scheme` = schemeID 
        and mft.`folio_number` = folioNo and mft.`broker_id` = brokerID 
        into @redAmt;
        if @redAmt IS NULL then
        	set @redAmt = 0;
        end if;
        set dpoLive = @purAmt - @redAmt;            
		if dpoLive is null or dpoLive < 0
		then
			set dpoUnit = 0;
		else
			set dpoUnit = dpoAmt/dpoLive;
        end if;
        /*select dpoAmt,dpolive,dpoUnit,dpodate;*/
        update `mutual_fund_transactions` set `DPO_units` = dpoUnit 
        where `transaction_id` = dpoTransID;
    end loop mf_dp_loop;
end BLOCK1;
BLOCK2: Begin
	#Procedure to get cNav, perUnitCount, Div_R2, Div Payout, CAGR, ABS etc
	declare mf_unit cursor for 
    select distinct mft.mutual_fund_scheme, mft.folio_number  
    from mutual_fund_valuation mfv 
    inner join mutual_fund_transactions mft on mft.transaction_id = mfv.transaction_id
    where mfv.broker_id = brokerID and mfv.transaction_id > transID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_unit_done = TRUE;
	OPEN mf_unit; 
    mf_unit_loop: LOOP
    fetch from mf_unit into schemeID, folioNo;
		IF mf_unit_done THEN
        set mf_unit_done = false;
        CLOSE mf_unit;
        LEAVE mf_unit_loop;
        END IF;
    #update current Nav & date
    select `current_nav`, `scheme_date` 
    from `mutual_fund_schemes` as mfs 
	  left join `mf_schemes_histories` as mfsh on mfsh.scheme_id = mfs.scheme_id 
    where mfs.`scheme_id` = schemeID 
    ORDER BY `mfsh`.`scheme_date` DESC LIMIT 1 
    into @cNav, @cNavDate;
    UPDATE `mutual_fund_valuation` mfv 
    INNER JOIN `mutual_fund_transactions` mft ON mfv.transaction_id = mft.transaction_id 
    SET c_nav = @cNav, c_nav_date = @cNavDate 
    WHERE mft.`mutual_fund_scheme` = schemeID AND mft.`folio_number` = folioNo 
    AND mfv.broker_id = brokerID;
    /*UPDATE `mutual_fund_valuation` SET current_value = (cNav*liveUnit) 
    WHERE transaction_id = transID;*/
    /* now we call unit procedure*/
	call sp_mf_unit(schemeID, folioNo, brokerID);
	END LOOP mf_unit_loop;
end BLOCK2;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation_broker_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
DECLARE mf_red_done, mf_pur_done, mf_dp_done, mf_unit_done BOOLEAN DEFAULT FALSE;
declare schemeID int default 0;
declare folioNo varchar(50) default '';
declare pQty decimal(18, 2) default 0.00;
declare mfSchemeType varchar(20) default '';
declare clientID varchar(30) default '';
declare purDate date;
declare pNav decimal(18,4) default 0.00;
declare dpoLive decimal(18,4) default 1.00;
declare dpoAmt decimal(18,2) default 0.00;
declare dpoDate date;
declare dpoTransID bigint default 0;
declare dpoUnit decimal(18,4) default 0.00;
    BLOCK1: begin
    declare mf_red cursor for select distinct `mutual_fund_scheme`, `folio_number` from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id  where
    `c.family_id` = familyID and `broker_id` = brokerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_red_done = TRUE;
    OPEN mf_red;
    mf_red_loop: LOOP
    fetch from mf_red into schemeID, folioNo;
        if mf_red_done then
        close mf_red;
        leave mf_red_loop;
        end if;
                set @liveUnits = 0;
        select SUM(quantity) from `mutual_fund_transactions` where `mutual_fund_type` in ('SWO', 'RED')
        and `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID into @redAmount;       
        if @redAmount is null
        then
            set @redAmount = 0;
        end if;
        set @redAmount = -@redAmount;
        BLOCK2:begin
        declare mf_pur cursor for select `mutual_fund_type`, `quantity`, `nav`, `purchase_date`, client_id from
        `mutual_fund_transactions` where `mutual_fund_type` IN ('PIP','SWI','DIV','IPO','TIN') and
        `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID order by `purchase_date`,`mutual_fund_type`;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_pur_done = TRUE;
        set @liveUnits = @redAmount;
        OPEN mf_pur;
        mf_pur_loop: LOOP
        fetch from mf_pur into mfSchemeType, pQty, pNav, purDate, clientID;
            IF mf_pur_done THEN
            set mf_pur_done = false;
            CLOSE mf_pur;
            LEAVE mf_pur_loop;
            END IF;
                        select `current_nav`, `scheme_date`, `scheme_type`, `scheme_name` from `mutual_fund_schemes` as mfs
            left join `mf_schemes_histories` as mfsh on mfsh.scheme_id = mfs.scheme_id left join `mf_scheme_types` as mft
            on mfs.`scheme_type_id` = mft.`scheme_type_id` where mfs.`scheme_id` = schemeID ORDER BY `mfsh`.`scheme_date` DESC LIMIT 1 into @cNav,
            @cNavDate, @schemeType, @schemeName;
            if @liveUnits <= 0
            then
                set @liveUnits = @liveUnits + pQty;
            else
                set @liveUnits = pQty;
            end if;
            insert into mf_valuation_reports (mf_scheme_name, scheme_id, folio_number, purchase_date, mf_scheme_type,
            p_amount, p_nav, c_nav, c_nav_date, live_unit, scheme_type, client_id, broker_id, current_value) values
            (@schemeName, schemeID, folioNo, purDate, mfSchemeType, pQty, pNav, @cNav, @cNavDate, @liveUnits, @schemeType,
            clientID, brokerID, @cNav*@liveUnits);
        END LOOP mf_pur_loop;
        end BLOCK2;
        BLOCK3: begin
        declare mf_dp cursor for select `transaction_id`,`amount`,`purchase_date` from `mutual_fund_transactions` where `mutual_fund_type` in ('DP') and
        `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID order by `purchase_date`;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_dp_done = TRUE;
        OPEN mf_dp;
        mf_dp_loop: LOOP
        fetch from mf_dp into dpoTransID, dpoAmt, dpoDate;
            if mf_dp_done then
            set mf_dp_done = false;
            close mf_dp;
            leave mf_dp_loop;
            end if;
                        set dpoLive = 1;
            select SUM(live_unit) from `mf_valuation_reports` where `mf_scheme_type` in ('PIP','IPO','DIV','SWI','TIN','NFO') and `purchase_date` < dpoDate
            and `scheme_id` = schemeID and `folio_number` = folioNo and broker_id = brokerID and live_unit > 0 into dpoLive; 
            if dpoLive is null
            then
                set dpoUnit = 0;
            else
                set dpoUnit = dpoAmt/dpoLive;
            end if;
            update `mutual_fund_transactions` set `DPO_units` = dpoUnit
            where `transaction_id` = dpoTransID;
        end loop mf_dp_loop;
        end BLOCK3;
    end loop mf_red_loop;
    end BLOCK1;
  delete from mf_valuation_reports where live_unit<=0;
    BLOCK4: Begin
        declare mf_unit cursor for select distinct scheme_id, folio_number from mf_valuation_reports where broker_id = brokerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_unit_done = TRUE;
    OPEN mf_unit;
    mf_unit_loop: LOOP
    fetch from mf_unit into schemeID, folioNo;
        IF mf_unit_done THEN
        CLOSE mf_unit;
        LEAVE mf_unit_loop;
        END IF;
        set @liveUnits = 0;
        call sp_unit_price(schemeID, folioNo, brokerID);
    END LOOP mf_unit_loop;
    end BLOCK4;
    select * from mf_valuation_reports;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation_client` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin
DECLARE mf_red_done, mf_pur_done, mf_unit_done BOOLEAN DEFAULT FALSE;
declare schemeID int default 0;
declare folioNo varchar(50) default '';
declare pQty decimal(18, 2) default 0.00;
declare mfSchemeType varchar(20) default '';
declare purDate date;
declare pNav decimal(18, 2) default 0.00;
#delete mf valuation for broker
delete from `mf_valuation_reports` where `broker_id` = brokerID;
	BLOCK1: begin
	declare mf_red cursor for select distinct `mutual_fund_scheme`, `folio_number` from `mutual_fund_transactions` where 
	`client_id` = clientID and `broker_id` = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_red_done = TRUE;
  set @liveUnits = 0;
	OPEN mf_red;
	mf_red_loop: LOOP
	fetch from mf_red into schemeID, folioNo;
		if mf_red_done then
		close mf_red;
		leave mf_red_loop;
		end if;
		#main functionality of mf_red starts from here
		set @liveUnits = 0;
		select SUM(quantity) from `mutual_fund_transactions` where `mutual_fund_type` in ('SWO', 'RED') 
		and `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID into @redAmount;        
		if @redAmount is null
		then
			set @redAmount = 0;
		end if;
		set @redAmount = -@redAmount;
    #select @redAmount as red;
		BLOCK2:begin
		declare mf_pur cursor for select `mutual_fund_type`, `quantity`, `nav`, `purchase_date` from `mutual_fund_transactions`
		where `mutual_fund_type` IN ('PIP','SWI','DIV','IPO','TIN') and `mutual_fund_scheme` = schemeID and 
		`folio_number` = folioNo and broker_id = brokerID order by `purchase_date`,`mutual_fund_type`;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_pur_done = TRUE;
		set @liveUnits = @redAmount;
    OPEN mf_pur; 
        mf_pur_loop: LOOP
        fetch from mf_pur into mfSchemeType, pQty, pNav, purDate;
			IF mf_pur_done THEN
            set mf_pur_done = false;
            CLOSE mf_pur;
            LEAVE mf_pur_loop;
            END IF; 
            #main functionality of mf_pur starts from here
            select `current_nav`, `scheme_date`, `scheme_type`, `scheme_name` from `mutual_fund_schemes` as mfs 
			left join `mf_schemes_histories` as mfsh on mfsh.scheme_id = mfs.scheme_id left join `mf_scheme_types` as mft 
			on mfsh.`scheme_type_id` = mft.`scheme_type_id` where mfs.`scheme_id` = schemeID ORDER BY `mfsh`.`scheme_date` DESC LIMIT 1 
            into @cNav, @cNavDate, @schemeType, @schemeName;
      set @lu = @liveUnits;
			if @liveUnits <= 0
			then
				set @liveUnits = @liveUnits + pQty;
			else
				set @liveUnits = pQty;
            end if;
      #select @redAmount,@lu,@liveUnits,pQty;
			insert into mf_valuation_reports (mf_scheme_name, scheme_id, folio_number, purchase_date, mf_scheme_type, 
			p_amount, p_nav, c_nav, c_nav_date, live_unit, scheme_type, client_id, broker_id, current_value) values(@schemeName, schemeID, 
			folioNo, purDate, mfSchemeType, pQty, pNav, @cNav, @cNavDate, @liveUnits, @schemeType, clientID, brokerID, 
			@cNav*@liveUnits);            
		END LOOP mf_pur_loop;
		end BLOCK2;
		/*insert into mf_valuation_reports (mf_scheme_name, scheme_id, folio_number, purchase_date, mf_scheme_type, div_payout, 
		scheme_type, current_value, client_id, broker_id)
		select `scheme_name`, scheme_id, `folio_number`, `purchase_date`, `mutual_fund_type`, `amount`, `transaction_type`,
		0, `client_id`, `broker_id` from `mutual_fund_transactions` mft inner join `mutual_fund_schemes` mfs on 
		mft.`mutual_fund_scheme` = mfs.`scheme_id` where `mutual_fund_type` IN ('DP') and `mutual_fund_scheme` = schemeID and 
		`folio_number` = folioNo and `purchase_date` > (select MIN(purchase_date) from mf_valuation_reports where scheme_id = schemeID
		and `folio_number` = folioNo and `live_unit` > 0) and broker_id = brokerID;*/
	end loop mf_red_loop;
	end BLOCK1;
  delete from mf_valuation_reports where live_unit<=0;
	BLOCK3: Begin
	#Procedure to get perUnitCount and Div_R2
	declare mf_unit cursor for select distinct scheme_id, folio_number from mf_valuation_reports where broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_unit_done = TRUE;
	OPEN mf_unit; 
    mf_unit_loop: LOOP
    fetch from mf_unit into schemeID, folioNo;
		IF mf_unit_done THEN
        CLOSE mf_unit;
        LEAVE mf_unit_loop;
        END IF;
		set @liveUnits = 0;
		call sp_unit_price(schemeID, folioNo, brokerID);
	END LOOP mf_unit_loop;
	end BLOCK3;
    create temporary table if not exists `mf_report_client` as 
    (select mf_scheme_name, name as client_name, mfv.client_id, folio_number, Date_format(purchase_date, '%d/%m/%Y') as purchase_date, mf_scheme_type, p_amount, div_amount, p_nav, live_unit, transaction_day, c_nav, Date_format(c_nav_date, '%d/%m/%Y') as c_nav_date, div_r2, div_payout, cagr, mf_abs, current_value from mf_valuation_reports mfv inner join clients c on mfv.client_id = c.client_id where mfv.broker_id = brokerID order by mf_scheme_name, folio_number, mfv.purchase_date);
    select * from mf_report_client;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))  NO SQL begin
DECLARE mf_red_done, mf_pur_done, mf_unit_done BOOLEAN DEFAULT FALSE;
declare schemeID int default 0;
declare folioNo varchar(50) default '';
declare pQty decimal(18, 2) default 0.00;
declare mfSchemeType varchar(20) default '';
declare clientID varchar(30) default '';
declare purDate date;
declare pNav decimal(18, 2) default 0.00;
delete from `mf_valuation_reports` where `broker_id` = brokerID;
    BLOCK1: begin
    declare mf_red cursor for select distinct `mutual_fund_scheme`, `folio_number` from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where
    `c.family_id` = familyID and `broker_id` = brokerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_red_done = TRUE;
    OPEN mf_red;
    mf_red_loop: LOOP
    fetch from mf_red into schemeID, folioNo;
        if mf_red_done then
        close mf_red;
        leave mf_red_loop;
        end if;
                set @liveUnits = 0;
        select SUM(quantity) from `mutual_fund_transactions` where `mutual_fund_type` in ('SWO', 'RED')
        and `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID into @redAmount;       
        if @redAmount is null
        then
            set @redAmount = 0;
        end if;
        set @redAmount = -@redAmount;
        BLOCK2:begin
        declare mf_pur cursor for select `mutual_fund_type`, `quantity`, `nav`, `purchase_date`, client_id from
        `mutual_fund_transactions` where `mutual_fund_type` IN ('PIP','SWI','DIV','IPO','TIN') and
        `mutual_fund_scheme` = schemeID and `folio_number` = folioNo and broker_id = brokerID order by `purchase_date`,`mutual_fund_type`;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_pur_done = TRUE;
        set @liveUnits = @redAmount;
        OPEN mf_pur;
        mf_pur_loop: LOOP
        fetch from mf_pur into mfSchemeType, pQty, pNav, purDate, clientID;
            IF mf_pur_done THEN
            set mf_pur_done = false;
            CLOSE mf_pur;
            LEAVE mf_pur_loop;
            END IF;
                        select `current_nav`, `scheme_date`, `scheme_type`, `scheme_name` from `mutual_fund_schemes` as mfs
            left join `mf_schemes_histories` as mfsh on mfsh.scheme_id = mfs.scheme_id left join `mf_scheme_types` as mft
            on mfsh.`scheme_type_id` = mft.`scheme_type_id` where mfs.`scheme_id` = schemeID ORDER BY `mfsh`.`scheme_date` DESC LIMIT 1 into @cNav,
            @cNavDate, @schemeType, @schemeName;
            if @liveUnits <= 0
            then
                set @liveUnits = @liveUnits + pQty;
            else
                set @liveUnits = pQty;
            end if;
            insert into mf_valuation_reports (mf_scheme_name, scheme_id, folio_number, purchase_date, mf_scheme_type,
            p_amount, p_nav, c_nav, c_nav_date, live_unit, scheme_type, client_id, broker_id, current_value) values
            (@schemeName, schemeID, folioNo, purDate, mfSchemeType, pQty, pNav, @cNav, @cNavDate, @liveUnits, @schemeType,
            clientID, brokerID, @cNav*@liveUnits);
        END LOOP mf_pur_loop;
        end BLOCK2;
    end loop mf_red_loop;
    end BLOCK1;
  delete from mf_valuation_reports where live_unit<=0;
    BLOCK3: Begin
        declare mf_unit cursor for select distinct scheme_id, folio_number from mf_valuation_reports where broker_id = brokerID;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET mf_unit_done = TRUE;
    OPEN mf_unit;
    mf_unit_loop: LOOP
    fetch from mf_unit into schemeID, folioNo;
        IF mf_unit_done THEN
        CLOSE mf_unit;
        LEAVE mf_unit_loop;
        END IF;
        set @liveUnits = 0;
        call sp_unit_price(schemeID, folioNo, brokerID);
    END LOOP mf_unit_loop;
    end BLOCK3;
    create temporary table if not exists `mf_report_family` as
    (select mf_scheme_name, f.name as family_name, c.family_id, c.name as client_name, mfv.client_id, folio_number, Date_format(purchase_date, '%d/%m/%Y') as purchase_date, mf_scheme_type, p_amount, div_amount, p_nav, live_unit, transaction_day, c_nav, Date_format(c_nav_date, '%d/%m/%Y') as c_nav_date, div_r2, div_payout, cagr, mf_abs, current_value from mf_valuation_reports mfv inner join clients c on mfv.client_id = c.client_id inner join families f on c.family_id = f.family_id where mfv.broker_id = brokerID order by c.report_order, c.name, mf_scheme_name, folio_number, mfv.purchase_date);
    select * from mf_report_family;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation_historical` (IN `brokerID` VARCHAR(10), IN `familyID` VARCHAR(30), IN `clientID` VARCHAR(30), IN `startDate` VARCHAR(20))  NO SQL begin
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_valuation_temp_h_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
SET @sql_create = CONCAT('CREATE TABLE `mf_val_valuation_temp_h_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    red_trans_id bigint,
    quantity decimal(18,4),
    mutual_fund_scheme  int,
    folio_number varchar(50),
    client_id varchar(30),                                          
    div_payout decimal(30,10),
    c_nav decimal(18,4),
    c_nav_date date,
    unit_per_count decimal(30,10),
    transaction_day int(11),
    mf_cagr decimal(18,2),
    mf_abs decimal(18,2),                    
    div_r2 decimal(30,10),
    p_amount decimal(30,12),
    div_amount decimal(30,12),                     
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`),
    INDEX `folio_number` (`folio_number`),
    INDEX `client_id` (`client_id`)
);');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
IF(familyID!="") then
    SET @sql_insert = CONCAT('INSERT INTO `mf_val_valuation_temp_h_',brokerID,'`(transaction_id, live_unit,quantity,mutual_fund_scheme,folio_number,client_id, broker_id)
    SELECT mft.transaction_id, mft.quantity,mft.quantity, mft.mutual_fund_scheme,mft.folio_number,mft.client_id, mft.broker_id FROM mutual_fund_transactions mft
      inner join  clients c on c.client_id=mft.client_id                                            
      WHERE mft.broker_id = "',brokerID,'"
            and c.family_id="',familyID,'"                                         
            and mft.transaction_type="Purchase"                 
      ORDER BY mft.transaction_id;');
    IF(@sql_insert != '') THEN
        PREPARE stmt4 FROM @sql_insert;
        EXECUTE stmt4;
        DEALLOCATE PREPARE stmt4;
    END IF;
else
	SET @sql_insert = CONCAT('INSERT INTO `mf_val_valuation_temp_h_',brokerID,'`(transaction_id, live_unit,quantity,mutual_fund_scheme,folio_number,client_id, broker_id)
    SELECT mft.transaction_id, mft.quantity,mft.quantity, mft.mutual_fund_scheme,mft.folio_number,mft.client_id, mft.broker_id FROM mutual_fund_transactions mft
      inner join  clients c on c.client_id=mft.client_id                                            
      WHERE mft.broker_id = "',brokerID,'"
            and c.client_id="',clientID,'"                                         
            and mft.transaction_type="Purchase"                 
      ORDER BY mft.transaction_id;');
    IF(@sql_insert != '') THEN
        PREPARE stmt4 FROM @sql_insert;
        EXECUTE stmt4;
        DEALLOCATE PREPARE stmt4;
    END IF;
end if;
SET @sql_update_first = CONCAT('UPDATE `mf_val_valuation_temp_h_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
SET vt.live_unit = vt.live_unit +
IFNULL((SELECT -(SUM(t2.quantity)) as units 
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption" 
	AND t2.purchase_date <= "',startDate,'"
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id
    AND t2.broker_id = mft.broker_id 
),0) WHERE vt.transaction_id =
    (SELECT MIN(transaction_id) FROM mutual_fund_transactions
     WHERE mutual_fund_scheme = mft.mutual_fund_scheme
     AND folio_number = mft.folio_number
     AND client_id = mft.client_id
     AND broker_id = mft.broker_id
     GROUP BY mutual_fund_scheme, folio_number, client_id
    )
AND mft.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt5 FROM @sql_update_first;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
SET @sql_update_all = CONCAT('UPDATE `mf_val_valuation_temp_h_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio = folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mf_val_valuation_temp_h_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id, a.mutual_fund_scheme, a.folio_number, a.purchase_date, a.valuation_id) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance 
WHERE vt1.live_unit != x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt6 FROM @sql_update_all;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;
/*
SET @sql_update_val = CONCAT('UPDATE mutual_fund_valuation v 
INNER JOIN `mf_val_temp_',brokerID,'` vt 
ON v.transaction_id = vt.transaction_id 
SET v.live_unit = vt.live_unit 
WHERE vt.live_unit != v.live_unit;');
IF(@sql_update_val != '') THEN
  	PREPARE stmt7 FROM @sql_update_val;
  	EXECUTE stmt7;
  	DEALLOCATE PREPARE stmt7;
END IF;
*/
SET @sql_delete_val = CONCAT('DELETE FROM `mf_val_valuation_temp_h_',brokerID,'` 
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete_val != '') THEN
  	PREPARE stmt9 FROM @sql_delete_val;
  	EXECUTE stmt9;
  	DEALLOCATE PREPARE stmt9;
END IF;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mf_valuation_temp` (IN `brokerID` VARCHAR(10))  NO SQL BEGIN
SET @sql_drop = '';
SET @sql_create = '';
SET @sql_insert = '';
SET @sql_update_first = '';
SET @sql_update_all = '';
SET @sql_delete = '';
/* drop temporary table if exists */
SET @sql_drop = CONCAT('DROP TABLE IF EXISTS `mf_val_temp_',brokerID,'`;');
IF(@sql_drop != '') THEN
  	PREPARE stmt1 FROM @sql_drop;
  	EXECUTE stmt1;
  	DEALLOCATE PREPARE stmt1;
END IF;
/* create new table for this broker */
SET @sql_create = CONCAT('CREATE TABLE `mf_val_temp_',brokerID,'` (
    valuation_id BIGINT NOT NULL AUTO_INCREMENT,
    transaction_id BIGINT NOT NULL,
    live_unit DECIMAL(18,4) DEFAULT NULL,
    broker_id VARCHAR(10) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`valuation_id`),
    INDEX `transaction_id` (`transaction_id`),
    INDEX `broker_id` (`broker_id`)
);');
IF(@sql_create != '') THEN
  	PREPARE stmt2 FROM @sql_create;
  	EXECUTE stmt2;
  	DEALLOCATE PREPARE stmt2;
END IF;
/* select all +ve purchases into temp table */
SET @sql_insert = CONCAT('INSERT INTO `mf_val_temp_',brokerID,'`(transaction_id, live_unit, broker_id)
SELECT transaction_id, quantity, broker_id FROM mutual_fund_transactions t
WHERE transaction_type = "Purchase"
AND broker_id = "',brokerID,'"
ORDER BY purchase_date asc, transaction_type asc, quantity desc, 
adjustment_ref_number asc, transaction_id asc;');
IF(@sql_insert != '') THEN
  	PREPARE stmt3 FROM @sql_insert;
  	EXECUTE stmt3;
  	DEALLOCATE PREPARE stmt3;
END IF;
/* update live_units for all 1st purchase transactions */
SET @sql_update_first = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt
INNER JOIN mutual_fund_transactions mft
ON vt.transaction_id = mft.transaction_id
SET vt.live_unit = vt.live_unit +
(SELECT -(IFNULL(SUM(t2.quantity),0)) as units
    FROM mutual_fund_transactions t2
    WHERE t2.transaction_type = "Redemption"
    AND t2.mutual_fund_scheme = mft.mutual_fund_scheme
    AND t2.folio_number = mft.folio_number
    AND t2.client_id = mft.client_id
    AND t2.broker_id = mft.broker_id
) WHERE vt.transaction_id =
    (SELECT MIN(transaction_id) FROM mutual_fund_transactions
     WHERE mutual_fund_scheme = mft.mutual_fund_scheme
     AND folio_number = mft.folio_number
     AND client_id = mft.client_id
     AND broker_id = mft.broker_id
     GROUP BY mutual_fund_scheme, folio_number, client_id
    )
AND mft.broker_id = "',brokerID,'";');
IF(@sql_update_first != '') THEN
  	PREPARE stmt4 FROM @sql_update_first;
  	EXECUTE stmt4;
  	DEALLOCATE PREPARE stmt4;
END IF;
/* update all remaining live units into */
SET @sql_update_all = CONCAT('UPDATE `mf_val_temp_',brokerID,'` vt1 JOIN (
SELECT 
 @bal := (case
             when @scheme = mutual_fund_scheme AND @folio := folio_number
    		 and @client = client_id and @bal < 0 then @bal + live_unit
         else live_unit
         end) as balance
, a.transaction_id
, @scheme := a.mutual_fund_scheme
, @folio := a.folio_number
, @client := a.client_id 
FROM
(
        select @bal := 0
             , @scheme := 0
             , @folio := 0
    		 , @client := 0 
    ) as init, 
(SELECT t1.valuation_id, t1.live_unit, mft1.* FROM `mf_val_temp_',brokerID,'` t1 
INNER JOIN mutual_fund_transactions mft1
ON t1.transaction_id = mft1.transaction_id
WHERE t1.broker_id = "',brokerID,'") as a
ORDER BY a.client_id, a.mutual_fund_scheme, a.folio_number, a.purchase_date, a.valuation_id) x 
ON vt1.transaction_id = x.transaction_id 
SET vt1.live_unit = x.balance;');
IF(@sql_update_all != '') THEN
  	PREPARE stmt5 FROM @sql_update_all;
  	EXECUTE stmt5;
  	DEALLOCATE PREPARE stmt5;
END IF;
/* delete all -ve live_unit records */
SET @sql_delete = CONCAT('DELETE FROM `mf_val_temp_',brokerID,'` 
                         WHERE broker_id = "',brokerID,'" 
                         AND live_unit <= 0;');
IF(@sql_delete != '') THEN
  	PREPARE stmt6 FROM @sql_delete;
  	EXECUTE stmt6;
  	DEALLOCATE PREPARE stmt6;
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_mutual_fund_summary_client_wise_import` (IN `brokerID` VARCHAR(10))  NO SQL begin 

    insert into mutual_fund_monthly_summary(Purchase_Value,value,client_id)
    select 
    SUM(mfv.p_amount)as Purchase_Value,
         SUM(ROUND((mfv.c_nav * mfv.live_unit))) AS value,
         mft.client_id
    from mutual_fund_valuation mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
	inner join families f on f.family_id=c.family_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where mfv.broker_id = brokerID           
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.client_id
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0;

    select 'Success' as result;
 end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_paidup` (IN `startDate` DATE)  NO SQL BEGIN
update `premium_calendar` set `paid_up` = (select `prem_amt` from `insurances` im where `premium_calendar`.`policy_num` = im.`policy_num`), `Jan`=null, `Feb` = null, `Mar` = null, 
`Apr` = null, `May` = null, `Jun` = null, `Jul` = null,
`Aug` = null, `Sep` = null, `Oct` = null, `Nov` = null,
`Dec`=null where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where pms.status='Paid Up' or (pms.status IN ('In Force','Grace') and im.paidup_date <= startDate));
update `premium_calendar` set `adjustment` = 1 where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where im.adjustment_flag='1');
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_premium_calender_client` (IN `month` VARCHAR(3), IN `start_date` DATE, IN `clientID` VARCHAR(30))  SQL SECURITY INVOKER BEGIN
if(month = 'Jan')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up', 
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec` 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Feb')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Mar')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Apr')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'May')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Jun')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Jul')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Aug')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Sep')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Oct')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Nov')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Dec')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id = clientID and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
end if;
/* used to show Inforce also as Paidup, so new update query below this..
update `premium_calendar` set `paid_up` = (select `prem_amt` from `insurances` im where `premium_calendar`.`policy_num` = im.`policy_num`), `Jan`=null, `Feb` = null, `Mar` = null, 
`Apr` = null, `May` = null, `Jun` = null, `Jul` = null,
`Aug` = null, `Sep` = null, `Oct` = null, `Nov` = null,
`Dec`=null where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where pms.status='Paid up' or (pms.status IN ('In Force','Grace') and im.paidup_date < Date_Add(start_date, interval 12 month))); */
update `premium_calendar` set `paid_up` = (select `prem_amt` from `insurances` im where `premium_calendar`.`policy_num` = im.`policy_num`), `Jan`=null, `Feb` = null, `Mar` = null, 
`Apr` = null, `May` = null, `Jun` = null, `Jul` = null,
`Aug` = null, `Sep` = null, `Oct` = null, `Nov` = null,
`Dec`=null where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where pms.status='Paid up');
update `premium_calendar` set `adjustment` = 1 where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where im.adjustment_flag='1');
#CALL sp_paidup(start_date);
select * from premium_calendar;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_premium_calender_family` (IN `month` VARCHAR(3), IN `start_date` DATE, IN `familyID` VARCHAR(30))  SQL SECURITY INVOKER BEGIN
if(month = 'Jan')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up', 
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec` 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Feb')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Mar')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Apr')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'May')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Jun')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Jul')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num 
inner join insurance_policies ip on ip.policy_num = i.policy_num 
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id 
inner join clients c on c.client_id = i.client_id 
inner join families f on f.family_id = c.family_id 
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Aug')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Sep')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Oct')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Nov')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
elseif(month = 'Dec')
then
create TEMPORARY TABLE IF NOT EXISTS `premium_calendar` AS
(select c.name, f.name as fam_name, ic.ins_comp_name, i.policy_num, adjustment,
0 as 'paid_up',
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
inner join families f on f.family_id = c.family_id
where (date_of_payment >= start_date and date_of_payment < Date_Add(start_date, interval 12 month) OR (date_of_payment < DATE_ADD(start_date, INTERVAL 12 month) AND i.status IN(select status_id from premium_status WHERE status = 'Paid Up'))) and i.client_id in (select client_id from clients where family_id = familyID) and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name);
end if;
#select * from premium_calendar;
/*  used to show Inforce also as Paidup, so new update query below this... 
update `premium_calendar` set `paid_up` = (select `prem_amt` from `insurances` im where `premium_calendar`.`policy_num` = im.`policy_num`), `Jan`=null, `Feb` = null, `Mar` = null, 
`Apr` = null, `May` = null, `Jun` = null, `Jul` = null,
`Aug` = null, `Sep` = null, `Oct` = null, `Nov` = null,
`Dec`=null where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where pms.status='Paid Up' or (pms.status IN ('In Force','Grace') and im.paidup_date < Date_Add(start_date, interval 12 month))); */
update `premium_calendar` set `paid_up` = (select `prem_amt` from `insurances` im where `premium_calendar`.`policy_num` = im.`policy_num`), `Jan`=null, `Feb` = null, `Mar` = null, 
`Apr` = null, `May` = null, `Jun` = null, `Jul` = null,
`Aug` = null, `Sep` = null, `Oct` = null, `Nov` = null,
`Dec`=null where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where pms.status='Paid Up');
update `premium_calendar` set `adjustment` = 1 where `policy_num` in (select im.policy_num from `insurances` im inner join `premium_status` pms on pms.status_id=im.status where im.adjustment_flag='1');
#CALL sp_paidup(start_date);
select * from premium_calendar;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_premium_calender_family_OLD` (IN `month` VARCHAR(3), IN `start_date` DATE, IN `familyID` VARCHAR(30))  SQL SECURITY INVOKER BEGIN
SET @sql = NULL;
if(month = 'Jan')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec` 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Feb')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Mar')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Apr')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'May')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Jun')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'July')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Aug')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Sep')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Oct')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Nov')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov,
sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
elseif(month = 'Dec')
then
SET @sql = concat("select name, i.policy_num, ic.ins_comp_name, adjustment, SubSTR(MONTHNAME(ppd.date_of_payment),1,3) as 'Payment Date', sum(if(month(ppd.date_of_payment) = 12, amount, 0)) AS `Dec`,
sum(if(month(ppd.date_of_payment) = 1, amount, 0))  AS Jan,
sum(if(month(ppd.date_of_payment) = 2, amount, 0))  AS Feb,
sum(if(month(ppd.date_of_payment) = 3, amount, 0))  AS Mar,
sum(if(month(ppd.date_of_payment) = 4, amount, 0))  AS Apr,
sum(if(month(ppd.date_of_payment) = 5, amount, 0))  AS May,
sum(if(month(ppd.date_of_payment) = 6, amount, 0))  AS Jun,
sum(if(month(ppd.date_of_payment) = 7, amount, 0))  AS Jul,
sum(if(month(ppd.date_of_payment) = 8, amount, 0))  AS Aug,
sum(if(month(ppd.date_of_payment) = 9, amount, 0))  AS Sep,
sum(if(month(ppd.date_of_payment) = 10, amount, 0)) AS Oct,
sum(if(month(ppd.date_of_payment) = 11, amount, 0)) AS Nov 
FROM `premium_paying_details` ppd 
inner join insurances i on i.policy_num = ppd.policy_num
inner join insurance_policies ip on ip.policy_num = i.policy_num
inner join ins_companies ic on ic.ins_comp_id = ip.ins_comp_id
inner join clients c on c.client_id = i.client_id
where date_of_payment between '",start_date,"' and Date_Add('",start_date,"', interval 12 month) and i.client_id = in (select client_id from clients where family_id = '",familyID,"') and i.status NOT IN(select status_id from premium_status where status IN('Matured','Surrender','Lapsed','Paid Up Cancellation'))
group by policy_num order by name");
end if;
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reminder` (IN `brokerID` VARCHAR(10), IN `scriptDate` DATE, IN `personalRem` INT, IN `insPremiumRem` INT, IN `insPremiumAmt` DECIMAL(18,2), IN `insGraceRem` INT, IN `insGraceAmt` DECIMAL(18,2), IN `insMaturityRem` INT, IN `insMaturityAmt` DECIMAL(18,2), IN `fdIntRem` INT, IN `fdIntAmt` DECIMAL(18,2), IN `fdMaturityRem` INT, IN `fdMaturityAmt` DECIMAL(18,2), IN `varAssetRem` INT, IN `varAssetAmount` DECIMAL(18,2))  NO SQL begin
Declare varRentAmount decimal(18,2);
select rent_amount into varRentAmount from reminder_days where broker_id=brokerId;
insert into today_reminders (reminder_type, client_id, client_name, broker_id, reminder_date, reminder_message) 
select 'Birthday Reminder', client_id, c.name, brokerID, CURRENT_DATE(), 
CONCAT('Many many happy returns of the day ', c.name, ' :)') 
from clients c inner join families fam on c.family_id = fam.family_id where dob_app = 1 and 
fam.broker_id = brokerID and 
(c.merge_ref_id IS NULL OR c.merge_ref_id = "") and 
DATE_FORMAT(scriptDate, '%d') = DAYOFMONTH(dob) and DATE_FORMAT(scriptDate, '%m') = MONTH(dob) union all 
select 'Anniversary Reminder', client_id, c.name, brokerID, CURRENT_DATE(), 
'Wishing you a happy Marriage Anniversary :)' 
from clients c inner join families fam on c.family_id = fam.family_id where anv_app = 1 and 
fam.broker_id = brokerID and 
(c.merge_ref_id IS NULL OR c.merge_ref_id = "") and 
DATE_FORMAT(scriptDate, '%d') = DAYOFMONTH(anv_date) and 
DATE_FORMAT(scriptDate, '%m') = MONTH(anv_date) union all 
select 'Premium Due', i.client_id, c1.name, brokerID, CURRENT_DATE(), 
Concat('Premium Rs. ', round(i.prem_amt), ' for ', ipm.plan_name, ', Policy Number: ', i.policy_num, ' is due on ', DATE_FORMAT(i.next_prem_due_date, '%d/%m/%Y')) 
from insurances i inner join clients c1 on c1.client_id = i.client_id INNER JOIN ins_plans ipm 
on i.plan_id = ipm.plan_id 
where DATEDIFF(i.next_prem_due_date, DATE_ADD(scriptDate, interval insPremiumRem day)) = 0 and 
i.prem_amt >= insPremiumAmt and i.broker_id = brokerID union all 
select 'Grace Date', i.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT('Premium Rs. ', round(i.prem_amt), ' for ', ipm.plan_name, ', Policy Number:', i.policy_num, ' has a grace days till ', DATE_FORMAT(i.grace_due_date, '%d/%m/%Y')) 
from insurances i inner join clients c1 on c1.client_id=i.client_id INNER JOIN ins_plans ipm on 
i.plan_id = ipm.plan_id 
where DateDiff(i.grace_due_date, DATE_ADD(scriptDate, interval insGraceRem day)) = 0  and 
i.prem_amt >= insGraceAmt and i.broker_id = brokerID union all 
select 'Insurance Maturity', c1.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(ipm.plan_name, ', Policy Number:', im.policy_num, ' is getting matured on: ', DATE_FORMAT(pm.maturity_date, '%d/%m/%Y')) 
from premium_maturities pm inner join insurances im on pm.policy_num = im.policy_num inner join 
clients c1 on c1.client_id = im.client_id INNER JOIN ins_plans ipm on im.plan_id = ipm.plan_id 
INNER JOIN ins_plan_types ipt on ipt.plan_type_id=im.plan_type_id
where datediff(pm.maturity_date, DATE_ADD(scriptDate, interval insMaturityRem day)) = 0 and 
(pm.amount >= insMaturityAmt || ipt.plan_type_name='General Insurance') and im.broker_id = brokerID union all 
select 'Fixed Income Maturity',c1.client_id, c1.name, brokerID, CURRENT_DATE(), CONCAT('Rs. ',round(fdt.maturity_amount),' Maturity amount is getting matured from ',fdc.fd_comp_name,', Ref. No.: ',fdt.ref_number,' on ',DATE_FORMAT(fdt.maturity_date, '%d/%m/%Y')) from fd_transactions fdt inner join clients c1 on fdt.client_id=c1.client_id INNER JOIN fd_companies fdc ON fdc.fd_comp_id=fdt.fd_comp_id where datediff(fdt.maturity_date, DATE_ADD(scriptDate, interval fdMaturityRem day)) = 0 and fdt.maturity_amount >= fdMaturityAmt and fdt.broker_id = brokerID union all  
select 'Fixed Income Payout', c1.client_id, c1.name, brokerID, CURRENT_DATE(), CONCAT('Rs. ',round(fdi.interest_amount),' ',fdt.interest_mode,' interest for ',fdit.fd_inv_type,' in  ',fdc.fd_comp_name,', Ref. No.: ',fdt.ref_number,' on ',DATE_FORMAT(fdi.interest_date, '%d/%m/%Y')) from fd_transactions fdt inner join fd_interests fdi on fdi.fd_transaction_id=fdt.fd_transaction_id inner join clients c1 on fdt.client_id=c1.client_id INNER JOIN fd_companies fdc ON fdc.fd_comp_id=fdt.fd_comp_id INNER JOIN fd_investment_types fdit ON fdit.fd_inv_id=fdt.fd_inv_id where datediff(fdi.interest_date, DATE_ADD(scriptDate, interval fdIntRem day)) = 0 and fdi.interest_amount >= fdIntAmt and fdt.broker_id = brokerID
union all 
select 'Asset', at.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' ', als.scheme_name, ' Rs. ', round(am.maturity_amount), ' is due on ', DATE_FORMAT(am.maturity_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from asset_transactions at inner join clients c1 on c1.client_id=at.client_id INNER JOIN asset_maturity am on 
am.asset_id = at.asset_id  left join al_products alp on
alp.product_id=at.product_id left join al_types alt on
alt.type_id=at.type_id left join mutual_fund_schemes als on
als.scheme_id=at.scheme_id
where DateDiff(am.maturity_date, DATE_ADD(scriptDate, interval varAssetRem day)) = 0  and 
am.maturity_amount >= varAssetAmount and at.broker_id = brokerID
union all 
select 'Liabilty', lt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' of ', alc.company_name,' ', als.scheme_name, ' Rs. ', round(lm.maturity_amount), ' is due on ', DATE_FORMAT(lm.maturity_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from liability_transactions lt inner join clients c1 on c1.client_id=lt.client_id INNER JOIN liability_maturity lm on 
lm.liability_id = lt.liability_id  left join al_products alp on
alp.product_id=lt.product_id left join al_types alt on
alt.type_id=lt.type_id left join mutual_fund_schemes als on
als.scheme_id=lt.scheme_id left join al_companies alc on
alc.company_id=lt.company_id 
where DateDiff(lm.maturity_date, DATE_ADD(scriptDate, interval varAssetRem day)) = 0  and 
lm.maturity_amount >= varAssetAmount and lt.broker_id = brokerID
union all 
select 'Rent', pt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(pt.property_name,' ', pt.property_location,' rent of Rs. ', round(prd.amount), ' is due on ', DATE_FORMAT(prd.rent_date, '%d/%m/%Y'))
from property_transactions pt inner join clients c1 on c1.client_id=pt.client_id INNER JOIN property_rents pr on 
pr.pro_transaction_id = pt.pro_transaction_id  inner join property_rent_details prd on
prd.rent_id=pr.pro_rent_id
where DateDiff(prd.rent_date,scriptDate) = 0  and 
prd.amount >= varRentAmount and pt.broker_id = brokerID
union all
select * from (select 'Last Rent Reminder', pt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(pt.property_name,' ', pt.property_location,' rent of Rs. ', round(prd.amount), ' is due on ', DATE_FORMAT(max(prd.rent_date), '%d/%m/%Y')) as str 
from property_transactions pt inner join clients c1 on c1.client_id=pt.client_id inner join property_rent_details prd on
prd.pro_transaction_id=pt.pro_transaction_id
where prd.amount >= varRentAmount and pt.broker_id = brokerID and 
pt.pro_transaction_id in (select prd2.pro_transaction_id from property_rent_details prd2 group by (prd2.pro_transaction_id) having max(prd2.rent_date)=scriptDate)) as lrent where str is not null 
union all
select 'Last Asset', at.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' of ', alc.company_name,' ', als.scheme_name, ' Rs. ', round(at.installment_amount), ' is due on ', DATE_FORMAT(at.end_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from asset_transactions at inner join clients c1 on 
c1.client_id=at.client_id  inner join al_products alp on
alp.product_id=at.product_id inner join al_types alt on
alt.type_id=at.type_id left join mutual_fund_schemes als on
als.scheme_id=at.scheme_id inner join al_companies alc on
alc.company_id=at.company_id 
where at.installment_amount >= varAssetAmount and at.broker_id = brokerID and at.asset_id IN (select at2.asset_id from asset_transactions at2 group by at2.asset_id having max(at2.end_date)=scriptDate)
union all
select 'Last Liability', lt.client_id, c1.name, brokerID, CURRENT_DATE(), 
CONCAT(alp.product_name,' ', alt.type_name,' of ', alc.company_name,' ', als.scheme_name, ' Rs. ', round(lt.installment_amount), ' is due on ', DATE_FORMAT(lt.end_date, '%d/%m/%Y') , ' Ref No.: ', ref_number) 
from liability_transactions lt inner join clients c1 on 
c1.client_id=lt.client_id  inner join al_products alp on
alp.product_id=lt.product_id inner join al_types alt on
alt.type_id=lt.type_id left join mutual_fund_schemes als on
als.scheme_id=lt.scheme_id inner join al_companies alc on
alc.company_id=lt.company_id 
where lt.installment_amount >= varAssetAmount and lt.broker_id = brokerID and lt.liability_id IN (select lt2.liability_id from liability_transactions lt2 group by lt2.liability_id having max(lt2.end_date)=scriptDate);
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_summary_report_client` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin 
SELECT c.client_id, c.name AS client_name, 
calculateTotalInsuranceInv(clientID,brokerID) AS insurance_inv, 
calculateTotalInsuranceFund(clientID,brokerID) AS insurance_fund, 
calculateTotalFD(clientID,brokerID) AS fixed_deposit, 
calculateTotalMFCurrentVal(clientID,brokerID) AS mutual_fund, 
calculateTotalShares(clientID,brokerID) AS equity, 
calculateTotalProperties(clientID,brokerID) AS property, 
calculateTotalCommodity(clientID,brokerID) AS commodity, 
calculateTotalLifeCover(clientID,brokerID) AS life_cover 
FROM clients c 
WHERE c.client_id = clientID 
GROUP BY c.client_id, c.name;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_summary_report_client_import` (IN `brokerID` VARCHAR(10))  NO SQL begin 



SELECT c.client_id,
calculateTotalInsuranceInv(c.client_id,brokerID) AS insurance, 
calculateTotalFD(c.client_id,brokerID) AS fixed_income, 
calculateTotalMFCurrentVal(c.client_id,brokerID) AS mutual_funds, 
calculateTotalShares(c.client_id,brokerID) AS equity, 
calculateTotalProperties(c.client_id,brokerID) AS real_estate, 
calculateTotalCommodity(c.client_id,brokerID) AS commodity, 
calculateTotalLifeCover(c.client_id,brokerID) AS life_cover,
brokerID as broker_id
FROM clients c 
inner join families f on f.family_id=c.family_id
WHERE f.broker_id = brokerID 
GROUP BY c.client_id;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_summary_report_client_previous` (IN `clientID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin 
SELECT 
	s.insurance AS insurance_inv, 
	s.insurance_fund AS insurance_fund, 
	s.fixed_income AS fixed_deposit, 
	s.mutual_funds AS mutual_fund, 
	s.equity AS equity, 
	s.real_estate AS property, 
	s.commodity AS commodity, 
	s.life_cover AS life_cover,
    DATE(s.added_on) as curr_date
FROM summary_report_data s
WHERE s.client_id = clientID 
and DATE(s.added_on)>LAST_DAY(CURDATE() - INTERVAL 12 MONTH)
order by s.added_on ;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_summary_report_family` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10))   begin 
SELECT f.family_id, f.name as family_name, c.client_id, c.name AS client_name, 
calculateTotalInsuranceInv(c.client_id,brokerID) AS insurance_inv, 
calculateTotalInsuranceFund(c.client_id,brokerID) AS insurance_fund, 
calculateTotalFD(c.client_id,brokerID) AS fixed_deposit, 
calculateTotalMFCurrentVal(c.client_id,brokerID) AS mutual_fund, 
calculateTotalShares(c.client_id,brokerID) AS equity, 
calculateTotalProperties(c.client_id,brokerID) AS property, 
calculateTotalCommodity(c.client_id,brokerID) AS commodity, 
calculateTotalLifeCover(c.client_id,brokerID) AS life_cover 
FROM clients c 
INNER JOIN families f ON f.family_id = c.family_id 
WHERE c.family_id = familyID AND f.broker_id = brokerID 
AND (c.merge_ref_id IS NULL OR c.merge_ref_id = '') 
GROUP BY c.client_id, c.name, c.report_order 
ORDER BY c.report_order;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_summary_report_family_previous` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(10), IN `reportDate` DATE)  NO SQL begin 
SELECT 
	sum(s.insurance) AS insurance_inv, 
	sum(s.insurance_fund) AS insurance_fund, 
	sum(s.fixed_income) AS fixed_deposit, 
	sum(s.mutual_funds) AS mutual_fund, 
	sum(s.equity) AS equity, 
	sum(s.real_estate) AS property, 
	sum(s.commodity) AS commodity,
    DATE(s.added_on) as curr_date
FROM summary_report_data s
inner join clients c 
	on c.client_id=s.client_id
INNER JOIN families f 
	ON f.family_id = c.family_id 
WHERE 
	c.family_id = familyID AND
    f.broker_id = brokerID AND 
    (c.merge_ref_id IS NULL OR c.merge_ref_id = '') 
    and DATE(s.added_on)>LAST_DAY(CURDATE() - INTERVAL 12 MONTH)
GROUP BY c.family_id,    DATE(s.added_on);


end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Sp_Total_Portfoilo_family` (IN `familyID` VARCHAR(20), IN `brokerID` VARCHAR(20))  NO SQL begin
SELECT  c.name AS client_name, c.client_id as client_id,
(calculateTotalInsuranceInv(c.client_id,brokerID)+
calculateTotalFD(c.client_id,brokerID) +
calculateTotalMFCurrentVal(c.client_id,brokerID)+
calculateTotalShares(c.client_id,brokerID)+
calculateTotalProperties(c.client_id,brokerID)+
calculateTotalCommodity(c.client_id,brokerID) ) as TotalPortfolio, c.client_id as client_id
FROM clients c
INNER JOIN families f ON f.family_id = c.family_id
WHERE c.family_id = familyID AND f.broker_id = brokerID 
AND (c.merge_ref_id IS NULL OR c.merge_ref_id = "") 
GROUP BY c.client_id, c.name, c.report_order
ORDER BY c.report_order;
select TotalPortfolio;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_unit_price` (IN `schemeID` VARCHAR(100), IN `folioNumber` VARCHAR(100), IN `brokerID` VARCHAR(10))  NO SQL begin
	DECLARE unit_done, divR2_done, divDiv_done, divDivPay_done BOOLEAN DEFAULT FALSE;
	declare mfSchemeType varchar(30) default '';
	declare pAmt decimal(30, 2) default 0.00;
	declare pNav decimal(18, 4) default 0.00;
	declare liveUnit decimal(18, 4) default 0.00;
	declare cNav decimal(18, 2) default 0.00;
	declare divR2 decimal(30, 10) default 0.00;
	declare divAmt decimal(30, 2) default 0.00;
	declare divPay decimal(30, 10) default 0.00;
  declare transID int;
	declare pDate date;
	declare cNavDate date;
	set @initialAmount = 0;
    set @unit_value = 0;
	set @final_unit_value = 0;
	#Calculate Unit Per Count
	BLOCK1: begin
	declare unit_cur cursor for select mf_scheme_type, p_nav, p_amount,  purchase_date, transaction_id from 
	mf_valuation_reports where scheme_id = schemeID and folio_number = folioNumber and broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET unit_done = TRUE;
	OPEN unit_cur; 
	unit_loop: LOOP
	fetch from unit_cur into mfSchemeType, pNav, pAmt, pDate, transID;
		IF unit_done THEN
		CLOSE unit_cur;
		LEAVE unit_loop;
		END IF;
		if(mfSchemeType = 'DIV') then
			select sum(quantity) from mutual_fund_transactions where (purchase_date < pDate) and mutual_fund_scheme = schemeID 
			and folio_number = folioNumber and broker_id = brokerID and mutual_fund_type IN ('SWO','RED') into @redAmount;
			select sum(quantity) from mutual_fund_transactions where (purchase_date < pDate) and mutual_fund_scheme = schemeID
			and folio_number = folioNumber and transaction_type ='Purchase' and broker_id = brokerID into @initialAmount;
			if @redAmount is NULL then
				set @redAmount = 0;
			end if;
            set @initialAmount = @initialAmount - @redAmount;
			set @unit_value = (pAmt * pNav) / @initialAmount;
			/*select mfSchemeType, pNav, pAmt, divAmt, pDate, transID, @unit_value, pAmt, pNav, (pAmt * pNav), @initialAmount;
    select @redAmount,@initialAmount,@unit_value;*/
			set @initialAmount = @initialAmount + pAmt;
			set @final_unit_value = @final_unit_value + @unit_value;
			update mf_valuation_reports set unit_per_count = @unit_value where transaction_id = transID and broker_id = brokerID;
		end if;
	END LOOP unit_loop;
	end BLOCK1;
	#Calculate For divR2
	BLOCK2: begin
	declare divR2_cur cursor for select transaction_id from mf_valuation_reports where scheme_id = schemeID and 
	folio_number = folioNumber and broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divR2_done = TRUE;
	OPEN divR2_cur; 
	divR2_loop: LOOP
	fetch from divR2_cur into transID;
		IF divR2_done THEN
		CLOSE divR2_cur;
		LEAVE divR2_loop;
		END IF;
		select live_unit from mf_valuation_reports where transaction_id = transID and broker_id = brokerID into @liveUnit_r;
		select sum(unit_per_count) from mf_valuation_reports where transaction_id > transID and scheme_id = schemeID and 
		folio_number = folioNumber and broker_id = brokerID into @unit_sum;
		if @liveUnit_r is NULL then
			set @liveUnit_r = 0;
		end if;
		if @unit_sum is NULL then
			set @unit_sum = 0;
		end if;
		set @divR = @liveUnit_r * @unit_sum;
		update mf_valuation_reports set div_r2 = @divR where transaction_id = transID and broker_id = brokerID;
	END LOOP divR2_loop;
	end BLOCK2;
	#Calculate Dividend Payout
	BLOCK3: begin
	declare divDivPay_cur cursor for select mf_scheme_type, p_amount, purchase_date, transaction_id, live_unit from 
	mf_valuation_reports where scheme_id = schemeID and folio_number = folioNumber and broker_id = brokerID 
	and mf_scheme_type IN ('PIP','SWI','IPO','TIN','DIV');
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divDivPay_done = TRUE;
	OPEN divDivPay_cur; 
	divDivPay_loop: LOOP
	fetch from divDivPay_cur into mfSchemeType, pAmt, pDate, transID, liveUnit;
		IF divDivPay_done THEN
		CLOSE divDivPay_cur;
		LEAVE divDivPay_loop;
		END IF;
		select sum(DPO_units) from mutual_fund_transactions where mutual_fund_scheme = schemeID and folio_number = folioNumber and 
		purchase_date > pDate and broker_id = brokerID into @totalDPOUnit;
		update mf_valuation_reports set div_payout = @totalDPOUnit * liveUnit where transaction_id = transID and 
		broker_id = brokerID;
	END LOOP divDivPay_loop;
	end BLOCK3;
	#Calculate div_amount, transDay, mf_abs, cagr
	BLOCK4: begin
	declare divDiv_cur cursor for select mf_scheme_type, p_amount, p_nav, purchase_date, transaction_id, c_nav, c_nav_date, div_r2,
	div_amount, div_payout, live_unit from mf_valuation_reports where scheme_id = schemeID and folio_number = folioNumber and
	broker_id = brokerID;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET divDiv_done = TRUE;
	OPEN divDiv_cur; 
	divDiv_loop: LOOP
	fetch from divDiv_cur into mfSchemeType, pAmt, pNav, pDate, transID, cNav, cNavDate, divR2, divAmt, divPay, liveUnit;
		IF divDiv_done THEN
		CLOSE divDiv_cur;
		LEAVE divDiv_loop;
		END IF;
		if(mfSchemeType = 'DIV') then
			update mf_valuation_reports set div_amount = pAmt, p_amount=0 where transaction_id = transID and broker_id = brokerID;
		end if;
		set @transDay = DATEDIFF(cNavDate, pDate);
		update mf_valuation_reports set transaction_day = @transDay where transaction_id = transID and broker_id = brokerID;
		if pAmt is NULL then
			set pAmt = 0;
		end if;
		if divR2 is null then
			set divR2 = 0;
		end if;
		if divPay is NULL then
			set divPay = 0;
		end if;
		if divAmt is NULL then
			set divAmt = 0;
		end if;
		set @abs = ((liveUnit * cNav + divR2 + divPay) * 100) / (liveUnit * pNav + (divAmt * pNav));
		update mf_valuation_reports set mf_abs = @abs - 100 where transaction_id = transID and broker_id = brokerID;
		if @transDay > 365 then
			set @powerval = @transDay / 365;
			set @cagr = power((((liveUnit * cNav) + divR2 + divPay) / ((liveUnit * pNav) + (divAmt * pNav))), ((1 / (@transDay/365))))-1;
      update mf_valuation_reports set cagr = @cagr * 100 where transaction_id = transID and broker_id = brokerID;
		else
			update mf_valuation_reports set cagr = ((@abs - 100)/ @transDay) * 365 where transaction_id = transID and 
			broker_id = brokerID;
		end if;
    #select transID,pAmt*cNav+divR2+divPay,((pAmt*pNav)+(divAmt*pNav)),@transDay,(@abs-100),((@abs-100)/@transDay);
	END LOOP divDiv_loop;
	end BLOCK4;
end$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_updatePremiumPayingDetails` (IN `policyNumber` VARCHAR(100), IN `comDate` DATE, IN `paidUpDate` DATE, IN `fupDate` DATE, IN `premiumAmount` FLOAT(18,2), IN `premiumPayingMode` INT(11), IN `brokerID` VARCHAR(10))  NO SQL BEGIN
	Declare pptDate date;
	Declare tempDate date;
	Declare benifitDate date;
	Declare amountInsured float;
	Declare j int;
    set j=0;
    set pptDate = paidUpDate;
    delete from premium_paying_details where date_of_payment >= fupDate and policy_num = policyNumber and broker_id = brokerID;
    set tempDate = fupDate;
	if (premiumPayingMode = 1)
	then
		set j=1;		
		#set pptDate=DATE_SUB(pptDate, INTERVAL 1 YEAR);
	elseif (premiumPayingMode = 2)
	then
		set j=2;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 6 MONTH);
	elseif (premiumPayingMode = 3)
	then
		set j=3;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 3 MONTH);
	elseif (premiumPayingMode = 4)
	then
		set j=4;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 1 MONTH);
	elseif (premiumPayingMode = 5)
	then
		set j=5;	
        #set pptDate=DATE_SUB(pptDate, INTERVAL 1 MONTH);
	end if;
	while(tempDate <= pptDate)
	do
		if(j = 1)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
		elseif(j = 2)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 6 MONTH);
		elseif(j = 3)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 3 MONTH);
		elseif (j = 4)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 MONTH);
        elseif (j = 5)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
		else
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
		end if;
	end while;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_update_user_status` (IN `newStatus` INT, IN `brokerID` VARCHAR(100))  NO SQL BEGIN
UPDATE users u SET u.status = newStatus WHERE u.id = brokerID OR u.broker_id = brokerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `temp_premium_add` (IN `policyNum` VARCHAR(30), IN `premAmt` FLOAT(18,2), IN `startDate` DATE, IN `endDate` DATE, IN `premMode` VARCHAR(50), IN `brokerID` VARCHAR(10))  NO SQL begin
Declare policyNo varchar(50); 
Declare preAmount float(18,2);
Declare preStart date;
Declare preDate date;
Declare preEnd date;
Declare pMode varchar(20);
Declare pDate varchar(10);
Declare pClientID varchar(30);
set policyNo= policyNum;
set preAmount= premAmt;
set preStart = startDate;
set preDate = preStart;
set preEnd = endDate;
set pMode=premMode;
select client_id into pClientID from insurances where broker_id = brokerID and policy_num = policyNo;
while preStart < preEnd DO
	if pMode="Annually" or pMode = "Single"
	then
		set preStart = DATE_ADD(preStart, INTERVAL 1 YEAR);
        set preDate = DATE_ADD(preStart, INTERVAL -1 YEAR);
	elseif	pMode = "Half-yearly"
	then
		set preStart = DATE_ADD(preStart, INTERVAL 6 MONTH);
        set preDate = DATE_ADD(preStart, INTERVAL -6 MONTH);
	elseif pMode = "Quarterly"
	then
		set preStart = DATE_ADD(preStart, INTERVAL 1 QUARTER);
        set preDate = DATE_ADD(preStart, INTERVAL -1 QUARTER);
	else
		set preStart = DATE_ADD(preStart, INTERVAL 1 MONTH);
        set preDate = DATE_ADD(preStart, INTERVAL -1 MONTH);
	end if;
	insert into premium_transactions(policy_number, premium_amount, cheque_date, next_premium_due_date, premium_mode, client_id, broker_id) 
	values (policyNo, preAmount, preDate, preStart, pMode, pClientID, brokerID);
END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `test` (IN `familyID` VARCHAR(30), IN `brokerID` VARCHAR(20), IN `clientID` VARCHAR(30))  NO SQL begin
SET @sql = '';

  	SET @sql = CONCAT('select
    c.name as client_name,
	mfs.scheme_name as mf_scheme_name,
    mfs.market_cap,                  
	mft.client_id,
    mft.folio_number as folio_number,
    Date_format(MIN(mft.purchase_date), "%d/%m/%Y") as purchase_date,
    mst.scheme_type as scheme_type,
    sum(mfv.p_amount) as purchase_amount, 
    sum(mfv.div_amount) as div_amount,
    ( (sum(mfv.p_amount+mfv.div_amount) ) / sum(mfv.live_unit) ) as p_nav,
    sum(mfv.live_unit) as live_unit,
    mft.mutual_fund_type as mf_scheme_type,
    MAX(mfv.transaction_day) as transaction_day,
    mfv.c_nav  as c_nav,
    Date_format(mfv.c_nav_date, "%d/%m/%Y") as c_nav_date,
    sum((mfv.c_nav * mfv.live_unit)) as current_value,
    sum(mfv.div_r2)as div_r2,
    sum(mfv.div_payout) as div_payout,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)/sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_abs )/sum(mfv.p_amount+mfv.div_amount)) as mf_abs,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.mf_cagr * mfv.transaction_day)) as cagr1,
    (sum((mfv.p_amount+mfv.div_amount)*mfv.transaction_day)) as cagr2,
                      (case when mst.scheme_type IN("Equity","Arbitrage","ELSS","ETF","FOF","Gold Fund") then "Equity"
              when mst.scheme_type IN("Hybrid","Balanced","MIP") then "Hybrid"
              when mst.scheme_type IN("Debt","Capital Protection","FMP","LT Debt","Liquid") then "Debt"
              else "" end) as Scheme_Group_TypeName
    from mutual_fund_valuation_h_',brokerID,' mfv
    inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id
    inner join clients c on mft.client_id = c.client_id
    inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id
    inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id
    where 
		mfv.broker_id = "',brokerID,'" AND 
		c.client_id = "',clientID,'"  
    and c.status=1
    and round((mfv.c_nav * mfv.live_unit)) > 3
    group by mft.mutual_fund_scheme, mft.folio_number
    having (sum(mfv.p_amount)+sum(mfv.div_amount)) != 0
    order by c.report_order,c.name, min(year(mft.purchase_date)),mfs.scheme_name, mft.folio_number;');
	IF(@sql != '') THEN
  		PREPARE stmt1 FROM @sql;
		EXECUTE stmt1;
  		DEALLOCATE PREPARE stmt1;
	END IF;

end$$

--
-- Functions
--
CREATE DEFINER=`threetense`@`localhost` FUNCTION `brokerID` () RETURNS VARCHAR(10) CHARSET latin1  begin
declare id_value varchar(10);
declare b_id varchar(10);
select trim(Leading '0' from id) into b_id from users order by id desc limit 1;
if length(b_id) = 1
then
	set b_id = cast(b_id as unsigned) + 1;
	set id_value = Concat('000', b_id);
elseif (length(b_id) = 2)
then
	set b_id = cast(b_id as unsigned) + 1;
	set id_value = Concat('00', b_id);
elseif (length(b_id) = 3)
then
	set b_id = cast(b_id as unsigned) + 1;
	set id_value = Concat('0', b_id);
else
	set id_value = cast(b_id as unsigned) + 1;
end if;
return id_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateFundsBal` (`clientID` VARCHAR(30)) RETURNS FLOAT(20,2)  begin
Declare addFunds float;
Declare withdrawFunds float;
Declare balance float;
Declare total float;
SELECT SUM(amount) INTO addFunds FROM add_funds 
WHERE client_id = clientID AND shares_app = 1;
SELECT SUM(amount) INTO withdrawFunds FROM withdraw_funds 
WHERE client_id = clientID AND withdraw_from = 'Shares';
SELECT SUM(balance) INTO balance FROM client_brokers 
WHERE client_id = clientID;
if addFunds IS NOT NULL AND withdrawFunds IS NOT NULL then 
	SET total = addFunds - withdrawFunds;
    if total >= 0 then 
    	SET total = 0;
    else
    	SET total = -total;
	end if;        
elseif addFunds IS NOT NULL then 
	SET total = addFunds;    
elseif withdrawFunds IS NOT NULL then 
	SET total = withdrawFunds;    
else 
	SET total = 0;
end if;
if balance is NOT NULL then
	SET total = total + balance;
end if;
return total;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateNetInvestmentClient` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS DECIMAL(18,2) NO SQL begin
declare invAmt decimal(18, 2);
select sum(amount) from mutual_fund_transactions where mutual_fund_type in ('PIP', 'NFO', 'TIN', 'IPO') and 
client_id = clientID and broker_id = brokerID into @purchase;
select sum(amount) from mutual_fund_transactions where mutual_fund_type in ('RED', 'DP') and client_id = clientID and broker_id = brokerID into @redemption;
if @purchase is null then
	set @purchase = 0.00;
end if;
if @redemption is null then
	set @redemption = 0.00;
end if;
set invAmt = @purchase - @redemption;
return invAmt;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateNetInvestmentClient_historical` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10), `reportDate` DATE) RETURNS DECIMAL(18,2) NO SQL begin
	declare invAmt decimal(18, 2);
	select sum(amount) 
	from mutual_fund_transactions 
	where mutual_fund_type in ('PIP', 'NFO', 'TIN', 'IPO') and 
		  client_id = clientID and 
		  broker_id = brokerID and
		  purchase_date <=reportDate

	into @purchase;
	select sum(amount) 
	from mutual_fund_transactions 
	where mutual_fund_type in ('RED', 'DP') and 
		  client_id = clientID and 
		  broker_id = brokerID and
		  purchase_date <=reportDate
	into @redemption;

	if @purchase is null then
		set @purchase = 0.00;
	end if;
	if @redemption is null then
		set @redemption = 0.00;
	end if;
	set invAmt = @purchase - @redemption;
	return invAmt;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateNetInvestmentFamily` (`familyID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS INT(11) NO SQL begin
declare invAmt decimal(18, 2);
select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('PIP', 'TIN', 'IPO', 'NFO') 
and 
c.family_id = familyID and broker_id = brokerID into @purchase;


select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('RED', 'DP') and
c.family_id = familyID 
and broker_id = brokerID into @redemption;
if @purchase is null then
    set @purchase = 0.00;
end if;
if @redemption is null then
    set @redemption = 0.00;
end if;
set invAmt = @purchase - @redemption;
return invAmt;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateNetInvestmentFamily_historical` (`familyID` VARCHAR(30), `brokerID` VARCHAR(10), `reportDate` DATE) RETURNS INT(11) NO SQL begin
declare invAmt decimal(18, 2);
select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('PIP', 'TIN', 'IPO', 'NFO') and
c.family_id = familyID and broker_id = brokerID  and mft.purchase_date<=reportDate into @purchase;

select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('RED', 'DP') and
c.family_id = familyID and broker_id = brokerID and mft.purchase_date<=reportDate into @redemption;
if @purchase is null then
    set @purchase = 0.00;
end if;
if @redemption is null then
    set @redemption = 0.00;
end if;
set invAmt = @purchase - @redemption;
return invAmt;
end$$

CREATE DEFINER=`root`@`localhost` FUNCTION `calculateNetInvestmentFamily_historical_new` (`familyID` VARCHAR(30), `brokerID` VARCHAR(20), `reportDate` DATE, `clientID` VARCHAR(500)) RETURNS INT(11) NO SQL begin
declare invAmt decimal(18, 2);
select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('PIP', 'TIN', 'IPO', 'NFO') and
c.family_id = familyID and broker_id = brokerID  
and
(case when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
	when clientID='' then 1 
else 0 end)=1
and 
mft.purchase_date<=reportDate into @purchase;

select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('RED', 'DP') and
c.family_id = familyID and broker_id = brokerID 
and
(case when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
	when clientID='' then 1 
else 0 end)=1
and mft.purchase_date<=reportDate into @redemption;
if @purchase is null then
    set @purchase = 0.00;
end if;
if @redemption is null then
    set @redemption = 0.00;
end if;
set invAmt = @purchase - @redemption;
return invAmt;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateNetInvestmentFamily_new` (`familyID` VARCHAR(30), `brokerID` VARCHAR(10), `clientID` VARCHAR(500)) RETURNS INT(11) NO SQL begin
declare invAmt decimal(18, 2);
select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('PIP', 'TIN', 'IPO', 'NFO') 
and 
c.family_id = familyID and
(case when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
	when clientID='' then 1 
else 0 end)=1
and broker_id = brokerID into @purchase;


select sum(amount) from mutual_fund_transactions mft inner join clients c on mft.family_id=c.family_id where mutual_fund_type in ('RED', 'DP') 
and
(case when clientID!='' and FIND_IN_SET(c.client_id, clientID) then 1
	when clientID='' then 1 
else 0 end)=1
and

c.family_id = familyID 
and broker_id = brokerID into @redemption;
if @purchase is null then
    set @purchase = 0.00;
end if;
if @redemption is null then
    set @redemption = 0.00;
end if;
set invAmt = @purchase - @redemption;
return invAmt;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateStocks` (`scripCode` VARCHAR(50), `quantity` INT(11)) RETURNS FLOAT(20,2)  begin
Declare totalStocks float;
Declare currentValue float;
SELECT (close_rate*quantity) INTO totalStocks FROM scrip_rates 
WHERE scrip_code = scripCode;
if totalStocks is NULL then
	return 0;
else 
	return totalStocks;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalCommodity` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
select ROUND(SUM(res)) INTO total from
(
    SELECT SUM((ct.quantity - (case when commoditySale(ct.commodity_trans_id) is not null then commoditySale(ct.commodity_trans_id) else 0 end))
    * cr.current_rate) as 'res' 
    FROM commodity_transactions ct 
    INNER JOIN commodity_rates cr ON cr.item_id = ct.commodity_item_id AND cr.unit_id = ct.commodity_unit_id 
    INNER JOIN advisers a on a.adviser_id = ct.adviser_id 
    WHERE (ct.transaction_type = 'Purchase') AND (ct.client_id = clientID) 
    AND (ct.broker_id = brokerID) 
    GROUP BY ct.quantity, cr.current_rate, ct.commodity_trans_id 
) as a;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalFD` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(amount_invested) INTO total FROM fd_transactions 
WHERE `status` = 'Active' AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalInsuranceFund` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(fund_value) INTO total FROM insurances 
WHERE `status` NOT IN (SELECT status_id FROM premium_status 
                       WHERE `status` IN('Surrender','Lapsed','Matured','Paid Up Cancellation')) 
AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalInsuranceInv` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(prem_paid_till_date) INTO total FROM insurances 
WHERE `status` IN (SELECT status_id FROM premium_status WHERE `status` IN('In Force','Paid up','Grace','Lapsed')) 
AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalLifeCover` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(amt_insured) INTO total FROM insurances 
WHERE client_id = clientID AND broker_id = brokerID AND `status` IN (
    SELECT status_id FROM premium_status WHERE `status` IN('In Force','Paid up','Grace')) 
AND plan_type_id IN (
    SELECT plan_type_id FROM ins_plan_types WHERE plan_type_name IN('Traditional','Unit Linked','Term Plan'));
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalMFCurrentVal` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
/*SELECT SUM(val) INTO total FROM (
    SELECT (getMFTotalQty(mft.mutual_fund_scheme,mft.folio_number,'Purchase') - getMFTotalQty(mft.mutual_fund_scheme,mft.folio_number,'Redemption')) 
    * getCurrentNav(mft.mutual_fund_scheme) AS `val` 
    FROM mutual_fund_transactions mft 
    INNER JOIN clients c ON c.client_id = mft.client_id 
    WHERE mft.client_id = clientID AND mft.broker_id = brokerID
    AND ROUND(getMFTotalQty(mft.mutual_fund_scheme,mft.folio_number,'Purchase') - getMFTotalQty(mft.mutual_fund_scheme,mft.folio_number,'Redemption')) > 0 
    GROUP BY c.name, mft.mutual_fund_scheme, mft.folio_number) AS a;*/
select sum(mfv.c_nav * mfv.live_unit) into total 
     from mutual_fund_valuation mfv 
     inner join mutual_fund_transactions mft on mfv.transaction_id = mft.transaction_id 
     inner join mutual_fund_schemes mfs on mft.mutual_fund_scheme = mfs.scheme_id 
     inner join mf_scheme_types mst on mfs.scheme_type_id = mst.scheme_type_id 
     inner join clients c on mft.client_id = c.client_id 
     where mfv.broker_id = brokerID AND mft.client_id = clientID 
     and round((mfv.c_nav * mfv.live_unit)) > 3 
     order by mfs.scheme_name, mft.folio_number, mft.purchase_date;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalProperties` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(pt.property_area * pt.current_rate) INTO total FROM property_transactions pt 
INNER JOIN clients c ON c.client_id = pt.client_id 
WHERE pt.pro_transaction_id IN (
    SELECT pro_transaction_id FROM property_transactions 
    WHERE transaction_type = 'Purchase' AND property_name NOT IN (
        SELECT property_name from property_transactions 
        WHERE transaction_type = 'Sale'))
AND pt.client_id = clientID AND pt.broker_id = brokerID 
GROUP BY c.name;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `calculateTotalShares` (`clientID` VARCHAR(30), `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(calculateStocks(scrip_code,quantity)) INTO total FROM equities 
WHERE client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_FDInterest` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(fdi.interest_amount) INTO total FROM fd_interests fdi 
INNER JOIN fd_transactions fdt ON fdt.fd_transaction_id = fdi.fd_transaction_id 
WHERE (fdi.interest_date BETWEEN fromDate AND toDate) AND fdt.client_id = clientID AND fdt.broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_FDMaturity` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(maturity_amount) INTO total FROM fd_transactions 
WHERE (issued_date BETWEEN fromDate AND toDate) AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_insuranceLifeCover` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(amt_insured) INTO total FROM insurances 
WHERE (commence_date BETWEEN fromDate AND toDate) 
AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_insuranceMaturity` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(pt.premium_amount) INTO total FROM premium_transactions pt 
INNER JOIN insurances i ON i.policy_num = pt.policy_number 
WHERE (pt.cheque_date BETWEEN fromDate AND toDate) AND (i.`status` NOT IN(
    select status_id from premium_status where `status` IN('Lapsed','Surrender','Paid Up Cancellation'))) 
AND i.client_id = clientID AND pt.broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_insurancePremiumAmount` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(pt.premium_amount) INTO total FROM premium_transactions pt 
INNER JOIN insurances i ON i.policy_num = pt.policy_number 
WHERE (pt.cheque_date BETWEEN fromDate AND toDate) 
AND i.client_id = clientID AND pt.broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_LifeCover` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(amt_insured) INTO total FROM insurances 
WHERE client_id = clientID AND broker_id = brokerID 
AND plan_type_id NOT IN (
    SELECT plan_type_id FROM ins_plan_types WHERE plan_type_name IN('Traditional','Unit Linked'));
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_rent` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(prd.amount) INTO total FROM property_rent_details prd 
INNER JOIN property_transactions pt ON pt.pro_transaction_id = prd.pro_transaction_id 
WHERE (prd.rent_date BETWEEN fromDate AND toDate) AND pt.client_id = clientID AND pt.broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `cf_totalOutflow` (`clientID` VARCHAR(30), `fromDate` DATE, `toDate` DATE, `brokerID` VARCHAR(10)) RETURNS FLOAT(20,2)  begin
Declare total float;
SELECT SUM(amount) INTO total FROM withdraw_funds 
WHERE (transaction_date BETWEEN fromDate AND toDate) 
AND client_id = clientID AND broker_id = brokerID;
if total is NULL then
	return 0;
else 
	return total;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `clientID` () RETURNS VARCHAR(30) CHARSET latin1  begin
declare id_value varchar(10);
declare full_id_value varchar(30);
declare cli_year int;
declare cur_year int;
declare id int;
select cast(substring(client_id, 6) as unsigned) into id from clients order by cast(substring(client_id, 6) as unsigned) desc limit 1;
select substring(client_id, 2, 4) into cli_year from clients order by substring(client_id, 2) desc limit 1;
select year(now()) into cur_year;
if id is null or cli_year < cur_year
then
set id_value = '00001';
else
if length(id) = 1 and id < 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('0000', id);
elseif length(id) = 2 and id < 99 or id = 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('000', id);
elseif length(id) = 3 and id < 999 or id = 99
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('00', id);
elseif length(id) = 4 and id < 9999 or id = 999
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('0',id);
else
	set id_value = cast(id as unsigned) + 1;
end if;
end if;
set full_id_value = Concat('C',cur_year,id_value);
return full_id_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `commodityID` (`brokerID` VARCHAR(10)) RETURNS VARCHAR(30) CHARSET latin1 NO SQL begin
declare id_value varchar(10);
declare full_id_value varchar(30);
declare comm_year int;
declare cur_year int;
declare id int;
select substring(commodity_trans_id, 12) into id from commodity_transactions where `broker_id` = brokerID order by substring(commodity_trans_id, 4) desc limit 1;
select substring(commodity_trans_id, 4, 4) into comm_year from commodity_transactions where `broker_id` = brokerID order by substring(commodity_trans_id, 4) desc limit 1;
select year(now()) into cur_year;
if id is null or comm_year < cur_year
then
set id_value = '0001';
else
if length(id) = 1 and id < 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('000', id);
elseif length(id) = 2 and id < 99 or id = 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('00', id);
elseif length(id) = 3 and id < 999 or id = 99
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('0', id);
else
	set id_value = cast(id as unsigned) + 1;
end if;
end if;
set full_id_value = Concat('COM',cur_year,brokerID,id_value);
return full_id_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `commoditySale` (`commTransID` VARCHAR(20)) RETURNS FLOAT(11,2)  begin
declare sale_value float(18,2);
select sum(quantity) into sale_value from commodity_transactions where sale_ref = commTransID and transaction_type = 'Sale';
return sale_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `familyID` (`brokerID` VARCHAR(10)) RETURNS VARCHAR(30) CHARSET latin1  begin
declare id_value varchar(10);
declare full_id_value varchar(30);
declare fam_year int;
declare cur_year int;
declare id int;
select substring(family_id, 10) into id from families where `broker_id` = brokerID order by substring(family_id, 2)  desc limit 1;
select substring(family_id, 2, 4) into fam_year from families
where `broker_id` = brokerID order by substring(family_id, 2) desc limit 1;
select year(now()) into cur_year;
if id is null or fam_year < cur_year
then
set id_value = '0001';
else
if length(id) = 1 and id < 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('000', id);
elseif length(id) = 2 and id < 99 or id = 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('00', id);
elseif length(id) = 3 and id < 999 or id = 99
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('0', id);
else
	set id_value = cast(id as unsigned) + 1;
end if;
end if;
set full_id_value = Concat('F',cur_year,brokerID,id_value);
return full_id_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `findFirstMaturityDate` (`policyNo` VARCHAR(30)) RETURNS DATE NO SQL begin
	declare maxDate date;
	select min(maturity_date) into maxDate from premium_maturities where policy_num=policyNo;
	return maxDate;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `findLastPremiumPaidForLapseReport` (`policyNo` VARCHAR(20)) RETURNS DATE NO SQL begin
	Declare premDate date;
	select next_premium_due_date into premDate from premium_transactions where premium_id In
	(select premium_id from premium_transactions where policy_number=policyNo order by next_premium_due_date desc) order by next_premium_due_date asc limit 1;
	return premDate;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `find_rent` (`property_id` VARCHAR(30), `from_date` DATE) RETURNS FLOAT(18,2) NO SQL begin
declare res float;
select sum(prd.amount) into res from property_rent_details as prd inner join property_transactions as pt on prd.pro_transaction_id = pt.pro_transaction_id inner join clients c on c.client_id = pt.client_id 
where prd.pro_transaction_id = property_id and prd.rent_date < from_date;
if(res is null)
then
return 0;
end if;
return res;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getAssetDate` (`assetID` INT) RETURNS INT(11) NO SQL begin
declare noOfDate int;
select count(maturity_date) into noOfDate from asset_maturity where 
asset_id = assetID;
return noOfDate;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getCurrentNav` (`schemeID` INT(11)) RETURNS FLOAT(10,4)  begin
Declare nav float;
SELECT current_nav INTO nav FROM mf_schemes_histories 
WHERE scheme_id = schemeID 
ORDER BY scheme_date DESC
LIMIT 1;
if nav is NULL then
	return 0;
else 
	return nav;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getFundValue` (`policy_num` VARCHAR(100), `brokerID` VARCHAR(10)) RETURNS INT(10) NO SQL begin
declare fund_value int;
select sum(`value`) into fund_value from fund_options 
where policy_number = policy_num and broker_id = brokerID
group by policy_number, broker_id;
return fund_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getLiabilityDate` (`liabilityID` INT) RETURNS INT(11) NO SQL begin
declare noOfDate int;
select count(maturity_date) into noOfDate from liability_maturity where 
liability_id = liabilityID;
return noOfDate;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getMFTotalQty` (`schemeID` INT(11), `folioNo` VARCHAR(200), `transType` VARCHAR(50)) RETURNS FLOAT(20,2)  begin
Declare totalQty float;
SELECT SUM(quantity) INTO totalQty FROM mutual_fund_transactions 
WHERE mutual_fund_scheme = schemeID AND folio_number = folioNo AND transaction_type = transType;
if totalQty is NULL then
	return 0;
else 
	return totalQty;
end if;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `getStakeValue` (`policy_num` VARCHAR(100), `brokerID` VARCHAR(10)) RETURNS INT(11) NO SQL begin
declare stake_value int;
select sum(`amount`) into stake_value from real_stakes 
where policy_number = policy_num and broker_id = brokerID
group by policy_number, broker_id;
return stake_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `propertyID` (`brokerID` VARCHAR(10)) RETURNS VARCHAR(30) CHARSET latin1 NO SQL begin
declare id_value varchar(10);
declare full_id_value varchar(30);
declare pro_year int;
declare cur_year int;
declare id int;
select substring(pro_transaction_id, 10) into id from property_transactions where `broker_id` = brokerID order by substring(pro_transaction_id, 2) desc limit 1;
select substring(pro_transaction_id, 2, 4) into pro_year from property_transactions where `broker_id` = brokerID order by substring(pro_transaction_id, 2) desc limit 1;
select year(now()) into cur_year;
if id is null or pro_year < cur_year
then
set id_value = '0001';
else
if length(id) = 1 and id < 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('000', id);
elseif length(id) = 2 and id < 99 or id = 9
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('00', id);
elseif length(id) = 3 and id < 999 or id = 99
then
	set id = cast(id as unsigned) + 1;
	set id_value = Concat('0', id);
else
	set id_value = cast(id as unsigned) + 1;
end if;
end if;
set full_id_value = Concat('P',cur_year,brokerID,id_value);
return full_id_value;
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `total_portfolio` (`clientID` VARCHAR(20), `brokerID` VARCHAR(20)) RETURNS INT(20) UNSIGNED NO SQL begin
Declare  insurance_inv decimal(18,2);
Declare fixed_deposit decimal(18,2);
Declare mutual_fund decimal(18,2);
Declare toal_shares decimal(18,2);
Declare property decimal(18,2);
Declare commodity decimal(18,2);
SELECT
calculateTotalInsuranceInv(clientID,brokerID)   ,
calculateTotalFD(clientID,brokerID)   ,
calculateTotalMFCurrentVal(clientID,brokerID)   ,
calculateTotalShares(clientID,brokerID)   ,
calculateTotalProperties(clientID,brokerID)   ,
calculateTotalCommodity(clientID,brokerID)
into insurance_inv,fixed_deposit,mutual_fund,toal_shares,property,commodity
FROM clients c
WHERE c.client_id = clientID ;
return (insurance_inv+fixed_deposit+mutual_fund+toal_shares+property+commodity);
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `total_portfolio_HOF` (`familyID` VARCHAR(20), `brokerID` VARCHAR(20)) RETURNS BIGINT(20) UNSIGNED NO SQL begin
Declare  a decimal(18,2);
Declare b decimal(18,2);
Declare c decimal(18,2);
Declare d decimal(18,2);
Declare e decimal(18,2);
Declare f decimal(18,2);
Declare g decimal(18,2);
select sum(x.inv), sum(x.fd), sum(x.cval), sum(x.eq), sum(x.prop), sum(x.comm)
into  a,c,d,e,f,g
from (SELECT c.name,
calculateTotalInsuranceInv(c.client_id,brokerID) as inv,
calculateTotalFD(c.client_id,brokerID) as fd,
calculateTotalMFCurrentVal(c.client_id,brokerID) as cval,
calculateTotalShares(c.client_id,brokerID) as eq ,
calculateTotalProperties(c.client_id,brokerID) as prop,
calculateTotalCommodity(c.client_id,brokerID) as comm
FROM clients c
INNER JOIN families f ON f.family_id = c.family_id
WHERE c.family_id =  familyID AND f.broker_id = brokerID
) as x ;
return(a+c+d+e+f+g);
end$$

CREATE DEFINER=`threetense`@`localhost` FUNCTION `updatePaidupDate` (`policyNumber` VARCHAR(100), `pptDate` DATE, `brokerID` VARCHAR(10)) RETURNS INT(11) NO SQL begin
Declare rows INT;
update insurances set paidup_date = pptDate where policy_num = policyNumber and broker_id = brokerID;
SELECT ROW_COUNT() into rows;
return rows;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `add_funds`
--

CREATE TABLE `add_funds` (
  `add_fund_id` int(11) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `cheque_no` varchar(30) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `shares_app` int(11) NOT NULL DEFAULT '0',
  `trading_broker_id` int(11) DEFAULT NULL,
  `client_code` varchar(100) DEFAULT NULL,
  `add_notes` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` date DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `add_info` text,
  `super_admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `advisers`
--

CREATE TABLE `advisers` (
  `adviser_id` int(11) NOT NULL,
  `adviser_name` varchar(200) NOT NULL,
  `company_name` varchar(300) DEFAULT NULL,
  `product` varchar(300) DEFAULT NULL,
  `agency_code` varchar(50) DEFAULT NULL,
  `contact_person` varchar(200) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `held_type` varchar(100) NOT NULL DEFAULT 'Non-held'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `al_companies`
--

CREATE TABLE `al_companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `al_products`
--

CREATE TABLE `al_products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `al_schemes`
--

CREATE TABLE `al_schemes` (
  `scheme_id` int(11) NOT NULL,
  `scheme_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `al_types`
--

CREATE TABLE `al_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE `api_logs` (
  `log_id` bigint(20) NOT NULL,
  `request_payload` longtext NOT NULL,
  `response_payload` longtext,
  `entity` enum('Client','Broker') DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `hof` enum('0','1') DEFAULT NULL,
  `operation` varchar(50) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `device_os` varchar(50) DEFAULT NULL,
  `device_os_version` varchar(50) DEFAULT NULL,
  `app_version` varchar(25) DEFAULT NULL,
  `response_code` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_maturity`
--

CREATE TABLE `asset_maturity` (
  `asset_maturity_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `maturity_date` date NOT NULL,
  `maturity_amount` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_transactions`
--

CREATE TABLE `asset_transactions` (
  `asset_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `folio_no` varchar(200) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `goal` varchar(200) DEFAULT NULL,
  `ref_number` varchar(150) DEFAULT NULL,
  `Bank_AccountNo` varchar(50) DEFAULT NULL,
  `Bank` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reg_date` date NOT NULL,
  `cease_date` date DEFAULT NULL,
  `frequency` varchar(20) NOT NULL,
  `installment_amount` decimal(18,2) NOT NULL,
  `rate_of_return` decimal(18,2) NOT NULL,
  `expected_mat_value` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `narration` text,
  `added_on` date NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `asset_transactions`
--
DELIMITER $$
CREATE TRIGGER `delete_maturity` AFTER DELETE ON `asset_transactions` FOR EACH ROW DELETE FROM asset_maturity WHERE asset_id = OLD.asset_id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `aum_report`
--

CREATE TABLE `aum_report` (
  `broker_name` varchar(50) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `mf_scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `purchase_amount` decimal(52,2) DEFAULT NULL,
  `div_amount` decimal(52,2) DEFAULT NULL,
  `live_unit` decimal(52,4) DEFAULT NULL,
  `current_value` decimal(65,8) DEFAULT NULL,
  `div_r2` decimal(52,10) DEFAULT NULL,
  `div_payout` decimal(52,10) DEFAULT NULL,
  `cagr` decimal(65,8) DEFAULT NULL,
  `cagr1` decimal(65,4) DEFAULT NULL,
  `cagr2` decimal(63,2) DEFAULT NULL,
  `mf_abs` decimal(65,8) DEFAULT NULL,
  `total` decimal(65,10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int(11) NOT NULL,
  `bank_name` varchar(200) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `account_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `account_type` int(50) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `account_number` varchar(30) NOT NULL,
  `IFSC` varchar(30) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_account_types`
--

CREATE TABLE `bank_account_types` (
  `account_type_id` int(11) NOT NULL,
  `account_type_name` varchar(50) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_name_clients_list`
--

CREATE TABLE `bank_name_clients_list` (
  `bank_name` varchar(100) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `dob_app` int(11) NOT NULL DEFAULT '0',
  `occupation_id` int(11) DEFAULT NULL,
  `head_of_family` int(11) DEFAULT '0',
  `relation_HOF` varchar(30) DEFAULT NULL,
  `client_type` int(11) NOT NULL,
  `spouse_name` varchar(150) DEFAULT NULL,
  `anv_date` date DEFAULT NULL,
  `anv_app` int(11) DEFAULT '0',
  `pan_no` varchar(30) NOT NULL,
  `passport_no` varchar(50) DEFAULT NULL,
  `add_flat` varchar(200) DEFAULT NULL,
  `add_street` varchar(200) DEFAULT NULL,
  `add_area` varchar(200) DEFAULT NULL,
  `add_city` varchar(200) DEFAULT NULL,
  `add_state` varchar(50) NOT NULL,
  `add_pin` varchar(10) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `date_of_comm` date DEFAULT NULL,
  `family_id` varchar(30) NOT NULL,
  `children_name` text,
  `report_order` int(11) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `app_access` enum('0','1') NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL,
  `merge_ref_id` varchar(155) DEFAULT NULL,
  `client_category` varchar(50) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `add_info` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pwd_reset` int(11) NOT NULL DEFAULT '0',
  `device_id` varchar(50) DEFAULT NULL,
  `device_token` varchar(500) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `device_os` varchar(50) DEFAULT NULL,
  `device_os_version` varchar(50) DEFAULT NULL,
  `app_version` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_ClientAccountMandateMaster`
--

CREATE TABLE `BSC_ClientAccountMandateMaster` (
  `Id` bigint(20) NOT NULL,
  `MANDATECODE` varchar(50) DEFAULT NULL,
  `CLIENTCODE` varchar(7) DEFAULT NULL,
  `CLIENTNAME` varchar(30) DEFAULT NULL,
  `MEMBERCODE` varchar(50) DEFAULT NULL,
  `BANKNAME` varchar(29) DEFAULT NULL,
  `BANKBRANCH` varchar(35) DEFAULT NULL,
  `AMOUNT` int(7) DEFAULT NULL,
  `REGNDATE` varchar(10) DEFAULT NULL,
  `STATUS` varchar(23) DEFAULT NULL,
  `UMRNNO` varchar(20) DEFAULT NULL,
  `REMARKS` varchar(50) DEFAULT NULL,
  `APPROVEDDATE` varchar(10) DEFAULT NULL,
  `BANKACCOUNTNUMBER` varchar(50) DEFAULT NULL,
  `MANDATECOLLECTIONTYPE` varchar(4) DEFAULT NULL,
  `MANDATETYPE` varchar(3) DEFAULT NULL,
  `DATEOFUPLOAD` varchar(16) DEFAULT NULL,
  `STARTDATE` varchar(10) DEFAULT NULL,
  `ENDDATE` varchar(10) DEFAULT NULL,
  `DATEOFREUPLOAD` varchar(10) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_ClientMaster`
--

CREATE TABLE `BSC_ClientMaster` (
  `Id` bigint(20) NOT NULL,
  `CLIENTCODE` varchar(50) DEFAULT NULL,
  `CLIENTHOLDING` varchar(50) DEFAULT NULL,
  `TAXSTATUS` varchar(50) DEFAULT NULL,
  `OCCUPATION` varchar(50) DEFAULT NULL,
  `FIRSTAPPLICANTNAME` varchar(100) DEFAULT NULL,
  `SECONDAPPLICANTNAME` varchar(100) DEFAULT NULL,
  `THIRDAPPLICANTNAME` varchar(100) DEFAULT NULL,
  `FIRSTAPPLICANTDOB` varchar(20) DEFAULT NULL,
  `FIRSTAPPGENDER` varchar(6) DEFAULT NULL,
  `CLIENTGUARDIAN` varchar(100) DEFAULT NULL,
  `FIRSTAPPLICANTPAN` varchar(15) DEFAULT NULL,
  `CLIENTNOMINEE` varchar(100) DEFAULT NULL,
  `CLIENTNOMINEERELATION` varchar(50) DEFAULT NULL,
  `GUARDIANPAN` varchar(20) DEFAULT NULL,
  `CLIENTTYPE` varchar(20) DEFAULT NULL,
  `CLIENTDEFAULTDP` varchar(20) DEFAULT NULL,
  `CDSLDPID` varchar(20) DEFAULT NULL,
  `CDSLCLTID` varchar(20) DEFAULT NULL,
  `NSDLDPID` varchar(20) DEFAULT NULL,
  `NSDLCLTID` varchar(20) DEFAULT NULL,
  `ACCTYPE1` varchar(50) DEFAULT NULL,
  `ACCNO1` varchar(50) DEFAULT NULL,
  `CLIENTMICRNO1` varchar(20) DEFAULT NULL,
  `NEFTIFSCCODE1` varchar(20) DEFAULT NULL,
  `BANKNAME1` varchar(100) DEFAULT NULL,
  `BANKBRANCH1` varchar(100) DEFAULT NULL,
  `DEFAULTBANKFLAG1` varchar(2) DEFAULT NULL,
  `ACCTYPE2` varchar(50) DEFAULT NULL,
  `ACCNO2` varchar(50) DEFAULT NULL,
  `CLIENTMICRNO2` varchar(20) DEFAULT NULL,
  `NEFTIFSCCODE2` varchar(20) DEFAULT NULL,
  `DEFAULTBANKFLAG2` varchar(2) DEFAULT NULL,
  `BANKNAME2` varchar(100) DEFAULT NULL,
  `BANKBRANCH2` varchar(100) DEFAULT NULL,
  `ACCTYPE3` varchar(20) DEFAULT NULL,
  `ACCNO3` varchar(50) DEFAULT NULL,
  `CLIENTMICRNO3` varchar(20) DEFAULT NULL,
  `NEFTIFSCCODE3` varchar(20) DEFAULT NULL,
  `DefaultBankFlag3` varchar(2) DEFAULT NULL,
  `BANKNAME3` varchar(100) DEFAULT NULL,
  `BankBranch3` varchar(100) DEFAULT NULL,
  `ACCTYPE4` varchar(20) DEFAULT NULL,
  `ACCNO4` varchar(50) DEFAULT NULL,
  `CLIENTMICRNO4` varchar(20) DEFAULT NULL,
  `NEFTIFSCCODE4` varchar(20) DEFAULT NULL,
  `DEFAULTBANKFLAG4` varchar(20) DEFAULT NULL,
  `BANKNAME4` varchar(100) DEFAULT NULL,
  `BANKBRANCH4` varchar(100) DEFAULT NULL,
  `ACCTYPE5` varchar(20) DEFAULT NULL,
  `ACCNO5` varchar(50) DEFAULT NULL,
  `CLIENTMICRNO5` varchar(20) DEFAULT NULL,
  `NEFTIFSCCODE5` varchar(20) DEFAULT NULL,
  `BANKNAME5` varchar(100) DEFAULT NULL,
  `BANKBRANCH5` varchar(100) DEFAULT NULL,
  `DEFAULTBANKFLAG5` varchar(10) DEFAULT NULL,
  `CLIENTCHEQUENAME5` varchar(100) DEFAULT NULL,
  `ADD1` varchar(40) DEFAULT NULL,
  `ADD2` varchar(40) DEFAULT NULL,
  `ADD3` varchar(40) DEFAULT NULL,
  `CITY` varchar(100) DEFAULT NULL,
  `CLIENTSTATE` varchar(100) DEFAULT NULL,
  `PINCODE` int(20) DEFAULT NULL,
  `COUNTRY` varchar(100) DEFAULT NULL,
  `RESIPHONE` varchar(20) DEFAULT NULL,
  `RESIFAX` varchar(20) DEFAULT NULL,
  `OFFICEPHONE` varchar(20) DEFAULT NULL,
  `CLIENTOFFICEFAX` varchar(20) DEFAULT NULL,
  `CLIENTEMAIL` varchar(32) DEFAULT NULL,
  `COMMMODE` varchar(20) DEFAULT NULL,
  `DIVPAYMODE` varchar(20) DEFAULT NULL,
  `SECONDAPPPAN` varchar(20) DEFAULT NULL,
  `THIRDAPPPAN` varchar(20) DEFAULT NULL,
  `MAPINNO` varchar(30) DEFAULT NULL,
  `FORADD1` varchar(50) DEFAULT NULL,
  `FORADD2` varchar(50) DEFAULT NULL,
  `FORADD3` varchar(50) DEFAULT NULL,
  `FORCITY` varchar(100) DEFAULT NULL,
  `FORPINCODE` varchar(20) DEFAULT NULL,
  `FORSTATE` varchar(100) DEFAULT NULL,
  `FORCOUNTRY` varchar(100) DEFAULT NULL,
  `FORRESIPHONE` varchar(20) DEFAULT NULL,
  `FORRESIFAX` varchar(20) DEFAULT NULL,
  `FOROFFPHONE` varchar(20) DEFAULT NULL,
  `FOROFFFAX` varchar(20) DEFAULT NULL,
  `MOBILE` bigint(20) DEFAULT NULL,
  `CKYC` varchar(2) DEFAULT NULL,
  `KYCTYPE1stHOLDER` varchar(20) DEFAULT NULL,
  `KYCTYPE2ndHOLDER` varchar(20) DEFAULT NULL,
  `KYCTYPE3rdHOLDER` varchar(20) DEFAULT NULL,
  `KYCTYPEGUARDIAN` varchar(20) DEFAULT NULL,
  `FirstHolderCKYCNumber` varchar(20) DEFAULT NULL,
  `SecondHolderCKYCNumber` varchar(20) DEFAULT NULL,
  `ThirdHolderCKYCNumber` varchar(20) DEFAULT NULL,
  `GuardianCKYCNumber` varchar(20) DEFAULT NULL,
  `JointHolder1DOB` varchar(20) DEFAULT NULL,
  `JointHolder2DOB` varchar(20) DEFAULT NULL,
  `GuardianCKYCDOB` varchar(20) DEFAULT NULL,
  `DEALER` varchar(50) DEFAULT NULL,
  `BRANCH` varchar(50) DEFAULT NULL,
  `CREATEDBY` varchar(50) DEFAULT NULL,
  `CREATEDAT` varchar(25) DEFAULT NULL,
  `LASTMODIFIEDBY` varchar(50) DEFAULT NULL,
  `LASTMODIFIEDAT` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_ClientMaster_New`
--

CREATE TABLE `BSC_ClientMaster_New` (
  `Id` bigint(20) NOT NULL,
  `MemberCode` varchar(30) DEFAULT NULL,
  `ClientCode` varchar(30) NOT NULL,
  `PrimaryHolderFirstName` varchar(100) DEFAULT NULL,
  `PrimaryHolderMiddleName` varchar(100) DEFAULT NULL,
  `PrimaryHolderLastName` varchar(100) DEFAULT NULL,
  `TaxStatus` varchar(50) DEFAULT NULL,
  `Gender` varchar(30) DEFAULT NULL,
  `PrimaryHolderDOBIncorporation` varchar(20) DEFAULT NULL,
  `OccupationCode` varchar(20) DEFAULT NULL,
  `HoldingNature` varchar(20) DEFAULT NULL,
  `SecondHolderFirstName` varchar(100) DEFAULT NULL,
  `SecondHolderMiddleName` varchar(100) DEFAULT NULL,
  `SecondHolderLastName` varchar(100) DEFAULT NULL,
  `ThirdHolderFirstName` varchar(100) DEFAULT NULL,
  `ThirdHolderMiddleName` varchar(100) DEFAULT NULL,
  `ThirdHolderLastName` varchar(100) DEFAULT NULL,
  `SecondHolderDOB` varchar(20) DEFAULT NULL,
  `ThirdHolderDOB` varchar(20) DEFAULT NULL,
  `GuardianFirstName` varchar(100) DEFAULT NULL,
  `GuardianMiddleName` varchar(100) DEFAULT NULL,
  `GuardianLastName` varchar(100) DEFAULT NULL,
  `GuardianDOB` varchar(20) DEFAULT NULL,
  `PrimaryHolderPANExempt` varchar(20) DEFAULT NULL,
  `SecondHolderPANExempt` varchar(20) DEFAULT NULL,
  `ThirdHolderPANExempt` varchar(20) DEFAULT NULL,
  `GuardianPANExempt` varchar(20) DEFAULT NULL,
  `PrimaryHolderPAN` varchar(20) DEFAULT NULL,
  `SecondHolderPAN` varchar(20) DEFAULT NULL,
  `ThirdHolderPAN` varchar(20) DEFAULT NULL,
  `GuardianPAN` varchar(20) DEFAULT NULL,
  `PrimaryHolderExemptCategory` varchar(50) DEFAULT NULL,
  `SecondHolderExemptCategory` varchar(50) DEFAULT NULL,
  `ThirdHolderExemptCategory` varchar(50) DEFAULT NULL,
  `GuardianExemptCategory` varchar(50) DEFAULT NULL,
  `ClientType` varchar(20) DEFAULT NULL,
  `PMS` varchar(50) DEFAULT NULL,
  `DefaultDP` varchar(20) DEFAULT NULL,
  `CDSLDPID` varchar(20) DEFAULT NULL,
  `CDSLCLTID` varchar(20) DEFAULT NULL,
  `CMBPId` varchar(20) DEFAULT NULL,
  `NSDLDPID` varchar(20) DEFAULT NULL,
  `NSDLCLTID` varchar(20) DEFAULT NULL,
  `AccountType1` varchar(30) DEFAULT NULL,
  `AccountNo1` varchar(30) DEFAULT NULL,
  `MICRNo1` varchar(30) DEFAULT NULL,
  `IFSCCode1` varchar(20) DEFAULT NULL,
  `BankName1` varchar(100) DEFAULT NULL,
  `BankBranch1` varchar(100) DEFAULT NULL,
  `DefaultBankFlag1` varchar(10) DEFAULT NULL,
  `Bank1CreatedAt` varchar(20) DEFAULT NULL,
  `Bank1LastModifiedAt` varchar(20) DEFAULT NULL,
  `Bank1Status` varchar(20) DEFAULT NULL,
  `AccountType2` varchar(30) DEFAULT NULL,
  `AccountNo2` varchar(30) DEFAULT NULL,
  `MICRNo2` varchar(20) DEFAULT NULL,
  `IFSCCode2` varchar(20) DEFAULT NULL,
  `BankName2` varchar(100) DEFAULT NULL,
  `BankBranch2` varchar(100) DEFAULT NULL,
  `DefaultBankFlag2` varchar(10) DEFAULT NULL,
  `Bank2CreatedAt` varchar(20) DEFAULT NULL,
  `Bank2LastModifiedAt` varchar(20) DEFAULT NULL,
  `Bank2Status` varchar(20) DEFAULT NULL,
  `Accounttype3` varchar(20) DEFAULT NULL,
  `AccountNo3` varchar(30) DEFAULT NULL,
  `MICRNo3` varchar(20) DEFAULT NULL,
  `IFSCCode3` varchar(20) DEFAULT NULL,
  `BankName3` varchar(100) DEFAULT NULL,
  `BankBranch3` varchar(100) DEFAULT NULL,
  `DefaultBankFlag3` varchar(10) DEFAULT NULL,
  `Bank3CreatedAt` varchar(20) DEFAULT NULL,
  `Bank3LastModifiedAt` varchar(20) DEFAULT NULL,
  `Bank3Status` varchar(20) DEFAULT NULL,
  `Accounttype4` varchar(20) DEFAULT NULL,
  `AccountNo4` varchar(30) DEFAULT NULL,
  `MICRNo4` varchar(20) DEFAULT NULL,
  `IFSCCode4` varchar(20) DEFAULT NULL,
  `BankName4` varchar(100) DEFAULT NULL,
  `BankBranch4` varchar(100) DEFAULT NULL,
  `DefaultBankFlag4` varchar(10) DEFAULT NULL,
  `Bank4CreatedAt` varchar(20) DEFAULT NULL,
  `Bank4LastModifiedAt` varchar(20) DEFAULT NULL,
  `Bank4Status` varchar(20) DEFAULT NULL,
  `Accounttype5` varchar(20) DEFAULT NULL,
  `AccountNo5` varchar(30) DEFAULT NULL,
  `MICRNo5` varchar(20) DEFAULT NULL,
  `IFSCCode5` varchar(20) DEFAULT NULL,
  `BankName5` varchar(100) DEFAULT NULL,
  `BankBranch5` varchar(100) DEFAULT NULL,
  `DefaultBankFlag5` varchar(10) DEFAULT NULL,
  `Bank5CreatedAt` varchar(20) DEFAULT NULL,
  `Bank5LastModifiedAt` varchar(20) DEFAULT NULL,
  `Bank5Status` varchar(20) DEFAULT NULL,
  `ChequeName` varchar(100) DEFAULT NULL,
  `Divpaymode` varchar(100) DEFAULT NULL,
  `Address1` varchar(1000) DEFAULT NULL,
  `Address2` varchar(1000) DEFAULT NULL,
  `Address3` varchar(1000) DEFAULT NULL,
  `City` varchar(200) DEFAULT NULL,
  `State` varchar(100) DEFAULT NULL,
  `Pincode` varchar(20) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `ResiPhone` varchar(20) DEFAULT NULL,
  `ResiFax` varchar(20) DEFAULT NULL,
  `OfficePhone` varchar(20) DEFAULT NULL,
  `OfficeFax` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `CommunicationMode` varchar(20) DEFAULT NULL,
  `ForeignAddress1` varchar(1000) DEFAULT NULL,
  `ForeignAddress2` varchar(1000) DEFAULT NULL,
  `ForeignAddress3` varchar(1000) DEFAULT NULL,
  `ForeignAddressCity` varchar(100) DEFAULT NULL,
  `ForeignAddressPincode` varchar(20) DEFAULT NULL,
  `ForeignAddressState` varchar(100) DEFAULT NULL,
  `ForeignAddressCountry` varchar(100) DEFAULT NULL,
  `ForeignAddressResiPhone` varchar(20) DEFAULT NULL,
  `ForeignAddressFax` varchar(20) DEFAULT NULL,
  `ForeignAddressOffPhone` varchar(20) DEFAULT NULL,
  `ForeignAddressOffFax` varchar(20) DEFAULT NULL,
  `IndianMobileNo` varchar(20) DEFAULT NULL,
  `Nominee1Name` varchar(500) DEFAULT NULL,
  `Nominee1Relationship` varchar(30) DEFAULT NULL,
  `Nominee1ApplicablePer` int(11) DEFAULT NULL,
  `Nominee1MinorFlag` varchar(10) DEFAULT NULL,
  `Nominee1DOB` varchar(20) DEFAULT NULL,
  `Nominee1Guardian` varchar(500) DEFAULT NULL,
  `Nominee2Name` varchar(500) DEFAULT NULL,
  `Nominee2Relationship` varchar(30) DEFAULT NULL,
  `Nominee2ApplicablePer` int(11) DEFAULT NULL,
  `Nominee2DOB` varchar(20) DEFAULT NULL,
  `Nominee2MinorFlag` varchar(10) DEFAULT NULL,
  `Nominee2Guardian` varchar(500) DEFAULT NULL,
  `Nominee3Name` varchar(500) DEFAULT NULL,
  `Nominee3Relationship` varchar(30) DEFAULT NULL,
  `Nominee3ApplicablePer` int(11) DEFAULT NULL,
  `Nominee3DOB` varchar(20) DEFAULT NULL,
  `Nominee3MinorFlag` varchar(10) DEFAULT NULL,
  `Nominee3Guardian` varchar(500) DEFAULT NULL,
  `PrimaryHolderKYCType` varchar(10) DEFAULT NULL,
  `PrimaryHolderCKYCNumber` varchar(30) DEFAULT NULL,
  `SecondHolderKYCType` varchar(50) DEFAULT NULL,
  `SecondHolderCKYCNumber` varchar(30) DEFAULT NULL,
  `ThirdHolderKYCType` varchar(20) DEFAULT NULL,
  `ThirdHolderCKYCNumber` varchar(30) DEFAULT NULL,
  `GuardianKYCType` varchar(20) DEFAULT NULL,
  `GuardianCKYCNumber` varchar(30) DEFAULT NULL,
  `PrimaryHolderKRAExemptRefNo` varchar(30) DEFAULT NULL,
  `SecondHolderKRAExemptRefNo` varchar(30) DEFAULT NULL,
  `ThirdHolderKRAExemptRefNo` varchar(30) DEFAULT NULL,
  `GuardianExemptRefNo` varchar(30) DEFAULT NULL,
  `AadhaarUpdated` varchar(10) DEFAULT NULL,
  `MapinId` varchar(30) DEFAULT NULL,
  `Paperlessflag` varchar(10) DEFAULT NULL,
  `LEINo` varchar(30) DEFAULT NULL,
  `LEIValidity` varchar(20) DEFAULT NULL,
  `EmailDeclarationFlag` varchar(20) DEFAULT NULL,
  `MobileDeclarationFlag` varchar(20) DEFAULT NULL,
  `Branch` varchar(50) DEFAULT NULL,
  `Dealer` varchar(100) DEFAULT NULL,
  `NominationOpt` varchar(10) DEFAULT NULL,
  `NominationAuthenticationMode` varchar(100) DEFAULT NULL,
  `Nominee1PAN` varchar(20) DEFAULT NULL,
  `Nominee1GuardianPAN` varchar(20) DEFAULT NULL,
  `Nominee2PAN` varchar(20) DEFAULT NULL,
  `Nominee2GuardianPAN` varchar(20) DEFAULT NULL,
  `Nominee3PAN` varchar(20) DEFAULT NULL,
  `Nominee3GuardianPAN` varchar(20) DEFAULT NULL,
  `SecondHolderEmail` varchar(100) DEFAULT NULL,
  `SecondholderEmailDeclaration` varchar(100) DEFAULT NULL,
  `SecondholderMobile` varchar(20) DEFAULT NULL,
  `SecondholderMobileDeclaration` varchar(20) DEFAULT NULL,
  `ThirdHolderEmail` varchar(100) DEFAULT NULL,
  `ThirdholderEmailDeclaration` varchar(100) DEFAULT NULL,
  `ThirdholderMobile` varchar(20) DEFAULT NULL,
  `ThirdholderMobileDeclaration` varchar(20) DEFAULT NULL,
  `NominationFlag` varchar(10) DEFAULT NULL,
  `NominationAuthenticationDate` varchar(20) DEFAULT NULL,
  `CreatedBy` varchar(100) DEFAULT NULL,
  `CreatedAt` varchar(100) DEFAULT NULL,
  `LastModifiedBy` varchar(20) DEFAULT NULL,
  `LastModifiedAt` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Bsc_MFFolio`
--

CREATE TABLE `Bsc_MFFolio` (
  `Id` bigint(20) NOT NULL,
  `RtaName` varchar(500) DEFAULT NULL,
  `FileName` varchar(100) DEFAULT NULL,
  `Folio` varchar(100) DEFAULT NULL,
  `ClientName` varchar(200) DEFAULT NULL,
  `HoldingNature` varchar(50) DEFAULT NULL,
  `JointHolder1` varchar(200) DEFAULT NULL,
  `JointHolder2` varchar(200) DEFAULT NULL,
  `PanNo` varchar(50) DEFAULT NULL,
  `JointHolder1PanNo` varchar(50) DEFAULT NULL,
  `JointHolder2PanNo` varchar(50) DEFAULT NULL,
  `GuardianPanNo` varchar(50) DEFAULT NULL,
  `ChannelPartnerCode` varchar(400) DEFAULT NULL,
  `UpdatedBy` int(50) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_Payment_Request`
--

CREATE TABLE `BSC_Payment_Request` (
  `id` int(11) NOT NULL,
  `clientcode` varchar(50) NOT NULL,
  `membercode` varchar(50) NOT NULL,
  `modeofpayment` varchar(50) NOT NULL,
  `bankid` varchar(100) NOT NULL,
  `accountnumber` varchar(100) NOT NULL,
  `ifsc` varchar(100) NOT NULL,
  `ordernumber` varchar(255) NOT NULL,
  `totalamount` varchar(255) NOT NULL,
  `internalrefno` varchar(255) NOT NULL,
  `NEFTreference` varchar(255) NOT NULL,
  `mandateid` varchar(255) NOT NULL,
  `vpaid` varchar(255) NOT NULL,
  `loopbackURL` text NOT NULL,
  `allowloopBack` text NOT NULL,
  `filler1` varchar(255) NOT NULL,
  `filler2` varchar(255) NOT NULL,
  `filler3` varchar(255) NOT NULL,
  `filler4` varchar(255) NOT NULL,
  `filler5` varchar(255) NOT NULL,
  `CreatedBy` int(11) NOT NULL,
  `CreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_Payment_Response`
--

CREATE TABLE `BSC_Payment_Response` (
  `id` int(11) NOT NULL,
  `internalrefno` varchar(100) NOT NULL,
  `statuscode` varchar(100) NOT NULL,
  `responsestring` text NOT NULL,
  `filler1` varchar(255) NOT NULL,
  `filler2` varchar(255) NOT NULL,
  `filler3` varchar(255) NOT NULL,
  `filler4` varchar(255) NOT NULL,
  `filler5` varchar(255) NOT NULL,
  `CreatedBy` int(11) NOT NULL,
  `CreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_SchemeMaster`
--

CREATE TABLE `BSC_SchemeMaster` (
  `Id` int(11) NOT NULL,
  `UniqueNo` int(5) DEFAULT NULL,
  `SchemeCode` varchar(20) DEFAULT NULL,
  `RTASchemeCode` varchar(5) DEFAULT NULL,
  `AMCSchemeCode` varchar(10) DEFAULT NULL,
  `ISIN` varchar(12) DEFAULT NULL,
  `AMCCode` varchar(32) DEFAULT NULL,
  `AMCName` varchar(500) DEFAULT NULL,
  `SchemeType` varchar(11) DEFAULT NULL,
  `SchemePlan` varchar(6) DEFAULT NULL,
  `SchemeName` varchar(129) DEFAULT NULL,
  `PurchaseAllowed` varchar(1) DEFAULT NULL,
  `PurchaseTransactionmode` varchar(2) DEFAULT NULL,
  `MinimumPurchaseAmount` decimal(10,2) DEFAULT NULL,
  `AdditionalPurchaseAmount` decimal(9,2) DEFAULT NULL,
  `MaximumPurchaseAmount` bigint(10) DEFAULT NULL,
  `PurchaseAmountMultiplier` decimal(10,3) DEFAULT NULL,
  `PurchaseCutoffTime` varchar(8) DEFAULT NULL,
  `RedemptionAllowed` varchar(1) DEFAULT NULL,
  `RedemptionTransactionMode` varchar(2) DEFAULT NULL,
  `MinimumRedemptionQty` decimal(12,3) DEFAULT NULL,
  `RedemptionQtyMultiplier` decimal(12,3) DEFAULT NULL,
  `MaximumRedemptionQty` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMinimum` decimal(12,3) DEFAULT NULL,
  `RedemptionAmountOtherMaximum` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMultiple` decimal(11,3) DEFAULT NULL,
  `RedemptionCutoffTime` varchar(8) DEFAULT NULL,
  `RTAAgentCode` varchar(8) DEFAULT NULL,
  `AMCActiveFlag` int(1) DEFAULT NULL,
  `DividendReinvestmentFlag` varchar(1) DEFAULT NULL,
  `SIPFLAG` varchar(1) DEFAULT NULL,
  `STPFLAG` varchar(1) DEFAULT NULL,
  `SWPFlag` varchar(1) DEFAULT NULL,
  `SwitchFLAG` varchar(1) DEFAULT NULL,
  `SETTLEMENTTYPE` varchar(3) DEFAULT NULL,
  `AMC_IND` varchar(10) DEFAULT NULL,
  `FaceValue` int(4) DEFAULT NULL,
  `StartDate` varchar(11) DEFAULT NULL,
  `EndDate` varchar(11) DEFAULT NULL,
  `ExitLoadFlag` varchar(1) DEFAULT NULL,
  `ExitLoad` int(1) DEFAULT NULL,
  `LockInPeriodFlag` varchar(1) DEFAULT NULL,
  `LockInPeriod` varchar(4) DEFAULT NULL,
  `ChannelPartnerCode` varchar(10) DEFAULT NULL,
  `tenseSchemeId` varchar(5) DEFAULT NULL,
  `SIPDATES` varchar(500) DEFAULT NULL,
  `SIPMINIMUMINSTALLMENTAMOUNT` bigint(20) DEFAULT NULL,
  `SIPMAXIMUMINSTALLMENTAMOUNT` bigint(20) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_SchemeMaster_old`
--

CREATE TABLE `BSC_SchemeMaster_old` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `UniqueNo` int(5) DEFAULT NULL,
  `SchemeCode` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `RTASchemeCode` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `AMCSchemeCode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `ISIN` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `AMCCode` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `AMCName` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `SchemeType` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `SchemePlan` varchar(6) CHARACTER SET utf8 DEFAULT NULL,
  `SchemeName` varchar(129) CHARACTER SET utf8 DEFAULT NULL,
  `PurchaseAllowed` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `PurchaseTransactionmode` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
  `MinimumPurchaseAmount` decimal(10,2) DEFAULT NULL,
  `AdditionalPurchaseAmount` decimal(9,2) DEFAULT NULL,
  `MaximumPurchaseAmount` bigint(10) DEFAULT NULL,
  `PurchaseAmountMultiplier` decimal(10,3) DEFAULT NULL,
  `PurchaseCutoffTime` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `RedemptionAllowed` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `RedemptionTransactionMode` varchar(2) CHARACTER SET utf8 DEFAULT NULL,
  `MinimumRedemptionQty` decimal(12,3) DEFAULT NULL,
  `RedemptionQtyMultiplier` decimal(12,3) DEFAULT NULL,
  `MaximumRedemptionQty` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMinimum` decimal(12,3) DEFAULT NULL,
  `RedemptionAmountOtherMaximum` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMultiple` decimal(11,3) DEFAULT NULL,
  `RedemptionCutoffTime` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `RTAAgentCode` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `AMCActiveFlag` int(1) DEFAULT NULL,
  `DividendReinvestmentFlag` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `SIPFLAG` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `STPFLAG` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `SWPFlag` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `SwitchFLAG` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `SETTLEMENTTYPE` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `AMC_IND` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `FaceValue` int(4) DEFAULT NULL,
  `StartDate` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `EndDate` varchar(11) CHARACTER SET utf8 DEFAULT NULL,
  `ExitLoadFlag` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `ExitLoad` int(1) DEFAULT NULL,
  `LockInPeriodFlag` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `LockInPeriod` varchar(4) CHARACTER SET utf8 DEFAULT NULL,
  `ChannelPartnerCode` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `tenseSchemeId` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `SIPDATES` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `SIPMINIMUMINSTALLMENTAMOUNT` bigint(20) DEFAULT NULL,
  `SIPMAXIMUMINSTALLMENTAMOUNT` bigint(20) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_Transcation_Request`
--

CREATE TABLE `BSC_Transcation_Request` (
  `Id` bigint(20) NOT NULL,
  `Type` varchar(30) DEFAULT NULL,
  `ServiceURL` varchar(1000) DEFAULT NULL,
  `TranscationType` varchar(50) DEFAULT NULL,
  `TransNo` varchar(50) DEFAULT NULL,
  `TransactionCode` varchar(3) DEFAULT NULL,
  `OrderId` bigint(20) DEFAULT NULL,
  `UserID` bigint(20) DEFAULT NULL,
  `MemberId` varchar(20) DEFAULT NULL,
  `ClientCode` varchar(20) DEFAULT NULL,
  `SchemeCd` varchar(20) DEFAULT NULL,
  `BuySell` varchar(2) DEFAULT NULL,
  `BuySellType` varchar(10) DEFAULT NULL,
  `DPTxn` varchar(10) DEFAULT NULL,
  `Amount` decimal(10,0) DEFAULT NULL,
  `Qty` decimal(10,0) DEFAULT NULL,
  `AllRedeem` varchar(2) DEFAULT NULL,
  `FolioNo` varchar(20) DEFAULT NULL,
  `Remarks` varchar(500) DEFAULT NULL,
  `KYCStatus` varchar(2) DEFAULT NULL,
  `SubBrCode` varchar(20) DEFAULT NULL,
  `EUIN` varchar(20) DEFAULT NULL,
  `EUINVal` varchar(2) DEFAULT NULL,
  `MinRedeem` varchar(2) DEFAULT NULL,
  `DPC` varchar(2) DEFAULT NULL,
  `IPAdd` varchar(20) DEFAULT NULL,
  `Password` varchar(250) DEFAULT NULL,
  `PassKey` varchar(20) DEFAULT NULL,
  `Param1` varchar(20) DEFAULT NULL,
  `Param2` varchar(20) DEFAULT NULL,
  `Param3` varchar(20) DEFAULT NULL,
  `SIPStartDate` varchar(30) DEFAULT NULL,
  `SIPFrequency` varchar(30) DEFAULT NULL,
  `MendateId` varchar(50) DEFAULT NULL,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `CreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BSC_Transcation_Response`
--

CREATE TABLE `BSC_Transcation_Response` (
  `Id` bigint(20) NOT NULL,
  `TransactionCode` varchar(5) DEFAULT NULL,
  `UniqueReferenceNumber` varchar(20) DEFAULT NULL,
  `OrderId` bigint(20) DEFAULT NULL,
  `UserID` bigint(20) DEFAULT NULL,
  `MemberId` varchar(20) DEFAULT NULL,
  `ClientCode` varchar(20) DEFAULT NULL,
  `BSCRemarks` varchar(1000) DEFAULT NULL,
  `SuccessFlag` varchar(2) DEFAULT NULL,
  `SIP_REG_ID` int(11) DEFAULT NULL,
  `XSIP_REG_ID` int(11) DEFAULT NULL,
  `RequestString` text,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `CreatedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_commitments`
-- (See below for the actual view)
--
CREATE TABLE `cf_commitments` (
`investment` varchar(11)
,`client_id` varchar(30)
,`comp_date` date
,`year` bigint(20)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_fd`
-- (See below for the actual view)
--
CREATE TABLE `cf_fd` (
`investment` varchar(2)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_fd1`
-- (See below for the actual view)
--
CREATE TABLE `cf_fd1` (
`investment` varchar(2)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
,`fd_comp_name` varchar(200)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_fd_maturity`
-- (See below for the actual view)
--
CREATE TABLE `cf_fd_maturity` (
`investment` varchar(11)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_insurance`
-- (See below for the actual view)
--
CREATE TABLE `cf_insurance` (
`investment` varchar(9)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_insurance_life_cover`
-- (See below for the actual view)
--
CREATE TABLE `cf_insurance_life_cover` (
`investment` varchar(10)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(10,0)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_insurance_premium`
-- (See below for the actual view)
--
CREATE TABLE `cf_insurance_premium` (
`investment` varchar(17)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(10,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `cf_rent`
-- (See below for the actual view)
--
CREATE TABLE `cf_rent` (
`investment` varchar(11)
,`client_id` varchar(30)
,`comp_date` date
,`year` int(4)
,`amount` decimal(18,2)
,`name` varchar(150)
,`broker_id` varchar(10)
);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `dob_app` int(11) NOT NULL DEFAULT '0',
  `occupation_id` int(11) DEFAULT NULL,
  `head_of_family` int(11) DEFAULT '0',
  `relation_HOF` varchar(30) DEFAULT NULL,
  `client_type` int(11) NOT NULL,
  `spouse_name` varchar(150) DEFAULT NULL,
  `anv_date` date DEFAULT NULL,
  `anv_app` int(11) DEFAULT '0',
  `pan_no` varchar(30) NOT NULL,
  `passport_no` varchar(50) DEFAULT NULL,
  `add_flat` varchar(200) DEFAULT NULL,
  `add_street` varchar(200) DEFAULT NULL,
  `add_area` varchar(200) DEFAULT NULL,
  `add_city` varchar(200) DEFAULT NULL,
  `add_state` varchar(50) NOT NULL,
  `add_pin` varchar(10) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `date_of_comm` date DEFAULT NULL,
  `family_id` varchar(30) NOT NULL,
  `children_name` text,
  `report_order` int(11) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `app_access` enum('0','1') NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL,
  `merge_ref_id` varchar(155) DEFAULT NULL,
  `client_category` varchar(50) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `add_info` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pwd_reset` int(11) NOT NULL DEFAULT '0',
  `device_id` varchar(50) DEFAULT NULL,
  `device_token` varchar(500) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `device_os` varchar(50) DEFAULT NULL,
  `device_os_version` varchar(50) DEFAULT NULL,
  `app_version` varchar(50) DEFAULT NULL,
  `bscclientid` varchar(50) DEFAULT NULL,
  `kycstatus` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `clients`
--
DELIMITER $$
CREATE TRIGGER `add_client_id` BEFORE INSERT ON `clients` FOR EACH ROW SET new.client_id = clientID()
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `client_merge_update` AFTER UPDATE ON `clients` FOR EACH ROW BEGIN
DECLARE old_ref_id VARCHAR(30);
DECLARE new_ref_id VARCHAR(30);
DECLARE clientID VARCHAR(30);
DECLARE ref_orig_id VARCHAR(30);
DECLARE ref_orig_name VARCHAR(150);
SET old_ref_id = OLD.merge_ref_id;
SET new_ref_id = NEW.merge_ref_id;
IF (old_ref_id != new_ref_id OR old_ref_id IS NULL) AND new_ref_id != "" AND new_ref_id IS NOT NULL THEN 
SET clientID = NEW.client_id;
SET ref_orig_id = NEW.merge_ref_id;
SELECT name INTO ref_orig_name FROM clients WHERE client_id = ref_orig_id;
SET foreign_key_checks = 0;
UPDATE add_funds SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE asset_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE bank_accounts SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE client_brokers SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE client_contact_details SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE commodity_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE complete_reminders SET client_id = ref_orig_id, client_name = ref_orig_name WHERE client_id = clientID;
UPDATE demat_accounts SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE equities SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE fd_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE insurances SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE insurance_policies SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE liability_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE mutual_fund_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE premium_maturities SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE premium_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE property_transactions SET client_id = ref_orig_id WHERE client_id = clientID;
UPDATE today_reminders SET client_id = ref_orig_id, client_name = ref_orig_name WHERE client_id = clientID;
UPDATE withdraw_funds SET client_id = ref_orig_id WHERE client_id = clientID;
SET foreign_key_checks = 1;
END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_client_bank_details` AFTER DELETE ON `clients` FOR EACH ROW BEGIN
DELETE FROM client_bank_details WHERE client_bank_details.client_id = old.client_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `clients_0004_11092021`
--

CREATE TABLE `clients_0004_11092021` (
  `name` varchar(150) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `pan_no` varchar(30) NOT NULL,
  `dob` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_bank_details`
--

CREATE TABLE `client_bank_details` (
  `bid` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `folio_number` varchar(255) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_branch` varchar(100) NOT NULL,
  `bank_acc_no` varchar(100) NOT NULL,
  `bank_ifsc` varchar(100) NOT NULL,
  `bank_account_types` varchar(100) NOT NULL,
  `bank_address_building` varchar(100) NOT NULL,
  `bank_address_road` varchar(100) NOT NULL,
  `bank_address_area` varchar(100) NOT NULL,
  `bank_address_city` varchar(100) NOT NULL,
  `bank_pincode` int(25) NOT NULL,
  `bank_state` varchar(200) NOT NULL,
  `bank_country` varchar(200) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `productId` varchar(155) NOT NULL,
  `jointName1` varchar(155) NOT NULL,
  `jointName2` varchar(155) NOT NULL,
  `pan_no` varchar(155) NOT NULL,
  `joint1_pan` varchar(155) NOT NULL,
  `joint2_pan` varchar(155) NOT NULL,
  `guard_pan` varchar(155) NOT NULL,
  `tax_status` varchar(155) NOT NULL,
  `broker_code` varchar(155) NOT NULL,
  `sub_boroker` varchar(155) NOT NULL,
  `client_family_broker_id` varchar(30) NOT NULL,
  `occ_name` varchar(155) NOT NULL,
  `mode_holding` varchar(155) NOT NULL,
  `nominee_name1` varchar(155) NOT NULL,
  `nom1_relation` varchar(155) NOT NULL,
  `nominee_name2` varchar(155) NOT NULL,
  `nom2_relation` varchar(155) NOT NULL,
  `guardian_name` varchar(155) NOT NULL,
  `folioDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_bank_details_rita`
--

CREATE TABLE `client_bank_details_rita` (
  `bid` int(11) NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `folio_number` varchar(255) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_branch` varchar(100) NOT NULL,
  `bank_acc_no` varchar(100) NOT NULL,
  `bank_ifsc` varchar(100) NOT NULL,
  `bank_account_types` varchar(100) NOT NULL,
  `bank_address_building` varchar(100) NOT NULL,
  `bank_address_road` varchar(100) NOT NULL,
  `bank_address_area` varchar(100) NOT NULL,
  `bank_address_city` varchar(100) NOT NULL,
  `bank_pincode` int(25) NOT NULL,
  `bank_state` varchar(200) NOT NULL,
  `bank_country` varchar(200) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `productId` varchar(155) NOT NULL,
  `jointName1` varchar(155) NOT NULL,
  `jointName2` varchar(155) NOT NULL,
  `pan_no` varchar(155) NOT NULL,
  `joint1_pan` varchar(155) NOT NULL,
  `joint2_pan` varchar(155) NOT NULL,
  `guard_pan` varchar(155) NOT NULL,
  `tax_status` varchar(155) NOT NULL,
  `broker_code` varchar(155) NOT NULL,
  `sub_boroker` varchar(155) NOT NULL,
  `client_family_broker_id` varchar(30) NOT NULL,
  `occ_name` varchar(155) NOT NULL,
  `mode_holding` varchar(155) NOT NULL,
  `nominee_name1` varchar(155) NOT NULL,
  `nom1_relation` varchar(155) NOT NULL,
  `nominee_name2` varchar(155) NOT NULL,
  `nom2_relation` varchar(155) NOT NULL,
  `guardian_name` varchar(155) NOT NULL,
  `folioDate` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_brokers`
--

CREATE TABLE `client_brokers` (
  `id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker` int(11) NOT NULL,
  `client_code` varchar(100) NOT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `held_type` varchar(50) NOT NULL DEFAULT 'Non-held'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_brokers_history`
--

CREATE TABLE `client_brokers_history` (
  `Id` bigint(20) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `broker` int(11) DEFAULT NULL,
  `client_code` varchar(100) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `held_type` varchar(50) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_contact_details`
--

CREATE TABLE `client_contact_details` (
  `client_contact_id` int(11) NOT NULL,
  `contact_category_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `flat` varchar(200) DEFAULT NULL,
  `street` varchar(200) DEFAULT NULL,
  `area` varchar(200) DEFAULT NULL,
  `city` varchar(200) NOT NULL DEFAULT 'N/A',
  `state` varchar(50) NOT NULL DEFAULT 'N/A',
  `pin` varchar(10) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL DEFAULT 'N/A',
  `email_id` varchar(50) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_device_detail`
--

CREATE TABLE `client_device_detail` (
  `Id` bigint(20) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `device_token` varchar(1000) NOT NULL,
  `device_type` varchar(50) NOT NULL,
  `device_os` varchar(50) NOT NULL,
  `device_os_version` varchar(50) NOT NULL,
  `app_version` varchar(50) NOT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_logs`
--

CREATE TABLE `client_logs` (
  `id` int(11) NOT NULL,
  `client_id` varchar(30) DEFAULT NULL,
  `message` text,
  `other` text,
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client_types`
--

CREATE TABLE `client_types` (
  `client_type_id` int(11) NOT NULL,
  `client_type_name` varchar(30) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commodity_items`
--

CREATE TABLE `commodity_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commodity_rates`
--

CREATE TABLE `commodity_rates` (
  `commodity_rate_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `current_rate` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commodity_transactions`
--

CREATE TABLE `commodity_transactions` (
  `commodity_trans_id` varchar(30) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `commodity_item_id` int(11) NOT NULL,
  `transaction_rate` decimal(18,2) NOT NULL DEFAULT '0.00',
  `quantity` float(11,5) NOT NULL,
  `commodity_unit_id` int(11) NOT NULL,
  `quality` varchar(300) DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `adviser_id` int(11) NOT NULL,
  `total_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `initial_investment` int(11) NOT NULL DEFAULT '0',
  `sale_ref` varchar(30) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `added_on` date DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commodity_units`
--

CREATE TABLE `commodity_units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `complete_reminders`
--

CREATE TABLE `complete_reminders` (
  `reminder_id` bigint(20) UNSIGNED NOT NULL,
  `reminder_type` varchar(200) DEFAULT NULL,
  `client_id` varchar(20) DEFAULT NULL,
  `client_name` varchar(200) DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `reminder_message` text,
  `reminder_status` varchar(50) DEFAULT NULL,
  `next_date` date DEFAULT NULL,
  `remark` text,
  `concern_user` varchar(200) DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `completed_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact_categories`
--

CREATE TABLE `contact_categories` (
  `contact_category_id` int(11) NOT NULL,
  `contact_category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `copy_users`
--

CREATE TABLE `copy_users` (
  `id` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(10000) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `user_type` varchar(10) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `add_info` text,
  `broker_id` varchar(10) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '3' COMMENT '1- Read, 2-Read and write, 3. All',
  `pwd_reset` int(11) NOT NULL DEFAULT '0',
  `client_limit` int(11) NOT NULL DEFAULT '50000',
  `client_access` int(11) NOT NULL DEFAULT '5000',
  `user_limit` int(11) NOT NULL DEFAULT '50',
  `arn` varchar(20) NOT NULL,
  `cams_rta_password` varchar(50) NOT NULL,
  `karvy_rta_password` varchar(50) NOT NULL,
  `mailback_mail` varchar(100) NOT NULL,
  `EUIN` varchar(100) DEFAULT NULL,
  `BSCUserId` varchar(100) DEFAULT NULL,
  `BSCMemberId` varchar(100) DEFAULT NULL,
  `BSCPassword` varchar(100) DEFAULT NULL,
  `BSCTransUniqueRefNo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `demat_accounts`
--

CREATE TABLE `demat_accounts` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `type_of_account` varchar(50) NOT NULL,
  `account_number` varchar(100) NOT NULL,
  `demat_id` varchar(100) NOT NULL,
  `user_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `demat_providers`
--

CREATE TABLE `demat_providers` (
  `provider_id` int(11) NOT NULL,
  `demat_provider` varchar(200) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `document_type_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equities`
--

CREATE TABLE `equities` (
  `equity_transaction_id` bigint(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `trading_broker_id` int(11) NOT NULL,
  `client_code` varchar(100) NOT NULL,
  `transaction_date` date NOT NULL,
  `scrip_name` varchar(100) DEFAULT NULL,
  `scrip_code` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `tracking` int(11) DEFAULT '0',
  `initial_investment` int(11) DEFAULT '0',
  `acquiring_rate` decimal(18,2) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` date DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equities_apc`
--

CREATE TABLE `equities_apc` (
  `id` bigint(20) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `client_code` varchar(100) NOT NULL,
  `scrip_code` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `apc` decimal(18,2) NOT NULL,
  `broker_Id` varchar(100) NOT NULL,
  `added_by` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equities_history`
--

CREATE TABLE `equities_history` (
  `equities_history_id` bigint(20) NOT NULL,
  `client_id` varchar(30) DEFAULT NULL,
  `client_code` varchar(100) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `scrip_code` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `close_rate` decimal(10,2) DEFAULT NULL,
  `apc` decimal(18,2) NOT NULL DEFAULT '0.00',
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equities_monthly_summary`
--

CREATE TABLE `equities_monthly_summary` (
  `Id` bigint(20) NOT NULL,
  `value` bigint(20) DEFAULT NULL,
  `client_id` varchar(30) NOT NULL,
  `CreatedDTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `error_log_id` int(11) NOT NULL,
  `error_on` varchar(100) DEFAULT NULL,
  `remarks` text,
  `error_msg` text,
  `broker_id` varchar(10) DEFAULT NULL,
  `error_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `families`
--

CREATE TABLE `families` (
  `family_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `broker_id` varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `families_blank_client`
--

CREATE TABLE `families_blank_client` (
  `family_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `broker_id` varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `families_not_client_exist`
--

CREATE TABLE `families_not_client_exist` (
  `family_id` varchar(30) NOT NULL,
  `name` varchar(150) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `broker_id` varchar(10) NOT NULL,
  `status` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `FDRateMaster`
--

CREATE TABLE `FDRateMaster` (
  `Id` int(11) NOT NULL,
  `CompanyName` varchar(500) DEFAULT NULL,
  `Period` int(11) DEFAULT NULL,
  `NonCumulativeMonthly` decimal(10,2) DEFAULT NULL,
  `NonCumulativeQuatuerly` decimal(10,2) DEFAULT NULL,
  `NonCumulativeHalfYearly` decimal(10,2) DEFAULT NULL,
  `NonCumulativeYearly` decimal(10,2) DEFAULT NULL,
  `Cumulative` decimal(10,2) DEFAULT NULL,
  `IsSeniorCitizen` varchar(10) DEFAULT NULL,
  `UpdatedBy` varchar(50) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fd_companies`
--

CREATE TABLE `fd_companies` (
  `fd_comp_id` int(11) NOT NULL,
  `fd_comp_name` varchar(200) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fd_interests`
--

CREATE TABLE `fd_interests` (
  `interest_id` bigint(20) UNSIGNED NOT NULL,
  `fd_transaction_id` int(11) NOT NULL,
  `interest_date` date NOT NULL,
  `interest_amount` decimal(18,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fd_investment_types`
--

CREATE TABLE `fd_investment_types` (
  `fd_inv_id` int(11) NOT NULL,
  `fd_inv_type` varchar(200) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fd_payout_modes`
--

CREATE TABLE `fd_payout_modes` (
  `payout_mode_id` int(11) NOT NULL,
  `payout_mode` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fd_transactions`
--

CREATE TABLE `fd_transactions` (
  `fd_transaction_id` int(11) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `fd_inv_id` int(11) NOT NULL,
  `fd_comp_id` int(11) NOT NULL,
  `fd_method` varchar(100) NOT NULL,
  `ref_number` varchar(100) NOT NULL,
  `issued_date` date NOT NULL,
  `amount_invested` decimal(18,2) NOT NULL,
  `interest_rate` decimal(18,2) NOT NULL,
  `int_round_off` int(11) NOT NULL DEFAULT '0' COMMENT 'if 1 then round-off',
  `interest_mode` varchar(50) NOT NULL,
  `maturity_date` date NOT NULL,
  `maturity_amount` decimal(18,2) NOT NULL,
  `nominee` varchar(200) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `adjustment_flag` int(11) NOT NULL DEFAULT '0',
  `adjustment` varchar(300) DEFAULT NULL,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `inv_bank_id` int(11) DEFAULT NULL,
  `inv_cheque_number` varchar(150) DEFAULT NULL,
  `inv_account_number` varchar(150) DEFAULT NULL,
  `inv_cheque_date` date NOT NULL,
  `inv_amount` decimal(18,2) NOT NULL,
  `maturity_bank_id` int(11) DEFAULT NULL,
  `maturity_account_number` varchar(150) DEFAULT NULL,
  `maturity_payout_id` int(11) DEFAULT NULL,
  `adv_id` int(11) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(10) NOT NULL,
  `sf_id` varchar(20) NOT NULL,
  `ext_id` varchar(20) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `private` varchar(50) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `url` longtext NOT NULL,
  `service` varchar(50) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `new_url` longtext,
  `downloaded` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fund_options`
--

CREATE TABLE `fund_options` (
  `policy_number` varchar(100) NOT NULL,
  `fund_option` varchar(50) NOT NULL,
  `value` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurances`
--

CREATE TABLE `insurances` (
  `policy_num` varchar(100) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `ins_comp_id` int(11) NOT NULL,
  `plan_type_id` int(11) NOT NULL,
  `paidup_date` date NOT NULL,
  `maturity_date` date NOT NULL,
  `amt_insured` decimal(18,2) NOT NULL,
  `commence_date` date NOT NULL,
  `mode` int(11) NOT NULL,
  `prem_amt` decimal(18,2) NOT NULL,
  `prem_type` int(11) NOT NULL,
  `prem_pay_mode_id` int(11) NOT NULL,
  `next_prem_due_date` date NOT NULL,
  `grace_due_date` date NOT NULL,
  `status` int(11) NOT NULL,
  `remarks` text,
  `fund_value` decimal(18,2) NOT NULL DEFAULT '0.00',
  `prem_paid_till_date` decimal(18,2) NOT NULL DEFAULT '0.00',
  `mat_type` varchar(50) NOT NULL,
  `adv_id` int(11) DEFAULT NULL,
  `nominee` varchar(200) DEFAULT NULL,
  `adjustment_flag` int(10) NOT NULL DEFAULT '0',
  `adjustment` varchar(300) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `insurances`
--
DELIMITER $$
CREATE TRIGGER `addPremiumDetails` AFTER INSERT ON `insurances` FOR EACH ROW BEGIN
	Declare comDate date;
	Declare pptDate date;
	Declare tempDate date;
	Declare benifitDate date;
	Declare amountInsured float;
	declare premiumAmount float;
	Declare premiumPayingMode int;
	Declare policyNumber varchar(100);
	Declare brokerID varchar(10);
	Declare j int;
    Declare updated INT;
	set j=0;
	set policyNumber = NEW.policy_num;
	set comDate = NEW.commence_date;
	set pptDate = NEW.paidup_date;
	set benifitDate = NEW.maturity_date;
	set amountInsured = NEW.amt_insured;
	set premiumAmount = NEW.prem_amt;
	set premiumPayingMode = NEW.mode;
	set brokerID = NEW.broker_id;
	if (premiumPayingMode = 1)
	then
		set j=1;		
		#set pptDate=DATE_SUB(pptDate, INTERVAL 1 YEAR);
	elseif (premiumPayingMode = 2)
	then
		set j=2;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 6 MONTH);
	elseif (premiumPayingMode = 3)
	then
		set j=3;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 3 MONTH);
	elseif (premiumPayingMode = 4)
	then
		set j=4;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 1 MONTH);
	elseif (premiumPayingMode = 5)
	then
		set j=5;
		#set pptDate=DATE_SUB(pptDate, INTERVAL 1 YEAR);
	end if;
    set tempDate = comDate;
	while(tempDate <= pptDate)
	do
		if(j = 1)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
		elseif(j = 2)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 6 MONTH);
		elseif(j = 3)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 3 MONTH);
		elseif (j = 4)
		then
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 MONTH);
		else
			insert into premium_paying_details values(null, policyNumber, tempDate, premiumAmount, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
		end if;
	end while;
	-- To add information in Life cover
	set tempDate=comDate;
	while(tempDate<=benifitDate)
	do		
		insert into insurance_life_covers values(policyNumber,tempDate,amountInsured, brokerID);
		set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
	end while;
    #set updated = updatePaidupDate(policyNumber,pptDate,brokerID);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `onUpdateValues` AFTER UPDATE ON `insurances` FOR EACH ROW BEGIN
	Declare comDate date;
	Declare tempDate date;
	Declare benefitDate date;
	Declare oldBenefitDate date;
	Declare amountInsured float;
	Declare policyNumber varchar(100);
	Declare matType varchar(20);
	Declare brokerID varchar(10);
    Declare updated INT;
	set policyNumber = NEW.policy_num;
	set comDate = NEW.commence_date;
	set benefitDate = NEW.maturity_date;
	set oldBenefitDate = OLD.maturity_date;
	set amountInsured = NEW.amt_insured;
	set brokerID = NEW.broker_id;
	set matType = NEW.mat_type;
	/*check if old matDate is different than new matDate,
	if yes, then delete and add new records in insurance_life_covers */
	if(benefitDate != oldBenefitDate) then
		delete from insurance_life_covers 
		where policy_num = policyNumber and broker_id = brokerID;
		set tempDate=comDate;
        while(tempDate<=benefitDate)
        do		
            insert into insurance_life_covers values(policyNumber,tempDate,amountInsured, brokerID);
            set tempDate=DATE_ADD(tempDate, INTERVAL 1 YEAR);
        end while;
		/*change maturity_date in premium_maturities*/
		/*if(matType = 'Single') then 
			update premium_maturities set maturity_date = benefitDate 
			where policy_num = policyNumber;
		end if;
		update premium_maturities set maturity_date = benefitDate 
		where policy_num = policyNumber order by maturity_date asc limit 1;*/
	end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `insurances_nomiee`
--

CREATE TABLE `insurances_nomiee` (
  `policy_num` varchar(100) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `ins_comp_id` int(11) NOT NULL,
  `plan_type_id` int(11) NOT NULL,
  `paidup_date` date NOT NULL,
  `maturity_date` date NOT NULL,
  `amt_insured` decimal(18,2) NOT NULL,
  `commence_date` date NOT NULL,
  `mode` int(11) NOT NULL,
  `prem_amt` decimal(18,2) NOT NULL,
  `prem_type` int(11) NOT NULL,
  `prem_pay_mode_id` int(11) NOT NULL,
  `next_prem_due_date` date NOT NULL,
  `grace_due_date` date NOT NULL,
  `status` int(11) NOT NULL,
  `remarks` text,
  `fund_value` decimal(18,2) NOT NULL DEFAULT '0.00',
  `prem_paid_till_date` decimal(18,2) NOT NULL DEFAULT '0.00',
  `mat_type` varchar(50) NOT NULL,
  `adv_id` int(11) DEFAULT NULL,
  `nominee` varchar(200) DEFAULT NULL,
  `adjustment_flag` int(10) NOT NULL DEFAULT '0',
  `adjustment` varchar(300) DEFAULT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_life_covers`
--

CREATE TABLE `insurance_life_covers` (
  `policy_num` varchar(100) NOT NULL,
  `date_of_payment` date NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_policies`
--

CREATE TABLE `insurance_policies` (
  `ins_policy_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `ins_comp_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `policy_num` varchar(100) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_traditional_plans`
--

CREATE TABLE `insurance_traditional_plans` (
  `transaction_id` int(11) NOT NULL,
  `cheque_date` date DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `policy_number` varchar(100) NOT NULL,
  `premium_amount` decimal(18,2) NOT NULL,
  `premium_date` date NOT NULL,
  `premium_id` int(11) NOT NULL,
  `sum_assured` decimal(18,2) NOT NULL,
  `bonus_calculation` decimal(18,2) NOT NULL,
  `cummulative` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_unit_linked_plans`
--

CREATE TABLE `insurance_unit_linked_plans` (
  `transaction_id` int(11) NOT NULL,
  `cheque_date` date DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `policy_number` varchar(100) NOT NULL,
  `premium_amount` decimal(18,2) NOT NULL,
  `premium_date` date NOT NULL,
  `premium_id` int(11) NOT NULL,
  `sum_assured` decimal(18,2) NOT NULL,
  `annual_returns_calculation` decimal(18,2) NOT NULL,
  `cummulative` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ins_companies`
--

CREATE TABLE `ins_companies` (
  `ins_comp_id` int(11) NOT NULL,
  `ins_comp_name` varchar(200) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ins_plans`
--

CREATE TABLE `ins_plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(200) NOT NULL,
  `grace_period` int(11) NOT NULL DEFAULT '15',
  `ins_comp_id` int(11) NOT NULL,
  `plan_type_id` int(11) NOT NULL,
  `annual_cumm_one` float NOT NULL,
  `annual_cumm` float NOT NULL,
  `return_cumm` float NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `policy_id` int(11) NOT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ins_plan_types`
--

CREATE TABLE `ins_plan_types` (
  `plan_type_id` int(11) NOT NULL,
  `plan_type_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `last_imports`
--

CREATE TABLE `last_imports` (
  `broker_id` varchar(10) DEFAULT NULL,
  `import_type` varchar(50) NOT NULL,
  `last_import_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file_name` varchar(200) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `liability_histories`
--

CREATE TABLE `liability_histories` (
  `liability_history_id` int(11) NOT NULL,
  `liability_id` int(11) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_date` date NOT NULL,
  `narration` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `liability_maturity`
--

CREATE TABLE `liability_maturity` (
  `liability_maturity_id` int(11) NOT NULL,
  `liability_id` int(11) NOT NULL,
  `maturity_date` date NOT NULL,
  `maturity_amount` decimal(18,2) NOT NULL,
  `interest_rate` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `liability_transactions`
--

CREATE TABLE `liability_transactions` (
  `liability_id` int(11) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `particular` varchar(200) DEFAULT NULL,
  `ref_number` varchar(150) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pre_payment` int(11) NOT NULL DEFAULT '0',
  `installment_amount` decimal(18,2) NOT NULL,
  `interest_rate` decimal(18,2) NOT NULL,
  `total_liability` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `narration` text,
  `added_on` date NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_caluclated_live_unit`
--

CREATE TABLE `mf_caluclated_live_unit` (
  `scheme_name` varchar(300) NOT NULL,
  `folio_no` varchar(200) NOT NULL,
  `brokerId` varchar(10) NOT NULL,
  `live_unit` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_schemes_current_nav`
--

CREATE TABLE `mf_schemes_current_nav` (
  `scheme_id` int(11) NOT NULL,
  `current_nav` decimal(12,4) NOT NULL,
  `scheme_date` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_schemes_histories`
--

CREATE TABLE `mf_schemes_histories` (
  `scheme_history_id` bigint(20) UNSIGNED NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `current_nav` decimal(12,4) NOT NULL,
  `scheme_type_id` int(11) DEFAULT NULL,
  `scheme_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_schemes_histories_audit`
--

CREATE TABLE `mf_schemes_histories_audit` (
  `scheme_history_id` bigint(20) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `current_nav` decimal(10,0) NOT NULL,
  `scheme_type_id` int(11) NOT NULL,
  `scheme_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_schemes_histories_bk_not_in_tran`
--

CREATE TABLE `mf_schemes_histories_bk_not_in_tran` (
  `scheme_history_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `scheme_id` int(11) NOT NULL,
  `current_nav` decimal(12,4) NOT NULL,
  `scheme_type_id` int(11) DEFAULT NULL,
  `scheme_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_schemes_histories_need_to_delete_scheme`
--

CREATE TABLE `mf_schemes_histories_need_to_delete_scheme` (
  `scheme_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_scheme_types`
--

CREATE TABLE `mf_scheme_types` (
  `scheme_type_id` int(11) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `scheme_target_value` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_transaction_id`
--

CREATE TABLE `mf_transaction_id` (
  `transaction_id` bigint(11) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0004`
--

CREATE TABLE `mf_trans_temp_0004` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0009`
--

CREATE TABLE `mf_trans_temp_0009` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0010`
--

CREATE TABLE `mf_trans_temp_0010` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0063`
--

CREATE TABLE `mf_trans_temp_0063` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0147`
--

CREATE TABLE `mf_trans_temp_0147` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_trans_temp_0180`
--

CREATE TABLE `mf_trans_temp_0180` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_Tr_0180_customorder`
--

CREATE TABLE `mf_Tr_0180_customorder` (
  `transaction_id` bigint(11) UNSIGNED DEFAULT NULL,
  `client_id` varchar(30) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `purchase_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_valuation_reports`
--

CREATE TABLE `mf_valuation_reports` (
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` varchar(100) DEFAULT NULL,
  `mf_scheme_name` varchar(300) DEFAULT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `folio_number` varchar(25) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `mf_scheme_type` varchar(20) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `p_nav` decimal(18,4) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `payout` decimal(30,2) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,10) DEFAULT NULL,
  `div_payout` decimal(30,10) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `cagr` decimal(18,2) DEFAULT NULL,
  `scheme_type` varchar(20) DEFAULT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `current_value` decimal(30,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_val_temp_0004_1`
--

CREATE TABLE `mf_val_temp_0004_1` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `live_unit` decimal(18,4) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_val_temp_01`
--

CREATE TABLE `mf_val_temp_01` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `live_unit` decimal(18,4) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `MonthlySIPBook`
--

CREATE TABLE `MonthlySIPBook` (
  `Id` int(11) NOT NULL,
  `sip_date` date NOT NULL,
  `amount` float NOT NULL,
  `broker_Id` varchar(50) DEFAULT NULL,
  `CreatedBy` varchar(50) DEFAULT NULL,
  `DTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_monthly_summary`
--

CREATE TABLE `mutual_fund_monthly_summary` (
  `Id` bigint(20) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `Purchase_Value` bigint(20) NOT NULL DEFAULT '0',
  `value` bigint(20) NOT NULL,
  `CreatedDTStamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes`
--

CREATE TABLE `mutual_fund_schemes` (
  `scheme_id` int(11) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `isin` varchar(100) DEFAULT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_07062022`
--

CREATE TABLE `mutual_fund_schemes_07062022` (
  `scheme_id` int(11) NOT NULL DEFAULT '0',
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `isin` varchar(30) NOT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_19022022`
--

CREATE TABLE `mutual_fund_schemes_19022022` (
  `scheme_id` int(11) NOT NULL DEFAULT '0',
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `isin` varchar(30) NOT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_19022022_1`
--

CREATE TABLE `mutual_fund_schemes_19022022_1` (
  `scheme_id` int(11) NOT NULL DEFAULT '0',
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text,
  `isin` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_24012023`
--

CREATE TABLE `mutual_fund_schemes_24012023` (
  `scheme_id` int(11) NOT NULL DEFAULT '0',
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `isin` varchar(100) DEFAULT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_duplicate`
--

CREATE TABLE `mutual_fund_schemes_duplicate` (
  `scheme_id` int(11) NOT NULL DEFAULT '0',
  `scheme_name` varchar(300) NOT NULL,
  `scheme_status` int(11) NOT NULL DEFAULT '1',
  `scheme_type_id` int(11) NOT NULL DEFAULT '12',
  `prod_code` varchar(100) NOT NULL,
  `isin` varchar(100) DEFAULT NULL,
  `other` varchar(100) DEFAULT NULL,
  `other2` varchar(100) DEFAULT NULL,
  `market_cap` text,
  `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_isin`
--

CREATE TABLE `mutual_fund_schemes_isin` (
  `id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `isin` varchar(30) NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_schemes_not_in_tran_bk`
--

CREATE TABLE `mutual_fund_schemes_not_in_tran_bk` (
  `scheme_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions`
--

CREATE TABLE `mutual_fund_transactions` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `mutual_fund_transactions`
--
DELIMITER $$
CREATE TRIGGER `delete_valuation` AFTER DELETE ON `mutual_fund_transactions` FOR EACH ROW DELETE FROM mutual_fund_valuation WHERE transaction_id = OLD.transaction_id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_0004_1`
--

CREATE TABLE `mutual_fund_transactions_0004_1` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_0004_C20164816`
--

CREATE TABLE `mutual_fund_transactions_0004_C20164816` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_001`
--

CREATE TABLE `mutual_fund_transactions_001` (
  `transaction_id` bigint(11) UNSIGNED DEFAULT NULL,
  `client_id` varchar(30) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_004_1032022`
--

CREATE TABLE `mutual_fund_transactions_004_1032022` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_0180_1032022`
--

CREATE TABLE `mutual_fund_transactions_0180_1032022` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_02032022`
--

CREATE TABLE `mutual_fund_transactions_02032022` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_05022022_0004`
--

CREATE TABLE `mutual_fund_transactions_05022022_0004` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_05022022_0180`
--

CREATE TABLE `mutual_fund_transactions_05022022_0180` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_101`
--

CREATE TABLE `mutual_fund_transactions_101` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_21716`
--

CREATE TABLE `mutual_fund_transactions_21716` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_aditry_0004`
--

CREATE TABLE `mutual_fund_transactions_aditry_0004` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `schemeId` int(11) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_aditry_0004_1`
--

CREATE TABLE `mutual_fund_transactions_aditry_0004_1` (
  `client_name` varchar(150) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `schemeId` int(11) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prod_code` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_C202165913`
--

CREATE TABLE `mutual_fund_transactions_C202165913` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_delete_Scheme_24012023`
--

CREATE TABLE `mutual_fund_transactions_delete_Scheme_24012023` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_duplicate_tran_07082023`
--

CREATE TABLE `mutual_fund_transactions_duplicate_tran_07082023` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_transactions_sip_tag_example`
--

CREATE TABLE `mutual_fund_transactions_sip_tag_example` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ignore_from_duplicate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_types`
--

CREATE TABLE `mutual_fund_types` (
  `mutual_fund_type_id` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `use_for` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation`
--

CREATE TABLE `mutual_fund_valuation` (
  `valuation_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `c_nav` decimal(18,4) DEFAULT '0.0000',
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT '0.0000',
  `unit_per_count` decimal(30,4) DEFAULT '0.0000',
  `div_r2` decimal(30,10) DEFAULT '0.0000000000',
  `div_payout` decimal(30,10) DEFAULT '0.0000000000',
  `div_amount` decimal(30,2) DEFAULT '0.00',
  `p_amount` decimal(30,2) DEFAULT '0.00',
  `transaction_day` int(11) DEFAULT '0',
  `mf_abs` decimal(18,2) DEFAULT '0.00',
  `mf_cagr` decimal(18,2) DEFAULT '0.00',
  `broker_id` varchar(10) DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_25`
--

CREATE TABLE `mutual_fund_valuation_25` (
  `valuation_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `c_nav` decimal(18,4) DEFAULT '0.0000',
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT '0.0000',
  `unit_per_count` decimal(30,4) DEFAULT '0.0000',
  `div_r2` decimal(30,10) DEFAULT '0.0000000000',
  `div_payout` decimal(30,10) DEFAULT '0.0000000000',
  `div_amount` decimal(30,2) DEFAULT '0.00',
  `p_amount` decimal(30,2) DEFAULT '0.00',
  `transaction_day` int(11) DEFAULT '0',
  `mf_abs` decimal(18,2) DEFAULT '0.00',
  `mf_cagr` decimal(18,2) DEFAULT '0.00',
  `broker_id` varchar(10) DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_cagr`
--

CREATE TABLE `mutual_fund_valuation_cagr` (
  `id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `cagr_date` date NOT NULL,
  `mf_cagr` decimal(18,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0004`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0004` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0009`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0009` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0063`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0063` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0154`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0154` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0180`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0180` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_delete_op_0204`
--

CREATE TABLE `mutual_fund_valuation_delete_op_0204` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0004`
--

CREATE TABLE `mutual_fund_valuation_h_0004` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0009`
--

CREATE TABLE `mutual_fund_valuation_h_0009` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0010`
--

CREATE TABLE `mutual_fund_valuation_h_0010` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0063`
--

CREATE TABLE `mutual_fund_valuation_h_0063` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0147`
--

CREATE TABLE `mutual_fund_valuation_h_0147` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0180`
--

CREATE TABLE `mutual_fund_valuation_h_0180` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mutual_fund_valuation_h_0204`
--

CREATE TABLE `mutual_fund_valuation_h_0204` (
  `valuation_id` bigint(20) NOT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `c_nav` decimal(18,4) DEFAULT NULL,
  `c_nav_date` date DEFAULT NULL,
  `live_unit` decimal(30,4) DEFAULT NULL,
  `unit_per_count` decimal(30,4) DEFAULT NULL,
  `div_r2` decimal(30,4) DEFAULT NULL,
  `div_payout` decimal(30,4) DEFAULT NULL,
  `div_amount` decimal(30,2) DEFAULT NULL,
  `p_amount` decimal(30,2) DEFAULT NULL,
  `transaction_day` int(11) DEFAULT NULL,
  `mf_abs` decimal(18,2) DEFAULT NULL,
  `mf_cagr` decimal(18,2) DEFAULT NULL,
  `prod_code` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(300) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `broker_id` varchar(10) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `nfo_detail`
--

CREATE TABLE `nfo_detail` (
  `Id` int(11) NOT NULL,
  `nfo_description` text NOT NULL,
  `desc_color` varchar(50) NOT NULL,
  `nfo_image_path` text NOT NULL,
  `broker_Id` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `occupations`
--

CREATE TABLE `occupations` (
  `occupation_id` int(11) NOT NULL,
  `occupation_name` varchar(50) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `policies`
--

CREATE TABLE `policies` (
  `policy_id` int(11) NOT NULL,
  `policy_name` varchar(200) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premium_maturities`
--

CREATE TABLE `premium_maturities` (
  `maturity_id` bigint(11) UNSIGNED NOT NULL,
  `policy_num` varchar(100) NOT NULL,
  `maturity_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `client_id` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `premium_maturities`
--
DELIMITER $$
CREATE TRIGGER `updateMaturityDate` AFTER INSERT ON `premium_maturities` FOR EACH ROW BEGIN
	/*Declare toUpdate Boolean;
	Declare policyNumber varchar(100);
	Declare clientID varchar(10);
    Declare matDate date;
	set policyNumber = NEW.policy_num;
	set clientID = NEW.client_id;
    set matDate = NEW.maturity_date;
    SELECT 1 INTO @toUpdate FROM `insurances` 
    WHERE `maturity_date` < matDate 
    AND `policy_num` = policyNumber 
    AND `client_id` = clientID;
    if @toUpdate = 1 then		
    	UPDATE `insurances` SET `maturity_date` = matDate 
    	WHERE `policy_num` = policyNumber AND `client_id` = clientID;
    end if;*/
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `updateMaturityDelete` AFTER DELETE ON `premium_maturities` FOR EACH ROW BEGIN
	/*Declare toUpdate Boolean;
	Declare policyNumber varchar(100);
	Declare clientID varchar(10);
    Declare matDate date;
	set policyNumber = OLD.policy_num;
	set clientID = OLD.client_id;
    /*set matDate = NEW.maturity_date;*/
    /*SELECT `maturity_date` INTO matDate FROM `premium_maturities` 
    WHERE policy_num = policyNumber AND client_id = clientID 
    ORDER BY `maturity_date` DESC LIMIT 1;
    SELECT 1 INTO @toUpdate FROM `insurances` 
    WHERE `maturity_date` > matDate 
    AND `policy_num` = policyNumber 
    AND `client_id` = clientID;
    if @toUpdate = 1 then		
    	UPDATE `insurances` SET `maturity_date` = matDate 
    	WHERE `policy_num` = policyNumber AND `client_id` = clientID;
    end if;*/
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `premium_modes`
--

CREATE TABLE `premium_modes` (
  `mode_id` int(11) NOT NULL,
  `mode_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premium_paying_details`
--

CREATE TABLE `premium_paying_details` (
  `premium_pay_id` bigint(11) UNSIGNED NOT NULL,
  `policy_num` varchar(100) NOT NULL,
  `date_of_payment` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premium_pay_modes`
--

CREATE TABLE `premium_pay_modes` (
  `prem_pay_mode_id` int(11) NOT NULL,
  `prem_pay_mode` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premium_status`
--

CREATE TABLE `premium_status` (
  `status_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `premium_transactions`
--

CREATE TABLE `premium_transactions` (
  `premium_id` int(11) NOT NULL,
  `policy_number` varchar(100) NOT NULL,
  `cheque_number` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `premium_amount` decimal(10,2) NOT NULL,
  `advisers` varchar(150) DEFAULT NULL,
  `adjustment` text,
  `adjustment_ref_number` varchar(150) DEFAULT NULL,
  `narration` text,
  `next_premium_due_date` date DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL COMMENT 'id of user or broker who has made the changes',
  `account_number` varchar(30) DEFAULT NULL,
  `client_id` varchar(30) DEFAULT NULL,
  `premium_mode` varchar(100) DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `premium_transactions`
--
DELIMITER $$
CREATE TRIGGER `DeletePreTransaction` AFTER DELETE ON `premium_transactions` FOR EACH ROW BEGIN
	declare chequeDate date;
	declare chequeNumber varchar(100);
	declare policyNumber varchar(150);
	declare premiumAmount float;
	declare planTypeName varchar(100);
	declare preId int;
	declare oldFundValue float;
    #FOR SETING NEXT PREMIUM DUE DATE
	declare nextPremiumDueDate date;
    declare oldNextDue date;
    declare pMode varchar(20);
	declare gracePeriod int;
    declare brokerID varchar(10);
	set preId = OLD.premium_id;
	set chequeDate = OLD.cheque_date;
	set chequeNumber = OLD.cheque_number;
	set policyNumber = OLD.policy_number;
	set premiumAmount = OLD.premium_amount;
    set oldNextDue = OLD.next_premium_due_date;
    set pMode = OLD.premium_mode;
    set brokerID = OLD.broker_id;
    /*select next_premium_due_date into nextPremiumDueDate from premium_transactions where policy_number = policyNumber and broker_id = brokerID order by next_premium_due_date desc limit 1;*/
	select plan_type_name into planTypeName from ins_plan_types iptn inner join ins_plans ipm
	on iptn.plan_type_id=ipm.plan_type_id inner join insurances im on im.plan_id=ipm.plan_id where policy_num=policyNumber and im.broker_id = brokerID;
	if(planTypeName='Traditional')
	then
		select sum(premium_amount)+sum(bonus_calculation) into oldFundValue from insurance_traditional_plans 
		where policy_number=policyNumber and broker_id = brokerID;
		UPDATE insurances set fund_value=oldFundValue, prem_paid_till_date=prem_paid_till_date-premiumAmount 
		where policy_num=policyNumber and broker_id = brokerID;
	elseif(planTypeName='Unit Linked')
	then
		select cummulative from insurance_unit_linked_plans where policy_number=policyNumber  and broker_id = brokerID  order by premium_id desc limit 1 into oldFundValue;
		UPDATE insurances set fund_value=oldFundValue, prem_paid_till_date=prem_paid_till_date-premiumAmount 
		where policy_num=policyNumber and broker_id = brokerID;
	else
		update insurances set prem_paid_till_date=prem_paid_till_date-premiumAmount where policy_num=policyNumber and broker_id = brokerID;	
	end if;
    #get the gracePeriod
    select ins_grace_reminder into gracePeriod from reminder_days where broker_id = brokerID;
    #check the premium payment mode and update next_premium_due_date in ins
    if pMode="Annually" or pMode = "Single"
	then
		set nextPremiumDueDate = DATE_ADD(oldNextDue, INTERVAL -1 YEAR);
	elseif	pMode = "Half-yearly"
	then
		set nextPremiumDueDate = DATE_ADD(oldNextDue, INTERVAL -6 MONTH);
	elseif pMode = "Quarterly"
	then
		set nextPremiumDueDate = DATE_ADD(oldNextDue, INTERVAL -1 QUARTER);
	else
		set nextPremiumDueDate = DATE_ADD(oldNextDue, INTERVAL -1 MONTH);
	end if;
    UPDATE insurances set next_prem_due_date = nextPremiumDueDate, grace_due_date = DATE_ADD(nextPremiumDueDate, INTERVAL gracePeriod DAY) where policy_num = policyNumber and broker_id = brokerID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `updateTraditinal_unitLink` AFTER INSERT ON `premium_transactions` FOR EACH ROW begin
	Declare chequeDate date;
	Declare chequeNumber varchar(50);
	Declare policyNum varchar(200);
	Declare premiumTransId int;
	Declare premiumPayingDate date;
	Declare premiumAmount float;
	Declare preMode varchar(100);
	Declare sumAssured float;
	Declare bonus float;
	Declare FinalBonus float;
	Declare returnCum float;
	Declare BonusCal float;
	Declare Cummulative float;
	Declare planTypeName varchar(50);
	Declare returnsCalculate float;
	Declare totalBonus float;
	Declare finalFundValue float;
    Declare brokerID varchar(10);
	#FOR SETING NEXT PREMIUM DUE DATE
	Declare nextPremiumDueDate date;
	Declare gracePeriod int;
    Declare modeName varchar(50);
	Declare totPremiumTrans int;
	#Declare mod int;
    Declare modeNameUL varchar(50);
	Declare totPremiumTransUL int;
	Declare modUL int;
	set premiumTransId = New.premium_id; 
    set chequeDate = NEW.cheque_date; 
    set chequeNumber = NEW.cheque_number;
	set policyNum = NEW.policy_number; 
	set premiumPayingDate = current_date(); 
	set premiumAmount = NEW.premium_amount;
	set nextPremiumDueDate = NEW.next_premium_due_date; 
	set preMode = NEW.premium_mode;
    set brokerID = new.broker_id;
	select amt_insured into sumAssured from insurances where policy_num=policyNum and broker_id = brokerID;
	select distinct annual_cumm_one, annual_cumm, return_cumm, 
	iptn.plan_type_name, grace_period from insurances im 
	inner join ins_plans ipm on im.plan_id=ipm.plan_id 
	inner join ins_plan_types iptn on iptn.plan_type_id=ipm.plan_type_id 
	inner join premium_transactions pt on pt.policy_number=im.policy_num 
	where pt.policy_number=policyNum and pt.broker_id = brokerID into bonus, FinalBonus, returnCum, planTypeName, gracePeriod;
	if (planTypeName='Traditional')
	then		
		set BonusCal=0;
		#Update On the basis of Modes i.e Annual, Monthly ,half-yearly, quaterly
		select mode_name into modeName from insurances im inner join  premium_modes pmm on im.mode=pmm.mode_id 
		where policy_num=policyNum and im.broker_id = brokerID;
		select count(policy_number) into totPremiumTrans from premium_transactions 
		where policy_number=policyNum and premium_mode=preMode and broker_id = brokerID;
		if modeName='Annually' OR modeName='Single' then
			set BonusCal=(sumAssured*FinalBonus)/100;
		elseif modeName='Half-Yearly' then
			if totPremiumTrans%2=0 then
				set BonusCal=(sumAssured*FinalBonus)/100;
			end if;
		elseif modeName='Quarterly'	then
			if totPremiumTrans%4=0 then
				set BonusCal=(sumAssured*FinalBonus)/100;
			end if;
		elseif modeName='Monthly' then
			if totPremiumTrans%12=0 then
				set BonusCal=(sumAssured*FinalBonus)/100;
			end if;
		end if;
		set Cummulative=sumAssured+BonusCal;
		insert into insurance_traditional_plans(`cheque_date`, cheque_number, policy_number, premium_amount, 
		premium_date, premium_id, sum_assured, bonus_calculation, cummulative, broker_id)
		values(chequeDate, chequeNumber, policyNum, premiumAmount, premiumPayingDate, premiumTransId, sumAssured,
		BonusCal, Cummulative, brokerID);
		select sum(bonus_calculation) into totalBonus from insurance_traditional_plans where policy_number=policyNum and broker_id = brokerID;
		set finalFundValue=totalBonus;
		update insurances set prem_paid_till_date=prem_paid_till_date+premiumAmount, 
		fund_value=fund_value+premiumAmount where policy_num=policyNum and broker_id = brokerID;
		#Update On the basis of Modes i.e Annual, Monthly ,half-yearly, quaterly
		if modeName='Annually' OR modeName='Single' then
			update insurances set fund_value=prem_paid_till_date+finalFundValue where policy_num=policyNum and broker_id = brokerID;
		elseif modeName='Half-Yearly' then
			if totPremiumTrans%2=0 then
				update insurances set fund_value=prem_paid_till_date+finalFundValue 
				where policy_num=policyNum and broker_id = brokerID;
			end if;
		elseif modeName='Quarterly' then
			if totPremiumTrans%4=0 then
				update insurances set fund_value=prem_paid_till_date+finalFundValue 
				where policy_num=policyNum and broker_id = brokerID;
			end if;
		elseif modeName='Monthly' then
			if totPremiumTrans%12=0 then
				update insurances set fund_value=prem_paid_till_date+finalFundValue where policy_num=policyNum and broker_id = brokerID;
			end if;
   		end if;
	elseif(planTypeName='Unit Linked') then
		select fund_value into sumAssured from insurances where policy_num=policyNum and broker_id = brokerID;
		set returnsCalculate=0;
		if (sumAssured=0.0 or sumAssured is NULL) then
			set sumAssured=premiumAmount;
		else 
			set sumAssured=sumAssured+premiumAmount;
		end if;
		#Update On the basis of Modes i.e Annual, Monthly ,half-yearly, quaterly
		select mode_name into modeNameUL from insurances im inner join premium_modes pmm on im.mode=pmm.mode_id 
		where policy_num=policyNum and im.broker_id = brokerID;
		select count(policy_number) into totPremiumTransUL from premium_transactions 
		where policy_number=policyNum and broker_id = brokerID;
		if modeNameUL='Annually' OR modeName='Single' then
			set returnsCalculate=(sumAssured*returnCum)/100;
		elseif modeNameUL='Half-Yearly' then
			if totPremiumTransUL%2=0 then
				set returnsCalculate=(sumAssured*returnCum)/100;
			end if;
		elseif modeNameUL='Quarterly' then
			if totPremiumTransUL%4=0 then
				set returnsCalculate=(sumAssured*returnCum)/100;
			end if;
		elseif modeNameUL='Monthly' then
			if totPremiumTransUL%12=0 then
				set returnsCalculate=(sumAssured*returnCum)/100;
			end if;
		end if;
		set cummulative=sumAssured+returnsCalculate;
		insert into insurance_unit_linked_plans(`cheque_date`, cheque_number, policy_number, premium_amount,
		premium_date, premium_id, sum_assured, annual_returns_calculation, cummulative, broker_id)
		values(chequeDate, chequeNumber, policyNum, premiumAmount, premiumPayingDate, premiumTransId, sumAssured,
		returnsCalculate, Cummulative, brokerID);
		update insurances set fund_value=cummulative, prem_paid_till_date=prem_paid_till_date+premiumAmount
		where policy_num=policyNum and broker_id = brokerID;
	else
		update insurances set prem_paid_till_date=prem_paid_till_date+premiumAmount
		where policy_num=policyNum and broker_id = brokerID;
	end if;
	#update NextPremiumDuedate and NextPremiumGarceDate
		update insurances
		set next_prem_due_date=nextPremiumDueDate,
		grace_due_date=DATE_ADD(nextPremiumDueDate, interval gracePeriod day),
		status =(select status_id from premium_status where status='In Force')
		where policy_num=policyNum and broker_id = brokerID;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `premium_types`
--

CREATE TABLE `premium_types` (
  `prem_type_id` int(11) NOT NULL,
  `prem_type_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_rents`
--

CREATE TABLE `property_rents` (
  `pro_rent_id` int(11) NOT NULL,
  `pro_transaction_id` varchar(30) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `property_rents`
--
DELIMITER $$
CREATE TRIGGER `addPropertyDetails` AFTER INSERT ON `property_rents` FOR EACH ROW BEGIN
	declare propRentId int;
	declare propTransId varchar(20);
	declare frmDate datetime;
	declare toDate datetime;
	declare amount float;
	declare j int;
	declare i int;
	set j=0;
    set propRentId = NEW.pro_rent_id;
	set propTransId = NEW.pro_transaction_id;
	set frmDate = NEW.from_date;
	set toDate = NEW.to_date;
	set amount = NEW.amount;
    set i = period_diff(DATE_FORMAT(toDate, '%Y%m'), DATE_FORMAT(frmDate, '%Y%m'));
	while(j <= i)
	do
		insert into property_rent_details (rent_id, pro_transaction_id, rent_date, amount) values(propRentId, propTransId, frmDate, amount);
		set j = j + 1;
		set frmDate = DATE_ADD(frmDate, INTERVAL 1 MONTH);
	end while;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `deleteRenrDetails` BEFORE DELETE ON `property_rents` FOR EACH ROW begin
	declare id int;
    set id = old.pro_rent_id;
    delete from property_rent_details where rent_id = id;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `updateRentDetails` AFTER UPDATE ON `property_rents` FOR EACH ROW BEGIN
	declare propRentId int;
	declare propTransId varchar(100);
    declare frmdate datetime;
	declare toDate datetime;
	declare amount float;
	declare j int;
	declare i int;
    set propRentId = OLD.pro_rent_id;
    set propTransId = OLD.pro_transaction_id;
    delete from property_rent_details where rent_id = propRentId and pro_transaction_id = propTransId;
	set j=0;
    set frmDate = NEW.from_date;
	set toDate = NEW.to_date;
	set amount = NEW.amount;
    set i = period_diff(DATE_FORMAT(toDate, '%Y%m'), DATE_FORMAT(frmDate, '%Y%m'));
	while(j <= i)
	do
		insert into property_rent_details (rent_id, pro_transaction_id, rent_date, amount) values(propRentId, propTransId, frmDate, amount);
		set j = j + 1;
		set frmDate = DATE_ADD(frmDate, INTERVAL 1 MONTH);
	end while;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `property_rent_details`
--

CREATE TABLE `property_rent_details` (
  `rent_detail_id` int(11) NOT NULL,
  `rent_id` int(11) NOT NULL,
  `pro_transaction_id` varchar(30) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `rent_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_transactions`
--

CREATE TABLE `property_transactions` (
  `pro_transaction_id` varchar(30) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` varchar(12) NOT NULL,
  `property_name` varchar(150) NOT NULL,
  `property_type_id` int(11) NOT NULL,
  `property_location` varchar(150) NOT NULL,
  `property_area` float(15,2) NOT NULL,
  `property_unit_id` int(11) NOT NULL,
  `transaction_rate` decimal(18,2) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `current_rate` decimal(18,2) NOT NULL,
  `property_updated_on` date DEFAULT NULL,
  `deposit_amount` decimal(18,2) DEFAULT '0.00',
  `remarks` text,
  `adviser_id` int(11) DEFAULT NULL,
  `rent_applicable` int(11) NOT NULL DEFAULT '0',
  `gain` decimal(18,2) DEFAULT '0.00',
  `total_gain` int(11) DEFAULT '0',
  `abs` decimal(18,2) DEFAULT '0.00',
  `cagr` decimal(18,2) DEFAULT '0.00',
  `user_id` varchar(10) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL,
  `added_on` date DEFAULT NULL,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_types`
--

CREATE TABLE `property_types` (
  `property_type_id` int(11) NOT NULL,
  `property_type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_units`
--

CREATE TABLE `property_units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `push_notification_logs`
--

CREATE TABLE `push_notification_logs` (
  `id` int(10) NOT NULL,
  `type` varchar(100) NOT NULL,
  `request_payload` longtext NOT NULL,
  `response_payload` longtext NOT NULL,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `real_stakes`
--

CREATE TABLE `real_stakes` (
  `policy_number` varchar(100) NOT NULL,
  `stake_year` varchar(10) NOT NULL,
  `bonus` decimal(18,2) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reminder`
--

CREATE TABLE `reminder` (
  `reminder_id` int(11) NOT NULL,
  `is_personal` int(11) NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text,
  `status` int(11) NOT NULL DEFAULT '1',
  `reminder_date` date NOT NULL,
  `next_date` date DEFAULT NULL,
  `remark` text,
  `concern_user` varchar(10) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_days`
--

CREATE TABLE `reminder_days` (
  `reminder_days_id` int(11) NOT NULL,
  `ins_premium_reminder` int(11) NOT NULL,
  `ins_maturity_reminder` int(11) NOT NULL,
  `ins_grace_reminder` int(11) DEFAULT NULL,
  `mf_redemption` int(11) DEFAULT NULL,
  `mf_dpo` int(11) NOT NULL,
  `fd_maturity_reminder` int(11) DEFAULT NULL,
  `fd_interest` int(11) DEFAULT NULL,
  `share_negative` int(11) DEFAULT NULL,
  `personal_reminder` int(11) NOT NULL,
  `assets_reminder` int(11) DEFAULT NULL,
  `ins_premium_amount` int(11) DEFAULT NULL,
  `ins_maturity_amount` int(11) DEFAULT NULL,
  `ins_grace_amount` int(11) DEFAULT NULL,
  `fd_maturity_amount` int(11) DEFAULT NULL,
  `fd_interest_amount` int(11) DEFAULT NULL,
  `personal_amount` int(11) DEFAULT NULL,
  `assets_amount` int(11) DEFAULT NULL,
  `rent_amount` int(11) DEFAULT NULL,
  `mf_redemption_amount` int(11) DEFAULT NULL,
  `mf_dpo_amount` int(11) DEFAULT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_email_config`
--

CREATE TABLE `reminder_email_config` (
  `email_id` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reminder_script_counter`
--

CREATE TABLE `reminder_script_counter` (
  `script_date` date NOT NULL,
  `script_status` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scrip_rates`
--

CREATE TABLE `scrip_rates` (
  `scrip_code` varchar(50) NOT NULL,
  `scrip_name` varchar(300) DEFAULT NULL,
  `close_rate` decimal(10,2) DEFAULT NULL,
  `industry` text,
  `cap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scrip_rates_03112020`
--

CREATE TABLE `scrip_rates_03112020` (
  `scrip_code` varchar(50) NOT NULL,
  `scrip_name` varchar(300) DEFAULT NULL,
  `close_rate` decimal(10,2) DEFAULT NULL,
  `industry` text,
  `cap` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sip_auto_import_error_backup`
--

CREATE TABLE `sip_auto_import_error_backup` (
  `a_id` int(10) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `product_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `folio_no` varchar(200) NOT NULL,
  `company_id` int(11) NOT NULL,
  `scheme_id` int(11) NOT NULL,
  `goal` varchar(200) DEFAULT NULL,
  `ref_number` varchar(150) DEFAULT NULL,
  `Bank_AccountNo` varchar(50) DEFAULT NULL,
  `Bank` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reg_date` date DEFAULT NULL,
  `cease_date` date DEFAULT NULL,
  `frequency` varchar(20) DEFAULT NULL,
  `installment_amount` decimal(18,2) NOT NULL,
  `rate_of_return` decimal(18,2) DEFAULT NULL,
  `expected_mat_value` decimal(18,2) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `narration` text,
  `added_on` date NOT NULL,
  `updated_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `error_file_name` varchar(80) DEFAULT NULL,
  `error_col` varchar(50) DEFAULT NULL,
  `error_msg` varchar(70) DEFAULT NULL,
  `email_status` enum('1','0') NOT NULL DEFAULT '0' COMMENT '1-Email sent,0-Email not sent'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sip_rate`
--

CREATE TABLE `sip_rate` (
  `id` int(20) NOT NULL,
  `scheme_type` varchar(50) NOT NULL,
  `rate` float NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `summary_report_data`
--

CREATE TABLE `summary_report_data` (
  `id` bigint(11) NOT NULL,
  `client_id` varchar(30) DEFAULT NULL,
  `insurance` decimal(12,2) DEFAULT NULL,
  `fixed_income` decimal(12,2) DEFAULT NULL,
  `mutual_funds` decimal(12,2) DEFAULT NULL,
  `equity` decimal(12,2) DEFAULT NULL,
  `real_estate` decimal(12,2) DEFAULT NULL,
  `commodity` decimal(12,2) DEFAULT NULL,
  `life_cover` decimal(12,2) DEFAULT NULL,
  `insurance_fund` decimal(12,2) DEFAULT NULL,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `super_admins`
--

CREATE TABLE `super_admins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `add_info` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TABLE 136`
--

CREATE TABLE `TABLE 136` (
  `Id` int(11) NOT NULL,
  `UniqueNo` int(5) DEFAULT NULL,
  `SchemeCode` varchar(20) DEFAULT NULL,
  `RTA Scheme Code` varchar(5) DEFAULT NULL,
  `AMC Scheme Code` varchar(10) DEFAULT NULL,
  `ISIN` varchar(12) DEFAULT NULL,
  `AMC Code` varchar(32) DEFAULT NULL,
  `Scheme Type` varchar(11) DEFAULT NULL,
  `Scheme Plan` varchar(6) DEFAULT NULL,
  `Scheme Name` varchar(129) DEFAULT NULL,
  `Purchase Allowed` varchar(1) DEFAULT NULL,
  `Purchase Transaction mode` varchar(2) DEFAULT NULL,
  `Minimum Purchase Amount` decimal(10,2) DEFAULT NULL,
  `Additional Purchase Amount` decimal(9,2) DEFAULT NULL,
  `Maximum Purchase Amount` bigint(10) DEFAULT NULL,
  `Purchase Amount Multiplier` decimal(10,3) DEFAULT NULL,
  `Purchase Cutoff Time` varchar(8) DEFAULT NULL,
  `Redemption Allowed` varchar(1) DEFAULT NULL,
  `Redemption Transaction Mode` varchar(2) DEFAULT NULL,
  `Minimum Redemption Qty` decimal(12,3) DEFAULT NULL,
  `Redemption Qty Multiplier` decimal(12,3) DEFAULT NULL,
  `Maximum Redemption Qty` decimal(4,3) DEFAULT NULL,
  `Redemption Amount - Minimum` decimal(12,3) DEFAULT NULL,
  `Redemption Amount ? Maximum` decimal(4,3) DEFAULT NULL,
  `Redemption Amount Multiple` decimal(11,3) DEFAULT NULL,
  `Redemption Cut off Time` varchar(8) DEFAULT NULL,
  `RTA Agent Code` varchar(8) DEFAULT NULL,
  `AMC Active Flag` int(1) DEFAULT NULL,
  `Dividend Reinvestment Flag` varchar(1) DEFAULT NULL,
  `SIP FLAG` varchar(1) DEFAULT NULL,
  `STP FLAG` varchar(1) DEFAULT NULL,
  `SWP Flag` varchar(1) DEFAULT NULL,
  `Switch FLAG` varchar(1) DEFAULT NULL,
  `SETTLEMENT TYPE` varchar(3) DEFAULT NULL,
  `AMC_IND` varchar(10) DEFAULT NULL,
  `Face Value` int(4) DEFAULT NULL,
  `Start Date` varchar(11) DEFAULT NULL,
  `End Date` varchar(11) DEFAULT NULL,
  `Exit Load Flag` varchar(1) DEFAULT NULL,
  `Exit Load` int(1) DEFAULT NULL,
  `Lock-in Period Flag` varchar(1) DEFAULT NULL,
  `Lock-in Period` varchar(4) DEFAULT NULL,
  `Channel Partner Code` varchar(10) DEFAULT NULL,
  `3tenseSchemeId` varchar(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `TABLE 137`
--

CREATE TABLE `TABLE 137` (
  `UniqueNo` int(5) DEFAULT NULL,
  `SchemeCode` varchar(20) DEFAULT NULL,
  `RTASchemeCode` varchar(5) DEFAULT NULL,
  `AMCSchemeCode` varchar(10) DEFAULT NULL,
  `ISIN` varchar(12) DEFAULT NULL,
  `AMCCode` varchar(32) DEFAULT NULL,
  `SchemeType` varchar(11) DEFAULT NULL,
  `SchemePlan` varchar(6) DEFAULT NULL,
  `SchemeName` varchar(129) DEFAULT NULL,
  `PurchaseAllowed` varchar(1) DEFAULT NULL,
  `PurchaseTransactionmode` varchar(2) DEFAULT NULL,
  `MinimumPurchaseAmount` decimal(10,2) DEFAULT NULL,
  `AdditionalPurchaseAmount` decimal(9,2) DEFAULT NULL,
  `MaximumPurchaseAmount` bigint(10) DEFAULT NULL,
  `PurchaseAmountMultiplier` decimal(10,3) DEFAULT NULL,
  `PurchaseCutoffTime` varchar(8) DEFAULT NULL,
  `RedemptionAllowed` varchar(1) DEFAULT NULL,
  `RedemptionTransactionMode` varchar(2) DEFAULT NULL,
  `MinimumRedemptionQty` decimal(12,3) DEFAULT NULL,
  `RedemptionQtyMultiplier` decimal(12,3) DEFAULT NULL,
  `MaximumRedemptionQty` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMinimum` decimal(12,3) DEFAULT NULL,
  `RedemptionAmountOtherMaximum` decimal(4,3) DEFAULT NULL,
  `RedemptionAmountMultiple` decimal(11,3) DEFAULT NULL,
  `RedemptionCutoffTime` varchar(8) DEFAULT NULL,
  `RTAAgentCode` varchar(8) DEFAULT NULL,
  `AMCActiveFlag` int(1) DEFAULT NULL,
  `DividendReinvestmentFlag` varchar(1) DEFAULT NULL,
  `SIPFLAG` varchar(1) DEFAULT NULL,
  `STPFLAG` varchar(1) DEFAULT NULL,
  `SWPFlag` varchar(1) DEFAULT NULL,
  `SwitchFLAG` varchar(1) DEFAULT NULL,
  `SETTLEMENTTYPE` varchar(3) DEFAULT NULL,
  `AMC_IND` varchar(10) DEFAULT NULL,
  `FaceValue` int(4) DEFAULT NULL,
  `StartDate` varchar(11) DEFAULT NULL,
  `EndDate` varchar(11) DEFAULT NULL,
  `ExitLoadFlag` varchar(1) DEFAULT NULL,
  `ExitLoad` int(1) DEFAULT NULL,
  `LockInPeriodFlag` varchar(1) DEFAULT NULL,
  `LockInPeriod` varchar(4) DEFAULT NULL,
  `ChannelPartnerCode` varchar(10) DEFAULT NULL,
  `tenseSchemeId` varchar(5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `temp_101`
--

CREATE TABLE `temp_101` (
  `max(transaction_id)` bigint(11) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp_funds_info`
--

CREATE TABLE `temp_funds_info` (
  `client_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `add` decimal(18,2) NOT NULL,
  `withdraw` decimal(18,2) NOT NULL,
  `user_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temp_mf_transaction`
--

CREATE TABLE `temp_mf_transaction` (
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `client_id` varchar(30) NOT NULL,
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `folio_number` varchar(200) NOT NULL,
  `transaction_date` date NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `broker_id` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `testing_table_mf`
--

CREATE TABLE `testing_table_mf` (
  `scheme_name` varchar(300) NOT NULL,
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `testing_table_mf_1`
--

CREATE TABLE `testing_table_mf_1` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `today_reminders`
--

CREATE TABLE `today_reminders` (
  `reminder_id` bigint(20) UNSIGNED NOT NULL,
  `reminder_type` varchar(200) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `client_id` varchar(20) DEFAULT NULL,
  `client_name` varchar(200) DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `mail_sent_status` enum('0','1') NOT NULL,
  `reminder_message` text,
  `reminder_status` varchar(50) DEFAULT NULL,
  `next_date` date DEFAULT NULL,
  `remark` text,
  `concern_user` varchar(200) DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `broker_id` varchar(10) DEFAULT NULL,
  `completed_on` date DEFAULT NULL,
  `client_view` int(11) NOT NULL DEFAULT '0',
  `IsSendNotification` int(11) NOT NULL DEFAULT '0',
  `attachment_url` text,
  `created_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `today_reminders`
--
DELIMITER $$
CREATE TRIGGER `updateCompletelReminder` AFTER DELETE ON `today_reminders` FOR EACH ROW begin
	insert into complete_reminders (reminder_type, client_id, client_name, reminder_date, reminder_message, reminder_status, next_date, remark, concern_user, user_id, broker_id, completed_on) 
    values (OLD.reminder_type, OLD.client_id, OLD.client_name, OLD.reminder_date, OLD.reminder_message, OLD.reminder_status, OLD.next_date, OLD.remark, OLD.concern_user, OLD.user_id, OLD.broker_id, CURRENT_DATE());
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `trading_brokers`
--

CREATE TABLE `trading_brokers` (
  `trading_broker_id` int(11) NOT NULL,
  `trading_broker_name` varchar(100) NOT NULL,
  `broker_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TRANSACTION_0004`
--

CREATE TABLE `TRANSACTION_0004` (
  `client_name` varchar(150) NOT NULL,
  `family_name` varchar(150) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `prod_code` varchar(100) NOT NULL,
  `scheme_name` varchar(300) NOT NULL,
  `scheme_type` varchar(100) NOT NULL,
  `mutual_fund_type` varchar(200) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment` text,
  `balance_unit` varchar(30) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `from_file` text,
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tran_102`
--

CREATE TABLE `tran_102` (
  `transaction_id` bigint(11) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tra_0004`
--

CREATE TABLE `tra_0004` (
  `transaction_id` bigint(11) UNSIGNED DEFAULT NULL,
  `client_id` varchar(30) NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `customOrder` int(11) NOT NULL DEFAULT '0',
  `quantity` decimal(12,4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(10000) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `user_type` varchar(10) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `add_info` text,
  `broker_id` varchar(10) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '3' COMMENT '1- Read, 2-Read and write, 3. All',
  `pwd_reset` int(11) NOT NULL DEFAULT '0',
  `client_limit` int(11) NOT NULL DEFAULT '50000',
  `client_access` int(11) NOT NULL DEFAULT '5000',
  `user_limit` int(11) NOT NULL DEFAULT '50',
  `arn` varchar(20) NOT NULL,
  `cams_rta_password` varchar(50) NOT NULL,
  `karvy_rta_password` varchar(50) NOT NULL,
  `mailback_mail` varchar(100) NOT NULL,
  `EUIN` varchar(100) DEFAULT NULL,
  `BSCUserId` varchar(100) DEFAULT NULL,
  `BSCMemberId` varchar(100) DEFAULT NULL,
  `BSCPassword` varchar(100) DEFAULT NULL,
  `BSCTransUniqueRefNo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `add_other_details` AFTER INSERT ON `users` FOR EACH ROW BEGIN
DECLARE brokerID varchar(10);
DECLARE mainBrokerID varchar(10);
DECLARE userType varchar(10);
SET brokerID = NEW.id;
SET mainBrokerID = NEW.broker_id;
SET userType = NEW.user_type;
IF(mainBrokerID is null and userType = 'broker') THEN 
	INSERT INTO reminder_days 
    VALUES('', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
			0, 0, 0, 0, 0, 0, 0, 0, 0, brokerID);
	INSERT INTO families(family_id, name, user_id, broker_id, `status`) 
    VALUES(familyID(brokerID), 'Default family', brokerID, brokerID, 
     		1);
	INSERT INTO sip_rate(scheme_type, rate, broker_id) VALUES 
    ('debt', 10, brokerID),
    ('equity', 10, brokerID),
    ('hybrid', 10, brokerID);
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `vmutual_fund_transactions_C20165238`
--

CREATE TABLE `vmutual_fund_transactions_C20165238` (
  `transaction_id` bigint(11) UNSIGNED NOT NULL DEFAULT '0',
  `client_id` varchar(30) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `transaction_date` date NOT NULL,
  `mutual_fund_scheme` int(11) NOT NULL,
  `mutual_fund_type` varchar(200) NOT NULL,
  `mutual_fund_sub_type` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `folio_number` varchar(200) NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `adjustment_flag` int(11) DEFAULT '0',
  `adjustment` text,
  `adjustment_ref_number` varchar(100) DEFAULT NULL,
  `DPO_units` decimal(18,10) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `cheque_number` varchar(50) DEFAULT NULL,
  `orig_trxn_no` varchar(30) DEFAULT NULL,
  `orig_trxn_type` varchar(10) DEFAULT NULL,
  `trxn_mode` varchar(10) DEFAULT NULL,
  `ref_no` varchar(30) DEFAULT NULL,
  `rej_ref_no` varchar(30) DEFAULT NULL,
  `amc_name` varchar(50) DEFAULT NULL,
  `arn` varchar(20) DEFAULT NULL,
  `sub_arn` varchar(20) DEFAULT NULL,
  `commission_uf` varchar(50) DEFAULT NULL,
  `commission_trail` varchar(50) DEFAULT NULL,
  `balance_unit` varchar(30) DEFAULT NULL,
  `from_file` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL COMMENT 'id of user or broker who has made the changes',
  `added_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_funds`
--

CREATE TABLE `withdraw_funds` (
  `withdraw_fund_id` int(11) NOT NULL,
  `family_id` varchar(30) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `transaction_date` date NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `cheque_no` varchar(30) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `bank_account_id` int(11) DEFAULT NULL,
  `withdraw_from` varchar(100) DEFAULT NULL,
  `trading_broker_id` int(11) DEFAULT NULL,
  `client_code` varchar(100) DEFAULT NULL,
  `mf_type` varchar(100) DEFAULT NULL,
  `add_notes` text,
  `broker_id` varchar(10) NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `added_on` date DEFAULT NULL,
  `updated_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure for view `cf_commitments`
--
DROP TABLE IF EXISTS `cf_commitments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_commitments`  AS SELECT 'Commitments' AS `investment`, `c`.`client_id` AS `client_id`, `lm`.`maturity_date` AS `comp_date`, year(`lm`.`maturity_date`) AS `year`, `lm`.`maturity_amount` AS `amount`, `c`.`name` AS `name`, `lt`.`broker_id` AS `broker_id` FROM ((`threeten_3tense_db`.`liability_maturity` `lm` join `threeten_3tense_db`.`liability_transactions` `lt` on((`lt`.`liability_id` = `lm`.`liability_id`))) join `threeten_3tense_db`.`clients` `c` on((`lt`.`client_id` = `c`.`client_id`))) WHERE (year(`lm`.`maturity_date`) = year(curdate()))union all select 'Commitments' AS `investment`,`c`.`client_id` AS `client_id`,`am`.`maturity_date` AS `maturity_date`,year(`am`.`maturity_date`) AS `year`,`am`.`maturity_amount` AS `amount`,`c`.`name` AS `name`,`att`.`broker_id` AS `broker_id` from ((`threeten_3tense_db`.`asset_maturity` `am` join `threeten_3tense_db`.`asset_transactions` `att` on((`att`.`asset_id` = `am`.`asset_id`))) join `threeten_3tense_db`.`clients` `c` on((`att`.`client_id` = `c`.`client_id`))) where (year(`am`.`maturity_date`) = year(curdate()))  ;

-- --------------------------------------------------------

--
-- Structure for view `cf_fd`
--
DROP TABLE IF EXISTS `cf_fd`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_fd`  AS SELECT 'FD' AS `investment`, `c`.`client_id` AS `client_id`, `fdi`.`interest_date` AS `comp_date`, year(`fdi`.`interest_date`) AS `year`, `fdi`.`interest_amount` AS `amount`, `c`.`name` AS `name`, `fdt`.`broker_id` AS `broker_id` FROM ((`threeten_3tense_db`.`fd_transactions` `fdt` join `threeten_3tense_db`.`fd_interests` `fdi` on((`fdt`.`fd_transaction_id` = `fdi`.`fd_transaction_id`))) join `threeten_3tense_db`.`clients` `c` on((`fdt`.`client_id` = `c`.`client_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_fd1`
--
DROP TABLE IF EXISTS `cf_fd1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_fd1`  AS SELECT 'FD' AS `investment`, `c`.`client_id` AS `client_id`, `fdi`.`interest_date` AS `comp_date`, year(`fdi`.`interest_date`) AS `year`, `fdi`.`interest_amount` AS `amount`, `c`.`name` AS `name`, `fdt`.`broker_id` AS `broker_id`, `fc`.`fd_comp_name` AS `fd_comp_name` FROM (((`threeten_3tense_db`.`fd_transactions` `fdt` join `threeten_3tense_db`.`fd_interests` `fdi` on((`fdt`.`fd_transaction_id` = `fdi`.`fd_transaction_id`))) join `threeten_3tense_db`.`clients` `c` on((`fdt`.`client_id` = `c`.`client_id`))) join `threeten_3tense_db`.`fd_companies` `fc` on((`fc`.`fd_comp_id` = `fdt`.`fd_comp_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_fd_maturity`
--
DROP TABLE IF EXISTS `cf_fd_maturity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_fd_maturity`  AS SELECT 'FD_Maturity' AS `investment`, `fdt`.`client_id` AS `client_id`, `fdt`.`maturity_date` AS `comp_date`, year(`fdt`.`maturity_date`) AS `year`, `fdt`.`maturity_amount` AS `amount`, `c`.`name` AS `name`, `fdt`.`broker_id` AS `broker_id` FROM (`threeten_3tense_db`.`fd_transactions` `fdt` join `threeten_3tense_db`.`clients` `c` on((`c`.`client_id` = `fdt`.`client_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_insurance`
--
DROP TABLE IF EXISTS `cf_insurance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_insurance`  AS SELECT 'Insurance' AS `investment`, `c`.`client_id` AS `client_id`, `pm`.`maturity_date` AS `comp_date`, year(`pm`.`maturity_date`) AS `year`, `pm`.`amount` AS `amount`, `c`.`name` AS `name`, `i`.`broker_id` AS `broker_id` FROM ((`threeten_3tense_db`.`premium_maturities` `pm` join `threeten_3tense_db`.`insurances` `i` on((`i`.`policy_num` = `pm`.`policy_num`))) join `threeten_3tense_db`.`clients` `c` on((`pm`.`client_id` = `c`.`client_id`))) WHERE (not(`i`.`status` in (select `threeten_3tense_db`.`premium_status`.`status_id` from `threeten_3tense_db`.`premium_status` where (`threeten_3tense_db`.`premium_status`.`status` in ('Lapsed','Paid up Cancellation'))))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_insurance_life_cover`
--
DROP TABLE IF EXISTS `cf_insurance_life_cover`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_insurance_life_cover`  AS SELECT 'Life_Cover' AS `investment`, `c`.`client_id` AS `client_id`, `ilc`.`date_of_payment` AS `comp_date`, year(`ilc`.`date_of_payment`) AS `year`, `ilc`.`amount` AS `amount`, `c`.`name` AS `name`, `i`.`broker_id` AS `broker_id` FROM (((`threeten_3tense_db`.`insurance_life_covers` `ilc` join `threeten_3tense_db`.`insurances` `i` on((`i`.`policy_num` = `ilc`.`policy_num`))) join `threeten_3tense_db`.`ins_plan_types` `ipt` on((`ipt`.`plan_type_id` = `i`.`plan_type_id`))) join `threeten_3tense_db`.`clients` `c` on((`i`.`client_id` = `c`.`client_id`))) WHERE ((not(`i`.`status` in (select `threeten_3tense_db`.`premium_status`.`status_id` from `threeten_3tense_db`.`premium_status` where (`threeten_3tense_db`.`premium_status`.`status` in ('Lapsed','Surrender','Matured','Paid Up Cancellation'))))) AND (`ipt`.`plan_type_name` in ('Traditional','Unit Linked','Term Plan'))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_insurance_premium`
--
DROP TABLE IF EXISTS `cf_insurance_premium`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_insurance_premium`  AS SELECT 'Insurance_Premium' AS `investment`, `c`.`client_id` AS `client_id`, `ppd`.`date_of_payment` AS `comp_date`, year(`ppd`.`date_of_payment`) AS `year`, `ppd`.`amount` AS `amount`, `c`.`name` AS `name`, `i`.`broker_id` AS `broker_id` FROM ((`threeten_3tense_db`.`premium_paying_details` `ppd` join `threeten_3tense_db`.`insurances` `i` on((`i`.`policy_num` = `ppd`.`policy_num`))) join `threeten_3tense_db`.`clients` `c` on((`i`.`client_id` = `c`.`client_id`))) WHERE `i`.`status` in (select `threeten_3tense_db`.`premium_status`.`status_id` from `threeten_3tense_db`.`premium_status` where (`threeten_3tense_db`.`premium_status`.`status` in ('Paid Up','In Force','Grace'))) ;

-- --------------------------------------------------------

--
-- Structure for view `cf_rent`
--
DROP TABLE IF EXISTS `cf_rent`;

CREATE ALGORITHM=UNDEFINED DEFINER=`threetense`@`localhost` SQL SECURITY DEFINER VIEW `cf_rent`  AS SELECT 'Rent_Amount' AS `investment`, `c`.`client_id` AS `client_id`, `prd`.`rent_date` AS `comp_date`, year(`prd`.`rent_date`) AS `year`, `prd`.`amount` AS `amount`, `c`.`name` AS `name`, `pt`.`broker_id` AS `broker_id` FROM ((`threeten_3tense_db`.`property_rent_details` `prd` join `threeten_3tense_db`.`property_transactions` `pt` on((`pt`.`pro_transaction_id` = `prd`.`pro_transaction_id`))) join `threeten_3tense_db`.`clients` `c` on((`pt`.`client_id` = `c`.`client_id`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_funds`
--
ALTER TABLE `add_funds`
  ADD PRIMARY KEY (`add_fund_id`),
  ADD KEY `bank_account_id` (`bank_account_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `bank_account_id_2` (`bank_account_id`),
  ADD KEY `trading_broker_id` (`trading_broker_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `super_admin_id` (`super_admin_id`);

--
-- Indexes for table `advisers`
--
ALTER TABLE `advisers`
  ADD PRIMARY KEY (`adviser_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `broker_id_2` (`broker_id`);

--
-- Indexes for table `al_companies`
--
ALTER TABLE `al_companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `al_products`
--
ALTER TABLE `al_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `al_schemes`
--
ALTER TABLE `al_schemes`
  ADD PRIMARY KEY (`scheme_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `al_types`
--
ALTER TABLE `al_types`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `operation` (`operation`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `asset_maturity`
--
ALTER TABLE `asset_maturity`
  ADD PRIMARY KEY (`asset_maturity_id`),
  ADD KEY `asset_id` (`asset_id`),
  ADD KEY `asset_id_2` (`asset_id`);

--
-- Indexes for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  ADD PRIMARY KEY (`asset_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `scheme_id_2` (`scheme_id`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `bank_id` (`bank_id`,`client_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `account_type` (`account_type`);

--
-- Indexes for table `bank_account_types`
--
ALTER TABLE `bank_account_types`
  ADD PRIMARY KEY (`account_type_id`),
  ADD UNIQUE KEY `account_type_name` (`account_type_name`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `BSC_ClientAccountMandateMaster`
--
ALTER TABLE `BSC_ClientAccountMandateMaster`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `BSC_ClientMaster`
--
ALTER TABLE `BSC_ClientMaster`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `BSC_ClientMaster_New`
--
ALTER TABLE `BSC_ClientMaster_New`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `Bsc_MFFolio`
--
ALTER TABLE `Bsc_MFFolio`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `BSC_Payment_Request`
--
ALTER TABLE `BSC_Payment_Request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `BSC_Payment_Response`
--
ALTER TABLE `BSC_Payment_Response`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `BSC_SchemeMaster`
--
ALTER TABLE `BSC_SchemeMaster`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `BSC_Transcation_Request`
--
ALTER TABLE `BSC_Transcation_Request`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `BSC_Transcation_Response`
--
ALTER TABLE `BSC_Transcation_Response`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `broker_id` (`user_id`),
  ADD KEY `head_of_family` (`head_of_family`),
  ADD KEY `client_type` (`client_type`),
  ADD KEY `occupation_id` (`occupation_id`),
  ADD KEY `pan_no` (`pan_no`),
  ADD KEY `merge_ref_id` (`merge_ref_id`);

--
-- Indexes for table `client_bank_details`
--
ALTER TABLE `client_bank_details`
  ADD PRIMARY KEY (`client_id`,`folio_number`,`productId`,`client_family_broker_id`) USING BTREE,
  ADD UNIQUE KEY `bid` (`bid`);

--
-- Indexes for table `client_brokers`
--
ALTER TABLE `client_brokers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id_2` (`client_id`),
  ADD KEY `broker` (`broker`),
  ADD KEY `client_code` (`client_code`);

--
-- Indexes for table `client_brokers_history`
--
ALTER TABLE `client_brokers_history`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `client_contact_details`
--
ALTER TABLE `client_contact_details`
  ADD PRIMARY KEY (`client_contact_id`),
  ADD KEY `contact_category_id` (`contact_category_id`,`client_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `client_device_detail`
--
ALTER TABLE `client_device_detail`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `client_logs`
--
ALTER TABLE `client_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_types`
--
ALTER TABLE `client_types`
  ADD PRIMARY KEY (`client_type_id`),
  ADD UNIQUE KEY `client_type_name` (`client_type_name`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `commodity_items`
--
ALTER TABLE `commodity_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item_unit` (`broker_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `commodity_rates`
--
ALTER TABLE `commodity_rates`
  ADD PRIMARY KEY (`commodity_rate_id`),
  ADD KEY `item_id` (`item_id`,`unit_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `broker_id_2` (`broker_id`);

--
-- Indexes for table `commodity_transactions`
--
ALTER TABLE `commodity_transactions`
  ADD PRIMARY KEY (`commodity_trans_id`),
  ADD KEY `client_id` (`client_id`,`commodity_item_id`,`commodity_unit_id`,`adviser_id`,`user_id`,`broker_id`),
  ADD KEY `client_id_2` (`client_id`),
  ADD KEY `commodity_item_id` (`commodity_item_id`),
  ADD KEY `commodity_unit_id` (`commodity_unit_id`),
  ADD KEY `adviser_id` (`adviser_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `broker_id_2` (`broker_id`);

--
-- Indexes for table `commodity_units`
--
ALTER TABLE `commodity_units`
  ADD PRIMARY KEY (`unit_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `broker_id_2` (`broker_id`);

--
-- Indexes for table `complete_reminders`
--
ALTER TABLE `complete_reminders`
  ADD PRIMARY KEY (`reminder_id`);

--
-- Indexes for table `contact_categories`
--
ALTER TABLE `contact_categories`
  ADD PRIMARY KEY (`contact_category_id`);

--
-- Indexes for table `demat_accounts`
--
ALTER TABLE `demat_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`,`client_id`,`user_id`),
  ADD KEY `broker_id` (`user_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `demat_providers`
--
ALTER TABLE `demat_providers`
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`document_type_id`),
  ADD UNIQUE KEY `document_type` (`document_type`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `equities`
--
ALTER TABLE `equities`
  ADD PRIMARY KEY (`equity_transaction_id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `client_broker_id` (`trading_broker_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_code` (`client_code`);

--
-- Indexes for table `equities_apc`
--
ALTER TABLE `equities_apc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equities_history`
--
ALTER TABLE `equities_history`
  ADD PRIMARY KEY (`equities_history_id`);

--
-- Indexes for table `equities_monthly_summary`
--
ALTER TABLE `equities_monthly_summary`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`error_log_id`);

--
-- Indexes for table `families`
--
ALTER TABLE `families`
  ADD PRIMARY KEY (`family_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `FDRateMaster`
--
ALTER TABLE `FDRateMaster`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `fd_companies`
--
ALTER TABLE `fd_companies`
  ADD PRIMARY KEY (`fd_comp_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `fd_interests`
--
ALTER TABLE `fd_interests`
  ADD PRIMARY KEY (`interest_id`),
  ADD KEY `fd_transaction_id` (`fd_transaction_id`);

--
-- Indexes for table `fd_investment_types`
--
ALTER TABLE `fd_investment_types`
  ADD PRIMARY KEY (`fd_inv_id`),
  ADD KEY `broker` (`broker_id`);

--
-- Indexes for table `fd_payout_modes`
--
ALTER TABLE `fd_payout_modes`
  ADD PRIMARY KEY (`payout_mode_id`);

--
-- Indexes for table `fd_transactions`
--
ALTER TABLE `fd_transactions`
  ADD PRIMARY KEY (`fd_transaction_id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `fd_inv_id` (`fd_inv_id`),
  ADD KEY `fd_comp_id` (`fd_comp_id`),
  ADD KEY `bank_id` (`inv_bank_id`),
  ADD KEY `maturity_bank_id` (`maturity_bank_id`),
  ADD KEY `maturity_payout_id` (`maturity_payout_id`),
  ADD KEY `adv_id` (`adv_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `fund_options`
--
ALTER TABLE `fund_options`
  ADD UNIQUE KEY `unique_index` (`policy_number`,`fund_option`,`broker_id`),
  ADD KEY `policy_number` (`policy_number`),
  ADD KEY `client_id` (`broker_id`);

--
-- Indexes for table `insurances`
--
ALTER TABLE `insurances`
  ADD PRIMARY KEY (`policy_num`),
  ADD KEY `client_id` (`client_id`,`plan_id`,`ins_comp_id`,`plan_type_id`,`mode`,`prem_amt`,`prem_type`,`prem_pay_mode_id`,`status`,`adv_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `ins_comp_id` (`ins_comp_id`),
  ADD KEY `plan_type_id` (`plan_type_id`),
  ADD KEY `mode` (`mode`),
  ADD KEY `prem_amt` (`prem_amt`),
  ADD KEY `prem_type` (`prem_type`),
  ADD KEY `prem_pay_mode_id` (`prem_pay_mode_id`),
  ADD KEY `status` (`status`),
  ADD KEY `adv_id` (`adv_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `insurance_life_covers`
--
ALTER TABLE `insurance_life_covers`
  ADD KEY `policy_num` (`policy_num`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `insurance_policies`
--
ALTER TABLE `insurance_policies`
  ADD PRIMARY KEY (`ins_policy_id`),
  ADD KEY `client_id` (`client_id`,`ins_comp_id`,`plan_id`,`policy_num`),
  ADD KEY `ins_comp_id` (`ins_comp_id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `policy_num` (`policy_num`);

--
-- Indexes for table `insurance_traditional_plans`
--
ALTER TABLE `insurance_traditional_plans`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `premium_id` (`premium_id`),
  ADD KEY `policy_number` (`policy_number`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `insurance_unit_linked_plans`
--
ALTER TABLE `insurance_unit_linked_plans`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `policy_number` (`policy_number`),
  ADD KEY `premium_id` (`premium_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `ins_companies`
--
ALTER TABLE `ins_companies`
  ADD PRIMARY KEY (`ins_comp_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `ins_plans`
--
ALTER TABLE `ins_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `ins_comp_id` (`ins_comp_id`,`plan_type_id`),
  ADD KEY `plan_type_id` (`plan_type_id`),
  ADD KEY `broker_id` (`user_id`),
  ADD KEY `policy_id` (`policy_id`),
  ADD KEY `broker_id_2` (`broker_id`),
  ADD KEY `broker_id_3` (`broker_id`);

--
-- Indexes for table `ins_plan_types`
--
ALTER TABLE `ins_plan_types`
  ADD PRIMARY KEY (`plan_type_id`);

--
-- Indexes for table `last_imports`
--
ALTER TABLE `last_imports`
  ADD UNIQUE KEY `broker_id` (`broker_id`,`import_type`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `liability_histories`
--
ALTER TABLE `liability_histories`
  ADD PRIMARY KEY (`liability_history_id`),
  ADD KEY `liability_id` (`liability_id`);

--
-- Indexes for table `liability_maturity`
--
ALTER TABLE `liability_maturity`
  ADD PRIMARY KEY (`liability_maturity_id`),
  ADD KEY `asset_id` (`liability_id`);

--
-- Indexes for table `liability_transactions`
--
ALTER TABLE `liability_transactions`
  ADD PRIMARY KEY (`liability_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `liability_id` (`liability_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `mf_schemes_current_nav`
--
ALTER TABLE `mf_schemes_current_nav`
  ADD KEY `scheme_id` (`scheme_id`);

--
-- Indexes for table `mf_schemes_histories`
--
ALTER TABLE `mf_schemes_histories`
  ADD PRIMARY KEY (`scheme_history_id`),
  ADD KEY `scheme_id` (`scheme_id`),
  ADD KEY `scheme_date` (`scheme_date`);

--
-- Indexes for table `mf_schemes_histories_audit`
--
ALTER TABLE `mf_schemes_histories_audit`
  ADD PRIMARY KEY (`scheme_history_id`);

--
-- Indexes for table `mf_scheme_types`
--
ALTER TABLE `mf_scheme_types`
  ADD PRIMARY KEY (`scheme_type_id`),
  ADD KEY `scheme_type` (`scheme_type`) USING BTREE;

--
-- Indexes for table `mf_trans_temp_0004`
--
ALTER TABLE `mf_trans_temp_0004`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_trans_temp_0009`
--
ALTER TABLE `mf_trans_temp_0009`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_trans_temp_0010`
--
ALTER TABLE `mf_trans_temp_0010`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_trans_temp_0063`
--
ALTER TABLE `mf_trans_temp_0063`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_trans_temp_0147`
--
ALTER TABLE `mf_trans_temp_0147`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_trans_temp_0180`
--
ALTER TABLE `mf_trans_temp_0180`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_valuation_reports`
--
ALTER TABLE `mf_valuation_reports`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `mf_val_temp_0004_1`
--
ALTER TABLE `mf_val_temp_0004_1`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mf_val_temp_01`
--
ALTER TABLE `mf_val_temp_01`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `MonthlySIPBook`
--
ALTER TABLE `MonthlySIPBook`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mutual_fund_monthly_summary`
--
ALTER TABLE `mutual_fund_monthly_summary`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `mutual_fund_schemes`
--
ALTER TABLE `mutual_fund_schemes`
  ADD PRIMARY KEY (`scheme_id`),
  ADD KEY `scheme_type_id` (`scheme_type_id`);

--
-- Indexes for table `mutual_fund_schemes_isin`
--
ALTER TABLE `mutual_fund_schemes_isin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scheme_id` (`scheme_id`);

--
-- Indexes for table `mutual_fund_transactions`
--
ALTER TABLE `mutual_fund_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `mf_duplicate` (`client_id`,`mutual_fund_scheme`,`mutual_fund_type`,`folio_number`,`purchase_date`,`quantity`,`amount`,`orig_trxn_no`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `mutual_fund_scheme` (`mutual_fund_scheme`),
  ADD KEY `folio_number` (`folio_number`),
  ADD KEY `mutual_fund_type` (`mutual_fund_type`),
  ADD KEY `transaction_type` (`transaction_type`),
  ADD KEY `purchase_date` (`purchase_date`),
  ADD KEY `orig_trxn_no` (`orig_trxn_no`),
  ADD KEY `orig_trxn_type` (`orig_trxn_type`),
  ADD KEY `ref_no` (`ref_no`),
  ADD KEY `rej_ref_no` (`rej_ref_no`),
  ADD KEY `amc_name` (`amc_name`),
  ADD KEY `arn` (`arn`),
  ADD KEY `quantity` (`quantity`),
  ADD KEY `bank_name` (`bank_name`),
  ADD KEY `added_on` (`added_on`);

--
-- Indexes for table `mutual_fund_types`
--
ALTER TABLE `mutual_fund_types`
  ADD PRIMARY KEY (`mutual_fund_type_id`);

--
-- Indexes for table `mutual_fund_valuation`
--
ALTER TABLE `mutual_fund_valuation`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `mutual_fund_valuation_cagr`
--
ALTER TABLE `mutual_fund_valuation_cagr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0004`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0004`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0009`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0009`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0063`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0063`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0154`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0154`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0180`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0180`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_delete_op_0204`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0204`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0004`
--
ALTER TABLE `mutual_fund_valuation_h_0004`
  ADD PRIMARY KEY (`valuation_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0009`
--
ALTER TABLE `mutual_fund_valuation_h_0009`
  ADD PRIMARY KEY (`valuation_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0010`
--
ALTER TABLE `mutual_fund_valuation_h_0010`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0063`
--
ALTER TABLE `mutual_fund_valuation_h_0063`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0147`
--
ALTER TABLE `mutual_fund_valuation_h_0147`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0180`
--
ALTER TABLE `mutual_fund_valuation_h_0180`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `mutual_fund_valuation_h_0204`
--
ALTER TABLE `mutual_fund_valuation_h_0204`
  ADD PRIMARY KEY (`valuation_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `nfo_detail`
--
ALTER TABLE `nfo_detail`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `occupations`
--
ALTER TABLE `occupations`
  ADD PRIMARY KEY (`occupation_id`),
  ADD UNIQUE KEY `occupation_name` (`occupation_name`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `policies`
--
ALTER TABLE `policies`
  ADD PRIMARY KEY (`policy_id`);

--
-- Indexes for table `premium_maturities`
--
ALTER TABLE `premium_maturities`
  ADD PRIMARY KEY (`maturity_id`),
  ADD KEY `policy_number` (`policy_num`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `premium_modes`
--
ALTER TABLE `premium_modes`
  ADD PRIMARY KEY (`mode_id`);

--
-- Indexes for table `premium_paying_details`
--
ALTER TABLE `premium_paying_details`
  ADD PRIMARY KEY (`premium_pay_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `policy_num` (`policy_num`);

--
-- Indexes for table `premium_pay_modes`
--
ALTER TABLE `premium_pay_modes`
  ADD PRIMARY KEY (`prem_pay_mode_id`);

--
-- Indexes for table `premium_status`
--
ALTER TABLE `premium_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `premium_transactions`
--
ALTER TABLE `premium_transactions`
  ADD PRIMARY KEY (`premium_id`),
  ADD KEY `policy_number` (`policy_number`,`bank_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `advisors` (`advisers`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `premium_types`
--
ALTER TABLE `premium_types`
  ADD PRIMARY KEY (`prem_type_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `property_rents`
--
ALTER TABLE `property_rents`
  ADD PRIMARY KEY (`pro_rent_id`),
  ADD KEY `pro_transaction_id` (`pro_transaction_id`);

--
-- Indexes for table `property_rent_details`
--
ALTER TABLE `property_rent_details`
  ADD PRIMARY KEY (`rent_detail_id`),
  ADD KEY `rent_id` (`rent_id`),
  ADD KEY `pro_transaction_id` (`pro_transaction_id`);

--
-- Indexes for table `property_transactions`
--
ALTER TABLE `property_transactions`
  ADD PRIMARY KEY (`pro_transaction_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `property_type_id` (`property_type_id`),
  ADD KEY `property_unit_id` (`property_unit_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `broker_id_2` (`broker_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `adviser_id` (`adviser_id`);

--
-- Indexes for table `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`property_type_id`);

--
-- Indexes for table `property_units`
--
ALTER TABLE `property_units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `real_stakes`
--
ALTER TABLE `real_stakes`
  ADD UNIQUE KEY `unique_index_stake` (`policy_number`,`stake_year`,`broker_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `client_id` (`client_id`,`concern_user`,`broker_id`),
  ADD KEY `concern_user` (`concern_user`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `reminder_days`
--
ALTER TABLE `reminder_days`
  ADD PRIMARY KEY (`reminder_days_id`),
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `reminder_email_config`
--
ALTER TABLE `reminder_email_config`
  ADD PRIMARY KEY (`email_id`,`broker_id`);

--
-- Indexes for table `scrip_rates`
--
ALTER TABLE `scrip_rates`
  ADD UNIQUE KEY `scrip_code` (`scrip_code`);

--
-- Indexes for table `sip_auto_import_error_backup`
--
ALTER TABLE `sip_auto_import_error_backup`
  ADD PRIMARY KEY (`a_id`),
  ADD UNIQUE KEY `sip_error_check` (`folio_no`,`scheme_id`,`broker_id`);

--
-- Indexes for table `sip_rate`
--
ALTER TABLE `sip_rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `summary_report_data`
--
ALTER TABLE `summary_report_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `super_admins`
--
ALTER TABLE `super_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `TABLE 136`
--
ALTER TABLE `TABLE 136`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `today_reminders`
--
ALTER TABLE `today_reminders`
  ADD PRIMARY KEY (`reminder_id`);

--
-- Indexes for table `trading_brokers`
--
ALTER TABLE `trading_brokers`
  ADD PRIMARY KEY (`trading_broker_id`),
  ADD UNIQUE KEY `trading_broker_name` (`trading_broker_name`,`trading_broker_id`) USING BTREE,
  ADD KEY `broker_id` (`broker_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `withdraw_funds`
--
ALTER TABLE `withdraw_funds`
  ADD PRIMARY KEY (`withdraw_fund_id`),
  ADD KEY `family_id` (`family_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `bank_account_id` (`bank_account_id`),
  ADD KEY `trading_broker_id` (`trading_broker_id`),
  ADD KEY `broker_id` (`broker_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_funds`
--
ALTER TABLE `add_funds`
  MODIFY `add_fund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5398;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT for table `al_companies`
--
ALTER TABLE `al_companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `al_products`
--
ALTER TABLE `al_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `al_schemes`
--
ALTER TABLE `al_schemes`
  MODIFY `scheme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11554;

--
-- AUTO_INCREMENT for table `al_types`
--
ALTER TABLE `al_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30177;

--
-- AUTO_INCREMENT for table `asset_maturity`
--
ALTER TABLE `asset_maturity`
  MODIFY `asset_maturity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14266761;

--
-- AUTO_INCREMENT for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12775;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5653;

--
-- AUTO_INCREMENT for table `bank_account_types`
--
ALTER TABLE `bank_account_types`
  MODIFY `account_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `BSC_ClientAccountMandateMaster`
--
ALTER TABLE `BSC_ClientAccountMandateMaster`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `BSC_ClientMaster`
--
ALTER TABLE `BSC_ClientMaster`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=520;

--
-- AUTO_INCREMENT for table `BSC_ClientMaster_New`
--
ALTER TABLE `BSC_ClientMaster_New`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=648;

--
-- AUTO_INCREMENT for table `Bsc_MFFolio`
--
ALTER TABLE `Bsc_MFFolio`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2505;

--
-- AUTO_INCREMENT for table `BSC_Payment_Request`
--
ALTER TABLE `BSC_Payment_Request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=490;

--
-- AUTO_INCREMENT for table `BSC_Payment_Response`
--
ALTER TABLE `BSC_Payment_Response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=502;

--
-- AUTO_INCREMENT for table `BSC_SchemeMaster`
--
ALTER TABLE `BSC_SchemeMaster`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18991;

--
-- AUTO_INCREMENT for table `BSC_Transcation_Request`
--
ALTER TABLE `BSC_Transcation_Request`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=566;

--
-- AUTO_INCREMENT for table `BSC_Transcation_Response`
--
ALTER TABLE `BSC_Transcation_Response`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=548;

--
-- AUTO_INCREMENT for table `client_bank_details`
--
ALTER TABLE `client_bank_details`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=443641;

--
-- AUTO_INCREMENT for table `client_brokers`
--
ALTER TABLE `client_brokers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1161;

--
-- AUTO_INCREMENT for table `client_brokers_history`
--
ALTER TABLE `client_brokers_history`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341735;

--
-- AUTO_INCREMENT for table `client_contact_details`
--
ALTER TABLE `client_contact_details`
  MODIFY `client_contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=263;

--
-- AUTO_INCREMENT for table `client_device_detail`
--
ALTER TABLE `client_device_detail`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=627;

--
-- AUTO_INCREMENT for table `client_logs`
--
ALTER TABLE `client_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_types`
--
ALTER TABLE `client_types`
  MODIFY `client_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `commodity_items`
--
ALTER TABLE `commodity_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `commodity_rates`
--
ALTER TABLE `commodity_rates`
  MODIFY `commodity_rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `commodity_units`
--
ALTER TABLE `commodity_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `complete_reminders`
--
ALTER TABLE `complete_reminders`
  MODIFY `reminder_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176254;

--
-- AUTO_INCREMENT for table `contact_categories`
--
ALTER TABLE `contact_categories`
  MODIFY `contact_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `demat_accounts`
--
ALTER TABLE `demat_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=517;

--
-- AUTO_INCREMENT for table `demat_providers`
--
ALTER TABLE `demat_providers`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `document_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1301;

--
-- AUTO_INCREMENT for table `equities`
--
ALTER TABLE `equities`
  MODIFY `equity_transaction_id` bigint(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2087238;

--
-- AUTO_INCREMENT for table `equities_apc`
--
ALTER TABLE `equities_apc`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4487;

--
-- AUTO_INCREMENT for table `equities_history`
--
ALTER TABLE `equities_history`
  MODIFY `equities_history_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312928;

--
-- AUTO_INCREMENT for table `equities_monthly_summary`
--
ALTER TABLE `equities_monthly_summary`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9391;

--
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `error_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `FDRateMaster`
--
ALTER TABLE `FDRateMaster`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `fd_companies`
--
ALTER TABLE `fd_companies`
  MODIFY `fd_comp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `fd_interests`
--
ALTER TABLE `fd_interests`
  MODIFY `interest_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81030;

--
-- AUTO_INCREMENT for table `fd_investment_types`
--
ALTER TABLE `fd_investment_types`
  MODIFY `fd_inv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `fd_payout_modes`
--
ALTER TABLE `fd_payout_modes`
  MODIFY `payout_mode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fd_transactions`
--
ALTER TABLE `fd_transactions`
  MODIFY `fd_transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20648;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34250;

--
-- AUTO_INCREMENT for table `insurance_policies`
--
ALTER TABLE `insurance_policies`
  MODIFY `ins_policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48902;

--
-- AUTO_INCREMENT for table `insurance_traditional_plans`
--
ALTER TABLE `insurance_traditional_plans`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=740261;

--
-- AUTO_INCREMENT for table `insurance_unit_linked_plans`
--
ALTER TABLE `insurance_unit_linked_plans`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30502;

--
-- AUTO_INCREMENT for table `ins_companies`
--
ALTER TABLE `ins_companies`
  MODIFY `ins_comp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `ins_plans`
--
ALTER TABLE `ins_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4224;

--
-- AUTO_INCREMENT for table `ins_plan_types`
--
ALTER TABLE `ins_plan_types`
  MODIFY `plan_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `liability_histories`
--
ALTER TABLE `liability_histories`
  MODIFY `liability_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `liability_maturity`
--
ALTER TABLE `liability_maturity`
  MODIFY `liability_maturity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6900;

--
-- AUTO_INCREMENT for table `liability_transactions`
--
ALTER TABLE `liability_transactions`
  MODIFY `liability_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `mf_schemes_histories`
--
ALTER TABLE `mf_schemes_histories`
  MODIFY `scheme_history_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21921723;

--
-- AUTO_INCREMENT for table `mf_schemes_histories_audit`
--
ALTER TABLE `mf_schemes_histories_audit`
  MODIFY `scheme_history_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mf_scheme_types`
--
ALTER TABLE `mf_scheme_types`
  MODIFY `scheme_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mf_valuation_reports`
--
ALTER TABLE `mf_valuation_reports`
  MODIFY `transaction_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1792;

--
-- AUTO_INCREMENT for table `mf_val_temp_0004_1`
--
ALTER TABLE `mf_val_temp_0004_1`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71373;

--
-- AUTO_INCREMENT for table `mf_val_temp_01`
--
ALTER TABLE `mf_val_temp_01`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `MonthlySIPBook`
--
ALTER TABLE `MonthlySIPBook`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `mutual_fund_monthly_summary`
--
ALTER TABLE `mutual_fund_monthly_summary`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38937;

--
-- AUTO_INCREMENT for table `mutual_fund_schemes`
--
ALTER TABLE `mutual_fund_schemes`
  MODIFY `scheme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21780;

--
-- AUTO_INCREMENT for table `mutual_fund_schemes_isin`
--
ALTER TABLE `mutual_fund_schemes_isin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21450;

--
-- AUTO_INCREMENT for table `mutual_fund_transactions`
--
ALTER TABLE `mutual_fund_transactions`
  MODIFY `transaction_id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5366565;

--
-- AUTO_INCREMENT for table `mutual_fund_types`
--
ALTER TABLE `mutual_fund_types`
  MODIFY `mutual_fund_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation`
--
ALTER TABLE `mutual_fund_valuation`
  MODIFY `valuation_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32972005;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_cagr`
--
ALTER TABLE `mutual_fund_valuation_cagr`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23354;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0004`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0004`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0009`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0009`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0063`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0063`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0154`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0154`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0180`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0180`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1074;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_delete_op_0204`
--
ALTER TABLE `mutual_fund_valuation_delete_op_0204`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0004`
--
ALTER TABLE `mutual_fund_valuation_h_0004`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0009`
--
ALTER TABLE `mutual_fund_valuation_h_0009`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=783;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0010`
--
ALTER TABLE `mutual_fund_valuation_h_0010`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0063`
--
ALTER TABLE `mutual_fund_valuation_h_0063`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0147`
--
ALTER TABLE `mutual_fund_valuation_h_0147`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0180`
--
ALTER TABLE `mutual_fund_valuation_h_0180`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=885;

--
-- AUTO_INCREMENT for table `mutual_fund_valuation_h_0204`
--
ALTER TABLE `mutual_fund_valuation_h_0204`
  MODIFY `valuation_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `nfo_detail`
--
ALTER TABLE `nfo_detail`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `occupations`
--
ALTER TABLE `occupations`
  MODIFY `occupation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `policies`
--
ALTER TABLE `policies`
  MODIFY `policy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3126;

--
-- AUTO_INCREMENT for table `premium_maturities`
--
ALTER TABLE `premium_maturities`
  MODIFY `maturity_id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177954;

--
-- AUTO_INCREMENT for table `premium_modes`
--
ALTER TABLE `premium_modes`
  MODIFY `mode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `premium_paying_details`
--
ALTER TABLE `premium_paying_details`
  MODIFY `premium_pay_id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5724912;

--
-- AUTO_INCREMENT for table `premium_pay_modes`
--
ALTER TABLE `premium_pay_modes`
  MODIFY `prem_pay_mode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `premium_status`
--
ALTER TABLE `premium_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `premium_transactions`
--
ALTER TABLE `premium_transactions`
  MODIFY `premium_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=783154;

--
-- AUTO_INCREMENT for table `premium_types`
--
ALTER TABLE `premium_types`
  MODIFY `prem_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `property_rents`
--
ALTER TABLE `property_rents`
  MODIFY `pro_rent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `property_rent_details`
--
ALTER TABLE `property_rent_details`
  MODIFY `rent_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7781;

--
-- AUTO_INCREMENT for table `property_types`
--
ALTER TABLE `property_types`
  MODIFY `property_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `property_units`
--
ALTER TABLE `property_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reminder`
--
ALTER TABLE `reminder`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminder_days`
--
ALTER TABLE `reminder_days`
  MODIFY `reminder_days_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `sip_auto_import_error_backup`
--
ALTER TABLE `sip_auto_import_error_backup`
  MODIFY `a_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sip_rate`
--
ALTER TABLE `sip_rate`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `summary_report_data`
--
ALTER TABLE `summary_report_data`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170941;

--
-- AUTO_INCREMENT for table `super_admins`
--
ALTER TABLE `super_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `TABLE 136`
--
ALTER TABLE `TABLE 136`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `today_reminders`
--
ALTER TABLE `today_reminders`
  MODIFY `reminder_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1126288;

--
-- AUTO_INCREMENT for table `trading_brokers`
--
ALTER TABLE `trading_brokers`
  MODIFY `trading_broker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `withdraw_funds`
--
ALTER TABLE `withdraw_funds`
  MODIFY `withdraw_fund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1816;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_funds`
--
ALTER TABLE `add_funds`
  ADD CONSTRAINT `add_funds_account_id_bank_accounts` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`account_id`),
  ADD CONSTRAINT `add_funds_broker_id_users` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `add_funds_client_id_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `add_funds_family_id_families` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `add_funds_trading_broker_id_trading_brokers` FOREIGN KEY (`trading_broker_id`) REFERENCES `trading_brokers` (`trading_broker_id`),
  ADD CONSTRAINT `add_funds_user_id_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `super_admin_ref` FOREIGN KEY (`super_admin_id`) REFERENCES `super_admins` (`id`);

--
-- Constraints for table `advisers`
--
ALTER TABLE `advisers`
  ADD CONSTRAINT `advisors_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `al_companies`
--
ALTER TABLE `al_companies`
  ADD CONSTRAINT `companies_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `al_products`
--
ALTER TABLE `al_products`
  ADD CONSTRAINT `al_product_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `al_types`
--
ALTER TABLE `al_types`
  ADD CONSTRAINT `type_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `asset_transactions`
--
ALTER TABLE `asset_transactions`
  ADD CONSTRAINT `asset_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `asset_trans_company_ref` FOREIGN KEY (`company_id`) REFERENCES `al_companies` (`company_id`),
  ADD CONSTRAINT `asset_trans_product_ref` FOREIGN KEY (`product_id`) REFERENCES `al_products` (`product_id`),
  ADD CONSTRAINT `asset_trans_type_ref` FOREIGN KEY (`type_id`) REFERENCES `al_types` (`type_id`),
  ADD CONSTRAINT `asset_trans_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `asset_transactions_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `banks`
--
ALTER TABLE `banks`
  ADD CONSTRAINT `banks_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `bank_account_account_type_ref` FOREIGN KEY (`account_type`) REFERENCES `bank_account_types` (`account_type_id`),
  ADD CONSTRAINT `bank_account_bank_ref` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`),
  ADD CONSTRAINT `bank_account_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `bank_accounts_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bank_account_types`
--
ALTER TABLE `bank_account_types`
  ADD CONSTRAINT `bank_account_types_broker_id_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `client_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `client_family_ref` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `client_type_ref` FOREIGN KEY (`client_type`) REFERENCES `client_types` (`client_type_id`);

--
-- Constraints for table `client_brokers`
--
ALTER TABLE `client_brokers`
  ADD CONSTRAINT `client_brokers_clients_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `client_brokers_trading_broker_ref` FOREIGN KEY (`broker`) REFERENCES `trading_brokers` (`trading_broker_id`),
  ADD CONSTRAINT `client_brokers_users_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `client_contact_details`
--
ALTER TABLE `client_contact_details`
  ADD CONSTRAINT `contact_details_category_rel` FOREIGN KEY (`contact_category_id`) REFERENCES `contact_categories` (`contact_category_id`),
  ADD CONSTRAINT `contact_details_client_rel` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `contact_details_user_id_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `client_types`
--
ALTER TABLE `client_types`
  ADD CONSTRAINT `client_type_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commodity_items`
--
ALTER TABLE `commodity_items`
  ADD CONSTRAINT `commodity_items_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commodity_rates`
--
ALTER TABLE `commodity_rates`
  ADD CONSTRAINT `commodity_rate_item_ref` FOREIGN KEY (`item_id`) REFERENCES `commodity_items` (`item_id`),
  ADD CONSTRAINT `commodity_rate_unit_ref` FOREIGN KEY (`unit_id`) REFERENCES `commodity_units` (`unit_id`),
  ADD CONSTRAINT `commodity_rates_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commodity_transactions`
--
ALTER TABLE `commodity_transactions`
  ADD CONSTRAINT `commodity_trans_adviser_ref` FOREIGN KEY (`adviser_id`) REFERENCES `advisers` (`adviser_id`),
  ADD CONSTRAINT `commodity_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `commodity_trans_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `commodity_trans_item_ref` FOREIGN KEY (`commodity_item_id`) REFERENCES `commodity_items` (`item_id`),
  ADD CONSTRAINT `commodity_trans_unit_ref` FOREIGN KEY (`commodity_unit_id`) REFERENCES `commodity_units` (`unit_id`),
  ADD CONSTRAINT `commodity_trans_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commodity_units`
--
ALTER TABLE `commodity_units`
  ADD CONSTRAINT `commodity_units_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `demat_accounts`
--
ALTER TABLE `demat_accounts`
  ADD CONSTRAINT `demat_account_broker_rel` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `demat_account_client_rel` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `demat_account_provider_rel` FOREIGN KEY (`provider_id`) REFERENCES `demat_providers` (`provider_id`);

--
-- Constraints for table `demat_providers`
--
ALTER TABLE `demat_providers`
  ADD CONSTRAINT `demat_provider_broker_rel` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `document_types`
--
ALTER TABLE `document_types`
  ADD CONSTRAINT `document_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `equities`
--
ALTER TABLE `equities`
  ADD CONSTRAINT `equity_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `equity_clients_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `equity_families_ref` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `equity_trading_broker_ref` FOREIGN KEY (`trading_broker_id`) REFERENCES `trading_brokers` (`trading_broker_id`),
  ADD CONSTRAINT `equity_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `families`
--
ALTER TABLE `families`
  ADD CONSTRAINT `family_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `fd_companies`
--
ALTER TABLE `fd_companies`
  ADD CONSTRAINT `fd_companies_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `fd_interests`
--
ALTER TABLE `fd_interests`
  ADD CONSTRAINT `fd_interest_fd_trans_ref` FOREIGN KEY (`fd_transaction_id`) REFERENCES `fd_transactions` (`fd_transaction_id`);

--
-- Constraints for table `fd_investment_types`
--
ALTER TABLE `fd_investment_types`
  ADD CONSTRAINT `fd_inv_types_brokers_id` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `fd_transactions`
--
ALTER TABLE `fd_transactions`
  ADD CONSTRAINT `fd_trans_advisers_ref` FOREIGN KEY (`adv_id`) REFERENCES `advisers` (`adviser_id`),
  ADD CONSTRAINT `fd_trans_brokers_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fd_trans_clients_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `fd_trans_families_ref` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `fd_trans_fd_companies_ref` FOREIGN KEY (`fd_comp_id`) REFERENCES `fd_companies` (`fd_comp_id`),
  ADD CONSTRAINT `fd_trans_inv_banks_ref` FOREIGN KEY (`inv_bank_id`) REFERENCES `banks` (`bank_id`),
  ADD CONSTRAINT `fd_trans_inv_types_ref` FOREIGN KEY (`fd_inv_id`) REFERENCES `fd_investment_types` (`fd_inv_id`),
  ADD CONSTRAINT `fd_trans_mat_banks_ref` FOREIGN KEY (`maturity_bank_id`) REFERENCES `banks` (`bank_id`),
  ADD CONSTRAINT `fd_trans_payout_ref` FOREIGN KEY (`maturity_payout_id`) REFERENCES `fd_payout_modes` (`payout_mode_id`),
  ADD CONSTRAINT `fd_trans_users_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `fund_options`
--
ALTER TABLE `fund_options`
  ADD CONSTRAINT `fund_option_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fund_options_ins_ref` FOREIGN KEY (`policy_number`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE;

--
-- Constraints for table `insurances`
--
ALTER TABLE `insurances`
  ADD CONSTRAINT `ins_adviser_ref` FOREIGN KEY (`adv_id`) REFERENCES `advisers` (`adviser_id`),
  ADD CONSTRAINT `ins_broker_id` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ins_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `ins_company_ref` FOREIGN KEY (`ins_comp_id`) REFERENCES `ins_companies` (`ins_comp_id`),
  ADD CONSTRAINT `ins_plan_ref` FOREIGN KEY (`plan_id`) REFERENCES `ins_plans` (`plan_id`),
  ADD CONSTRAINT `ins_premium_mode_ref` FOREIGN KEY (`mode`) REFERENCES `premium_modes` (`mode_id`),
  ADD CONSTRAINT `ins_premium_pay_mode_ref` FOREIGN KEY (`prem_pay_mode_id`) REFERENCES `premium_pay_modes` (`prem_pay_mode_id`),
  ADD CONSTRAINT `ins_premium_status_ref` FOREIGN KEY (`status`) REFERENCES `premium_status` (`status_id`),
  ADD CONSTRAINT `ins_premium_type_ref` FOREIGN KEY (`prem_type`) REFERENCES `premium_types` (`prem_type_id`),
  ADD CONSTRAINT `insurance_ins_plan_type_id_ref` FOREIGN KEY (`plan_type_id`) REFERENCES `ins_plan_types` (`plan_type_id`),
  ADD CONSTRAINT `insurances_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `insurance_life_covers`
--
ALTER TABLE `insurance_life_covers`
  ADD CONSTRAINT `ins_life_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ins_life_ins_ref` FOREIGN KEY (`policy_num`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE;

--
-- Constraints for table `insurance_policies`
--
ALTER TABLE `insurance_policies`
  ADD CONSTRAINT `clients_ins_policies_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `ins_comp_ins_policies_ref` FOREIGN KEY (`ins_comp_id`) REFERENCES `ins_companies` (`ins_comp_id`),
  ADD CONSTRAINT `ins_plan_ins_policies_ref` FOREIGN KEY (`plan_id`) REFERENCES `ins_plans` (`plan_id`);

--
-- Constraints for table `insurance_traditional_plans`
--
ALTER TABLE `insurance_traditional_plans`
  ADD CONSTRAINT `ins_trad_plans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ins_trad_premium_trans_id` FOREIGN KEY (`premium_id`) REFERENCES `premium_transactions` (`premium_id`),
  ADD CONSTRAINT `ins_traditional_ins_ref` FOREIGN KEY (`policy_number`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE;

--
-- Constraints for table `insurance_unit_linked_plans`
--
ALTER TABLE `insurance_unit_linked_plans`
  ADD CONSTRAINT `ins_ul_plans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ins_unit_linked_plans_ins_ref` FOREIGN KEY (`policy_number`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE,
  ADD CONSTRAINT `ins_unit_linked_plans_premium_trans_ref` FOREIGN KEY (`premium_id`) REFERENCES `premium_transactions` (`premium_id`);

--
-- Constraints for table `ins_companies`
--
ALTER TABLE `ins_companies`
  ADD CONSTRAINT `ins_comps_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ins_plans`
--
ALTER TABLE `ins_plans`
  ADD CONSTRAINT `ins_plan_company_ref` FOREIGN KEY (`ins_comp_id`) REFERENCES `ins_companies` (`ins_comp_id`),
  ADD CONSTRAINT `ins_plan_type_ref` FOREIGN KEY (`plan_type_id`) REFERENCES `ins_plan_types` (`plan_type_id`),
  ADD CONSTRAINT `ins_plans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ins_plans_policies_ref` FOREIGN KEY (`policy_id`) REFERENCES `policies` (`policy_id`),
  ADD CONSTRAINT `ins_plans_users_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `last_imports`
--
ALTER TABLE `last_imports`
  ADD CONSTRAINT `last_import_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `last_import_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `liability_histories`
--
ALTER TABLE `liability_histories`
  ADD CONSTRAINT `liab_history_liab_trans_ref` FOREIGN KEY (`liability_id`) REFERENCES `liability_transactions` (`liability_id`);

--
-- Constraints for table `liability_maturity`
--
ALTER TABLE `liability_maturity`
  ADD CONSTRAINT `liab_mat_liab_trans_ref` FOREIGN KEY (`liability_id`) REFERENCES `liability_transactions` (`liability_id`);

--
-- Constraints for table `liability_transactions`
--
ALTER TABLE `liability_transactions`
  ADD CONSTRAINT `liab_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `liab_trans_comp_ref` FOREIGN KEY (`company_id`) REFERENCES `al_companies` (`company_id`),
  ADD CONSTRAINT `liab_trans_prod_ref` FOREIGN KEY (`product_id`) REFERENCES `al_products` (`product_id`),
  ADD CONSTRAINT `liab_trans_scheme_ref` FOREIGN KEY (`scheme_id`) REFERENCES `al_schemes` (`scheme_id`),
  ADD CONSTRAINT `liab_trans_types_ref` FOREIGN KEY (`type_id`) REFERENCES `al_types` (`type_id`),
  ADD CONSTRAINT `liab_trans_users_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `liability_transactions_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `mf_schemes_histories`
--
ALTER TABLE `mf_schemes_histories`
  ADD CONSTRAINT `mf_scheme_hist_mf_scheme_ref` FOREIGN KEY (`scheme_id`) REFERENCES `mutual_fund_schemes` (`scheme_id`);

--
-- Constraints for table `mutual_fund_schemes`
--
ALTER TABLE `mutual_fund_schemes`
  ADD CONSTRAINT `mf_schemes_scheme_type_id_ref` FOREIGN KEY (`scheme_type_id`) REFERENCES `mf_scheme_types` (`scheme_type_id`);

--
-- Constraints for table `mutual_fund_transactions`
--
ALTER TABLE `mutual_fund_transactions`
  ADD CONSTRAINT `mf_trans_bank_ref` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`),
  ADD CONSTRAINT `mf_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `mf_trans_family_ref` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `mf_trans_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `mutual_fund_trans_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `occupations`
--
ALTER TABLE `occupations`
  ADD CONSTRAINT `occupation_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `premium_maturities`
--
ALTER TABLE `premium_maturities`
  ADD CONSTRAINT `prem_mats_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prem_mats_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `premium_mat_ins_ref` FOREIGN KEY (`policy_num`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE;

--
-- Constraints for table `premium_paying_details`
--
ALTER TABLE `premium_paying_details`
  ADD CONSTRAINT `prem_pay_broker` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `prem_pay_ins_ref` FOREIGN KEY (`policy_num`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE;

--
-- Constraints for table `premium_transactions`
--
ALTER TABLE `premium_transactions`
  ADD CONSTRAINT `prem_trans_bank_ref` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`bank_id`),
  ADD CONSTRAINT `prem_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `premium_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `premium_trans_ins_ref` FOREIGN KEY (`policy_number`) REFERENCES `insurances` (`policy_num`) ON DELETE CASCADE,
  ADD CONSTRAINT `premium_transactions_broker_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `premium_types`
--
ALTER TABLE `premium_types`
  ADD CONSTRAINT `premium_types_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `property_rents`
--
ALTER TABLE `property_rents`
  ADD CONSTRAINT `rents_pro_transaction_ref` FOREIGN KEY (`pro_transaction_id`) REFERENCES `property_transactions` (`pro_transaction_id`);

--
-- Constraints for table `property_rent_details`
--
ALTER TABLE `property_rent_details`
  ADD CONSTRAINT `rent_details_rents_ref` FOREIGN KEY (`rent_id`) REFERENCES `property_rents` (`pro_rent_id`),
  ADD CONSTRAINT `rents_details_pro_transaction_ref` FOREIGN KEY (`pro_transaction_id`) REFERENCES `property_transactions` (`pro_transaction_id`);

--
-- Constraints for table `property_transactions`
--
ALTER TABLE `property_transactions`
  ADD CONSTRAINT `pro_trans_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pro_trans_cleints_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `pro_trans_pro_type_ref` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`property_type_id`),
  ADD CONSTRAINT `pro_trans_pro_units_ref` FOREIGN KEY (`property_unit_id`) REFERENCES `property_units` (`unit_id`),
  ADD CONSTRAINT `pro_trans_users_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `property_trans_advisers_ref` FOREIGN KEY (`adviser_id`) REFERENCES `advisers` (`adviser_id`);

--
-- Constraints for table `real_stakes`
--
ALTER TABLE `real_stakes`
  ADD CONSTRAINT `stakes_broker_id` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reminder`
--
ALTER TABLE `reminder`
  ADD CONSTRAINT `reminder_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reminder_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `reminder_user_ref` FOREIGN KEY (`concern_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `reminder_days`
--
ALTER TABLE `reminder_days`
  ADD CONSTRAINT `reminder_days_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `trading_brokers`
--
ALTER TABLE `trading_brokers`
  ADD CONSTRAINT `trading_brokers_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `admin_ref` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `users_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `withdraw_funds`
--
ALTER TABLE `withdraw_funds`
  ADD CONSTRAINT `wf_bank_account_ref` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`account_id`),
  ADD CONSTRAINT `wf_broker_ref` FOREIGN KEY (`broker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wf_client_ref` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `wf_family_ref` FOREIGN KEY (`family_id`) REFERENCES `families` (`family_id`),
  ADD CONSTRAINT `wf_trading_broker_ref` FOREIGN KEY (`trading_broker_id`) REFERENCES `trading_brokers` (`trading_broker_id`),
  ADD CONSTRAINT `wf_user_ref` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
