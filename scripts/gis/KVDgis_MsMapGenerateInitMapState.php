<?php
    if ( $argc < 2 || in_array ( $argv[1], array ('--help','-help','-h','-?') ) ) {
        echo "\nThis is a commandline php script with one option. It takes a UMN Mapserver mapfile and generates a KVDgis_MsMapState object in JSON notation, ready to be used by a KVDgis_MsMapClient.";
        echo "\n";
        echo "\nUsage:";
        echo "\n$argv[0] <mapfile> [<imagepath> <imageurl]";
        echo "\n";
        echo "\n<mapfile> is a valid UMN Mapserver mapfile.";
        echo "\n<imagepath> is an optional path to a directory where the standard output files will be stored. If absent, defaults from mapfile will be used.";
        echo "\n<imageurl> must also be included when an imagepath is set, this is the url used to access the generate images.";
        die();
    }

    define ('KVDGISMAP' , 'c:\opt\kvd\classes\gis\\');

    $mapFile = $argv[1];

    if (!file_exists($mapFile)) {
        echo "\nKan bestand $mapFile niet vinden!";
        die();
    }

    $map = ms_newMapObj($mapFile);

    if ($argc == 4 ) {
        $imagePath = $argv[2];
        $imageUrl = $argv[3];

        $map->web->set( 'imagepath' , $imagePath );
        $map->web->set( 'imageurl' , $imageUrl );
    }

    require_once ('HTML/AJAX/JSON.php');
   
    
    require_once (KVDGISMAP . 'KVDgis_MsMapState.class.php');

    $mapState = new KVDgis_MsMapState();
    
    
    $mapState->setMapImageUrl($map->draw()->saveWebImage());
    //$mapState->setLegendImageUrl($map->drawLegend()->saveWebImage());
    $mapState->setLegendHtml($map->processlegendtemplate(array()));
    $extent =   Array ( 'minx' => $map->extent->minx,
                        'miny' => $map->extent->miny,
                        'maxx' => $map->extent->maxx,
                        'maxy' => $map->extent->maxy
                        );
    $mapState->setCurrentExtent( $extent );
    $mapState->setMapImageWidth ( $map->width );
    $mapState->setMapImageHeight ( $map->height );
    $mapState->setScale ( round($map->scale,0) );
    //print_r($mapState);
    $json = new HTML_AJAX_JSON();
    $initMapState = $json->encode ($mapState->convertToObject());
    echo "<?php\n";
    echo '$mapStateString = \'' . $initMapState . "'\n";
    echo "?>\n";
?>
