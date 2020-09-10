<?php
/*
 * Created on 5 sept. 2020 by rezki
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
if (! empty($_GET['postsParams'])) {
    $postsParams = json_decode(urldecode($_GET['postsParams']));
    foreach ($postsParams as $key => $value) {
        $_POST[$key] = $value;
    }
}

// check if file exist
If (isset($_GET['q'])) {
    $root = dirname(dirname(dirname(__FILE__)));
    if (! is_file($root. urldecode($_GET['q']))) {
        $url = '?';
        $url .= 'q=' . str_replace('LCI-Ronse', 'Students', urldecode($_GET['q']));
        $url .= '&' . customGbn_getPostsParams();
        header("Location: {$url}");
    }
}

/**
 * redirect: q=/modules/Students/applicationForm.php to q=/modules/LCI-Ronse/applicationForm_custom.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/applicationForm.php') {
    // var_dump($_POST);
    // $_POST['applicationType'];
    $url = '?';
    $url .= 'q=/modules/LCI-Ronse/applicationForm_custom.php';
    $url .= '&' . customGbn_getPostsParams();
    header("Location: {$url}");
}

/**
 * redirect: q=/modules/Students/applicationForm_manage_accept.php to q=/modules/LCI-Ronse/applicationForm_manage_accept.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/applicationForm_manage_accept.php') {
    $url = '?';
    $url .= 'q=/modules/LCI-Ronse/applicationForm_manage_accept.php';
    $url .= '&gibbonApplicationFormID=' . $_GET['gibbonApplicationFormID'];
    $url .= '&gibbonSchoolYearID=' . $_GET['gibbonSchoolYearID'];
    $url .= '&search=' . $_GET['search'];
    header("Location: {$url}");
}

/**
 * redirect: q=/modules/Students/applicationForm_manage_edit.php to q=/modules/LCI-Ronse/applicationForm_manage_edit.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/applicationForm_manage_edit.php') {
    $url = '?';
    $url .= 'q=/modules/LCI-Ronse/applicationForm_manage_edit.php';
    $url .= '&gibbonApplicationFormID=' . $_GET['gibbonApplicationFormID'];
    $url .= '&gibbonSchoolYearID=' . $_GET['gibbonSchoolYearID'];
    $url .= '&search=' . $_GET['search'];
    header("Location: {$url}");
}

/**
 * redirect: q=/modules/Students/studentEnrolment_manage_add.php to q=/modules/LCI-Ronse/studentEnrolment_manage_add.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/studentEnrolment_manage_add.php') {
    $url = '?';
    $url .= 'q=/modules/LCI-Ronse/studentEnrolment_manage_add.php';
    $url .= '&gibbonSchoolYearID=' . $_GET['gibbonSchoolYearID'];
    $url .= '&search=' . $_GET['search'];
    header("Location: {$url}");
}

/**
 * redirect: q=/modules/Students/studentEnrolment_manage_edit.php to q=/modules/LCI-Ronse/studentEnrolment_manage_edit.php
 */
if (isset($_GET['q']) && urldecode($_GET['q']) == '/modules/Students/studentEnrolment_manage_edit.php') {
    $url = '?';
    $url .= 'q=/modules/LCI-Ronse/studentEnrolment_manage_edit.php';
    $url .= '&gibbonStudentEnrolmentID=' . $_GET['gibbonStudentEnrolmentID'];
    $url .= '&gibbonSchoolYearID=' . $_GET['gibbonSchoolYearID'];
    $url .= '&search=' . $_GET['search'];
    header("Location: {$url}");
}
