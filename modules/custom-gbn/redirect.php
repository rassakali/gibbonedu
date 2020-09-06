<?php
/*
 * Created on 5 sept. 2020 by rezki
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * redirect: q=/modules/Students/applicationForm_manage.php to q=/modules/custom-gbn/applicationForm_manage.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/applicationForm_manage_accept.php') {
    $url = '?';
    $url .= 'q=/modules/custom-gbn/applicationForm_manage_accept.php';
    $url .= '&gibbonApplicationFormID=' . $_GET['gibbonApplicationFormID'];
    $url .= '&gibbonSchoolYearID=' . $_GET['gibbonSchoolYearID'];
    $url .= '&search=' . $_GET['search'];
    header("Location: {$url}");
}
