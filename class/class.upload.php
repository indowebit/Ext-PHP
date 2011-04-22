<?php
// +------------------------------------------------------------------------+
// | class.upload.php                                                       |
// +------------------------------------------------------------------------+
// | Copyright (c) Colin Verot 2003-2006. All rights reserved.              |
// | Version       0.21                                                     |
// | Last modified 30/09/2006                                               |
// | Email         colin@verot.net                                          |
// | Web           http://www.verot.net                                     |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License version 2 as      |
// | published by the Free Software Foundation.                             |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License      |
// | along with this program; if not, write to the                          |
// |   Free Software Foundation, Inc., 59 Temple Place, Suite 330,          |
// |   Boston, MA 02111-1307 USA                                            |
// |                                                                        |
// | Please give credit on sites that use class.upload and submit changes   |
// | of the script so other people can use them as well.                    |
// | This script is free to use, don't abuse.                               |
// +------------------------------------------------------------------------+
//

/**
 * Class upload
 *
 * @version   0.21
 * @author    Colin Verot <colin@verot.net>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Colin Verot
 * @package   cmf
 * @subpackage external
 */

/**
 * Class upload
 *
 * <b>What does it do?</b>
 *
 * It manages file uploads for you. In short, it manages the uploaded file,
 * and allows you to do whatever you want with the file, especially if it
 * is an image, and as many times as you want.
 *
 * It is the ideal class to quickly integrate file upload in your site.
 * If the file is an image, you can convert, resize, crop it in many ways.
 * You can also apply filters, add borders, text, watermarks, etc...
 * That's all you need for a gallery script for instance.
 *
 * You can also use the class to work on local files, which is especially
 * useful to use the image manipulation features
 *
 * <b>How does it work?</b>
 *
 * You instanciate the class with the $_FILES['my_field'] array
 * where my_field is the field name from your upload form.
 * The class will check if the original file has been uploaded
 * to its temporary location (alternatively, you can instanciate
 * the class with a local filename).
 *
 * You can then set a number of processing variables to act on the file.
 * For instance, you can rename the file, and if it is an image,
 * convert and resize it in many ways.
 * You can also set what will the class do if the file already exists.
 *
 * Then you call the function {@link process} to actually perform the actions
 * according to the processing parameters you set above.
 * It will create new instances of the original file,
 * so the original file remains the same between each process.
 * The file will be manipulated, and copied to the given location.
 * The processing variables will be reseted once it is done.
 *
 * You can repeat setting up a new set of processing variables,
 * and calling {@link process} again as many times as you want.
 * When you have finished, you can call {@link clean} to delete
 * the original uploaded file.
 *
 * If you don't set any processing parameters and call {@link process}
 * just after instanciating the class. The uploaded file will be simply
 * copied to the given location without any alteration or checks.
 *
 * Don't forget to add <i>enctype="multipart/form-data"</i> in your form
 * tag <form> if you want your form to upload the file.
 *
 * <b>How to use it?</b><br>
 * Create a simple HTML file, with a form such as:
 * <pre>
 * <form enctype="multipart/form-data" method="post" action="upload.php">
 *   <input type="file" size="32" name="image_field" value="">
 *   <input type="submit" name="Submit" value="upload">
 * </form>
 * </pre>
 * Create a file called upload.php:
 * <pre>
 *  $MyObject = new upload($_FILES['image_field']);
 *  if ($MyObject->uploaded) {
 *      $MyObject->file_new_name_body   = 'image_resized';
 *      $MyObject->image_resize         = true;
 *      $MyObject->image_x              = 100;
 *      $MyObject->image_ratio_y        = true;
 *      $MyObject->process('/home/user/files/');
 *      if ($MyObject->processed) {
 *          echo 'image resized';
 *          $MyObject->clean();
 *      } else {
 *          echo 'error : ' . $MyObject->error;
 *      }
 *  }
 * </pre>
 *
 * <b>Processing parameters</b> (reseted after each process)
 * <ul>
 *  <li><b>file_new_name_body</b> replaces the name body (default: '')<br>
 *  <pre>$MyObject->file_new_name_body = 'new name';</pre></li>
 *  <li><b>file_name_body_add</b> appends to the name body (default: '')<br>
 *  <pre>$MyObject->file_name_body_add = '_uploaded';</pre></li>
 *  <li><b>file_new_name_ext</b> replaces the file extension (default: '')<br>
 *  <pre>$MyObject->file_new_name_ext = 'txt';</pre></li>
 *  <li><b>file_safe_name</b> formats the filename (spaces changed to _) (default: true)<br>
 *  <pre>$MyObject->file_safe_name = true;</pre></li>
 *  <li><b>file_overwrite</b> sets behaviour if file already exists (default: false)<br>
 *  <pre>$MyObject->file_overwrite = true;</pre></li>
 *  <li><b>file_auto_rename</b> automatically renames file if it already exists (default: true)<br>
 *  <pre>$MyObject->file_auto_rename = true;</pre></li>
 *  <li><b>auto_create_dir</b> automatically creates destination directory if missing (default: true)<br>
 *  <pre>$MyObject->auto_create_dir = true;</pre></li>
 *  <li><b>dir_auto_chmod</b> automatically attempts to chmod the destination directory if not writeable (default: true)<br>
 *  <pre>$MyObject->dir_auto_chmod = true;</pre></li>
 *  <li><b>dir_chmod</b> chmod used when creating directory or if directory not writeable (default: 0777)<br>
 *  <pre>$MyObject->dir_chmod = 0777;</pre></li>
 *  <li><b>file_max_size</b> sets maximum upload size (default: upload_max_filesize from php.ini)<br>
 *  <pre>$MyObject->file_max_size = '1024'; // 1KB</pre></li>
 *  <li><b>mime_check</b> sets if the class check the MIME against the {@link allowed} list (default: true)<br>
 *  <pre>$MyObject->mime_check = false;</pre></li>
 *  <li><b>mime_magic_check</b> sets if the class uses mime_magic (default: false)<br>
 *  <pre>$MyObject->mime_magic_check = true;</pre></li>
 *  <li><b>no_script</b> sets if the class turns scripts into test files (default: true)<br>
 *  <pre>$MyObject->no_script = false;</pre></li>
 *  <li><b>allowed</b> array of allowed mime-types (default: check {@link Init})<br>
 *  <pre>$MyObject->allowed = array('application/pdf','application/msword');</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_convert</b> if set, image will be converted (possible values : ''|'png'|'jpeg'|'gif'; default: '')<br>
 *  <pre>$MyObject->image_convert = 'jpg';</pre></li>
 *  <li><b>jpeg_quality</b> sets the compression quality for JPEG images (default: 75)<br>
 *  <pre>$MyObject->jpeg_quality = 50;</pre></li>
 *  <li><b>jpeg_size</b> if set to a size in bytes, will approximate {@link jpeg_quality} so the output image fits within the size (default: NULL)<br>
 *  <pre>$MyObject->jpeg_size = 3072;</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_resize</b> determines is an image will be resized (default: false)<br>
 *  <pre>$MyObject->image_resize = true;</pre></li>
 * </ul>
 *  The following variables are used only if {@link image_resize} == true
 * <ul>
 *  <li><b>image_x</b> destination image width (default: 150)<br>
 *  <pre>$MyObject->image_x = 100;</pre></li>
 *  <li><b>image_y</b> destination image height (default: 150)<br>
 *  <pre>$MyObject->image_y = 200;</pre></li>
 * </ul>
 *  Use either one of the following
 * <ul>
 *  <li><b>image_ratio</b> if true, resize image conserving the original sizes ratio, using {@link image_x} AND {@link image_y} as max sizes if true (default: false)<br>
 *  <pre>$MyObject->image_ratio = true;</pre></li>
 *  <li><b>image_ratio_crop</b> if true, resize image conserving the original sizes ratio, using {@link image_x} AND {@link image_y} as max sizes, and cropping excedent to fill the space if true (default: false)<br>
 *  <pre>$MyObject->image_ratio = true;</pre></li>
 *  <li><b>image_ratio_no_zoom_in</b> same as {@link image_ratio}, but won't resize if the source image is smaller than {@link image_x} x {@link image_y} (default: false)<br>
 *  <pre>$MyObject->image_ratio_no_zoom_in = true;</pre></li>
 *  <li><b>image_ratio_no_zoom_out</b> same as {@link image_ratio}, but won't resize if the source image is bigger than {@link image_x} x {@link image_y} (default: false)<br>
 *  <pre>$MyObject->image_ratio_no_zoom_out = true;</pre></li>
 *  <li><b>image_ratio_x</b> if true, resize image, calculating {@link image_x} from {@link image_y} and conserving the original sizes ratio (default: false)<br>
 *  <pre>$MyObject->image_ratio_x = true;</pre></li>
 *  <li><b>image_ratio_y</b> if true, resize image, calculating {@link image_y} from {@link image_x} and conserving the original sizes ratio (default: false)<br>
 *  <pre>$MyObject->image_ratio_y = true;</pre></li>
 * </ul>
 *  The following image manipulations require GD2+
 * <ul>
 *  <li><b>image_brightness</b> if set, corrects the brightness. value between -127 and 127 (default: NULL)<br>
 *  <pre>$MyObject->image_brightness = 40;</pre></li>
 *  <li><b>image_contrast</b> if set, corrects the contrast. value between -127 and 127 (default: NULL)<br>
 *  <pre>$MyObject->image_contrast = 50;</pre></li>
 *  <li><b>image_tint_color</b> if set, will tint the image with a color, value as hexadecimal #FFFFFF (default: NULL)<br>
 *  <pre>$MyObject->image_tint_color = '#FF0000';</pre></li>
 *  <li><b>image_overlay_color</b> if set, will add a colored overlay, value as hexadecimal #FFFFFF (default: NULL)<br>
 *  <pre>$MyObject->image_overlay_color = '#FF0000';</pre></li>
 *  <li><b>image_overlay_percent</b> used when {@link image_overlay_color} is set, determines the opacity (default: 50)<br>
 *  <pre>$MyObject->image_overlay_percent = 20;</pre></li>
 *  <li><b>image_negative</b> inverts the colors in the image (default: false)<br>
 *  <pre>$MyObject->image_negative = true;</pre></li>
 *  <li><b>image_greyscale</b> transforms an image into greyscale (default: false)<br>
 *  <pre>$MyObject->image_greyscale = true;</pre></li>
 *  <li><b>image_threshold</b> applies a threshold filter. value between -127 and 127 (default: NULL)<br>
 *  <pre>$MyObject->image_threshold = 20;</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_text</b> creates a text label on the image, value is a string (default: NULL)<br>
 *  <pre>$MyObject->image_text = 'test';</pre></li>
 *  <li><b>image_text_direction</b> text label direction, either 'h' horizontal or 'v' vertical (default: 'h')<br>
 *  <pre>$MyObject->image_text_direction = 'v';</pre></li>
 *  <li><b>image_text_color</b> text color for the text label, in hexadecimal (default: #FFFFFF)<br>
 *  <pre>$MyObject->image_text_color = '#FF0000';</pre></li>
 *  <li><b>image_text_percent</b> text opacity on the text label, integer between 0 and 100 (default: 100)<br>
 *  <pre>$MyObject->image_text_percent = 50;</pre></li>
 *  <li><b>image_text_background</b> text label background color, in hexadecimal (default: NULL)<br>
 *  <pre>$MyObject->image_text_background = '#FFFFFF';</pre></li>
 *  <li><b>image_text_background_percent</b> text label background opacity, integer between 0 and 100 (default: 100)<br>
 *  <pre>$MyObject->image_text_background_percent = 50;</pre></li>
 *  <li><b>image_text_font</b> built-in font for the text label, from 1 to 5. 1 is the smallest (default: 5)<br>
 *  <pre>$MyObject->image_text_font = 4;</pre></li>
 *  <li><b>image_text_x</b> absolute text label position, in pixels from the left border. can be negative (default: NULL)<br>
 *  <pre>$MyObject->image_text_x = 5;</pre></li>
 *  <li><b>image_text_y</b> absolute text label position, in pixels from the top border. can be negative (default: NULL)<br>
 *  <pre>$MyObject->image_text_y = 5;</pre></li>
 *  <li><b>image_text_position</b> text label position withing the image, a combination of one or two from 'TBLR': top, bottom, left, right (default: NULL)<br>
 *  <pre>$MyObject->image_text_position = 'LR';</pre></li>
 *  <li><b>image_text_padding</b> text label padding, in pixels. can be overridden by {@link image_text_padding_x} and {@link image_text_padding_y} (default: 0)<br>
 *  <pre>$MyObject->image_text_padding = 5;</pre></li>
 *  <li><b>image_text_padding_x</b> text label horizontal padding (default: NULL)<br>
 *  <pre>$MyObject->image_text_padding_x = 2;</pre></li>
 *  <li><b>image_text_padding_y</b> text label vertical padding (default: NULL)<br>
 *  <pre>$MyObject->image_text_padding_y = 10;</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_flip</b> flips image, wither 'h' horizontal or 'v' vertical (default: NULL)<br>
 *  <pre>$MyObject->image_flip = 'h';</pre></li>
 *  <li><b>image_rotate</b> rotates image. possible values are 90, 180 and 270 (default: NULL)<br>
 *  <pre>$MyObject->image_rotate = 90;</pre></li>
 *  <li><b>image_crop</b> crops image. accepts 4, 2 or 1 values as 'T R B L' or 'TB LR' or 'TBLR'. dimension can be 20, or 20px or 20% (default: NULL)<br>
 *  <pre>$MyObject->image_crop = array(50,40,30,20); OR '-20 20%'...</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_bevel</b> adds a bevel border to the image. value is thickness in pixels (default: NULL)<br>
 *  <pre>$MyObject->image_bevel = 20;</pre></li>
 *  <li><b>image_bevel_color1</b> top and left bevel color, in hexadecimal (default: #FFFFFF)<br>
 *  <pre>$MyObject->image_bevel_color1 = '#FFFFFF';</pre></li>
 *  <li><b>image_bevel_color2</b> bottom and right bevel color, in hexadecimal (default: #000000)<br>
 *  <pre>$MyObject->image_bevel_color2 = '#000000';</pre></li>
 *  <li><b>image_border</b> adds a unicolor border to the image. accepts 4, 2 or 1 values as 'T R B L' or 'TB LR' or 'TBLR'. dimension can be 20, or 20px or 20% (default: NULL)<br>
 *  <pre>$MyObject->image_border = '3px'; OR '-20 20%' OR array(3,2)...</pre></li>
 *  <li><b>image_border_color</b> border color, in hexadecimal (default: #FFFFFF)<br>
 *  <pre>$MyObject->image_border_color = '#FFFFFF';</pre></li>
 *  <li><b>image_frame</b> type of frame: 1=flat 2=crossed (default: NULL)<br>
 *  <pre>$MyObject->image_frame = 2;</pre></li>
 *  <li><b>image_frame_colors</b> list of hex colors, in an array or a space separated string (default: '#FFFFFF #999999 #666666 #000000')<br>
 *  <pre>$MyObject->image_frame_colors = array('#999999',  '#FF0000', '#666666', '#333333', '#000000');</pre></li>
 * </ul>
 * <ul>
 *  <li><b>image_watermark</b> adds a watermark on the image, value is a local filename. accepted files are GIF, JPG, PNG and PNG alpha (default: NULL)<br>
 *  <pre>$MyObject->image_watermark = 'watermark.png';</pre></li>
 *  <li><b>image_watermark_x</b> absolute watermark position, in pixels from the left border. can be negative (default: NULL)<br>
 *  <pre>$MyObject->image_watermark_x = 5;</pre></li>
 *  <li><b>image_watermark_y</b> absolute watermark position, in pixels from the top border. can be negative (default: NULL)<br>
 *  <pre>$MyObject->image_watermark_y = 5;</pre></li>
 *  <li><b>image_watermark_position</b> watermark position withing the image, a combination of one or two from 'TBLR': top, bottom, left, right (default: NULL)<br>
 *  <pre>$MyObject->image_watermark_position = 'LR';</pre></li>
 * </ul>
 *
 * <b>Requirements</b>
 *
 * Most of the image operations require GD. GD2 is greatly recommended
 *
 * The class is compatible with PHP 4.3+, and compatible with PHP5
 *
 * <b>Changelog</b>
 * <ul>
 *  <li><b>v 0.21</b> 30/09/2006<br>
 *   - added {@link image_ratio_crop} which resizes within {@link image_x} and {@link image_y}, keeping ratio, but filling the space by cropping excedent of image<br>
 *   - added {@link mime_check}, which default is true, to set checks against {@link allowed} MIME list<br>
 *   - if MIME is empty, the class now triggers an error<br>
 *   - color #000000 is OK for {@link image_text_color}, and related text transparency bug fixed<br>
 *   - {@link gd_version}() now uses gd_info(), or else phpinfo()<br>
 *   - fixed path issue when the destination path has no trailing slash on Windows systems <br>
 *   - removed inline functions to be fully PHP5 compatible </li>
 *  <li><b>v 0.20</b> 11/08/2006<br>
 *   - added some more error checking and messages (GD presence, permissions...)<br>
 *   - fix when uploading files without extension<br>
 *   - changed values for {@link image_brightness} and {@link image_contrast} to be between -127 and 127<br>
 *   - added {@link dir_auto_create} to automatically and recursively create destination directory if missing.<br>
 *   - added {@link dir_auto_chmod} to automatically chmod the destination directory if not writeable.<br>
 *   - added {@link dir_chmod} to set the default chmod to use.<br>
 *   - added {@link image_crop} to crop images<br>
 *   - added {@link image_negative} to invert the colors on the image<br>
 *   - added {@link image_greyscale} to turn the image into greyscale<br>
 *   - added {@link image_threshold} to apply a threshold filter on the image<br>
 *   - added {@link image_bevel}, {@link image_bevel_color1} and {@link image_bevel_color2} to add a bevel border<br>
 *   - added {@link image_border} and {@link image_border_color} to add a single color border<br>
 *   - added {@link image_frame} and {@link image_frame_colors} to add a multicolored frame</li>
 *  <li><b>v 0.19</b> 29/03/2006<br>
 *   - class is now compatible i18n (thanks Sylwester).<br>
 *   - the class can mow manipulate local files, not only uploaded files (instanciate the class with a local filename).<br>
 *   - {@link file_safe_name} has been improved a bit.<br>
 *   - added {@link image_brightness}, {@link image_contrast}, {@link image_tint_color}, {@link image_overlay_color} and {@link image_overlay_percent} to do color manipulation on the images.<br>
 *   - added {@link image_text} and all derivated settings to add a text label on the image.<br>
 *   - added {@link image_watermark} and all derivated settings to add a watermark image on the image.<br>
 *   - added {@link image_flip} and {@link image_rotate} for more image manipulations<br>
 *   - added {@link jpeg_size} to calculate the JPG compression quality in order to fit within one filesize.</li>
 *  <li><b>v 0.18</b> 02/02/2006<br>
 *   - added {@link no_script} to turn dangerous scripts into text files.<br>
 *   - added {@link mime_magic_check} to set the class to use mime_magic.<br>
 *   - added {@link preserve_transparency} *experimental*. Thanks Gregor.<br>
 *   - fixed size and mime checking, wasn't working :/ Thanks Willem.<br>
 *   - fixed memory leak when resizing images.<br>
 *   - when resizing, it is not necessary anymore to set {@link image_convert}.<br>
 *   - il is now possible to simply convert an image, with no resizing.<br>
 *   - sets the default {@link file_max_size} to upload_max_filesize from php.ini. Thanks Edward</li>
 *  <li><b>v 0.17</b> 28/05/2005<br>
 *   - the class can be used with any version of GD.<br>
 *   - added security check on the file with a list of mime-types.<br>
 *   - changed the license to GPL v2 only</li>
 *  <li><b>v 0.16</b> 19/05/2005<br>
 *   - added {@link file_auto_rename} automatic file renaming if the same filename already exists.<br>
 *   - added {@link file_safe_name} safe formatting of the filename (spaces to _underscores so far).<br>
 *   - added some more error reporting to avoid crash if GD is not present</li>
 *  <li><b>v 0.15</b> 16/04/2005<br>
 *   - added JPEG compression quality setting. Thanks Vad</li>
 *  <li><b>v 0.14</b> 14/03/2005<br>
 *   - reworked the class file to allow parsing with phpDocumentor</li>
 *  <li><b>v 0.13</b> 07/03/2005<br>
 *   - fixed a bug with {@link image_ratio}. Thanks Justin.<br>
 *   - added {@link image_ratio_no_zoom_in} and {@link image_ratio_no_zoom_out}</li>
 *  <li><b>v 0.12</b> 21/01/2005<br>
 *   - added {@link image_ratio} to resize within max values, keeping image ratio</li>
 *  <li><b>v 0.11</b> 22/08/2003<br>
 *   - update for GD2 (changed imageresized() into imagecopyresampled() and imagecreate() into imagecreatetruecolor())</li>
 * </ul>
 *
 * @package   cmf
 * @subpackage external
 */
