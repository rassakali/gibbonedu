<?php
/*
 * Gibbon, Flexible & Open School System
 * Copyright (C) 2010, Ross Parker
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Gibbon\Module\LCIRonse\Forms;

use Gibbon\Contracts\Database\Connection;
use Gibbon\Module\Finance\Forms\FinanceFormFactory;

/**
 * FinanceFormFactory
 *
 * @version v16
 * @since v16
 */
class CustomFinanceFormFactory extends FinanceFormFactory
{

    /**
     * Create and return an instance of DatabaseFormFactory.
     *
     * @return object DatabaseFormFactory
     */
    public static function create(Connection $pdo = null)
    {
        return new CustomFinanceFormFactory($pdo);
    }

    public function createSelectInvoicee($name, $gibbonSchoolYearID = '', $params = array())
    {
        // Check params and set defaults if not defined
        $params = array_replace(array(
            'allStudents' => false
        ), $params);

        $values = array();

        // Opt Groups
        if ($params['allStudents'] != true) {
            $byRollGroup = __('All Enrolled Students by Roll Group');
            $byName = __('All Enrolled Students by Alphabet');
        } else {
            $byRollGroup = __('All Students by Roll Group');
            $byName = __('All Students by Alphabet');
        }

        $data = array(
            'gibbonSchoolYearID' => $gibbonSchoolYearID
        );
        if ($params['allStudents'] != true) {
            $sql = "SELECT gibbonFinanceInvoicee.gibbonFinanceInvoiceeID, preferredName, surname, gibbonRollGroup.nameShort AS rollGroupName, dayType,
                GROUP_CONCAT(DISTINCT gibbonFinanceFee.nameShort ORDER BY gibbonFinanceFee.nameShort ASC) invoiced
                FROM gibbonPerson
                JOIN gibbonStudentEnrolment ON (gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID)
                JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID)
                JOIN gibbonFinanceInvoicee ON (gibbonFinanceInvoicee.gibbonPersonID=gibbonPerson.gibbonPersonID)
                LEFT JOIN gibbonFinanceInvoice ON gibbonFinanceInvoice.gibbonFinanceInvoiceeID=gibbonFinanceInvoicee.gibbonFinanceInvoiceeID
                LEFT JOIN gibbonFinanceInvoiceFee ON gibbonFinanceInvoiceFee.gibbonFinanceInvoiceID=gibbonFinanceInvoice.gibbonFinanceInvoiceID
                LEFT JOIN gibbonFinanceFee ON (gibbonFinanceFee.gibbonFinanceFeeID=gibbonFinanceInvoiceFee.gibbonFinanceFeeID AND gibbonFinanceFee.name in (SELECT name FROM gibbonFinanceFee WHERE gibbonFinanceFeeCategoryID IN (SELECT gibbonFinanceFeeCategoryID FROM gibbonFinanceFeeCategory WHERE name='RECURRING_FEE') ORDER BY gibbonFinanceFee.name ASC))
                WHERE gibbonPerson.status='Full'                
                AND gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID
                GROUP BY gibbonFinanceInvoicee.gibbonFinanceInvoiceeID
                ORDER BY gibbonRollGroup.name, surname, preferredName";
        } else {
            $sql = "SELECT gibbonFinanceInvoiceeID, preferredName, surname, gibbonRollGroup.nameShort AS rollGroupName, dayType
                FROM gibbonPerson
                JOIN gibbonStudentEnrolment ON (gibbonPerson.gibbonPersonID=gibbonStudentEnrolment.gibbonPersonID)
                JOIN gibbonRollGroup ON (gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID)
                JOIN gibbonFinanceInvoicee ON (gibbonFinanceInvoicee.gibbonPersonID=gibbonPerson.gibbonPersonID)
                WHERE gibbonStudentEnrolment.gibbonSchoolYearID=:gibbonSchoolYearID
                ORDER BY gibbonRollGroup.name, surname, preferredName";
        }

        $results = $this->pdo->executeQuery($data, $sql);
        $students = ($results->rowCount() > 0) ? $results->fetchAll() : array();

