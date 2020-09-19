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
function customGbn_getSql($string, $data)
{
    $indexed = $data == array_values($data);
    $total = count($data);
    $i = 1;
    foreach ($data as $k => $v) {
        $v = utf8_encode($v);
        $v = "'$v'";
        if ($indexed)
            $string = preg_replace('/\?/', $v, $string, 1);
        else {
            var_dump(":$k", "$v", $string);
            if ($i == $total) {
                $string = str_replace(":$k", "$v", $string);
            } elseif (stristr($string, ":$k,")) {
                $string = str_replace(":$k,", "$v,", $string);
            } else {
                $string = str_replace(":$k", "$v", $string);
            }
        }
        $i ++;
    }
    return $string;
}

function customGbn_getPostsParams()
{
    if (! empty($_POST))
        return '&postsParams=' . urlencode(json_encode($_POST));
    return '';
}

function customGbn_getSqlStudentsCanEnrolment()
{
    $sql = "SELECT gibbonPerson.gibbonPersonID as value, CONCAT(gibbonPerson.surname, ' ', gibbonPerson.preferredName,', ', gibbonPerson.nameInCharacters) AS name
            FROM gibbonPerson
            JOIN gibbonRole ON (gibbonPerson.gibbonRoleIDPrimary=gibbonRole.gibbonRoleID)
            WHERE gibbonRole.category='Student'";
    $sql .= " AND  gibbonPerson.status <> 'Left'";
    $sql .= " AND gibbonPerson.gibbonPersonID NOT IN (SELECT gibbonPersonID FROM gibbonStudentEnrolment WHERE gibbonSchoolYearID=:gibbonSchoolYearID)";
    $sql .= " ORDER BY surname, preferredName";
    return $sql;
}

function customGbn_getSqlRollGroup()
{
    $sql = "SELECT gibbonRollGroupID as value, CONCAT(name,' (',(SELECT count(*) FROM gibbonStudentEnrolment WHERE gibbonStudentEnrolment.gibbonRollGroupID=gibbonRollGroup.gibbonRollGroupID),')') as name FROM gibbonRollGroup WHERE gibbonSchoolYearID=:gibbonSchoolYearID ORDER BY LENGTH(name), name";
    return $sql;
}

function customGbn_isExistBySql($pdo, $sql, $data = [])
{
    $result = $pdo->executeQuery($data, $sql);
    if ($result->rowCount() > 0) {
        return true;
    }
    return false;
}

function customGbn_printMsg($msg)
{
    print $msg . '<br/>';
}

function customGbn_printErr($msg)
{
    $msg = '<span style="color:red;">' . $msg . '</span>';
    print $msg . '<br/>';
}

function customGbn_printWarning($msg)
{
    $msg = '<span style="color:orange;">' . $msg . '</span>';
    print $msg . '<br/>';
}

function customGbn_quote($connection2, $str)
{
    //$str = utf8_encode($str);
    $str = $connection2->quote($str);
    return $str;
}

function customGbn_getModuleByName($pdo, $name)
{
    $row = false;
    $result = $pdo->executeQuery([], "SELECT * FROM gibbonModule WHERE name='$name'");
    if ($result && $result->rowCount() == 1) {
        $row = $result->fetch();
    }
    return $row;
}