class upload {


    /**
     * Uploaded file name
     *
     * @access public
     * @var string
     */
    var $file_src_name;

    /**
     * Uploaded file name body (i.e. without extension)
     *
     * @access public
     * @var string
     */
    var $file_src_name_body;

    /**
     * Uploaded file name extension
     *
     * @access public
     * @var string
     */
    var $file_src_name_ext;

    /**
     * Uploaded file MIME type
     *
     * @access public
     * @var string
     */
    var $file_src_mime;

    /**
     * Uploaded file size, in bytes
     *
     * @access public
     * @var double
     */
    var $file_src_size;

    /**
     * Holds eventual PHP error code from $_FILES
     *
     * @access public
     * @var string
     */
    var $file_src_error;

    /**
     * Uloaded file name, including server path
     *
     * @access private
     * @var string
     */
    var $file_src_pathname;

    /**
     * Destination file name
     *
     * @access private
     * @var string
     */
    var $file_dst_path;

    /**
     * Destination file name
     *
     * @access public
     * @var string
     */
    var $file_dst_name;

    /**
     * Destination file name body (i.e. without extension)
     *
     * @access public
     * @var string
     */
    var $file_dst_name_body;

    /**
     * Destination file extension
     *
     * @access public
     * @var string
     */
    var $file_dst_name_ext;

    /**
     * Destination file name, including path
     *
     * @access private
     * @var string
     */
    var $file_dst_pathname;

    /**
     * Source image width
     *
     * @access private
     * @var integer
     */
    var $image_src_x;

    /**
     * Source image height
     *
     * @access private
     * @var integer
     */
    var $image_src_y;

    /**
     * Destination image width
     *
     * @access private
     * @var integer
     */
    var $image_dst_x;

    /**
     * Destination image height
     *
     * @access private
     * @var integer
     */
    var $image_dst_y;

    /**
     * Flag set after instanciating the class
     *
     * Indicates if the file has been uploaded properly
     *
     * @access public
     * @var bool
     */
    var $uploaded;

    /**
     * Flag stopping PHP upload checks
     *
     * Indicates whether we instanciated the class with a filename, in which case
     * we will not check on the validity of the PHP *upload*
     *
     * This flag is automatically set to true when working on a local file
     *
     * Warning: for uploads, this flag MUST be set to false for security reason
     *
     * @access public
     * @var bool
     */
    var $no_upload_check;

    /**
     * Flag set after calling a process
     *
     * Indicates if the processing, and copy of the resulting file went OK
     *
     * @access public
     * @var bool
     */
    var $processed;

    /**
     * Holds eventual error message in plain english
     *
     * @access public
     * @var string
     */
    var $error;

    /**
     * Holds an HTML formatted log
     *
     * @access public
     * @var string
     */
    var $log;


    // overiddable processing variables


