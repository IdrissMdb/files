<?php

/**
* ownCloud - ajax frontend
*
* @author Robin Appelman
* @copyright 2010 Robin Appelman icewind1991@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/


// Init owncloud
 

OCP\User::checkAdminUser();

$htaccessWorking=(getenv('htaccessWorking')=='true');

$upload_max_filesize = OCP\Util::computerFileSize(ini_get('upload_max_filesize'));
$post_max_size = OCP\Util::computerFileSize(ini_get('post_max_size'));
$maxUploadFilesize = OCP\Util::humanFileSize(min($upload_max_filesize, $post_max_size));
if($_POST) {
	if(isset($_POST['maxUploadSize'])){
		if(($setMaxSize = OC_Files::setUploadLimit(OCP\Util::computerFileSize($_POST['maxUploadSize']))) !== false) {
			$maxUploadFilesize = OCP\Util::humanFileSize($setMaxSize);
		}
	}
	if(isset($_POST['maxZipInputSize'])) {
		$maxZipInputSize=$_POST['maxZipInputSize'];
		OC_Config::setValue('maxZipInputSize', OCP\Util::computerFileSize($maxZipInputSize));
	}
	if(isset($_POST['submitFilesAdminSettings'])) {
		OC_Config::setValue('allowZipDownload', isset($_POST['allowZipDownload']));
	}
}
$maxZipInputSize = OCP\Util::humanFileSize(OC_Config::getValue('maxZipInputSize', OCP\Util::computerFileSize('800 MB')));
$allowZipDownload = intval(OC_Config::getValue('allowZipDownload', true));

OC_App::setActiveNavigationEntry( "files_administration" );

$tmpl = new OC_Template( 'files', 'admin' );
$tmpl->assign( 'htaccessWorking', $htaccessWorking );
$tmpl->assign( 'uploadMaxFilesize', $maxUploadFilesize);
$tmpl->assign( 'maxPossibleUploadSize', OCP\Util::humanFileSize(PHP_INT_MAX));
$tmpl->assign( 'allowZipDownload', $allowZipDownload);
$tmpl->assign( 'maxZipInputSize', $maxZipInputSize);
return $tmpl->fetchPage();