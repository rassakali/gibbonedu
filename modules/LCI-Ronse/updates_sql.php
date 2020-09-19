<?php
/*
 * Created on 16 sept. 2020 by rezki
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
// Module includes
require_once __DIR__ . '/moduleFunctions.php';

if (isModuleAccessible($guid, $connection2) == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {

    // die("For run this page, remove die;");

    // Proceed!
    echo "<h1>Updates..!</h1>";
    $page->breadcrumbs->add(__('Update SQLq'));

    // init
    $gibbonSchoolYearID = null;
    $gibbonFinanceFeeCategoryID = null;
    $schoolYear1 = null;
    $schoolYear2 = null;

    /**
     * gibbonSchoolYear
     */
    if (empty($gibbonSchoolYearID)) {
        $data = [];
        $sql = "SELECT gibbonSchoolYearID FROM gibbonSchoolYear WHERE status='Current'";
        $result = $pdo->executeQuery($data, $sql);
        if ($result) {
            $count = $result->rowCount();
            if ($count == 1) {
                $row = $result->fetch();
                $gibbonSchoolYearID = $row['gibbonSchoolYearID'];
                customGbn_printMsg("fetch OK: gibbonSchoolYearID=$gibbonSchoolYearID");
            } else {
                customGbn_printErr("Current year dont exists or cant be more then one in gibbonSchoolYear table. sql= " . customGbn_getSql($sql, $data));
                die();
            }
        } else {
            customGbn_printErr("result=false, sql=" . customGbn_getSql($sql, $data));
        }
    }
    if (empty($gibbonSchoolYearID)) {
        customGbn_printErr("gibbonSchoolYearID is empty");
    } else {
        customGbn_printMsg("gibbonSchoolYearID=$gibbonSchoolYearID");
    }
    /**
     * Calculate School Year
     */
    if (! empty($gibbonSchoolYearID)) {
        $result = $pdo->executeQuery([], "SELECT * FROM gibbonSchoolYear WHERE gibbonSchoolYearID=$gibbonSchoolYearID");
        if ($result && $result->rowCount() == 1) {
            $row = $result->fetch();
            // $schoolYear1
            $date = DateTime::createFromFormat("Y-m-d", $row['firstDay']);
            $schoolYear1 = $date->format("Y");
            customGbn_printMsg("schoolYear1=$schoolYear1 --> calculated OK");
            // $schoolYear2
            $date = DateTime::createFromFormat("Y-m-d", $row['lastDay']);
            $schoolYear2 = $date->format("Y");
            customGbn_printMsg("schoolYear2=$schoolYear2 --> calculated OK");
        } else {
            customGbn_printErr("result=false, sql=" . customGbn_getSql($sql, []));
        }
    }
    if (empty($schoolYear1) || empty($schoolYear2)) {
        customGbn_printErr("schoolYear1 or schoolYear2 are empty");
    } else {
        customGbn_printMsg("schoolYear1=$schoolYear1, schoolYear2=$schoolYear2");
    }

    /**
     * gibbonFinanceFeeCategory
     */
    if (empty($gibbonFinanceFeeCategoryID)) {
        $data = array(
            'name' => 'RECURRING_FEE'
        );
        $sql = "SELECT * FROM gibbonFinanceFeeCategory WHERE name=:name";
        $result = $pdo->executeQuery($data, $sql);
        if ($result) {
            $count = $result->rowCount();
            if ($count == 0) {
                try {
                    $data = [
                        'description' => "Frais récurrents" // ne pas utiliser "customGbn_quote" car il est dans "prepare"
                    ];
                    $sql = "INSERT INTO `gibbonFinanceFeeCategory` (`name`, `nameShort`, `description`, `active`, `gibbonPersonIDCreator`, `timestampCreator`, `gibbonPersonIDUpdate`, `timestampUpdate`) VALUES ('RECURRING_FEE', 'RF', :description, 'Y', 0000000001, NOW(), NULL, NULL)";
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                    $gibbonFinanceFeeCategoryID = $connection2->lastInsertId();
                    customGbn_printMsg("insert OK: gibbonFinanceFeeCategoryID=$gibbonFinanceFeeCategoryID");
                } catch (PDOException $e) {
                    echo customGbn_getSql($sql, $data);
                    echo '</br>';
                    echo $e->getMessage();
                    die();
                }
            } elseif ($count == 1) {
                $row = $result->fetch();
                $gibbonFinanceFeeCategoryID = $row['gibbonFinanceFeeCategoryID'];
                customGbn_printMsg("fetch OK: gibbonFinanceFeeCategoryID=$gibbonFinanceFeeCategoryID");
            } else {
                customGbn_printErr("'RECURRING_FEE' cant be more then one in gibbonFinanceFeeCategory table. sql= " . customGbn_getSql($sql, $data));
                die();
            }
        } else {
            customGbn_printErr("result=false, sql=" . customGbn_getSql($sql, $data));
        }
    }
    if (empty($gibbonFinanceFeeCategoryID)) {
        customGbn_printErr("gibbonFinanceFeeCategoryID is empty");
    } else {
        customGbn_printMsg("gibbonFinanceFeeCategoryID=$gibbonFinanceFeeCategoryID");
    }

    /**
     * gibbonFinanceFee
     */
    if (! empty($gibbonSchoolYearID) && ! empty($gibbonFinanceFeeCategoryID)) {
        try {
            $sql2 = '';
            $sql1 = "SELECT * FROM gibbonFinanceFee WHERE gibbonSchoolYearID=$gibbonSchoolYearID AND gibbonFinanceFeeCategoryID=$gibbonFinanceFeeCategoryID AND name=";
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME001);
            $feeDesc = customGbn_quote($connection2, "Frais d'inscription");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[0]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= "($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '30.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }

            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME002);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de septembre");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[1]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME003);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de octobre");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[2]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME004);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de novembre");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[3]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME005);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de décembre");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[4]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME006);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de janvier");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[5]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME007);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de février");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[6]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME008);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de mars");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[7]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME009);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de avril");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[8]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME010);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de mai");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[9]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME011);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de juin");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[10]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '15.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_FEE_NAME012);
            $feeDesc = customGbn_quote($connection2, "Réduction annuelle");
            $feeNameShort = customGbn_quote($connection2, array_keys(CO_FINANCE_FEE_NAMESHORT)[11]);
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeNameShort, $feeDesc, 'Y', $gibbonFinanceFeeCategoryID, '-30.00', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            if (! empty($sql2)) {
                $sql = "INSERT INTO `gibbonFinanceFee` (`gibbonSchoolYearID`, `name`, `nameShort`, `description`, `active`, `gibbonFinanceFeeCategoryID`, `fee`, `gibbonPersonIDCreator`, `timestampCreator`, `gibbonPersonIDUpdate`, `timestampUpdate`) VALUES";
                $sql .= substr_replace($sql2, "", - 1);
                $pdo->executeQuery([], $sql);
                customGbn_printMsg("insert OK in gibbonFinanceFee");
                // customGbn_printMsg("sql=" . customGbn_getSql($sql, []));
            } else {
                customGbn_printWarning("No insert in gibbonFinanceFee OK");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            customGbn_printErr("sql=" . customGbn_getSql($sql, []));
            die();
        }
    } else {
        customGbn_printErr("Empties values: gibbonSchoolYearID, gibbonFinanceFeeCategoryID");
    }

    /**
     * gibbonFinanceBillingSchedule
     */
    if (! empty($gibbonSchoolYearID) && ! empty($schoolYear1) && ! empty($schoolYear2)) {
        try {
            $sql2 = '';
            $sql1 = "SELECT * FROM gibbonFinanceBillingSchedule WHERE gibbonSchoolYearID=$gibbonSchoolYearID AND name=";

            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE001);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - toute l'année");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= "($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear1-09-01', '$schoolYear1-10-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE002);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de septembre");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear1-09-01', '$schoolYear1-09-30', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE003);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de octobre");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear1-10-01', '$schoolYear1-10-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE004);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de novembre");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear1-11-01', '$schoolYear1-11-30', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE005);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de décembre");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear1-12-01', '$schoolYear1-12-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE006);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de janvier");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-01-01', '$schoolYear2-01-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE007);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de février");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-02-01', '$schoolYear2-02-28', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE008);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de mars");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-03-01', '$schoolYear2-03-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE009);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de avril");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-04-01', '$schoolYear2-04-30', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE010);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de mai");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-05-01', '$schoolYear2-05-31', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            $feeName = customGbn_quote($connection2, CO_FINANCE_BILLING_SCHEDULE011);
            $feeDesc = customGbn_quote($connection2, "Frais de scolarité - mois de juin");
            if (! customGbn_isExistBySql($pdo, "$sql1$feeName")) {
                $sql2 .= " ($gibbonSchoolYearID, $feeName, $feeDesc, 'Y', '$schoolYear2-06-01', '$schoolYear2-06-30', 0000000001, NOW(), NULL, NULL),";
                customGbn_printMsg("$feeName : not existing, will created..!");
            } else {
                customGbn_printWarning("$feeName : exists..!");
            }
            if (! empty($sql2)) {
                $sql = "INSERT INTO `gibbonFinanceBillingSchedule` (`gibbonSchoolYearID`, `name`, `description`, `active`, `invoiceIssueDate`, `invoiceDueDate`, `gibbonPersonIDCreator`, `timestampCreator`, `gibbonPersonIDUpdate`, `timestampUpdate`) VALUES";
                $sql .= substr_replace($sql2, "", - 1);
                $pdo->executeQuery([], $sql);
                customGbn_printMsg("insert OK in gibbonFinanceBillingSchedule");
                // customGbn_printMsg("sql=" . customGbn_getSql($sql, []));
            } else {
                customGbn_printWarning("No insert in gibbonFinanceBillingSchedule OK");
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            customGbn_printErr("sql=" . customGbn_getSql($sql, []));
            die();
        }
    } else {
        customGbn_printErr("Empties values: gibbonSchoolYearID, schoolYear1, schoolYear2");
    }

    /**
     * gibbonCountry
     */
    $data = [
        'printable_name' => customGbn_quote($connection2, "Inconnu"),
        'iddCountryCode' => ''
    ];
    if (! customGbn_isExistBySql($pdo, "SELECT * FROM gibbonCountry WHERE printable_name=:printable_name AND iddCountryCode=:iddCountryCode", $data)) {
        try {
            $sql = "INSERT INTO `gibbonCountry` (`printable_name`, `iddCountryCode`) VALUES (:printable_name, :iddCountryCode)";
            $result = $connection2->prepare($sql);
            $result->execute($data);
            customGbn_printMsg("insert OK: gibbonCountry");
        } catch (PDOException $e) {
            echo customGbn_getSql($sql, $data);
            echo '</br>';
            echo $e->getMessage();
            die();
        }
    } else {
        customGbn_printWarning("No insert in gibbonCountry OK");
    }

    /**
     * gibbonAction
     */
    $module = customGbn_getModuleByName($pdo, 'Finance');
    if (! empty($module)) {
        $data = [
            'gibbonModuleID' => $module['gibbonModuleID'],
            'name' => 'Manage Recurring Invoices',
            'description' => 'Allows users to add recurring invoices.',
            'category' => 'Billing',
            'entryURL' => 'custom_invoices_manage.php',
            'URLList' => 'custom_invoices_manage.php,custom_invoices_manage_add.php,custom_invoices_manage_edit.php'
        ];
        if (! customGbn_isExistBySql($pdo, "SELECT * FROM gibbonAction WHERE gibbonModuleID=:gibbonModuleID AND name=:name AND description=:description AND category=:category AND entryURL=:entryURL AND URLList=:URLList", $data)) {
            try {
                $sql = "INSERT INTO `gibbonAction` (`gibbonActionID`, `gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `entrySidebar`, `menuShow`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES (NULL, :gibbonModuleID, :name, '0', :category, :description, :URLList, :entryURL, 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N')";
                $result = $connection2->prepare($sql);
                $result->execute($data);
                $gibbonActionID = $connection2->lastInsertId();
                customGbn_printMsg("insert OK: gibbonAction:Finance-->custom_invoices_manage.php. gibbonActionID=$gibbonActionID");
                if (! empty($gibbonActionID)) {
                    $sql = "INSERT INTO `gibbonPermission` (`permissionID`, `gibbonRoleID`, `gibbonActionID`) VALUES (NULL, '001', '$gibbonActionID')";
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                    customGbn_printMsg("insert OK: gibbonPermission:Finance-->custom_invoices_manage.php");
                } else {
                    customGbn_printErr("No insert in gibbonPermission NOT OK");
                }
            } catch (PDOException $e) {
                echo customGbn_getSql($sql, $data);
                echo '</br>';
                echo $e->getMessage();
                die();
            }
        } else {
            customGbn_printWarning("No insert in gibbonAction OK");
        }
    }

    // INSERT INTO `gibbonCountry` (`printable_name`, `iddCountryCode`) VALUES ('Inconnu', '');
    // if ($result && $result->rowCount() > 0) {
    // while ($row = $result->fetch()) {
    // // TODO
    // }
    // }
    // This is where you can start writing code for your module.
    // See the developer docs for more info: https://docs.gibbonedu.org/developers/
}