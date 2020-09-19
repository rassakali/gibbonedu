<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Module Manifest
 *
 * This file describes the module, including database tables, settings, actions and hooks.
 */

/**
 * Basic variables
 */
$name        = "LCI-Ronse";            // The name of the variable as it appears to users. Needs to be unique to installation. Also the name of the folder that holds the unit.
$description = "Customization for LCI Ronse";            // Short text description
$entryURL    = "index.php";   // The landing page for the unit, used in the main menu
$type        = "Additional";  // Do not change.
$category    = "Other";            // The main menu area to place the module in
$version     = "0.2";            // Version number
$author      = "Rezki ASSAKALI";            // Your name
$url         = "";            // Your URL

/**
 * Module Tables
 *
 * One array entry for every database table you need to create. Convention dictates that you preface the table name with the module name, to keep the db neat.
 * Other sql can be run, but resulting data will not be cleaned up on uninstall.
 */
$moduleTables[] = "";

/**
 * Settings
 *
 * One array entry for every gibbonSetting entry you need to create. The scope field for the setting should be your module name.
 */
$gibbonSetting[] = "";

/**
 * Action rows
 *
 * One array per action.
 */
$actionRows[] = [
    'name'         => "Application Form",   // The name of the action (appears to user in the right hand side module menu)
    'precedence'   => "0",  // If it is a grouped action, the precedence controls which is highest action in group
    'category'     => "Admissions",   // Optional: subgroups for the right hand side module menu
    'description'  => "LCI-Ronse - Application Form",   // Text description
    'URLList'      => "applicationForm_custom.php",   // List of pages included in this action
    'entryURL'     => "applicationForm_custom.php",   // The landing action for the page
    'entrySidebar' => "Y",  // Will the page have a sidebar? Set this to N for fullscreen
    'menuShow'     => "Y",  // Does the page display in the module menu?
    'defaultPermissionAdmin'    => "Y",   // Default permission for built in role Admin
    'defaultPermissionTeacher'  => "Y",   // Default permission for built in role Teacher
    'defaultPermissionStudent'  => "Y",   // Default permission for built in role Student
    'defaultPermissionParent'   => "Y",   // Default permission for built in role Parent
    'defaultPermissionSupport'  => "Y",   // Default permission for built in role Support
    'categoryPermissionStaff'   => "Y",   // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => "Y",   // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => "Y",   // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => "Y",   // Should this action be available to user roles in the Other category?
];
$actionRows[] = [
    'name'         => "Manage Applications",   // The name of the action (appears to user in the right hand side module menu)
    'precedence'   => "0",  // If it is a grouped action, the precedence controls which is highest action in group
    'category'     => "Admissions",   // Optional: subgroups for the right hand side module menu
    'description'  => "LCI-Ronse - Manage Applications",   // Text description
    'URLList'      => "applicationForm_manage.php",   // List of pages included in this action
    'entryURL'     => "applicationForm_manage.php",   // The landing action for the page
    'entrySidebar' => "Y",  // Will the page have a sidebar? Set this to N for fullscreen
    'menuShow'     => "Y",  // Does the page display in the module menu?
    'defaultPermissionAdmin'    => "Y",   // Default permission for built in role Admin
    'defaultPermissionTeacher'  => "Y",   // Default permission for built in role Teacher
    'defaultPermissionStudent'  => "Y",   // Default permission for built in role Student
    'defaultPermissionParent'   => "Y",   // Default permission for built in role Parent
    'defaultPermissionSupport'  => "Y",   // Default permission for built in role Support
    'categoryPermissionStaff'   => "Y",   // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => "Y",   // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => "Y",   // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => "Y",   // Should this action be available to user roles in the Other category?
];
$actionRows[] = [
    'name'         => "Student Enrolment",   // The name of the action (appears to user in the right hand side module menu)
    'precedence'   => "0",  // If it is a grouped action, the precedence controls which is highest action in group
    'category'     => "Admissions",   // Optional: subgroups for the right hand side module menu
    'description'  => "LCI-Ronse - Student Enrolment",   // Text description
    'URLList'      => "studentEnrolment_manage.php",   // List of pages included in this action
    'entryURL'     => "studentEnrolment_manage.php",   // The landing action for the page
    'entrySidebar' => "Y",  // Will the page have a sidebar? Set this to N for fullscreen
    'menuShow'     => "Y",  // Does the page display in the module menu?
    'defaultPermissionAdmin'    => "Y",   // Default permission for built in role Admin
    'defaultPermissionTeacher'  => "Y",   // Default permission for built in role Teacher
    'defaultPermissionStudent'  => "Y",   // Default permission for built in role Student
    'defaultPermissionParent'   => "Y",   // Default permission for built in role Parent
    'defaultPermissionSupport'  => "Y",   // Default permission for built in role Support
    'categoryPermissionStaff'   => "Y",   // Should this action be available to user roles in the Staff category?
    'categoryPermissionStudent' => "Y",   // Should this action be available to user roles in the Student category?
    'categoryPermissionParent'  => "Y",   // Should this action be available to user roles in the Parent category?
    'categoryPermissionOther'   => "Y",   // Should this action be available to user roles in the Other category?
];

/**
 * Hooks
 *
 * Serialised array to create hook and set options. See Hooks documentation online.
 */
$hooks[] = "";