    /**
     * Set this variable to replace the name body (i.e. without extension)
     *
     * @access public
     * @var string
     */
    var $file_new_name_body;

    /**
     * Set this variable to add a string to the faile name body
     *
     * @access public
     * @var string
     */
    var $file_name_body_add;

    /**
     * Set this variable to change the file extension
     *
     * @access public
     * @var string
     */
    var $file_new_name_ext;

    /**
     * Set this variable to format the filename (spaces changed to _)
     *
     * @access public
     * @var boolean
     */
    var $file_safe_name;

    /**
     * Set this variable to false if you don't want to check the MIME against the allowed list
     *
     * This variable is set to true by default for security reason
     *
     * @access public
     * @var boolean
     */
    var $mime_check;

    /**
     * Set this variable to true if you want to check the MIME type against a mime_magic file
     *
     * This variable is set to false by default as many systems don't have mime_magic installed or properly set
     *
     * @access public
     * @var boolean
     */
    var $mime_magic_check;

    /**
     * Set this variable to false if you don't want to turn dangerous scripts into simple text files
     *
     * @access public
     * @var boolean
     */
    var $no_script;

    /**
     * Set this variable to true to allow automatic renaming of the file
     * if the file already exists
     *
     * Default value is true
     *
     * For instance, on uploading foo.ext,<br>
     * if foo.ext already exists, upload will be renamed foo_1.ext<br>
     * and if foo_1.ext already exists, upload will be renamed foo_2.ext<br>
     *
     * @access public
     * @var bool
     */
    var $file_auto_rename;

    /**
     * Set this variable to true to allow automatic creation of the destination
     * directory if it is missing (works recursively)
     *
     * Default value is true
     *
     * @access public
     * @var bool
     */
    var $dir_auto_create;

    /**
     * Set this variable to true to allow automatic chmod of the destination
     * directory if it is not writeable
     *
     * Default value is true
     *
     * @access public
     * @var bool
     */
    var $dir_auto_chmod;

     /**
     * Set this variable to the default chmod you want the class to use
     * when creating directories, or attempting to write in a directory
     *
     * Default value is 0777 (without quotes)
     *
     * @access public
     * @var bool
     */
    var $dir_chmod;

    /**
     * Set this variable tu true to allow overwriting of an existing file
     *
     * Default value is false, so no files will be overwritten
     *
     * @access public
     * @var bool
     */
    var $file_overwrite;

    /**
     * Set this variable to change the maximum size in bytes for an uploaded file
     *
     * Default value is the value <i>upload_max_filesize</i> from php.ini
     *
     * @access public
     * @var double
     */
    var $file_max_size;

    /**
     * Set this variable to true to resize the file if it is an image
     *
     * You will probably want to set {@link image_x} and {@link image_y}, and maybe one of the ratio variables
     *
     * Default value is false (no resizing)
     *
     * @access public
     * @var bool
     */
    var $image_resize;

    /**
     * Set this variable to convert the file if it is an image
     *
     * Possibles values are : ''; 'png'; 'jpeg'; 'gif'
     *
     * Default value is '' (no conversion)<br>
     * If {@link resize} is true, {@link convert} will be set to the source file extension
     *
     * @access public
     * @var string
     */
    var $image_convert;

    /**
     * Set this variable to the wanted (or maximum/minimum) width for the processed image, in pixels
     *
     * Default value is 150
     *
     * @access public
     * @var integer
     */
    var $image_x;

    /**
     * Set this variable to the wanted (or maximum/minimum) height for the processed image, in pixels
     *
     * Default value is 150
     *
     * @access public
     * @var integer
     */
    var $image_y;

    /**
     * Set this variable to keep the original size ratio to fit within {@link image_x} x {@link image_y}
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio;

    /**
     * Set this variable to keep the original size ratio to fit within {@link image_x} x {@link image_y}
     *
     * The image will be resized as to fill the whole space, and excedent will be cropped
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio_crop;

    /**
     * Set this variable to keep the original size ratio to fit within {@link image_x} x {@link image_y},
     * but only if original image is bigger
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio_no_zoom_in;

    /**
     * Set this variable to keep the original size ratio to fit within {@link image_x} x {@link image_y},
     * but only if original image is smaller
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio_no_zoom_out;

    /**
     * Set this variable to calculate {@link image_x} automatically , using {@link image_y} and conserving ratio
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio_x;

    /**
     * Set this variable to calculate {@link image_y} automatically , using {@link image_x} and conserving ratio
     *
     * Default value is false
     *
     * @access public
     * @var bool
     */
    var $image_ratio_y;

    /**
     * Quality of JPEG created/converted destination image
     *
     * Default value is 75
     *
     * @access public
     * @var integer;
     */
    var $jpeg_quality;

    /**
     * Determines the quality of the JPG image to fit a desired file size
     *
     * Value is in bytes. The JPG quality will be set between 1 and 100%
     * The calculations are approximations.
     *
     * Default value is NULL (no calculations)
     *
     * @access public
     * @var integer;
     */
    var $jpeg_size;

    /**
     * Preserve transparency when resizing or converting an image (experimental)
     *
     * Default value is false
     *
     * Currently works only when resizing GIFs or converting transparent GIF to PNG<br>
     * It has problems with transparent PNG
     *
     * @access public
     * @var integer;
     */
    var $preserve_transparency;

    /**
     * Corrects the image brightness
     *
     * Value can range between -127 and 127
     *
     * Default value is NULL
     *
     * @access public
     * @var integer;
     */
    var $image_brightness;

    /**
     * Corrects the image contrast
     *
     * Value can range between -127 and 127
     *
     * Default value is NULL
     *
     * @access public
     * @var integer;
     */
    var $image_contrast;

    /**
     * Applies threshold filter
     *
     * Value can range between -127 and 127
     *
     * Default value is NULL
     *
     * @access public
     * @var integer;
     */
    var $image_threshold;

    /**
     * Applies a tint on the image
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * Default value is NULL
     *
     * @access public
     * @var string;
     */
    var $image_tint_color;

    /**
     * Applies a colored overlay on the image
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * To use with {@link image_overlay_percent}
     *
     * Default value is NULL
     *
     * @access public
     * @var string;
     */
    var $image_overlay_color;

    /**
     * Sets the percentage for the colored overlay
     *
     * Value is a percentage, as an integer between 0 and 100
     *
     * Unless used with {@link image_overlay_color}, this setting has no effect
     *
     * Default value is 50
     *
     * @access public
     * @var integer;
     */
    var $image_overlay_percent;

    /**
     * Inverts the color of an image
     *
     * Default value is FALSE
     *
     * @access public
     * @var boolean;
     */
    var $image_negative;

    /**
     * Turns the image into greyscale
     *
     * Default value is FALSE
     *
     * @access public
     * @var boolean;
     */
    var $image_greyscale;

    /**
     * Adds a text label on the image
     *
     * Value is a string, any text. Beware that the text won't wordwrap
     *
     * If set, this setting allow the use of all other settings starting with image_text_
     *
     * Default value is NULL
     *
     * @access public
     * @var string;
     */
    var $image_text;

    /**
     * Sets the text direction for the text label
     *
     * Value is either 'h' or 'v', as in horizontal and vertical
     *
     * Default value is h (horizontal)
     *
     * @access public
     * @var string;
     */
    var $image_text_direction;

    /**
     * Sets the text color for the text label
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * Default value is #FFFFFF (white)
     *
     * @access public
     * @var string;
     */
    var $image_text_color;

    /**
     * Sets the text visibility in the text label
     *
     * Value is a percentage, as an integer between 0 and 100
     *
     * Default value is 100
     *
     * @access public
     * @var integer;
     */
    var $image_text_percent;

    /**
     * Sets the text background color for the text label
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * Default value is NULL (no background)
     *
     * @access public
     * @var string;
     */
    var $image_text_background;

    /**
     * Sets the text background visibility in the text label
     *
     * Value is a percentage, as an integer between 0 and 100
     *
     * Default value is 100
     *
     * @access public
     * @var integer;
     */
    var $image_text_background_percent;

    /**
     * Sets the text font in the text label
     *
     * Value is a an integer between 1 and 5
     *
     * These fonts are built-in on your system. 1 is the smallest font, 5 the biggest
     *
     * Default value is 5
     *
     * @access public
     * @var integer;
     */
    var $image_text_font;

    /**
     * Sets the text label position within the image
     *
     * Value is one or two out of 'TBLR' (top, bottom, left, right)
     *
     * The positions are as following:
     * <pre>
     *                        TL  T  TR
     *                        L       R
     *                        BL  B  BR
     * </pre>
     *
     * Default value is NULL (centered, horizontal and vertical)
     *
     * Note that is {@link image_text_x} and {@link image_text_y} are used, this setting has no effect
     *
     * @access public
     * @var string;
     */
    var $image_text_position;

    /**
     * Sets the text label absolute X position within the image
     *
     * Value is in pixels, representing the distance between the left of the image and the label
     * If a negative value is used, it will represent the distance between the right of the image and the label
     *
     * Default value is NULL (so {@link image_text_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_text_x;

    /**
     * Sets the text label absolute Y position within the image
     *
     * Value is in pixels, representing the distance between the top of the image and the label
     * If a negative value is used, it will represent the distance between the bottom of the image and the label
     *
     * Default value is NULL (so {@link image_text_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_text_y;

    /**
     * Sets the text label padding
     *
     * Value is in pixels, representing the distance between the text and the label background border
     *
     * Default value is 0
     *
     * This setting can be overriden by {@link image_text_padding_x} and {@link image_text_padding_y}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding;

    /**
     * Sets the text label horizontal padding
     *
     * Value is in pixels, representing the distance between the text and the left and right label background borders
     *
     * Default value is NULL
     *
     * If set, this setting overrides the horizontal part of {@link image_text_padding}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding_x;

    /**
     * Sets the text label vertical padding
     *
     * Value is in pixels, representing the distance between the text and the top and bottom label background borders
     *
     * Default value is NULL
     *
     * If set, his setting overrides the vertical part of {@link image_text_padding}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding_y;

    /**
     * Flips the image vertically or horizontally
     *
     * Value is either 'h' or 'v', as in horizontal and vertical
     *
     * Default value is NULL (no flip)
     *
     * @access public
     * @var string;
     */
    var $image_flip;

    /**
     * Rotates the image by increments of 45 degrees
     *
     * Value is either 90, 180 or 270
     *
     * Default value is NULL (no rotation)
     *
     * @access public
     * @var string;
     */
    var $image_rotate;

    /**
     * Crops an image
     *
     * Values are four dimensions, or two, or one (CSS style)
     * They represent the amount cropped top, right, bottom and left.
     * These values can either be in an array, or a space separated string.
     * Each value can be in pixels (with or without 'px'), or percentage (of the source image)
     *
     * For instance, are valid:
     * <pre>
     * $foo->image_crop = 20                  OR array(20);
     * $foo->image_crop = '20px'              OR array('20px');
     * $foo->image_crop = '20 40'             OR array('20', 40);
     * $foo->image_crop = '-20 25%'           OR array(-20, '25%');
     * $foo->image_crop = '20px 25%'          OR array('20px', '25%');
     * $foo->image_crop = '20% 25%'           OR array('20%', '25%');
     * $foo->image_crop = '20% 25% 10% 30%'   OR array('20%', '25%', '10%', '30%');
     * $foo->image_crop = '20px 25px 2px 2px' OR array('20px', '25%px', '2px', '2px');
     * $foo->image_crop = '20 25% 40px 10%'   OR array(20, '25%', '40px', '10%');
     * </pre>
     *
     * If a value is negative, the image will be expanded, and the extra parts will be filled with black
     *
     * Default value is NULL (no cropping)
     *
     * @access public
     * @var string OR array;
     */
    var $image_crop;

    /**
     * Adds a bevel border on the image
     *
     * Value is a positive integer, representing the thickness of the bevel
     *
     * If the bevel colors are the same as the background, it makes a fade out effect
     *
     * Default value is NULL (no bevel)
     *
     * @access public
     * @var integer;
     */
    var $image_bevel;

