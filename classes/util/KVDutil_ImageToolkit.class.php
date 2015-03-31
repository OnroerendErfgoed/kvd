<?php
/**
 * @package     KVD.util
 * @copyright   2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 */

/**
 * Utility functies om te werken met afbeeldingen.
 *
 * @package     KVD.util
 * @since       september 2007
 * @copyright   2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Dieter Standaert <dieter.standaert@eds.com>
 */

class KVDutil_ImageToolkit
{

    /**
     * Maak een thumbnail van een afbeelding
     *
     * Maakt een thumbnail voor de foto. Daarbij worden de verhoudingen van de
     * oorspronkelijke foto behouden en geven de parameters mee welke de
     * maximale afmetingen zijn.
     *
     * @todo documenteren hoe size werkt
     *
     * @param resource $srcImg een PHP beeld handler voor de foto
     * @param integer  $size standaard 128
     *
     * @return resource een php beeld handler voor de thumbnail
     */
    public static function createThumbnailFor($srcImg,$size = 128)
    {
        /* calculate thumbnail size, preserving aspect ratio */
        $oldX=imageSX($srcImg);
        $oldY=imageSY($srcImg);
        if ($oldX > $oldY) {
            $thumbW=$size;
            $thumbH=$oldY*($size/$oldX);
        } else {
            $thumbW=$oldX*($size/$oldY);
            $thumbH=$size;
        }
        /* create new thumbnail */
        $dstImg=ImageCreateTrueColor($thumbW, $thumbH);
        /* Insert scaled picture */
        imagecopyresampled(
            $dstImg, $srcImg, 0, 0, 0, 0,
            $thumbW, $thumbH, $oldX, $oldY
        );
        /* Save file */
        return $dstImg;
    }

    /**
     * Geeft een string met de binaire data van de foto.
     *
     * @param resource $img een PHP beeld handler voor de foto
     *
     * @return string binaire data van de foto
     */
    public static function getData($img)
    {
        if (!ob_start()) {
            return null; // start a new output buffer
        }
        imagejpeg($img, NULL, 100);
        $imageData = ob_get_contents();
        ob_end_clean(); // stop this output buffer
        return $imageData;
    }

  /**
   * getImage
   * 	Geeft een php beeld handler van de binaire data van de foto.
   * @throws RuntimeException - Indien de foto niet kon aangemaakt worden.
   * @param string $data de binare data van de foto
   * @return resource een php beeld handle.
   */
    public static function getImage($data)
    {
        $src = imagecreatefromstring($data);
        if (!$src) {
            throw new RuntimeException(
                "Failed to create image from data: ".$data
            );
        }
        return $src;
    }

    /**
     * Draait de foto van de handle willekeurig in stappen van 90 graden.
     *
     * @param resource $src een php source van de foto
     * @param integer $angle het aantal graden dat de foto,
     *                       in wijzerszin, wordt gedraaid
     *
     * @return resource een php resource met het resultaat van de operatie.
     */
    public static function rotate($src, $angle)
    {
        $srcX = imagesx($src);
        $srcY = imagesy($src);
        switch( $angle ) {
            case 90:
                $dst = imagecreatetruecolor($srcY, $srcX);
                for($x = 0; $x <$srcX; $x ++)
                    for($y = 0; $y <$srcY; $y ++)
                        imagecopy($dst, $src, $srcY-$y-1, $x, $x, $y, 1, 1);
                break;
            case 180:
                $dstTemp = KVDutil_ImageToolkit::rotate($src, 90);
                $dst = KVDutil_ImageToolkit::rotate($dstTemp, 90);
                imagedestroy($dstTemp);
                break;
            case 270:
                $dst = imagecreatetruecolor($srcY, $srcX);
                for( $x=0; $x<$srcX; $x++ )
                    for( $y=0; $y<$srcY; $y++ )
                        imagecopy($dst, $src, $y, $srcX-$x-1, $x, $y, 1, 1);
                break;
        }
        return $dst;
    }

    /**
     * flip
     *
     * @todo documenteren wat deze functie doet.
     *
     * @param mixed $src Afbeelding die geflipt moet worden.
     *
     * @return resource
     */
    public static function flip($src)
    {
        $dst = imagecreatetruecolor(imagesx($src), imagesy($src));
        $srcX = imagesx($src);
        $srcY = imagesy($src);
        for ($x =0; $x < $srcX; $x++) {
            for ($y =0; $y < $srcY; $y++) {
                imagecopy($dst, $src, $x, $y, $srcX-$x-1, $y, 1, 1);
            }
        }
        return $dst;
    }

    /**
     * Vraag de afmetingen van een foto op.
     *
     * Zoekt de breedte en hoogte van de foto op en geeft deze in een
     * array terug onder de sleutels x en y.
     *
     * @param resource $src Handle van de afbeelding
     *
     * @return array Array met sleutels x en y.
     */
    public static function getSize($src)
    {
        $x = imagesx($src);
        $y = imagesy($src);
        return array("x"=>$x, "y"=>$y);
    }

    /**
     * Controleer of een foto aan bepaalde eisen voldoet.
     *
     * Neemt een foto en controleert of deze voldoet aan de eisen gegeven
     * in de configuratie. De test bevat minimum en maximum afmetingen
     * alsook mimetypes.
     *
     * @param string $filename bestandsnaam van de foto
     * @param array  $config   configuratie
     *
     * @return boolean
     */
    public static function isValidFoto($filename, $config)
    {
        $data = getimagesize($filename);
        // breedte
        if (($data[0] < $config["minWidth"]) || ($data[0] > $config["maxWidth"])) {
            return false;
        }
        // hoogte
        if (($data[1] < $config["minHeight"]) || ($data[1] > $config["maxHeight"])) {
            return false;
        }
        // else
        return in_array($data['mime'], $config["mimes"]);
    }
}
