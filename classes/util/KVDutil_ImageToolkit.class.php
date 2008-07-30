<?php
/**
 * @package KVD.util
 * @subpackage 
 * @version $Id: KVDutil_ImageToolkit.class.php 1 2007-10-05 13:16:16Z standadi $
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_ImageToolkit 
 * @package KVD.util
 * @subpackage 
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDutil_ImageToolkit{

 /** 
   * createThumbnailFor
   * 	Maakt een thumbnail voor de foto. Daarbij worden de verhoudingen van de
   *	oorspronkelijke foto behouden en geven de parameters mee welke de 
   *	maximale hoogte en breedte zijn.
   * @todo Methode naar een utility class verplaatsen. Hoort hier niet thuis.
   * @param resource $src_img een PHP beeld handler voor de foto
   * @param integer $new_w de maximale breedte voor de thumbnail, is standaard 130
   * @param integer $new_h de maximale hoogte voor de thumbnail, is standaard 130
   * @return resource een php beeld handler voor de thumbnail
   */
  static function createThumbnailFor($src_img,$new_w = 130,$new_h = 130){
    /* calculate thumbnail size, preserving aspect ratio */ 
    $old_x=imageSX($src_img);
    $old_y=imageSY($src_img);
    if ($old_x > $old_y) {
      $thumb_w=$new_w;
      $thumb_h=$old_y*($new_h/$old_x);
    } else {
      $thumb_w=$old_x*($new_w/$old_y);
      $thumb_h=$new_h;
    }
    /* create new thumbnail */
    $dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
    /* Insert scaled picture */
    imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
    /* Save file */
    return $dst_img;
  }
   
  /** 
   * getData
   * 	Geeft een string met de binaire data van de foto.
   * @param resource $src_img een PHP beeld handler voor de foto
   * @return string binaire data van de foto
   */
  static function getData($img){
    if(!ob_start()) return null; // start a new output buffer
    imagejpeg( $img, NULL, 100 );
    $ImageData = ob_get_contents();
    ob_end_clean(); // stop this output buffer
    return $ImageData;
  }
  /** 
   * getImage
   * 	Geeft een php beeld handler van de binaire data van de foto.
   * @throws RuntimeException - Indien de foto niet kon aangemaakt worden.
   * @param string $data de binare data van de foto
   * @return resource een php beeld handle.
   */
  static function getImage($data){
    $src = imagecreatefromstring($data);
    if(!$src) throw new RuntimeException("Failed to create image from data: ".$data);
    return $src;
  }



	/** 
   * rotate
   * 	Draait de foto van de handle willekeurig in stappen van 90 gradenen 
   * @todo Methode verplaatsen naar een utility class.
   * @param resource $src een php source van de foto
   * @param integer $angle het aantal graden dat de foto, in wijzerszin, wordt gedraaid
   * @return resource een php resource met het resultaat van de operatie.
   */   
  static function rotate($src, $angle){
    $srcX = imagesx( $src );
    $srcY = imagesy( $src );
    switch( $angle ) {
        case 90: 
          $dst = imagecreatetruecolor( $srcY, $srcX );
          for($x = 0; $x <$srcX; $x ++)
            for($y = 0; $y <$srcY; $y ++)
              imagecopy($dst, $src, $srcY-$y-1, $x, $x, $y, 1, 1);
          break;
        case 180: 
          $dst_temp = KVDutil_ImageToolkit::rotate($src, 90);
          $dst = KVDutil_ImageToolkit::rotate($dst_temp, 90);
          imagedestroy($dst_temp);
          break;
        case 270: 
          $dst = imagecreatetruecolor( $srcY, $srcX );
            for( $x=0; $x<$srcX; $x++ )
                for( $y=0; $y<$srcY; $y++ )
                    imagecopy($dst, $src, $y, $srcX-$x-1, $x, $y, 1, 1);
          break;
    }
    return $dst;
 }
 
 
	/**
	 * getSize
	 *  zoekt de breedte en hoogte van de foto op en geeft deze in een array terug.
	 * @param resource
	 * @return array
	 */
	static function getSize($src)
	{
		$x = imagesx( $src );
		$y = imagesy( $src );
		return array("x"=>$x, "y"=>$y);
	}
	
	
	/**
	 * isValidFoto
	 *  Neemt een foto en controleert of deze voldoet aan de eisen gegeven
	 *  in de configuratie. De test bevat minimum en maximum afmetingen 
	 *  alsook mimetypes.
	 * @param string bestandsnaam van de foto
	 * @param array configuratie
	 * @return boolean
	 */
	static function isValidFoto($filename, $config)
	{
		$data = getimagesize($filename);
		// breedte
		if(($data[0] < $config["minWidth"]) || ($data[0] > $config["maxWidth"])) return false;
		// hooogte
		if(($data[1] < $config["minHeight"]) || ($data[1] > $config["maxHeight"])) return false;
		// else
		return in_array($data['mime'], $config["mimes"]);
	}
 

}

?>