    /**
     * Top and left bevel color
     *
     * Value is a color, in hexadecimal format
     * This setting is used only if {@link image_bevel} is set
     *
     * Default value is #FFFFFF
     *
     * @access public
     * @var string;
     */
    var $image_bevel_color1;

    /**
     * Right and bottom bevel color
     *
     * Value is a color, in hexadecimal format
     * This setting is used only if {@link image_bevel} is set
     *
     * Default value is #000000
     *
     * @access public
     * @var string;
     */
    var $image_bevel_color2;


    /**
     * Adds a single-color border on the outer of the image
     *
     * Values are four dimensions, or two, or one (CSS style)
     * They represent the border thickness top, right, bottom and left.
     * These values can either be in an array, or a space separated string.
     * Each value can be in pixels (with or without 'px'), or percentage (of the source image)
     *
     * See {@link image_crop} for valid formats
     *
     * If a value is negative, the image will be cropped.
     * Note that the dimensions of the picture will be increased by the borders' thickness
     *
     * Default value is NULL (no border)
     *
     * @access public
     * @var integer;
     */
    var $image_border;

    /**
     * Border color
     *
     * Value is a color, in hexadecimal format.
     * This setting is used only if {@link image_border} is set
     *
     * Default value is #FFFFFF
     *
     * @access public
     * @var string;
     */
    var $image_border_color;

    /**
     * Adds a multi-color frame on the outer of the image
     *
     * Value is an integer. Two values are possible for now:
     * 1 for flat border, meaning that the frame is mirrored horizontally and vertically
     * 2 for crossed border, meaning that the frame will be inversed, as in a bevel effect
     *
     * The frame will be composed of colored lines set in {@link image_frame_colors}
     *
     * Note that the dimensions of the picture will be increased by the borders' thickness
     *
     * Default value is NULL (no frame)
     *
     * @access public
     * @var integer;
     */
    var $image_frame;

    /**
     * Sets the colors used to draw a frame
     *
     * Values is a list of n colors in hexadecimal format.
     * These values can either be in an array, or a space separated string.
     *
     * The colors are listed in the following order: from the outset of the image to its center
     *
     * For instance, are valid:
     * <pre>
     * $foo->image_frame_colors = '#FFFFFF #999999 #666666 #000000';
     * $foo->image_frame_colors = array('#FFFFFF', '#999999', '#666666', '#000000');
     * </pre>
     *
     * This setting is used only if {@link image_frame} is set
     *
     * Default value is '#FFFFFF #999999 #666666 #000000'
     *
     * @access public
     * @var string OR array;
     */
    var $image_frame_colors;

    /**
     * Adds a watermark on the image
     *
     * Value is a local image filename, relative or absolute. GIF, JPG and PNG are supported, as well as PNG alpha.
     *
     * If set, this setting allow the use of all other settings starting with image_watermark_
     *
     * Default value is NULL
     *
     * @access public
     * @var string;
     */
    var $image_watermark;

    /**
     * Sets the watermarkposition within the image
     *
     * Value is one or two out of 'TBLR' (top, bottom, left, right)
     *
     * The positions are as following:   TL  T  TR
     *                                   L       R
     *                                   BL  B  BR
     *
     * Default value is NULL (centered, horizontal and vertical)
     *
     * Note that is {@link image_watermark_x} and {@link image_watermark_y} are used, this setting has no effect
     *
     * @access public
     * @var string;
     */
    var $image_watermark_position;

    /**
     * Sets the watermark absolute X position within the image
     *
     * Value is in pixels, representing the distance between the top of the image and the watermark
     * If a negative value is used, it will represent the distance between the bottom of the image and the watermark
     *
     * Default value is NULL (so {@link image_watermark_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_watermark_x;

    /**
     * Sets the twatermark absolute Y position within the image
     *
     * Value is in pixels, representing the distance between the left of the image and the watermark
     * If a negative value is used, it will represent the distance between the right of the image and the watermark
     *
     * Default value is NULL (so {@link image_watermark_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_watermark_y;

    /**
     * Allowed MIME types
     *
     * Default is a selection of safe mime-types, but you might want to change it
     *
     * @access public
     * @var integer
     */
    var $allowed;


    /**
     * Init or re-init all the processing variables to their default values
     *
     * This function is called in the constructor, and after each call of {@link process}
     *
     * @access private
     */
    function init() {

        // overiddable variables
        $this->file_new_name_body       = '';       // replace the name body
        $this->file_name_body_add       = '';       // append to the name body
        $this->file_new_name_ext        = '';       // replace the file extension
        $this->file_safe_name           = true;     // format safely the filename
        $this->file_overwrite           = false;    // allows overwritting if the file already exists
        $this->file_auto_rename         = true;     // auto-rename if the file already exists
        $this->dir_auto_create          = true;     // auto-creates directory if missing
        $this->dir_auto_chmod           = true;     // auto-chmod directory if not writeable
        $this->dir_chmod                = 0777;     // default chmod to use

        $this->mime_check               = true;     // don't check the mime type against the allowed list
        $this->mime_magic_check         = false;    // don't double check the MIME type with mime_magic
        $this->no_script                = true;     // turns scripts into test files

        $val = trim(ini_get('upload_max_filesize'));
        $last = strtolower($val{strlen($val)-1});
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        $this->file_max_size = $val;

        $this->image_resize             = false;    // resize the image
        $this->image_convert            = '';       // convert. values :''; 'png'; 'jpeg'; 'gif'

        $this->image_x                  = 150;
        $this->image_y                  = 150;
        $this->image_ratio              = false;    // keeps aspect ration with x and y dimensions
        $this->image_ratio_crop         = false;    // keeps aspect ration with x and y dimensions, filling the space
        $this->image_ratio_no_zoom_in   = false;
        $this->image_ratio_no_zoom_out  = false;
        $this->image_ratio_x            = false;    // calculate the $image_x if true
        $this->image_ratio_y            = false;    // calculate the $image_y if true
        $this->jpeg_quality             = 75;
        $this->jpeg_size                = NULL;
        $this->preserve_transparency    = false;

        $this->image_brightness         = NULL;
        $this->image_contrast           = NULL;
        $this->image_threshold          = NULL;
        $this->image_tint_color         = NULL;
        $this->image_overlay_color      = NULL;
        $this->image_overlay_percent    = NULL;
        $this->image_negative           = false;
        $this->image_greyscale          = false;

        $this->image_text               = NULL;
        $this->image_text_direction     = NULL;
        $this->image_text_color         = '#FFFFFF';
        $this->image_text_percent       = 100;
        $this->image_text_background    = NULL;
        $this->image_text_background_percent = 100;
        $this->image_text_font          = 5;
        $this->image_text_x             = NULL;
        $this->image_text_y             = NULL;
        $this->image_text_position      = NULL;
        $this->image_text_padding       = 0;
        $this->image_text_padding_x     = NULL;
        $this->image_text_padding_y     = NULL;

        $this->image_watermark          = NULL;
        $this->image_watermark_x        = NULL;
        $this->image_watermark_y        = NULL;
        $this->image_watermark_position = NULL;

        $this->image_flip               = NULL;
        $this->image_rotate             = NULL;
        $this->image_crop               = NULL;

        $this->image_bevel              = NULL;
        $this->image_bevel_color1       = '#FFFFFF';
        $this->image_bevel_color2       = '#000000';
        $this->image_border             = NULL;
        $this->image_border_color       = '#FFFFFF';
        $this->image_frame              = NULL;
        $this->image_frame_colors       = '#FFFFFF #999999 #666666 #000000';

        $this->allowed = array("application/arj",
                               "application/excel",
                               "application/gnutar",
                               "application/msword",
                               "application/mspowerpoint",
                               "application/octet-stream",
                               "application/pdf",
                               "application/powerpoint",
                               "application/postscript",
                               "application/plain",
                               "application/rtf",
                               "application/vnd.ms-excel",
                               "application/vocaltec-media-file",
                               "application/wordperfect",
                               "application/x-bzip",
                               "application/x-bzip2",
                               "application/x-compressed",
                               "application/x-excel",
                               "application/x-gzip",
                               "application/x-latex",
                               "application/x-midi",
                               "application/x-msexcel",
                               "application/x-rtf",
                               "application/x-sit",
                               "application/x-stuffit",
                               "application/x-shockwave-flash",
                               "application/x-troff-msvideo",
                               "application/x-zip-compressed",
                               "application/xml",
                               "application/zip",
                               "audio/aiff",
                               "audio/basic",
                               "audio/midi",
                               "audio/mod",
                               "audio/mpeg",
                               "audio/mpeg3",
                               "audio/wav",
                               "audio/x-aiff",
                               "audio/x-au",
                               "audio/x-mid",
                               "audio/x-midi",
                               "audio/x-mod",
                               "audio/x-mpeg-3",
                               "audio/x-wav",
                               "audio/xm",
                               "image/bmp",
                               "image/gif",
                               "image/jpeg",
                               "image/pjpeg",
                               "image/png",
                               "image/tiff",
                               "image/x-tiff",
                               "image/x-windows-bmp",
                               "multipart/x-zip",
                               "multipart/x-gzip",
                               "music/crescendo",
                               "text/richtext",
                               "text/plain",
                               "text/xml",
                               "video/avi",
                               "video/mpeg",
                               "video/msvideo",
                               "video/quicktime",
                               "video/quicktime",
                               "video/x-mpeg",
                               "video/x-ms-asf",
                               "video/x-ms-asf-plugin",
                               "video/x-msvideo",
                               "x-music/x-midi");
    }