        // Add students by Roll Group and Name
        foreach ($students as $student) {
            $fullName = formatName('', $student['preferredName'], $student['surname'], 'Student', true);

            $values[$byRollGroup][$student['gibbonFinanceInvoiceeID']] = $student['rollGroupName'] . ' - ' . $fullName;
            $tmp = '';
            if (! empty($student['invoiced'])) {
                $invoicedMonths = explode(',', $student['invoiced']);
                $tmp .= ' (';
                foreach ($invoicedMonths as $invoicedMonth) {
                    if (isset(CO_FINANCE_FEE_NAMESHORT[$invoicedMonth])) {
                        $tmp .= CO_FINANCE_FEE_NAMESHORT[$invoicedMonth] . ',';
                    }
                }
                $tmp .= ')';
            }
            $values[$byRollGroup][$student['gibbonFinanceInvoiceeID']] .= $tmp;
            // $values[$byName][$student['gibbonFinanceInvoiceeID']] = $fullName.' - '.$student['rollGroupName'];
        }

        // Sort the byName list so it's not byRollGroup
        if (! empty($values[$byName]) && is_array($values[$byName])) {
            asort($values[$byName]);
        }

        // Add students by Day Type (optionally)
        $dayTypeOptions = getSettingByScope($this->pdo->getConnection(), 'User Admin', 'dayTypeOptions');
        if (! empty($dayTypeOptions)) {
            $dayTypes = explode(',', $dayTypeOptions);

            foreach ($students as $student) {
                if (empty($student['dayType']) || ! in_array($student['dayType'], $dayTypes))
                    continue;

                $byDayType = $student['dayType'] . ' ' . __('Students by Roll Groups');
                $fullName = formatName('', $student['preferredName'], $student['surname'], 'Student', true);

                $values[$byDayType][$student['gibbonFinanceInvoiceeID']] = $student['rollGroupName'] . ' - ' . $fullName;
            }
        }

        return $this->createSelect($name)
            ->fromArray($values)
            ->placeholder();
    }

    public function createSelectBillingSchedule($name, $gibbonSchoolYearID)
    {
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE001;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE002;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE003;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE004;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE005;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE006;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE007;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE008;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE009;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE010;
        $allowedBillingSchedule[] = CO_FINANCE_BILLING_SCHEDULE011;

        $data = array(
            'gibbonSchoolYearID' => $gibbonSchoolYearID
        );
        $sql = "SELECT gibbonFinanceBillingScheduleID as value, CONCAT(name,' - ',description) name FROM gibbonFinanceBillingSchedule
                WHERE gibbonSchoolYearID=:gibbonSchoolYearID AND name IN ('" . implode("','", $allowedBillingSchedule) . "') ORDER BY name";

        return $this->createSelect($name)
            ->fromQuery($this->pdo, $sql, $data)
            ->placeholder();
    }

    public function createSelectFee($name, $gibbonSchoolYearID)
    {
        $data = array(
            'gibbonSchoolYearID' => $gibbonSchoolYearID
        );
        $sql = "SELECT gibbonFinanceFeeCategory.name as groupBy, gibbonFinanceFee.gibbonFinanceFeeID as value, CONCAT(gibbonFinanceFee.name,' - ',gibbonFinanceFee.description) name 
                FROM gibbonFinanceFee 
                JOIN gibbonFinanceFeeCategory ON (gibbonFinanceFee.gibbonFinanceFeeCategoryID=gibbonFinanceFeeCategory.gibbonFinanceFeeCategoryID) 
                WHERE gibbonFinanceFee.gibbonSchoolYearID=:gibbonSchoolYearID AND gibbonFinanceFee.gibbonFinanceFeeCategoryID IN (SELECT gibbonFinanceFeeCategoryID FROM gibbonFinanceFeeCategory WHERE name='RECURRING_FEE')
                ORDER BY gibbonFinanceFeeCategory.name, gibbonFinanceFee.name";

        return $this->createSelect($name)
            ->fromArray(array(
            '' => __('Choose a fee to add it')
        ))
            ->fromQuery($this->pdo, $sql, $data, 'groupBy');
    }
}