    /**
     * Constructor. Checks if the file has been uploaded
     *
     * The constructor takes $_FILES['form_field'] array as argument
     * where form_field is the form field name
     *
     * The constructor will check if the file has been uploaded in its temporary location, and
     * accordingly will set {@link uploaded} (and {@link error} is an error occurred)
     *
     * If the file has been uploaded, the constructor will populate all the variables holding the upload
     * information (none of the processing class variables are used here).
     * You can have access to information about the file (name, size, MIME type...).
     *
     *
     * Alternatively, you can set the first argument to be a local filename (string)
     * and the second argument to be a MIME type (string) (second argument optional if mime_magic is installed)
     * This allows processing of a local file, as if the file was uploaded
     *
     * @access private
     * @param  array  $file $_FILES['form_field']
     *    or   string $file Local filename
     */
    function upload($file) {

        $this->file_src_name      = '';
        $this->file_src_name_body = '';
        $this->file_src_name_ext  = '';
        $this->file_src_mime      = '';
        $this->file_src_size      = '';
        $this->file_src_error     = '';
        $this->file_src_pathname  = '';

        $this->file_dst_path      = '';
        $this->file_dst_name      = '';
        $this->file_dst_name_body = '';
        $this->file_dst_name_ext  = '';
        $this->file_dst_pathname  = '';

        $this->image_src_x        = 0;
        $this->image_src_y        = 0;
        $this->image_dst_type     = '';
        $this->image_dst_x        = 0;
        $this->image_dst_y        = 0;

        $this->uploaded           = true;
        $this->no_upload_check    = false;
        $this->processed          = true;
        $this->error              = '';
        $this->log                = '';
        $this->allowed            = array();
        $this->init();

        if (!$file) {
            $this->uploaded = false;
            $this->error = _("File error. Please try again");
        }

        // check if we sent a local filename rather than a $_FILE element
        if (!is_array($file)) {
            if (empty($file)) {
                $this->uploaded = false;
                $this->error = _("File error. Please try again");
            } else {
                $this->no_upload_check = TRUE;
                // this is a local filename, i.e.not uploaded
                $this->log .= '<b>' . _("source is a local file") . ' ' . $file . '</b><br />';

                if ($this->uploaded && !file_exists($file)) {
                    $this->uploaded = false;
                    $this->error = _("Local file doesn't exist");
                }

                if ($this->uploaded && !is_readable($file)) {
                    $this->uploaded = false;
                    $this->error = _("Local file is not readable");
                }

                if ($this->uploaded) {
                    $this->file_src_pathname   = $file;
                    $this->file_src_name       = basename($file);
                    $this->log .= '- ' . _("local file name OK") . '<br />';
                    ereg('\.([^\.]*$)', $this->file_src_name, $extension);
                    if (is_array($extension)) {
                        $this->file_src_name_ext      = strtolower($extension[1]);
                        $this->file_src_name_body     = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext)))-1);
                    } else {
                        $this->file_src_name_ext      = '';
                        $this->file_src_name_body     = $this->file_src_name;
                    }
                    $this->file_src_size = (file_exists($file) ? filesize($file) : 0);
                    // we try to retrieve the MIME type
                    $info = getimagesize($this->file_src_pathname);
                    $this->file_src_mime = (array_key_exists('mime', $info) ? $info['mime'] : NULL);
                    // if we don't have a MIME type, we attempt to retrieve it the old way
                    if (empty($this->file_src_mime)) {
                        $mime = (array_key_exists(2, $info) ? $info[2] : NULL); // 1 = GIF, 2 = JPG, 3 = PNG
                        $this->file_src_mime = ($mime==1 ? 'image/gif' : ($mime==2 ? 'image/jpeg' : ($mime==3 ? 'image/png' : NULL)));
                    }
                    // if we still don't have a MIME type, we attempt to retrieve it otherwise
                    if (empty($this->file_src_mime) && function_exists('mime_content_type')) {
                        $this->file_src_mime = mime_content_type($this->file_src_pathname);
                    }
                    $this->file_src_error = 0;
                }

            }
        } else {
            // this is an element from $_FILE, i.e. an uploaded file
            $this->log .= '<b>' . _("source is an uploaded file") . '</b><br />';
            if ($this->uploaded) {
                $this->file_src_error         = $file['error'];
                switch($this->file_src_error) {
                    case 0:
                        // all is OK
                        $this->log .= '- ' . _("upload OK") . '<br />';
                        break;
                    case 1:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file exceeds the upload_max_filesize directive in php.ini)");
                        break;
                    case 2:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form)");
                        break;
                    case 3:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file was only partially uploaded)");
                        break;
                    case 4:
                        $this->uploaded = false;
                        $this->error = _("File upload error (no file was uploaded)");
                        break;
                    default:
                        $this->uploaded = false;
                        $this->error = _("File upload error (unknown error code)");
                }
            }

            if ($this->uploaded) {
                $this->file_src_pathname   = $file['tmp_name'];
                $this->file_src_name       = $file['name'];
                if ($this->file_src_name == '') {
                    $this->uploaded = false;
                    $this->error = _("File upload error. Please try again");
                }
            }

            if ($this->uploaded) {
                $this->log .= '- ' . _("file name OK") . '<br />';
                ereg('\.([^\.]*$)', $this->file_src_name, $extension);
                if (is_array($extension)) {
                    $this->file_src_name_ext      = strtolower($extension[1]);
                    $this->file_src_name_body     = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext)))-1);
                } else {
                    $this->file_src_name_ext      = '';
                    $this->file_src_name_body     = $this->file_src_name;
                }
                $this->file_src_size = $file['size'];
                $this->file_src_mime = $file['type'];
            }
        }

        $this->log .= '- ' . _("source variables") . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name         : ' . $this->file_src_name . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_body    : ' . $this->file_src_name_body . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_ext     : ' . $this->file_src_name_ext . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_pathname     : ' . $this->file_src_pathname . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_mime         : ' . $this->file_src_mime . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_size         : ' . $this->file_src_size . ' (max= ' . $this->file_max_size . ')<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_error        : ' . $this->file_src_error . '<br />';
    }


    /**
     * Returns the version of GD
     *
     * @access public
     * @return float GD version
     */
    function gd_version() {
        static $gd_version = null;
        if ($gd_version === null) {
            if (function_exists('gd_info')) {
                $gd = gd_info();
                $gd = $gd["GD Version"];
                $regex = "/([\d\.]+)/i";
            } else {
                ob_start();
                phpinfo(8);
                $gd = ob_get_contents();
                ob_end_clean();
                $regex = "/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i";
            }
            if (preg_match($regex, $gd, $m)) {
                $gd_version = (float) $m[1];
            } else {
                $gd_version = 0;
            }
        }
        return $gd_version;
    }


    /**
     * Creates directories recursively
     *
     * @access private
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    function r_mkdir($path, $mode = 0777) {
        return is_dir($path) || ( $this->r_mkdir(dirname($path), $mode) && @mkdir($path, $mode) );
    }


    /**
     * Actually uploads the file, and act on it according to the set processing class variables
     *
     * This function copies the uploaded file to the given location, eventually performing actions on it.
     * Typically, you can call {@link process} several times for the same file,
     * for instance to create a resized image and a thumbnail of the same file.
     * The original uploaded file remains intact in its temporary location, so you can use {@link process} several times.
     * You will be able to delete the uploaded file with {@link clean} when you have finished all your {@link process} calls.
     *
     * According to the processing class variables set in the calling file, the file can be renamed,
     * and if it is an image, can be resized or converted.
     *
     * When the processing is completed, and the file copied to its new location, the
     * processing class variables will be reset to their default value.
     * This allows you to set new properties, and perform another {@link process} on the same uploaded file
     *
     * It will set {@link processed} (and {@link error} is an error occurred)
     *
     * @access public
     * @param  string $server_path Path location of the uploaded file, with an ending slash
     */
    function process($server_path) {

        $this->error        = '';
        $this->processed    = true;

        if(strtolower(substr(PHP_OS, 0, 3)) === 'win') {
            if (substr($server_path, -1, 1) != '\\') $server_path = $server_path . '\\';
        } else {
            if (substr($server_path, -1, 1) != '/') $server_path = $server_path . '/';
        }
        $this->log .= '<b>' . _("process file to") . ' '  . $server_path . '</b><br />';

        // checks file size and mine type
        if ($this->uploaded) {

            if ($this->file_src_size > $this->file_max_size ) {
                $this->processed = false;
                $this->error = _("File too big");
            } else {
                $this->log .= '- ' . _("file size OK") . '<br />';
            }

            // turn dangerous scripts into text files
            if ($this->no_script) {
                if (((substr($this->file_src_mime, 0, 5) == 'text/' || strpos($this->file_src_mime, 'javascript') !== false)  && (substr($this->file_src_name, -4) != '.txt'))
                    || preg_match('/\.(php|pl|py|cgi|asp)$/i', $this->file_src_name) || empty($this->file_src_name_ext)) {
                    $this->file_src_mime = 'text/plain';
                    $this->log .= '- ' . _("script") . ' '  . $this->file_src_name . ' ' . _("renamed as") . ' ' . $this->file_src_name . '.txt!<br />';
                    $this->file_src_name_ext .= (empty($this->file_src_name_ext) ? 'txt' : '.txt');
                }
            }

            // checks MIME type with mime_magic
            if ($this->mime_magic_check && function_exists('mime_content_type')) {
                $detected_mime = mime_content_type($this->file_src_pathname);
                if ($this->file_src_mime != $detected_mime) {
                    $this->log .= '- ' . _("MIME type detected as") . ' ' . $detected_mime . ' ' . _("but given as") . ' ' . $this->file_src_mime . '!<br />';
                    $this->file_src_mime = $detected_mime;
                }
            }

            if ($this->mime_check && empty($this->file_src_mime)) {
                $this->processed = false;
                $this->error = _("MIME type can't be detected!");
            } else if ($this->mime_check && !empty($this->file_src_mime) && !array_key_exists($this->file_src_mime, array_flip($this->allowed))) {
                $this->processed = false;
                $this->error = _("Incorrect type of file");
            } else {
                $this->log .= '- ' . _("file mime OK") . ' : ' . $this->file_src_mime . '<br />';
            }
        } else {
            $this->error = _("File not uploaded. Can't carry on a process");
            $this->processed = false;
        }

        if ($this->processed) {
            $this->file_dst_path        = $server_path;

            // repopulate dst variables from src
            $this->file_dst_name        = $this->file_src_name;
            $this->file_dst_name_body   = $this->file_src_name_body;
            $this->file_dst_name_ext    = $this->file_src_name_ext;


            if ($this->file_new_name_body != '') { // rename file body
                $this->file_dst_name_body = $this->file_new_name_body;
                $this->log .= '- ' . _("new file name body") . ' : ' . $this->file_new_name_body . '<br />';
            }
            if ($this->file_new_name_ext != '') { // rename file ext
                $this->file_dst_name_ext  = $this->file_new_name_ext;
                $this->log .= '- ' . _("new file name ext") . ' : ' . $this->file_new_name_ext . '<br />';
            }
               if ($this->file_name_body_add != '') { // append a bit to the name
                $this->file_dst_name_body  = $this->file_dst_name_body . $this->file_name_body_add;
                $this->log .= '- ' . _("file name body add") . ' : ' . $this->file_name_body_add . '<br />';
            }
            if ($this->file_safe_name) { // formats the name
                $this->file_dst_name_body = str_replace(array(' ', '-'), array('_','_'), $this->file_dst_name_body) ;
                $this->file_dst_name_body = ereg_replace('[^A-Za-z0-9_]', '', $this->file_dst_name_body) ;
                $this->log .= '- ' . _("file name safe format") . '<br />';
            }

            $this->log .= '- ' . _("destination variables") . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_path         : ' . $this->file_dst_path . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_body    : ' . $this->file_dst_name_body . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_ext     : ' . $this->file_dst_name_ext . '<br />';

            // do we do some image manipulation?
            $image_manipulation  = ($this->image_resize
                                 || $this->image_convert != ''
                                 || is_numeric($this->image_brightness)
                                 || is_numeric($this->image_contrast)
                                 || is_numeric($this->image_threshold)
                                 || !empty($this->image_tint_color)
                                 || !empty($this->image_overlay_color)
                                 || !empty($this->image_text)
                                 || $this->image_greyscale
                                 || $this->image_negative
                                 || !empty($this->image_watermark)
                                 || is_numeric($this->image_rotate)
                                 || is_numeric($this->jpeg_size)
                                 || !empty($this->image_flip)
                                 || !empty($this->image_crop)
                                 || !empty($this->image_border)
                                 || $this->image_frame > 0
                                 || $this->image_bevel > 0);

            if ($image_manipulation) {
                if ($this->image_convert=='') {
                    $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                    $this->log .= '- ' . _("image operation, keep extension") . '<br />';
                } else {
                    $this->file_dst_name = $this->file_dst_name_body . '.' . $this->image_convert;
                    $this->log .= '- ' . _("image operation, change extension for conversion type") . '<br />';
                }
            } else {
                $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                $this->log .= '- ' . _("no image operation, keep extension") . '<br />';
            }

            if (!$this->file_auto_rename) {
                $this->log .= '- ' . _("no auto_rename if same filename exists") . '<br />';
                $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
            } else {
                $this->log .= '- ' . _("checking for auto_rename") . '<br />';
                $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
                $body     = $this->file_dst_name_body;
                $cpt = 1;
                while (@file_exists($this->file_dst_pathname)) {
                    $this->file_dst_name_body = $body . '_' . $cpt;
                    $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                    $cpt++;
                    $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
                }
                if ($cpt>1) $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("auto_rename to") . ' ' . $this->file_dst_name . '<br />';
            }

            $this->log .= '- ' . _("destination file details") . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name         : ' . $this->file_dst_name . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_pathname     : ' . $this->file_dst_pathname . '<br />';

            if ($this->file_overwrite) {
                 $this->log .= '- ' . _("no overwrite checking") . '<br />';
            } else {
                if (@file_exists($this->file_dst_pathname)) {
                    $this->processed = false;
                    $this->error = $this->file_dst_name . ' ' . _("already exists. Please change the file name");
                } else {
                    $this->log .= '- ' . $this->file_dst_name . ' '  . _("doesn't exist already") . '<br />';
                }
            }
        } else {
            $this->processed = false;
        }

        if (!$this->no_upload_check && !is_uploaded_file($this->file_src_pathname)) {
            $this->processed = false;
            $this->error = _("No correct source file. Can't carry on a process");
        }

        if ($this->processed && !file_exists($this->file_src_pathname)) {
            $this->processed = false;
            $this->error = _("No source file. Can't carry on a process");
        }

        // checks if the destination directory is readable
        if ($this->processed && !($f = @fopen($this->file_src_pathname, 'r'))) {
            $this->processed = false;
            if (is_readable($this->file_src_pathname)) {
                $this->error = _("Source file is not readable. open_basedir restriction in place?");
            } else {
                $this->error = _("Source file is not readable. Can't carry on a process");
            }
        } else {
            @fclose($f);
        }

        // checks if the destination directory exists, and attempt to create it
        if ($this->processed && !file_exists($this->file_dst_path)) {
            if ($this->dir_auto_create) {
                $this->log .= '- ' . $this->file_dst_path . ' '  . _("doesn't exist. Attempting creation:");
                if (!$this->r_mkdir($this->file_dst_path, $this->dir_chmod)) {
                    $this->log .= ' ' . _("failed") . '<br />';
                    $this->processed = false;
                    $this->error = _("Destination directory can't be created. Can't carry on a process");
                } else {
                    $this->log .= ' ' . _("success") . '<br />';
                }
            } else {
                $this->error = _("Destination directory doesn't exist. Can't carry on a process");
            }
        }

        if ($this->processed && !is_dir($this->file_dst_path)) {
            $this->processed = false;
            $this->error = _("Destination path is not a directory. Can't carry on a process");
        }

        // checks if the destination directory is writeable, and attempt to make it writeable
        if ($this->processed && !($f = @fopen($this->file_dst_pathname, 'w+'))) {
            if ($this->dir_auto_chmod) {
                $this->log .= '- ' . $this->file_dst_path . ' '  . _("is not writeable. Attempting chmod:");
                if (!@chmod($this->file_dst_path, $this->dir_chmod)) {
                    $this->log .= ' ' . _("failed") . '<br />';
                    $this->processed = false;
                    $this->error = _("Destination directory can't be made writeable. Can't carry on a process");
                } else {
                    $this->log .= ' ' . _("success") . '<br />';
                    if (!($f = @fopen($this->file_dst_pathname, 'w+'))) { // we re-check
                        $this->processed = false;
                        $this->error = _("Destination directory is still not writeable. Can't carry on a process");
                    } else {
                        @fclose($f);
                    }
                }
            } else {
                $this->processed = false;
                $this->error = _("Destination path is not a writeable. Can't carry on a process");
            }
        } else {
            @fclose($f);
        }

        if ($this->processed) {

            if ($image_manipulation) {

                $this->log .= '- ' . _("image resizing or conversion wanted") . '<br />';
                if ($this->gd_version()) {
                    switch($this->file_src_mime) {
                        case 'image/pjpeg':
                        case 'image/jpeg':
                        case 'image/jpg':
                            if (!function_exists('imagecreatefromjpeg')) {
                                $this->processed = false;
                                $this->error = _("No create from JPEG support");
                            } else {
                                $image_src = @imagecreatefromjpeg($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("Error in creating JPEG image from source");
                                } else {
                                    $this->log .= '- ' . _("source image is JPEG") . '<br />';
                                }
                            }
                            break;
                        case 'image/png':
                            if (!function_exists('imagecreatefrompng')) {
                                $this->processed = false;
                                $this->error = _("No create from PNG support");
                            } else {
                                $image_src = @imagecreatefrompng($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("Error in creating PNG image from source");
                                } else {
                                    $this->log .= '- ' . _("source image is PNG") . '<br />';
                                }
                            }
                            break;
                        case 'image/gif':
                            if (!function_exists('imagecreatefromgif')) {
                                $this->processed = false;
                                $this->error = _("Error in creating GIF image from source");
                            } else {
                                $image_src = @imagecreatefromgif($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("No GIF read support");
                                } else {
                                    $this->log .= '- ' . _("source image is GIF") . '<br />';
                                }
                            }
                            break;
                        default:
                            $this->processed = false;
                            $this->error = _("Can't read image source. not an image?");
                    }
                } else {
                    $this->processed = false;
                    $this->error = _("GD doesn't seem to be present");
                }

                if ($this->processed && $image_src) {

                    $this->image_src_x = imagesx($image_src);
                    $this->image_src_y = imagesy($image_src);
                    $this->image_dst_x = $this->image_src_x;
                    $this->image_dst_y = $this->image_src_y;
                    $gd_version = $this->gd_version();
                    $ratio_crop = null;

                    if ($this->image_resize) {
                        $this->log .= '- ' . _("resizing...") . '<br />';

                        if ($this->image_ratio_x) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("calculate x size") . '<br />';
                            $this->image_dst_x = round(($this->image_src_x * $this->image_y) / $this->image_src_y);
                            $this->image_dst_y = $this->image_y;
                        } else if ($this->image_ratio_y) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("calculate y size") . '<br />';
                            $this->image_dst_x = $this->image_x;
                            $this->image_dst_y = round(($this->image_src_y * $this->image_x) / $this->image_src_x);
                        } else if ($this->image_ratio || $this->image_ratio_crop || $this->image_ratio_no_zoom_in || $this->image_ratio_no_zoom_out) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("check x/y sizes") . '<br />';
                            if ((!$this->image_ratio_no_zoom_in && !$this->image_ratio_no_zoom_out)
                                 || ($this->image_ratio_no_zoom_in && ($this->image_src_x > $this->image_x || $this->image_src_y > $this->image_y))
                                 || ($this->image_ratio_no_zoom_out && $this->image_src_x < $this->image_x && $this->image_src_y < $this->image_y)) {
                                $this->image_dst_x = $this->image_x;
                                $this->image_dst_y = $this->image_y;
                                if ($this->image_ratio_crop) {
                                    if (($this->image_src_x/$this->image_x) > ($this->image_src_y/$this->image_y)) {
                                        $this->image_dst_y = $this->image_y;
                                        $this->image_dst_x = intval($this->image_src_x*($this->image_y / $this->image_src_y));
                                        $ratio_crop = array();
                                        $ratio_crop['x'] = $this->image_dst_x -$this->image_x;
                                        $ratio_crop['l'] = round($ratio_crop['x']/2);
                                        $ratio_crop['r'] = $ratio_crop['x'] - $ratio_crop['l'];
                                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("ratio_crop_x") . '         : ' . $ratio_crop['x'] . ' (' . $ratio_crop['l'] . ';' . $ratio_crop['r'] . ')<br />';
                                        if (is_null($this->image_crop)) $this->image_crop = array(0, 0, 0, 0);
                                    } else {
                                        $this->image_dst_x = $this->image_x;
                                        $this->image_dst_y = intval($this->image_src_y*($this->image_x / $this->image_src_x));
                                        $ratio_crop = array();
                                        $ratio_crop['y'] = $this->image_dst_y - $this->image_y;
                                        $ratio_crop['t'] = round($ratio_crop['y']/2);
                                        $ratio_crop['b'] = $ratio_crop['y'] - $ratio_crop['t'];
                                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("ratio_crop_y") . '         : ' . $ratio_crop['y'] . ' (' . $ratio_crop['t'] . ';' . $ratio_crop['b'] . ')<br />';
                                        if (is_null($this->image_crop)) $this->image_crop = array(0, 0, 0, 0);
                                    }
                                } else {
                                    if (($this->image_src_x/$this->image_x) > ($this->image_src_y/$this->image_y)) {
                                        $this->image_dst_x = $this->image_x;
                                        $this->image_dst_y = intval($this->image_src_y*($this->image_x / $this->image_src_x));
                                    } else {
                                        $this->image_dst_y = $this->image_y;
                                        $this->image_dst_x = intval($this->image_src_x*($this->image_y / $this->image_src_y));
                                    }
                                }
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("doesn't calculate x/y sizes") . '<br />';
                                $this->image_dst_x = $this->image_src_x;
                                $this->image_dst_y = $this->image_src_y;
                            }
                        } else {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("use plain sizes") . '<br />';
                            $this->image_dst_x = $this->image_x;
                            $this->image_dst_y = $this->image_y;
                        }

                        if ($this->preserve_transparency && $this->file_src_mime != 'image/gif' && $this->file_src_mime != 'image/png') $this->preserve_transparency = false;

                        if ($gd_version >= 2 && !$this->preserve_transparency) {
                            $image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        } else {
                            $image_dst = imagecreate($this->image_dst_x, $this->image_dst_y);
                        }

                        if ($this->preserve_transparency) {
                            $this->log .= '- ' . _("preserve transparency") . '<br />';
                            $transparent_color = imagecolortransparent($image_src);
                            imagepalettecopy($image_dst, $image_src);
                            imagefill($image_dst, 0, 0, $transparent_color);
                            imagecolortransparent($image_dst, $transparent_color);
                        }

 /*
         // we try to put a palette GIF image into a JPEG container so upsizing of picture is not pixelated. does not work

       if (!imageistruecolor($image_src)) {
            echo 'convert';
           $tmp = imagecreatetruecolor($this->image_src_x, $this->image_src_y);
           imagecopy($tmp,$image_src,0,0,0,0,$this->image_src_x, $this->image_src_y);
           $image_src = $tmp;
           //imagedestroy($tmp);
       }*/
                        if ($gd_version >= 2 && !$this->preserve_transparency) {
                           $res = imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_src_x, $this->image_src_y);
                        } else {
                            $res = imagecopyresized($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_src_x, $this->image_src_y);
                        }

                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("resized image object created") . '<br />';
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_src_x y        : ' . $this->image_src_x . ' x ' . $this->image_src_y . '<br />';
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_dst_x y        : ' . $this->image_dst_x . ' x ' . $this->image_dst_y . '<br />';

                    } else {
                        // we only convert, so we link the dst image to the src image
                        $image_dst = & $image_src;
                    }

                    // we have to set image_convert if it is not already
                    if (empty($this->image_convert)) {
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("setting destination file type to") . ' ' . $this->file_src_name_ext . '<br />';
                        $this->image_convert = $this->file_src_name_ext;
                    }

                    // crop imag (and also crops if image_ratio_crop is used)
                    if ($gd_version >= 2 && (!empty($this->image_crop) || !is_null($ratio_crop))) {
                        if (is_array($this->image_crop)) {
                            $vars = $this->image_crop;
                        } else {
                            $vars = explode(' ', $this->image_crop);
                        }
                        if (sizeof($vars) == 4) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[2]; $cl = $vars[3];
                        } else if (sizeof($vars) == 2) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[0]; $cl = $vars[1];
                        } else {
                            $ct = $vars[0]; $cr = $vars[0]; $cb = $vars[0]; $cl = $vars[0];
                        }
                        if (strpos($ct, '%')>0) $ct = $this->image_dst_y * (str_replace('%','',$ct) / 100);
                        if (strpos($cr, '%')>0) $cr = $this->image_dst_x * (str_replace('%','',$cr) / 100);
                        if (strpos($cb, '%')>0) $cb = $this->image_dst_y * (str_replace('%','',$cb) / 100);
                        if (strpos($cl, '%')>0) $cl = $this->image_dst_x * (str_replace('%','',$cl) / 100);
                        if (strpos($ct, 'px')>0) $ct = str_replace('px','',$ct);
                        if (strpos($cr, 'px')>0) $cr = str_replace('px','',$cr);
                        if (strpos($cb, 'px')>0) $cb = str_replace('px','',$cb);
                        if (strpos($cl, 'px')>0) $cl = str_replace('px','',$cl);
                        $ct = (int) $ct;
                        $cr = (int) $cr;
                        $cb = (int) $cb;
                        $cl = (int) $cl;
                        // we adjust the cropping if we use image_ratio_crop
                        if (!is_null($ratio_crop)) {
                            if (array_key_exists('t', $ratio_crop)) $ct += $ratio_crop['t'];
                            if (array_key_exists('r', $ratio_crop)) $cr += $ratio_crop['r'];
                            if (array_key_exists('b', $ratio_crop)) $cb += $ratio_crop['b'];
                            if (array_key_exists('l', $ratio_crop)) $cl += $ratio_crop['l'];
                        }
                        $this->log .= '- ' . _("crop image") . ' : ' . $ct . ' ' . $cr . ' ' . $cb . ' ' . $cl . ' <br />';
                        $this->image_dst_x = $this->image_dst_x - $cl - $cr;
                        $this->image_dst_y = $this->image_dst_y - $ct - $cb;
                        if ($this->image_dst_x < 1) $this->image_dst_x = 1;
                        if ($this->image_dst_y < 1) $this->image_dst_y = 1;

                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($tmp, $image_dst, 0, 0, $cl, $ct, $this->image_dst_x, $this->image_dst_y);

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);
                    }


                    // flip image
                    if ($gd_version >= 2 && !empty($this->image_flip)) {
                        $this->image_flip = strtolower($this->image_flip);
                        $this->log .= '- ' . _("flip image") . ' : ' . $this->image_flip . '<br />';
                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        for ($x = 0; $x < $this->image_dst_x; $x++) {
                            for ($y = 0; $y < $this->image_dst_y; $y++){
                                if (strpos($this->image_flip, 'v') !== false) {
                                    imagecopy($tmp, $image_dst, $this->image_dst_x - $x - 1, $y, $x, $y, 1, 1);
                                } else {
                                    imagecopy($tmp, $image_dst, $x, $this->image_dst_y - $y - 1, $x, $y, 1, 1);
                                }
                            }
                        }

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);
                    }


                    // rotate image
                    if ($gd_version >= 2 && is_numeric($this->image_rotate)) {
                        if (!in_array($this->image_rotate, array(0, 90, 180, 270))) $this->image_rotate = 0;
                        if ($this->image_rotate != 0) {
                            if ($this->image_rotate == 90 || $this->image_rotate == 270) {
                                $tmp=imagecreatetruecolor($this->image_dst_y, $this->image_dst_x);
                            } else {
                                $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                            }
                            $this->log .= '- ' . _("rotate image") . ' : ' . $this->image_rotate . '<br />';
                            for ($x = 0; $x < $this->image_dst_x; $x++) {
                                for ($y = 0; $y < $this->image_dst_y; $y++){
                                    if ($this->image_rotate == 90) {
                                        imagecopy($tmp, $image_dst, $y, $x, $x, $this->image_dst_y - $y - 1, 1, 1);
                                    } else if ($this->image_rotate == 180) {
                                        imagecopy($tmp, $image_dst, $x, $y, $this->image_dst_x - $x - 1, $this->image_dst_y - $y - 1, 1, 1);
                                    } else if ($this->image_rotate == 270) {
                                        imagecopy($tmp, $image_dst, $y, $x, $this->image_dst_x - $x - 1, $y, 1, 1);
                                    } else {
                                        imagecopy($tmp, $image_dst, $x, $y, $x, $y, 1, 1);
                                    }
                                }
                            }
                            if ($this->image_rotate == 90 || $this->image_rotate == 270) {
                                $t = $this->image_dst_y;
                                $this->image_dst_y = $this->image_dst_x;
                                $this->image_dst_x = $t;
                            }

                            // we transfert tmp into image_dst
                            imagedestroy($image_dst);
                            $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                            imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                            imagedestroy($tmp);
                        }
                    }

                    // add color overlay
                   if ($gd_version >= 2 && (is_numeric($this->image_overlay_percent) && !empty($this->image_overlay_color))) {
                        $this->log .= '- ' . _("apply color overlay") . '<br />';
                        sscanf($this->image_overlay_color, "#%2x%2x%2x", $red, $green, $blue);
                        $filter=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        $color=imagecolorallocate($filter, $red, $green, $blue);
                        imagefilledrectangle($filter, 0, 0, $this->image_dst_x, $this->image_dst_y, $color);
                        imagecopymerge($image_dst, $filter, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_overlay_percent);
                        imagedestroy($filter);
                    }

                    // add brightness, contrast and tint, turns to greyscale and inverts colors
                    if ($gd_version >= 2 && ($this->image_negative || $this->image_greyscale || is_numeric($this->image_threshold)|| is_numeric($this->image_brightness) || is_numeric($this->image_contrast) || !empty($this->image_tint_color))) {
                        $this->log .= '- ' . _("apply tint, light, contrast correction, negative, greyscale and threshold") . '<br />';
                        if (!empty($this->image_tint_color)) sscanf($this->image_tint_color, "#%2x%2x%2x", $red, $green, $blue);
                        $background = imagecolorallocatealpha($image_dst, 255, 255, 255, 0);
                        imagefill($image_dst, 0, 0, $background);
                        imagealphablending($image_dst, TRUE);
                        for($y=0; $y < $this->image_dst_y; $y++) {
                            for($x=0; $x < $this->image_dst_x; $x++) {
                                if ($this->image_greyscale) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = $g = $b = round((0.2125 * $pixel['red']) + (0.7154 * $pixel['green']) + (0.0721 * $pixel['blue']));
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (is_numeric($this->image_threshold)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $c = (round($pixel['red']+$pixel['green']+$pixel['blue'])/3) - 127;
                                    $r = $g = $b = ($c > $this->image_threshold ? 255 : 0);
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (is_numeric($this->image_brightness)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = max(min(round($pixel['red']+(($this->image_brightness*2))),255),0);
                                    $g = max(min(round($pixel['green']+(($this->image_brightness*2))),255),0);
                                    $b = max(min(round($pixel['blue']+(($this->image_brightness*2))),255),0);
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (is_numeric($this->image_contrast)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = max(min(round(($this->image_contrast+128)*$pixel['red']/128),255),0);
                                    $g = max(min(round(($this->image_contrast+128)*$pixel['green']/128),255),0);
                                    $b = max(min(round(($this->image_contrast+128)*$pixel['blue']/128),255),0);
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (!empty($this->image_tint_color)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = min(round($red*$pixel['red']/169),255);
                                    $g = min(round($green*$pixel['green']/169),255);
                                    $b = min(round($blue*$pixel['blue']/169),255);
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (!empty($this->image_negative)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = round(255-$pixel['red']);
                                    $g = round(255-$pixel['green']);
                                    $b = round(255-$pixel['blue']);
                                    $a = $pixel['alpha'];
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                            }
                        }
                    }

                    // adds a border
                    if ($gd_version >= 2 && !empty($this->image_border)) {
                        if (is_array($this->image_border)) {
                            $vars = $this->image_border;
                            $this->log .= '- ' . _("add border") . ' : ' . implode(' ', $this->image_border) . '<br />';
                        } else {
                            $this->log .= '- ' . _("add border") . ' : ' . $this->image_border . '<br />';
                            $vars = explode(' ', $this->image_border);
                        }
                        if (sizeof($vars) == 4) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[2]; $cl = $vars[3];
                        } else if (sizeof($vars) == 2) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[0]; $cl = $vars[1];
                        } else {
                            $ct = $vars[0]; $cr = $vars[0]; $cb = $vars[0]; $cl = $vars[0];
                        }
                        if (strpos($ct, '%')>0) $ct = $this->image_dst_y * (str_replace('%','',$ct) / 100);
                        if (strpos($cr, '%')>0) $cr = $this->image_dst_x * (str_replace('%','',$cr) / 100);
                        if (strpos($cb, '%')>0) $cb = $this->image_dst_y * (str_replace('%','',$cb) / 100);
                        if (strpos($cl, '%')>0) $cl = $this->image_dst_x * (str_replace('%','',$cl) / 100);
                        if (strpos($ct, 'px')>0) $ct = str_replace('px','',$ct);
                        if (strpos($cr, 'px')>0) $cr = str_replace('px','',$cr);
                        if (strpos($cb, 'px')>0) $cb = str_replace('px','',$cb);
                        if (strpos($cl, 'px')>0) $cl = str_replace('px','',$cl);
                        $ct = (int) $ct;
                        $cr = (int) $cr;
                        $cb = (int) $cb;
                        $cl = (int) $cl;
                        $this->image_dst_x = $this->image_dst_x + $cl + $cr;
                        $this->image_dst_y = $this->image_dst_y + $ct + $cb;
                        if (!empty($this->image_border_color)) sscanf($this->image_border_color, "#%2x%2x%2x", $red, $green, $blue);

                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        $background = imagecolorallocatealpha($tmp, $red, $green, $blue, 0);
                        imagefill($tmp, 0, 0, $background);
                        imagecopy($tmp, $image_dst, $cl, $ct, 0, 0, $this->image_dst_x - $cr - $cl, $this->image_dst_y - $cb - $ct);

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);
                    }

                    // add frame border
                    if (is_numeric($this->image_frame)) {
                        if (is_array($this->image_frame_colors)) {
                            $vars = $this->image_frame_colors;
                            $this->log .= '- ' . _("add frame") . ' : ' . implode(' ', $this->image_frame_colors) . '<br />';
                        } else {
                            $this->log .= '- ' . _("add frame") . ' : ' . $this->image_frame_colors . '<br />';
                            $vars = explode(' ', $this->image_frame_colors);
                        }

                        $nb = sizeof($vars);
                        $this->image_dst_x = $this->image_dst_x + ($nb * 2);
                        $this->image_dst_y = $this->image_dst_y + ($nb * 2);
                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($tmp, $image_dst, $nb, $nb, 0, 0, $this->image_dst_x - ($nb * 2), $this->image_dst_y - ($nb * 2));

                        for ($i=0; $i<$nb; $i++) {
                            sscanf($vars[$i], "#%2x%2x%2x", $red, $green, $blue);
                            $c = imagecolorallocate($tmp, $red, $green, $blue);
                            if ($this->image_frame == 1) {
                                imageline($tmp, $i, $i, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $i, $this->image_dst_y - $i -1, $c);
                                imageline($tmp, $i, $i, $i, $this->image_dst_y - $i -1, $c);
                            } else {
                                imageline($tmp, $i, $i, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $this->image_dst_x - $nb + $i, $nb - $i, $c);
                                imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $nb - $i, $this->image_dst_y - $nb + $i, $c);
                                imageline($tmp, $i, $i, $i, $this->image_dst_y - $i -1, $c);
                            }
                        }

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);
                    }

                    // add bevel border
                    if ($this->image_bevel > 0) {
                        if (empty($this->image_bevel_color1)) $this->image_bevel_color1 = '#FFFFFF';
                        if (empty($this->image_bevel_color2)) $this->image_bevel_color2 = '#000000';
                        sscanf($this->image_bevel_color1, "#%2x%2x%2x", $red1, $green1, $blue1);
                        sscanf($this->image_bevel_color2, "#%2x%2x%2x", $red2, $green2, $blue2);
                        imagealphablending($image_dst, true);
                        for ($i=0; $i<$this->image_bevel; $i++) {
                            $alpha = round(($i / $this->image_bevel) * 127);
                            $c1 = imagecolorallocatealpha($image_dst, $red1, $green1, $blue1, $alpha);
                            $c2 = imagecolorallocatealpha($image_dst, $red2, $green2, $blue2, $alpha);
                            imageline($image_dst, $i, $i, $this->image_dst_x - $i -1, $i, $c1);
                            imageline($image_dst, $this->image_dst_x - $i -1, $this->image_dst_y - $i, $this->image_dst_x - $i -1, $i, $c2);
                            imageline($image_dst, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $i, $this->image_dst_y - $i -1, $c2);
                            imageline($image_dst, $i, $i, $i, $this->image_dst_y - $i -1, $c1);
                        }
                    }

                    // add watermark image
                    if ($this->image_watermark!='' && file_exists($this->image_watermark)) {
                        $this->log .= '- ' . _("add watermark") . '<br />';
                        $this->image_watermark_position = strtolower($this->image_watermark_position);

                        $watermark_info = getimagesize($this->image_watermark);
                        $watermark_type = (array_key_exists(2, $watermark_info) ? $watermark_info[2] : NULL); // 1 = GIF, 2 = JPG, 3 = PNG
                        $watermark_checked = false;

                        if ($watermark_type == 1) {
                            if (!function_exists('imagecreatefromgif')) {
                                $this->error = _("No create from GIF support, can't read watermark");
                            } else {
                                $filter = @imagecreatefromgif($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No GIF read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is GIF") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        } else if ($watermark_type == 2) {
                            if (!function_exists('imagecreatefromjpeg')) {
                                $this->error = _("No create from JPG support, can't read watermark");
                            } else {
                                $filter = @imagecreatefromjpeg($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No JPG read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is JPG") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        } else if ($watermark_type == 3) {
                            if (!function_exists('imagecreatefrompng')) {
                                $this->error = _("No create from PNG support, can't read watermark");
                            } else {
                                $filter = @imagecreatefrompng($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No PNG read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is PNG") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        }
                        if ($watermark_checked) {
                            $watermark_width = imagesx($filter);
                            $watermark_height = imagesy($filter);
                            $watermark_x = 0;
                            $watermark_y = 0;
                            if (is_numeric($this->image_watermark_x)) {
                                if ($this->image_watermark_x < 0) {
                                    $watermark_x = $this->image_dst_x - $watermark_width + $this->image_watermark_x;
                                } else {
                                    $watermark_x = $this->image_watermark_x;
                                }
                            } else {
                                if (strpos($this->image_watermark_position, 'r') !== false) {
                                    $watermark_x = $this->image_dst_x - $watermark_width;
                                } else if (strpos($this->image_watermark_position, 'l') !== false) {
                                    $watermark_x = 0;
                                } else {
                                    $watermark_x = ($this->image_dst_x - $watermark_width) / 2;
                                }
                            }

                            if (is_numeric($this->image_watermark_y)) {
                                if ($this->image_watermark_y < 0) {
                                    $watermark_y = $this->image_dst_y - $watermark_height + $this->image_watermark_y;
                                } else {
                                    $watermark_y = $this->image_watermark_y;
                                }
                            } else {
                                if (strpos($this->image_watermark_position, 'b') !== false) {
                                    $watermark_y = $this->image_dst_y - $watermark_height;
                                } else if (strpos($this->image_watermark_position, 't') !== false) {
                                    $watermark_y = 0;
                                } else {
                                    $watermark_y = ($this->image_dst_y - $watermark_height) / 2;
                                }
                            }
                            imagecopyresampled ($image_dst, $filter, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);

                        } else {
                            $this->error = _("Watermark image is of unknown type");
                        }
                    }

                    // add text
                    if (!empty($this->image_text)) {
                        $this->log .= '- ' . _("add text") . '<br />';

                        if (!is_numeric($this->image_text_padding)) $this->image_text_padding = 0;
                        if (!is_numeric($this->image_text_padding_x)) $this->image_text_padding_x = $this->image_text_padding;
                        if (!is_numeric($this->image_text_padding_y)) $this->image_text_padding_y = $this->image_text_padding;
                        $this->image_text_position = strtolower($this->image_text_position);
                        $this->image_text_direction = strtolower($this->image_text_direction);

                        if ($this->image_text_direction == 'v') {
                            $text_height = (ImageFontWidth($this->image_text_font) * strlen($this->image_text)) + (2 * $this->image_text_padding_y);
                            $text_width = ImageFontHeight($this->image_text_font) + (2 * $this->image_text_padding_x);
                        } else {
                            $text_width = (ImageFontWidth($this->image_text_font) * strlen($this->image_text)) + (2 * $this->image_text_padding_x);
                            $text_height = ImageFontHeight($this->image_text_font) + (2 * $this->image_text_padding_y);
                        }
                        $text_x = 0;
                        $text_y = 0;
                        if (is_numeric($this->image_text_x)) {
                            if ($this->image_text_x < 0) {
                                $text_x = $this->image_dst_x - $text_width + $this->image_text_x;
                            } else {
                                $text_x = $this->image_text_x;
                            }
                        } else {
                            if (strpos($this->image_text_position, 'r') !== false) {
                                $text_x = $this->image_dst_x - $text_width;
                            } else if (strpos($this->image_text_position, 'l') !== false) {
                                $text_x = 0;
                            } else {
                                $text_x = ($this->image_dst_x - $text_width) / 2;
                            }
                        }

                        if (is_numeric($this->image_text_y)) {
                            if ($this->image_text_y < 0) {
                                $text_y = $this->image_dst_y - $text_height + $this->image_text_y;
                            } else {
                                $text_y = $this->image_text_y;
                            }
                        } else {
                            if (strpos($this->image_text_position, 'b') !== false) {
                                $text_y = $this->image_dst_y - $text_height;
                            } else if (strpos($this->image_text_position, 't') !== false) {
                                $text_y = 0;
                            } else {
                                $text_y = ($this->image_dst_y - $text_height) / 2;
                            }
                        }

                        // add a background, maybe transparent
                        if (!empty($this->image_text_background)) {
                            sscanf($this->image_text_background, "#%2x%2x%2x", $red, $green, $blue);
                            if ($gd_version >= 2 && (is_numeric($this->image_text_background_percent)) && $this->image_text_background_percent >= 0 && $this->image_text_background_percent <= 100) {
                                $filter=imagecreatetruecolor($text_width, $text_height);
                                $background_color=imagecolorallocate($filter, $red, $green, $blue);
                                imagefilledrectangle($filter, 0, 0, $text_width, $text_height, $background_color);
                                imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $text_width, $text_height, $this->image_text_background_percent);
                                imagedestroy($filter);
                            } else {
                                $background_color = imageColorAllocate($image_dst ,$red, $green, $blue);
                                imagefilledrectangle($image_dst, $text_x, $text_y, $text_x + $text_width, $text_y + $text_height, $background_color);
                            }
                        }

                        $text_x += $this->image_text_padding_x;
                        $text_y += $this->image_text_padding_y;

                        sscanf($this->image_text_color, "#%2x%2x%2x", $red, $green, $blue);


                        // add the text, maybe transparent
                        if ($gd_version >= 2 && (is_numeric($this->image_text_percent)) && $this->image_text_percent >= 0 && $this->image_text_percent <= 100) {
                            $t_width = $text_width - (2 * $this->image_text_padding_x);
                            $t_height = $text_height - (2 * $this->image_text_padding_y);
                            if ($t_width < 0) $t_width = 0;
                            if ($t_height < 0) $t_height = 0;
                            $filter=imagecreatetruecolor($t_width, $t_height);
                            $color = imagecolorallocate($filter, 0, 0, ($blue == 0 ? 255 : 0));
                            imagefill($filter, 0, 0, $color);
                            $text_color = imagecolorallocate($filter ,$red, $green, $blue);
                            imagecolortransparent($filter, $color);
                            if ($this->image_text_direction == 'v') {
                                imagestringup($filter, $this->image_text_font, 0, $text_height - (2 * $this->image_text_padding_y), $this->image_text, $text_color);
                            } else {
                                imagestring($filter, $this->image_text_font, 0, 0, $this->image_text, $text_color);
                            }
                            imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $t_width, $t_height, $this->image_text_percent);
                            imagedestroy($filter);
                        } else {
                            $text_color = imageColorAllocate($image_dst ,$red, $green, $blue);
                            if ($this->image_text_direction == 'v') {
                                imagestringup($image_dst, $this->image_text_font, $text_x, $text_y + $text_height - (2 * $this->image_text_padding_y), $this->image_text, $text_color);
                            } else {
                                imagestring($image_dst, $this->image_text_font, $text_x, $text_y, $this->image_text, $text_color);
                            }
                        }

                    }



                    if (is_numeric($this->jpeg_size) && $this->jpeg_size > 0 && ($this->image_convert == 'jpeg' || $this->image_convert == 'jpg')) {
                        // inspired by: JPEGReducer class version 1, 25 November 2004, Author: Huda M ElMatsani, justhuda at netscape dot net
                        $this->log .= '- ' . _("JPEG desired file size") . ' : ' . $this->jpeg_size . '<br />';
                        //calculate size of each image. 75%, 50%, and 25% quality
                        ob_start(); imagejpeg($image_dst,'',75);  $buffer = ob_get_contents(); ob_end_clean();
                        $size75 = strlen($buffer);
                        ob_start(); imagejpeg($image_dst,'',50);  $buffer = ob_get_contents(); ob_end_clean();
                        $size50 = strlen($buffer);
                        ob_start(); imagejpeg($image_dst,'',25);  $buffer = ob_get_contents(); ob_end_clean();
                        $size25 = strlen($buffer);

                        //calculate gradient of size reduction by quality
                        $mgrad1 = 25/($size50-$size25);
                        $mgrad2 = 25/($size75-$size50);
                        $mgrad3 = 50/($size75-$size25);
                        $mgrad  = ($mgrad1+$mgrad2+$mgrad3)/3;
                        //result of approx. quality factor for expected size
                        $q_factor=round($mgrad*($this->jpeg_size-$size50)+50);

                        if ($q_factor<1) {
                            $this->jpeg_quality=1;
                        } elseif ($q_factor>100) {
                            $this->jpeg_quality=100;
                        } else {
                            $this->jpeg_quality=$q_factor;
                        }
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("JPEG quality factor set to") . ' ' . $this->jpeg_quality . '<br />';
                    }



                    // outputs image
                    $this->log .= '- ' . _("converting..") . '<br />';
                    switch($this->image_convert) {
                        case 'jpeg':
                        case 'jpg':
                            $result = @imagejpeg ($image_dst, $this->file_dst_pathname, $this->jpeg_quality);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No JPEG create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("JPEG image created") . '<br />';
                            }
                            break;
                        case 'png':
                            $result = @imagepng ($image_dst, $this->file_dst_pathname);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No PNG create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("PNG image created") . '<br />';
                            }
                            break;
                        case 'gif':
                            $result = @imagegif ($image_dst, $this->file_dst_pathname);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No GIF create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("GIF image created") . '<br />';
                            }
                            break;
                        default:
                            $this->processed = false;
                            $this->error = _("No convertion type defined");
                    }
                    if ($this->processed) {
                        if (is_resource($image_src)) imagedestroy($image_src);
                        if (is_resource($image_dst)) imagedestroy($image_dst);
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("image objects destroyed") . '<br />';
                    }
                }

            } else {
                $this->log .= '- ' . _("no image processing wanted") . '<br />';

                if (!$this->no_upload_check) {
                    $result = is_uploaded_file($this->file_src_pathname);
                } else {
                    $result = TRUE;
                }
                if ($result) {
                    $result = file_exists($this->file_src_pathname);
                    if ($result) {
                        $result = copy($this->file_src_pathname, $this->file_dst_pathname);
                        if (!$result) {
                            $this->processed = false;
                            $this->error = _("Error copying file on the server. Copy failed");
                        }
                    } else {
                        $this->processed = false;
                        $this->error = _("Error copying file on the server. Missing source file");
                    }
                } else {
                    $this->processed = false;
                    $this->error = _("Error copying file on the server. Incorrect source file");
                }


                //$result = move_uploaded_file($this->file_src_pathname, $this->file_dst_pathname);
                //if (!$result) {
                //    $this->processed = false;
                //    $this->error = _("Error copying file on the server");
                //}
            }

        }

        if ($this->processed) {
            $this->log .= '- <b>' . _("process OK") . '</b><br />';

        }
        // we reinit all the var
        $this->init();

    }

    /**
     * Deletes the uploaded file from its temporary location
     *
     * When PHP uploads a file, it stores it in a temporary location.
     * When you {@link process} the file, you actually copy the resulting file to the given location, it doesn't alter the original file.
     * Once you have processed the file as many times as you wanted, you can delete the uploaded file.
     *
     * You might want not to use this function if you work on local files, as it will delete the source file
     *
     * @access public
     */
    function clean() {
        @unlink($this->file_src_pathname);
    }

}

// i18n gettext compatibility
if (!function_exists("_")) {
  function _($str) {
    return $str;
  }
}

?>
